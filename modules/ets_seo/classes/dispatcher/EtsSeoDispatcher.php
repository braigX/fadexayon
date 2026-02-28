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
if (!class_exists('EtsSeoRedirect')) {
    require_once __DIR__ . '/../EtsSeoRedirect.php';
}
if (!class_exists('EtsUrlHelper')) {
    require_once __DIR__ . '/../utils/EtsUrlHelper.php';
}
class EtsSeoDispatcher
{
    public $default_routes;
    public $old_routes;
    public $config_schema;
    public $request_uri;
    public $listDefaultControllers;
    public static $instance;
    public $routes;
    public const REWRITE_PATTERN = '[_a-zA-Z0-9\x{0600}-\x{06FF}\pL\pS-]+';
    public const OLD_REWRITE_PATTERN = '[_a-zA-Z0-9\x{0600}-\x{06FF}\pL\pS-]*';

    public function __construct()
    {
        $this->default_routes = [
            'category_rule' => [
                'controller' => 'category',
                'rule' => '{parent_rewrite:/}{rewrite}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_product'],
                    'rewrite' => ['regexp' => self::REWRITE_PATTERN, 'param' => 'rewrite'],
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'parent_rewrite' => ['regexp' => '[_a-zA-Z0-9-\/\pL]*', 'param' => 'parent_rewrite'],
                ],
            ],
            'supplier_rule' => [
                'controller' => 'supplier',
                'rule' => 'supplier/{rewrite}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_supplier'],
                    'rewrite' => ['regexp' => self::REWRITE_PATTERN, 'param' => 'rewrite'],
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                ],
            ],
            'manufacturer_rule' => [
                'controller' => 'manufacturer',
                'rule' => 'manufacturer/{rewrite}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_manufacturer'],
                    'rewrite' => ['regexp' => self::REWRITE_PATTERN, 'param' => 'rewrite'],
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                ],
            ],
            'cms_rule' => [
                'controller' => 'cms',
                'rule' => 'content/{rewrite}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_cms'],
                    'rewrite' => ['regexp' => self::REWRITE_PATTERN, 'param' => 'rewrite'],
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                ],
            ],
            'cms_category_rule' => [
                'controller' => 'cms',
                'rule' => 'content/category/{rewrite}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                    'rewrite' => ['regexp' => self::REWRITE_PATTERN, 'param' => 'rewrite'],
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                ],
            ],
            'module' => [
                'controller' => null,
                'rule' => 'module/{module}{/:controller}',
                'keywords' => [
                    'module' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'module'],
                    'controller' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                ],
            ],
            'product_rule' => [
                'controller' => 'product',
                'rule' => '{category}/{rewrite}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_product'],
                    'id_product_attribute' => ['regexp' => '[0-9]+', 'param' => 'id_product_attribute'],
                    'rewrite' => ['regexp' => self::REWRITE_PATTERN, 'param' => 'rewrite'],
                    'ean13' => ['regexp' => '[0-9\pL]*'],
                    'category' => ['regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'category'],
                    'categories' => ['regexp' => '[/_a-zA-Z0-9-\pL]*'],
                    'reference' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'manufacturer' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'supplier' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'price' => ['regexp' => '[0-9\.,]*'],
                    'tags' => ['regexp' => '[a-zA-Z0-9-\pL]*'],
                ],
            ],

            'layered_rule' => [
                'controller' => 'category',
                'rule' => '{parent_rewrite:/}{rewrite}/filter/{selected_filters}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                    'selected_filters' => ['regexp' => '.*', 'param' => 'selected_filters'],
                    'rewrite' => ['regexp' => self::REWRITE_PATTERN, 'param' => 'rewrite'],
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'parent_rewrite' => ['regexp' => '[_a-zA-Z0-9-\/\pL]*', 'param' => 'parent_rewrite'],
                ],
            ],
        ];

        $this->old_routes = [
            'category_rule' => [
                'controller' => 'category',
                'rule' => '{id}-{rewrite}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                    'rewrite' => ['regexp' => self::OLD_REWRITE_PATTERN],
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                ],
            ],
            'supplier_rule' => [
                'controller' => 'supplier',
                'rule' => '{id}__{rewrite}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_supplier'],
                    'rewrite' => ['regexp' => self::OLD_REWRITE_PATTERN],
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                ],
            ],
            'manufacturer_rule' => [
                'controller' => 'manufacturer',
                'rule' => '{id}_{rewrite}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_manufacturer'],
                    'rewrite' => ['regexp' => self::OLD_REWRITE_PATTERN],
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                ],
            ],
            'cms_rule' => [
                'controller' => 'cms',
                'rule' => 'content/{id}-{rewrite}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_cms'],
                    'rewrite' => ['regexp' => self::OLD_REWRITE_PATTERN],
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                ],
            ],
            'cms_category_rule' => [
                'controller' => 'cms',
                'rule' => 'content/category/{id}-{rewrite}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_cms_category'],
                    'rewrite' => ['regexp' => self::OLD_REWRITE_PATTERN],
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                ],
            ],
            'module' => [
                'controller' => null,
                'rule' => 'module/{module}{/:controller}',
                'keywords' => [
                    'module' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'module'],
                    'controller' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                ],
            ],
            'product_rule' => [
                'controller' => 'product',
                'rule' => version_compare('1.7.0.0', _PS_VERSION_, '<=') ? '{category:/}{id}{-:id_product_attribute}-{rewrite}{-:ean13}.html' : '{category:/}{id}-{rewrite}{-:ean13}.html',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_product'],
                    'id_product_attribute' => ['regexp' => '[0-9]+', 'param' => 'id_product_attribute'],
                    'rewrite' => ['regexp' => self::OLD_REWRITE_PATTERN, 'param' => 'rewrite'],
                    'ean13' => ['regexp' => '[0-9\pL]*'],
                    'category' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'categories' => ['regexp' => '[/_a-zA-Z0-9-\pL]*'],
                    'reference' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'manufacturer' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'supplier' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'price' => ['regexp' => '[0-9\.,]*'],
                    'tags' => ['regexp' => '[a-zA-Z0-9-\pL]*'],
                ],
            ],
            /* Must be after the product and category rules in order to avoid conflict */
            'layered_rule' => [
                'controller' => 'category',
                'rule' => '{id}-{rewrite}{/:selected_filters}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                    /* Selected filters is used by the module blocklayered */
                    'selected_filters' => ['regexp' => '.*', 'param' => 'selected_filters'],
                    'rewrite' => ['regexp' => self::OLD_REWRITE_PATTERN],
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                ],
            ],
        ];

        $this->config_schema = [
            'category_rule' => [
                'root_name' => 'ETS_SEO_ROOT_URL_CATEGORY_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_CATEGORY_RULE',
                'name' => 'ETS_SEO_URL_CATEGORY_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_CATEGORY_RULE',
            ],
            'supplier_rule' => [
                'root_name' => 'ETS_SEO_ROOT_URL_SUPPLIER_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_SUPPLIER_RULE',
                'name' => 'ETS_SEO_URL_SUPPLIER_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_SUPPLIER_RULE',
            ],
            'manufacturer_rule' => [
                'root_name' => 'ETS_SEO_ROOT_URL_MANUF_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_MANUF_RULE',
                'name' => 'ETS_SEO_URL_MANUF_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_MANUF_RULE',
            ],
            'cms_rule' => [
                'root_name' => 'ETS_SEO_ROOT_URL_CMS_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_CMS_RULE',
                'name' => 'ETS_SEO_URL_CMS_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_CMS_RULE',
            ],
            'cms_category_rule' => [
                'root_name' => 'ETS_SEO_ROOT_URL_CMS_CATEGORY_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_CMS_CATEGORY_RULE',
                'name' => 'ETS_SEO_URL_CMS_CATEGORY_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_CMS_CATEGORY_RULE',
            ],
            'module' => [
                'root_name' => 'ETS_SEO_ROOT_URL_MODULE_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_MODULE_RULE',
                'name' => 'ETS_SEO_URL_MODULE_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_MODULE_RULE',
            ],
            'product_rule' => [
                'root_name' => 'ETS_SEO_ROOT_URL_PRODUCT_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_PRODUCT_RULE',
                'name' => 'ETS_SEO_URL_PRODUCT_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_PRODUCT_RULE',
            ],
            'layered_rule' => [
                'root_name' => 'ETS_SEO_ROOT_URL_LAYERED_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_LAYERED_RULE',
                'name' => 'ETS_SEO_URL_LAYERED_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_LAYERED_RULE',
            ],
        ];

        $this->listDefaultControllers = [
            'product',
            'category',
            'cms',
            'cms_category',
            'manufacturer',
            'supplier',
        ];
    }

    public static function getDispatcher()
    {
        if (!isset(self::$instance)) {
            self::$instance = new EtsSeoDispatcher();
        }

        return self::$instance;
    }

    public function getLinkRewrite()
    {
        if (($rewrite = Tools::getValue('rewrite')) && (is_array($rewrite) || (!is_array($rewrite) && Validate::isCleanHtml($rewrite)))) {
            return $rewrite;
        }
        $uri = $_SERVER['REQUEST_URI'];
        $uri = str_replace('/' . Tools::getValue('isolang') . '/', '/', $uri);

        return strtok($uri, '?');
    }

    public function getProductIdBySlug($link_rewrite, $context = null)
    {
        if (!$context) {
            $context = Ets_Seo::getContextStatic();
        }
        $id_lang = (int) $context->language->id;
        $id_shop = (int) $context->shop->id;
        $idProduct = (int) Db::getInstance()->getValue('SELECT pl.`id_product` 
                                            FROM `' . _DB_PREFIX_ . 'product_lang` pl
                                            JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = pl.`id_product`)
                                            JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON (ps.`id_product` = pl.`id_product` AND ps.`id_shop` = ' . $id_shop . ")
                                            WHERE pl.`link_rewrite`='" . pSQL((string) $link_rewrite) . "' AND pl.id_shop=" . (int) $id_shop . ' AND pl.id_lang=' . (int) $id_lang);
        if (!$idProduct) {
            $idProduct = (int) Db::getInstance()->getValue('SELECT pl.`id_product` 
                                            FROM `' . _DB_PREFIX_ . 'product_lang` pl
                                            JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = pl.`id_product`)
                                            JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON (ps.`id_product` = pl.`id_product` AND ps.`id_shop` = ' . $id_shop . ")
                                            WHERE pl.`link_rewrite`='" . pSQL((string) $link_rewrite) . "' AND pl.id_shop=" . (int) $id_shop);
            if (isset(${'_GET'}['category'])) {
                unset($_GET['category']);
            }
        }

        return $idProduct;
    }

    public function getCategoryIdBySlug($link_rewrite, $context = null)
    {
        if (!$context) {
            $context = Ets_Seo::getContextStatic();
        }
        $id_lang = (int) $context->language->id;
        $id_shop = (int) $context->shop->id;
        $catId = 0;

        if (Dispatcher::getInstance()->hasKeyword('category_rule', $id_lang, 'parent_rewrite', $id_shop)) {
            $parentRewrite = Tools::getValue('parent_rewrite');

            if ($parentRewrite && '' !== $parentRewrite) {
                $parentRewriteArr = array_reverse(explode('/', $parentRewrite));
                $query = new \DbQuery();
                $whereParent = '';

                foreach ($parentRewriteArr as $key => $val) {
                    $whereParent .= ' AND cpl' . bqSQL($key) . '.link_rewrite="' . pSQL($val) . '"';
                }
                $query->select('c.id_category')
                    ->from('category', 'c')
                    ->leftJoin('category_lang', 'cl', 'c.id_category = cl.id_category')
                    ->where('cl.link_rewrite = "' . pSQL($link_rewrite) . '"' . $whereParent . ' AND c.active = 1')
                    ->groupBy('c.id_category');

                foreach ($parentRewriteArr as $key => $val) {
                    $query->innerJoin('category', 'cp' . $key, 'cp' . $key . '.id_category = ' . ($key ? 'cp' . ($key - 1) : 'c') . '.id_parent')
                        ->leftJoin('category_lang', 'cpl' . $key, 'cp' . $key . '.id_category = cpl' . $key . '.id_category');
                }
                $catId = (int) Db::getInstance()->getValue($query);
            }
        }

        if (!$catId) {
            $catId = (int) Db::getInstance()->getValue('SELECT cl.`id_category` 
                FROM `' . _DB_PREFIX_ . 'category_lang` cl 
                JOIN `' . _DB_PREFIX_ . "category_shop` cs ON cl.id_category = cs.id_category
                WHERE cl.`link_rewrite`='" . pSQL((string) $link_rewrite) . "' AND cl.id_shop=" . (int) $id_shop . ' AND cs.id_shop=' . (int) $id_shop . ' AND cl.id_lang=' . (int) $id_lang);
        }

        return $catId;
    }

    public function getCmsIdBySlug($link_rewrite, $context = null)
    {
        if (!$context) {
            $context = Ets_Seo::getContextStatic();
        }
        $id_lang = (int) $context->language->id;
        $id_shop = (int) $context->shop->id;

        return (int) Db::getInstance()->getValue('SELECT cl.`id_cms` 
                                            FROM `' . _DB_PREFIX_ . 'cms_lang` cl 
                                            JOIN `' . _DB_PREFIX_ . "cms_shop` cs ON cl.id_cms = cs.id_cms 
                                            WHERE cl.`link_rewrite`='" . pSQL((string) $link_rewrite) . "' AND cl.id_shop=" . (int) $id_shop . ' AND cs.id_shop=' . (int) $id_shop . ' AND cl.id_lang=' . (int) $id_lang);
        // TODO : 1.6 doesnot have cms_shop ?
    }

    public function getManufIdBySlug($link_rewrite)
    {
        try {
            return (int) Db::getInstance()->getValue('SELECT esm.`id_manufacturer` FROM `' . _DB_PREFIX_ . 'ets_seo_manufacturer_url` esm
            JOIN `' . _DB_PREFIX_ . "manufacturer` m ON m.id_manufacturer = esm.id_manufacturer AND m.active = 1
            WHERE esm.`link_rewrite`='" . pSQL(Tools::str2url($link_rewrite)) . "'");
        } catch (\Exception $ex) {
            $name = str_replace('-', ' ', $link_rewrite);

            return (int) Db::getInstance()->getValue('SELECT `id_manufacturer` 
                                                FROM `' . _DB_PREFIX_ . "manufacturer` 
                                                WHERE REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`name`, '!', ''), '.', ''), '\'', ' '), '" . '"' . "', ''), '&',''), '  ', ' ') LIKE '%" . pSQL($name) . "%'");
        }
    }

    public function getSupplierIdBySlug($link_rewrite)
    {
        try {
            return (int) Db::getInstance()->getValue('SELECT `id_supplier` FROM `' . _DB_PREFIX_ . "ets_seo_supplier_url` WHERE `link_rewrite`='" . pSQL(Tools::str2url($link_rewrite)) . "'");
        } catch (\Exception $ex) {
            $name = str_replace('-', ' ', $link_rewrite);

            return (int) Db::getInstance()->getValue('SELECT `id_supplier` 
                                            FROM `' . _DB_PREFIX_ . "supplier` 
                                            WHERE REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`name`, '!', ''), '.', ''), '\'', ' '), '" . '"' . "', ''), '&', ''), '  ', ' ') LIKE '%" . pSQL($name) . "%'");
        }
    }

    public function getCmsCategoryIdBySlug($link_rewrite, $context = null)
    {
        if (!$context) {
            $context = Ets_Seo::getContextStatic();
        }
        $isPs17 = version_compare(_PS_VERSION_, '1.7.0.0', '>=');
        $id_shop = (int) $context->shop->id;
        $id_lang = $context->language->id;
        $sql = 'SELECT cl.`id_cms_category` FROM `' . _DB_PREFIX_ . 'cms_category_lang` cl';
        if ($isPs17) {
            $sql .= sprintf(' JOIN `%scms_category_shop` cs ON cl.id_cms_category = cs.id_cms_category', _DB_PREFIX_);
        }
        $sql .= sprintf(' WHERE cl.`link_rewrite`="%s"', pSQL($link_rewrite));
        if ($isPs17) {
            $sql .= sprintf(' AND cl.id_shop = %d AND cs.id_shop= %d', (int) $id_shop, (int) $id_shop);
        }
        $sql .= sprintf(' AND cl.id_lang = %d', (int) $id_lang);

        return (int) Db::getInstance()->getValue($sql);
    }

    public function setOldDefaultRoute($type = 'nearest')
    {
        $is178 = version_compare(_PS_VERSION_, '1.7.8.0', '>=');
        $urlSchemaConfigs = $this->config_schema;
        $routes = $this->old_routes;
        $errors = false;
        foreach ($routes as $k => &$route) {
            if ($is178 && 'layered_rule' == $k) {
                continue;
            }
            if ('root' == $type) {
                $config = Configuration::get($urlSchemaConfigs[$k]['root_name']);
                if (isset($urlSchemaConfigs[$k]['root_name']) && $config && ('module' == $k || ('module' != $k && preg_match('/\{id\}/', $config)))) {
                    $route['rule'] = Configuration::get($urlSchemaConfigs[$k]['root_name']);
                } else {
                    $errors = true;
                    break;
                }
            } elseif ('old' == $type) {
                $config = Configuration::get($urlSchemaConfigs[$k]['old_name']);
                if (isset($urlSchemaConfigs[$k]['old_name']) && ('module' == $k || ('module' != $k && preg_match('/\{id\}/', $config)))) {
                    $route['rule'] = Configuration::get($urlSchemaConfigs[$k]['old_name']);
                } else {
                    $errors = true;
                    break;
                }
            } else {
                $config = Configuration::get($urlSchemaConfigs[$k]['name']);
                if (isset($urlSchemaConfigs[$k]['name']) && ('module' == $k || ('module' != $k && preg_match('/\{id\}/', $config)))) {
                    $route['rule'] = Configuration::get($urlSchemaConfigs[$k]['name']);
                } else {
                    $errors = true;
                    break;
                }
            }
        }

        if ($errors) {
            return false;
        }

        $this->old_routes = $routes;

        return true;
    }

    public function setDefaultRoute()
    {
        // Copy from "ets_seo/defines.php" if has change please update this
        $urlSchemaConfigs = $this->config_schema;
        foreach ($this->default_routes as $k => &$route) {
            if ($config = Configuration::get($urlSchemaConfigs[$k]['no_id'])) {
                $route[$k] = $config;
            }
        }
    }

    public function getDefaultRouteNoId()
    {
        $routes = $this->default_routes;
        foreach ($this->default_routes as $key => $route) {
            if ($route) {
            }
            if ($config = Configuration::get('PS_ROUTE_' . $key)) {
                $routes[$key]['rule'] = $config;
            }
        }

        return $routes;
    }

    public function getMetaIdBySlug($link_rewrite, $context = null)
    {
        if (!$context) {
            $context = Ets_Seo::getContextStatic();
        }
        $id_lang = $context->language->id;
        $id_shop = $context->shop->id;

        return (int) Db::getInstance()->getValue('SELECT `id_meta` 
                                            FROM `' . _DB_PREFIX_ . "meta_lang` 
                                            WHERE `url_rewrite`='" . pSQL((string) $link_rewrite) . "' AND id_shop=" . (int) $id_shop . ' AND id_lang=' . (int) $id_lang);
    }
    public function getController($dispatcherCore, $controller)
    {
        $linkRewrite = $this->getLinkRewrite();
        $success = false;
        $token = ($token = Tools::getValue('token')) && Validate::isCleanHtml($token) ? $token : '';
        $fc = Tools::getValue('fc');
        $module = ($module = Tools::getValue('module')) && Validate::isModuleName($module) ? $module : '';
        $idProduct = (int) Tools::getValue('id_product');
        $idCategory = (int) Tools::getValue('id_category');
        $idCms = (int) Tools::getValue('id_cms');
        $idCmsCategory = (int) Tools::getValue('id_cms_category');
        $idManufacturer = (int) Tools::getValue('id_manufacturer');
        $idSupplier = (int) Tools::getValue('id_supplier');
        Module::getInstanceByName('ets_seo');
        $curIdShop = Ets_Seo::getContextStatic()->shop->id;
        $curIdLang = Ets_Seo::getContextStatic()->language->id;
        $routes = $dispatcherCore->getRoutes();
        $meta = new Meta($this->getMetaIdBySlug($linkRewrite));
        list($this->request_uri) = explode('?',$this->request_uri);
        if ('module' !== $fc && !$module) {
            switch ($controller) {
                case 'product':
                    if ($idProduct) {
                        if (
                            (!Tools::isSubmit('editmp_front_products') && ( 'cart' !== $meta->page))
                            || (!$token && (int) Tools::getValue('quantity_wanted'))
                        ) {
                            $_GET['id_product'] = $idProduct;
                            $_GET['controller'] = 'product';

                            return Tools::getValue('controller');
                        }
                    }
                    $productRule = @$routes[$curIdShop][$curIdLang]['product_rule'];
                    if (!$idProduct && $linkRewrite) {
                        if (@preg_match(@$productRule['regexp'], $this->request_uri) && $idProduct = $this->getProductIdBySlug($linkRewrite)) {
                            $success = true;
                            if (!Tools::getValue('id_product_attribute') && $defaultAttr = (int) Product::getDefaultAttribute($idProduct)) {
                                $_GET['id_product_attribute'] = $defaultAttr;
                            }
                        }
                        if ($routes[$curIdShop][$curIdLang]['cart']['rule'] == $linkRewrite && '{rewrite}' == trim($routes[$curIdShop][$curIdLang]['product_rule']['rule'])) {
                            return 'cart';
                        }
                    }
                    if ($success) {
                        $_GET['controller'] = 'product';
                        $_GET['id_product'] = $idProduct;

                        return Tools::getValue('controller');
                    }
                    // no break
                case 'category':
                    if ($idCategory && !$token) {
                        $success = true;
                    }
                    $catRule = @$routes[$curIdShop][$curIdLang]['category_rule'];
                    if (!$idCategory && $linkRewrite && @preg_match(@$catRule['regexp'], $this->request_uri)) {
                        if ($idCategory = $this->getCategoryIdBySlug($linkRewrite)) {
                            $success = true;
                        }
                    }
                    if ($success) {
                        $_GET['id_category'] = $idCategory;
                        $_GET['controller'] = 'category';

                        return Tools::getValue('controller');
                    }
                    // no break;
                case 'cms':
                    $cmsRule = @$routes[$curIdShop][$curIdLang]['cms_rule'];
                    if ($idCms && !$token) {
                        $success = true;
                    }
                    if (!$idCms && $linkRewrite && @preg_match(@$cmsRule['regexp'], $this->request_uri)) {
                        if ($idCms = $this->getCmsIdBySlug($linkRewrite)) {
                            $_GET['id_cms'] = $idCms;
                            $success = true;
                        }
                    }
                    if ($success) {
                        $_GET['id_cms'] = $idCms;
                        $_GET['controller'] = 'cms';

                        return Tools::getValue('controller');
                    }
                    // no break;
                case 'cms_category':
                    if ($idCmsCategory && !$token) {
                        $success = true;
                    }
                    $cmsCatRule = @$routes[$curIdShop][$curIdLang]['cms_category_rule'];
                    if (!$idCmsCategory && $linkRewrite && @preg_match(@$cmsCatRule['regexp'], $this->request_uri)) {
                        if ($idCmsCategory = $this->getCmsCategoryIdBySlug($linkRewrite)) {
                            $_GET['id_cms_category'] = $idCmsCategory;
                            $success = true;
                        }
                    }
                    if ($success) {
                        $_GET['controller'] = 'cms';
                        $_GET['id_cms_category'] = $idCmsCategory;

                        return Tools::getValue('controller');
                    }
                    // no break;
                case 'manufacturer':
                    if ($idManufacturer && !$token) {
                        $success = true;
                    }
                    $brandRule = @$routes[$curIdShop][$curIdLang]['manufacturer_rule'];
                    if (!$idManufacturer && $linkRewrite && @preg_match(@$brandRule['regexp'], $this->request_uri)) {
                        if ($idManufacturer = $this->getManufIdBySlug($linkRewrite)) {
                            $success = true;
                        }
                    }
                    if ($success) {
                        $_GET['controller'] = 'manufacturer';
                        $_GET['id_manufacturer'] = $idManufacturer;

                        return Tools::getValue('controller');
                    }
                    // no break;
                case 'supplier':
                    if ($idSupplier && !$token) {
                        $success = true;
                    }
                    $supplierRule = @$routes[$curIdShop][$curIdLang]['supplier_rule'];
                    if (!$idSupplier && $linkRewrite && @preg_match(@$supplierRule['regexp'], $this->request_uri)) {
                        if ($idSupplier = $this->getSupplierIdBySlug($linkRewrite)) {
                            $success = true;
                        }
                    }
                    if ($success) {
                        $_GET['id_supplier'] = $idSupplier;
                        $_GET['controller'] = 'supplier';

                        return Tools::getValue('controller');
                    }
                    // no break;
                default:
                    break;
            }
        }

        if (!$this->routes) {
            $this->routes = $routes;
        }
        if (!in_array($controller, $this->listDefaultControllers)) {
            $success = true;
        }
        if (!$success && $controller && 'module' !== $fc && !$module && isset($routes[$curIdShop][$curIdLang]) && count($routes[$curIdShop][$curIdLang])) {
            foreach ($routes[$curIdShop][$curIdLang] as $key => $route) {
                if (!isset($route['controller']) || !$route['controller']) {
                    unset($routes[$curIdShop][$curIdLang][$key]);
                    continue;
                }

                if ($route['controller'] == $controller) {
                    if (isset($route['keywords']) && (0 == count($route['keywords']))) {
                        unset(${'_GET'}['rewrite']);
                        $success = true;
                        break;
                    }

                    if (isset($route['params']['fc'], $route['params']['module']) && 'module' == $route['params']['fc'] && $route['params']['module']) {
                        $success = true;
                    } else {
                        unset($routes[$curIdShop][$curIdLang][$key]);
                    }

                    break;
                }
            }
            if (!$success) {
                $dispatcherCore->setRoutes($routes);
                $controller = $dispatcherCore->getControllerChecking();
            }
        }
        if (null !== $linkRewrite && 'module' !== $fc && ($page = $this->getPageController($linkRewrite))) {
            if (!(int) Tools::getValue('id_shop')) {
                ${'_POST'}['id_shop'] = Ets_Seo::getContextStatic()->shop->id;
            }
            if (!(int) Tools::getValue('id_lang')) {
                ${'_POST'}['id_lang'] = Ets_Seo::getContextStatic()->language->id;
            }
            if (false !== strpos($page, 'module-')) {
                $frags = explode('-', $page);
                $dispatcherCore->setFrontController(Dispatcher::FC_MODULE);
                $dispatcherCore->setRoutes($this->routes);
                $controller = $frags[2];
                $_GET['module'] = $frags[1];

                return $controller;
            }
            $controller = $page;
        }
        if ($this->routes) {
            $dispatcherCore->setRoutes($this->routes);
        }

        $controller = str_replace('-', '', $controller);

        return $controller;
    }

    /**
     * @param \Dispatcher $dispatcher
     */
    public function mergeRssRoute(Dispatcher $dispatcher)
    {
        $shops = Shop::getShops();
        $langs = Language::getLanguages();
        $this->mergeSitemapRoute($dispatcher, $shops, $langs);
        if (!(bool) Configuration::get('ETS_SEO_RSS_ENABLE')) {
            return;
        }
        foreach ($shops as $shop) {
            foreach ($langs as $lang) {
                $meta = Meta::getMetaByPage('module-ets_seo-rss', $lang['id_lang']);
                $prefix = $meta && isset($meta['url_rewrite']) && $meta['url_rewrite'] ? $meta['url_rewrite'] : 'rss';
                $rule = $prefix . '{/:frags:.xml}';
                $keyword = [
                    'frags' => ['regexp' => '[a-z0-9\/\-_]+'],
                ];
                $dispatcher->addRoute('module-ets_seo-rss', $rule, 'module-ets_seo-rss', $lang['id_lang'], $keyword, ['fc' => 'module', 'module' => 'ets_seo'], $shop['id_shop']);
            }
        }
    }

    /**
     * @param \Dispatcher $dispatcher
     * @param array $shops
     * @param array $langs
     */
    public function mergeSitemapRoute(Dispatcher $dispatcher, $shops, $langs)
    {
        if (!(bool) Configuration::get('ETS_SEO_ENABLE_XML_SITEMAP')) {
            return;
        }
        foreach ($shops as $shop) {
            foreach ($langs as $lang) {
                $meta = Meta::getMetaByPage('module-ets_seo-sitemap', $lang['id_lang']);
                $prefix = $meta && isset($meta['url_rewrite']) && $meta['url_rewrite'] ? $meta['url_rewrite'] : 'sitemap';
                $rule = $prefix . '{/:page}{/:id}.xml';
                $keyword = [
                    'page' => ['regexp' => '[a-z0-9\/\-_]*'],
                    'id' => ['regexp' => '[a-z0-9\/\-_]*'],
                ];
                $dispatcher->addRoute('module-ets_seo-sitemap', $rule, 'module-ets_seo-sitemap', $lang['id_lang'], $keyword, ['fc' => 'module', 'module' => 'ets_seo'], $shop['id_shop']);
                
                // SmartBlog sitemap route (same style as ybc_blog)
                if (Module::isEnabled('smartblog')) {
                    $smartblogRule = 'smartblog_sitemap.xml';
                    $dispatcher->addRoute('module-ets_seo-sitemap-smartblog', $smartblogRule, 'module-ets_seo-sitemap', $lang['id_lang'], [], ['fc' => 'module', 'module' => 'ets_seo'], $shop['id_shop']);
                }
            }
        }
    }

    /**
     * @param int $type
     *
     * @return string
     */
    private function getRedirectHeaderText($type)
    {
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        if (301 == $type) {
            $text = 'Moved Permanently';
        } elseif (302 == $type) {
            $text = 'Found';
        } else {
            $type = 303;
            $text = 'See Other';
        }

        return sprintf('%s %s %s', $protocol, $type, $text);
    }

    /**
     * Do redirect if url matched in DB - table : ets_seo_redirect.
     *
     * @param string|null $requestUri
     */
    public function checkForRedirect($requestUri = null)
    {
        if ($requestUri) {
            $this->request_uri = $requestUri;
        }
        if (defined('_PS_ADMIN_DIR_')) {
            return;
        }
        if (!(bool) Configuration::get('ETS_SEO_ENABLE_URL_REDIRECT')) {
            return;
        }
        if (!$requestUri) {
            $requestUri = urldecode($_SERVER['REQUEST_URI']);
        }
        $ctx = Ets_Seo::getContextStatic();
        // Find redirect matching given url
        $baseUrl = self::getFullUrlFromRequestUri($requestUri);
        $redirect = EtsSeoRedirect::getTypeUrlRedirect($baseUrl, $ctx, true);
        $langUrl = '';
        $isUseLangUrl = false;
        if (!$redirect) {
            // if redirect not found , prepend Lang ISO code then find
            $langUrl = self::getFullUrlFromRequestUri(EtsUrlHelper::getInstance()->prependLangIso($requestUri, $ctx->language->iso_code));
            $redirect = EtsSeoRedirect::getTypeUrlRedirect($langUrl, $ctx, true);
            $isUseLangUrl = (bool) $redirect;
        }
        // Do redirect if matched
        if ($redirect && $redirect['target']) {
            if (false === strpos($redirect['target'], 'http://') && false === strpos($redirect['target'], 'https://')) {
                $redirect['target'] = parse_url($baseUrl, PHP_URL_SCHEME) . '://' . trim($redirect['target']);
            }
            if ($redirect['target'] === $baseUrl || ($isUseLangUrl && $redirect['target'] === $langUrl)) {
                return;
            }
            header('X-Seo-Redirected: 1');
            Tools::redirect($redirect['target'], __PS_BASE_URI__, $ctx->link, $this->getRedirectHeaderText($redirect['type']));
            exit;
        }
    }

    /**
     * @param string|null $uri
     *
     * @return string
     */
    public static function getFullUrlFromRequestUri($uri = null)
    {
        if (!$uri) {
            $uri = $_SERVER['REQUEST_URI'];
        }
        $uri = ltrim($uri, '/');
        $shopUrl = Ets_Seo::getContextStatic()->shop->getBaseURL(true, true);
        $shopUrl = rtrim($shopUrl, '/');

        return $shopUrl . '/' . $uri;
    }

    /**
     * @param string $rewrite
     * @param string $type
     *
     * @return int|null
     */
    private function guessIdFromRewriteUrl($rewrite, $type)
    {
        /** @var array $keyMappings */
        $keyMappings = [
            'product' => [
                'key' => 'id_product',
                'slugMatch' => 'getProductIdBySlug',
                'regexPattern' => '/^([0-9]+)-(.+)$/',
                'slugGrIdx' => 2,
                'idGrIdx' => 1,
            ],
            'category' => [
                'key' => 'id_category',
                'slugMatch' => 'getCategoryIdBySlug',
                'regexPattern' => '/^([0-9]+)-(.+)$/',
                'slugGrIdx' => 2,
                'idGrIdx' => 1,
            ],
            'supplier' => [
                'key' => 'id_supplier',
                'slugMatch' => 'getSupplierIdBySlug',
                'regexPattern' => '/^([0-9]+)-(.+)$/',
                'slugGrIdx' => 2,
                'idGrIdx' => 1,
            ],
            'manufacturer' => [
                'key' => 'id_manufacturer',
                'slugMatch' => 'getManufIdBySlug',
                'regexPattern' => '/^([0-9]+)-(.+)$/',
                'slugGrIdx' => 2,
                'idGrIdx' => 1,
            ],
            'cms' => [
                'key' => 'id_cms',
                'slugMatch' => 'getCmsIdBySlug',
                'regexPattern' => '/^([0-9]+)-(.+)$/',
                'slugGrIdx' => 2,
                'idGrIdx' => 1,
            ],
            'cms_category' => [
                'key' => 'id_cms_category',
                'slugMatch' => 'getCmsCategoryIdBySlug',
                'regexPattern' => '/^([0-9]+)-(.+)$/',
                'slugGrIdx' => 2,
                'idGrIdx' => 1,
            ],
        ];
        $tries = ['getValue', 'slug', 'regexSlug', 'regexId'];
        $idx = 0;
        $id = null;
        if (array_key_exists($type, $keyMappings) && ($detail = $keyMappings[$type])) {
            $linkRewrite = $rewrite;
            $fn = $detail['slugMatch'];
            $expr = $detail['regexPattern'];
            $m = false;
            if (!class_exists(EtsSeoStrHelper::class)) {
                require_once __DIR__ . '/../utils/EtsSeoStrHelper.php';
            }
            if (EtsSeoStrHelper::endsWith($linkRewrite, '.html')) {
                $linkRewrite = EtsSeoStrHelper::beforeLast($linkRewrite, '.html');
            }
            if (EtsSeoStrHelper::contains($linkRewrite, '/')) {
                $linkRewrite = EtsSeoStrHelper::afterLast($linkRewrite, '/');
            }
            while ($idx < count($tries)) {
                $t = $tries[$idx];
                ++$idx;
                switch ($t) {
                    case 'getValue':
                        $id = (int) Tools::getValue($detail['key']);
                        break;
                    case 'slug':
                        $id = ($fn ? $this->$fn($linkRewrite) : null);
                        break;
                    case 'regexSlug':
                        preg_match($expr, $linkRewrite, $m) && isset($m[$detail['slugGrIdx']]) && $fn && ($id = (int) $this->$fn($m[$detail['slugGrIdx']]));
                        break;
                    case 'regexId':
                        $m && isset($m[$detail['idGrIdx']]) && ($id = (int) $m[$detail['idGrIdx']]);
                        break;
                }

                if ($id) {
                    return $id;
                }
            }
        }

        return null;
    }
    public function redirectToOldUrl($dispatcherCore, $redirectToUrlHasId = false)
    {
        $controller = '404';
        $linkRewrite = $this->getLinkRewrite();
        if ($redirectToUrlHasId && (int) Configuration::get('ETS_SEO_SET_REMOVE_ID')) {
            if ($this->setOldDefaultRoute('old')) {
                $dispatcherCore->setDefaultRoutes($this->old_routes);
                $dispatcherCore->setOldRoutes();
                $controller = $dispatcherCore->getControllerForRedirect();
                // set id product, category if exist in url
                // $dispatcherCore->getControllerCore();
            }
            if (('404' == $controller || 'pagenotfound' == $controller) && $this->setOldDefaultRoute('nearest')) {
                $dispatcherCore->setDefaultRoutes($this->old_routes);
                $dispatcherCore->setOldRoutes();
                $controller = $dispatcherCore->getControllerForRedirect();
            }
            if (('404' == $controller || 'pagenotfound' == $controller) && $this->setOldDefaultRoute('root')) {
                $dispatcherCore->setDefaultRoutes($this->old_routes);
                $dispatcherCore->setOldRoutes();
                $controller = $dispatcherCore->getControllerForRedirect();
            }
            if ('404' == $controller && ($newController = $dispatcherCore->getControllerForRedirect()) && !in_array($newController, ['pagenotfound', '404'], true)) {
                $controller = $newController;
                $dispatcherCore->getControllerForRedirect();
            }

            if ('404' == $controller) {
                if (preg_match('/^\/?([_a-zA-Z0-9\x{0600}\-\x{06FF}\pL\pS-]+)\/.+\.html$/u', $linkRewrite)) {
                    $controller = 'product';
                }
                if (($newController = $dispatcherCore->getControllerForRedirect()) && !in_array($newController, ['pagenotfound', '404'], true)) {
                    $controller = $newController;
                    $dispatcherCore->getControllerCore();
                }
            }

            $rules = $this->getConfigRule(true);
            $dispatcherCore->setDefaultRoutes($rules);
            $dispatcherCore->publicLoadRoutes();
        } elseif (!$redirectToUrlHasId && (int) Configuration::get('ETS_SEO_SET_REMOVE_ID')) {
            $this->setDefaultRoute();
            $dispatcherCore->setDefaultRoutes($this->default_routes);
            $dispatcherCore->setOldRoutes();
            $controller = $dispatcherCore->getControllerForRedirect();

            $dispatcherCore->setDefaultRoutes($this->getConfigRule());
            $dispatcherCore->publicLoadRoutes();
        }
        $redirectLink = null;
        if ($controller && '404' != $controller) {
            $id_lang = (int) Ets_Seo::getContextStatic()->language->id;
            $link = Ets_Seo::getContextStatic()->link;
            switch ($controller) {
                case 'product':
                    $id = $this->guessIdFromRewriteUrl($linkRewrite, 'product');
                    $product = new Product((int)$id, false  , $id_lang);
                    $idProductAttr = Tools::getValue('id_attribute');
                    if (Validate::isLoadedObject($product)) {
                        $redirectLink = $link->getProductLink($product->id, $product->link_rewrite, $product->category, $product->ean13, $id_lang, null, $idProductAttr ?: null);
                    }
                    break;
                case 'category':
                    $id = $this->guessIdFromRewriteUrl($linkRewrite, 'category');
                    $category = new Category($id, $id_lang);
                    if (Validate::isLoadedObject($category)) {
                        $redirectLink = $category->getLink($link, $id_lang);
                    }
                    break;
                case 'cms':
                    $idCms = $this->guessIdFromRewriteUrl($linkRewrite, 'cms');
                    $cms = new CMS($idCms, (int) $id_lang);
                    if (Validate::isLoadedObject($cms)) {
                        $redirectLink = $link->getCMSLink($cms, null, null, $id_lang);
                        // only break when cms is found
                        break;
                    }
                    // no break
                case 'cms_category':
                    $id = $this->guessIdFromRewriteUrl($linkRewrite, 'cms_category');
                    $cmsCategory = new CMSCategory($id, (int) $id_lang);
                    if (Validate::isLoadedObject($cmsCategory)) {
                        $redirectLink = $link->getCMSCategoryLink($cmsCategory, null, $id_lang);
                    }
                    break;
                case 'manufacturer':
                    $id = $this->guessIdFromRewriteUrl($linkRewrite, 'manufacturer');
                    $manufacturer = new Manufacturer($id, $id_lang);
                    if (Validate::isLoadedObject($manufacturer)) {
                        $redirectLink = $link->getManufacturerLink($manufacturer, null, $id_lang);
                    }
                    break;
                case 'supplier':
                    $id = $this->guessIdFromRewriteUrl($linkRewrite, 'supplier');
                    $supplier = new Supplier($id, $id_lang);
                    if (Validate::isLoadedObject($supplier)) {
                        $redirectLink = $link->getSupplierLink($supplier, null, $id_lang);
                    }
                    break;
            }

            if ($redirectLink) {
                $statusCode = (int) Configuration::get('ETS_SEO_REDIRECT_STATUS_CODE');
                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                if (301 == $statusCode) {
                    $header = $protocol . ' 301 Moved Permanently';
                } else {
                    $header = $protocol . ' 302 Moved Temporarily';
                }

                header('X-Seo-Redirected: 1');
                Tools::redirect($redirectLink, __PS_BASE_URI__, null, $header);
            }
        }
    }

    public function getConfigRule($noid = false)
    {
        if ($noid) {
            return $this->getDefaultRouteNoId();
        } else {
            $routes = $this->old_routes;
            foreach ($routes as $key => &$route) {
                $route['rule'] = Configuration::get('PS_ROUTE_' . $key);
            }

            return $routes;
        }
    }

    public function getPageController($linkRewrite)
    {
        $context = Ets_Seo::getContextStatic();
        $shopId = (int) $context->shop->id;
        $langId = (int) $context->language->id;

        return Db::getInstance()->getValue('SELECT m.page FROM `' . _DB_PREFIX_ . 'meta` m 
                                JOIN `' . _DB_PREFIX_ . 'meta_lang` ml ON m.id_meta = ml.id_meta AND ml.id_lang=' . (int) $langId . ' AND ml.id_shop=' . (int) $shopId . " 
                                WHERE ml.url_rewrite='" . pSQL($linkRewrite) . "' AND ml.id_lang=" . (int) $langId . ' AND ml.id_shop=' . (int) $shopId);
    }

    public function isCartPage($linkRewrite)
    {
        $context = Ets_Seo::getContextStatic();
        $shopId = (int) $context->shop->id;
        $langId = (int) $context->language->id;

        return (int) Db::getInstance()->getValue('SELECT m.id_meta FROM `' . _DB_PREFIX_ . 'meta` m 
                                JOIN `' . _DB_PREFIX_ . 'meta_lang` ml ON m.id_meta = ml.id_meta AND ml.id_lang=' . (int) $langId . ' AND ml.id_shop=' . (int) $shopId . " 
                                WHERE m.page='cart' AND ml.url_rewrite='" . pSQL($linkRewrite) . "' AND ml.id_lang=" . (int) $langId . ' AND ml.id_shop=' . (int) $shopId);
    }

    /**
     * @deprecated Since 2.6.3
     *
     * @param Dispatcher $dispatcherCore
     * @param string $controller
     * @param string $uri
     *
     * @return string
     */
    public function getSitemapAndRssController($dispatcherCore, $controller, $uri)
    {
        return $controller;
    }
}
