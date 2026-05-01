<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

/**
 * Front controller: GET /module/prestaload/pages?site=URL&page=1&per_page=50
 *
 * Returns a paginated list of public URLs for the PrestaShop store, covering:
 *   - Home page
 *   - Product pages
 *   - Category pages
 *   - CMS (static) pages
 *
 * The endpoint paginates at the SQL/source level so large stores do not need
 * to generate every URL before returning page 1.
 *
 * Authentication: X-Prestaload-Key header must equal hash('sha256', stored_api_key).
 */
class PrestaloadPagesModuleFrontController extends ModuleFrontController
{
    public function initContent(): void
    {
        header('Content-Type: application/json');

        if (! $this->verifyServerRequest()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized.']);
            exit;
        }

        $page    = max(1, (int) Tools::getValue('page', 1));
        $perPage = min(100, max(1, (int) Tools::getValue('per_page', 50)));

        if (! $this->applyShopContext((string) Tools::getValue('site', ''))) {
            http_response_code(404);
            echo json_encode(['error' => 'Requested shop site was not found.']);
            exit;
        }

        $total    = $this->countAllUrls();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page     = min($page, $lastPage);
        $offset   = ($page - 1) * $perPage;

        echo json_encode([
            'data' => $this->collectPaginatedUrls($offset, $perPage),
            'meta' => [
                'current_page' => $page,
                'last_page'    => $lastPage,
                'total'        => $total,
                'per_page'     => $perPage,
            ],
        ]);
        exit;
    }

    /**
     * Build only the requested slice. Order: home -> products -> categories -> CMS pages.
     * This avoids expensive full-store link generation on the first page request.
     *
     * @return array<int, array{url: string, type: string, title: string}>
     */
    private function collectPaginatedUrls(int $offset, int $limit): array
    {
        $urls = [];
        $segments = [
            'home'     => 1,
            'product'  => $this->countProducts(),
            'category' => $this->countCategories(),
            'cms'      => $this->countCmsPages(),
        ];

        foreach ($segments as $segment => $count) {
            if ($limit <= 0) {
                break;
            }

            if ($offset >= $count) {
                $offset -= $count;
                continue;
            }

            $take = min($limit, $count - $offset);
            $urls = array_merge($urls, $this->fetchSegmentUrls($segment, $offset, $take));
            $limit -= $take;
            $offset = 0;
        }

        return $urls;
    }

    private function countAllUrls(): int
    {
        // Counts are DB-level and may be slightly higher than what isValidFrontUrl
        // accepts. Invalid URLs are filtered at generation time, so the last page
        // may return fewer items than per_page. The caller handles this correctly.
        return 1 + $this->countProducts() + $this->countCategories() + $this->countCmsPages();
    }

    /**
     * @return array<int, array{url: string, type: string, title: string}>
     */
    private function fetchSegmentUrls(string $segment, int $offset, int $limit): array
    {
        return match ($segment) {
            'home'     => $this->fetchHomeUrl($offset, $limit),
            'product'  => $this->fetchProductUrls($offset, $limit),
            'category' => $this->fetchCategoryUrls($offset, $limit),
            'cms'      => $this->fetchCmsUrls($offset, $limit),
            default    => [],
        };
    }

    /**
     * @return array<int, array{url: string, type: string, title: string}>
     */
    private function fetchHomeUrl(int $offset, int $limit): array
    {
        if ($offset > 0 || $limit <= 0) {
            return [];
        }

        $context = Context::getContext();

        return [[
            'url'   => rtrim($context->shop->getBaseURL(true), '/'),
            'type'  => 'home',
            'title' => Configuration::get('PS_SHOP_NAME'),
        ]];
    }

    /**
     * @return array<int, array{url: string, type: string, title: string}>
     */
    private function fetchProductUrls(int $offset, int $limit): array
    {
        $context = Context::getContext();
        $idLang  = (int) $context->language->id;
        $link    = $context->link;
        $urls    = [];

        $products = Db::getInstance()->executeS(
            'SELECT DISTINCT p.id_product, pl.name
             FROM `' . _DB_PREFIX_ . 'product` p
             INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl
               ON pl.id_product = p.id_product
               AND pl.id_lang = ' . (int) $idLang . '
               AND pl.id_shop = ' . (int) $context->shop->id . '
             INNER JOIN `' . _DB_PREFIX_ . 'product_shop` ps
               ON ps.id_product = p.id_product
               AND ps.id_shop = ' . (int) $context->shop->id . '
             WHERE p.active = 1
               AND ps.active = 1
               AND ps.visibility != "none"
             ORDER BY p.id_product ASC
             LIMIT ' . (int) $offset . ', ' . (int) $limit
        );

        $seen = [];

        foreach ((array) $products as $row) {
            $productUrl = $link->getProductLink(
                (int) $row['id_product'],
                null, null, null, $idLang, $context->shop->id
            );

            if (! $productUrl || ! $this->isValidFrontUrl($productUrl, 'product')) {
                continue;
            }

            $normalized = rtrim($productUrl, '/');

            if (isset($seen[$normalized])) {
                continue;
            }

            $seen[$normalized] = true;
            $urls[] = [
                'url'   => $normalized,
                'type'  => 'product',
                'title' => $row['name'],
            ];
        }

        return $urls;
    }

