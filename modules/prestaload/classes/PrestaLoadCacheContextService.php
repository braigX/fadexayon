<?php

class PrestaLoadCacheContextService
{
    /**
     * @var Prestaload
     */
    private $module;

    public function __construct(Prestaload $module)
    {
        $this->module = $module;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public function prepare(array $payload)
    {
        $url = trim((string) (isset($payload['url']) ? $payload['url'] : ''));
        $deviceClass = trim((string) (isset($payload['device_class']) ? $payload['device_class'] : 'desktop'));
        $languageIso = trim((string) (isset($payload['language_iso']) ? $payload['language_iso'] : ''));
        $shopId = isset($payload['shop_id']) ? (int) $payload['shop_id'] : 0;

        if ($url === '') {
            return [
                'cacheable' => false,
                'reason' => 'Missing URL.',
            ];
        }

        $normalizedUrl = $this->normalizeUrl($url);
        if ($normalizedUrl === '') {
            return [
                'cacheable' => false,
                'reason' => 'Invalid URL.',
            ];
        }

        $variant = [
            'shop_id' => $shopId > 0 ? $shopId : (int) $this->module->getCurrentShopId(),
            'language_iso' => $languageIso !== '' ? strtoupper($languageIso) : $this->getDefaultLanguageIso($shopId),
            'currency_iso' => $this->getDefaultCurrencyIso($shopId),
            'device_class' => $deviceClass !== '' ? strtolower($deviceClass) : 'desktop',
            'login_state' => 'guest',
            'theme_hash' => $this->getThemeHash($shopId),
        ];

        ksort($variant);
        $variantKey = hash('sha256', $normalizedUrl . json_encode($variant));
        $cacheService = $this->module->getCacheStoreService();
        $cacheMeta = $cacheService->getCacheMeta($variantKey);

        return [
            'cacheable' => true,
            'reason' => null,
            'normalized_url' => $normalizedUrl,
            'variant' => $variant,
            'variant_key' => $variantKey,
            'cache_exists' => $cacheMeta !== null,
            'cache_meta' => $cacheMeta,
        ];
    }

    private function normalizeUrl($url)
    {
        $parsed = parse_url($url);
        if (!is_array($parsed) || empty($parsed['host'])) {
            return '';
        }

        $scheme = !empty($parsed['scheme']) ? strtolower((string) $parsed['scheme']) : 'https';
        $host = strtolower((string) $parsed['host']);
        $path = isset($parsed['path']) ? (string) $parsed['path'] : '/';
        if ($path === '') {
            $path = '/';
        }

        $query = '';
        if (!empty($parsed['query'])) {
            parse_str((string) $parsed['query'], $queryParams);
            if (is_array($queryParams)) {
                foreach ([
                    '_gl', 'epik', 'fbclid', 'gbraid', 'gclid', 'msclkid',
                    'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
                    'vgo_ee', 'wbraid', 'zenid', 'rltest', 'rlrand',
                ] as $ignoredParam) {
                    unset($queryParams[$ignoredParam]);
                }

                if (!empty($queryParams)) {
                    ksort($queryParams);
                    $query = http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
                }
            }
        }

        return $scheme . '://' . $host . $path . ($query !== '' ? '?' . $query : '');
    }

    private function getDefaultLanguageIso($shopId = 0)
    {
        $languageId = (int) Configuration::get('PS_LANG_DEFAULT', null, null, $shopId > 0 ? $shopId : null);
        if ($languageId <= 0) {
            return 'EN';
        }

        $language = new Language($languageId);
        if (!Validate::isLoadedObject($language)) {
            return 'EN';
        }

        return strtoupper((string) $language->iso_code);
    }

    private function getDefaultCurrencyIso($shopId = 0)
    {
        $currencyId = (int) Configuration::get('PS_CURRENCY_DEFAULT', null, null, $shopId > 0 ? $shopId : null);
        if ($currencyId <= 0) {
            return 'EUR';
        }

        $currency = new Currency($currencyId);
        if (!Validate::isLoadedObject($currency)) {
            return 'EUR';
        }

        return strtoupper((string) $currency->iso_code);
    }

    private function getThemeHash($shopId = 0)
    {
        $themeName = (string) Configuration::get('PS_THEME_NAME', null, null, $shopId > 0 ? $shopId : null);

        if ($themeName === '') {
            $context = Context::getContext();
            if (isset($context->shop) && method_exists($context->shop, 'getTheme')) {
                $theme = $context->shop->getTheme();
                if (is_object($theme) && is_callable([$theme, 'getName'])) {
                    $themeName = (string) $theme->getName();
                } elseif (is_object($theme) && isset($theme->name)) {
                    $themeName = (string) $theme->name;
                }
            }
        }

        return sha1($themeName !== '' ? $themeName : 'default-theme');
    }
}
