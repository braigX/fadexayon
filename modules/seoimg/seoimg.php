<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (defined('_PS_VERSION_') === false) {
    exit;
}

if (version_compare(phpversion(), '5.1.2', '<')) {
    if (!class_exists('TinyCache')) {
        require_once dirname(__FILE__) . '/classes/TinyCache.php';
    }
    if (!class_exists('Pattern')) {
        require_once dirname(__FILE__) . '/classes/Pattern.php';
    }
    if (!class_exists('SeoToolsImg')) {
        require_once dirname(__FILE__) . '/classes/SeoToolsImg.php';
    }
} else {
    require_once dirname(__FILE__) . '/autoload.php';
}

class SeoImg extends Module
{
    public static $rules_table = 'module_seoimg_rules';
    public static $objects_table = 'module_seoimg_objects';
    public static $patterns_table = 'module_seoimg_patterns';

    public $history = null;

    /**
     * @var string Admin Module template path
     *             (eg. '/home/prestashop/modules/module_name/views/templates/admin/')
     */
    protected $admin_tpl_path = null;

    /**
     * @var string Admin Module template path
     *             (eg. '/home/prestashop/modules/module_name/views/templates/hook/')
     */
    protected $hooks_tpl_path = null;

    /** @var string Module js path (eg. '/shop/modules/module_name/js/') */
    protected $js_path = null;

    /** @var string Module css path (eg. '/shop/modules/module_name/css/') */
    protected $css_path = null;

    /** @var string Module css path (eg. '/shop/modules/module_name/css/') */
    protected $sql_path = null;

    /** @var array cache filled with lang informations */
    protected static $lang_cache;

    /** @var array cache filled with lang informations */
    protected static $rc;
    protected static $static_products;

    /** @var string cache filled with informations */
    protected $cache_path;

    /** @var bool allow to increase memory size */
    public $increase_memory = true;

    public $front_url;

    /** SQL files */
    const INSTALL_SQL_FILE = 'install.sql';
    const UNINSTALL_SQL_FILE = 'uninstall.sql';

    /**
     * @var string
     */
    public $secure_key;

    /**
     * @var string
     */
    public $author_address;

    public function __construct()
    {
        $this->name = 'seoimg';
        $this->tab = 'seo';
        $this->version = '1.1.3';
        $this->author = 'PrestaShop';

        $this->need_instance = 0;

        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        $this->module_key = 'a4567cc9e09981ef423c8f73e1085b1b';
        $this->author_address = '0x64aa3c1e4034d07015f639b0e171b0d7b27d01aa';

        parent::__construct();

        $this->checkCloud();

        $this->displayName = $this->l('SEO Image');
        $this->description = $this->l('Increase your SEO and your visibility in search engines such as Google');

        $this->js_path = $this->_path . 'views/js/';
        $this->css_path = $this->_path . 'views/css/';
        $this->sql_path = dirname(__FILE__) . '/sql/';
        $this->cache_path = $this->local_path . 'cache/';

        $this->admin_tpl_path = $this->local_path . 'views/templates/admin/';
        $this->hooks_tpl_path = $this->local_path . 'views/templates/hook/';

        $this->front_url = SeoToolsImg::getFrontUrl();

        TinyCache::setPath($this->cache_path);
        $this->getLang();

        $this->history = [
            'product' => [],
            'category' => [],
            'cms' => [],
            'cmscategory' => [],
            'supplier' => [],
            'manufacturer' => [],
            'static' => [],
        ];

        if ($this->increase_memory === true) {
            @ignore_user_abort(true);
            @set_time_limit(0);
            @ini_set('memory_limit', '1024M');
        }
    }

    /**
     * Get Language
     */
    private function getLang()
    {
        self::$lang_cache = TinyCache::getCache('language_' . (int) $this->context->shop->id);
        $languages = Language::getLanguages(true, $this->context->shop->id);

        if (self::$lang_cache === null || empty(self::$lang_cache)) {
            foreach ($languages as &$row) {
                $exprow = explode(' (', $row['name']);
                $subtitle = (isset($exprow[1]) ? trim(Tools::substr($exprow[1], 0, -1)) : '');
                self::$lang_cache[$row['iso_code']] = [
                    'id' => (int) $row['id_lang'],
                    'title' => trim($exprow[0]),
                    'subtitle' => $subtitle,
                ];
            }
            // Cache Data
            TinyCache::setCache('language_' . (int) $this->context->shop->id, self::$lang_cache);
            // Clean memory
            unset($row, $exprow, $subtitle, $languages);
        }
    }

    /**
     * Install SQL
     *
     * @return bool
     */
    private function installSQL()
    {
        // Create database tables from install.sql
        if (!Tools::file_exists_cache($this->sql_path . self::INSTALL_SQL_FILE)) {
            return false;
        }

        if (!$sql = Tools::file_get_contents($this->sql_path . self::INSTALL_SQL_FILE)) {
            return false;
        }

        $replace = [
            'PREFIX' => _DB_PREFIX_,
            'ENGINE_DEFAULT' => _MYSQL_ENGINE_,
        ];
        $sql = strtr($sql, $replace);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as &$q) {
            if ($q && !Db::getInstance()->Execute(trim($q))) {
                return false;
            }
        }

