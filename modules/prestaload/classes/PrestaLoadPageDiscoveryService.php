<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaLoadPageDiscoveryService
{
    /**
     * @var Prestaload
     */
    private $module;

    /**
     * @var Context
     */
    private $context;

    public function __construct(Prestaload $module)
    {
        $this->module = $module;
        $this->context = Context::getContext();
    }

    /**
     * @return array{items:array<int,array<string,mixed>>,total:int,page:int,per_page:int,has_more:bool}
     */
    public function discoverForShop($shopId, $pageType, $page, $perPage)
    {
        if (!class_exists('Shop') || !class_exists('Language') || !class_exists('Link')) {
            return $this->buildPageBatchResult([], 0, $page, $perPage);
        }

        $shopId = (int) $shopId;
        $pageType = $this->normalizePageType($pageType);
        $page = max(1, (int) $page);
        $perPage = max(1, min(1000, (int) $perPage));

        if ($shopId <= 0 || $pageType === null) {
            return $this->buildPageBatchResult([], 0, $page, $perPage);
        }

        return $this->withShopContext($shopId, function () use ($shopId, $pageType, $page, $perPage) {
            switch ($pageType) {
                case 'home':
                    return $this->collectHomePageBatch($shopId, $page, $perPage);

                case 'category':
                    return $this->collectCategoryPageBatch($shopId, $page, $perPage);

                case 'product':
                    return $this->collectProductPageBatch($shopId, $page, $perPage);

                case 'cms':
                    return $this->collectCmsPageBatch($shopId, $page, $perPage);
            }

            return $this->buildPageBatchResult([], 0, $page, $perPage);
        });
    }

    public function normalizePageType($pageType)
    {
        $pageType = trim((string) $pageType);
        if ($pageType === '') {
            return null;
        }

        $allowed = ['home', 'category', 'product', 'cms'];

        return in_array($pageType, $allowed, true) ? $pageType : null;
    }

    private function collectHomePageBatch($shopId, $page, $perPage)
    {
        $languages = $this->getActiveLanguagesForShop($shopId);
        $total = count($languages);
        $offset = ($page - 1) * $perPage;
        $selected = array_slice($languages, $offset, $perPage);
        $items = [];
        $link = $this->context->link ?: new Link();

        foreach ($selected as $language) {
            $langId = (int) $language['id_lang'];
            $langIso = (string) $language['iso_code'];
            $homeUrl = $this->normalizeDiscoveredUrl($link->getPageLink('index', true, $langId, null, false, $shopId));
            $pageTitle = $this->resolveHomePageTitle($shopId, $langId);

            if ($homeUrl === '') {
                continue;
            }

            $items[] = $this->buildDiscoveredPageRow('home', 'home', 0, $langIso, $homeUrl, $pageTitle);
        }

        return $this->buildPageBatchResult($items, $total, $page, $perPage);
    }

    private function collectCategoryPageBatch($shopId, $page, $perPage)
    {
        $link = $this->context->link ?: new Link();
        $languages = $this->getActiveLanguagesForShop($shopId);
        $baseCount = $this->getCategoryBaseCount($shopId);
        $total = $baseCount * count($languages);
        $items = $this->collectRowsForLanguageWindow($languages, $baseCount, $page, $perPage, function ($language, $languageOffset, $languageLimit) use ($shopId, $link) {
            $langId = (int) $language['id_lang'];
            $langIso = (string) $language['iso_code'];
            $rows = $this->getCategoryRows($shopId, $langId, $languageOffset, $languageLimit);
            $items = [];

            foreach ($rows as $row) {
                $categoryId = isset($row['id_category']) ? (int) $row['id_category'] : 0;
                if ($categoryId <= 1) {
                    continue;
                }

                $categoryUrl = $this->normalizeDiscoveredUrl($link->getCategoryLink($categoryId, null, $langId, null, $shopId));
                if ($categoryUrl === '') {
                    continue;
                }

                $items[] = $this->buildDiscoveredPageRow(
                    'category',
                    'category',
                    $categoryId,
                    $langIso,
                    $categoryUrl,
                    isset($row['name']) ? (string) $row['name'] : ''
                );
            }

            return $items;
        });

        return $this->buildPageBatchResult($items, $total, $page, $perPage);
    }

    private function collectProductPageBatch($shopId, $page, $perPage)
    {
        $link = $this->context->link ?: new Link();
        $languages = $this->getActiveLanguagesForShop($shopId);
        $baseCount = $this->getProductBaseCount($shopId);
        $total = $baseCount * count($languages);
        $items = $this->collectRowsForLanguageWindow($languages, $baseCount, $page, $perPage, function ($language, $languageOffset, $languageLimit) use ($shopId, $link) {
            $langId = (int) $language['id_lang'];
            $langIso = (string) $language['iso_code'];
            $rows = Product::getProducts($langId, $languageOffset, $languageLimit, 'id_product', 'ASC', false, true, $this->context);
            $items = [];

            foreach ($rows as $row) {
                $productId = isset($row['id_product']) ? (int) $row['id_product'] : 0;
                if ($productId <= 0) {
                    continue;
                }

                $productUrl = $this->normalizeDiscoveredUrl($link->getProductLink($productId, null, null, null, $langId, $shopId));
                if ($productUrl === '') {
                    continue;
                }

                $items[] = $this->buildDiscoveredPageRow(
                    'product',
                    'product',
                    $productId,
                    $langIso,
                    $productUrl,
                    isset($row['name']) ? (string) $row['name'] : ''
                );
            }

            return $items;
        });

        return $this->buildPageBatchResult($items, $total, $page, $perPage);
    }

    private function collectCmsPageBatch($shopId, $page, $perPage)
    {
        $link = $this->context->link ?: new Link();
        $languages = $this->getActiveLanguagesForShop($shopId);
        $baseCount = $this->getCmsBaseCount($shopId);
        $total = $baseCount * count($languages);
        $items = $this->collectRowsForLanguageWindow($languages, $baseCount, $page, $perPage, function ($language, $languageOffset, $languageLimit) use ($shopId, $link) {
            $langId = (int) $language['id_lang'];
            $langIso = (string) $language['iso_code'];
            $rows = $this->getCmsRows($shopId, $langId, $languageOffset, $languageLimit);
            $items = [];

            foreach ($rows as $row) {
                $cmsId = isset($row['id_cms']) ? (int) $row['id_cms'] : 0;
                if ($cmsId <= 0) {
                    continue;
                }

                $cmsUrl = $this->normalizeDiscoveredUrl($link->getCMSLink($cmsId, null, null, $langId, $shopId));
                if ($cmsUrl === '') {
                    continue;
                }

                $items[] = $this->buildDiscoveredPageRow(
                    'cms',
                    'cms',
                    $cmsId,
                    $langIso,
                    $cmsUrl,
                    isset($row['meta_title']) ? (string) $row['meta_title'] : ''
                );
            }

            return $items;
        });

        return $this->buildPageBatchResult($items, $total, $page, $perPage);
    }

    private function getActiveLanguagesForShop($shopId)
    {
        $languages = Language::getLanguages(true, $shopId, false);

        return array_values(array_filter(array_map(function ($language) {
            $langId = isset($language['id_lang']) ? (int) $language['id_lang'] : 0;
            if ($langId <= 0) {
                return null;
            }

            return [
                'id_lang' => $langId,
                'iso_code' => isset($language['iso_code']) ? (string) $language['iso_code'] : '',
            ];
        }, is_array($languages) ? $languages : [])));
    }

    private function collectRowsForLanguageWindow(array $languages, $baseCount, $page, $perPage, callable $collector)
    {
        $items = [];
        $globalOffset = max(0, ($page - 1) * $perPage);
        $remaining = $perPage;

        if ($baseCount <= 0 || $remaining <= 0) {
            return [];
        }

        foreach ($languages as $language) {
            if ($remaining <= 0) {
                break;
            }

            if ($globalOffset >= $baseCount) {
                $globalOffset -= $baseCount;
                continue;
            }

            $languageOffset = $globalOffset;
            $languageLimit = min($remaining, $baseCount - $languageOffset);
            $globalOffset = 0;

            if ($languageLimit <= 0) {
                continue;
            }

            $batch = $collector($language, $languageOffset, $languageLimit);
            if (is_array($batch) && $batch !== []) {
                $items = array_merge($items, $batch);
                $remaining -= count($batch);
            }
        }

        return $items;
    }

    /**
     * @return array{items:array<int,array<string,mixed>>,total:int,page:int,per_page:int,has_more:bool}
     */
    private function buildPageBatchResult(array $items, $total, $page, $perPage)
    {
        $total = max(0, (int) $total);
        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);

        return [
            'items' => array_values($items),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'has_more' => ($page * $perPage) < $total,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function buildDiscoveredPageRow($pageType, $entityType, $entityId, $languageIso, $url, $pageTitle = '')
    {
        return [
            'page_type' => (string) $pageType,
            'entity_type' => (string) $entityType,
            'entity_id' => (int) $entityId,
            'language_iso' => (string) $languageIso,
            'url' => (string) $url,
            'canonical_url' => (string) $url,
            'page_title' => trim((string) $pageTitle),
        ];
    }

    private function resolveHomePageTitle($shopId, $langId)
    {
        return (string) Configuration::get('PS_SHOP_NAME', null, null, (int) $shopId);
    }

    private function withShopContext($shopId, callable $callback)
    {
        $shopId = (int) $shopId;
        $context = Context::getContext();
        $previousShop = isset($context->shop) ? $context->shop : null;
        $previousLanguage = isset($context->language) ? $context->language : null;
        $previousShopContext = Shop::getContext();
        $previousShopId = $previousShop !== null && isset($previousShop->id) ? (int) $previousShop->id : null;

        Shop::setContext(Shop::CONTEXT_SHOP, $shopId);
        $context->shop = new Shop($shopId);

        $languageIds = Language::getLanguages(true, $shopId, true);
        if (is_array($languageIds) && !empty($languageIds)) {
            $context->language = new Language((int) reset($languageIds));
        }

        try {
            return $callback();
        } finally {
            if ($previousShop !== null) {
                $context->shop = $previousShop;
            }

            if ($previousLanguage !== null) {
                $context->language = $previousLanguage;
            }

            if ($previousShopId !== null) {
                Shop::setContext($previousShopContext, $previousShopId);
            } else {
                Shop::resetContext();
            }
        }
    }

    private function getCategoryBaseCount($shopId)
    {
        $query = new DbQuery();
        $query->select('COUNT(*)');
        $query->from('category', 'c');
        $query->innerJoin('category_shop', 'cs', 'c.id_category = cs.id_category AND cs.id_shop = ' . (int) $shopId);
        $query->where('c.active = 1');
        $query->where('c.id_category > 1');

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function getCategoryRows($shopId, $langId, $offset, $limit)
    {
        $query = new DbQuery();
        $query->select('c.id_category, cl.link_rewrite, cl.name');
        $query->from('category', 'c');
        $query->innerJoin('category_shop', 'cs', 'c.id_category = cs.id_category AND cs.id_shop = ' . (int) $shopId);
        $query->leftJoin('category_lang', 'cl', 'c.id_category = cl.id_category AND cl.id_lang = ' . (int) $langId . ' AND cl.id_shop = ' . (int) $shopId);
        $query->where('c.active = 1');
        $query->where('c.id_category > 1');
        $query->orderBy('c.level_depth ASC, cs.position ASC');
        $query->limit((int) $limit, (int) $offset);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query) ?: [];
    }

    private function getProductBaseCount($shopId)
    {
        $query = new DbQuery();
        $query->select('COUNT(*)');
        $query->from('product', 'p');
        $query->innerJoin('product_shop', 'ps', 'p.id_product = ps.id_product AND ps.id_shop = ' . (int) $shopId);
        $query->where('ps.visibility IN ("both","catalog")');
        $query->where('ps.active = 1');

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    private function getCmsBaseCount($shopId)
    {
        $query = new DbQuery();
        $query->select('COUNT(*)');
        $query->from('cms', 'c');
        $query->innerJoin('cms_shop', 'cs', 'c.id_cms = cs.id_cms AND cs.id_shop = ' . (int) $shopId);
        $query->where('c.active = 1');

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function getCmsRows($shopId, $langId, $offset, $limit)
    {
        $query = new DbQuery();
        $query->select('c.id_cms, l.link_rewrite, l.meta_title');
        $query->from('cms', 'c');
        $query->innerJoin('cms_shop', 'cs', 'c.id_cms = cs.id_cms AND cs.id_shop = ' . (int) $shopId);
        $query->innerJoin('cms_lang', 'l', 'c.id_cms = l.id_cms AND l.id_lang = ' . (int) $langId . ' AND l.id_shop = ' . (int) $shopId);
        $query->where('c.active = 1');
        $query->orderBy('c.position ASC');
        $query->limit((int) $limit, (int) $offset);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query) ?: [];
    }

    private function normalizeDiscoveredUrl($url)
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }

        $normalized = preg_replace('#(?<!:)//+#', '/', $url);

        return is_string($normalized) && $normalized !== '' ? $normalized : $url;
    }
}
