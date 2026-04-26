<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

class Prestaload extends Module
{
    const VERSION     = '1.0.0';
    const API_URL     = 'http://127.0.0.1:8000/';
    const CONFIG_KEY  = 'PRESTALOAD_SETTINGS';
    const CACHE_DIR   = _PS_ROOT_DIR_ . '/var/prestaload-cache';
    const VARIANT_MAP_KEY_PREFIX = 'PRESTALOAD_VARIANT_MAP_SHOP_';

    public function __construct()
    {
        $this->name         = 'prestaload';
        $this->tab          = 'administration';
        $this->version      = self::VERSION;
        $this->author       = 'Prestaload';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.0', 'max' => _PS_VERSION_];
        $this->bootstrap    = true;

        parent::__construct();

        $this->displayName = $this->l('Prestaload');
        $this->description = $this->l('Connect your PrestaShop store to Prestaload for full-page caching and asset optimization.');
    }

    public function log(string $level, string $message, array $context = []): void
    {
        $line = date('Y-m-d H:i:s') . ' [' . strtoupper($level) . '] ' . $message;

        if ($context) {
            $line .= ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        file_put_contents(__DIR__ . '/logs.txt', $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public function install(): bool
    {
        return parent::install()
            && $this->registerHook('actionCronJob')
            && $this->registerHook('actionDispatcher')
            && $this->registerHook('actionObjectProductAddAfter')
            && $this->registerHook('actionObjectProductUpdateAfter')
            && $this->registerHook('actionObjectProductDeleteAfter')
            && $this->registerHook('actionObjectCategoryAddAfter')
            && $this->registerHook('actionObjectCategoryUpdateAfter')
            && $this->registerHook('actionObjectCategoryDeleteAfter')
            && $this->registerHook('actionObjectCMSAddAfter')
            && $this->registerHook('actionObjectCMSUpdateAfter')
            && $this->registerHook('actionObjectCMSDeleteAfter')
            && $this->registerHook('actionObjectCmsAddAfter')
            && $this->registerHook('actionObjectCmsUpdateAfter')
            && $this->registerHook('actionObjectCmsDeleteAfter');
    }

    public function uninstall(): bool
    {
        Configuration::deleteByName(self::CONFIG_KEY);
        return parent::uninstall();
    }

    public function getContent(): string
    {
        $settings   = json_decode(Configuration::get(self::CONFIG_KEY, '{}'), true) ?: [];
        $connected  = ! empty($settings['connected']);
        $error      = '';
        $success    = '';

        if (Tools::isSubmit('prestaload_connect')) {
            [$error, $success, $settings, $connected] = $this->processConnect($settings);
        }

        if (Tools::isSubmit('prestaload_disconnect')) {
            if (! empty($settings['api_key'])) {
                $this->callApi('/plugin/disconnect', [
                    'api_key'  => $settings['api_key'],
                    'platform' => 'prestashop',
                ]);
            }

            Configuration::deleteByName(self::CONFIG_KEY);
            $settings  = [];
            $connected = false;
            $success   = $this->l('Disconnected successfully.');
        }

        return $this->renderPage($settings, $connected, $error, $success);
    }

    private function processConnect(array $settings): array
    {
        $apiKey = trim(Tools::getValue('api_key'));
        $error = $success = '';

        if (empty($apiKey)) {
            return [$this->l('Please enter your API key.'), '', $settings, false];
        }

        $response = $this->callApi('/plugin/handshake', [
            'api_key'        => $apiKey,
            'plugin_version' => self::VERSION,
            'platform'       => 'prestashop',
            'site_url'       => $this->context->shop->getBaseURL(true),
            'sites'          => $this->discoverSitesPayload(),
        ]);

        if ($response === null) {
            return [$this->l('Could not reach Prestaload. Please check your internet connection.'), '', $settings, false];
        }

        if (empty($response['connected'])) {
            return [$this->l('Invalid API key. Please check the key from your Prestaload dashboard.'), '', $settings, false];
        }

        $settings = [
            'api_key'      => $apiKey,
            'integration'  => $response['integration'] ?? '',
            'connected'    => true,
            'connected_at' => date('Y-m-d H:i:s'),
        ];

        Configuration::updateValue(self::CONFIG_KEY, json_encode($settings));
        $success = $this->l('Successfully connected to Prestaload!');

        return [$error, $success, $settings, true];
    }

    private function callApi(string $endpoint, array $body, int $timeout = 15): ?array
    {
        $ch = curl_init($this->apiUrl($endpoint));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($body),
        ]);

        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $code !== 200) {
            return null;
        }

        return json_decode($raw, true) ?: null;
    }

