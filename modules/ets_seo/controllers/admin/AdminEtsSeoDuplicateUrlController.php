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

/**
 * Class AdminEtsSeoDuplicateUrlController
 *
 * @property \Ets_Seo $module
 *
 * @mixin \ModuleAdminControllerCore
 */
class AdminEtsSeoDuplicateUrlController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->module->l('You must enable module SEO Audit to configure its features', 'AdminEtsSeoDuplicateUrlController');
        }
    }

    public function renderList()
    {
        return $this->getDuplicateProduct()
            . $this->getDuplicateCategories()
            . $this->getDuplicateCMS()
            . $this->getDuplicateCMSCategory()
            . $this->getDuplicateMeta();
    }

    public function getDuplicateProduct()
    {
        $shopId = (int) $this->context->shop->id;
        $this->toolbar_title = $this->module->l('Products', 'AdminEtsSeoDuplicateUrlController');
        $this->identifier = 'id_product';
        $this->table = 'product';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON a.id_product = pl.id_product LEFT JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON a.id_product = ps.id_product';
        $this->_select = 'pl.`link_rewrite`,COUNT(pl.id_product) as count_id, MIN(pl.id_product) as minid, pl.`id_shop`, pl.`id_lang`';
        $this->_where = ' AND pl.`id_shop` = ' . $shopId . ' AND ps.`id_shop` = ' . $shopId . " AND pl.`link_rewrite` IS NOT NULL AND pl.`link_rewrite` != ''";
        $this->_group = ' GROUP BY pl.id_lang, pl.link_rewrite';
        $this->_having = 'COUNT(pl.id_product) > 1';
        $this->_orderBy = 'minid';
        $this->_orderWay = 'DESC';
        $this->actions = ['view'];
        $this->list_simple_header = true;
        $this->fields_list = [
            'link_rewrite' => [
                'title' => $this->module->l('Link rewrite', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'language' => [
                'title' => $this->module->l('Language', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'count_id' => [
                'title' => $this->module->l('Total link duplicate', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'links' => [
                'title' => $this->module->l('Name', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-left',
                'float' => true,
                'remove_onclick' => true,
            ],
        ];

        return parent::renderList();
    }

    public function getDuplicateCategories()
    {
        $shopId = (int) $this->context->shop->id;
        $this->toolbar_title = $this->module->l('Product categories', 'AdminEtsSeoDuplicateUrlController');
        $this->identifier = 'id_category';
        $this->table = 'category';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON a.id_category = cl.id_category LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON a.id_category = cs.id_category';
        $this->_select = 'cl.`link_rewrite`,COUNT(cl.id_category) as count_id, MIN(cl.id_category) as minid, cl.`id_shop`, cl.`id_lang`';
        $this->_where = ' AND cl.`id_shop` = ' . $shopId . ' AND cs.`id_shop` = ' . $shopId . " AND cl.`link_rewrite` IS NOT NULL AND cl.`link_rewrite` != ''";
        $this->_group = ' GROUP BY cl.id_lang, cl.link_rewrite';
        $this->_having = 'COUNT(cl.id_category) > 1';
        $this->_orderBy = 'minid';
        $this->_orderWay = 'DESC';
        $this->actions = ['view'];
        $this->list_simple_header = true;
        $this->fields_list = [
            'link_rewrite' => [
                'title' => $this->module->l('Link rewrite', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'language' => [
                'title' => $this->module->l('Language', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'count_id' => [
                'title' => $this->module->l('Total link duplicate', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'links' => [
                'title' => $this->module->l('Name', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-left',
                'float' => true,
                'remove_onclick' => true,
            ],
        ];

        return parent::renderList();
    }

    public function getDuplicateCMS()
    {
        $this->toolbar_title = $this->module->l('CMS (pages)', 'AdminEtsSeoDuplicateUrlController');
        $this->identifier = 'id_cms';
        $this->table = 'cms';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'cms_lang` cl ON a.id_cms = cl.id_cms LEFT JOIN `' . _DB_PREFIX_ . 'cms_shop` cs ON a.id_cms = cs.id_cms';
        $this->_select = 'cl.`link_rewrite`,COUNT(cl.id_cms) as count_id, MIN(cl.id_cms) as minid, cl.`id_shop`, cl.`id_lang`, cl.id_cms';
        $this->_where = ' AND cl.`id_shop` = ' . (int) $this->context->shop->id . ' AND cs.`id_shop` = ' . (int) $this->context->shop->id . " AND cl.`link_rewrite` IS NOT NULL AND cl.`link_rewrite` != ''";
        $this->_group = ' GROUP BY cl.id_lang, cl.link_rewrite';
        $this->_having = 'COUNT(cl.id_cms) > 1';
        $this->_orderBy = 'minid';
        $this->_orderWay = 'DESC';
        $this->actions = ['view'];
        $this->list_simple_header = true;
        $this->fields_list = [
            'link_rewrite' => [
                'title' => $this->module->l('Link rewrite', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'language' => [
                'title' => $this->module->l('Language', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'count_id' => [
                'title' => $this->module->l('Total link duplicate', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'links' => [
                'title' => $this->module->l('Name', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-left',
                'float' => true,
                'remove_onclick' => true,
            ],
        ];

        return parent::renderList();
    }

    public function getDuplicateCMSCategory()
    {
        $shopId = (int) $this->context->shop->id;
        $this->toolbar_title = $this->module->l('CMS categories', 'AdminEtsSeoDuplicateUrlController');
        $this->identifier = 'id_cms_category';
        $this->table = 'cms_category';
        $this->_join = 'INNER JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl ON a.id_cms_category = cl.id_cms_category LEFT JOIN `' . _DB_PREFIX_ . 'cms_category_shop` cs ON a.id_cms_category = cs.id_cms_category';
        $this->_select = 'cl.`link_rewrite`,COUNT(cl.id_cms_category) as count_id, MIN(cl.id_cms_category) as minid, cl.`id_shop`, cl.`id_lang`, cl.id_cms_category';
        $this->_where = ' AND cl.`id_shop` = ' . $shopId . ' AND cs.`id_shop` = ' . $shopId . " AND cl.`link_rewrite` IS NOT NULL AND cl.`link_rewrite` != ''";
        $this->_group = ' GROUP BY cl.id_lang, cl.link_rewrite';
        $this->_having = 'COUNT(cl.id_cms_category) > 1';
        $this->_orderBy = 'minid';
        $this->_orderWay = 'DESC';
        $this->actions = ['view'];
        $this->list_simple_header = true;
        $this->fields_list = [
            'link_rewrite' => [
                'title' => $this->module->l('Link rewrite', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'language' => [
                'title' => $this->module->l('Language', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'count_id' => [
                'title' => $this->module->l('Total link duplicate', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'links' => [
                'title' => $this->module->l('Name', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-left',
                'float' => true,
                'remove_onclick' => true,
            ],
        ];

        return parent::renderList();
    }

    public function getDuplicateMeta()
    {
        $this->toolbar_title = $this->module->l('Other pages', 'AdminEtsSeoDuplicateUrlController');
        $this->identifier = 'id_meta';
        $this->table = 'meta';
        $this->_join = 'INNER JOIN `' . _DB_PREFIX_ . 'meta_lang` ml ON a.id_meta = ml.id_meta';
        $this->_select = 'ml.`url_rewrite`,COUNT(ml.id_meta) as count_id, MIN(ml.id_meta) as minid, ml.`id_shop`, ml.`id_lang`, ml.id_meta';
        $this->_where = " AND ml.url_rewrite IS NOT NULL AND ml.url_rewrite != '' AND ml.`id_shop` = " . (int) $this->context->shop->id;
        $this->_group = ' GROUP BY ml.id_lang, ml.url_rewrite';
        $this->_having = 'COUNT(ml.id_meta) > 1';
        $this->_orderBy = 'minid';
        $this->_orderWay = 'DESC';
        $this->actions = ['view'];
        $this->list_simple_header = true;
        $this->fields_list = [
            'url_rewrite' => [
                'title' => $this->module->l('Link rewrite', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'language' => [
                'title' => $this->module->l('Language', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'count_id' => [
                'title' => $this->module->l('Total link duplicate', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-center',
                'remove_onclick' => true,
            ],
            'links' => [
                'title' => $this->module->l('Name', 'AdminEtsSeoDuplicateUrlController'),
                'align' => 'text-left',
                'float' => true,
                'remove_onclick' => true,
            ],
        ];

        return parent::renderList();
    }

    /**
     * @param string $type
     * @param array $params
     *
     * @return string
     */
    private function getLinkDuplicate($type, $params)
    {
        $duplicate_link = '';
        $duplicate_title = '';
        if ('product' == $type) {
            $sfP = ['route' => 'admin_product_form', 'id' => $params['id']];
            $p = ['id_product' => $params['id'], 'edit' => true];
            $duplicate_link = $this->context->link->getAdminLink('AdminProducts', true, $sfP, $p);
            $duplicate_title = $params['title'];
        } elseif ('category' == $type) {
            $sfP = ['route' => 'admin_categories_edit', 'categoryId' => $params['id']];
            $p = ['id_category' => $params['id'], 'edit' => true];
            try {
                $duplicate_link = $this->context->link->getAdminLink('AdminCategories', true, $sfP, $p);
            } catch (Exception $ex) {
                $duplicate_link = $this->context->link->getAdminLink('AdminCategories', true, [], $p);
            }
            $duplicate_title = $params['title'];
        } elseif ('cms' == $type) {
            $sfP = ['route' => 'admin_cms_pages_edit', 'cmsPageId' => $params['id']];
            $p = ['id_cms' => $params['id'], 'edit' => true];
            try {
                $duplicate_link = $this->context->link->getAdminLink('AdminCmsContent', true, $sfP, $p);
            } catch (Exception $ex) {
                $duplicate_link = $this->context->link->getAdminLink('AdminCmsContent', true, [], $p);
            }
            $duplicate_title = $params['title'];
        } elseif ('cms_category' == $type) {
            $sfP = ['route' => 'admin_cms_pages_category_edit', 'cmsCategoryId' => $params['id']];
            $p = ['id_cms_category' => $params['id'], 'edit' => true];
            try {
                $duplicate_link = $this->context->link->getAdminLink('AdminCmsContent', true, $sfP, $p);
            } catch (Exception $ex) {
                $duplicate_link = $this->context->link->getAdminLink('AdminCmsContent', true, [], $p);
            }
            $duplicate_title = $params['title'];
        } elseif ('meta' == $type) {
            $sfP = ['route' => 'admin_metas_edit', 'metaId' => $params['id']];
            $p = ['id_meta' => $params['id'], 'edit' => true];
            try {
                $duplicate_link = $this->context->link->getAdminLink('AdminMeta', true, $sfP, $p);
            } catch (Exception $ex) {
                $duplicate_link = $this->context->link->getAdminLink('AdminMeta', true, [], $p);
            }
            $duplicate_title = $params['title'];
        }
        if (!$duplicate_link || !$duplicate_title) {
            return '';
        }

        return EtsSeoStrHelper::displayText($duplicate_title, 'a', ['href' => $duplicate_link, 'target' => '_blank', 'class' => 'ets-seo-link-duplicate']);
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
        if ('product' == $this->table) {
            foreach ($this->_list as $key => &$item) {
                $item['language'] = Language::getIsoById((int) $item['id_lang']);
                if (!$item['language']) {
                    unset($this->_list[$key]);
                    continue;
                }
                $duplicate = EtsSeoProduct::getProductsByLinkRewrite($item['link_rewrite'], $item['id_shop'], $item['id_lang']);
                $links = '';
                foreach ($duplicate as $data) {
                    $links .= $this->getLinkDuplicate('product', ['id' => $data['id_product'], 'title' => $data['name']]);
                }
                $item['links'] = $links;
            }
            unset($item);
        } elseif ('category' == $this->table) {
            foreach ($this->_list as $key => &$item) {
                $item['language'] = Language::getIsoById((int) $item['id_lang']);
                if (!$item['language']) {
                    unset($this->_list[$key]);
                    continue;
                }
                $duplicate = EtsSeoCategory::getCategoriesByLinkRewrite($item['link_rewrite'], $item['id_shop'], $item['id_lang']);
                $links = '';
                foreach ($duplicate as $data) {
                    $links .= $this->getLinkDuplicate('category', ['id' => $data['id_category'], 'title' => $data['name']]);
                }
                $item['links'] = $links;
            }
            unset($item);
        } elseif ('cms' == $this->table) {
            foreach ($this->_list as $key => &$item) {
                $item['language'] = Language::getIsoById((int) $item['id_lang']);
                if (!$item['language']) {
                    unset($this->_list[$key]);
                    continue;
                }
                $duplicate = EtsSeoCms::getCMSByLinkRewrite($item['link_rewrite'], $item['id_shop'], $item['id_lang']);
                $links = '';
                foreach ($duplicate as $data) {
                    $links .= $this->getLinkDuplicate('cms', ['id' => $data['id_cms'], 'title' => $data['meta_title']]);
                }
                $item['links'] = $links;
            }
            unset($item);
        } elseif ('cms_category' == $this->table) {
            foreach ($this->_list as $key => &$item) {
                $item['language'] = Language::getIsoById((int) $item['id_lang']);
                if (!$item['language']) {
                    unset($this->_list[$key]);
                    continue;
                }
                $duplicate = EtsSeoCmsCategory::getCmsCategoriesByLinkRewrite($item['link_rewrite'], $item['id_shop'], $item['id_lang']);
                $links = '';
                foreach ($duplicate as $data) {
                    $links .= $this->getLinkDuplicate('cms_category', ['id' => $data['id_cms_category'], 'title' => $data['name']]);
                }
                $item['links'] = $links;
            }
            unset($item);
        } elseif ('meta' == $this->table) {
            foreach ($this->_list as $key => &$item) {
                $item['language'] = Language::getIsoById((int) $item['id_lang']);
                if (!$item['language']) {
                    unset($this->_list[$key]);
                    continue;
                }
                $duplicate = EtsSeoMeta::getMetaByLinkRewrite($item['url_rewrite'], $item['id_shop'], $item['id_lang']);
                $links = '';
                foreach ($duplicate as $data) {
                    $links .= $this->getLinkDuplicate('meta', ['id' => $data['id_meta'], 'title' => $data['title']]);
                }
                $item['links'] = $links;
            }
            unset($item);
        }
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $this->module->_clearCache('*');
        }
    }
}