        // Clean memory
        unset($sql, $q, $replace);

        return true;
    }

    /**
     * Uninstall SQL
     *
     * @return bool
     */
    private function uninstallSQL()
    {
        // Create database tables from uninstall.sql
        if (!Tools::file_exists_cache($this->sql_path . self::UNINSTALL_SQL_FILE)) {
            return false;
        }

        if (!$sql = Tools::file_get_contents($this->sql_path . self::UNINSTALL_SQL_FILE)) {
            return false;
        }

        $replace = [
            'PREFIX' => _DB_PREFIX_,
            'ENGINE_DEFAULT' => _MYSQL_ENGINE_,
        ];
        $sql = strtr($sql, $replace);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as &$q) {
            if ($q && !Db::getInstance()->Execute(trim($q))) {
                return false;
            }
        }
        // Clean memory
        unset($sql, $q, $replace);

        return true;
    }

    /**
     * Install Tab
     *
     * @return bool
     */
    private function installTab()
    {
        // Check hide host mode
        $this->checkCloud();

        $tab = new Tab();
        $tab->active = true;
        $tab->class_name = 'AdminSeoimg';
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'SEO (Search Engine Optimization)';
        }
        unset($lang);
        $tab->id_parent = -1;
        $tab->module = $this->name;

        return (bool) $tab->add();
    }

    /**
     * Uninstall Tab
     *
     * @return bool
     */
    private function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminSeoimg');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            if ($tab instanceof Tab) {
                return $tab->delete();
            } else { /* @phpstan-ignore-line */
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * Check MySQL Engine
     *
     * @return bool
     */
    public function isMyisam()
    {
        /* @phpstan-ignore-next-line */
        if (_MYSQL_ENGINE_ === 'MyISAM') {
            return true;
        }

        return false;
    }

    /**
     * Check if column exist in Tab
     *
     * @return void
     */
    public function checkCloud()
    {
        $status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(1)
		FROM information_schema.COLUMNS
		WHERE TABLE_SCHEMA = "' . pSQL(_DB_NAME_) . '"
		AND TABLE_NAME = "' . pSQL(_DB_PREFIX_) . 'tab"
		AND COLUMN_NAME = "hide_host_mode"');
        // Maybe a deficient Update: We add the column to avoid bugs
        if ((int) $status !== 1) {
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(
                'ALTER TABLE ' . pSQL(_DB_PREFIX_) . 'tab
                ADD hide_host_mode TINYINT(1) UNSIGNED NOT NULL DEFAULT "0" AFTER active'
            );
        }
    }

    /**
     * Insert module into datable
     *
     * @return bool result
     */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        // Clean up cache
        TinyCache::clearAllCache();

        if (parent::install() === false
            || $this->registerHook('actionObjectCategoryAddAfter') === false
            || $this->registerHook('actionObjectCategoryUpdateAfter') === false
            || $this->registerHook('actionObjectCategoryDeleteAfter') === false

            || $this->registerHook('actionObjectProductAddAfter') === false
            || $this->registerHook('actionObjectProductUpdateAfter') === false

            || $this->registerHook('actionObjectCMSCategoryAddAfter') === false
            || $this->registerHook('actionObjectCMSCategoryUpdateAfter') === false
            || $this->registerHook('actionObjectCMSCategoryDeleteAfter') === false

            || $this->registerHook('actionObjectLanguageAddAfter') === false
            || $this->registerHook('actionObjectLanguageUpdateAfter') === false
            || $this->registerHook('actionObjectLanguageDeleteAfter') === false

            || $this->registerHook('displayBackOfficeHeader') === false
//             || $this->registerHook('displayAdminProductsExtra') === false
            || $this->installSQL() === false
            || $this->installTab() === false) {
            return false;
        }

        $now = new \DateTime('now');
        \Configuration::updateValue('PS_SEO_IMG_DATE_INSTALL', $now->format('Y-m-d H:i:s'));

        return true;
    }

    /**
     * Delete module from datable
     *
     * @return bool result
     */
    public function uninstall()
    {
        if (parent::uninstall() === false
            || $this->uninstallSQL() === false
            || $this->uninstallTab() === false) {
            return false;
        }

        \Configuration::deleteByName('PS_SEO_IMG_DATE_INSTALL');

        return true;
    }

    /**
     * Loads asset resources
     */
    public function loadAsset()
    {
        $css_compatibility = $js_compatibility = [];

        // Load CSS
        $css = [
            $this->css_path . 'font-awesome.min.css',
            $this->css_path . 'bootstrap-select.min.css',
            $this->css_path . 'bootstrap-dialog.min.css',
            $this->css_path . 'bootstrap.vertical-tabs.min.css',
            $this->css_path . 'bootstrap-responsive.min.css',
            $this->css_path . 'DT_bootstrap.css',
            $this->css_path . 'jstree.min.css',
            $this->css_path . 'faq.css',
            $this->css_path . $this->name . '.css',
        ];
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $css_compatibility = [
                $this->css_path . 'bootstrap.min.css',
                $this->css_path . 'bootstrap.extend.css',
                $this->css_path . 'font-awesome.min.css',
            ];
            $css = array_merge($css_compatibility, $css);
        }
        $this->context->controller->addCSS($css, 'all');

        if (method_exists($this->context->controller, 'addJquery')) {
            $this->context->controller->addJquery();
        }

        // Load JS
        $jss = [
            $this->js_path . 'jquery-2.1.0.min.js',
            $this->js_path . 'jquery-migrate-1.2.1.min',
            $this->js_path . 'mynoConflict.js',
            $this->js_path . 'bootstrap-select.min.js',
            $this->js_path . 'bootstrap-dialog.js',
            $this->js_path . 'jquery.autosize.min.js',
            $this->js_path . 'jquery.dataTables.js',
            $this->js_path . 'jquery.smartWizard.js',
            $this->js_path . 'DT_bootstrap.js',
            $this->js_path . 'dynamic_table_init.js',
            $this->js_path . 'jstree.min.js',
            $this->js_path . 'faq.js',
            $this->js_path . $this->name . '.js',
        ];

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $js_compatibility = [
                $this->js_path . 'bootstrap.min.js',
            ];
            $jss = array_merge($jss, $js_compatibility);
        }
        $this->context->controller->addJS($jss);
        // Clean memory
        unset($jss, $css, $js_compatibility, $css_compatibility);
    }

    /**
     * Show the configuration module
     */
    public function getContent()
    {
        // We load asset
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            // Clean the code use tpl file for html
            $tab = '&tab_module=' . $this->tab;
            $token_mod = '&token=' . Tools::getAdminTokenLite('AdminModules');
            $token_pos = '&token=' . Tools::getAdminTokenLite('AdminModulesPositions');
            $token_trad = '&token=' . Tools::getAdminTokenLite('AdminTranslations');

            $mod_url = 'index.php?controller=AdminModules';

            $this->context->smarty->assign([
                'module_active' => (bool) $this->active,
                'module_trad' => 'index.php?controller=AdminTranslations' . $token_trad . '&type=modules&lang=',
                'module_hook' => 'index.php?controller=AdminModulesPositions' . $token_pos . '&show_modules=' . $this->id,
                'module_back' => $mod_url . $token_mod . $tab . '&module_name=' . $this->name,
                'module_form' => $mod_url . '&configure=' . $this->name . $token_mod . $tab . '&module_name=' . $this->name,
                'module_reset' => $mod_url . $token_mod . '&module_name=' . $this->name . '&reset' . $tab,
            ]);
            // Clean memory
            unset($tab, $token_mod, $token_pos, $token_trad);
        }

        $controller_name = 'AdminSeoimg';
        $current_id_tab = (int) $this->context->controller->id;
        $controller_url = $this->context->link->getAdminLink($controller_name);
        $token_seo = '&token=' . Tools::getAdminTokenLite('AdminMeta');

        /* Language for documentation in back-office */
        $iso_code = Context::getContext()->language->iso_code;

        switch ($iso_code) {
            case 'fr':
                $lang = 'FR';
                $white_seo = 'fr/livre-blanc-seo';
            break;
            case 'en':
            default:
                $lang = 'EN';
                $white_seo = 'en/white-paper-seo';
            break;
        }
        if ($iso_code == 'ca' || $iso_code == 'es' || $iso_code == 'gl') {
            $lang = 'ES';
        } elseif (empty($lang)) {
            $lang = 'EN';
        }

        // API FAQ Update
        include_once 'classes/APIFAQClass.php';
        $api = new APIFAQ();

        /** @var stdClass $api_json */
        $api_json = json_decode($api->getData($this));
        $apifaq_json_categories = [];
        if (!empty($api_json)) {
            $apifaq_json_categories = $api_json->categories;
        }
        $this->context->smarty->assign([
            'module_display_name' => $this->displayName,
            'apifaq' => $apifaq_json_categories,
        ]);

        $showRateModule = \DateTime::createFromFormat('Y-m-d H:i:s', \Configuration::get('PS_SEO_IMG_DATE_INSTALL'));
        $now = new \DateTime('now');
        $showRateModule = (int) $now->diff($showRateModule)->format('%a') > 7 && (int) $now->diff($showRateModule)->format('%a') < 92;

        $this->context->smarty->assign([
            'module_name' => $this->name,
            'module_version' => $this->version,
            'module_enabled' => (int) $this->active,
            'rule_history' => $this->history,
            'debug_mode' => (int) _PS_MODE_DEV_,
            'lang_select' => self::$lang_cache,
            'current_id_tab' => $current_id_tab,
            'controller_url' => $controller_url,
            'controller_name' => $controller_name,
            'module_display' => $this->displayName,
            'multishop' => (int) Shop::isFeatureActive(),
            'guide_link' => 'docs/seo_pro_guide_' . $lang . '.pdf',
            'white_seo' => $white_seo,
            'admin_seo' => 'index.php?controller=AdminMeta' . $token_seo,
            'table_tpl_path' => $this->admin_tpl_path . 'table/table.tpl',
            'actions_tpl_path' => $this->admin_tpl_path . 'table/actions.tpl',
            'ps_version' => (bool) version_compare(_PS_VERSION_, '1.6', '>'),
            'rewriting_allow' => (int) Configuration::get('PS_REWRITING_SETTINGS'),
            'showRateModule' => $showRateModule,
            'currentLangIsoCode' => $this->context->language->iso_code,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configuration.tpl');
    }

    /**
     * Switch the status of one rule
     */
    public function switchAction($id_rule)
    {
        $status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT SQL_SMALL_RESULT msr.active
            FROM `' . _DB_PREFIX_ . pSQL(self::$rules_table) . '` msr
            WHERE msr.id_rule = ' . (int) $id_rule
        );
        if ((int) $status === 1) {
            $data = ['active' => 0];
        } else {
            $data = ['active' => 1];
        }

        $data['id_rule'] = (int) $id_rule;

        return $this->update(pSQL(self::$rules_table), $data);
    }

    /**
     * Update the date of last apply of a rule
     */
    public function updateApply($id_rule)
    {
        $data = [
            'date_upd' => date('Y-m-d H:i:s'),
            'id_rule' => (int) $id_rule,
        ];

        return $this->update(pSQL(self::$rules_table), $data);
    }

    /**
     * Counts the number of object with respect to the previous query
     * See DataTables (http://goo.gl/C5ho60)
     *
     * @return int
     */
    public function countRules($type = 'product')
    {
        return (int) Db::getInstance()->getValue('SELECT SQL_SMALL_RESULT FOUND_ROWS() `' . trim(bqSQL($type)) . '`');
    }

    /**
     * Get Image
     *
     * @return array Lang
     */
    public function getTwitterImage($type)
    {
        $images = ImageType::getImagesTypes($type);
        if (!empty($images)) {
            $name = [];
            foreach ($images as $key => $image) {
                if ($images[$key]['width'] >= 120) {
                    $name[$image['name']] = $image['width'] . ' x ' . $image['height'];
                }
            }
            unset($key, $image, $images);

            return $name;
        }

        return [];
    }

    /**
     * Get all categories with childs
     *
     * @return array
     */
    public function getSimpleCategories($type)
    {
        if ($type === 'category') {
            // Remove root only if storeCommander is not installed
            $root = '';
            $storecommander = Module::getInstanceByName('storecommander');
            if (empty($storecommander)
              || !Tools::file_exists_cache(_PS_MODULE_DIR_ . 'storecommander/storecommander.php')) {
                $root = 'AND c.`id_category` != ' . (int) Configuration::get('PS_ROOT_CATEGORY');
            }

            $restult = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT c.*, cl.*
				FROM `' . _DB_PREFIX_ . 'category` c
				INNER JOIN ' . _DB_PREFIX_ . 'category_shop category_shop ON (
					category_shop.id_category = c.id_category AND category_shop.id_shop =  ' . (int) $this->context->shop->id . '
				)
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (
					c.`id_category` = cl.`id_category` AND cl.id_shop = 1
				)
				RIGHT JOIN `' . _DB_PREFIX_ . 'category` c2 ON (
					c2.`id_category` = ' . (int) Configuration::get('PS_ROOT_CATEGORY') . '
          AND c.`nleft` >= c2.`nleft`
          AND c.`nright` <= c2.`nright`
				)
				WHERE 1 AND `id_lang` = ' . (int) $this->context->language->id . '
			');

            if (empty($restult)) {
                $restult = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT SQL_BIG_RESULT c.`id_parent`, c.`id_category`, cl.`name`
				FROM `' . _DB_PREFIX_ . 'category` c
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (
          c.`id_category` = cl.`id_category`
          ' . Shop::addSqlRestrictionOnLang('cl') . '
        )
				' . Shop::addSqlAssociation('category', 'c') . '
				WHERE cl.`id_lang` = ' . (int) $this->context->language->id . '
				' . $root . '
				GROUP BY c.id_category
				ORDER BY c.`id_category`, category_shop.`position`');
            }

            return $restult;
        } else {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT SQL_BIG_RESULT c.`id_parent`, c.`id_cms_category` id_category , cl.`name`
			FROM `' . _DB_PREFIX_ . 'cms_category` c
			LEFT JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl ON (c.`id_cms_category` = cl.`id_cms_category`)
			WHERE cl.`id_lang` = ' . (int) $this->context->language->id . '
			GROUP BY c.id_cms_category
			ORDER BY c.`id_cms_category`');
        }
    }

    /**
     * Make tree with the categories
     *
     * @return array|false
     */
    public function getTree($type, $res_par, $ids, $max_depth, $id_cat = null, $cur_depth = 0)
    {
        if (is_null($id_cat)) {
            if ($type === 'cmscategory') {
                $id_cat = (int) Configuration::get('PS_ROOT_CATEGORY');
            } else {
                $id_cat = (int) $this->context->shop->getCategory();
            }
        }

        $children = [];
        if (isset($res_par[$id_cat]) && count($res_par[$id_cat]) && ($max_depth == 0 || $cur_depth < $max_depth)) {
            foreach ($res_par[$id_cat] as &$subcat) {
                $children[] = $this->getTree($type, $res_par, $ids, $max_depth, $subcat['id_category'], $cur_depth + 1);
            }
            unset($subcat);
        }

        if (!isset($ids[$id_cat])) {
            return false;
        }

        $return = [
            'id' => (int) $id_cat,
            'name' => $ids[$id_cat]['name'],
            'children' => $children,
        ];

        return $return;
    }

    /**
     * Get all objects of a rule
     *
     * @param int $id_rule
     *
     * @return array
     */
    public function getObjectsRule($id_rule)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT SQL_BIG_RESULT id_obj
		FROM `' . _DB_PREFIX_ . bqSQL(self::$objects_table) . '`
		WHERE `id_rule` = ' . (int) $id_rule);
    }

    /**
     * Get all objects of a rule
     *
     * @param int $id_rule
     *
     * @return array
     */
    public function getPatternsRule($id_rule)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT SQL_BIG_RESULT msr.id_rule, msr.id_lang, msr.id_shop, msr.active, msp.field, msp.pattern
		FROM  `' . _DB_PREFIX_ . bqSQL(self::$rules_table) . '` msr
		LEFT JOIN `' . _DB_PREFIX_ . bqSQL(self::$patterns_table) . '` msp ON ( msr.id_rule = msp.id_rule )
		WHERE msp.`id_rule` = ' . (int) $id_rule . '
		AND msp.field NOT LIKE "fb_%"
		AND msp.field NOT LIKE "tw_%"
		AND msr.active = 1');
    }

    /**
     * Get all objects of a rule
     *
     * @param int $id_rule
     *
     * @return false|string|null
     */
    public function getLangRule($id_rule)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT SQL_SMALL_RESULT msr.id_lang
		FROM `' . _DB_PREFIX_ . pSQL(self::$rules_table) . '` msr
		WHERE msr.id_rule = ' . (int) $id_rule . '
		AND msr.id_shop = ' . (int) $this->context->shop->id);
    }

    /**
     * Get all objects of a rule
     *
     * @param string $type
     * @param array|bool $default
     *
     * @return array
     *
     * @throws PrestaShopDatabaseException
     */
    public function getRules($type, $default = false)
    {
        $def = (is_array($default) ? (int) $default['id_category_default'] : (int) $default);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
    		SELECT SQL_BIG_RESULT msr.id_rule, msr.id_lang, msr.id_shop, msr.active, mso.id_obj, msp.field, msp.pattern
    		FROM `' . _DB_PREFIX_ . bqSQL(self::$rules_table) . '` msr
    		LEFT JOIN `' . _DB_PREFIX_ . bqSQL(self::$objects_table) . '` mso ON (msr.id_rule = mso.id_rule)
    		LEFT JOIN `' . _DB_PREFIX_ . bqSQL(self::$patterns_table) . '` msp ON (msr.id_rule = msp.id_rule)
    		WHERE msr.type = "' . pSQL($type) . '"
    		AND msr.active = 1
    		AND (msp.field NOT LIKE "fb_%" AND msp.field NOT LIKE "tw_%")
    		AND mso.id_obj  = ' . (($default === false) ? '0' : $def) . '
    		AND msr.id_shop = ' . (int) $this->context->shop->id);
    }

    /**
     * Get all rules already create
     *
     * @return array
     */
    public function getHistory($type = 'product', $role = 'meta', $filter = '', $order = '', $limit = '')
    {
        $calc = '';
        $numargs = func_num_args();
        if ($numargs > 1) {
            $calc = 'SQL_BIG_RESULT SQL_CALC_FOUND_ROWS';
        }

        $sql = 'SELECT ' . $calc . ' msr.id_rule, msr.name, msr.id_lang, l.name lang, s.name shop,
		IF (mso.id_obj>0,COUNT(id_obj),"All") nb_obj, msr.active, msr.date_upd
		FROM `' . _DB_PREFIX_ . bqSQL(self::$rules_table) . '` msr
		LEFT JOIN `' . _DB_PREFIX_ . bqSQL(self::$objects_table) . '` mso ON (msr.id_rule = mso.id_rule)
		LEFT JOIN ' . _DB_PREFIX_ . 'lang l ON (msr.id_lang = l.id_lang)
		LEFT JOIN ' . _DB_PREFIX_ . 'shop s ON (msr.id_shop = s.id_shop)
		WHERE msr.type = "' . pSQL($type) . '"
		AND msr.role = "' . pSQL($role) . '"
		AND msr.id_shop = "' . (int) $this->context->shop->id . '"
		' . $filter . '
		GROUP BY msr.id_rule
		' . (!empty($order) ? pSQL($order) : 'ORDER BY msr.id_rule ASC') . pSQL($limit);

        if (!empty($sql)) {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } else {
            return [];
        }
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon($type, $obj = null)
    {
        if ($type === 'flag') {
            $this->context->smarty->assign([
                'obj' => $obj,
                'lang_img' => _PS_IMG_ . '/l/' . $obj . '.jpg',
            ]);
        }
        $this->context->smarty->assign([
            'type' => $type,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/table/icons.tpl');
    }

    /**
     * Get the name of the rule
     *
     * @param int $id_rule
     *
     * @return string
     */
    public function getRuleName($id_rule)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT SQL_SMALL_RESULT name
			       FROM `' . _DB_PREFIX_ . bqSQL(self::$rules_table) . '`
             WHERE id_rule = ' . (int) $id_rule
        );
    }

    /**
     * Check if the rule is the default rule
     *
     * @param int $id_lang
     * @param int $id_rule
     *
     * @return bool
     */
    public function isDefaultRule($id_lang, $id_rule, $role, $type)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT SQL_SMALL_RESULT msr.id_rule
		FROM  ' . _DB_PREFIX_ . pSQL(self::$rules_table) . ' msr
		LEFT JOIN ' . _DB_PREFIX_ . pSQL(self::$objects_table) . ' mso ON (msr.id_rule = mso.id_rule)
		WHERE msr.id_lang = ' . (int) $id_lang . '
		AND msr.type = "' . pSQL($type) . '"
		AND msr.role = "' . pSQL($role) . '"
		' . (!empty($id_rule) ? ' AND msr.id_rule = ' . (int) $id_rule : '') . '
		AND id_obj = 0');
    }

    /**
     * Load the form template file
     *
     * @return string
     */
    public function loadForm($id_object, $role, $type = 'product')
    {
        $img_type = '';
        $id_object = (int) $id_object;
        if ($type == 'product' || $type == 'category') {
            if ($type == 'product') {
                $img_type = $type . 's';
            } else {
                $img_type = 'categories';
            }

            $cache_type = 'category';
        } elseif ($type == 'cms' || $type == 'cmscategory') {
            $cache_type = 'cmscategory';
        } else {
            $cache_type = false;
        }

        $category_select = false;
        if ($cache_type !== false) {
            $category_select = TinyCache::getCache($cache_type . '_' . (int) $this->context->shop->id);
            if (empty($category_select)) {
                if (!$result = $this->getSimpleCategories($cache_type)) {
                    return '';
                }

                $result_ids = [];
                $result_parents = [];
                foreach ($result as &$row) {
                    $result_parents[$row['id_parent']][] = &$row;
                    $result_ids[$row['id_category']] = &$row;
                }

                $category_select = $this->getTree($cache_type, $result_parents, $result_ids, 0);
                unset($result, $row, $result_parents, $result_ids);
                TinyCache::setCache($cache_type . '_' . (int) $this->context->shop->id, $category_select, 6);
            }
        }

        $default_category = 0;
        if ($id_object > 0) {
            $histories = $this->loadRuleDetails($id_object, $type, false);
            if (!empty($histories)) {
                foreach ($histories as &$history) {
                    $this->context->smarty->assign([
                        $history['field'] => $history['pattern'],
                    ]);
                }
                unset($histories, $history);
            }
            $default_category = $this->getObjectsRule($id_object);
        }

        $iso_code = Context::getContext()->language->iso_code;
        $lang = 'EN';
        if ($iso_code == 'fr' || $iso_code == 'FR') {
            $lang = 'FR';
        }

        $this->context->smarty->assign([
            'object' => $id_object,
            'lang_select' => self::$lang_cache,
            'blockCategTree' => $category_select,
            'default_category' => $default_category,
            'rule_name' => $this->getRuleName($id_object),
            'guide_link' => 'docs/seo_pro_guide_' . $lang . '.pdf',
            'rule_lang' => (int) $this->getLangRule($id_object),
            'default_lang' => (int) $this->context->language->id,
            'branche_tpl_path' => $this->admin_tpl_path . 'tree/category-tree-branch.tpl',
            'shop_name' => sprintf($this->l('You are on the %s shop'), $this->context->shop->name),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/forms/forms_legend.tpl');
    }

    /**
     * Load all objects of a rule in details
     *
     * @param int $id_obj
     * @param string $type
     * @param bool $smarty
     *
     * @return array|string|false
     */
    public function loadRuleDetails($id_obj, $type = 'product', $smarty = true)
    {
        $sql = 'SELECT SQL_BIG_RESULT msp.field, msp.pattern
		FROM `' . _DB_PREFIX_ . bqSQL(self::$rules_table) . '` msr
		LEFT JOIN `' . _DB_PREFIX_ . bqSQL(self::$patterns_table) . '` msp ON (msr.id_rule = msp.id_rule)
		WHERE msr.type = "' . pSQL($type) . '"
		AND msr.`id_rule` = ' . (int) $id_obj;
        $history = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (!empty($history)) {
            if ($smarty === false) {
                return $history;
            } else {
                $this->context->smarty->assign([
                    'history' => $history,
                ]);

                return $this->display(__FILE__, 'views/templates/admin/history/history.tpl');
            }
        }

        return false;
    }

    /**
     * Load the status of a rule with an icon
     *
     * @param int $status
     *
     * @return string
     */
    public function loadStatus($status)
    {
        $this->context->smarty->assign([
            'status' => $status,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/table/status.tpl');
    }

    /**
     * Load action buttons that apply, modify or delete the rule
     *
     * @param array $actions
     * @param string $type
     * @param string $role
     *
     * @return string
     */
    public function loadActions($actions, $type, $role)
    {
        $type = array_keys($this->history);
        $count_rule = (int) array_search($type, $this->history) + 1;

        $this->context->smarty->assign([
            'role' => $role,
            'type' => $type,
            'prod' => $actions,
            'count_rule' => $count_rule,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/table/actions.tpl');
    }

    /*			 CRUD 			*/

    /**
     * Save current object to database (add or update)
     *
     * @param string $table
     * @param array $data
     */
    public function saveObj($table, $data)
    {
        $this->save($table, $data);
    }

    /**
     * Save current object to database
     *
     * @param string $table
     * @param array $data
     *
     * @return int|string|false Insertion result
     */
    public function save($table, $data)
    {
        $keys = array_keys($data);
        $keys = array_map('bqSQL', $keys);
        $vals = array_values($data);
        $vals = array_map('pSQL', $vals);

        $sql = 'INSERT INTO `' . _DB_PREFIX_ . bqSQL($table) . '`';
        $counter_meta = 0;
        if ($counter_meta === 0) {
            $sql .= ' (`' . implode('`, `', $keys) . '`) VALUES';
        }
        $sql .= " ('" . implode("', '", array_values($vals)) . "'),";
        ++$counter_meta;
        $sql = rtrim($sql, ',') . ';';

        unset($data, $keys, $vals);
        if (Db::getInstance()->execute($sql)) {
            return Db::getInstance()->Insert_ID();
        }

        return false;
    }

    /**
     * Update current object to database
     *
     * @param string $table
     * @param array $data
     *
     * @return bool Insertion result
     */
    public function update($table, $data)
    {
        $set = $where = '';
        $sql = 'UPDATE `' . _DB_PREFIX_ . bqSQL($table) . '` SET ';
        $counter_meta = 0;

        foreach ($data as $key => $value) {
            if ($key === 'pattern') {
                $set = '`' . bqSQL($key) . '` = "' . pSQL($value) . '"';
            } elseif ($key === 'date_upd' || $key === 'name') {
                $counter_meta = -1;
                $set = '`' . bqSQL($key) . '` = "' . pSQL($value) . '"';
            } elseif ($key === 'active') {
                $counter_meta = -1;
                $set = '`' . bqSQL($key) . '` = ' . (int) $value;
            } elseif ($key !== 'field') {
                if ($counter_meta === 0) {
                    $where .= ' WHERE `' . bqSQL($key) . '` = ' . (int) $value;
                } else {
                    $where .= ' AND `' . bqSQL($key) . '` = ' . (int) $value;
                }
            } else {
                $where .= ' AND `' . bqSQL($key) . '` = "' . pSQL($value) . '"';
            }
            ++$counter_meta;
        }

        unset($key, $value, $data);

        return Db::getInstance()->execute($sql . $set . $where);
    }

    /**
     * Delete current object from database
     *
     * @param int $id_object
     * @param string $table
     *
     * @return bool Insertion result
     */
    public function delete($id_object, $table = '')
    {
        $quick = '';
        $result = 0;
        if ($this->isMyisam() === true) {
            $quick = 'QUICK';
        }

        if (!empty($table)) {
            $tables = ['`' . _DB_PREFIX_ . bqSQL($table) . '`'];
        } else {
            $tables = [
                '`' . _DB_PREFIX_ . bqSQL(self::$rules_table) . '`',
                '`' . _DB_PREFIX_ . bqSQL(self::$rules_table) . '`',
                '`' . _DB_PREFIX_ . bqSQL(self::$objects_table) . '`',
            ];
        }

        foreach ($tables as &$table) {
            $sql = 'DELETE ' . bqSQL($quick) . ' FROM ' . $table . ' WHERE id_rule = ' . (int) $id_object;
            $result += Db::getInstance()->execute($sql);
        }
        unset($tables, $table);

        return (bool) $result;
    }

    /**
     * Delete current object from database
     *
     * @param int $id_product
     * @param array $rules
     * @param int|false $id_shop
     * @param int|false $id_lang
     */
    public function generate($id_product, $rules, $id_shop = false, $id_lang = false)
    {
        if (!empty($rules)) {
            $type = gettype($id_product);

            if ($type === 'object') {
                $product = $id_product;
            } else {
                if ($id_shop !== false && Shop::isFeatureActive()) {
                    Shop::setContext(Shop::CONTEXT_SHOP, (int) $id_shop);
                }
                $product = new Product($id_product, false, null, $id_shop);
            }

            if ($product instanceof Product) {
                $field = '';
                foreach ($rules as $keys => &$value) {
                    foreach ($value as $pkey => &$pval) {
                        $pattern = trim(pSQL(Pattern::compilePattern($product, $pval, $id_lang)));
                        $pattern = str_replace("\'", "'", $pattern);
                        $pattern = preg_replace("~\\\\+([\"\'\\x00\\\\])~", '$1', $pattern);
                        $pattern = html_entity_decode($pattern);
                        $string = SeoToolsImg::truncateString(strip_tags($pattern), 128);
                        $field .= '`' . bqSQL($pkey) . '` = "' . pSQL($string) . '", ';
                    }
                    unset($value, $pkey, $pval);
                }
                unset($rules, $keys, $value);

                $field = Tools::substr($field, 0, -2);

                $imgs_id = array_filter(array_unique(array_column($product->getImages((int) $id_lang), 'id_image')));

                if (!empty($imgs_id)) {
                    $update = 'UPDATE ' . _DB_PREFIX_ . 'image_lang SET ' . Tools::stripslashes($field) . '
                    WHERE id_image IN (' . implode(',', $imgs_id) . ')
                    AND id_lang = ' . (int) $id_lang;
                    if (!Db::getInstance()->execute($update)) {
                        return $this->l('An error occurred while updating the product');
                    }
                }
                unset($id_product, $product, $field, $update, $type, $rules);
            } else {
                return $this->l('An error occurred while updating the product');
            }
        }
    }

    /*		 HOOK Display 		*/

    public function hookDisplayBackOfficeHeader()
    {
        if (!$this->active) {
            return;
        }

        $module = pSQL(trim(Tools::getValue('configure')));
        $controller_name = pSQL(trim(Tools::getValue('controller')));

        if ($controller_name === 'AdminModules' && $module === $this->name) {
            $this->loadAsset();
        }
    }

    public function hookactionObjectProductAddAfter($params)
    {
        if (!$this->active) {
            return;
        }

        if (!empty($params['object'])) {
            $obj = $params['object'];
            $type = Tools::strtolower(get_class($obj));
            $rules = $this->getRules($type);
            $default_category = $obj->getDefaultCategory();
            $get_rules = $this->getRules($type, $default_category);
            $rules = SeoToolsImg::mergeRecursiveArray($rules, $get_rules);
            if (!empty($rules)) {
                foreach ($rules as $idlang => $rule) {
                    $id_lang = (int) $idlang;
                    foreach ($rule as $idshop => $patterns) {
                        $myrule = [];
                        $id_shop = (int) $idshop;
                        foreach ($patterns as $field => $pattern) {
                            $myrule[] = [$field => $pattern];
                        }
                        unset($field, $pattern);
                    }
                    if (!isset($myrule, $id_shop)) {
                        continue;
                    }
                    $this->generate($obj, $myrule, $id_shop, $id_lang);
                    unset($myrule, $id_shop, $id_lang);
                }
                unset($rules, $idlang, $rule, $default_category, $get_rules, $obj);
            }
        }
    }

    public function hookactionObjectProductUpdateAfter($params)
    {
        if (!$this->active) {
            return;
        }

        if (!empty($params['object'])) {
            $obj = $params['object'];
            $type = Tools::strtolower(get_class($obj));
            $rules = $this->getRules($type);
            $default_category = $obj->getDefaultCategory();
            $get_rules = $this->getRules($type, $default_category);
            $rules = SeoToolsImg::mergeRecursiveArray($rules, $get_rules);
            if (!empty($rules)) {
                foreach ($rules as $idlang => $rule) {
                    $id_lang = (int) $idlang;
                    foreach ($rule as $idshop => $patterns) {
                        $myrule = [];
                        $id_shop = (int) $idshop;
                        foreach ($patterns as $field => $pattern) {
                            $myrule[] = [$field => $pattern];
                        }
                        unset($field, $pattern);
                    }
                    if (!isset($myrule, $id_shop)) {
                        continue;
                    }
                    $this->generate($obj, $myrule, $id_shop, $id_lang);
                    unset($myrule, $id_shop, $id_lang);
                }
                unset($rules, $idlang, $rule, $default_category, $get_rules, $obj);
            }
        }
    }

    /*		 CLEAN cache 		*/

    public function hookactionObjectCategoryAddAfter($params)
    {
        if (!$this->active) {
            return;
        }

        $this->cleanerObj($params['object']);
    }

    public function hookactionObjectCategoryUpdateAfter($params)
    {
        if (!$this->active) {
            return;
        }

        $this->cleanerObj($params['object']);
    }

    public function hookactionObjectCategoryDeleteAfter($params)
    {
        if (!$this->active) {
            return;
        }
        $this->cleanerObj($params['object']);
    }

    public function hookactionObjectCMSCategoryAddAfter($params)
    {
        if (!$this->active) {
            return;
        }
        $this->cleanerObj($params['object']);
    }

    public function hookactionObjectCMSCategoryUpdateAfter($params)
    {
        if (!$this->active) {
            return;
        }
        $this->cleanerObj($params['object']);
    }

    public function hookactionObjectCMSCategoryDeleteAfter($params)
    {
        if (!$this->active) {
            return;
        }
        $this->cleanerObj($params['object']);
    }

    public function hookactionObjectLanguageAddAfter($params)
    {
        if (!$this->active) {
            return;
        }
        $this->cleanerObj($params['object']);
    }

    public function hookactionObjectLanguageUpdateAfter($params)
    {
        if (!$this->active) {
            return;
        }
        $this->cleanerObj($params['object']);
    }

    public function hookactionObjectLanguageDeleteAfter($params)
    {
        if (!$this->active) {
            return;
        }
        $this->cleanerObj($params['object']);
    }

    public function cleanerObj($obj)
    {
        TinyCache::clearCache(Tools::strtolower(get_class($obj)) . '_' . $this->context->shop->id);
    }
}
