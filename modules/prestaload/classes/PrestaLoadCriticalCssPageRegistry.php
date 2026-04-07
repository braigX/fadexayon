<?php
/**
 * Provides one representative public URL per page type for critical CSS
 * generation. This keeps the beta UI simple and avoids generating per-URL CSS.
 */

class PrestaLoadCriticalCssPageRegistry
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var PrestaLoadCacheSettings
     */
    private $settings;

    public function __construct(Context $context, PrestaLoadCacheSettings $settings)
    {
        $this->context = $context;
        $this->settings = $settings;
    }

    public function getPages()
    {
        $pages = [];

        $pages['home'] = [
            'key' => 'home',
            'type' => 'home',
            'label' => 'Homepage',
            'url' => $this->normalizeScanUrl($this->context->link->getPageLink('index', true)),
        ];

        $category = $this->getFirstCategoryPage();
        if (!empty($category)) {
            $pages['category'] = $category;
        }

        $product = $this->getFirstProductPage();
        if (!empty($product)) {
            $pages['product'] = $product;
        }

        $cms = $this->getFirstCmsPage();
        if (!empty($cms)) {
            $pages['cms'] = $cms;
        }

        return array_values($pages);
    }

    public function getPageByKey($key)
    {
        foreach ($this->getPages() as $page) {
            if ($page['key'] === $key) {
                return $page;
            }
        }

        return [];
    }

    private function getFirstCategoryPage()
    {
        $rows = Db::getInstance()->executeS(
            'SELECT c.id_category
             FROM ' . _DB_PREFIX_ . 'category c
             INNER JOIN ' . _DB_PREFIX_ . 'category_shop cs ON (cs.id_category = c.id_category AND cs.id_shop = ' . (int) $this->context->shop->id . ')
             WHERE c.active = 1 AND c.is_root_category = 0
             ORDER BY c.id_category ASC
             LIMIT 1'
        );

        if (empty($rows[0]['id_category'])) {
            return [];
        }

        $category = new Category((int) $rows[0]['id_category'], (int) $this->context->language->id, (int) $this->context->shop->id);
        if (!Validate::isLoadedObject($category)) {
            return [];
        }

        return [
            'key' => 'category',
            'type' => 'category',
            'label' => 'Category pages',
            'url' => $this->normalizeScanUrl($this->context->link->getCategoryLink($category)),
            'sample_label' => $category->name,
        ];
    }

    private function getFirstProductPage()
    {
        $rows = Db::getInstance()->executeS(
            'SELECT p.id_product
             FROM ' . _DB_PREFIX_ . 'product p
             INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = ' . (int) $this->context->shop->id . ')
             WHERE ps.active = 1
             ORDER BY p.id_product ASC
             LIMIT 1'
        );

        if (empty($rows[0]['id_product'])) {
            return [];
        }

        $product = new Product((int) $rows[0]['id_product'], false, (int) $this->context->language->id, (int) $this->context->shop->id);
        if (!Validate::isLoadedObject($product)) {
            return [];
        }

        return [
            'key' => 'product',
            'type' => 'product',
            'label' => 'Product pages',
            'url' => $this->normalizeScanUrl($this->context->link->getProductLink($product)),
            'sample_label' => $product->name,
        ];
    }

    private function getFirstCmsPage()
    {
        $rows = Db::getInstance()->executeS(
            'SELECT c.id_cms
             FROM ' . _DB_PREFIX_ . 'cms c
             INNER JOIN ' . _DB_PREFIX_ . 'cms_shop cs ON (cs.id_cms = c.id_cms AND cs.id_shop = ' . (int) $this->context->shop->id . ')
             WHERE c.active = 1
             ORDER BY c.id_cms ASC
             LIMIT 1'
        );

        if (empty($rows[0]['id_cms'])) {
            return [];
        }

        $cms = new CMS((int) $rows[0]['id_cms'], (int) $this->context->language->id, (int) $this->context->shop->id);
        if (!Validate::isLoadedObject($cms)) {
            return [];
        }

        return [
            'key' => 'cms',
            'type' => 'cms',
            'label' => 'CMS pages',
            'url' => $this->normalizeScanUrl($this->context->link->getCMSLink($cms)),
            'sample_label' => $cms->meta_title,
        ];
    }

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
