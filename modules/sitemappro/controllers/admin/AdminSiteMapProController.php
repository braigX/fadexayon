<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    SeoSA <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

require_once(dirname(__FILE__).'/../../classes/tools/config.php');

class AdminSiteMapProController extends ModuleAdminControllerSMP
{
    public function __construct()
    {
        $this->display = 'edit';
        $this->bootstrap = true;
        $this->className = 'SitemapConfig';
        parent::__construct();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => ''
        );
        $this->addCSS($this->module->getPathUri().'views/css/select2.min.css');
        $this->addCSS($this->module->getPathUri().'views/css/jquery-confirm.css');
        $this->addJS($this->module->getPathUri().'views/js/select2.full.min.js');
        $this->context->controller->addJS($this->module->getPathUri().'views/js/bootstrap-dropdown.js');
        $this->context->controller->addJS($this->module->getPathUri().'views/js/langField.jquery.js');
        $this->context->controller->addJS($this->module->getPathUri().'views/js/underscore.js');
        $this->context->controller->addJS($this->module->getPathUri().'views/js/compatibility.js');
        $this->context->controller->addJS($this->module->getPathUri().'views/js/message_viewer.js');
        $this->context->controller->addJS($this->module->getPathUri().'views/js/tabContainer.jquery.js');
        $this->context->controller->addJS($this->module->getPathUri().'views/js/tree_custom.js');
        $this->context->controller->addJS($this->module->getPathUri().'views/js/jquery-confirm.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/pagination.js');
        $this->context->controller->addJS($this->module->getPathUri().'views/js/admin.js');
        if (version_compare(_PS_VERSION_, '1.6.0', '<')) {
            $this->context->controller->addCSS($this->module->getPathURI().'views/css/admin-theme.css');
        }
        ToolsModuleSMP::autoloadCSS($this->module->getPathUri().'/views/css/autoload/');
        if (version_compare(_PS_VERSION_, '1.7.8', '>')) {
            $this->context->controller->addCSS($this->module->getPathURI().'views/css/admin-theme-178.css');
        }

        $pages = Meta::getPages();
        $tmp_pages = array();

        foreach (array_keys($pages) as $page) {
            $meta = Meta::getMetaByPage($page, $this->context->language->id);
            if (!$meta) {
                continue;
            }
            $tmp_pages[] = $meta;
        }

        $categories = Category::getCategories($this->context->language->id, false);

        $id_root_category = Configuration::get('PS_ROOT_CATEGORY');
        if (!isset($categories[$id_root_category]) || !count($categories[$id_root_category])) {
            $id_root_category = Configuration::get('PS_HOME_CATEGORY');
        }
        $sitemap_excluded  = (
            Configuration::get('SEOSA_SITEMAP_EXCLUDED_PRODUCTS')
            ? $this->getSelect2Products(
                array(),
                json_decode(Configuration::get('SEOSA_SITEMAP_EXCLUDED_PRODUCTS'), true)
            )
            : array()
        );

        if (isset($_SERVER['HTTPS'])) {
            $label_protocol = "https";
        } else {
            $label_protocol  = "http";
        }
        $base_link = ConfSMP::getConf('protocol');

        $this->tpl_form_vars = array(
            'categories' => $categories,
            'id_root_category' => $id_root_category,
            'changefreqs' => SitemapConfig::getChangeFreqs(),
            'priorities' => SitemapConfig::getPriorities(),
            'cms' => self::getCMS($this->context->language->id, 20, 1),
            'id_shop' => Shop::getContextShopID(),
            'meta' => $tmp_pages,
            'protocol' => ($base_link == 'HTTP') ? 1 : 0,
            'label_protocol' => $label_protocol,
            'conf_categories' => SitemapConfigCategory::getConfig(),
            'conf_products' => SitemapConfigProduct::getConfig(),
            'conf_meta' => SitemapConfigMeta::getConfig(),
            'conf_cms' => SitemapConfigCms::getConfig(),
            'conf_manufacturers' => SitemapConfigManufacturer::getConfig(),
            'conf_suppliers' => SitemapConfigSupplier::getConfig(),
            'sitemaps' => ToolsSMP::getSitemaps(Shop::getContextShopID()),
            'languages' => ToolsModuleSMP::getLanguages(false),
            'user_links' => UserLink::getCollection(true),
            'shop_url' => Context::getContext()->link->getPageLink('index'),
            'shop_domain' => ToolsSMP::getShopDomain(),
            'sitemap_excluded' => $sitemap_excluded,
            'id_lang_default' => $this->context->language->id,
            'default_settings' => SitemapConfig::getDefaultSettings(),
            'sitemap_categories' => SitemapConfig::getSitemapCategories(),
            'link_error_log' => _MODULE_DIR_.$this->module->name.'/error.log',
            'protect' => ConfSMP::getConf('SMP_PROTECT'),
            'secret' => ConfSMP::getConf('SMP_SECRET'),
            'secret_file' =>  ConfSMP::getConf('SMP_SECRET_FILE'),
            'protect_file' => ConfSMP::getConf('SMP_PROTECT_FILE'),
        );

