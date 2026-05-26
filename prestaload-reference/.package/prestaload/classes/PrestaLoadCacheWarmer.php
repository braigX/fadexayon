<?php
/**
 * Warms page-cache variants by issuing real front-office requests for the
 * dimensions used in the cache key.
 */

class PrestaLoadCacheWarmer
{
    private const DEVICE_VARIANTS = [
        [
            'key' => 'desktop',
            'context_device' => Context::DEVICE_COMPUTER,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',
        ],
        [
            'key' => 'tablet',
            'context_device' => Context::DEVICE_TABLET,
            'user_agent' => 'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/146.0.0.0 Mobile/15E148 Safari/604.1',
        ],
        [
            'key' => 'mobile',
            'context_device' => Context::DEVICE_MOBILE,
            'user_agent' => 'Mozilla/5.0 (Linux; Android 11; moto g power (2022)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36',
        ],
    ];

    private $context;
    private $settings;
    private $pageRegistry;
    private $modulePath;

    public function __construct(Context $context, PrestaLoadCacheSettings $settings, PrestaLoadAssetPageRegistry $pageRegistry, $modulePath)
    {
        $this->context = $context;
        $this->settings = $settings;
        $this->pageRegistry = $pageRegistry;
        $this->modulePath = rtrim((string) $modulePath, '/');
    }

    public function warmAll()
    {
        @set_time_limit(0);

        $startedAt = gmdate('c');
        $report = [
            'started_at' => $startedAt,
            'finished_at' => null,
            'shop' => [
                'id' => isset($this->context->shop->id) ? (int) $this->context->shop->id : 0,
                'name' => isset($this->context->shop->name) ? (string) $this->context->shop->name : '',
            ],
            'summary' => [
                'pages' => 0,
                'variants' => 0,
                'requests_total' => 0,
                'requests_ok' => 0,
                'requests_failed' => 0,
                'early_requests' => 0,
                'avg_time_ms' => 0,
            ],
            'pages' => [],
        ];

        $totalTime = 0.0;

        foreach ($this->getLanguages() as $language) {
            $variants = $this->buildVariantsForLanguage($language);
            $pages = $this->pageRegistry->getPagesForLanguage((int) $language['id_lang']);
            foreach ($pages as $page) {
                $pageEntry = $this->warmPageEntry($page, $language, $variants, $report['summary'], $totalTime);
                $report['pages'][] = $pageEntry;
            }
        }

        $report['summary']['pages'] = count($report['pages']);
        if (!empty($report['pages'])) {
            $report['summary']['variants'] = (int) ($report['pages'][0]['variant_count'] ?? 0);
        }
        if ($report['summary']['requests_total'] > 0) {
            $report['summary']['avg_time_ms'] = (int) round($totalTime / $report['summary']['requests_total']);
        }
        $report['finished_at'] = gmdate('c');

        $this->writeReport($report);

        return $report;
    }

    public function warmPage($pageKey, $languageId)
    {
        @set_time_limit(0);

        $language = $this->findLanguageById($languageId);
        if (empty($language)) {
            throw new Exception('Selected language was not found for cache warming.');
        }

        $page = $this->findPageForLanguage($pageKey, (int) $language['id_lang']);
        if (empty($page)) {
            throw new Exception('Selected page was not found for cache warming.');
        }

        $startedAt = gmdate('c');
        $totalTime = 0.0;
        $summary = [
            'pages' => 1,
            'variants' => 0,
            'requests_total' => 0,
            'requests_ok' => 0,
            'requests_failed' => 0,
            'early_requests' => 0,
            'avg_time_ms' => 0,
        ];

        $variants = $this->buildVariantsForLanguage($language);
        $pageEntry = $this->warmPageEntry($page, $language, $variants, $summary, $totalTime);
        $summary['variants'] = (int) ($pageEntry['variant_count'] ?? 0);
        if ($summary['requests_total'] > 0) {
            $summary['avg_time_ms'] = (int) round($totalTime / $summary['requests_total']);
        }

        $report = [
            'started_at' => $startedAt,
            'finished_at' => gmdate('c'),
            'shop' => [
                'id' => isset($this->context->shop->id) ? (int) $this->context->shop->id : 0,
                'name' => isset($this->context->shop->name) ? (string) $this->context->shop->name : '',
            ],
            'summary' => $summary,
            'pages' => [$pageEntry],
        ];

        $this->writeReport($report);

        return $report;
    }

