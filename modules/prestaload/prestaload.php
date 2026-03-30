<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/PrestaLoadPageDiscoveryService.php';
require_once __DIR__ . '/classes/PrestaLoadSignedRequestService.php';
require_once __DIR__ . '/classes/PrestaLoadCacheContextService.php';
require_once __DIR__ . '/classes/PrestaLoadCacheVariantService.php';
require_once __DIR__ . '/classes/PrestaLoadCacheStoreService.php';

class Prestaload extends Module
{
    private const DEFAULT_API_BASE_URL = 'https://api.prestaload.com/';
    private const CFG_STORE_KEY = 'PRESTALOAD_STORE_KEY';
    private const CFG_SHARED_SECRET = 'PRESTALOAD_SHARED_SECRET';
    private const CFG_STORE_ID = 'PRESTALOAD_STORE_ID';
    private const CFG_CONNECTED_AT = 'PRESTALOAD_CONNECTED_AT';

    /**
     * @var PrestaLoadPageDiscoveryService|null
     */
    private $pageDiscoveryService;
    /**
     * @var PrestaLoadSignedRequestService|null
     */
    private $signedRequestService;
    /**
     * @var PrestaLoadCacheContextService|null
     */
    private $cacheContextService;
    /**
     * @var PrestaLoadCacheVariantService|null
     */
    private $cacheVariantService;
    /**
     * @var PrestaLoadCacheStoreService|null
     */
    private $cacheStoreService;

