<?php
/**
 * Discovers a practical list of front-office pages that merchants can scan.
 *
 * V1 keeps the scope intentionally small: homepage plus a few sample category,
 * product, and CMS pages. That is enough to drive asset rules without flooding
 * the UI with every possible URL in the catalog.
 */

class PrestaLoadAssetPageRegistry
{
    /**
     * @var Context
     */
    private $context;
    private $settings;

    public function __construct(Context $context, PrestaLoadCacheSettings $settings)
    {
        $this->context = $context;
        $this->settings = $settings;
    }

    /**
     * Returns a compact list of candidate pages for scanning.
     */
    public function getPages()
    {
        $pages = [];
        $pages[] = [
            'key' => 'home',
            'type' => 'home',
            'label' => 'Homepage',
            'url' => $this->normalizeScanUrl($this->context->link->getPageLink('index', true)),
        ];

        $pages = array_merge($pages, $this->buildCategoryPages());
        $pages = array_merge($pages, $this->buildProductPages());
        $pages = array_merge($pages, $this->buildCmsPages());

        return $pages;
    }

    private function buildCategoryPages()
    {
        $rows = Db::getInstance()->executeS(
            'SELECT c.id_category
             FROM ' . _DB_PREFIX_ . 'category c
             INNER JOIN ' . _DB_PREFIX_ . 'category_shop cs ON (cs.id_category = c.id_category AND cs.id_shop = ' . (int) $this->context->shop->id . ')
             WHERE c.active = 1 AND c.is_root_category = 0
             ORDER BY c.id_category ASC
             LIMIT 5'
        );

        $pages = [];
        foreach ($rows as $row) {
            $category = new Category((int) $row['id_category'], (int) $this->context->language->id, (int) $this->context->shop->id);
            if (!Validate::isLoadedObject($category)) {
                continue;
            }

            $pages[] = [
                'key' => 'category:' . (int) $category->id,
                'type' => 'category',
                'label' => 'Category: ' . $category->name,
                'url' => $this->normalizeScanUrl($this->context->link->getCategoryLink($category)),
            ];
        }

        return $pages;
    }

    private function buildProductPages()
    {
        $rows = Db::getInstance()->executeS(
            'SELECT p.id_product
             FROM ' . _DB_PREFIX_ . 'product p
             INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = ' . (int) $this->context->shop->id . ')
             WHERE ps.active = 1
             ORDER BY p.id_product ASC
             LIMIT 5'
        );

        $pages = [];
        foreach ($rows as $row) {
            $product = new Product((int) $row['id_product'], false, (int) $this->context->language->id, (int) $this->context->shop->id);
            if (!Validate::isLoadedObject($product)) {
                continue;
            }

            $pages[] = [
                'key' => 'product:' . (int) $product->id,
                'type' => 'product',
                'label' => 'Product: ' . $product->name,
                'url' => $this->normalizeScanUrl($this->context->link->getProductLink($product)),
            ];
        }

        return $pages;
    }

    private function buildCmsPages()
    {
        $rows = Db::getInstance()->executeS(
            'SELECT c.id_cms
             FROM ' . _DB_PREFIX_ . 'cms c
             INNER JOIN ' . _DB_PREFIX_ . 'cms_shop cs ON (cs.id_cms = c.id_cms AND cs.id_shop = ' . (int) $this->context->shop->id . ')
             WHERE c.active = 1
             ORDER BY c.id_cms ASC
             LIMIT 5'
        );

        $pages = [];
        foreach ($rows as $row) {
            $cms = new CMS((int) $row['id_cms'], (int) $this->context->language->id, (int) $this->context->shop->id);
            if (!Validate::isLoadedObject($cms)) {
                continue;
            }

            $pages[] = [
                'key' => 'cms:' . (int) $cms->id,
                'type' => 'cms',
                'label' => 'CMS: ' . $cms->meta_title,
                'url' => $this->normalizeScanUrl($this->context->link->getCMSLink($cms)),
            ];
        }

        return $pages;
    }

    /**
     * Allows the scanner to target a public domain even when the back office is
     * running on a local development hostname.
     */
    private function normalizeScanUrl($url)
    {
        $overrideBaseUrl = $this->settings->getAssetScanTargetBaseUrl();
        if ($overrideBaseUrl === '') {
            return $url;
        }

        $parts = parse_url((string) $url);
        if ($parts === false) {
            return $url;
        }

        $path = isset($parts['path']) ? $parts['path'] : '/';
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        return $overrideBaseUrl . $path . $query;
    }
}