    public function getLastReport()
    {
        $path = $this->getReportPath();
        if (!is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) @file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    public function getWarmablePages()
    {
        $pages = [];

        foreach ($this->getLanguages() as $language) {
            foreach ($this->pageRegistry->getPagesForLanguage((int) $language['id_lang']) as $page) {
                $page['language_iso'] = isset($language['iso_code']) ? (string) $language['iso_code'] : '';
                $page['variant_count'] = count($this->buildVariantsForLanguage($language));
                $pages[] = $page;
            }
        }

        return $pages;
    }

    private function buildVariantsForLanguage(array $language)
    {
        $variants = [];

        foreach ($this->getCurrencies() as $currency) {
            foreach ($this->getCountries() as $country) {
                foreach (self::DEVICE_VARIANTS as $device) {
                    $variants[] = [
                        'language_id' => (int) $language['id_lang'],
                        'language_iso' => isset($language['iso_code']) ? (string) $language['iso_code'] : '',
                        'currency_id' => (int) $currency['id_currency'],
                        'currency_iso' => isset($currency['iso_code']) ? (string) $currency['iso_code'] : '',
                        'country_id' => (int) $country['id_country'],
                        'country_iso' => isset($country['iso_code']) ? (string) $country['iso_code'] : '',
                        'device' => $device['key'],
                        'context_device' => (int) $device['context_device'],
                        'user_agent' => (string) $device['user_agent'],
                    ];
                }
            }
        }

        return $variants;
    }

    private function performAnonymousRequest($url, array $device)
    {
        return $this->performRequest($url, [
            'user_agent' => $device['user_agent'],
        ]);
    }

    private function performVariantRequest($url, array $variant)
    {
        $cookie = $this->buildFrontOfficeCookie(
            (int) $variant['language_id'],
            (int) $variant['currency_id'],
            (string) $variant['country_iso']
        );

        return $this->performRequest($url, [
            'user_agent' => $variant['user_agent'],
            'cookie_name' => $cookie['name'],
            'cookie_value' => $cookie['value'],
        ]);
    }

    private function warmPageEntry(array $page, array $language, array $variants, array &$summary, &$totalTime)
    {
        $pageEntry = [
            'page_key' => isset($page['key']) ? (string) $page['key'] : '',
            'page_label' => isset($page['label']) ? (string) $page['label'] : '',
            'url' => isset($page['url']) ? (string) $page['url'] : '',
            'language_id' => (int) $language['id_lang'],
            'language_iso' => isset($language['iso_code']) ? (string) $language['iso_code'] : '',
            'variant_count' => count($variants),
            'requests' => [],
        ];

        if ($pageEntry['page_key'] === 'home') {
            $early = $this->performAnonymousRequest($pageEntry['url'], self::DEVICE_VARIANTS[0]);
            $pageEntry['requests'][] = array_merge([
                'kind' => 'early_anonymous',
                'language_id' => (int) $language['id_lang'],
                'language_iso' => isset($language['iso_code']) ? (string) $language['iso_code'] : '',
            ], $early);
            $summary['requests_total']++;
            $summary['early_requests']++;
            if (!empty($early['ok'])) {
                $summary['requests_ok']++;
            } else {
                $summary['requests_failed']++;
            }
            $totalTime += (float) ($early['time_ms'] ?? 0);
        }

        foreach ($variants as $variant) {
            $result = $this->performVariantRequest($pageEntry['url'], $variant);
            $pageEntry['requests'][] = array_merge($variant, $result);
            $summary['requests_total']++;
            if (!empty($result['ok'])) {
                $summary['requests_ok']++;
            } else {
                $summary['requests_failed']++;
            }
            $totalTime += (float) ($result['time_ms'] ?? 0);
        }

        return $pageEntry;
    }

    private function performRequest($url, array $options)
    {
        $ch = curl_init();
        if ($ch === false) {
            return [
                'ok' => false,
                'status_code' => 0,
                'time_ms' => 0,
                'error' => 'Could not initialize cURL.',
                'headers' => [],
            ];
        }

        $headers = [];
        if (!empty($options['cookie_name']) && array_key_exists('cookie_value', $options)) {
            $headers[] = 'Cookie: ' . $options['cookie_name'] . '=' . $options['cookie_value'];
        }
        $headers[] = 'X-PrestaLoad-Warmer: 1';

        curl_setopt_array($ch, [
            CURLOPT_URL => (string) $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_USERAGENT => isset($options['user_agent']) ? (string) $options['user_agent'] : self::DEVICE_VARIANTS[0]['user_agent'],
        ]);

        $raw = curl_exec($ch);
        $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $timeMs = (int) round(((float) curl_getinfo($ch, CURLINFO_TOTAL_TIME)) * 1000);
        $error = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            return [
                'ok' => false,
                'status_code' => $statusCode,
                'time_ms' => $timeMs,
                'error' => $error !== '' ? $error : 'Unknown cURL failure.',
                'headers' => [],
            ];
        }

        $headerBlock = substr((string) $raw, 0, $headerSize);

        return [
            'ok' => $statusCode >= 200 && $statusCode < 400,
            'status_code' => $statusCode,
            'time_ms' => $timeMs,
            'error' => $error,
            'headers' => $this->extractInterestingHeaders($headerBlock),
        ];
    }

