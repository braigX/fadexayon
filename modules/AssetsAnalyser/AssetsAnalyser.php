<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class AssetsAnalyser extends Module
{
    const MAX_URLS = 100;
    const DEFAULT_COVERAGE_WORKER_URL = 'https://optimizer.prestaload.com';

    public function __construct()
    {
        $this->name = 'AssetsAnalyser';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Acrosoft';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Assets Analyser');
        $this->description = $this->l('Analyze CSS and JavaScript assets loaded by selected front-office pages.');
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_,
        ];
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayBackOfficeHeader');
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') !== $this->name) {
            return;
        }

        $this->context->controller->addCSS($this->_path . 'views/css/admin.css?v=' . $this->getAssetVersion('views/css/admin.css'));
        $this->context->controller->addJS($this->_path . 'views/js/admin.js?v=' . $this->getAssetVersion('views/js/admin.js'));
    }

    public function getContent()
    {
        if (Tools::getValue('ajax') && Tools::getValue('configure') === $this->name) {
            $this->handleAjaxRequest();
        }

        $ajaxUrl = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . urlencode($this->name)
            . '&tab_module=' . urlencode($this->tab)
            . '&module_name=' . urlencode($this->name)
            . '&token=' . Tools::getAdminTokenLite('AdminModules');

        $this->context->smarty->assign([
            'assets_analyser_ajax_url' => $ajaxUrl,
            'assets_analyser_page_types' => $this->getPageTypes(),
            'assets_analyser_default_page_type' => 'home',
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
    }

    private function handleAjaxRequest()
    {
        $this->ajaxJsonHeaders();

        if (!$this->isAjaxTokenValid()) {
            $this->renderJson([
                'success' => false,
                'message' => $this->l('Invalid security token.'),
            ]);
        }

        try {
            $action = Tools::getValue('action');

            if ($action === 'getUrls') {
                $pageType = Tools::getValue('page_type', 'home');
                $this->renderJson([
                    'success' => true,
                    'urls' => $this->getUrlsForPageType($pageType),
                ]);
            }

            if ($action === 'analyzeUrl') {
                $url = trim((string) Tools::getValue('url'));
                $this->renderJson([
                    'success' => true,
                    'analysis' => $this->analyzeUrl($url),
                ]);
            }

            if ($action === 'analyzeCoverage') {
                $url = trim((string) Tools::getValue('url'));
                $assets = json_decode((string) Tools::getValue('assets', '[]'), true);
                $this->renderJson([
                    'success' => true,
                    'coverage' => $this->analyzeCoverage($url, is_array($assets) ? $assets : []),
                ]);
            }

            $this->renderJson([
                'success' => false,
                'message' => $this->l('Unknown AJAX action.'),
            ]);
        } catch (Exception $exception) {
            $this->renderJson([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function getAssetVersion($relativePath)
    {
        $path = $this->local_path . $relativePath;

        return $this->version . '-' . (file_exists($path) ? (int) filemtime($path) : time());
    }

    private function isAjaxTokenValid()
    {
        return Tools::getValue('token') === Tools::getAdminTokenLite('AdminModules');
    }

    private function ajaxJsonHeaders()
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
    }

    private function renderJson(array $payload)
    {
        die(json_encode($payload));
    }

    private function getPageTypes()
    {
        return [
            'home' => $this->l('Home'),
            'category' => $this->l('Category'),
            'product' => $this->l('Product'),
            'cms' => $this->l('CMS page'),
            'manufacturer' => $this->l('Manufacturer'),
            'supplier' => $this->l('Supplier'),
            'search' => $this->l('Search'),
            'contact' => $this->l('Contact'),
            'new-products' => $this->l('New products'),
            'prices-drop' => $this->l('Prices drop'),
            'best-sales' => $this->l('Best sales'),
            'sitemap' => $this->l('Sitemap'),
            'manual' => $this->l('Manual URL'),
        ];
    }

    private function getUrlsForPageType($pageType)
    {
        $pageType = (string) $pageType;
        $link = $this->context->link;
        $idLang = (int) $this->context->language->id;

        switch ($pageType) {
            case 'home':
                return [$this->buildUrlOption($this->l('Home page'), $link->getPageLink('index', true, $idLang))];
            case 'category':
                return $this->getCategoryUrls($idLang);
            case 'product':
                return $this->getProductUrls($idLang);
            case 'cms':
                return $this->getCmsUrls($idLang);
            case 'manufacturer':
                return $this->getManufacturerUrls($idLang);
            case 'supplier':
                return $this->getSupplierUrls($idLang);
            case 'search':
                return [$this->buildUrlOption($this->l('Search page'), $link->getPageLink('search', true, $idLang))];
            case 'contact':
                return [$this->buildUrlOption($this->l('Contact page'), $link->getPageLink('contact', true, $idLang))];
            case 'new-products':
                return [$this->buildUrlOption($this->l('New products'), $link->getPageLink('new-products', true, $idLang))];
            case 'prices-drop':
                return [$this->buildUrlOption($this->l('Prices drop'), $link->getPageLink('prices-drop', true, $idLang))];
            case 'best-sales':
                return [$this->buildUrlOption($this->l('Best sales'), $link->getPageLink('best-sales', true, $idLang))];
            case 'sitemap':
                return [$this->buildUrlOption($this->l('Sitemap'), $link->getPageLink('sitemap', true, $idLang))];
            case 'manual':
                return [];
        }

        throw new Exception($this->l('Unsupported page type.'));
    }

    private function getCategoryUrls($idLang)
    {
        $rows = Db::getInstance()->executeS(
            'SELECT c.id_category, cl.name
            FROM `' . _DB_PREFIX_ . 'category` c
            INNER JOIN `' . _DB_PREFIX_ . 'category_lang` cl
                ON (cl.id_category = c.id_category AND cl.id_lang = ' . (int) $idLang . ' AND cl.id_shop = ' . (int) $this->context->shop->id . ')
            INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs
                ON (cs.id_category = c.id_category AND cs.id_shop = ' . (int) $this->context->shop->id . ')
            WHERE c.active = 1 AND c.id_category > 2
            ORDER BY c.id_category DESC
            LIMIT ' . (int) self::MAX_URLS
        );

        $urls = [];
        foreach ((array) $rows as $row) {
            $category = new Category((int) $row['id_category'], $idLang);
            $urls[] = $this->buildUrlOption($row['name'], $this->context->link->getCategoryLink($category));
        }

        return $urls;
    }

    private function getProductUrls($idLang)
    {
        $rows = Db::getInstance()->executeS(
            'SELECT p.id_product, pl.name
            FROM `' . _DB_PREFIX_ . 'product` p
            INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int) $idLang . ' AND pl.id_shop = ' . (int) $this->context->shop->id . ')
            INNER JOIN `' . _DB_PREFIX_ . 'product_shop` ps
                ON (ps.id_product = p.id_product AND ps.id_shop = ' . (int) $this->context->shop->id . ')
            WHERE ps.active = 1
            ORDER BY p.id_product DESC
            LIMIT ' . (int) self::MAX_URLS
        );

        $urls = [];
        foreach ((array) $rows as $row) {
            $product = new Product((int) $row['id_product'], false, $idLang, (int) $this->context->shop->id);
            $urls[] = $this->buildUrlOption($row['name'], $this->context->link->getProductLink($product));
        }

        return $urls;
    }

    private function getCmsUrls($idLang)
    {
        $rows = CMS::getCMSPages($idLang, null, true, (int) $this->context->shop->id);
        $urls = [];

        foreach ((array) $rows as $row) {
            if (count($urls) >= self::MAX_URLS) {
                break;
            }
            $cms = new CMS((int) $row['id_cms'], $idLang);
            $label = isset($row['meta_title']) ? $row['meta_title'] : '#' . (int) $row['id_cms'];
            $urls[] = $this->buildUrlOption($label, $this->context->link->getCMSLink($cms));
        }

        return $urls;
    }

    private function getManufacturerUrls($idLang)
    {
        $rows = Manufacturer::getManufacturers(false, $idLang, true, 1, self::MAX_URLS);
        $urls = [];

        foreach ((array) $rows as $row) {
            $manufacturer = new Manufacturer((int) $row['id_manufacturer'], $idLang);
            $urls[] = $this->buildUrlOption($row['name'], $this->context->link->getManufacturerLink($manufacturer));
        }

        return $urls;
    }

    private function getSupplierUrls($idLang)
    {
        $rows = Supplier::getSuppliers(false, $idLang, true, 1, self::MAX_URLS);
        $urls = [];

        foreach ((array) $rows as $row) {
            $supplier = new Supplier((int) $row['id_supplier'], $idLang);
            $urls[] = $this->buildUrlOption($row['name'], $this->context->link->getSupplierLink($supplier));
        }

        return $urls;
    }

    private function buildUrlOption($label, $url)
    {
        return [
            'label' => html_entity_decode((string) $label, ENT_QUOTES, 'UTF-8'),
            'url' => html_entity_decode((string) $url, ENT_QUOTES, 'UTF-8'),
        ];
    }

    private function analyzeUrl($url)
    {
        if (!$url) {
            throw new Exception($this->l('Please select or enter a URL.'));
        }

        if (!$this->isAllowedUrl($url)) {
            throw new Exception($this->l('Only URLs from the current shop are allowed.'));
        }

        $response = $this->fetchUrl($url);
        $html = $response['body'];

        if (trim($html) === '') {
            throw new Exception($this->l('The selected URL returned an empty response.'));
        }

        $assets = $this->extractAssets($html, $response['final_url']);
        if (empty($assets['css']) && empty($assets['js'])) {
            throw new Exception($this->l('No CSS or JavaScript assets were found in the rendered HTML.'));
        }

        return [
            'url' => $url,
            'final_url' => $response['final_url'],
            'status' => $response['status'],
            'coverage' => null,
            'counts' => [
                'css' => count($assets['css']),
                'js' => count($assets['js']),
                'total' => count($assets['css']) + count($assets['js']),
            ],
            'assets' => $assets,
        ];
    }

    private function analyzeCoverage($url, array $assets)
    {
        if (!$url) {
            throw new Exception($this->l('Please analyze a page before requesting coverage.'));
        }

        if (!$this->isAllowedUrl($url)) {
            throw new Exception($this->l('Coverage is allowed only for URLs from the current shop.'));
        }

        $normalizedAssets = $this->normalizeCoverageAssets($assets);
        if (empty($normalizedAssets)) {
            throw new Exception($this->l('No assets were provided for coverage analysis.'));
        }

        $payload = [
            'url' => $url,
            'platform' => 'prestashop',
            'variant' => $this->buildCoverageVariant(),
            'variant_hash' => $this->buildCoverageVariantHash($url),
            'warmup_token' => $this->buildCoverageWarmupToken($url),
            'timeout_ms' => 45000,
            'assets' => $normalizedAssets,
        ];

        $response = $this->postJson($this->getCoverageWorkerUrl() . '/analyze-asset-coverage', $payload);

        if (empty($response['ok'])) {
            $message = !empty($response['error']) ? $response['error'] : $this->l('Coverage analysis failed.');
            throw new Exception($message);
        }

        return [
            'worker_url' => $this->getCoverageWorkerUrl(),
            'duration_ms' => isset($response['duration_ms']) ? (int) $response['duration_ms'] : null,
            'stats' => isset($response['stats']) && is_array($response['stats']) ? $response['stats'] : [],
            'assets' => $this->normalizeCoverageResponseAssets(isset($response['assets']) && is_array($response['assets']) ? $response['assets'] : []),
        ];
    }

    private function isAllowedUrl($url)
    {
        $parts = parse_url($url);
        if (empty($parts['scheme']) || empty($parts['host']) || !in_array(Tools::strtolower($parts['scheme']), ['http', 'https'])) {
            return false;
        }

        $shopHosts = [
            parse_url($this->context->shop->getBaseURL(true, true), PHP_URL_HOST),
            parse_url($this->context->shop->getBaseURL(false, true), PHP_URL_HOST),
            Tools::getShopDomain(),
            Tools::getShopDomainSsl(),
        ];
        $shopHosts = array_filter(array_unique(array_map([$this, 'normalizeHost'], $shopHosts)));

        return in_array($this->normalizeHost($parts['host']), $shopHosts);
    }

    private function normalizeHost($host)
    {
        return preg_replace('/^www\./', '', Tools::strtolower((string) $host));
    }

    private function fetchUrl($url)
    {
        if (function_exists('curl_init')) {
            return $this->fetchUrlWithCurl($url);
        }

        $body = Tools::file_get_contents($url);
        if ($body === false) {
            throw new Exception($this->l('HTTP request failed.'));
        }

        return [
            'body' => $body,
            'status' => null,
            'final_url' => $url,
        ];
    }

    private function fetchUrlWithCurl($url)
    {
        $currentUrl = $url;
        $status = 0;
        $body = '';

        for ($redirects = 0; $redirects <= 5; $redirects++) {
            $ch = curl_init($currentUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'PrestaShop AssetsAnalyser/1.0');
            $response = curl_exec($ch);
            $error = curl_error($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            curl_close($ch);

            if ($response === false) {
                throw new Exception(sprintf($this->l('HTTP request failed: %s'), $error));
            }

            $headers = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);

            if ($status >= 300 && $status < 400) {
                $location = $this->extractRedirectLocation($headers);
                if (!$location) {
                    break;
                }

                $nextUrl = $this->absoluteUrl($location, $currentUrl);
                if (!$nextUrl || !$this->isAllowedUrl($nextUrl)) {
                    throw new Exception($this->l('The selected URL redirects outside the current shop.'));
                }

                $currentUrl = $nextUrl;
                continue;
            }

            break;
        }

        if ($status >= 300 && $status < 400) {
            throw new Exception($this->l('The selected URL has too many redirects.'));
        }

        if ($status >= 400 || $status === 0) {
            throw new Exception(sprintf($this->l('The selected URL returned HTTP status %d.'), $status));
        }

        return [
            'body' => $body,
            'status' => $status,
            'final_url' => $currentUrl,
        ];
    }

    private function extractRedirectLocation($headers)
    {
        if (preg_match_all('/^Location:\s*(.+)$/mi', $headers, $matches) && !empty($matches[1])) {
            return trim(end($matches[1]));
        }

        return null;
    }

    private function extractAssets($html, $baseUrl)
    {
        $dom = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded) {
            throw new Exception($this->l('Could not parse the rendered HTML.'));
        }

        $xpath = new DOMXPath($dom);
        $assets = [
            'css' => [],
            'js' => [],
        ];
        $seen = [
            'css' => [],
            'js' => [],
        ];

        foreach ($xpath->query('//link[@href]') as $node) {
            $rel = Tools::strtolower($node->getAttribute('rel'));
            if (strpos($rel, 'stylesheet') === false) {
                continue;
            }
            $url = $this->absoluteUrl($node->getAttribute('href'), $baseUrl);
            if (!$url || isset($seen['css'][$url])) {
                continue;
            }
            $seen['css'][$url] = true;
            $assets['css'][] = $this->buildAssetRow('css', $url);
        }

        foreach ($xpath->query('//script[@src]') as $node) {
            $url = $this->absoluteUrl($node->getAttribute('src'), $baseUrl);
            if (!$url || isset($seen['js'][$url])) {
                continue;
            }
            $seen['js'][$url] = true;
            $assets['js'][] = $this->buildAssetRow('js', $url);
        }

        return $assets;
    }

    private function absoluteUrl($assetUrl, $baseUrl)
    {
        $assetUrl = trim(html_entity_decode((string) $assetUrl, ENT_QUOTES, 'UTF-8'));
        if ($assetUrl === '' || strpos($assetUrl, 'data:') === 0 || strpos($assetUrl, 'javascript:') === 0) {
            return null;
        }

        if (preg_match('#^https?://#i', $assetUrl)) {
            return $assetUrl;
        }

        if (strpos($assetUrl, '//') === 0) {
            $scheme = parse_url($baseUrl, PHP_URL_SCHEME) ?: 'https';
            return $scheme . ':' . $assetUrl;
        }

        $base = parse_url($baseUrl);
        if (!$base || empty($base['host'])) {
            return $assetUrl;
        }

        $scheme = isset($base['scheme']) ? $base['scheme'] : 'https';
        $port = isset($base['port']) ? ':' . $base['port'] : '';

        if (strpos($assetUrl, '/') === 0) {
            return $scheme . '://' . $base['host'] . $port . $assetUrl;
        }

        $path = isset($base['path']) ? $base['path'] : '/';
        $directory = preg_replace('#/[^/]*$#', '/', $path);

        return $scheme . '://' . $base['host'] . $port . $this->normalizePath($directory . $assetUrl);
    }

    private function normalizePath($path)
    {
        $segments = [];
        foreach (explode('/', $path) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }
            if ($segment === '..') {
                array_pop($segments);
                continue;
            }
            $segments[] = $segment;
        }

        return '/' . implode('/', $segments);
    }

    private function buildAssetRow($type, $url)
    {
        $path = $this->normalizedAssetPath($url);
        $attribution = $this->attributeAsset($url, $path);

        return [
            'type' => $type,
            'url' => $url,
            'path' => $path,
            'source_type' => $attribution['source_type'],
            'source_label' => $attribution['source_label'],
            'module' => $attribution['module'],
            'confidence' => $attribution['confidence'],
            'note' => $attribution['note'],
            'coverage_status' => 'not_analyzed',
            'covered' => null,
            'original_bytes' => null,
            'used_bytes' => null,
            'unused_bytes' => null,
            'unused_ratio' => null,
            'unused_percent' => null,
            'ranges_count' => null,
        ];
    }

    private function normalizedAssetPath($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        return $path ? rawurldecode($path) : $url;
    }

    private function attributeAsset($url, $path)
    {
        $host = parse_url($url, PHP_URL_HOST);
        $shopHost = parse_url($this->context->shop->getBaseURL(true, true), PHP_URL_HOST);
        if ($host && $shopHost && $this->normalizeHost($host) !== $this->normalizeHost($shopHost)) {
            return $this->assetAttribution('external', $this->l('External / CDN'), null, 'certain', '');
        }

        $themeName = $this->context->shop->theme ? $this->context->shop->theme->getName() : '';
        $pathForMatch = str_replace('\\', '/', $path);

        if (preg_match('#/(?:cache|assets/cache|themes/[^/]+/assets/cache|themes/[^/]+/cache)/#i', $pathForMatch)
            || preg_match('#/(?:css|js)/[a-f0-9]{8,}[_-]#i', $pathForMatch)
            || preg_match('#/cache/.*\.(?:css|js)$#i', $pathForMatch)
        ) {
            return $this->assetAttribution('ccc', $this->l('CCC / cache bundle'), null, 'likely', $this->l('Disable CSS/JS CCC for clearer attribution.'));
        }

        if (preg_match('#/themes/[^/]+/modules/([^/]+)/#i', $pathForMatch, $matches)) {
            return $this->assetAttribution('theme_module_override', $this->l('Theme module override'), $matches[1], 'likely', '');
        }

        if (preg_match('#/modules/([^/]+)/#i', $pathForMatch, $matches)) {
            return $this->assetAttribution('module', $this->l('Module'), $matches[1], 'certain', '');
        }

        if ($themeName && preg_match('#/themes/' . preg_quote($themeName, '#') . '/assets/#i', $pathForMatch)) {
            return $this->assetAttribution('theme', $this->l('Theme'), null, 'certain', '');
        }

        if (preg_match('#/(?:assets/css|assets/js)/#i', $pathForMatch)) {
            return $this->assetAttribution('theme', $this->l('Theme'), null, 'likely', '');
        }

        if (preg_match('#^/(?:js|css)/#i', $pathForMatch) || preg_match('#/themes/core\.js$#i', $pathForMatch)) {
            return $this->assetAttribution('core', $this->l('PrestaShop core'), null, 'likely', '');
        }

        return $this->assetAttribution('unknown', $this->l('Unknown'), null, 'unknown', '');
    }

    private function assetAttribution($sourceType, $sourceLabel, $module, $confidence, $note)
    {
        return [
            'source_type' => $sourceType,
            'source_label' => $sourceLabel,
            'module' => $module,
            'confidence' => $confidence,
            'note' => $note,
        ];
    }

    private function normalizeCoverageAssets(array $assets)
    {
        $normalized = [];

        foreach ($assets as $asset) {
            if (!is_array($asset)) {
                continue;
            }

            $type = isset($asset['type']) ? Tools::strtolower((string) $asset['type']) : '';
            $url = isset($asset['url']) ? trim((string) $asset['url']) : '';

            if (!in_array($type, ['css', 'js']) || !$url || !Validate::isAbsoluteUrl($url)) {
                continue;
            }

            $normalized[] = [
                'type' => $type,
                'url' => $url,
            ];

            if (count($normalized) >= 128) {
                break;
            }
        }

        return $normalized;
    }

    private function buildCoverageVariant()
    {
        $variant = [
            'auth_state' => 'guest',
            'device' => 'desktop',
        ];

        if (!empty($this->context->language->iso_code)) {
            $variant['language'] = (string) $this->context->language->iso_code;
        }

        if (!empty($this->context->currency->iso_code)) {
            $variant['currency'] = (string) $this->context->currency->iso_code;
        }

        if (!empty($this->context->shop->domain)) {
            $variant['shop'] = (string) $this->context->shop->domain;
        }

        return $variant;
    }

    private function buildCoverageVariantHash($url)
    {
        return substr(sha1($url . '|' . (int) $this->context->language->id . '|desktop'), 0, 20);
    }

    private function buildCoverageWarmupToken($url)
    {
        return substr(hash_hmac('sha256', $url, _COOKIE_KEY_), 0, 32);
    }

    private function getCoverageWorkerUrl()
    {
        return rtrim(self::DEFAULT_COVERAGE_WORKER_URL, '/');
    }

    private function postJson($url, array $payload)
    {
        $body = json_encode($payload);
        if ($body === false) {
            throw new Exception($this->l('Could not encode the coverage request.'));
        }

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'Content-Length: ' . Tools::strlen($body),
            ]);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            $responseBody = curl_exec($ch);
            $error = curl_error($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($responseBody === false) {
                throw new Exception(sprintf($this->l('Coverage worker request failed: %s'), $error));
            }

            $decoded = json_decode($responseBody, true);
            if (!is_array($decoded)) {
                throw new Exception(sprintf($this->l('Coverage worker returned invalid JSON (HTTP %d).'), $status));
            }

            if ($status >= 400) {
                $message = !empty($decoded['error']) ? $decoded['error'] : sprintf($this->l('Coverage worker returned HTTP status %d.'), $status);
                throw new Exception($message);
            }

            return $decoded;
        }

        throw new Exception($this->l('cURL is required for coverage analysis.'));
    }

    private function normalizeCoverageResponseAssets(array $assets)
    {
        $normalized = [];

        foreach ($assets as $asset) {
            if (!is_array($asset) || empty($asset['asset_url']) || empty($asset['asset_type'])) {
                continue;
            }

            $type = Tools::strtolower((string) $asset['asset_type']);
            if (!in_array($type, ['css', 'js'])) {
                continue;
            }

            $unusedRatio = isset($asset['unused_ratio']) && $asset['unused_ratio'] !== null
                ? (float) $asset['unused_ratio']
                : null;

            $normalized[] = [
                'type' => $type,
                'url' => (string) $asset['asset_url'],
                'coverage_status' => !empty($asset['covered']) ? 'analyzed' : 'unavailable',
                'covered' => !empty($asset['covered']),
                'original_bytes' => isset($asset['original_bytes']) ? (int) $asset['original_bytes'] : null,
                'used_bytes' => isset($asset['used_bytes']) ? (int) $asset['used_bytes'] : null,
                'unused_bytes' => isset($asset['unused_bytes']) ? (int) $asset['unused_bytes'] : null,
                'unused_ratio' => $unusedRatio,
                'unused_percent' => $unusedRatio !== null ? round($unusedRatio * 100, 2) : null,
                'ranges_count' => isset($asset['ranges_count']) ? (int) $asset['ranges_count'] : null,
            ];
        }

        return $normalized;
    }
}