        return parent::renderForm();
    }

    public function getSelect2Products($categories = array(), $ids_products = array(), $search = null)
    {
        if (!count($ids_products) && !count($categories)) {
            return array();
        }
        $sql = 'SELECT DISTINCT(cp.`id_product`) as `id`, CONCAT(cp.`id_product`, "| ", pl.`name`) as `text`
                FROM `'._DB_PREFIX_.'product_lang` pl 
                JOIN `'._DB_PREFIX_.'category_product` cp ON pl.`id_product` = cp.`id_product`
                WHERE pl.`id_lang` = "'.(int)$this->context->language->id.'"'
            .(count($categories) ? ' AND cp.`id_category` IN ('.pSQL(implode(',', $categories)).')' : '')
            .(count($ids_products) ? ' AND cp.`id_product` IN ('.pSQL(implode(',', $ids_products)).')' : '')
            .(!is_null($search) ? ' HAVING '.ToolsModuleSMP::buildSQLSearchWhereFromQuery(
                $search,
                true,
                '`text`'
            ) : '');
        $products = Db::getInstance()->executeS($sql);
        return is_array($products) ? $products : array();
    }

    public function ajaxProcessSearchProduct()
    {
        $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP
            ? (int)$this->context->shop->id : 'p.id_shop_default';
        $categories = ToolsSMP::gerIntArrayFromRequest('categories');
        $search_query = Tools::getValue('search_query');
        $exclude_ids = ToolsSMP::gerIntArrayFromRequest('exclude_ids');
        $sql_search_query = '';
        if ($search_query) {
            $words = explode(' ', $search_query);
            ToolsSMP::pSQLArrayAndRemoveEmpty($words);
            $sql_search_query = ' AND pl.`name` REGEXP "'.implode('|', $words).'"';
        }
        if (is_array($categories) && count($categories)) {
            $products = Db::getInstance()->executeS(
                'SELECT p.`id_product`, CONCAT(p.`id_product`,"| ", pl.`name`) as `name`,
                (SELECT group_concat(DISTINCT cp.`id_category`) FROM '._DB_PREFIX_.'category_product cp
                WHERE cp.`id_category` IN('.implode(',', array_map('intval', $categories)).')
                 AND cp.`id_product` = p.`id_product`) as categories
                FROM '._DB_PREFIX_.'product p
                LEFT JOIN '._DB_PREFIX_.'product_shop ps ON ps.`id_product` = p.`id_product`
                LEFT JOIN '._DB_PREFIX_.'product_lang pl ON p.`id_product` = pl.`id_product`
                 AND pl.`id_lang` = '.(int)$this->context->language->id.'
                WHERE 1 AND p.`active` = 1 '.$sql_search_query.' AND (SELECT COUNT(cp.`id_category`)
                 FROM '._DB_PREFIX_.'category_product cp
                WHERE ps.`id_shop` = '.$id_shop.' AND pl.`id_shop` = '.$id_shop.'
                AND cp.`id_category` IN('.implode(',', array_map('intval', $categories)).')
                AND cp.`id_product` = p.`id_product`)'
                .(is_array($exclude_ids) && count($exclude_ids)
                ? ' AND p.`id_product` NOT IN('.implode(',', array_map('intval', $exclude_ids)).')' : '')
            );
            die(json_encode($products));
        } else {
            die(json_encode(array()));
        }
    }

    public function ajaxProcessSearchManufacturer()
    {
        $id_shop = Shop::getContextShopID();
        $query = Tools::getValue('search');
        $excl_ids = Tools::getValue('excl_ids');
        $result = Db::getInstance()->executeS(
            'SELECT CONCAT(m.`id_manufacturer`, "| ", m.`name`) as `text`,
            m.`id_manufacturer` as `id`
            FROM '._DB_PREFIX_.'manufacturer m
             LEFT JOIN '._DB_PREFIX_.'manufacturer_shop ms on ms.id_manufacturer = m.id_manufacturer
            where ms.id_shop = '.$id_shop .'
            '.(
            is_array($excl_ids) && count($excl_ids)
                ? 'and m.`id_manufacturer` NOT IN('.implode(
                    ',',
                    array_map('intval', $excl_ids)
                ).')' : ''
            ).'
            HAVING '.ToolsModuleSMP::buildSQLSearchWhereFromQuery(
                $query,
                false,
                '`text`'
            )
        );

        die(json_encode(
            array(
                'results' => (is_array($result) ? $result : array()),
                'pagination' => array(
                    'more' => false
                )
            )
        ));
    }

    public function ajaxProcessSearchSupplier()
    {
        $id_shop = Shop::getContextShopID();
        $query = Tools::getValue('query');
        $excl_ids = Tools::getValue('excl_ids');
        $result = Db::getInstance()->executeS(
            'SELECT CONCAT(s.`id_supplier`, "| ", s.`name`) as `text`,
            s.`id_supplier` as `id`
            FROM '._DB_PREFIX_.'supplier s
             LEFT JOIN '._DB_PREFIX_.'supplier_shop ss on ss.id_supplier = s.id_supplier
            where ss.id_shop = '.$id_shop .'
            '.(
            is_array($excl_ids) && count($excl_ids)
                ? 'and s.`id_supplier` NOT IN('.implode(
                    ',',
                    array_map('intval', $excl_ids)
                ).')' : ''
            ).'
            HAVING '.ToolsModuleSMP::buildSQLSearchWhereFromQuery(
                $query,
                false,
                '`text`'
            )
        );

        die(json_encode(
            array(
                'results' => (is_array($result) ? $result : array()),
                'pagination' => array(
                    'more' => false
                )
            )
        ));
    }

    public function ajaxProcessSearchCategory()
    {
        $query = Tools::getValue('search');
        $excl_ids = Tools::getValue('excl_ids');
        $selected_categories = Tools::getValue('selected_categories');

        $id_lang = (int)$this->context->language->id;
        $sql = new DbQuery();
        $sql->select(
            'CONCAT(c.`id_category`, "| ", cl.`name`) as `text`,
            c.`id_category` as `id`'
        );
        $sql->from('category', 'c');
        $sql->leftJoin('category_lang', 'cl', 'c.`id_category` = cl.`id_category`
         AND cl.`id_lang` = '.(int)$id_lang);
        $sql->leftJoin('category_shop', 'cs', 'c.`id_category` = cs.`id_category`');
        $sql->where('cl.`id_shop` = '.(int)Shop::getContextShopID());
        $sql->where('cs.`id_shop` = '.(int)Shop::getContextShopID());
        $sql->having(ToolsModuleSMP::buildSQLSearchWhereFromQuery(
            $query,
            false,
            '`text`'
        ));
        if (is_array($excl_ids) && count($excl_ids)) {
            $sql->where(
                'c.`id_category` NOT IN('.implode(
                    ',',
                    array_map('intval', $excl_ids)
                ).')'
            );
        }
        if (is_array($selected_categories) && count($selected_categories)) {
            $sql->where(
                'c.`id_category` IN('.implode(
                    ',',
                    array_map('intval', $selected_categories)
                ).')'
            );
        }

        $result = Db::getInstance()->executeS($sql->build());

        die(json_encode(
            array(
                'results' => (is_array($result) ? $result : array()),
                'pagination' => array(
                    'more' => false
                )
            )
        ));
    }

    public function ajaxProcessProductsSelectedCategories()
    {
        if ($categories = Tools::getValue('categories')) {
            $ids_categories = array();
            foreach ($categories as $category) {
                $ids_categories[] = (int)$category['id'];
            }
            $search = Tools::getValue('search');
            $products = $this->getSelect2Products(
                $ids_categories,
                array(),
                $search
            );

            if ($products) {
                die(json_encode(array(
                    'results' => $products,
                    'pagination' => array(
                        'more' => false
                    )
                )));
            }
        }
        die(json_encode(array(
            'results' => array(),
            'pagination' => array(
                'more' => false
            )
        )));
    }

    public function ajaxProcessSaveConf()
    {
        $sitemap = Tools::getValue('sitemap');
        $data = ToolsModuleSMP::properParseStr(urldecode($sitemap));
        $excluded_products = (isset($data['sitemap_excluded']) ? $data['sitemap_excluded'] : array());
        $user_links = (isset($data['user_links']) ? $data['user_links'] : array());
        $default_settings = (isset($data['default_settings']) ? $data['default_settings'] : array());
        $categories = (isset($data['categories']) ? $data['categories'] : array());
        $id_shop = (int)Shop::getContextShopID();

        if (is_array($data)
            && isset($data['sitemap'])
            && is_array($data['sitemap'])
            && count($data['sitemap'])) {
            // перед удаление считываем настройки cms
            $all_cms_setting = DB::getInstance()->executes(
                'SELECT `type_object`, `id_object`, `priority`, `changefreq`, `is_export` 
                 FROM `' . _DB_PREFIX_ . 'site_map_item_conf` 
                 WHERE `type_object` = "cms"');
            array_unique($all_cms_setting);

            Db::getInstance()->delete(
                'site_map_item_conf',
                'id_shop = ' . (int)$id_shop
            );

            $insert = array();
            // берем все cms из Prestashop
            $all_cms = self::getCMS(Context::getContext()->language->id, 1000000, 0);
            $index = 0;
            // из ajax берем измененые настройки
            $cms_ajax = array();
            foreach ($data['sitemap'] as $key => $element) {
                if ($element['type_object'] == 'cms') {
                    $cms_ajax[] = $element;
                    unset($data['sitemap'][$key]);
                }
            }
            $index = 0;
            $cms = array();
// во все cms вставляем старые настройки если нет создаем дефолтные
            foreach ($all_cms as $key => $element) {
                if(!empty($all_cms_setting)) {
                    foreach ($all_cms_setting as $item) {
                        if ($item['id_object'] == $element['id_cms']) {
                            $cms[$key] = $item;
                            $index = 1;
                            break;
                        }
                        if ($index == 0) {
                            $cms[$key] = array(
                                "type_object" => "cms",
                                "id_object" => $element['id_cms'],
                                "priority" => 0.5,
                                "changefreq" => "always",
                                "is_export" => 1
                            );
                        }
                        $index = 0;
                    }
                } else {
                    $cms[$key] = array(
                        "type_object" => "cms",
                        "id_object" => $element['id_cms'],
                        "priority" => 0.5,
                        "changefreq" => "always",
                        "is_export" => 1
                    );
                }
            }

            foreach ($cms as $key => $cms_item) {
                foreach ($cms_ajax as $cms_ajax_item) {
                    if ($cms_item['id_object'] == $cms_ajax_item['id_object']) {
                        if (empty($cms_ajax_item['is_export'])) {
                            $cms_ajax_item['is_export'] = 0;
                        }
                        $cms[$key] = $cms_ajax_item;
                    }
                }
            }
            $merge_cms = array_merge($data['sitemap'], $cms);

            foreach ($merge_cms as $item) {
                if (in_array(
                        $item['type_object'],
                        array('category', 'cms', 'meta', 'manufacturer', 'supplier')
                    ) && !isset($item['is_export'])) {
                    $item['is_export'] = 0;
                }
                $item['is_export'] = (!empty($item['is_export']) ? $item['is_export'] : 0);
                $item['id_shop'] = $id_shop;
                $insert[] = $item;
            }

            Db::getInstance()->insert('site_map_item_conf', $insert);
        } else {
            $this->errors[] = $this->l('No sitemap conf');
        }

        if (is_array($user_links) && count($user_links)) {
            foreach ($user_links as &$user_link) {
                if ((int)$user_link['id_user_link'] && (int)$user_link['deleted']) {
                    $object = new UserLink($user_link['id_user_link']);
                    if (Validate::isLoadedObject($object)) {
                        $object->delete();
                    }
                } elseif ($user_link['deleted']) {
                    continue;
                } else {
                    $object = new UserLink($user_link['id_user_link']);
                    $object->changefreq = pSQL($user_link['changefreq']);
                    $object->priority = pSQL(round($user_link['priority'], 1));
                    $object->link = array_map('pSQL', $user_link['link']);

                    try {
                        $object->save();
                        $user_link['id_user_link'] = $object->id;
                    } catch (Exception $e) {
                        $this->errors[] = sprintf(
                            $this->l('Can not save user link, data: %s%sMessage: %s'),
                            print_r($user_link),
                            PHP_EOL,
                            $e->getMessage()
                        );
                    }
                }
            }
        } else {
            $user_links = array();
        }

        SitemapConfig::setDefaultSettings($default_settings);

        Db::getInstance()->delete(
            'sitemap_category',
            ' id_shop = '.(int)Shop::getContextShopID()
        );
        if (is_array($categories) && count($categories)) {
            $data = array();
            foreach ($categories as $category) {
                $data[] = array(
                    'id_category' => (int)$category,
                    'id_shop' => (int)Shop::getContextShopID()
                );
            }
            Db::getInstance()->insert('sitemap_category', $data);
        }

        if (is_array($excluded_products) && count($excluded_products)) {
            Configuration::updateValue(
                'SEOSA_SITEMAP_EXCLUDED_PRODUCTS',
                json_encode($excluded_products)
            );
        } else {
            Configuration::deleteByName('SEOSA_SITEMAP_EXCLUDED_PRODUCTS');
        }

        die(json_encode(array(
            'hasError' => (count($this->errors) ? true : false),
            'message' => (count($this->errors) ? implode(PHP_EOL, $this->errors)
                : $this->l('Sitemap configuration save successfully!')),
            'user_links' => $user_links
        )));
    }

    public function ajaxProcessSetConfig()
    {
        $name = Tools::getValue('name');
        $value = Tools::getValue('value');

        if ($name == 'protocol') {
            if ($value == 1) {
                ConfSMP::setConf($name, 'HTTP');
            } else {
                ConfSMP::setConf($name, 'HTTPS');
            }
        } else {
            ConfSMP::setConf($name, $value);
        }

        die();
    }

    public static function getCMS($idLang = null, $limit = 25, $page = 1)
    {
        if ($idLang == null) {
            $idLang = Context::getcontext()->language->id;
        }
        $p = $page * $limit;
        if ($page > 1) {
            $n = $p - $limit;
        } else {
            $n = 0;
        }
        $sql = 'SELECT 
       c.`id_cms`, 
       c.`id_cms_category`, 
       c.`active`,
       l.`id_lang`, 
       l.`id_shop`, 
       l.`meta_title`, 
       l.`meta_description`, 
       l.`meta_keywords`, 
       l.`link_rewrite`
       FROM `' . _DB_PREFIX_ . 'cms` c
       INNER JOIN `' . _DB_PREFIX_ . 'cms_lang` l ON c.id_cms = l.id_cms AND l.id_lang = ' . $idLang . '
       WHERE c.active = 1 ORDER BY c.`id_cms` asc limit ' . $n . "," . $limit;

        return Db::getInstance()->executeS($sql);
    }

    public static function getCountCMS()
    {
        $sql = 'SELECT count(*) FROM `ps_cms` WHERE active = 1';
        return Db::getInstance()->getValue($sql);
    }

    public function ajaxProcessCmsPaginator()
    {
        $current = Tools::getValue('current');
        $length = Tools::getValue('length');
        $total = ceil(self::getCountCMS());
        $data = self::getCMS($idLang = null, $length, $current);
        if ($current > 0) {
            $start = $length * $current;
        } else {
            $start = 0;
        }
        $conf_cms = SitemapConfigCms::getConfig();
        $array_id = [];
        foreach ($conf_cms as $key => $row) {
            $array_name[$key] = $row['id_cms'];
        }

        array_multisort($array_name, SORT_ASC, $conf_cms);

        $conf_cms_select = $conf_cms;
        $index = 0;

        foreach ($data as $key => $item) {
            foreach ($conf_cms_select as $value) {
                if ($item['id_cms'] == $value['id_cms']) {
                    $data[$key]['priority'] = $value['priority'];
                    $data[$key]['changefreq'] = $value['changefreq'];
                    $data[$key]['is_export'] = $value['is_export'];
                    $data[$key]['meta_title'] = $value['meta_title'];
                    $insex = 1;
                    break;
                }
            }
            if ($insex == 0) {
                $data[$key]['priority'] = 0.5;
                $data[$key]['is_export'] = 1;
            }
            $insex = 0;
        }

        $this->context->smarty->assign(array(
            'data' => $data,
            'cong_cms' => $conf_cms,
            'changefreqs' => SitemapConfig::getChangeFreqs(),
            'priorities' => SitemapConfig::getPriorities()

        ));

        $content = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'sitemappro/views/templates/admin/site_map_pro/cms_pagination.tpl'
        );


        die(json_encode(array(
            'hasError' => false,
            'total' => $total,
            'length' => $length,
            'data' => $content
        )));
    }
}
