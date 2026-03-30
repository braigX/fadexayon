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
        $currencyIso = trim((string) (isset($payload['currency_iso']) ? $payload['currency_iso'] : ''));
        $loginState = trim((string) (isset($payload['login_state']) ? $payload['login_state'] : 'guest'));
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
            'currency_iso' => $currencyIso !== '' ? strtoupper($currencyIso) : $this->getDefaultCurrencyIso($shopId),
            'device_class' => $deviceClass !== '' ? strtolower($deviceClass) : 'desktop',
            'login_state' => $loginState !== '' ? strtolower($loginState) : 'guest',
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

    /**
     * @return array<string, mixed>
     */
    public function prepareCurrentRequest()
    {
        if (!$this->isFrontOfficeCacheableRequest()) {
            return [
                'cacheable' => false,
                'reason' => 'Request is not eligible for cache serving.',
            ];
        }

        $context = Context::getContext();
        $url = $this->buildCurrentRequestUrl();
        $shopId = isset($context->shop) ? (int) $context->shop->id : 0;
        $languageIso = isset($context->language) && Validate::isLoadedObject($context->language)
            ? strtoupper((string) $context->language->iso_code)
            : $this->getDefaultLanguageIso($shopId);
        $currencyIso = isset($context->currency) && Validate::isLoadedObject($context->currency)
            ? strtoupper((string) $context->currency->iso_code)
            : $this->getDefaultCurrencyIso($shopId);

        if ($url === '') {
            return [
                'cacheable' => false,
                'reason' => 'Failed to build current request URL.',
            ];
        }

        $normalizedUrl = $this->normalizeUrl($url);
        if ($normalizedUrl === '') {
            return [
                'cacheable' => false,
                'reason' => 'Failed to normalize current request URL.',
            ];
        }

        $variant = [
            'shop_id' => $shopId,
            'language_iso' => $languageIso,
            'currency_iso' => $currencyIso,
            'device_class' => $this->detectDeviceClass(),
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

    private function isFrontOfficeCacheableRequest()
    {
        if (PHP_SAPI === 'cli') {
            return false;
        }

        $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper((string) $_SERVER['REQUEST_METHOD']) : 'GET';
        if ($method !== 'GET') {
            return false;
        }

        if (defined('_PS_ADMIN_DIR_')) {
            $adminDir = basename((string) _PS_ADMIN_DIR_);
            $requestUri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
            if ($adminDir !== '' && strpos($requestUri, '/' . $adminDir . '/') !== false) {
                return false;
            }
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return false;
        }

        $context = Context::getContext();
        if (isset($context->customer) && Validate::isLoadedObject($context->customer) && !empty($context->customer->isLogged())) {
            return false;
        }

        if (isset($context->cart) && Validate::isLoadedObject($context->cart) && (int) $context->cart->nbProducts() > 0) {
            return false;
        }

        return true;
    }

    private function buildCurrentRequestUrl()
    {
        $scheme = Tools::usingSecureMode() ? 'https://' : 'http://';
        $host = isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : '';
        $requestUri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';

        if ($host === '' || $requestUri === '') {
            return '';
        }

        return $scheme . $host . $requestUri;
    }

    private function detectDeviceClass()
    {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower((string) $_SERVER['HTTP_USER_AGENT']) : '';
        if ($userAgent !== '' && preg_match('/android|iphone|ipad|ipod|mobile|blackberry|opera mini|iemobile/', $userAgent)) {
            return 'mobile';
        }

        return 'desktop';
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