    private function apiUrl(string $endpoint): string
    {
        return rtrim(self::API_URL, '/') . '/api/' . ltrim($endpoint, '/');
    }

    private function discoverSitesPayload(): array
    {
        $sites = [];

        if (Shop::isFeatureActive()) {
            foreach (Shop::getShops(true) as $shop) {
                $shopObj = new Shop((int) $shop['id_shop']);
                $url     = 'https://' . $shopObj->domain_ssl . $shopObj->getBaseURI();

                $sites[] = [
                    'platform_site_id' => (string) $shop['id_shop'],
                    'url'              => rtrim($url, '/'),
                    'name'             => $shop['name'],
                    'metadata'         => [
                        'multi_shop'    => true,
                        'id_shop_group' => $shop['id_shop_group'],
                        'theme_name'    => $shop['theme_name'] ?? null,
                    ],
                ];
            }

            return array_slice($sites, 0, 500);
        }

        return [[
            'platform_site_id' => (string) $this->context->shop->id,
            'url'              => rtrim($this->context->shop->getBaseURL(true), '/'),
            'name'             => Configuration::get('PS_SHOP_NAME'),
            'metadata'         => ['multi_shop' => false],
        ]];
    }

    public function hookActionCronJob(): void
    {
        $settings = json_decode(Configuration::get(self::CONFIG_KEY, '{}'), true) ?: [];

        if (empty($settings['connected']) || empty($settings['api_key'])) {
            return;
        }

        $this->callApi('/plugin/heartbeat', [
            'api_key'        => $settings['api_key'],
            'plugin_version' => self::VERSION,
            'platform'       => 'prestashop',
        ]);
    }

    public function hookActionObjectProductAddAfter(array $params): void
    {
        $this->reportObjectContentChange($params['object'] ?? null, 'published');
    }

    public function hookActionObjectProductUpdateAfter(array $params): void
    {
        $this->reportObjectContentChange($params['object'] ?? null, 'updated');
    }

    public function hookActionObjectProductDeleteAfter(array $params): void
    {
        $this->reportObjectContentChange($params['object'] ?? null, 'deleted');
    }

    public function hookActionObjectCategoryAddAfter(array $params): void
    {
        $this->reportObjectContentChange($params['object'] ?? null, 'published');
    }

    public function hookActionObjectCategoryUpdateAfter(array $params): void
    {
        $this->reportObjectContentChange($params['object'] ?? null, 'updated');
    }

    public function hookActionObjectCategoryDeleteAfter(array $params): void
    {
        $this->reportObjectContentChange($params['object'] ?? null, 'deleted');
    }

    public function hookActionObjectCmsAddAfter(array $params): void
    {
        $this->reportObjectContentChange($params['object'] ?? null, 'published');
    }

    public function hookActionObjectCmsUpdateAfter(array $params): void
    {
        $this->reportObjectContentChange($params['object'] ?? null, 'updated');
    }

    public function hookActionObjectCmsDeleteAfter(array $params): void
    {
        $this->reportObjectContentChange($params['object'] ?? null, 'deleted');
    }

    private function reportObjectContentChange($object, string $changeType): void
    {
        $settings = json_decode(Configuration::get(self::CONFIG_KEY, '{}'), true) ?: [];

        if (empty($settings['connected']) || empty($settings['api_key']) || ! is_object($object)) {
            return;
        }

        $page = $this->publicPageForObject($object);

        if (! $page) {
            return;
        }

        $payload = [
            'api_key' => $settings['api_key'],
            'platform' => 'prestashop',
            'site_url' => rtrim($this->context->shop->getBaseURL(true), '/'),
            'change_type' => $changeType,
            'entity_type' => $page['type'],
            'entity_id' => (string) ($object->id ?? ''),
            'changed_urls' => [$page],
        ];

        $this->callApi('/plugin/content-changed', $payload, 3);

        $settings['last_content_change'] = [
            'sent_at' => date('Y-m-d H:i:s'),
            'urls' => [$page['url']],
        ];

        Configuration::updateValue(self::CONFIG_KEY, json_encode($settings));
    }