    /**
     * @return array<int, array{url: string, type: string, title: string}>
     */
    private function fetchCategoryUrls(int $offset, int $limit): array
    {
        $context = Context::getContext();
        $idLang  = (int) $context->language->id;
        $link    = $context->link;
        $urls    = [];

        $categories = Db::getInstance()->executeS(
            'SELECT DISTINCT c.id_category, cl.name
             FROM `' . _DB_PREFIX_ . 'category` c
             INNER JOIN `' . _DB_PREFIX_ . 'category_lang` cl
               ON cl.id_category = c.id_category
               AND cl.id_lang = ' . (int) $idLang . '
               AND cl.id_shop = ' . (int) $context->shop->id . '
             INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs
               ON cs.id_category = c.id_category
               AND cs.id_shop = ' . (int) $context->shop->id . '
             WHERE c.active = 1 AND c.id_parent > 0
               AND c.id_category != ' . (int) Configuration::get('PS_HOME_CATEGORY') . '
               AND c.id_category != ' . (int) Configuration::get('PS_ROOT_CATEGORY') . '
             ORDER BY c.id_category ASC
             LIMIT ' . (int) $offset . ', ' . (int) $limit
        );

        $seen = [];

        foreach ((array) $categories as $row) {
            $catUrl = $link->getCategoryLink((int) $row['id_category'], null, $idLang);

            if (! $catUrl || ! $this->isValidFrontUrl($catUrl, 'category')) {
                continue;
            }

            $normalized = rtrim($catUrl, '/');

            if (isset($seen[$normalized])) {
                continue;
            }

            $seen[$normalized] = true;
            $urls[] = [
                'url'   => $normalized,
                'type'  => 'category',
                'title' => $row['name'],
            ];
        }

        return $urls;
    }

    /**
     * @return array<int, array{url: string, type: string, title: string}>
     */
    private function fetchCmsUrls(int $offset, int $limit): array
    {
        $context = Context::getContext();
        $idLang  = (int) $context->language->id;
        $link    = $context->link;
        $urls    = [];

        $cmsPages = Db::getInstance()->executeS(
            'SELECT DISTINCT c.id_cms, cl.meta_title
             FROM `' . _DB_PREFIX_ . 'cms` c
             INNER JOIN `' . _DB_PREFIX_ . 'cms_lang` cl
               ON cl.id_cms = c.id_cms
               AND cl.id_lang = ' . (int) $idLang . '
               AND cl.id_shop = ' . (int) $context->shop->id . '
             INNER JOIN `' . _DB_PREFIX_ . 'cms_shop` cs
               ON cs.id_cms = c.id_cms
               AND cs.id_shop = ' . (int) $context->shop->id . '
             WHERE c.active = 1
             ORDER BY c.id_cms ASC
             LIMIT ' . (int) $offset . ', ' . (int) $limit
        );

        $seen = [];

        foreach ((array) $cmsPages as $row) {
            $cmsUrl = $link->getCMSLink((int) $row['id_cms'], null, null, $idLang);

            if (! $cmsUrl || ! $this->isValidFrontUrl($cmsUrl, 'cms')) {
                continue;
            }

            $normalized = rtrim($cmsUrl, '/');

            if (isset($seen[$normalized])) {
                continue;
            }

            $seen[$normalized] = true;
            $urls[] = [
                'url'   => $normalized,
                'type'  => 'cms',
                'title' => $row['meta_title'],
            ];
        }

        return $urls;
    }