    public function __construct()
    {
        $this->name = 'prestaload';
        $this->tab = 'administration';
        $this->version = '0.1.0';
        $this->author = 'Acrosoft';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PrestaLoad');
        $this->description = $this->l('Secure connection between your PrestaShop store and PrestaLoad SaaS.');
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_,
        ];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('actionDispatcherBefore')
            && $this->initializeDefaults();
    }

    public function uninstall()
    {
        return $this->deleteConfig() && parent::uninstall();
    }

    public function getContent()
    {
        $this->ensureCredentials();

        $html = '';

        if (Tools::isSubmit('submitPrestaLoadConnect')) {
            $connectNotice = $this->startOneClickConnect();
            if ($connectNotice !== '') {
                $html .= $connectNotice;
            }
        }

        if (Tools::isSubmit('submitPrestaLoadPing')) {
            $html .= $this->pingDashboard();
        }

        if (Tools::isSubmit('submitPrestaLoadDisconnect')) {
            $html .= $this->disconnectStore();
        }

        $html .= $this->renderConfiguration();

        return $html;
    }

    public function finalizeConnection($storeId)
    {
        $storeId = (string) $storeId;

        $this->logMessage('module.connection_finalize.start', [
            'store_id' => $storeId,
        ]);

        try {
            Configuration::updateValue(self::CFG_STORE_ID, $storeId);
            Configuration::updateValue(self::CFG_CONNECTED_AT, date('c'));

            $this->logMessage('module.connection_finalize.done', [
                'store_id' => $storeId,
            ]);
        } catch (Exception $exception) {
            $this->logMessage('module.connection_finalize.failed', [
                'store_id' => $storeId,
                'type' => 'exception',
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        } catch (Throwable $throwable) {
            $this->logMessage('module.connection_finalize.failed', [
                'store_id' => $storeId,
                'type' => 'throwable',
                'message' => $throwable->getMessage(),
            ]);

            throw $throwable;
        }
    }

    public function hookActionDispatcherBefore()
    {
        $this->maybeServeCachedHtml();
    }

    public function getCurrentShopId()
    {
        return (int) ($this->context->shop ? $this->context->shop->id : 0);
    }

    public function getDetectedShops()
    {
        if (!class_exists('Shop')) {
            return [];
        }

        $shops = Shop::getShops(true, null, false);
        $currentShopId = $this->getCurrentShopId();
        $rows = [];

        foreach ($shops as $shop) {
            $shopId = isset($shop['id_shop']) ? (int) $shop['id_shop'] : 0;
            if ($shopId <= 0) {
                continue;
            }

            $domainSsl = trim((string) ($shop['domain_ssl'] ?? ''));
            $domain = trim((string) ($shop['domain'] ?? ''));
            $uri = '/' . ltrim((string) ($shop['uri'] ?? '/'), '/');
            $uri = preg_replace('#/+#', '/', $uri);
            if (!is_string($uri) || $uri === '') {
                $uri = '/';
            }

            $scheme = $domainSsl !== '' ? 'https://' : 'http://';
            $host = $domainSsl !== '' ? $domainSsl : $domain;
            $url = $host !== '' ? rtrim($scheme . $host . $uri, '/') : '';

            $rows[] = [
                'shop_id' => $shopId,
                'shop_group_id' => isset($shop['id_shop_group']) ? (int) $shop['id_shop_group'] : null,
                'name' => (string) ($shop['name'] ?? ''),
                'domain' => $domain,
                'domain_ssl' => $domainSsl,
                'uri' => $uri,
                'url' => $url,
                'active' => !empty($shop['active']),
                'is_current' => $shopId === $currentShopId,
            ];
        }

        return $rows;
    }

    public function getDiscoveredPageUrls($requestedShopId = null, $pageType = null, $page = 1, $perPage = 250)
    {
        $requestedShopId = $requestedShopId !== null ? (int) $requestedShopId : 0;
        $page = max(1, (int) $page);
        $perPage = max(1, min(1000, (int) $perPage));
        $pageType = $this->getPageDiscoveryService()->normalizePageType($pageType);

        foreach ($this->getDetectedShops() as $shopRow) {
            $shopId = isset($shopRow['shop_id']) ? (int) $shopRow['shop_id'] : 0;
            if ($shopId <= 0 || empty($shopRow['active'])) {
                continue;
            }

            if ($requestedShopId > 0 && $shopId !== $requestedShopId) {
                continue;
            }

            return $this->getPageDiscoveryService()->discoverForShop($shopId, $pageType, $page, $perPage);
        }

        return $this->getPageDiscoveryService()->discoverForShop(0, $pageType, $page, $perPage);
    }

    private function initializeDefaults()
    {
        $this->ensureCredentials();

        if (!$this->registerHook('actionDispatcherBefore')) {
            return false;
        }

        return true;
    }

    private function ensureCredentials()
    {
        if (!Configuration::get(self::CFG_STORE_KEY)) {
            Configuration::updateValue(self::CFG_STORE_KEY, Tools::passwdGen(40));
        }

        if (!Configuration::get(self::CFG_SHARED_SECRET)) {
            Configuration::updateValue(self::CFG_SHARED_SECRET, bin2hex(random_bytes(32)));
        }
    }

    private function deleteConfig()
    {
        foreach ([
            self::CFG_STORE_KEY,
            self::CFG_SHARED_SECRET,
            self::CFG_STORE_ID,
            self::CFG_CONNECTED_AT,
        ] as $key) {
            Configuration::deleteByName($key);
        }

        return true;
    }

    private function startOneClickConnect()
    {
        $this->ensureCredentials();

        $payload = [
            'store_key' => (string) Configuration::get(self::CFG_STORE_KEY),
            'shop_name' => (string) Configuration::get('PS_SHOP_NAME'),
            'shop_url' => rtrim((string) $this->context->shop->getBaseURL(true), '/'),
            'shop_logo_url' => $this->getShopLogoUrl(),
            'shop_email' => (string) Configuration::get('PS_SHOP_EMAIL'),
            'platform_version' => _PS_VERSION_,
            'module_version' => $this->version,
            'shared_secret' => (string) Configuration::get(self::CFG_SHARED_SECRET),
            'return_url' => $this->getConfigureUrl(),
        ];

        $response = $this->sendJsonRequest(
            'POST',
            $this->getApiBaseUrl() . '/api/prestaboost/handshake/init',
            $payload
        );

        if (!empty($response['error'])) {
            return $this->displayError($this->l('Could not contact dashboard: ') . $response['error']);
        }

        if ((int) $response['status'] !== 200 || empty($response['json']['authorize_url'])) {
            $message = $this->extractApiErrorMessage(
                $response,
                $this->l('Dashboard returned an unexpected response.')
            );

            return $this->displayError($this->l('Connection start failed: ') . $message);
        }

        Tools::redirect((string) $response['json']['authorize_url']);

        return '';
    }

    private function pingDashboard()
    {
        $request = $this->sendSignedRequest('GET', '/api/prestaboost/ping', []);
        if (!empty($request['error'])) {
            return $this->displayError($this->l('Ping failed: ') . $request['error']);
        }

        if ((int) $request['status'] !== 200) {
            $message = !empty($request['json']['message'])
                ? $request['json']['message']
                : $this->l('Unexpected dashboard response.');

            return $this->displayError($this->l('Ping failed: ') . $message);
        }

        return $this->displayConfirmation($this->l('Connection is healthy.'));
    }

    private function disconnectStore()
    {
        $request = $this->sendSignedRequest('POST', '/api/prestaboost/disconnect', []);

        if ((int) $request['status'] === 200) {
            Configuration::updateValue(self::CFG_STORE_ID, '');
            Configuration::updateValue(self::CFG_CONNECTED_AT, '');

            return $this->displayConfirmation($this->l('Store disconnected.'));
        }

        $message = !empty($request['error'])
            ? $request['error']
            : (!empty($request['json']['message']) ? $request['json']['message'] : $this->l('Unexpected dashboard response.'));

        return $this->displayError($this->l('Disconnect failed: ') . $message);
    }

    private function sendSignedRequest($method, $path, $payload)
    {
        $storeId = (string) Configuration::get(self::CFG_STORE_ID);
        $secret = (string) Configuration::get(self::CFG_SHARED_SECRET);

        if ($storeId === '' || $secret === '') {
            return ['error' => $this->l('Missing store ID or shared secret.')];
        }

        $url = $this->getApiBaseUrl() . $path;
        $body = $payload ? json_encode($payload) : '';
        if ($body === false) {
            $body = '';
        }

        $timestamp = time();
        $signPayload = implode("\n", [
            $timestamp,
            strtoupper((string) $method),
            $path,
            hash('sha256', $body),
        ]);
        $signature = hash_hmac('sha256', $signPayload, $secret);

        return $this->sendJsonRequest((string) $method, $url, $payload, [
            'X-PrestaBoost-Store: ' . $storeId,
            'X-PrestaBoost-Timestamp: ' . $timestamp,
            'X-PrestaBoost-Signature: ' . $signature,
        ]);
    }

    private function sendJsonRequest($method, $url, $payload, array $headers = [])
    {
        if (!function_exists('curl_init')) {
            return ['error' => 'cURL extension is not available.'];
        }

        $ch = curl_init();
        $body = '';
        if ($payload !== [] && $payload !== null) {
            $body = json_encode($payload);
            if ($body === false) {
                return ['error' => 'Failed to encode JSON payload.'];
            }
        }

        $curlHeaders = array_merge([
            'Accept: application/json',
            'Content-Type: application/json',
        ], $headers);

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => strtoupper((string) $method),
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_HTTPHEADER => $curlHeaders,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $raw = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        $json = null;
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $json = $decoded;
            }
        }

        return [
            'status' => $status,
            'error' => $error,
            'raw' => $raw,
            'json' => $json,
        ];
    }

    private function renderConfiguration()
    {
        $this->context->smarty->assign([
            'pl_store_key' => (string) Configuration::get(self::CFG_STORE_KEY),
            'pl_store_id' => (string) Configuration::get(self::CFG_STORE_ID),
            'pl_connected' => $this->isConnectedStore(),
            'pl_connected_at' => (string) Configuration::get(self::CFG_CONNECTED_AT),
            'pl_module_version' => $this->version,
            'pl_help_center_url' => rtrim(self::DEFAULT_API_BASE_URL, '/') . '/help-center',
            'pl_terms_url' => rtrim(self::DEFAULT_API_BASE_URL, '/') . '/terms-and-conditions',
            'pl_privacy_url' => rtrim(self::DEFAULT_API_BASE_URL, '/') . '/privacy-policy',
            'pl_i18n' => $this->getUiTranslations(),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    private function isConnectedStore()
    {
        return (string) Configuration::get(self::CFG_STORE_ID) !== ''
            && (string) Configuration::get(self::CFG_SHARED_SECRET) !== '';
    }

    private function getApiBaseUrl()
    {
        return rtrim(self::DEFAULT_API_BASE_URL, '/');
    }

    private function getConfigureUrl()
    {
        $url = $this->context->link->getAdminLink('AdminModules', true, [], [
            'configure' => $this->name,
        ]);

        if (is_string($url) && $url !== '') {
            return $url;
        }

        if (!empty($_SERVER['REQUEST_SCHEME']) && !empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REQUEST_URI'])) {
            return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        if (Tools::usingSecureMode() && !empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REQUEST_URI'])) {
            return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        if (!empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REQUEST_URI'])) {
            return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        return rtrim((string) $this->context->shop->getBaseURL(true), '/');
    }

    private function getShopLogoUrl()
    {
        $logo = (string) Configuration::get('PS_LOGO');
        if ($logo === '') {
            return null;
        }

        $base = rtrim((string) $this->context->shop->getBaseURL(true), '/');

        return $base . '/img/' . ltrim($logo, '/');
    }

    private function extractApiErrorMessage(array $response, $fallback)
    {
        $json = is_array($response['json'] ?? null) ? $response['json'] : [];
        $message = trim((string) ($json['message'] ?? ''));
        $errors = is_array($json['errors'] ?? null) ? $json['errors'] : [];

        $errorParts = [];
        foreach ($errors as $field => $fieldErrors) {
            if (!is_array($fieldErrors)) {
                continue;
            }

            foreach ($fieldErrors as $fieldError) {
                $text = trim((string) $fieldError);
                if ($text !== '') {
                    $errorParts[] = $field . ': ' . $text;
                }
            }
        }

        if ($errorParts !== []) {
            return implode(' | ', $errorParts);
        }

        if ($message !== '') {
            return $message;
        }

        return $fallback;
    }

    private function getUiTranslations()
    {
        return [
            'subtitle' => $this->l('Connect your store to PrestaLoad SaaS and manage it from the dashboard.'),
            'connection_status' => $this->l('Connection status:'),
            'connected' => $this->l('Connected'),
            'not_connected' => $this->l('Not connected'),
            'connected_at' => $this->l('Connected at:'),
            'connect_cta' => $this->l('Connect this store'),
            'ping_cta' => $this->l('Ping API'),
            'disconnect_cta' => $this->l('Disconnect'),
            'details_title' => $this->l('Connection details'),
            'module_version' => $this->l('Module version'),
            'store_key' => $this->l('Store key'),
            'store_id' => $this->l('Store ID'),
            'help_center' => $this->l('Help center'),
            'terms' => $this->l('Terms'),
            'privacy' => $this->l('Privacy'),
        ];
    }

    private function deduplicateDiscoveredPages(array $pages)
    {
        $unique = [];

        foreach ($pages as $page) {
            $key = implode('|', [
                (string) ($page['page_type'] ?? ''),
                (string) ($page['entity_type'] ?? ''),
                (string) ($page['entity_id'] ?? ''),
                (string) ($page['language_iso'] ?? ''),
                (string) ($page['url'] ?? ''),
            ]);

            $unique[$key] = $page;
        }

        return array_values($unique);
    }

    public function logMessage($event, array $context = [])
    {
        $logFile = $this->local_path . 'prestaload.log';
        $payloadContext = $this->enrichLogContext($context);
        $payload = [
            'logged_at' => date('c'),
            'event' => (string) $event,
            'context' => $payloadContext,
        ];
        $line = json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL;
        $result = @file_put_contents($logFile, $line, FILE_APPEND);

        if ($result === false && class_exists('PrestaShopLogger')) {
            PrestaShopLogger::addLog(
                '[PrestaLoad] ' . (string) $event . ' ' . json_encode($payloadContext, JSON_UNESCAPED_SLASHES),
                1,
                null,
                'PrestaLoad'
            );
        }
    }

    private function enrichLogContext(array $context)
    {
        if (!array_key_exists('store_id', $context)) {
            $storeId = (string) Configuration::get(self::CFG_STORE_ID);
            if ($storeId !== '') {
                $context['store_id'] = $storeId;
            }
        }

        if (!array_key_exists('shop_id', $context)) {
            $shopId = $this->getCurrentShopId();
            if ($shopId > 0) {
                $context['shop_id'] = $shopId;
            }
        }

        ksort($context);

        return $context;
    }

    public function getModuleLocalPath()
    {
        return $this->local_path;
    }

    public function getSignedRequestService()
    {
        if ($this->signedRequestService === null) {
            $this->signedRequestService = new PrestaLoadSignedRequestService($this);
        }

        return $this->signedRequestService;
    }

    public function getCacheContextService()
    {
        if ($this->cacheContextService === null) {
            $this->cacheContextService = new PrestaLoadCacheContextService($this);
        }

        return $this->cacheContextService;
    }

    public function getCacheStoreService()
    {
        if ($this->cacheStoreService === null) {
            $this->cacheStoreService = new PrestaLoadCacheStoreService($this);
        }

        return $this->cacheStoreService;
    }

    public function getCacheVariantService()
    {
        if ($this->cacheVariantService === null) {
            $this->cacheVariantService = new PrestaLoadCacheVariantService($this);
        }

        return $this->cacheVariantService;
    }

    private function maybeServeCachedHtml()
    {
        try {
            if ($this->shouldBypassCacheForCurrentRequest()) {
                if ($this->shouldLogRuntimeSkip()) {
                    $this->logMessage('runtime.cache.skip', [
                        'reason' => 'without_prestaload_parameter',
                        'request_uri' => isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '',
                    ]);
                }

                return;
            }

            $result = $this->getCacheContextService()->prepareCurrentRequest();

            if (empty($result['cacheable'])) {
                if (!empty($result['reason']) && $this->shouldLogRuntimeSkip()) {
                    $this->logMessage('runtime.cache.skip', [
                        'reason' => (string) $result['reason'],
                        'request_uri' => isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '',
                    ]);
                }

                return;
            }

            $variantKey = isset($result['variant_key']) ? (string) $result['variant_key'] : '';
            if ($variantKey === '') {
                return;
            }

            $html = $this->getCacheStoreService()->getCachedHtml($variantKey);
            if ($html === null) {
                $this->logMessage('runtime.cache.miss', [
                    'variant_key' => $variantKey,
                    'normalized_url' => isset($result['normalized_url']) ? (string) $result['normalized_url'] : '',
                ]);

                return;
            }

            $this->logMessage('runtime.cache.hit', [
                'variant_key' => $variantKey,
                'normalized_url' => isset($result['normalized_url']) ? (string) $result['normalized_url'] : '',
                'bytes' => strlen($html),
            ]);

            header('Content-Type: text/html; charset=utf-8');
            header('X-PrestaLoad-Cache: HIT');
            echo $html;
            exit;
        } catch (Exception $exception) {
            $this->logMessage('runtime.cache.error', [
                'error' => $exception->getMessage(),
            ]);
        } catch (Throwable $throwable) {
            $this->logMessage('runtime.cache.error', [
                'error' => $throwable->getMessage(),
            ]);
        }
    }

    private function shouldBypassCacheForCurrentRequest()
    {
        $flag = Tools::getValue('WITHOUTPRESTALOAD', null);
        if ($flag === null) {
            return false;
        }

        if (is_bool($flag)) {
            return $flag;
        }

        $value = strtolower(trim((string) $flag));

        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }

    private function shouldLogRuntimeSkip()
    {
        if (PHP_SAPI === 'cli') {
            return false;
        }

        $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper((string) $_SERVER['REQUEST_METHOD']) : 'GET';
        if ($method !== 'GET') {
            return false;
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return false;
        }

        $requestUri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        if ($requestUri === '') {
            return false;
        }

        if (defined('_PS_ADMIN_DIR_')) {
            $adminDir = basename((string) _PS_ADMIN_DIR_);
            if ($adminDir !== '' && strpos($requestUri, '/' . $adminDir . '/') !== false) {
                return false;
            }
        }

        return true;
    }

    private function getPageDiscoveryService()
    {
        if ($this->pageDiscoveryService === null) {
            $this->pageDiscoveryService = new PrestaLoadPageDiscoveryService($this);
        }

        return $this->pageDiscoveryService;
    }
}