    private function buildFrontOfficeCookie($languageId, $currencyId, $countryIso)
    {
        $cookie = $this->createFrontOfficeCookieObject();
        $this->setProtectedProperty($cookie, '_content', []);

        $cookie->id_lang = (int) $languageId;
        $cookie->id_currency = (int) $currencyId;
        $cookie->iso_code_country = Tools::strtoupper((string) $countryIso);
        $cookie->detect_language = 0;

        $content = '';
        $payload = (array) $this->getProtectedProperty($cookie, '_content');
        if (isset($payload['checksum'])) {
            unset($payload['checksum']);
        }

        foreach ($payload as $key => $value) {
            $content .= $key . '|' . $value . '¤';
        }

        $salt = (string) $this->getProtectedProperty($cookie, '_salt');
        $content .= 'checksum|' . hash('sha256', $salt . $content);

        $cipherTool = $this->getProtectedProperty($cookie, 'cipherTool');
        $value = is_object($cipherTool) && method_exists($cipherTool, 'encrypt')
            ? $cipherTool->encrypt($content)
            : '';

        return [
            'name' => (string) $this->getProtectedProperty($cookie, '_name'),
            'value' => $value,
        ];
    }

    private function createFrontOfficeCookieObject()
    {
        $forceSsl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        $cookieLifetime = (int) Configuration::get('PS_COOKIE_LIFETIME_FO');
        $cookieLifetime = $cookieLifetime > 0 ? time() + (max($cookieLifetime, 1) * 3600) : time() + 3600;

        if ($this->context->shop->getGroup()->share_order) {
            $cookie = new Cookie('ps-sg' . $this->context->shop->getGroup()->id, '', $cookieLifetime, $this->context->shop->getUrlsSharedCart(), false, (bool) $forceSsl);
        } else {
            $domains = null;
            if ($this->context->shop->domain != $this->context->shop->domain_ssl) {
                $domains = [$this->context->shop->domain_ssl, $this->context->shop->domain];
            }
            $cookie = new Cookie('ps-s' . $this->context->shop->id, '', $cookieLifetime, $domains, false, (bool) $forceSsl);
        }

        $cookie->disallowWriting();

        return $cookie;
    }

    private function getLanguages()
    {
        return Language::getLanguages(true, (int) $this->context->shop->id, false);
    }

    private function findLanguageById($languageId)
    {
        foreach ($this->getLanguages() as $language) {
            if ((int) ($language['id_lang'] ?? 0) === (int) $languageId) {
                return $language;
            }
        }

        return [];
    }

    private function findPageForLanguage($pageKey, $languageId)
    {
        foreach ($this->pageRegistry->getPagesForLanguage((int) $languageId) as $page) {
            if ((string) ($page['key'] ?? '') === (string) $pageKey) {
                return $page;
            }
        }

        return [];
    }

    private function getCurrencies()
    {
        $currencies = Currency::getCurrenciesByIdShop((int) $this->context->shop->id);

        return array_values(array_filter(is_array($currencies) ? $currencies : [], function ($currency) {
            return !empty($currency['active']) && empty($currency['deleted']);
        }));
    }

    private function getCountries()
    {
        $countryRows = Country::getCountries((int) $this->context->language->id, true);
        $allowedIsoCodes = array_filter(array_map('trim', explode(';', (string) Configuration::get('PS_ALLOWED_COUNTRIES'))));
        if (!empty($allowedIsoCodes)) {
            $allowedIsoCodes = array_map('strtoupper', $allowedIsoCodes);
            $countryRows = array_values(array_filter($countryRows, function ($country) use ($allowedIsoCodes) {
                return isset($country['iso_code']) && in_array(strtoupper((string) $country['iso_code']), $allowedIsoCodes, true);
            }));
        }

        if (!Configuration::get('PS_DETECT_COUNTRY') && !Configuration::get('PS_GEOLOCATION_ENABLED')) {
            $currentCountryId = isset($this->context->country->id) ? (int) $this->context->country->id : 0;
            $countryRows = array_values(array_filter($countryRows, function ($country) use ($currentCountryId) {
                return (int) ($country['id_country'] ?? 0) === $currentCountryId;
            }));
        }

        return $countryRows;
    }

    private function extractInterestingHeaders($headerBlock)
    {
        $interesting = [];
        foreach (preg_split('/\r\n|\r|\n/', (string) $headerBlock) as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }
            if (stripos($line, 'X-PrestaLoad-') === 0 || stripos($line, 'Cache-Control:') === 0) {
                $interesting[] = $line;
            }
        }

        return $interesting;
    }

    private function writeReport(array $report)
    {
        $path = $this->getReportPath();
        $directory = dirname($path);
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        @file_put_contents($path, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL, LOCK_EX);
    }

    private function getReportPath()
    {
        return $this->modulePath . '/cache/prestaload-cache-warmer.json';
    }

    private function getProtectedProperty($object, $propertyName)
    {
        $reflection = new ReflectionObject($object);
        while ($reflection) {
            if ($reflection->hasProperty($propertyName)) {
                $property = $reflection->getProperty($propertyName);
                $property->setAccessible(true);

                return $property->getValue($object);
            }

            $reflection = $reflection->getParentClass();
        }

        return null;
    }

    private function setProtectedProperty($object, $propertyName, $value)
    {
        $reflection = new ReflectionObject($object);
        while ($reflection) {
            if ($reflection->hasProperty($propertyName)) {
                $property = $reflection->getProperty($propertyName);
                $property->setAccessible(true);
                $property->setValue($object, $value);

                return;
            }

            $reflection = $reflection->getParentClass();
        }
    }
}
