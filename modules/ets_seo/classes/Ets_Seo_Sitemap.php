<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class Ets_Seo_Sitemap
{
    public $context;
    public $request_uri;
    private $currentLangId;
    public $currentPageType;

    public function __construct($request_uri, $context = null)
    {
        $this->request_uri = $request_uri;
        if (!$context) {
            $this->context = Ets_Seo::getContextStatic();
        }
    }

    public function sitemap()
    {
        $xml = '';
        if ((int) Configuration::get('ETS_SEO_ENABLE_XML_SITEMAP')) {
            $isoCode = $this->getLangFromUrl(true);
            $page_type = $this->getPageType();
            
            // Check for SmartBlog sitemap URL (same style as ybc_blog)
            if (!$page_type) {
                $page_type = $this->getSmartBlogSitemapPageType();
            }
            
            if ($isoCode && Language::isMultiLanguageActivated()) {
                $xml = $this->sitemapPage($isoCode, $page_type);
            } else {
                $languages = Language::getLanguages(true, $this->context->shop->id);
                if (count($languages) > 1 && Language::isMultiLanguageActivated()) {
                    $xml = $this->sitemapLang($languages);
                } else {
                    $xml = $this->sitemapPage($this->context->language->iso_code, $page_type);
                }
            }
        }

        if (ob_get_length() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/xml; charset=UTF-8');
        mb_internal_encoding('UTF-8');
        exit($xml);
    }

    /**
     * getLinkSitemap.
     *
     * @param array $params
     *
     * @return string
     */
    protected function getLinkSitemap($params = [])
    {
        $linkObj = Ets_Seo::getContextStatic()->link;
        $link = $linkObj->getBaseLink();
        if (isset($params['lang']) && $params['lang'] && Language::isMultiLanguageActivated()) {
            $link .= $params['lang'] . '/';
        }
        if (!isset($params['prependSitemap'])) {
            $meta = Meta::getMetaByPage('module-ets_seo-sitemap', $this->currentLangId);
            $link .= $meta ? $meta['url_rewrite'] : 'sitemap';
        }
        if (isset($params['page_type']) && $params['page_type']) {
            if (isset($params['page']) && (int) $params['page']) {
                return rtrim($link, '/') . '/' . $params['page_type'] . '/' . (int) $params['page'] . '.xml';
            }

            return rtrim($link, '/') . '/' . $params['page_type'] . '.xml';
        }

        return trim($link . '.xml');
    }

    /**
     * @param string $isoCode
     *
     * @return string
     *
     * @throws \PrestaShopException
     */
    private function listIndexByLang($isoCode)
    {
        static $languages;
        static $listItemsDisplayedSitemap;
        if (!$languages) {
            $languages = Language::getLanguages();
        }
        if (!$listItemsDisplayedSitemap) {
            $listItemsDisplayedSitemap = explode(',', (string) Configuration::get('ETS_SEO_SITEMAP_OPTION'));
        }
        $id_lang = Language::getIdByIso($isoCode);
        $xml = '';
        if (in_array('product', $listItemsDisplayedSitemap, true)) {
            $xml .= $this->getSitemapProduct($id_lang);
        }

        if (in_array('category', $listItemsDisplayedSitemap, true) && $this->getCategories($id_lang, true)) {
            $xml .= '<sitemap>';
            $xml .= '<loc><![CDATA[' . $this->getLinkSitemap(['lang' => $isoCode, 'page_type' => 'category']) . ']]></loc>';
            $xml .= '</sitemap>';
        }
        if (in_array('cms', $listItemsDisplayedSitemap, true) && CMS::getCMSPages($id_lang, null, true, $this->context->shop->id)) {
            $xml .= '<sitemap>';
            $xml .= '<loc><![CDATA[' . $this->getLinkSitemap(['lang' => $isoCode, 'page_type' => 'cms']) . ']]></loc>';
            $xml .= '</sitemap>';
        }
        if (in_array('cms_category', $listItemsDisplayedSitemap, true) && CMSCategory::getSimpleCategories($id_lang)) {
            $xml .= '<sitemap>';
            $xml .= '<loc><![CDATA[' . $this->getLinkSitemap(['lang' => $isoCode, 'page_type' => 'cms_category']) . ']]></loc>';
            $xml .= '</sitemap>';
        }
        if (in_array('meta', $listItemsDisplayedSitemap, true) && Meta::getMetasByIdLang($id_lang)) {
            $xml .= '<sitemap>';
            $xml .= '<loc><![CDATA[' . $this->getLinkSitemap(['lang' => $isoCode, 'page_type' => 'meta']) . ']]></loc>';
            $xml .= '</sitemap>';
        }
        if (in_array('manufacturer', $listItemsDisplayedSitemap, true) && Manufacturer::getManufacturers(false, $id_lang, true)) {
            $xml .= '<sitemap>';
            $xml .= '<loc><![CDATA[' . $this->getLinkSitemap(['lang' => $isoCode, 'page_type' => 'manufacturer']) . ']]></loc>';
            $xml .= '</sitemap>';
        }
        if (in_array('supplier', $listItemsDisplayedSitemap, true) && Supplier::getSuppliers(false, $id_lang, true)) {
            $xml .= '<sitemap>';
            $xml .= '<loc><![CDATA[' . $this->getLinkSitemap(['lang' => $isoCode, 'page_type' => 'supplier']) . ']]></loc>';
            $xml .= '</sitemap>';
        }
        if (in_array('blog', $listItemsDisplayedSitemap, true) && Module::isEnabled('ybc_blog')) {
            if (count($languages) < 2) {
                $xml .= '<sitemap>';
                $xml .= '<loc><![CDATA[' . $this->getLinkSitemap(['page_type' => 'blog_sitemap', 'prependSitemap' => false]) . ']]></loc>';
                $xml .= '</sitemap>';
            } else {
                $xml .= '<sitemap>';
                $xml .= '<loc><![CDATA[' . $this->getLinkSitemap(['lang' => $isoCode, 'page_type' => 'blog_sitemap', 'prependSitemap' => false]) . ']]></loc>';
                $xml .= '</sitemap>';
            }
        }
        // SmartBlog support - always include if module is enabled
        if (Module::isEnabled('smartblog')) {
            if (count($languages) < 2) {
                $xml .= '<sitemap>';
                $xml .= '<loc><![CDATA[' . $this->getLinkSitemap(['page_type' => 'smartblog_sitemap', 'prependSitemap' => false]) . ']]></loc>';
                $xml .= '</sitemap>';
            } else {
                $xml .= '<sitemap>';
                $xml .= '<loc><![CDATA[' . $this->getLinkSitemap(['lang' => $isoCode, 'page_type' => 'smartblog_sitemap', 'prependSitemap' => false]) . ']]></loc>';
                $xml .= '</sitemap>';
            }
        }

        return $xml;
    }

    /**
     * sitemapLang.
     *
     * @param array $languages
     *
     * @return string
     */
    protected function sitemapLang($languages)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($languages as $lang) {
            $xml .= $this->listIndexByLang($lang['iso_code']);
        }
        $xml .= '</sitemapindex>';

        return $xml;
    }

    /**
     * sitemapPage.
     *
     * @param string $isoCode
     * @param string $page_type
     *
     * @return string
     */
    protected function sitemapPage($isoCode, $page_type = null)
    {
        if ($page_type) {
            $id_lang = Language::getIdByIso($isoCode);
            switch ($page_type) {
                case 'category':
                    return $this->getSitemapCategory($id_lang);
                case 'cms':
                    return $this->getSitemapCms($id_lang);
                case 'meta':
                    return $this->getSitemapMeta($id_lang);
                case 'product':
                    return $this->getSitemapProduct($id_lang, true);
                case 'cms_category':
                    return $this->getSitemapCmsCategory($id_lang);
                case 'manufacturer':
                    return $this->getSitemapManufacturer($id_lang);
                case 'supplier':
                    return $this->getSitemapSupplier($id_lang);
                case 'smartblog_sitemap':
                case 'smartblog':
                    return $this->getSitemapSmartBlog($id_lang);
            }
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $xml .= $this->listIndexByLang($isoCode);

        $xml .= '</sitemapindex>';

        return $xml;
    }

    protected function getCategories($id_lang, $nb = false)
    {
        $sql = '
                SELECT ' . ($nb ? 'COUNT(*)' : 'c.`id_category`') . ' FROM `' . _DB_PREFIX_ . 'category` c 
                JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.id_category=cl.id_category AND cl.id_lang=' . (int) $id_lang . ' AND cl.id_shop=' . (int) $this->context->shop->id . '
                JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON cl.id_category = cs.id_category AND cs.id_shop=' . (int) $this->context->shop->id . '
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_category` ec ON c.id_category=ec.id_category
                WHERE c.is_root_category=0 AND c.id_parent > 0 AND c.active=1 AND (ec.id_category IS NULL OR ((ec.allow_search=1 OR (ec.allow_search=2 AND ' . (int) Configuration::get('ETS_SEO_CATEGORY_SHOW_IN_SEARCH_RESULT') . '=1)) AND ec.id_lang=' . (int) $id_lang . ' AND ec.id_shop=' . (int) $this->context->shop->id . ')) 
                ' . ($nb ? '' : ' GROUP BY c.`id_category`');

        return $nb ? Db::getInstance()->getValue($sql) : Db::getInstance()->executeS($sql);
    }

    protected function getSitemapCategory($id_lang)
    {
        $categories = $this->getCategories($id_lang);
        $params = [];
        $link = Ets_Seo::getContextStatic()->link;
        foreach ($categories as $item) {
            $cate = new Category($item['id_category'], $id_lang);
            $dataItem = ['last_modify' => date('Y-m-d', strtotime($cate->date_upd ?: time()))];
            $dataItem['link'] = $this->addParamsToUrl($cate->getLink(Ets_Seo::getContextStatic()->link, $id_lang));
            $dataItem['image'] = file_exists(_PS_IMG_DIR_ . 'c/' . $cate->id . '.jpg') ? [
                'link' => $link->getCatImageLink($cate->link_rewrite, $cate->id/* , 'category_default' */),
            ] : [];

            $params[] = $dataItem;
        }

        return $this->renderXmlLinks($params, [
            'priority' => Configuration::get('ETS_SEO_SITEMAP_PRIORITY_CATEGORY'),
            'changefreq' => Configuration::get('ETS_SEO_SITEMAP_FREQ_CATEGORY'),
        ]);
    }

    protected function getSitemapMeta($id_lang)
    {
        $metas = Db::getInstance()->executeS('
                SELECT c.`id_meta`, c.page, cl.* FROM `' . _DB_PREFIX_ . 'meta` c 
                JOIN `' . _DB_PREFIX_ . 'meta_lang` cl ON c.id_meta=cl.id_meta AND cl.id_lang=' . (int) $id_lang . ' AND cl.id_shop=' . (int) $this->context->shop->id . '
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_meta` ec ON c.id_meta=ec.id_meta
                WHERE ec.id_meta IS NULL OR (ec.allow_search=1 AND ec.id_lang=' . (int) $id_lang . ' AND ec.id_shop=' . (int) $this->context->shop->id . ') 
                GROUP BY c.`id_meta`');

        $params = [];
        foreach ($metas as $meta) {
            if (!$meta['url_rewrite'] && 'index' !== $meta['page']) {
                continue;
            }

            $dataItem = [];
            $link = Ets_Seo::getContextStatic()->link;
            $dataItem['link'] = $link->getPageLink($meta['page'], null, $id_lang);
            $params[] = $dataItem;
        }

        return $this->renderXmlLinks($params, [
            'priority' => Configuration::get('ETS_SEO_SITEMAP_PRIORITY_META'),
            'changefreq' => Configuration::get('ETS_SEO_SITEMAP_FREQ_META'),
        ]);
    }

    protected function getSitemapCms($id_lang)
    {
        $cmsPages = Db::getInstance()->executeS('
                SELECT c.`id_cms` FROM `' . _DB_PREFIX_ . 'cms` c 
                JOIN `' . _DB_PREFIX_ . 'cms_lang` cl ON c.id_cms=cl.id_cms AND cl.id_lang=' . (int) $id_lang . ' AND cl.id_shop=' . (int) $this->context->shop->id . '
                JOIN `' . _DB_PREFIX_ . 'cms_shop` cs ON cl.id_cms = cs.id_cms AND cs.id_shop=' . (int) $this->context->shop->id . '
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_cms` ec ON c.id_cms=ec.id_cms
                WHERE c.active=1 AND (ec.id_cms IS NULL OR ((ec.allow_search=1 OR (ec.allow_search=2 AND ' . (int) Configuration::get('ETS_SEO_CMS_SHOW_IN_SEARCH_RESULT') . '=1)) AND ec.id_lang=' . (int) $id_lang . ' AND ec.id_shop=' . (int) $this->context->shop->id . ')) 
                GROUP BY c.`id_cms`');
        $links = [];
        foreach ($cmsPages as $cms) {
            $dataItem = [];
            $obj = new CMS($cms['id_cms'], $id_lang);
            $link = Ets_Seo::getContextStatic()->link;
            $dataItem['link'] = $link->getCMSLink($obj, null, null, $id_lang);
            $links[] = $dataItem;
        }

        return $this->renderXmlLinks($links, [
            'priority' => Configuration::get('ETS_SEO_SITEMAP_PRIORITY_CMS'),
            'changefreq' => Configuration::get('ETS_SEO_SITEMAP_FREQ_CMS'),
        ]);
    }

    protected function getSitemapCmsCategory($id_lang)
    {
        $cmsPages = Db::getInstance()->executeS('
                SELECT c.`id_cms_category` FROM `' . _DB_PREFIX_ . 'cms_category` c 
                JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl ON c.id_cms_category=cl.id_cms_category AND cl.id_lang=' . (int) $id_lang . ' AND cl.id_shop=' . (int) $this->context->shop->id . '
                JOIN `' . _DB_PREFIX_ . 'cms_category_shop` cs ON cl.id_cms_category = cs.id_cms_category AND cs.id_shop=' . (int) $this->context->shop->id . '
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_cms_category` ec ON c.id_cms_category=ec.id_cms_category
                WHERE c.active=1 AND (ec.id_cms_category IS NULL OR ((ec.allow_search=1 OR (ec.allow_search=2 AND ' . (int) Configuration::get('ETS_SEO_CMS_CATE_SHOW_IN_SEARCH_RESULT') . '=1)) AND ec.id_lang=' . (int) $id_lang . ' AND ec.id_shop=' . (int) $this->context->shop->id . ')) 
                GROUP BY c.`id_cms_category`');
        $links = [];
        foreach ($cmsPages as $cms) {
            $link = Ets_Seo::getContextStatic()->link;
            $obj = new CMSCategory($cms['id_cms_category'], $id_lang);
            $dataItem = ['last_modify' => date('Y-m-d', strtotime($obj->date_upd ?: time()))];
            $dataItem['link'] = $link->getCMSCategoryLink($obj, null, $id_lang);
            $links[] = $dataItem;
        }

        return $this->renderXmlLinks($links, [
            'priority' => Configuration::get('ETS_SEO_SITEMAP_PRIORITY_CMS_CATEGORY'),
            'changefreq' => Configuration::get('ETS_SEO_SITEMAP_FREQ_CMS_CATEGORY'),
        ]);
    }

    protected function getSitemapManufacturer($id_lang)
    {
        $manufs = Db::getInstance()->executeS('
                SELECT c.`id_manufacturer` FROM `' . _DB_PREFIX_ . 'manufacturer` c 
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer_shop` ms ON c.id_manufacturer=ms.id_manufacturer AND ms.id_shop=' . (int) $this->context->shop->id . ' 
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_manufacturer` ec ON c.id_manufacturer=ec.id_manufacturer
                WHERE c.active=1 AND (ec.id_manufacturer IS NULL OR ((ec.allow_search=1 OR (ec.allow_search=2 AND ' . (int) Configuration::get('ETS_SEO_MANUFACTURER_SHOW_IN_SEARCH_RESULT') . '=1)) AND ec.id_lang=' . (int) $id_lang . ' AND ec.id_shop=' . (int) $this->context->shop->id . ')) 
                GROUP BY c.`id_manufacturer`');
        $links = [];
        foreach ($manufs as $manuf) {
            $link = Ets_Seo::getContextStatic()->link;
            $obj = new Manufacturer($manuf['id_manufacturer'], $id_lang);
            $dataItem = ['last_modify' => date('Y-m-d', strtotime($obj->date_upd ?: time()))];
            $dataItem['link'] = $link->getManufacturerLink($obj, null, $id_lang);
            if (file_exists(_PS_ROOT_DIR_ . '/img/m/' . $obj->id . '.jpg')) {
                $dataItem['image'] = [
                    'link' => $this->context->shop->getBaseURL(true, true) . 'img/m/' . $obj->id . '.jpg',
                ];
            }
            $links[] = $dataItem;
        }

        return $this->renderXmlLinks($links, [
            'priority' => Configuration::get('ETS_SEO_SITEMAP_PRIORITY_MANUFACTURER'),
            'changefreq' => Configuration::get('ETS_SEO_SITEMAP_FREQ_MANUFACTURER'),
        ]);
    }

    protected function getSitemapSupplier($id_lang)
    {
        $suppliers = Db::getInstance()->executeS('
                SELECT c.`id_supplier` FROM `' . _DB_PREFIX_ . 'supplier` c 
                LEFT JOIN `' . _DB_PREFIX_ . 'supplier_shop` ms ON c.id_supplier=ms.id_supplier AND ms.id_shop=' . (int) $this->context->shop->id . ' 
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_supplier` ec ON c.id_supplier=ec.id_supplier
                WHERE c.active=1 AND (ec.id_supplier IS NULL OR ((ec.allow_search=1 OR (ec.allow_search=2 AND ' . (int) Configuration::get('ETS_SEO_SUPPLIER_SHOW_IN_SEARCH_RESULT') . '=1)) AND ec.id_lang=' . (int) $id_lang . ' AND ec.id_shop=' . (int) $this->context->shop->id . '))
                GROUP BY c.id_supplier');
        $links = [];
        foreach ($suppliers as $supplier) {
            $link = Ets_Seo::getContextStatic()->link;
            $obj = new Supplier($supplier['id_supplier'], $id_lang);
            $dataItem = ['last_modify' => date('Y-m-d', strtotime($obj->date_upd ?: time()))];
            $dataItem['link'] = $link->getSupplierLink($obj, null, $id_lang);

            if (file_exists(_PS_ROOT_DIR_ . '/img/s/' . $obj->id . '.jpg')) {
                $dataItem['image'] = [
                    'link' => $this->context->shop->getBaseURL(true, true) . 'img/s/' . $obj->id . '.jpg',
                ];
            }
            $links[] = $dataItem;
        }

        return $this->renderXmlLinks($links, [
            'priority' => Configuration::get('ETS_SEO_SITEMAP_PRIORITY_SUPPLIER'),
            'changefreq' => Configuration::get('ETS_SEO_SITEMAP_FREQ_SUPPLIER'),
        ]);
    }

    protected function getSitemapSmartBlog($id_lang)
    {
        if (!Module::isEnabled('smartblog')) {
            return '';
        }
        
        $links = [];
        
        // Get blog posts
        $posts = Db::getInstance()->executeS('
            SELECT p.id_smart_blog_post, pl.link_rewrite, p.created 
            FROM `' . _DB_PREFIX_ . 'smart_blog_post` p
            JOIN `' . _DB_PREFIX_ . 'smart_blog_post_lang` pl ON p.id_smart_blog_post = pl.id_smart_blog_post
            WHERE p.active = 1 AND pl.id_lang = ' . (int)$id_lang . '
            ORDER BY p.id_smart_blog_post DESC
        ');
        
        foreach ($posts as $post) {
            $dataItem = [];
            $dataItem['link'] = smartblog::GetSmartBlogLink('smartblog_post_rule', [
                'id_post' => $post['id_smart_blog_post'],
                'rewrite' => $post['link_rewrite']
            ], $id_lang);
            $dataItem['last_modify'] = date('Y-m-d', strtotime($post['created'] ?: time()));
            $links[] = $dataItem;
        }
        
        // Get blog categories
        $categories = Db::getInstance()->executeS('
            SELECT c.id_smart_blog_category, cl.link_rewrite
            FROM `' . _DB_PREFIX_ . 'smart_blog_category` c
            JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` cl ON c.id_smart_blog_category = cl.id_smart_blog_category
            WHERE c.active = 1 AND cl.id_lang = ' . (int)$id_lang . '
            ORDER BY c.id_smart_blog_category
        ');
        
        foreach ($categories as $category) {
            $dataItem = [];
            $dataItem['link'] = smartblog::GetSmartBlogLink('smartblog_category_rule', [
                'id_blog_category' => $category['id_smart_blog_category'],
                'rewrite' => $category['link_rewrite']
            ], $id_lang);
            $links[] = $dataItem;
        }
        
        // Add main blog page
        $dataItem = [];
        $dataItem['link'] = smartblog::GetSmartBlogLink('smartblog', [], $id_lang);
        $links[] = $dataItem;
        
        return $this->renderXmlLinks($links, [
            'priority' => '0.7',
            'changefreq' => 'weekly',
        ]);
    }

    protected function getSitemapProduct($id_lang, $list = false)
    {
        $per_page = 250;
        if ($limit_prod = (int) Configuration::get('ETS_SEO_PROD_SITEMAP_LIMIT')) {
            $per_page = $limit_prod;
        }
        $page = $this->getProductPage();
        if ($list && !$page) {
            $page = 1;
        }
        $links = [];
        if ($page) {
            $page = (int) $page;
            $start = ($page - 1) * $per_page;
            $products = Db::getInstance()->executeS('
                    SELECT ep.*, p.id_product as id_product FROM `' . _DB_PREFIX_ . 'product` p 
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON p.id_product=ps.id_product AND ps.id_shop=' . (int) $this->context->shop->id . ' 
                    LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_product` ep ON p.id_product=ep.id_product AND ep.id_shop=' . (int) $this->context->shop->id . " 
                    WHERE ps.`visibility` IN ('both', 'catalog') AND ps.`active`=1 AND (ep.id_product IS NULL OR ((ep.allow_search=1 OR (ep.allow_search=2 AND " . (int) Configuration::get('ETS_SEO_PROD_SHOW_IN_SEARCH_RESULT') . '=1)) AND ep.id_lang=' . (int) $id_lang . ' AND ep.id_shop=' . (int) $this->context->shop->id . '))
                    GROUP BY p.id_product
                    ORDER BY p.id_product DESC
                    LIMIT ' . (int) $start . ',' . (int) $per_page);
            foreach ($products as $item) {
                $dataItem = [];
                $link = Ets_Seo::getContextStatic()->link;
                $product = new Product((int) $item['id_product'], false, $id_lang);
                $dataItem['link'] = $product->getLink($this->context);

                $images = Product::getCover($product->id);
                if ($images && isset($images['id_image']) && (int) $images['id_image']) {
                    $caption = null;
                    $img = new Image($images['id_image'], $id_lang);
                    $caption = $img->legend;
                    $dataItem['image'] = [
                        'link' => $link->getImageLink($product->link_rewrite, $images['id_image'], ImageType::getFormattedName('home')),
                        'caption' => $caption,
                        'title' => $product->name,
                    ];
                }
                $dataItem['last_modify'] = date('Y-m-d', strtotime($product->date_upd ?: time()));
                $links[] = $dataItem;
            }

            return $this->renderXmlLinks($links, [
                'priority' => Configuration::get('ETS_SEO_SITEMAP_PRIORITY_PRODUCT'),
                'changefreq' => Configuration::get('ETS_SEO_SITEMAP_FREQ_PRODUCT'),
            ]);
        }
        $total_product = EtsSeoProduct::getTotalProduct(true, $id_lang);
        $isoCode = Language::getIsoById($id_lang);
        $total_page = ceil($total_product / $per_page);
        if ($total_page > 1) {
            for ($p = 1; $p <= $total_page; ++$p) {
                $paramPage = ['lang' => $isoCode, 'page_type' => 'product', 'page' => $p];
                $links[] = $this->getLinkSitemap($paramPage);
            }

            return $this->renderXmlPages($links);
        }

        $paramPage = ['lang' => $isoCode, 'page_type' => 'product'];
        $links[] = $this->getLinkSitemap($paramPage);

        return $this->renderXmlPages($links);
    }

    /**
     * renderXmlPage.
     *
     * @param array $links
     * @param array $params
     *
     * @return string
     */
    protected function renderXmlLinks($links, $params = [])
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        if ($links) {
            foreach ($links as $link) {
                $xml .= '<url>';
                $xml .= '<loc><![CDATA[' . $link['link'] . ']]></loc>';
                $xml .= '<changefreq>' . (isset($params['changefreq']) && $params['changefreq'] ? $params['changefreq'] : 'weekly') . '</changefreq>';
                $xml .= '<priority>' . (isset($params['priority']) && '' !== $params['priority'] ? number_format($params['priority'], 1, '.', '') : 1.0) . '</priority>';
                if (isset($link['image']) && !empty($link['image'])) {
                    $xml .= '<image:image>';
                    $xml .= '<image:loc><![CDATA[' . $link['image']['link'] . ']]></image:loc>';
                    if (isset($link['image']['caption']) && $link['image']['caption'] && strip_tags($link['image']['caption'])) {
                        $xml .= '<image:caption><![CDATA[' . strip_tags($link['image']['caption']) . ']]></image:caption>';
                    }
                    if (isset($link['image']['title']) && $link['image']['title'] && strip_tags($link['image']['title'])) {
                        $xml .= '<image:title><![CDATA[' . strip_tags($link['image']['title']) . ']]></image:title>';
                    }
                    $xml .= '</image:image>';
                }
                if (isset($link['last_modify']) && Validate::isDate($link['last_modify'])) {
                    $xml .= '<lastmod>' . $link['last_modify'] . '</lastmod>';
                }
                $xml .= '</url>';
            }
        }
        $xml .= '</urlset>';

        return $xml;
    }

    protected function renderXmlPages($pages)
    {
        $xml = '';
        foreach ($pages as $page) {
            $xml .= '<sitemap>';
            $xml .= '<loc><![CDATA[' . $page . ']]></loc>';
            $xml .= '</sitemap>';
        }

        return $xml;
    }

    /**
     * addParamsToUrl.
     *
     * @param string $link
     * @param array $params
     *
     * @return string
     */
    protected function addParamsToUrl($link, $params = [])
    {
        $count = 0;
        foreach ($params as $k => $p) {
            if (0 == $count && false !== Tools::strpos($link, '?')) {
                $link = $link . '?' . $k . '=' . $p;
            } else {
                $link = $link . '&' . $k . '=' . $p;
            }
            $count ++;
        }

        return $link;
    }

    public function getLangFromUrl($getIsoCode = false)
    {
        // Get request uri (HTTP_X_REWRITE_URL is used by IIS)
        if (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        }
        else
            return 0;
        $requestUri = rawurldecode($requestUri);
        $requestUri = preg_replace(
            '#^' . preg_quote(Ets_Seo::getContextStatic()->shop->getBaseURI(), '#') . '#i',
            '/',
            $requestUri
        );

        // If there are several languages, get language from uri
        if (Language::isMultiLanguageActivated()) {
            if (preg_match('#^/([a-z]{2})(?:/.*)?$#', $requestUri, $m)) {
                $isoCode = $m[1];
                $id_lang = Language::getIdByIso($isoCode);
                $this->currentLangId = (int) $id_lang;
                if ($id_lang) {
                    if ($getIsoCode) {
                        return $isoCode;
                    }

                    return (int) $id_lang;
                }

                return false;
            }
        }

        return 0;
    }

    /**
     * @return string|null
     */
    public function getPageType()
    {
        $types = [
            'product',
            'category',
            'cms',
            'cms_category',
            'meta',
            'manufacturer',
            'supplier',
            'smartblog',
        ];
        $page_type = null;
        foreach ($types as $type) {
            $meta = Meta::getMetaByPage('module-ets_seo-sitemap', $this->currentLangId);
            $prefix = $meta ? $meta['url_rewrite'] : 'sitemap';
            $pattern = sprintf('/%s\/%s(\/(\d+)|)\.xml/', $prefix, $type);
            if (preg_match($pattern, $this->request_uri)) {
                $this->currentPageType = $type;
                $page_type = $type;
            }
        }

        return $page_type;
    }

    /**
     * Detect smartblog_sitemap from URL (same style as ybc_blog)
     * @return string|null
     */
    public function getSmartBlogSitemapPageType()
    {
        if (preg_match('/smartblog_sitemap\.xml$/', $this->request_uri)) {
            return 'smartblog_sitemap';
        }
        return null;
    }

    public function getProductPage()
    {
        $meta = Meta::getMetaByPage('module-ets_seo-sitemap', $this->currentLangId);
        $prefix = $meta ? $meta['url_rewrite'] : 'sitemap';
        $pattern = sprintf('/%s\/%s\/\d+\.xml$/', $prefix, 'product');
        if (preg_match($pattern, $this->request_uri)) {
            $uri = $this->request_uri;
            if (false !== strpos($this->request_uri, '?')) {
                $uri = explode('?', $this->request_uri)[0];
            }
            $uri = explode('/', $uri);
            $uri = end($uri);

            return (int) str_replace('.xml', '', $uri);
        }

        return false;
    }
}