    private function publicPageForObject($object): ?array
    {
        try {
            if ($object instanceof Product) {
                return [
                    'url' => rtrim($this->context->link->getProductLink($object), '/'),
                    'type' => 'product',
                    'title' => $this->localizedObjectName($object),
                ];
            }

            if ($object instanceof Category) {
                return [
                    'url' => rtrim($this->context->link->getCategoryLink($object), '/'),
                    'type' => 'category',
                    'title' => $this->localizedObjectName($object),
                ];
            }

            if ($object instanceof CMS) {
                return [
                    'url' => rtrim($this->context->link->getCMSLink($object), '/'),
                    'type' => 'cms',
                    'title' => $this->localizedObjectName($object),
                ];
            }
        } catch (Exception $e) {
            $this->log('warn', 'content change URL resolution failed', [
                'class' => get_class($object),
                'id' => $object->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    private function localizedObjectName($object): ?string
    {
        $name = $object->name ?? $object->meta_title ?? null;

        if (is_array($name)) {
            return (string) ($name[$this->context->language->id] ?? reset($name) ?: '');
        }

        return $name !== null ? (string) $name : null;
    }

    // -------------------------------------------------------------------------
    // Full-page cache: warmup interception + cache serving
    // -------------------------------------------------------------------------

    /**
     * actionDispatcher fires before the controller is initialized — the earliest
     * safe hook to intercept requests for cache serving and warmup bypass.
     */
    public function hookActionDispatcher(array $params): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return;
        }

        // Skip admin controllers.
        $controllerType = $params['controller_type'] ?? '';
        if ($controllerType === 'admin') {
            return;
        }

        // Skip module front controllers (dynamic endpoints, AJAX handlers, payment callbacks, etc.).
        if ($controllerType === 'moduleFront') {
            return;
        }

        // Skip AJAX requests (XMLHttpRequest header or PS fc=module pattern).
        if (! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return;
        }

        // Skip transactional and account pages — content is always user-specific.
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        foreach (['/checkout', '/cart', '/order', '/my-account', '/login', '/registration', '/password-recovery'] as $skip) {
            if (strpos($uri, $skip) !== false) {
                return;
            }
        }

        if (Tools::getValue('prestaload_warmup') === '1') {
            $this->log('info', 'warmup request', ['url' => $this->currentUrl()]);
            $this->handleWarmup();
            return;
        }

        $this->log('info', 'cache serve check', ['url' => $this->currentUrl()]);
        $this->serveFromCacheIfAvailable();
    }

    private function handleWarmup(): void
    {
        $token       = Tools::getValue('plwu_token', '');
        $variantHash = Tools::getValue('plwu_vh', '');
        $url         = $this->currentUrl();

        if (! $token || ! $variantHash) {
            $this->log('warn', 'warmup rejected: missing token or variant_hash', ['url' => $url]);
            header('HTTP/1.1 403 Forbidden');
            exit;
        }

        $parts = explode('.', $token, 2);
        if (count($parts) !== 2) {
            $this->log('warn', 'warmup rejected: malformed token', ['url' => $url]);
            header('HTTP/1.1 403 Forbidden');
            exit;
        }

        [$receivedSig, $expires] = $parts;
        $expires = (int) $expires;

        if ($expires <= time()) {
            $this->log('warn', 'warmup rejected: token expired', ['url' => $url, 'expires' => $expires]);
            header('HTTP/1.1 403 Forbidden');
            exit;
        }

        $settings = json_decode(Configuration::get(self::CONFIG_KEY, '{}'), true) ?: [];
        if (empty($settings['api_key'])) {
            $this->log('warn', 'warmup rejected: module not connected', ['url' => $url]);
            header('HTTP/1.1 403 Forbidden');
            exit;
        }

        $normalizedUrl = $this->normalizeUrl($url);
        $payload       = "warmup:{$normalizedUrl}:{$variantHash}:{$expires}";
        $apiKeyHash    = hash('sha256', $settings['api_key']);
        $expectedSig   = rtrim(strtr(base64_encode(hash_hmac('sha256', $payload, $apiKeyHash, true)), '+/', '-_'), '=');

        if (! hash_equals($expectedSig, $receivedSig)) {
            $this->log('warn', 'warmup rejected: invalid signature', [
                'url'      => $url,
                'expected' => $expectedSig,
                'received' => $receivedSig,
                'payload'  => $payload,
            ]);
            header('HTTP/1.1 403 Forbidden');
            exit;
        }

        $this->log('info', 'warmup accepted', ['url' => $url, 'variant_hash' => $variantHash]);
        define('PRESTALOAD_WARMUP', true);
    }

    private function serveFromCacheIfAvailable(): void
    {
        if (defined('PRESTALOAD_WARMUP')) {
            return;
        }

        $context = Context::getContext();

        if ($context->customer && $context->customer->isLogged()) {
            $this->log('info', 'cache MISS: logged-in user', ['url' => $this->currentUrl()]);
            return;
        }

        $variantHash = $this->resolveVariantHash($context);
        if (! $variantHash) {
            $this->log('warn', 'cache MISS: could not resolve variant hash', ['url' => $this->currentUrl()]);
            return;
        }

        $url  = $this->currentUrl();
        $path = $this->cachePath($url, $variantHash);

        if (! file_exists($path . '.meta') || ! file_exists($path . '.html')) {
            $this->log('info', 'cache MISS: no cache file', ['url' => $url, 'variant_hash' => $variantHash]);
            return;
        }

        $meta = json_decode((string) file_get_contents($path . '.meta'), true);
        if (! $meta || (int) ($meta['expires_at'] ?? 0) <= time()) {
            $this->log('info', 'cache MISS: expired', ['url' => $url, 'variant_hash' => $variantHash, 'expires_at' => $meta['expires_at'] ?? null]);
            return;
        }

        $html = file_get_contents($path . '.html');
        if ($html === false) {
            $this->log('warn', 'cache MISS: could not read html file', ['url' => $url, 'path' => $path . '.html']);
            return;
        }

        $this->log('info', 'cache HIT', ['url' => $url, 'variant_hash' => $variantHash, 'size' => strlen($html)]);

        header('Content-Type: text/html; charset=UTF-8');
        header('X-Prestaload-Cache: HIT');
        header('X-Prestaload-Variant: ' . $variantHash);
        echo $html;
        exit;
    }

    public function updateVariantMap(string $variantHash, array $dimensions, ?int $shopId = null): void
    {
        $targetShopId = $shopId ?: $this->resolveVariantMapShopId($dimensions);
        $map = $this->getVariantMap($targetShopId);
        $map[$this->dimensionKey($dimensions)] = $variantHash;
        $this->putVariantMap($targetShopId, $map);
    }

    private function resolveVariantHash(Context $context): ?string
    {
        $shopId   = $context->shop ? (string) $context->shop->id : null;
        $ua       = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
        $device   = (strpos($ua, 'mobile') !== false || strpos($ua, 'android') !== false || strpos($ua, 'iphone') !== false)
            ? 'mobile'
            : 'desktop';

        $language = $this->resolveLanguageIsoCode($context);
        $currency = $this->resolveCurrencyIsoCode($context);
        $country  = $this->resolveCountryIsoCode($context);

        $map = $this->getVariantMap($shopId !== null ? (int) $shopId : null);
        $key = $this->dimensionKey([
            'auth_state' => 'guest',
            'country'    => $country,
            'currency'   => $currency,
            'device'     => $device,
            'language'   => $language,
            'market'     => null,
            'shop'       => $shopId,
        ]);

        $hash = $map[$key] ?? null;

        $this->log('debug', 'resolveVariantHash', [
            'key'      => $key,
            'found'    => $hash !== null,
            'hash'     => $hash,
            'map_keys' => array_keys($map),
        ]);

        return $hash;
    }

    private function getVariantMap(?int $shopId): array
    {
        $targetShopId = $shopId ?: $this->resolveVariantMapShopId();
        $configKey = $this->variantMapConfigKey($targetShopId);
        $raw = Configuration::get($configKey, null, null, $targetShopId ?: null, '{}');

        return json_decode((string) $raw, true) ?: [];
    }

    private function putVariantMap(?int $shopId, array $map): void
    {
        $targetShopId = $shopId ?: $this->resolveVariantMapShopId();
        $configKey = $this->variantMapConfigKey($targetShopId);

        Configuration::updateValue($configKey, json_encode($map), false, null, $targetShopId ?: null);
    }

    private function variantMapConfigKey(?int $shopId): string
    {
        return self::VARIANT_MAP_KEY_PREFIX . ($shopId ?: 0);
    }

    private function resolveVariantMapShopId(array $dimensions = []): ?int
    {
        if (isset($dimensions['shop']) && $dimensions['shop'] !== '' && $dimensions['shop'] !== null) {
            return (int) $dimensions['shop'];
        }

        if ($this->context && $this->context->shop) {
            return (int) $this->context->shop->id;
        }

        return null;
    }

    /**
     * Resolves the visitor's active language ISO code.
     *
     * At actionDispatcher time, $context->language is set by PS before hooks fire,
     * but falls back to cookie → shop default for safety.
     */
    private function resolveLanguageIsoCode(Context $context): ?string
    {
        if ($context->language && $context->language->iso_code) {
            return $context->language->iso_code;
        }

        $idLang = (int) ($_COOKIE['id_lang'] ?? 0);
        if ($idLang > 0) {
            $lang = Language::getLanguage($idLang);
            if (! empty($lang['iso_code'])) {
                return (string) $lang['iso_code'];
            }
        }

        $defaultId = (int) Configuration::get('PS_LANG_DEFAULT');
        if ($defaultId > 0) {
            $lang = Language::getLanguage($defaultId);
            if (! empty($lang['iso_code'])) {
                return (string) $lang['iso_code'];
            }
        }

        return null;
    }

    /**
     * Resolves the visitor's active currency ISO code.
     *
     * $context->currency is null at actionDispatcher time. Read from PS session
     * cookie (id_currency) — same source PS uses during context init — then fall
     * back to the shop default.
     */
    private function resolveCurrencyIsoCode(Context $context): ?string
    {
        if ($context->currency && $context->currency->iso_code) {
            return $context->currency->iso_code;
        }

        $idCurrency = (int) ($_COOKIE['id_currency'] ?? 0);
        if ($idCurrency > 0) {
            $currency = new Currency($idCurrency);
            if ($currency->iso_code) {
                return $currency->iso_code;
            }
        }

        $defaultId = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        if ($defaultId > 0) {
            $currency = new Currency($defaultId);
            if ($currency->iso_code) {
                return $currency->iso_code;
            }
        }

        return null;
    }

    /**
     * Resolves the shop's default country ISO code.
     *
     * The /variants endpoint returns the shop's default country (not visitor geolocation),
     * so all cached variants are keyed against PS_COUNTRY_DEFAULT — not the visitor's IP.
     * At actionDispatcher time, $context->country is null; resolve directly from config.
     */
    private function resolveCountryIsoCode(Context $context): ?string
    {
        if (isset($context->country->iso_code) && $context->country->iso_code !== '') {
            return $context->country->iso_code;
        }

        $defaultId = (int) Configuration::get('PS_COUNTRY_DEFAULT');
        if ($defaultId > 0) {
            $iso = Country::getIsoById($defaultId);
            return ($iso !== false && $iso !== '') ? (string) $iso : null;
        }

        return null;
    }

    private function dimensionKey(array $dimensions): string
    {
        return implode('|', [
            (string) ($dimensions['auth_state'] ?? ''),
            (string) ($dimensions['country']    ?? ''),
            (string) ($dimensions['currency']   ?? ''),
            (string) ($dimensions['device']     ?? ''),
            (string) ($dimensions['language']   ?? ''),
            (string) ($dimensions['market']     ?? ''),
            (string) ($dimensions['shop']       ?? ''),
        ]);
    }

    private function cachePath(string $url, string $variantHash): string
    {
        $urlHash = md5($this->normalizeUrl($url));
        $shard   = substr($urlHash, 0, 2);

        return self::CACHE_DIR . "/{$shard}/{$urlHash}/{$variantHash}";
    }

    private function currentUrl(): string
    {
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? '';
        $uri    = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');

        return rtrim("{$scheme}://{$host}{$uri}", '/');
    }

    private function normalizeUrl(string $url): string
    {
        $parsed = parse_url(strtolower(rtrim($url, '/')));
        $host   = $parsed['host'] ?? '';
        $path   = $parsed['path'] ?? '';

        return $host . ($path !== '' && $path !== '/' ? rtrim($path, '/') : '');
    }

    private function renderPage(array $settings, bool $connected, string $error, string $success): string
    {
        $this->context->smarty->assign([
            'prestaload_connected'   => $connected,
            'prestaload_integration' => $settings['integration'] ?? '',
            'prestaload_error'       => $error,
            'prestaload_success'     => $success,
            'prestaload_action_url'  => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name,
            'prestaload_token'       => Tools::getAdminTokenLite('AdminModules'),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }
}