    private function countProducts(): int
    {
        $context = Context::getContext();

        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(DISTINCT p.id_product)
             FROM `' . _DB_PREFIX_ . 'product` p
             INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl
               ON pl.id_product = p.id_product
               AND pl.id_lang = ' . (int) $context->language->id . '
               AND pl.id_shop = ' . (int) $context->shop->id . '
             INNER JOIN `' . _DB_PREFIX_ . 'product_shop` ps
               ON ps.id_product = p.id_product
               AND ps.id_shop = ' . (int) $context->shop->id . '
             WHERE p.active = 1
               AND ps.active = 1
               AND ps.visibility != "none"'
        );
    }

    private function countCategories(): int
    {
        $context = Context::getContext();

        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(DISTINCT c.id_category)
             FROM `' . _DB_PREFIX_ . 'category` c
             INNER JOIN `' . _DB_PREFIX_ . 'category_lang` cl
               ON cl.id_category = c.id_category
               AND cl.id_lang = ' . (int) $context->language->id . '
               AND cl.id_shop = ' . (int) $context->shop->id . '
             INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs
               ON cs.id_category = c.id_category
               AND cs.id_shop = ' . (int) $context->shop->id . '
             WHERE c.active = 1 AND c.id_parent > 0
               AND c.id_category != ' . (int) Configuration::get('PS_HOME_CATEGORY') . '
               AND c.id_category != ' . (int) Configuration::get('PS_ROOT_CATEGORY')
        );
    }

    private function countCmsPages(): int
    {
        $context = Context::getContext();

        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(DISTINCT c.id_cms)
             FROM `' . _DB_PREFIX_ . 'cms` c
             INNER JOIN `' . _DB_PREFIX_ . 'cms_lang` cl
               ON cl.id_cms = c.id_cms
               AND cl.id_lang = ' . (int) $context->language->id . '
               AND cl.id_shop = ' . (int) $context->shop->id . '
             INNER JOIN `' . _DB_PREFIX_ . 'cms_shop` cs
               ON cs.id_cms = c.id_cms
               AND cs.id_shop = ' . (int) $context->shop->id . '
             WHERE c.active = 1'
        );
    }

    /**
     * Return true when a URL is a valid cacheable front page of the expected type.
     *
     * Rejects:
     *   - index.php URLs where the controller is not the canonical one for this type
     *     (catches third-party module controllers like "product_rule", "product_pack", etc.)
     *   - URLs with dangling empty route parameters (e.g. ean13=) from unresolved
     *     product combination rewrites
     *   - URLs generated for another shop/domain
     *   - URLs missing a path or pointing at non-HTTP schemes
     */
    private function isValidFrontUrl(string $url, string $expectedType): bool
    {
        $parsed = parse_url($url);

        if (empty($parsed['host'])) {
            return false;
        }

        // Must be http or https
        $scheme = strtolower($parsed['scheme'] ?? 'https');
        if (! in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        if (! $this->isCurrentShopHost($parsed['host'])) {
            return false;
        }

        $hasQuery = isset($parsed['query']) && $parsed['query'] !== '';

        if (! $hasQuery) {
            // Friendly URL on the current shop host: no further checks needed.
            return true;
        }

        parse_str($parsed['query'], $params);

        // Reject if controller exists but does not match the expected PS controller.
        $controllerMap = [
            'product'  => 'product',
            'category' => 'category',
            'cms'      => 'cms',
        ];

        if (isset($params['controller'])) {
            $expected = $controllerMap[$expectedType] ?? $expectedType;

            if ($params['controller'] !== $expected) {
                return false;
            }
        }

        // Reject unresolved route parameters while allowing harmless optional
        // metadata fields, such as an empty meta_keywords parameter on CMS links.
        foreach (['controller', 'id', 'id_product', 'id_category', 'id_cms', 'rewrite', 'ean13'] as $routeParam) {
            if (array_key_exists($routeParam, $params) && $this->isEmptyRouteParam($params[$routeParam])) {
                return false;
            }
        }

        return true;
    }

    private function isEmptyRouteParam($value): bool
    {
        if (is_array($value)) {
            return true;
        }

        return trim((string) $value) === '';
    }

    private function isCurrentShopHost(string $host): bool
    {
        $shop = Context::getContext()->shop;
        $host = strtolower($host);
        $domain = strtolower((string) $shop->domain);
        $sslDomain = strtolower((string) $shop->domain_ssl);

        return $host === $domain || $host === $sslDomain;
    }

    private function applyShopContext(string $siteUrl): bool
    {
        $normalizedUrl = $this->normalizeUrl($siteUrl);

        if ($normalizedUrl === '') {
            return false;
        }

        if (! Shop::isFeatureActive()) {
            return $normalizedUrl === $this->normalizeUrl(Context::getContext()->shop->getBaseURL(true));
        }

        foreach (Shop::getShops(true) as $shop) {
            $shopObj = new Shop((int) $shop['id_shop']);
            $shopUrl = 'https://' . $shopObj->domain_ssl . $shopObj->getBaseURI();

            if ($this->normalizeUrl($shopUrl) === $normalizedUrl) {
                Shop::setContext(Shop::CONTEXT_SHOP, (int) $shop['id_shop']);
                Context::getContext()->shop = $shopObj;

                return true;
            }
        }

        return false;
    }

    private function normalizeUrl(string $url): string
    {
        $parsed = parse_url(strtolower(rtrim($url, '/')));
        $host   = $parsed['host'] ?? '';
        $path   = $parsed['path'] ?? '';

        return $host . ($path !== '' && $path !== '/' ? rtrim($path, '/') : '');
    }

    /**
     * Validate the X-Prestaload-Key header.
     */
    private function verifyServerRequest(): bool
    {
        $receivedHash = $_SERVER['HTTP_X_PRESTALOAD_KEY'] ?? '';
        $settings     = json_decode(Configuration::get(Prestaload::CONFIG_KEY, '{}'), true) ?: [];

        if (empty($settings['api_key']) || empty($receivedHash)) {
            return false;
        }

        return hash_equals(hash('sha256', $settings['api_key']), $receivedHash);
    }
}
