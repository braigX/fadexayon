<?php
/**
 * pm_seointernallinking
 *
 * @author    Presta-Module.com <support@presta-module.com> - https://www.presta-module.com
 * @copyright Presta-Module 2021 - https://www.presta-module.com
 * @license   Commercial
 * @version   1.2.3
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
include_once(_PS_ROOT_DIR_.'/modules/pm_seointernallinking/models/ShopOverrided.php');
require_once(_PS_ROOT_DIR_.'/modules/pm_seointernallinking/models/seointernallinkingcoreclass.php');
require_once(_PS_ROOT_DIR_.'/modules/pm_seointernallinking/models/seointernallinkingexpressionclass.php');
require_once(_PS_ROOT_DIR_.'/modules/pm_seointernallinking/models/seointernallinkinggroupclass.php');
class pm_seointernallinking extends SeoInternalLinkingCoreClass
{
    public static $_module_prefix = 'SIL';
    protected $_css_js_to_load = array(
        'core',
        'jquerytiptip',
        'datatables',
        'jgrowl',
        'multiselect',
        'form',
        'chosen',
        'jquerytools',
    );
    private $_file_to_check = array();
    const INSTALL_SQL_FILE = 'sql/install.sql';
    const UPDATE_SQL_FILE = 'sql/update.sql';
    private $_group_concat_max_len = 1048576;
    protected $_copyright_link = array(
        'link'  => '',
        'img'   => '//www.presta-module.com/img/logo-module.JPG'
    );
    public $_exclude_headings = 0;
    public $_description_field = 'both_description';
    public $_default_datatables_length = 10;
    public $_preg_pattern_headings = '/(<h.*?>)(.*?)(<\/h.*?>)/siu';
    public $_max_affected_rows = 8000;
    public $_big_sql_results_memory_limit = '1024M';
    public $_crontab_max_execution_time = 360;
    public $_defaultConfiguration = array(
        'exclude_headings' => 0,
    );
    public function __construct()
    {
        $this->need_instance = 0;
        $this->name = 'pm_seointernallinking';
        $this->module_key = '39bb6143fcc710a105ee8f49cd644b0d';
        $this->version = '1.2.3';
        $this->author = 'Presta-Module';
        $this->tab = 'seo';
        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => '1.7.99.99');
        parent::__construct();
        $_exclude_headings = Configuration::get('PM_'.self::$_module_prefix.'_EXCLUDE_HEADINGS');
        if ($_exclude_headings === false) {
            Configuration::updateValue('PM_'.self::$_module_prefix.'_EXCLUDE_HEADINGS', $this->_exclude_headings);
        }
        $this->_exclude_headings = (int)Configuration::get('PM_'.self::$_module_prefix.'_EXCLUDE_HEADINGS');
        $_description_field = Configuration::get('PM_'.self::$_module_prefix.'_DESCRIPTION_FIELD');
        if ($_description_field === false) {
            Configuration::updateValue('PM_'.self::$_module_prefix.'_DESCRIPTION_FIELD', $this->_description_field);
        }
        $this->_description_field = Configuration::get('PM_'.self::$_module_prefix.'_DESCRIPTION_FIELD');
        if ($this->_onBackOffice()) {
            $this->displayName = $this->l('SEO Internal Linking');
            $this->description = $this->l('Improve your e-shop internal netlinking ! This module helps you to create a bunch of links on your products, cms and home page.');
            $_default_datatables_length = Configuration::get('PM_'.self::$_module_prefix.'_DEFAULT_DATATABLES_LENGTH');
            if ($_default_datatables_length === false) {
                Configuration::updateValue('PM_'.self::$_module_prefix.'_DEFAULT_DATATABLES_LENGTH', $this->_default_datatables_length);
            }
            $this->_default_datatables_length = (int)Configuration::get('PM_'.self::$_module_prefix.'_DEFAULT_DATATABLES_LENGTH');
            $doc_url_tab = array();
            $doc_url_tab['fr'] = '#/fr/seointernallinking/';
            $doc_url_tab['en'] = '#/en/seointernallinking/';
            $doc_url = $doc_url_tab['en'];
            if ($this->_iso_lang == 'fr') {
                $doc_url = $doc_url_tab['fr'];
            }
            $forum_url_tab = array();
            $forum_url_tab['fr'] = 'http://www.prestashop.com/forums/topic/163336-modulepm-seo-internal-linking-generateur-de-maillage-interne/';
            $forum_url_tab['en'] = 'http://www.prestashop.com/forums/topic/163337-modulepm-seo-internal-linking-aim-for-the-top-ranking/';
            $forum_url = $forum_url_tab['en'];
            if ($this->_iso_lang == 'fr') {
                $forum_url = $forum_url_tab['fr'];
            }
            $this->_support_link = array(
                array('link' => $forum_url, 'target' => '_blank', 'label' => $this->l('Forum topic')),
                
                array('link' => 'https://addons.prestashop.com/contact-form.php?id_product=4982', 'target' => '_blank', 'label' => $this->l('Support contact')),
            );
        }
    }
    public function install()
    {
        if (!parent::install()
        || !$this->installDb()
        || !$this->registerHook('actionProductSave')
        || !$this->registerHook('categoryUpdate')
        || !$this->registerHook('actionObjectManufacturerAddAfter')
        || !$this->registerHook('actionObjectManufacturerUpdateAfter')
        || !Configuration::updateValue('PM_'.self::$_module_prefix.'_DESCRIPTION_FIELD', 'both_description')
        || !Configuration::updateValue('PM_'.self::$_module_prefix.'_DEFAULT_DATATABLES_LENGTH', 10)
        || !Configuration::updateValue('PM_'.self::$_module_prefix.'_EXCLUDE_HEADINGS', 0)
        || !Configuration::updateValue('PM_'.self::$_module_prefix.'_CRON_SECURE_KEY', Tools::passwdGen(16))
        || !Configuration::updateValue('PM_'.self::$_module_prefix.'_CRON_LAST_RUN', 0)
        ) {
            return false;
        }
        $this->checkIfModuleIsUpdate(true, false);
        return true;
    }
    public function checkIfModuleIsUpdate($updateDb = false, $displayConfirm = true)
    {
        parent::checkIfModuleIsUpdate($updateDb, $displayConfirm);
        if (!$this->isRegisteredInHook('actionObjectManufacturerAddAfter')) {
            $this->registerHook('actionObjectManufacturerAddAfter');
        }
        if (!$this->isRegisteredInHook('actionObjectManufacturerUpdateAfter')) {
            $this->registerHook('actionObjectManufacturerUpdateAfter');
        }
        $isUpdate = true;
        if (!$updateDb && $this->version != Configuration::get('PM_'.self::$_module_prefix.'_LAST_VERSION', false)) {
            return false;
        }
        if ($updateDb) {
            unset($_GET ['makeUpdate']);
            Configuration::updateValue('PM_'.self::$_module_prefix.'_LAST_VERSION', $this->version);
            $this->installDb();
            $this->updateDb();
            if ($isUpdate && $displayConfirm) {
                $this->_html .= $this->displayConfirmation($this->l('Module updated successfully'));
            } else {
                $this->_html .= $this->displayError($this->l('Module update fail'));
            }
        }
        return $isUpdate;
    }
    public function installDB()
    {
        if (!file_exists(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE)) {
            return (false);
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE)) {
            return (false);
        }
        $sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
        $sql = str_replace('MYSQL_ENGINE', _MYSQL_ENGINE_, $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $query) {
            if (! Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute(trim($query))) {
                return false;
            }
        }
        return true;
    }
    public function updateDb()
    {
        if (!file_exists(dirname(__FILE__) . '/' . self::UPDATE_SQL_FILE)) {
            return (false);
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__) . '/' . self::UPDATE_SQL_FILE)) {
            return (false);
        }
        $sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
        $sql = str_replace('MYSQL_ENGINE', _MYSQL_ENGINE_, $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $query) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute(trim($query))) {
                return false;
            }
        }
        return true;
    }
    public static function headingsDoNotTouchStart($matches)
    {
        $hc = self::getDataSerialized($matches[2]);
        $heading_content = '';
        for ($i=0; $i < Tools::strlen($hc); $i++) {
            $heading_content .= $hc[$i] . '<!---->';
        }
        return $matches[1] . $heading_content . $matches[3];
    }
    public static function headingsDoNotTouchEnd($matches)
    {
        return $matches[1].Tools::stripslashes(str_replace('<!---->', '', self::getDataUnserialized($matches[2]))) . $matches[3];
    }
    public function runCrontab($type = 'products')
    {
        ini_set('max_execution_time', $this->_crontab_max_execution_time);
        switch ($type) {
            case 'products':
            default:
                $word_combinaisons = $this->getWordsToLink(null, 0, 0, false, true);
                $this->cleanAllHTMLField('products', true);
                Configuration::updateValue('PM_'.self::$_module_prefix.'_CRON_LAST_RUN', time());
                if (self::_isFilledArray($word_combinaisons)) {
                    $this->updateHTMLField('products', $word_combinaisons, true, true);
                    return true;
                }
            case 'cms':
                $word_combinaisons = $this->getCMSWordsToLink(0, 0, false, true);
                $this->cleanAllHTMLField('cms', true);
                Configuration::updateValue('PM_'.self::$_module_prefix.'_CRON_LAST_RUN', time());
                if (self::_isFilledArray($word_combinaisons)) {
                    $this->updateHTMLField('cms', $word_combinaisons, true, true);
                    return true;
                }
            case 'categories':
                $word_combinaisons = $this->getCategoriesWordsToLink(null, 0, 0, false, true);
                $this->cleanAllHTMLField('categories', true);
                Configuration::updateValue('PM_'.self::$_module_prefix.'_CRON_LAST_RUN', time());
                if (self::_isFilledArray($word_combinaisons)) {
                    $this->updateHTMLField('categories', $word_combinaisons, true, true);
                    return true;
                }
            case 'manufacturers':
                $word_combinaisons = $this->getManufacturersWordsToLink(null, 0, 0, false, true);
                $this->cleanAllHTMLField('manufacturers', true);
                Configuration::updateValue('PM_'.self::$_module_prefix.'_CRON_LAST_RUN', time());
                if (self::_isFilledArray($word_combinaisons)) {
                    $this->updateHTMLField('manufacturers', $word_combinaisons, true, true);
                    return true;
                }
            case 'editorial':
                $this->updateEditorialContent(true);
                Configuration::updateValue('PM_'.self::$_module_prefix.'_CRON_LAST_RUN', time());
                return true;
        }
        return false;
    }
    private function addLinkToWord($matches)
    {
        $word_array = $this->tmpCurrentWord;
        $word = $word_array['expression_content'];
        $url = $word_array['associated_url'];
        $url_title = htmlentities($word_array['url_title'], ENT_COMPAT, 'UTF-8');
        $nofollow = (bool)($word_array['nofollow']);
        $new_window = (bool)($word_array['new_window']);
        $link_position = (int)($word_array['link_position']);
        $updated_description = $this->updateLinks(
            $matches[2],
            '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))\b('.preg_quote($word).')\b/imu',
            '<a href="'.$url.'" class="pmsil"'. (($nofollow) ? ' rel="nofollow"' : '') . (($url_title != '') ? ' title="'.$url_title.'"' : '') . (($new_window) ? ' target="_blank"' : '') .'>$1</a>',
            $word,
            $link_position
        );
        if ($updated_description != false) {
            return $matches[1] . $updated_description . $matches[3];
        }
        return $matches[1] . $matches[2] . $matches[3];
    }
    private function updateEditorialContent($from_crontab = false)
    {
        if (!Module::isInstalled('editorial') && !Module::isInstalled('ps_customtext')) {
            return 2;
        }
        $words = $this->getEditorialWordsToLink($from_crontab);
        $this->cleanAllHTMLField('editorial', $from_crontab);
        $this->updateHTMLField('editorial', $words, true, $from_crontab);
        if (method_exists($this, '_clearCache')) {
            $this->_clearCache('editorial.tpl', 'editorial');
        }
        return 1;
    }
    private function cleanEditorialContent()
    {
        $this->cleanAllHTMLField('editorial');
        return true;
    }
    public function getGroupCombinaisonInformations($group_obj)
    {
        $groupExplain = "";
        if ($group_obj->group_type == 1) {
            $nbCategories = sizeof($group_obj->getCategories());
            $exclusionCategories = $group_obj->category_type;
            $nbProduits = sizeof($group_obj->getProducts());
            $exclusionProduits = $group_obj->product_type;
            $nbFabricants = sizeof($group_obj->getManufacturers());
            $exclusionFabricants = $group_obj->manufacturer_type;
            $nbFournisseurs = sizeof($group_obj->getSuppliers());
            $exclusionFournisseurs = $group_obj->supplier_type;
            if ($nbCategories == 0 && $nbProduits == 0 && $nbFabricants == 0 && $nbFournisseurs == 0) {
                return '';
            }
            if ($nbProduits == 0) {
                $groupExplain .= $this->l('Your products selection will be part').' ';
            }
            if ($nbProduits > 0 && $exclusionProduits == 1) {
                $groupExplain .= $this->l('Your selection will contain all products excepted').' '. $nbProduits .' '.$this->l('of them').', ';
            }
            if ($nbProduits > 0 && $exclusionProduits == 0) {
                $groupExplain .= $this->l('Your selection will contain').' '. $nbProduits .' '.$this->l('product(s)').' ';
            }
            if ($nbProduits > 0) {
                $groupExplain .= $this->l('in').' ';
            }
            if ($nbProduits == 0) {
                $groupExplain .= $this->l('of').' ';
            }
            if ($nbCategories == 0) {
                $groupExplain .= $this->l('all the categories of your website');
            }
            if ($nbCategories > 0 && $exclusionCategories == 1) {
                $groupExplain .= $this->l('all the categories of your website excepted').' '. $nbCategories .' '.$this->l('of them');
            }
            if ($nbCategories > 0 && $exclusionCategories == 0) {
                $groupExplain .= $nbCategories . ' ' . $this->l('categorie(s)');
            }
            if ($nbFabricants == 0) {
                $groupExplain .= ', '.$this->l('of every manufacturer');
            }
            if ($nbFabricants > 0 && $exclusionFabricants == 1) {
                $groupExplain .= ', '.$this->l('of every manufacturer excepted').' '. $nbFabricants .' '.$this->l('of them');
            }
            if ($nbFabricants > 0 && $exclusionFabricants == 0) {
                $groupExplain .= ', '.$this->l('of').' '. $nbFabricants .' '.$this->l('manufacturer(s)');
            }
            if ($nbFournisseurs == 0) {
                $groupExplain .= ', '.$this->l('of every supplier');
            }
            if ($nbFournisseurs > 0 && $exclusionFournisseurs == 1) {
                $groupExplain .= ', '.$this->l('of every supplier excepted').' '. $nbFournisseurs .' '.$this->l('of them');
            }
            if ($nbFournisseurs > 0 && $exclusionFournisseurs == 0) {
                $groupExplain .= ', '.$this->l('of').' '. $nbFournisseurs .' '.$this->l('supplier(s)');
            }
        } elseif ($group_obj->group_type == 2) {
            $nbPagesCMS = sizeof($group_obj->getCMSPages());
            $exclusionPagesCMS = $group_obj->cms_type;
            if ($nbPagesCMS == 0) {
                $groupExplain .= $this->l('You have to choose at least one CMS page');
            }
            if ($nbPagesCMS > 0 && $exclusionPagesCMS == 1) {
                $groupExplain .= ''.$this->l('Your selection will contain all the CMS pages excepted').' '. $nbPagesCMS .' '.$this->l('of them').', ';
            }
            if ($nbPagesCMS > 0 && $exclusionPagesCMS == 0) {
                $groupExplain .= ''.$this->l('Your selection will contain').' '. $nbPagesCMS .' '.$this->l('CMS page(s)');
            }
        } elseif ($group_obj->group_type == 4) {
            $nbCategories = sizeof($group_obj->getCategories());
            $exclusionCategories = $group_obj->category_type;
            $groupExplain = $this->l('Your selection will contain') . ' ';
            if ($nbCategories > 0 && $exclusionCategories == 1) {
                $groupExplain .= $this->l('all the categories of your website excepted').' '. $nbCategories .' '.$this->l('of them');
            }
            if ($nbCategories > 0 && $exclusionCategories == 0) {
                $groupExplain .= $nbCategories . ' ' . $this->l('categorie(s)');
            }
        } elseif ($group_obj->group_type == 5) {
            $nbManufacturers = sizeof($group_obj->getManufacturers());
            $exclusionManufacturers = $group_obj->manufacturer_type;
            $groupExplain = $this->l('Your selection will contain') . ' ';
            if ($nbManufacturers > 0 && $exclusionManufacturers == 1) {
                $groupExplain .= $this->l('all the manufacturers of your website excepted').' '. $nbManufacturers .' '.$this->l('of them');
            }
            if ($nbManufacturers > 0 && $exclusionManufacturers == 0) {
                $groupExplain .= $nbManufacturers . ' ' . $this->l('manufacturer(s)');
            }
        }
        return $groupExplain;
    }
    private function getLanguagesForSelectOptions()
    {
        $options = array();
        foreach (Language::getLanguages(false) as $language) {
            $options[$language['id_lang']] = $language['name'];
        }
        return $options;
    }
    private function getExpressions($ids_shop = array())
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT psil.*, psilgl.name as group_name
            FROM `'._DB_PREFIX_.'pm_seointernallinking` psil
            LEFT JOIN `'._DB_PREFIX_.'pm_seointernallinking_group` psilg ON (psil.id_group=psilg.id_group)
            LEFT JOIN `'._DB_PREFIX_.'pm_seointernallinking_group_lang` psilgl ON (psil.id_group=psilgl.id_group AND psilgl.id_lang='.(int)$this->_default_language.')'
            .self::addSqlAssociation('pm_seointernallinking_group', 'psilg', 'id_group')
            .self::addSqlGroupBy('psil', 'id_expression').
            'ORDER BY `id_expression` ASC
        ');
        return $result;
    }
    private function getGroups($id_group = null, $id_lang = null)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT sgl.*, sg.*
            FROM `'._DB_PREFIX_.'pm_seointernallinking_group` sg
            LEFT JOIN `'._DB_PREFIX_.'pm_seointernallinking_group_lang` sgl ON (sg.id_group=sgl.id_group '.(($id_lang && is_numeric($id_lang) && $id_lang > 0) ? 'AND sgl.id_lang="'.pSQL($id_lang).'"' : '').')
            '.self::addSqlAssociation('pm_seointernallinking_group', 'sg', 'id_group').'
            ' .(($id_group && is_numeric($id_group) && $id_group > 0) ? 'WHERE sg.id_group="'.pSQL($id_group).'"' : '')
            .self::addSqlGroupBy('sg', 'id_group') .
            'ORDER BY sg.id_group ASC
        ');
        return $result;
    }
    private function getLinkPositionForSelectOptions()
    {
        $options = array(
            1 => $this->l('Top'),
            2 => $this->l('Middle'),
            3 => $this->l('Bottom')
        );
        return $options;
    }
    private function getGroupsForSelectOptions()
    {
        $options = array();
        foreach ($this->getGroups(null, $this->_default_language) as $group) {
            $options[$group['id_group']] = $group['name'];
        }
        return $options;
    }
    private function getGroupsType()
    {
        $options = array(
            1 => $this->l('Products'),
            2 => $this->l('CMS'),
            3 => $this->l('Editorial Module'),
            4 => $this->l('Categories'),
        );
        $options[5] = $this->l('Manufacturers');
        return $options;
    }
    public function renderFormAddExpression($obj)
    {
        $this->_cleanOutput();
        $updateForm = false;
        if ($obj && isset($obj->id_expression)) {
            $updateForm = true;
        }
        $vars = array(
            'obj' => $obj,
            'updateForm' => (bool)$updateForm,
            'isDuplication' => Tools::getValue('duplicate') && Tools::getValue('id_expression'),
            'options' => array(
                'linkPositionForSelectOptions' => $this->getLinkPositionForSelectOptions(),
                'groupsForSelectOptions' => $this->getGroupsForSelectOptions(),
                'languagesForSelectOptions' => $this->getLanguagesForSelectOptions(),
            ),
            'default_language' => $this->_default_language,
        );
        $this->_html .= $this->fetchTemplate('module/forms/add_expression.tpl', $vars);
        return true;
    }
    public function renderFormAddGroup($obj)
    {
        $this->_cleanOutput();
        $updateForm = false;
        if ($obj && isset($obj->id_group)) {
            $updateForm = true;
        }
        $pm_htmloncategories_warning = false;
        $pm_htmloncategories = Module::getInstanceByName('pm_htmloncategories');
        if (is_object($pm_htmloncategories) && isset($pm_htmloncategories->active) && $pm_htmloncategories->active != true && version_compare($pm_htmloncategories->version, '1.1.0', '>=')) {
            $pm_htmloncategories_warning = true;
        }
        $vars = array(
            'obj' => $obj,
            'groupsType' => $this->getGroupsType(),
            'groupCategories' => ((bool)$updateForm ? $obj->getCategories() : array()),
            'root_category_id' => (int)Category::getRootCategory()->id,
            'updateForm' => (bool)$updateForm,
            'selectedoptions' => array(
                'products' => ($updateForm ? $obj->getProducts() : array()),
                'manufacturers' => ($updateForm ? $obj->getManufacturers() : array()),
                'suppliers' => ($updateForm ? $obj->getSuppliers() : array()),
                'cms_pages' => ($updateForm ? $obj->getCMSPages() : array()),
            ),
            'pm_htmloncategories_warning' => (bool)$pm_htmloncategories_warning,
            'showShopContextWarning' => $this->showShopContextWarning(),
        );
        $this->_html .= $this->fetchTemplate('module/forms/add_group.tpl', $vars);
        return true;
    }
    public function getContent()
    {
        if (Tools::getValue('makeUpdate')) {
            $this->checkIfModuleIsUpdate(true);
        }
        $moduleIsUpToDate = $this->checkIfModuleIsUpdate(false);
        $permissionsErrors = $this->_checkPermissions();
        if (!sizeof($permissionsErrors)) {
            if ($moduleIsUpToDate) {
                $this->_preProcess();
                $this->_postProcess();
            }
        }
        $config = $this->_getModuleConfiguration();
        $vars = array(
            'module_configuration' => $config,
            'module_display_name' => $this->displayName,
            'module_is_up_to_date' => $moduleIsUpToDate,
            'permissions_errors' => $permissionsErrors,
            'css_js_assets' => $this->_loadCssJsLibraries(),
            'rating_invite' => $this->_showRating(true),
            'parent_content' => parent::getContent(),
        );
        if (!sizeof($permissionsErrors)) {
            if ($moduleIsUpToDate) {
                $vars['global_options_tab'] = $this->renderGlobalOptionsTab();
                $vars['list_groups_tab'] = $this->renderListGroupsTab();
                $vars['list_expressions_tab'] = $this->renderListExpressionsTab();
                $vars['optimization_tab'] = $this->renderOptimizationTab();
                $vars['delete_tab'] = $this->renderDeleteTab();
                $vars['cron_tab'] = $this->renderCronTab();
            }
        }
        return $this->fetchTemplate('module/content.tpl', $vars);
    }
    private function renderGlobalOptionsTab()
    {
        $config = $this->_getModuleConfiguration();
        $options = array(
            'description_field' => array(
                'description' =>  $this->l('Long description'),
                'description_short' =>  $this->l('Short description'),
                'both_description' =>  $this->l('Short & Long description'),
            ),
            'default_datatables_length' => array(
                10 => 10,
                25 => 25,
                50 => 50,
                100 => 100,
                150 => 150,
                200 => 200,
            ),
        );
        $vars = array(
            'obj' => $this,
            'config' => $config,
            'default_config' => $this->_defaultConfiguration,
            'options' => $options,
        );
        return $this->fetchTemplate('module/tabs/global_options.tpl', $vars);
    }
    private function renderListGroupsTab()
    {
        $config = $this->_getModuleConfiguration();
        $groups = array();
        $gettedGroups = $this->getGroups(null, $this->_default_language);
        if ($gettedGroups && sizeof($gettedGroups)) {
            foreach ($gettedGroups as $group) {
                $group_obj = new seointernallinkinggroupclass((int)$group['id_group']);
                $groups[] = array(
                    'id_group' => (int)$group['id_group'],
                    'name' => $group['name'],
                    'group_type' => $group['group_type'],
                    'groupCombinaisonInformations' => $this->getGroupCombinaisonInformations($group_obj),
                );
            }
        }
        $vars = array(
            'config' => $config,
            'default_config' => $this->_defaultConfiguration,
            'groups' => $groups,
            'group_type' => $this->getGroupsType(),
        );
        return $this->fetchTemplate('module/tabs/list_groups.tpl', $vars);
    }
    private function renderListExpressionsTab()
    {
        $config = $this->_getModuleConfiguration();
        $languages_cache = array();
        $expressions = $this->getExpressions();
        foreach ($expressions as $expression) {
            if (!isset($languages_cache[(int)$expression['id_lang']])) {
                $languages_cache[(int)$expression['id_lang']] = new Language((int)$expression['id_lang']);
            }
        }
        $vars = array(
            'config' => $config,
            'default_config' => $this->_defaultConfiguration,
            'groups' => $this->getGroups(null, $this->_default_language),
            'expressions' => $expressions,
            'languages_cache' => $languages_cache,
        );
        return $this->fetchTemplate('module/tabs/list_expressions.tpl', $vars);
    }
    private function renderOptimizationTab()
    {
        $config = $this->_getModuleConfiguration();
        $homePageManagementModuleName = '';
        $homePageManagementModuleInstalled = true;
        if (Module::isInstalled('editorial')) {
            $module = Module::getInstanceByName('editorial');
            if ($module instanceof Module) {
                $homePageManagementModuleName = $module->displayName;
            }
        } elseif (Module::isInstalled('ps_customtext')) {
            $module = Module::getInstanceByName('ps_customtext');
            if ($module instanceof Module) {
                $homePageManagementModuleName = $module->displayName;
            }
        } else {
            $homePageManagementModuleInstalled = false;
            if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                $homePageManagementModuleName = $this->l('Home text editor');
            } else {
                $homePageManagementModuleName = $this->l('Custom text blocks');
            }
        }
        $vars = array(
            'config' => $config,
            'default_config' => $this->_defaultConfiguration,
            'homePageManagementModuleInstalled' => $homePageManagementModuleInstalled,
            'homePageManagementModuleName' => $homePageManagementModuleName,
        );
        return $this->fetchTemplate('module/tabs/optimization.tpl', $vars);
    }
    private function renderDeleteTab()
    {
        $config = $this->_getModuleConfiguration();
        $vars = array(
            'config' => $config,
            'default_config' => $this->_defaultConfiguration,
        );
        return $this->fetchTemplate('module/tabs/delete.tpl', $vars);
    }
    private function renderCronTab()
    {
        $config = $this->_getModuleConfiguration();
        $vars = array(
            'config' => $config,
            'default_config' => $this->_defaultConfiguration,
            'cron_last_run' => ((Configuration::get('PM_'.self::$_module_prefix.'_CRON_LAST_RUN', '0') != '0') ? date('r', Configuration::get('PM_'.self::$_module_prefix.'_CRON_LAST_RUN')) : 'N/A'),
            'cron_url' => $this->context->link->getModuleLink($this->name, 'cron', array('secure_key' => Configuration::get('PM_'.self::$_module_prefix.'_CRON_SECURE_KEY'))),
        );
        return $this->fetchTemplate('module/tabs/cron.tpl', $vars);
    }
    protected function _preProcess()
    {
        parent::_preProcess();
        if (Tools::getValue('expressionFormValidation') == 1) {
            $this->_cleanOutput();
            $expression_content = trim(Tools::getValue('expression_content'));
            $id_lang = Tools::getValue('id_lang');
            $id_group = Tools::getValue('id_group');
            $id_expression = Tools::getValue('id_expression', 0);
            $count_total = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                SELECT COUNT(*) as nb
                FROM `' . _DB_PREFIX_ . 'pm_seointernallinking`
                WHERE expression_content LIKE "'.pSQL($expression_content).'"
                AND id_lang="'.pSQL($id_lang).'"
                AND id_group="'.pSQL($id_group).'"
                '.((isset($id_expression) && is_numeric($id_expression) && $id_expression > 0) ? 'AND id_expression!="'.pSQL($id_expression).'"' : '').'
                ');
            if ((int)($count_total['nb']) > 0) {
                $this->_html .= json_encode(array('expression_content' => $this->l('This expression already exists into this language & group.')));
            } else {
                $this->_html .= 'true';
            }
            $this->_echoOutput(true);
        }
        if (Tools::getValue('synchroniseEverything') == 1) {
            session_start();
            $_SESSION['synchronise_everything_process'] = true;
            $this->_cleanOutput();
            $this->_html .= '
                $("a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks").hide();
                $("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", true).addClass("ui-state-disabled");
                // Products
                $("#progressSyncProductInformation").hide();
                $("#progressSyncAllInformation").html($("#progressSyncProductInformation").html()).show();
                $("#progressSyncProductRemainingTime").show();
                $("#progressSyncProduct span em").css("left", 0);
                $("#progressSyncProduct").show();
                // CMS
                $("#progressSyncCMSPagesInformation").hide();
                $("#progressSyncCMSPagesRemainingTime").show();
                $("#progressSyncCMSPages span em").css("left", 0);
                $("#progressSyncCMSPages").show();
                // Catégories
                $("#progressSyncCategoriesInformation").hide();
                $("#progressSyncCategoriesRemainingTime").show();
                $("#progressSyncCategories span em").css("left", 0);
                $("#progressSyncCategories").show();
                // Fabricants
                $("#progressSyncManufacturersInformation").hide();
                $("#progressSyncManufacturersRemainingTime").show();
                $("#progressSyncManufacturers span em").css("left", 0);
                $("#progressSyncManufacturers").show();
                // Editorial
                $("#progressSyncEditorialInformation").hide();
                $("#progressSyncEditorial span em").css("left", 0);
                $("#progressSyncEditorial").show();
                $.ajax( {
                    type : "GET",
                    url : \'' . $this->_base_config_url . '&synchroniseAllProducts=1' . '\',
                    dataType : "script",
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        //alert(msgAjaxError);
                    }
                });
            ';
            $this->_echoOutput(true);
            return;
        }
        if (Tools::getValue('removeAllLinks') == 1) {
            session_start();
            $this->_cleanOutput();
            $this->cleanAllHTMLField('products');
            $this->cleanAllHTMLField('cms');
            $this->cleanAllHTMLField('categories');
            $this->cleanAllHTMLField('manufacturers');
            $this->cleanEditorialContent();
            $this->_html .= '
                show_info("'. addcslashes($this->l('All the links have been removed !'), '"') .'");
                $("#deleteAllContainer").addClass(\'taskDone\');
                $("#progressDeleteAllInformation").hide();
                $("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", false).removeClass("ui-state-disabled");
                $("a.removealllinks").show();
            ';
            session_destroy();
            $this->_echoOutput(true);
            return;
        }
        if (Tools::getValue('synchroniseEditorial') == 1) {
            $this->_cleanOutput();
            session_start();
            if (!isset($_SESSION['clean_html_field_editorial'])) {
                $this->cleanEditorialContent();
                $_SESSION['clean_html_field_editorial'] = true;
            }
            $update_editorial_result = $this->updateEditorialContent();
            if ($update_editorial_result == 1) {
                $this->_html .= 'show_info("'. addcslashes($this->l('Optimization done !'), '"') .'");';
            } elseif ($update_editorial_result == 2) {
                $this->_html .= 'show_info("'. addcslashes($this->l('Error while updating editorial module content. The module isn\'t installed.'), '"') .'");';
            } elseif ($update_editorial_result == false) {
                $this->_html .= 'show_error("'. addcslashes($this->l('Error while updating editorial module content.'), '"') .'");';
            }
            $this->_html .= '
                $("#syncEditorialContainer").addClass(\'taskDone\');
                $("#progressSyncEditorialInformation").hide();
                $("#progressSyncEditorial").hide();
                $("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", false).removeClass("ui-state-disabled");
                $("a.synchroniseeditorial").show();
            ';
            if (isset($_SESSION['synchronise_everything_process']) && $_SESSION['synchronise_everything_process'] == true) {
                if (isset($_SESSION['count_total_ajax'])) {
                    unset($_SESSION['count_total_ajax']);
                }
                if (isset($_SESSION['count_total_iteration_step'])) {
                    unset($_SESSION['count_total_iteration_step']);
                }
                $this->_html .= '
                $("a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks").show();
                $("#progressSyncAllInformation").html("").hide();
                ';
            }
            session_destroy();
            $this->_echoOutput(true);
            return;
        }
        if (Tools::getValue('synchroniseAllCMSPages') == 1) {
            $this->_cleanOutput();
            session_start();
            if (!isset($_SESSION['clean_html_field_cms_pages'])) {
                $this->cleanAllHTMLField('cms');
                $_SESSION['clean_html_field_cms_pages'] = true;
            }
            if (!isset($_SESSION['iteration_time_start'])) {
                $_SESSION['iteration_time_start'] = time();
            }
            if (!isset($_SESSION['count_total_ajax']) || !isset($_SESSION['count_total_iteration_step'])) {
                $_SESSION['count_total_ajax'] = $this->getCMSWordsToLink(0, 0, true);
                $_SESSION['count_total_iteration'] = 0;
                $_SESSION['count_total_iteration_step'] = (int)ceil($_SESSION['count_total_ajax']/2);
                if ($_SESSION['count_total_ajax'] > $this->_max_affected_rows) {
                    $_SESSION['count_total_iteration_step'] = (int)$this->_max_affected_rows;
                }
            } else {
                $_SESSION['count_total_iteration'] += $_SESSION['count_total_iteration_step'];
            }
            if ($_SESSION['count_total_ajax'] == 0) {
                $this->_html .= '
                    $("#progressSyncCMSPages").hide(1000, function() {
                        show_info("'. addcslashes($this->l('Optimization done (no rows affected) !'), '"') .'");
                        $("#syncCMSPagesContainer").addClass(\'taskDone\');
                        $("#progressSyncCMSPagesInformation").hide();
                        $("#progressSyncCMSPagesRemainingTime").html(\'\').hide();
                ';
                if (isset($_SESSION['synchronise_everything_process']) && $_SESSION['synchronise_everything_process'] == true) {
                    if (isset($_SESSION['count_total_ajax'])) {
                        unset($_SESSION['count_total_ajax']);
                    }
                    if (isset($_SESSION['count_total_iteration_step'])) {
                        unset($_SESSION['count_total_iteration_step']);
                    }
                    $this->_html .= '
                            $("#progressSyncCMSPages span em").css("left", 0);
                            $.ajax( {
                                type : "GET",
                                url : \'' . $this->_base_config_url . '&synchroniseAllCategories=1' . '\',
                                dataType : "script",
                                error: function(XMLHttpRequest, textStatus, errorThrown) {
                                    //alert(msgAjaxError);
                                }
                            });
                            $("#progressSyncAllInformation").html($("#progressSyncCategoriesInformation").html()).show();
                    ';
                } else {
                    $this->_html .= '
                                $("a.synchroniseallcmspages").show();
                                $("#progressSyncCMSPages span em").css("left", 0);
                                $("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", false).removeClass("ui-state-disabled");
                            ';
                    session_destroy();
                }
                $this->_html .= '});';
                $this->_echoOutput(true);
                return;
            }
            if (isset($_SESSION['count_total_ajax']) && isset($_SESSION['count_total_iteration']) && $_SESSION['count_total_iteration'] > 0 && $_SESSION['count_total_ajax'] > 0 && $_SESSION['count_total_iteration'] >= $_SESSION['count_total_ajax']) {
                $avancement_synchro = (($_SESSION['count_total_iteration'] * 100) / $_SESSION['count_total_ajax']);
                if ($avancement_synchro > 100) {
                    $avancement_synchro = 100;
                }
                $this->_html .= '
                    $("#progressSyncCMSPages span em").animate({left: "'.(int)$avancement_synchro.'%"}, 800, function() {
                        $("#progressSyncCMSPages").hide(2000, function() {
                            show_info("'. addcslashes($this->l('Optimization done !'), '"') .'");
                            $("#syncCMSPagesContainer").addClass(\'taskDone\');
                            $("#progressSyncCMSPagesInformation").hide();
                            $("#progressSyncCMSPagesRemainingTime").html(\'\').hide();
                ';
                if (isset($_SESSION['synchronise_everything_process']) && $_SESSION['synchronise_everything_process'] == true) {
                    if (isset($_SESSION['count_total_ajax'])) {
                        unset($_SESSION['count_total_ajax']);
                    }
                    if (isset($_SESSION['count_total_iteration_step'])) {
                        unset($_SESSION['count_total_iteration_step']);
                    }
                    $this->_html .= '
                            $("#progressSyncCMSPages span em").css("left", 0);
                            $.ajax( {
                                type : "GET",
                                url : \'' . $this->_base_config_url . '&synchroniseAllCategories=1' . '\',
                                dataType : "script",
                                error: function(XMLHttpRequest, textStatus, errorThrown) {
                                    //alert(msgAjaxError);
                                }
                            });
                            $("#progressSyncAllInformation").html($("#progressSyncCategoriesInformation").html()).show();
                    ';
                } else {
                    $this->_html .= '
                                $("a.synchroniseallcmspages").show();
                                $("#progressSyncCMSPages span em").css("left", 0);
                                $("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", false).removeClass("ui-state-disabled");
                            ';
                    session_destroy();
                }
                $this->_html .= '
                        });
                    });
                ';
            } else {
                $avancement_synchro = (int)(($_SESSION['count_total_iteration'] * 100) / $_SESSION['count_total_ajax']);
                if ($avancement_synchro > 0) {
                    $remaining_time = trim($this->_getDuration((((time() - $_SESSION['iteration_time_start']) * 100) /  $avancement_synchro) - (time() - $_SESSION['iteration_time_start'])));
                    if ($remaining_time != '') {
                        $remaining_time .= ' '.$this->l('remaining').'...';
                    }
                } else {
                    $remaining_time = $this->l('Remaining time calculation in progress...');
                }
                $this->_html .= '
                    $("#progressSyncCMSPages span em").animate({left: "'.(int)($avancement_synchro).'%"}, 800);
                    $("#progressSyncCMSPagesRemainingTime").html(\''.$remaining_time.'\');
                    $("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", true).addClass("ui-state-disabled");
                    $.ajax( {
                        type : "GET",
                        url : \'' . $this->_base_config_url . '&synchroniseAllCMSPages=1' . '\',
                        dataType : "script",
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            //alert(msgAjaxError);
                        }
                    });
                ';
                $words = $this->getCMSWordsToLink($_SESSION['count_total_iteration'], $_SESSION['count_total_iteration_step']);
                $this->updateHTMLField('cms', $words, false);
            }
            $this->_echoOutput(true);
            return;
        }
        if (Tools::getValue('synchroniseAllCategories') == 1) {
            $this->_cleanOutput();
            session_start();
            if (!isset($_SESSION['clean_html_field_categories'])) {
                $this->cleanAllHTMLField('categories');
                $_SESSION['clean_html_field_categories'] = true;
            }
            if (!isset($_SESSION['iteration_time_start'])) {
                $_SESSION['iteration_time_start'] = time();
            }
            if (!isset($_SESSION['count_total_ajax']) || !isset($_SESSION['count_total_iteration_step'])) {
                $_SESSION['count_total_ajax'] = $this->getCategoriesWordsToLink(null, 0, 0, true);
                $_SESSION['count_total_iteration'] = 0;
                $_SESSION['count_total_iteration_step'] = (int)ceil($_SESSION['count_total_ajax']/2);
                if ($_SESSION['count_total_ajax'] > $this->_max_affected_rows) {
                    $_SESSION['count_total_iteration_step'] = (int)$this->_max_affected_rows;
                }
            } else {
                $_SESSION['count_total_iteration'] += $_SESSION['count_total_iteration_step'];
            }
            if ($_SESSION['count_total_ajax'] == 0) {
                $this->_html .= '
                $("#progressSyncCategories").hide(1000, function() {
                show_info("'. addcslashes($this->l('Optimization done (no rows affected) !'), '"') .'");
                $("#syncCategoriesContainer").addClass(\'taskDone\');
                $("#progressSyncCategoriesInformation").hide();
                $("#progressSyncCategoriesRemainingTime").html(\'\').hide();
                ';
                if (isset($_SESSION['synchronise_everything_process']) && $_SESSION['synchronise_everything_process'] == true) {
                    if (isset($_SESSION['count_total_ajax'])) {
                        unset($_SESSION['count_total_ajax']);
                    }
                    if (isset($_SESSION['count_total_iteration_step'])) {
                        unset($_SESSION['count_total_iteration_step']);
                    }
                    $this->_html .= '
                    $("#progressSyncCategories span em").css("left", 0);
                    $.ajax( {
                    type : "GET",
                    url : \'' . $this->_base_config_url . '&synchroniseAllManufacturers=1'. '\',
                    dataType : "script",
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                    //alert(msgAjaxError);
                }
                });
                $("#progressSyncAllInformation").html($("#progressSyncManufacturersInformation").html()).show();
                ';
                } else {
                    $this->_html .= '
                    $("a.synchroniseallcategories").show();
                    $("#progressSyncCategories span em").css("left", 0);
                    $("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", false).removeClass("ui-state-disabled");
                    ';
                    session_destroy();
                }
                $this->_html .= '});';
                $this->_echoOutput(true);
                return;
            }
            if (isset($_SESSION['count_total_ajax']) && isset($_SESSION['count_total_iteration']) && $_SESSION['count_total_iteration'] > 0 && $_SESSION['count_total_ajax'] > 0 && $_SESSION['count_total_iteration'] >= $_SESSION['count_total_ajax']) {
                $avancement_synchro = (($_SESSION['count_total_iteration'] * 100) / $_SESSION['count_total_ajax']);
                if ($avancement_synchro > 100) {
                    $avancement_synchro = 100;
                }
                $this->_html .= '
                $("#progressSyncCategories span em").animate({left: "'.(int)$avancement_synchro.'%"}, 800, function() {
                $("#progressSyncCategories").hide(2000, function() {
                show_info("'. addcslashes($this->l('Optimization done !'), '"') .'");
                $("#syncCategoriesContainer").addClass(\'taskDone\');
                $("#progressSyncCategoriesInformation").hide();
                $("#progressSyncCategoriesRemainingTime").html(\'\').hide();
                ';
                if (isset($_SESSION['synchronise_everything_process']) && $_SESSION['synchronise_everything_process'] == true) {
                    if (isset($_SESSION['count_total_ajax'])) {
                        unset($_SESSION['count_total_ajax']);
                    }
                    if (isset($_SESSION['count_total_iteration_step'])) {
                        unset($_SESSION['count_total_iteration_step']);
                    }
                    $this->_html .= '
                    $("#progressSyncCategories span em").css("left", 0);
                    $.ajax( {
                    type : "GET",
                    url : \'' . $this->_base_config_url . '&synchroniseAllManufacturers=1\',
                    dataType : "script",
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                    //alert(msgAjaxError);
                }
                });
                $("#progressSyncAllInformation").html($("#progressSyncManufacturersInformation").html()).show();
                ';
                } else {
                    $this->_html .= '
                    $("a.synchroniseallcategories").show();
                    $("#progressSyncCategories span em").css("left", 0);
                    $("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", false).removeClass("ui-state-disabled");
                    ';
                    session_destroy();
                }
                $this->_html .= '
            });
            });
            ';
            } else {
                $avancement_synchro = (int)(($_SESSION['count_total_iteration'] * 100) / $_SESSION['count_total_ajax']);
                if ($avancement_synchro > 0) {
                    $remaining_time = trim($this->_getDuration((((time() - $_SESSION['iteration_time_start']) * 100) /  $avancement_synchro) - (time() - $_SESSION['iteration_time_start'])));
                    if ($remaining_time != '') {
                        $remaining_time .= ' '.$this->l('remaining').'...';
                    }
                } else {
                    $remaining_time = $this->l('Remaining time calculation in progress...');
                }
                $this->_html .= '
                $("#progressSyncCategories span em").animate({left: "'.(int)($avancement_synchro).'%"}, 800);
                $("#progressSyncCategoriesRemainingTime").html(\''.$remaining_time.'\');
                $("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", true).addClass("ui-state-disabled");
                $.ajax( {
                type : "GET",
                url : \'' . $this->_base_config_url . '&synchroniseAllCategories=1' . '\',
                dataType : "script",
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert(msgAjaxError);
            }
            });
            ';
                $words = $this->getCategoriesWordsToLink(null, $_SESSION['count_total_iteration'], $_SESSION['count_total_iteration_step']);
                $this->updateHTMLField('categories', $words, false);
            }
            $this->_echoOutput(true);
            return;
        }
        if (Tools::getValue('synchroniseAllManufacturers') == 1) {
            $this->_cleanOutput();
            session_start();
            if (!isset($_SESSION['clean_html_field_manufacturers'])) {
                $this->cleanAllHTMLField('manufacturers');
                $_SESSION['clean_html_field_manufacturers'] = true;
            }
            if (!isset($_SESSION['iteration_time_start'])) {
                $_SESSION['iteration_time_start'] = time();
            }
            if (!isset($_SESSION['count_total_ajax']) || !isset($_SESSION['count_total_iteration_step'])) {
                $_SESSION['count_total_ajax'] = $this->getManufacturersWordsToLink(null, 0, 0, true);
                $_SESSION['count_total_iteration'] = 0;
                $_SESSION['count_total_iteration_step'] = (int)ceil($_SESSION['count_total_ajax']/2);
                if ($_SESSION['count_total_ajax'] > $this->_max_affected_rows) {
                    $_SESSION['count_total_iteration_step'] = (int)$this->_max_affected_rows;
                }
            } else {
                $_SESSION['count_total_iteration'] += $_SESSION['count_total_iteration_step'];
            }
            if ($_SESSION['count_total_ajax'] == 0) {
                $this->_html .= '
                $("#progressSyncManufacturers").hide(1000, function() {
                show_info("'. addcslashes($this->l('Optimization done (no rows affected) !'), '"') .'");
                $("#syncManufacturersContainer").addClass(\'taskDone\');
                $("#progressSyncManufacturersInformation").hide();
                $("#progressSyncManufacturersRemainingTime").html(\'\').hide();
                ';
                if (isset($_SESSION['synchronise_everything_process']) && $_SESSION['synchronise_everything_process'] == true) {
                    if (isset($_SESSION['count_total_ajax'])) {
                        unset($_SESSION['count_total_ajax']);
                    }
                    if (isset($_SESSION['count_total_iteration_step'])) {
                        unset($_SESSION['count_total_iteration_step']);
                    }
                    $this->_html .= '
                    $("#progressSyncManufacturers span em").css("left", 0);
                    $.ajax( {
                    type : "GET",
                    url : \'' . $this->_base_config_url . '&synchroniseEditorial=1' . '\',
                    dataType : "script",
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                    //alert(msgAjaxError);
                }
                });
                $("#progressSyncAllInformation").html($("#progressSyncEditorialInformation").html()).show();
                ';
                } else {
                    $this->_html .= '
                    $("a.synchroniseallmanufacturers").show();
                    $("#progressSyncManufacturers span em").css("left", 0);
                    $("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", false).removeClass("ui-state-disabled");
                    ';
                    session_destroy();
                }
                $this->_html .= '});';
                $this->_echoOutput(true);
                return;
            }
            if (isset($_SESSION['count_total_ajax']) && isset($_SESSION['count_total_iteration']) && $_SESSION['count_total_iteration'] > 0 && $_SESSION['count_total_ajax'] > 0 && $_SESSION['count_total_iteration'] >= $_SESSION['count_total_ajax']) {
                $avancement_synchro = (($_SESSION['count_total_iteration'] * 100) / $_SESSION['count_total_ajax']);
                if ($avancement_synchro > 100) {
                    $avancement_synchro = 100;
                }
                $this->_html .= '
                $("#progressSyncManufacturers span em").animate({left: "'.(int)($avancement_synchro).'%"}, 800, function() {
                $("#progressSyncManufacturers").hide(2000, function() {
                show_info("'. addcslashes($this->l('Optimization done !'), '"') .'");
                $("#syncManufacturersContainer").addClass(\'taskDone\');
                $("#progressSyncManufacturersInformation").hide();
                $("#progressSyncManufacturersRemainingTime").html(\'\').hide();
                ';
                if (isset($_SESSION['synchronise_everything_process']) && $_SESSION['synchronise_everything_process'] == true) {
                    if (isset($_SESSION['count_total_ajax'])) {
                        unset($_SESSION['count_total_ajax']);
                    }
                    if (isset($_SESSION['count_total_iteration_step'])) {
                        unset($_SESSION['count_total_iteration_step']);
                    }
                    $this->_html .= '
                    $("#progressSyncManufacturers span em").css("left", 0);
                    $.ajax( {
                    type : "GET",
                    url : \'' . $this->_base_config_url . '&synchroniseEditorial=1' . '\',
                    dataType : "script",
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                    //alert(msgAjaxError);
                }
                });
                $("#progressSyncAllInformation").html($("#progressSyncEditorialInformation").html()).show();
                ';
                } else {
                    $this->_html .= '
                    $("a.synchroniseallmanufacturers").show();
                    $("#progressSyncManufacturers span em").css("left", 0);
                    $("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", false).removeClass("ui-state-disabled");
                    ';
                    session_destroy();
                }
                $this->_html .= '
            });
            });
            ';
            } else {
                $avancement_synchro = (int)(($_SESSION['count_total_iteration'] * 100) / $_SESSION['count_total_ajax']);
                if ($avancement_synchro > 0) {
                    $remaining_time = trim($this->_getDuration((((time() - $_SESSION['iteration_time_start']) * 100) /  $avancement_synchro) - (time() - $_SESSION['iteration_time_start'])));
                    if ($remaining_time != '') {
                        $remaining_time .= ' '.$this->l('remaining').'...';
                    }
                } else {
                    $remaining_time = $this->l('Remaining time calculation in progress...');
                }
                $this->_html .= '
                $("#progressSyncManufacturers span em").animate({left: "'.(int)($avancement_synchro).'%"}, 800);
                $("#progressSyncManufacturersRemainingTime").html(\''.$remaining_time.'\');
                $("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", true).addClass("ui-state-disabled");
                $.ajax( {
                type : "GET",
                url : \'' . $this->_base_config_url . '&synchroniseAllManufacturers=1' . '\',
                dataType : "script",
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert(msgAjaxError);
            }
            });
            ';
                $words = $this->getManufacturersWordsToLink(null, $_SESSION['count_total_iteration'], $_SESSION['count_total_iteration_step']);
                $this->updateHTMLField('manufacturers', $words, false);
            }
            $this->_echoOutput(true);
            return;
        }
        if (Tools::getValue('synchroniseAllProducts') == 1) {
            $this->_cleanOutput();
            session_start();
            if (!isset($_SESSION['clean_html_field_products'])) {
                $this->cleanAllHTMLField('products');
                $_SESSION['clean_html_field_products'] = true;
            }
            if (!isset($_SESSION['iteration_time_start'])) {
                $_SESSION['iteration_time_start'] = time();
            }
            if (!isset($_SESSION['count_total_ajax']) || !isset($_SESSION['count_total_iteration_step'])) {
                $_SESSION['count_total_ajax'] = $this->getWordsToLink(null, 0, 0, true);
                $_SESSION['count_total_iteration'] = 0;
                $_SESSION['count_total_iteration_step'] = (int)ceil($_SESSION['count_total_ajax']/2);
                if ($_SESSION['count_total_ajax'] > $this->_max_affected_rows) {
                    $_SESSION['count_total_iteration_step'] = (int)$this->_max_affected_rows;
                }
            } else {
                $_SESSION['count_total_iteration'] += $_SESSION['count_total_iteration_step'];
            }
            if ($_SESSION['count_total_ajax'] == 0) {
                $this->_html .= '
                    $("#progressSyncProduct").hide(1000, function() {
                        show_info("'. addcslashes($this->l('Optimization done (no rows affected) !'), '"') .'");
                        $("#syncProductContainer").addClass(\'taskDone\');
                        $("#progressSyncProductInformation").hide();
                        $("#progressSyncProductRemainingTime").hide().html(\'\');
                        ';
                if (isset($_SESSION['synchronise_everything_process']) && $_SESSION['synchronise_everything_process'] == true) {
                    if (isset($_SESSION['count_total_ajax'])) {
                        unset($_SESSION['count_total_ajax']);
                    }
                    if (isset($_SESSION['count_total_iteration_step'])) {
                        unset($_SESSION['count_total_iteration_step']);
                    }
                    $this->_html .= '
                            $("#progressSyncProduct span em").css("left", 0);
                            $.ajax( {
                                type : "GET",
                                url : \'' . $this->_base_config_url . '&synchroniseAllCMSPages=1' . '\',
                                dataType : "script",
                                error: function(XMLHttpRequest, textStatus, errorThrown) {
                                    //alert(msgAjaxError);
                                }
                            });
                            $("#progressSyncAllInformation").html($("#progressSyncCMSPagesInformation").html()).show();
                            ';
                } else {
                    $this->_html .= '
                            $("a.synchroniseallproducts").show();
                            $("#progressSyncProduct span em").css("left", 0);
                            $("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", false).removeClass("ui-state-disabled");
                    ';
                    session_destroy();
                }
                $this->_html .= '});';
                $this->_echoOutput(true);
                return;
            }
            if (isset($_SESSION['count_total_ajax']) && isset($_SESSION['count_total_iteration']) && $_SESSION['count_total_iteration'] > 0 && $_SESSION['count_total_ajax'] > 0 && $_SESSION['count_total_iteration'] >= $_SESSION['count_total_ajax']) {
                $avancement_synchro = (($_SESSION['count_total_iteration'] * 100) / $_SESSION['count_total_ajax']);
                if ($avancement_synchro > 100) {
                    $avancement_synchro = 100;
                }
                $this->_html .= '
                    $("#progressSyncProduct span em").animate({left: "'.(int)($avancement_synchro).'%"}, 800, function() {
                        $("#progressSyncProduct").hide(2000, function() {
                            show_info("'. addcslashes($this->l('Optimization done !'), '"') .'");
                            $("#syncProductContainer").addClass(\'taskDone\');
                            $("#progressSyncProductInformation").hide();
                            $("#progressSyncProductRemainingTime").hide().html(\'\');
                            ';
                if (isset($_SESSION['synchronise_everything_process']) && $_SESSION['synchronise_everything_process'] == true) {
                    if (isset($_SESSION['count_total_ajax'])) {
                        unset($_SESSION['count_total_ajax']);
                    }
                    if (isset($_SESSION['count_total_iteration_step'])) {
                        unset($_SESSION['count_total_iteration_step']);
                    }
                    $this->_html .= '
                            $("#progressSyncProduct span em").css("left", 0);
                            $.ajax( {
                                type : "GET",
                                url : \'' . $this->_base_config_url . '&synchroniseAllCMSPages=1' . '\',
                                dataType : "script",
                                error: function(XMLHttpRequest, textStatus, errorThrown) {
                                    //alert(msgAjaxError);
                                }
                            });
                            $("#progressSyncAllInformation").html($("#progressSyncCMSPagesInformation").html()).show();
                            ';
                } else {
                    $this->_html .= '
                            $("a.synchroniseallproducts").show();
                            $("#progressSyncProduct span em").css("left", 0);
                            $("a.synchroniseeverything, a.synchroniseeditorial, a.synchroniseallcmspages, a.synchroniseallcategories, a.synchroniseallmanufacturers, a.synchroniseallproducts, a.removealllinks, input[name=submit_global_options]").attr("disabled", false).removeClass("ui-state-disabled");
                    ';
                    session_destroy();
                }
                $this->_html .= '
                    });
                });';
            } else {
                $avancement_synchro = (int)(($_SESSION['count_total_iteration'] * 100) / $_SESSION['count_total_ajax']);
                if ($avancement_synchro > 0) {
                    $remaining_time = trim($this->_getDuration((((time() - $_SESSION['iteration_time_start']) * 100) /  $avancement_synchro) - (time() - $_SESSION['iteration_time_start'])));
                    if ($remaining_time != '') {
                        $remaining_time .= ' '.$this->l('remaining').'...';
                    }
                } else {
                    $remaining_time = $this->l('Remaining time calculation in progress...');
                }
                $this->_html .= '
                    $("#progressSyncProduct span em").animate({left: "'.(int)($avancement_synchro).'%"}, 800);
                    $("#progressSyncProductRemainingTime").html(\''.$remaining_time.'\');
                    $.ajax( {
                        type : "GET",
                        url : \'' . $this->_base_config_url . '&synchroniseAllProducts=1' . '\',
                        dataType : "script",
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            //alert(msgAjaxError);
                        }
                    });
                ';
                $words = $this->getWordsToLink(null, $_SESSION['count_total_iteration'], $_SESSION['count_total_iteration_step']);
                $this->updateHTMLField('products', $words, false);
            }
            $this->_echoOutput(true);
            return;
        }
        if (Tools::getValue('getPanel')) {
            switch (Tools::getValue('getPanel')) {
                case 'displayFormAddExpression':
                    $this->_cleanOutput();
                    $obj = false;
                    if (Tools::getValue('alter') && Tools::getValue('id_expression')) {
                        $id_expression = Tools::getValue('id_expression');
                        $obj = new SEOInternalLinkingExpressionClass($id_expression);
                    } elseif (Tools::getValue('duplicate') && Tools::getValue('id_expression')) {
                        $id_expression = Tools::getValue('id_expression');
                        $obj_parent = new SEOInternalLinkingExpressionClass($id_expression);
                        $obj = new SEOInternalLinkingExpressionClass();
                        foreach ($obj_parent->getFields() as $key => $value) {
                            if ($key == 'id_expression') {
                                continue;
                            }
                            $obj->$key = $value;
                        }
                    } else {
                        $obj = new SEOInternalLinkingExpressionClass();
                    }
                    $this->renderFormAddExpression($obj);
                    $this->_echoOutput(true);
                    break;
                case 'displayFormAddGroup':
                    $this->_cleanOutput();
                    $obj = false;
                    if (Tools::getValue('alter') && Tools::getValue('id_group')) {
                        $id_group = Tools::getValue('id_group');
                        $obj = new SEOInternalLinkingGroupClass($id_group);
                    } else {
                        $obj = new SEOInternalLinkingGroupClass();
                    }
                    $this->renderFormAddGroup($obj);
                    $this->_echoOutput(true);
                    break;
                case 'displayExpressionTable':
                    $this->_cleanOutput();
                    $this->_html .= $this->renderListExpressionsTab();
                    $this->_echoOutput(true);
                    break;
                case 'displayGroupTable':
                    $this->_cleanOutput();
                    $this->_html .= $this->renderListGroupsTab();
                    $this->_echoOutput(true);
                    break;
            }
        }
    }
    protected function _postProcess()
    {
        if (Tools::getValue('submit_global_options')) {
            $return = '';
            $this->_exclude_headings = (int)Tools::getValue('_exclude_headings', false);
            if (Tools::getValue('_description_field') != 'description' && Tools::getValue('_description_field') != 'description_short' && Tools::getValue('_description_field') != 'both_description') {
                $this->_description_field = 'description';
            } else {
                $this->_description_field = Tools::getValue('_description_field');
            }
            if (!is_numeric(Tools::getValue('_default_datatables_length'))) {
                $this->_default_datatables_length = 10;
            } else {
                $this->_default_datatables_length = (int)Tools::getValue('_default_datatables_length');
            }
            Configuration::updateValue('PM_'.self::$_module_prefix.'_EXCLUDE_HEADINGS', $this->_exclude_headings);
            Configuration::updateValue('PM_'.self::$_module_prefix.'_DESCRIPTION_FIELD', $this->_description_field);
            Configuration::updateValue('PM_'.self::$_module_prefix.'_DEFAULT_DATATABLES_LENGTH', $this->_default_datatables_length);
            $return .= '
            <script type="text/javascript">parent.reloadPanel("displayGroupTable"); parent.reloadPanel("displayExpressionTable"); parent.show_info("' . addcslashes($this->l('Saved'), '"') . '");</script>
            ';
            SeoInternalLinkingCoreClass::_cleanBuffer();
            echo $return;
            die();
        } elseif (Tools::getValue('submit_expression')) {
            $return = '';
            $this->postProcessAddExpression();
        } elseif (Tools::getValue('deleteExpression') && Tools::getValue('id_expression')) {
            $return = '';
            $obj = new SEOInternalLinkingExpressionClass(Tools::getValue('id_expression'));
            if ($obj->delete()) {
                $return .= 'reloadPanel("displayExpressionTable"); show_info("' . addcslashes($this->l('Expression has been deleted.'), '"') . '");';
            } else {
                $return .= 'show_error("' . addcslashes($this->l('Error while deleting the expression'), '"') . '");';
            }
            SeoInternalLinkingCoreClass::_cleanBuffer();
            echo $return;
            die();
        } elseif (Tools::getValue('activeExpression') && Tools::getValue('id_expression')) {
            $return = '';
            $obj = new SEOInternalLinkingExpressionClass(Tools::getValue('id_expression'));
            $obj->active = ($obj->active ? 0 : 1);
            if ($obj->save()) {
                $return .= $this->renderReturn('ActiveExpression'.(int)$obj->id_expression, (int)$obj->active, true);
            } else {
                $return .= 'show_info("' . addcslashes($this->l('Error while updating the expression'), '"') . '");';
            }
            SeoInternalLinkingCoreClass::_cleanBuffer();
            echo $return;
            die();
        } elseif (Tools::getValue('noFollowExpression') && Tools::getValue('id_expression')) {
            $return = '';
            $obj = new SEOInternalLinkingExpressionClass(Tools::getValue('id_expression'));
            $obj->nofollow = ($obj->nofollow ? 0 : 1);
            if ($obj->save()) {
                $return .= $this->renderReturn('NoFollowExpression'.(int)$obj->id_expression, (int)$obj->nofollow);
            } else {
                $return .= 'show_info("' . addcslashes($this->l('Error while updating the no follow attribute'), '"') . '");';
            }
            SeoInternalLinkingCoreClass::_cleanBuffer();
            echo $return;
            die();
        } elseif (Tools::getValue('newWindowExpression') && Tools::getValue('id_expression')) {
            $return = '';
            $obj = new SEOInternalLinkingExpressionClass(Tools::getValue('id_expression'));
            $obj->new_window = ($obj->new_window ? 0 : 1);
            if ($obj->save()) {
                $return .= $this->renderReturn('NewWindowExpression'.(int)$obj->id_expression, (int)$obj->new_window);
            } else {
                $return .= 'show_info("' . addcslashes($this->l('Error while updating the open in a new window option'), '"') . '");';
            }
            SeoInternalLinkingCoreClass::_cleanBuffer();
            echo $return;
            die();
        } elseif (Tools::getValue('submit_group')) {
            $return = '';
            $this->postProcessAddGroup();
        } elseif (Tools::getValue('deleteGroup') && Tools::getValue('id_group')) {
            $return = '';
            $obj = new SEOInternalLinkingGroupClass(Tools::getValue('id_group'));
            if ($obj->delete()) {
                $return .= 'reloadPanel("displayGroupTable");reloadPanel("displayExpressionTable");show_info("' . addcslashes($this->l('Group has been deleted.'), '"') . '");';
            } else {
                $return .= 'show_error("' . addcslashes($this->l('Error while deleting the group'), '"') . '");';
            }
            SeoInternalLinkingCoreClass::_cleanBuffer();
            echo $return;
            die();
        }
        parent::_postProcess();
    }
    protected function renderReturn($elementID, $stateValue, $isAnActiveState = false)
    {
        if (!(int)$isAnActiveState) {
            $labelValue = ((int)$stateValue ? $this->l('Enabled') : $this->l('Disabled'));
        } else {
            $labelValue = ((int)$stateValue ? $this->l('Active') : $this->l('Inactive'));
        }
        $vars = array(
            'elementID' => $elementID,
            'state' => array(
                'path' => $this->_path.'views/img/module_' . ((int)$stateValue ? 'install' : 'disabled') . '.png',
                'label' => $labelValue,
                'value' => (int)$stateValue,
            ),
        );
        return $this->fetchTemplate('module/return.tpl', $vars);
    }
    private function postProcessAddExpression()
    {
        $this->_cleanOutput();
        $obj = new SEOInternalLinkingExpressionClass(Tools::getValue('id_expression', false));
        $this->_html = '';
        $this->_errors = $obj->validateController();
        if (!sizeof($this->_errors)) {
            $this->copyFromPost($obj);
            $obj->active = Tools::getValue('active_expression');
            if (!$obj->save()) {
                $this->_errors[] = $this->l('Error while saving in the database');
            }
            if (!sizeof($this->_errors)) {
                $this->_html .= '<script type="text/javascript">
                                    parent.parent.show_info("' . addcslashes($this->l('Saved'), '"') . '");
                                    parent.parent.reloadPanel("displayExpressionTable");
                                    parent.parent.closeDialogIframe();
                                </script>';
            }
        }
        if (sizeof($this->_errors)) {
            $this->_html .= '<script type="text/javascript">
                                parent.parent.show_error("' . addcslashes(implode('<br />', $this->_errors), '"') . '");
                            </script>';
        }
        $this->_echoOutput(true);
    }
    private function postProcessAddGroup()
    {
        $this->_cleanOutput();
        $obj = new SEOInternalLinkingGroupClass(Tools::getValue('id_group', false));
        $this->_html = '';
        $this->_errors = $obj->validateController();
        if (!sizeof($this->_errors)) {
            $this->copyFromPost($obj);
            if (!$obj->save()) {
                $this->_errors [] = $this->l('Error while saving in the database');
            }
            if (!sizeof($this->_errors)) {
                $this->_html .= '<script type="text/javascript">
                                    parent.parent.show_info("' . addcslashes($this->l('Saved'), '"') . '");
                                    parent.parent.reloadPanel("displayGroupTable");
                                    parent.parent.reloadPanel("displayExpressionTable");
                                    parent.parent.closeDialogIframe();
                                </script>';
            }
        }
        if (sizeof($this->_errors)) {
            $this->_html .= '<script type="text/javascript">
                                parent.parent.show_error("' . addcslashes(implode('<br />', $this->_errors), '"') . '");
                            </script>';
        }
        $this->_echoOutput(true);
    }
    public function hookHeader($params)
    {
        return $this->display(__FILE__, $this->name . '_header.tpl');
    }
    public function _setGroupConcatMaxLength()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('SET SESSION group_concat_max_len='.$this->_group_concat_max_len);
    }
    public function getWordsToLink($id_product = null, $start = 0, $limit = 0, $count = false, $from_crontab = false)
    {
        $this->_setGroupConcatMaxLength();
        $sql_group_search = '
        SELECT * FROM
        (SELECT silg.*,
        GROUP_CONCAT(DISTINCT silpr.id_product SEPARATOR \',\') AS products,
        GROUP_CONCAT(DISTINCT silcr.id_category SEPARATOR \',\') AS categories,
        GROUP_CONCAT(DISTINCT silmr.id_manufacturer SEPARATOR \',\') AS manufacturers,
        GROUP_CONCAT(DISTINCT silsr.id_supplier SEPARATOR \',\') AS suppliers
        FROM `' . _DB_PREFIX_ . 'pm_seointernallinking_group` silg
        LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking_manufacturer_rules` silmr ON silmr.id_group=silg.id_group
        LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking_supplier_rules` silsr ON silsr.id_group=silg.id_group
        LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking_category_rules` silcr ON silcr.id_group=silg.id_group
        LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking_product_rules` silpr ON silpr.id_group=silg.id_group
        WHERE silg.group_type=1
        GROUP BY silg.id_group)
        AS `group_search`
        WHERE (products IS NOT NULL OR categories IS NOT NULL OR manufacturers IS NOT NULL OR suppliers IS NOT NULL)
        ';
        $result_group_search = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql_group_search);
        $sql_product_search_query = array();
        ini_set('memory_limit', $this->_big_sql_results_memory_limit);
        $result_products_global = array();
        if (self::_isFilledArray($result_group_search)) {
            foreach ($result_group_search as $group) {
                $and_word = true;
                $sql_product_search = '
                (SELECT DISTINCT '.(Shop::isFeatureActive() ? 'p_shop.id_shop, ' : '').'p.id_product, sil.id_lang, sil.expression_content, sil.associated_url, sil.url_title, sil.nofollow, sil.new_window, sil.link_position
                FROM `' . _DB_PREFIX_ . 'product` p
                LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON cp.id_product=p.id_product
                LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking` sil ON sil.id_group=\''.$group['id_group'].'\'' .
                (Shop::isFeatureActive() ? ' LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking_group_shop` silgs ON silgs.id_group = sil.id_group ' : '')
                . self::addSqlAssociation('product', 'p', 'id_product', true, null, ($from_crontab ? 'all' : false), null, true) .
                (Shop::isFeatureActive() ? ' AND p_shop.id_shop=silgs.id_shop ' : '') .
                ' WHERE sil.active=1';
                if (isset($id_product) && is_numeric($id_product) && $id_product > 0) {
                    $sql_product_search .= ($and_word ? ' AND ' : ' ').'p.id_product=\''. (int)$id_product .'\' ';
                }
                if ($group['categories'] != '') {
                    if ($group['category_type'] == 0) {
                        $sql_product_search .= ($and_word ? ' AND ' : ' ').'cp.id_category IN ('.$group['categories'].')';
                    } else {
                        $sql_product_search .= ($and_word ? ' AND ' : ' ').'cp.id_category NOT IN ('.$group['categories'].')';
                    }
                    if (!$and_word) {
                        $and_word = true;
                    }
                }
                if ($group['products'] != '') {
                    if ($group['product_type'] == 0) {
                        $sql_product_search .= ($and_word ? ' AND ' : ' ').'p.id_product IN ('.$group['products'].')';
                    } else {
                        $sql_product_search .= ($and_word ? ' AND ' : ' ').'p.id_product NOT IN ('.$group['products'].')';
                    }
                    if (!$and_word) {
                        $and_word = true;
                    }
                }
                if ($group['manufacturers'] != '') {
                    if ($group['manufacturer_type'] == 0) {
                        $sql_product_search .= ($and_word ? ' AND ' : ' ').'p.id_manufacturer IN ('.$group['manufacturers'].')';
                    } else {
                        $sql_product_search .= ($and_word ? ' AND ' : ' ').'p.id_manufacturer NOT IN ('.$group['manufacturers'].')';
                    }
                    if (!$and_word) {
                        $and_word = true;
                    }
                }
                if ($group['suppliers'] != '') {
                    if ($group['supplier_type'] == 0) {
                        $sql_product_search .= ($and_word ? ' AND ' : ' ').' p.id_supplier IN ('.$group['suppliers'].')';
                    } else {
                        $sql_product_search .= ($and_word ? ' AND ' : ' ').'p.id_supplier NOT IN ('.$group['suppliers'].')';
                    }
                    if (!$and_word) {
                        $and_word = true;
                    }
                }
                $sql_product_search .= ')';
                $sql_product_search_query[] = $sql_product_search;
            }
            if (self::_isFilledArray($sql_product_search_query)) {
                $sql_product_search_global = implode($sql_product_search_query, ' UNION');
                if ($count) {
                    $sql_product_search_global_count = 'SELECT COUNT(*) as nb FROM ('.$sql_product_search_global.') AS table_count';
                    $count_total = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql_product_search_global_count);
                    return (int)$count_total['nb'];
                }
                if ($limit != 0) {
                    $sql_product_search_global = 'SELECT * FROM (SELECT * FROM ('.$sql_product_search_global.') AS table_result LIMIT '.$start.','.$limit.') as table_result2 ORDER BY RAND()';
                }
                $result_products_global = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql_product_search_global);
            }
        }
        if ($count) {
            return 0;
        }
        if (is_array($result_products_global)) {
            return $result_products_global;
        }
        return false;
    }
    public function getCMSWordsToLink($start = 0, $limit = 0, $count = false, $from_crontab = false)
    {
        $this->_setGroupConcatMaxLength();
        $sql_group_search = '
        SELECT * FROM
        (SELECT silg.*,
        GROUP_CONCAT(DISTINCT silcr.id_cms SEPARATOR \',\') AS cms_pages
        FROM `' . _DB_PREFIX_ . 'pm_seointernallinking_group` silg
        LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking_cms_rules` silcr ON silcr.id_group=silg.id_group
        WHERE silg.group_type=2
        GROUP BY silg.id_group)
        AS `group_search`
        WHERE cms_pages IS NOT NULL
        ';
        $result_group_search = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql_group_search);
        $sql_cms_search_query = array();
        ini_set('memory_limit', $this->_big_sql_results_memory_limit);
        $result_cms_global = array();
        if (self::_isFilledArray($result_group_search)) {
            foreach ($result_group_search as $group) {
                $and_word = true;
                $sql_cms_search = '
                (SELECT DISTINCT '.(Shop::isFeatureActive() ? 'c_shop.id_shop, ' : '').'c.id_cms, sil.id_lang, sil.expression_content, sil.associated_url, sil.url_title, sil.nofollow, sil.new_window, sil.link_position
                FROM `' . _DB_PREFIX_ . 'cms` c
                LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking` sil ON sil.id_group=\''.$group['id_group'].'\'' .
                (Shop::isFeatureActive() ? ' LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking_group_shop` silgs ON silgs.id_group = sil.id_group ' : '')
                . self::addSqlAssociation('cms', 'c', 'id_cms', true, null, ($from_crontab ? 'all' : false), null, true) .
                (Shop::isFeatureActive() ? ' AND c_shop.id_shop=silgs.id_shop ' : '') .
                'WHERE sil.active=1';
                if ($group['cms_type'] == 0) {
                    $sql_cms_search .= ' AND c.id_cms IN ('.$group['cms_pages'].')';
                } else {
                    $sql_cms_search .= ' AND c.id_cms NOT IN ('.$group['cms_pages'].')';
                }
                $sql_cms_search .= ')';
                $sql_cms_search_query[] = $sql_cms_search;
            }
            if (self::_isFilledArray($sql_cms_search_query)) {
                $sql_cms_search_global = implode($sql_cms_search_query, ' UNION');
                if ($count) {
                    $sql_cms_search_global_count = 'SELECT COUNT(*) as nb FROM ('.$sql_cms_search_global.') AS table_count';
                    $count_total = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql_cms_search_global_count);
                    return (int)$count_total['nb'];
                }
                if ($limit != 0) {
                    $sql_cms_search_global = 'SELECT * FROM ('.$sql_cms_search_global.') AS table_result LIMIT '.$start.','.$limit;
                }
                $result_cms_global = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql_cms_search_global);
            }
        }
        if ($count) {
            return 0;
        }
        if (self::_isFilledArray($result_cms_global)) {
            return $result_cms_global;
        }
        return false;
    }
    public function getCategoriesWordsToLink($id_category = null, $start = 0, $limit = 0, $count = false, $from_crontab = false)
    {
        $this->_setGroupConcatMaxLength();
        $sql_group_search = '
        SELECT * FROM
        (SELECT silg.*,
        GROUP_CONCAT(DISTINCT silcr.id_category SEPARATOR \',\') AS categories
        FROM `' . _DB_PREFIX_ . 'pm_seointernallinking_group` silg
        LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking_category_rules` silcr ON silcr.id_group=silg.id_group
        WHERE silg.group_type=4
        GROUP BY silg.id_group)
        AS `group_search`
        WHERE categories IS NOT NULL
        ';
        $result_group_search = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql_group_search);
        $sql_categories_search_query = array();
        ini_set('memory_limit', $this->_big_sql_results_memory_limit);
        $result_categories_global = array();
        if (self::_isFilledArray($result_group_search)) {
            foreach ($result_group_search as $group) {
                $and_word = true;
                $sql_categories_search = '
                (SELECT DISTINCT '.(Shop::isFeatureActive() ? 'c_shop.id_shop, ' : '').'c.id_category, sil.id_lang, sil.expression_content, sil.associated_url, sil.url_title, sil.nofollow, sil.new_window, sil.link_position
                FROM `' . _DB_PREFIX_ . 'category` c
                LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking` sil ON sil.id_group=\''.$group['id_group'].'\' ' .
                (Shop::isFeatureActive() ? ' LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking_group_shop` silgs ON silgs.id_group = sil.id_group ' : '')
                . self::addSqlAssociation('category', 'c', 'id_category', true, null, ($from_crontab ? 'all' : false), null, true) .
                (Shop::isFeatureActive() ? ' AND c_shop.id_shop=silgs.id_shop ' : '') . '
                WHERE sil.active=1';
                if (isset($id_category) && is_numeric($id_category) && $id_category > 0) {
                    $sql_categories_search .= ' AND c.id_category=\''. (int)$id_category .'\' ';
                }
                if ($group['category_type'] == 0) {
                    $sql_categories_search .= ' AND c.id_category IN ('.$group['categories'].')';
                } else {
                    $sql_categories_search .= ' AND c.id_category NOT IN ('.$group['category_type'].')';
                }
                $sql_categories_search .= ')';
                $sql_categories_search_query[] = $sql_categories_search;
            }
            if (self::_isFilledArray($sql_categories_search_query)) {
                $sql_categories_search_global = implode($sql_categories_search_query, ' UNION');
                if ($count) {
                    $sql_categories_search_global_count = str_replace(
                        'c.id_category, sil.id_lang, sil.expression_content, sil.associated_url, sil.url_title, sil.nofollow, sil.new_window, sil.link_position',
                        'c.id_category, sil.id_lang, sil.expression_content',
                        $sql_categories_search_global
                    );
                    $sql_categories_search_global_count = 'SELECT COUNT(*) as nb FROM ('.$sql_categories_search_global_count.') AS table_count';
                    $count_total = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql_categories_search_global_count);
                    return (int)$count_total['nb'];
                }
                if ($limit != 0) {
                    $sql_categories_search_global = 'SELECT * FROM ('.$sql_categories_search_global.') AS table_result GROUP BY id_category,id_lang,expression_content LIMIT '.$start.','.$limit;
                }
                $result_categories_global = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql_categories_search_global);
            }
        }
        if ($count) {
            return 0;
        }
        if (self::_isFilledArray($result_categories_global)) {
            return $result_categories_global;
        }
        return false;
    }
    public function getManufacturersWordsToLink($id_manufacturer = null, $start = 0, $limit = 0, $count = false, $from_crontab = false)
    {
        $this->_setGroupConcatMaxLength();
        $sql_group_search = '
        SELECT * FROM
        (SELECT silg.*,
        GROUP_CONCAT(DISTINCT silmr.id_manufacturer SEPARATOR \',\') AS manufacturers
        FROM `' . _DB_PREFIX_ . 'pm_seointernallinking_group` silg
        LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking_manufacturer_rules` silmr ON silmr.id_group=silg.id_group
        WHERE silg.group_type=5
        GROUP BY silg.id_group)
        AS `group_search`
        WHERE manufacturers IS NOT NULL
        ';
        $result_group_search = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql_group_search);
        $sql_manufacturers_search_query = array();
        ini_set('memory_limit', $this->_big_sql_results_memory_limit);
        $result_manufacturers_global = array();
        if (self::_isFilledArray($result_group_search)) {
            foreach ($result_group_search as $group) {
                $and_word = true;
                $sql_manufacturers_search = '
                (SELECT DISTINCT '.(Shop::isFeatureActive() ? 'm_shop.id_shop, ' : '').'m.id_manufacturer, sil.id_lang, sil.expression_content, sil.associated_url, sil.url_title, sil.nofollow, sil.new_window, sil.link_position
                FROM `' . _DB_PREFIX_ . 'manufacturer` m
                LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking` sil ON sil.id_group=\''.$group['id_group'].'\' ' .
                (Shop::isFeatureActive() ? ' LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking_group_shop` silgs ON silgs.id_group = sil.id_group ' : '')
                . self::addSqlAssociation('manufacturer', 'm', 'id_manufacturer', true, null, ($from_crontab ? 'all' : false), null, true) .
                (Shop::isFeatureActive() ? ' AND m_shop.id_shop=silgs.id_shop ' : '') . '
                WHERE sil.active=1';
                if (isset($id_manufacturer) && is_numeric($id_manufacturer) && $id_manufacturer > 0) {
                    $sql_manufacturers_search .= ' AND m.id_manufacturer=\''. (int)$id_manufacturer .'\' ';
                }
                if ($group['manufacturer_type'] == 0) {
                    $sql_manufacturers_search .= ' AND m.id_manufacturer IN ('.$group['manufacturers'].')';
                } else {
                    $sql_manufacturers_search .= ' AND m.id_manufacturer NOT IN ('.$group['manufacturer_type'].')';
                }
                $sql_manufacturers_search .= ')';
                $sql_manufacturers_search_query[] = $sql_manufacturers_search;
            }
            if (self::_isFilledArray($sql_manufacturers_search_query)) {
                $sql_manufacturers_search_global = implode($sql_manufacturers_search_query, ' UNION');
                if ($count) {
                    $sql_manufacturers_search_global_count = str_replace(
                        'm.id_manufacturer, sil.id_lang, sil.expression_content, sil.associated_url, sil.url_title, sil.nofollow, sil.new_window, sil.link_position',
                        'm.id_manufacturer, sil.id_lang, sil.expression_content',
                        $sql_manufacturers_search_global
                    );
                    $sql_manufacturers_search_global_count = 'SELECT COUNT(*) as nb FROM ('.$sql_manufacturers_search_global_count.') AS table_count';
                    $count_total = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql_manufacturers_search_global_count);
                    return (int)$count_total['nb'];
                }
                if ($limit != 0) {
                    $sql_manufacturers_search_global = 'SELECT * FROM ('.$sql_manufacturers_search_global.') AS table_result GROUP BY id_manufacturer,id_lang,expression_content LIMIT '.$start.','.$limit;
                }
                $result_manufacturers_global = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql_manufacturers_search_global);
            }
        }
        if ($count) {
            return 0;
        }
        if (self::_isFilledArray($result_manufacturers_global)) {
            return $result_manufacturers_global;
        }
        return false;
    }
    public function getEditorialWordsToLink($from_crontab = false)
    {
        ini_set('memory_limit', $this->_big_sql_results_memory_limit);
        if (Module::isInstalled('editorial')) {
            $sql_editorial_search_query = '
                SELECT DISTINCT '.((Shop::isFeatureActive()) ? 'e.id_shop, ' : '').'e.id_editorial, sil.id_lang, sil.expression_content, sil.associated_url, sil.url_title, sil.nofollow, sil.new_window, sil.link_position
                FROM `' . _DB_PREFIX_ . 'editorial` e
                LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking_group` silg ON 1=1
                LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking` sil ON sil.id_group=silg.id_group
                '. self::addSqlAssociation('editorial', 'e', 'id_editorial', true, null, ($from_crontab ? 'all' : false), 'editorial', true) . '
                WHERE silg.group_type=3
                AND sil.active=1
            ';
        } elseif (Module::isInstalled('ps_customtext')) {
            $sql_editorial_search_query = '
                SELECT DISTINCT '.((Shop::isFeatureActive()) ? 'i_shop.id_shop, ' : '').'i.id_info, sil.id_lang, sil.expression_content, sil.associated_url, sil.url_title, sil.nofollow, sil.new_window, sil.link_position
                FROM `' . _DB_PREFIX_ . 'info` i
                LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking_group` silg ON 1=1
                LEFT JOIN `' . _DB_PREFIX_ . 'pm_seointernallinking` sil ON sil.id_group=silg.id_group
                '. self::addSqlAssociation('info', 'i', 'id_info', true, null, ($from_crontab ? 'all' : false), 'info_shop', true) . '
                WHERE silg.group_type=3
                AND sil.active=1
            ';
        }
        $result_editorial_global = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql_editorial_search_query);
        if (self::_isFilledArray($result_editorial_global)) {
            return $result_editorial_global;
        }
        return array();
    }
    public function deleteAllLinks($description)
    {
        $description = preg_replace('#<a(?:.[^>]*)?rel="pmsil nofollow"(?:.[^<]*)?>(.[^<][^/]*)</a>#', '$1', $description);
        $description = preg_replace('#<a(?:.[^>]*)?rel="pmsil"(?:.[^<]*)?>(.[^<][^/]*)</a>#', '$1', $description);
        $description = preg_replace('#<a(?:.[^>]*)?class="pmsil"(?:.[^<]*)?>(.[^<][^/]*)</a>#', '$1', $description);
        return $description;
    }
    public function updateLinksAlternative($subject, $pattern, $replacement, $word, $link_position, $usingEntities = false)
    {
        $html_table = get_html_translation_table(HTML_ENTITIES, ENT_COMPAT);
        foreach ($html_table as $char => $entities) {
            $subject = str_replace($entities, $char, $subject);
        }
        unset($html_table[chr(60)]);
        unset($html_table[chr(62)]);
        unset($html_table[chr(34)]);
        unset($html_table[chr(38)]);
        foreach ($html_table as $char => $entities) {
            $entities = str_replace('&', '1SILENTITIES1', $entities);
            $entities = str_replace(';', '2SILENTITIES2', $entities);
            $subject = str_replace($char, $entities, $subject);
            if ($usingEntities) {
                $word = str_replace('&', '1SILENTITIES1', $word);
                $word = str_replace(';', '2SILENTITIES2', $word);
            }
            $word = str_replace($char, $entities, $word);
        }
        $pattern = '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))\b('.preg_quote($word).')\b/imu';
        $resultats = preg_split($pattern, $subject, -1, PREG_SPLIT_OFFSET_CAPTURE);
        $chaines_ok = array();
        foreach ($resultats as $resultat) {
            $offset = $resultat[1];
            $substr_start = $offset + Tools::strlen($resultat[0]);
            $substr_end = Tools::strlen($word) + 1;
            $chaine_a_traiter = Tools::substr($subject, $substr_start, $substr_end);
            if ($chaine_a_traiter != false) {
                $chaine_traitee = preg_replace($pattern, $replacement, $chaine_a_traiter, 1, $count);
                if ($count > 0) {
                    $chaine_finale = Tools::substr($subject, 0, $substr_start);
                    $chaine_finale .= $chaine_traitee;
                    $chaine_finale .= Tools::substr($subject, $substr_start + $substr_end);
                    foreach ($html_table as $char => $entities) {
                        $entities = str_replace('&', '1SILENTITIES1', $entities);
                        $entities = str_replace(';', '2SILENTITIES2', $entities);
                        $chaine_finale = str_replace($entities, $char, $chaine_finale);
                    }
                    $chaines_ok[] = $chaine_finale;
                }
            }
        }
        if (self::_isFilledArray($chaines_ok)) {
            $chaines_ok_count = count($chaines_ok);
            switch ($link_position) {
                case 1:
                default:
                    $index = 0;
                    break;
                case 2:
                    $index = ceil($chaines_ok_count/2) - 1;
                    break;
                case 3:
                    $index = $chaines_ok_count - 1;
                    break;
            }
            return $chaines_ok[$index];
        }
        return false;
    }
    public function updateLinks($subject, $pattern, $replacement, $word, $link_position, $usingEntities = false)
    {
        $resultats = preg_split($pattern, $subject, -1, PREG_SPLIT_OFFSET_CAPTURE);
        $chaines_ok = array();
        foreach ($resultats as $resultat) {
            $offset = $resultat[1];
            $substr_start = $offset + Tools::strlen($resultat[0]);
            $substr_end = Tools::strlen($word) + 1;
            $chaine_a_traiter = Tools::substr($subject, $substr_start, $substr_end);
            if ($chaine_a_traiter != false) {
                $chaine_traitee = preg_replace($pattern, $replacement, $chaine_a_traiter, 1, $count);
                if ($count > 0) {
                    $chaine_finale = Tools::substr($subject, 0, $substr_start);
                    $chaine_finale .= $chaine_traitee;
                    $chaine_finale .= Tools::substr($subject, $substr_start + $substr_end);
                    $chaines_ok[] = $chaine_finale;
                }
            }
        }
        if (self::_isFilledArray($chaines_ok)) {
            $chaines_ok_count = count($chaines_ok);
            switch ($link_position) {
                case 1:
                default:
                    $index = 0;
                    break;
                case 2:
                    $index = ceil($chaines_ok_count/2) - 1;
                    break;
                case 3:
                    $index = $chaines_ok_count - 1;
                    break;
            }
            return $chaines_ok[$index];
        }
        return $this->updateLinksAlternative($subject, $pattern, $replacement, $word, $link_position, $usingEntities);
    }
    public function updateProductDescription($product)
    {
        $this->cleanAllHTMLField('products', false, $product->id);
        $word_combinaisons = $this->getWordsToLink($product->id);
        if (self::_isFilledArray($word_combinaisons)) {
            $result = $this->updateHTMLField('products', $word_combinaisons, true);
        }
        return true;
    }
    public function updateCategoryDescription($category)
    {
        $this->cleanAllHTMLField('categories', false, $category->id);
        $word_combinaisons = $this->getCategoriesWordsToLink($category->id);
        if (self::_isFilledArray($word_combinaisons)) {
            $result = $this->updateHTMLField('categories', $word_combinaisons, true);
        }
        return true;
    }
    public function updateManufacturerDescription($manufacturer)
    {
        $this->cleanAllHTMLField('manufacturers', false, $manufacturer->id);
        $word_combinaisons = $this->getManufacturersWordsToLink($manufacturer->id);
        if (self::_isFilledArray($word_combinaisons)) {
            $result = $this->updateHTMLField('manufacturers', $word_combinaisons, true);
        }
        return true;
    }
    public function updateHTMLField($field_type = 'products', $word_combinaisons, $clean_first = false, $from_crontab = false)
    {
        if (!self::_isFilledArray($word_combinaisons)) {
            return;
        }
        switch ($field_type) {
            case 'editorial':
                if (Module::isInstalled('editorial')) {
                    $sql_association_table = 'editorial';
                    $table = '`'._DB_PREFIX_.'editorial_lang`';
                    $table_alias = 'el';
                    $identifier_shop = 'el_shop.`id_shop`';
                    $identifier_field = 'id_editorial';
                    $descriptions_field = array('body_paragraph');
                    $join_table_name = 'editorial';
                } elseif (Module::isInstalled('ps_customtext')) {
                    $sql_association_table = 'info';
                    $table = '`'._DB_PREFIX_.'info_lang`';
                    $table_alias = 'il';
                    $identifier_shop = 'il.`id_shop`';
                    $identifier_field = 'id_info';
                    $descriptions_field = array('text');
                    $join_table_name = 'info_shop';
                }
                break;
            case 'cms':
                $sql_association_table = 'cms';
                $table = '`'._DB_PREFIX_.'cms_lang`';
                $table_alias = 'cmsl';
                $identifier_shop = 'cmsl_shop.`id_shop`';
                $identifier_field = 'id_cms';
                $descriptions_field = array('content');
                $join_table_name = null;
                break;
            case 'categories':
                $sql_association_table = 'category';
                $table = '`'._DB_PREFIX_.'category_lang`';
                $table_alias = 'catl';
                $identifier_shop = 'catl.`id_shop`';
                $identifier_field = 'id_category';
                $descriptions_field = array('description');
                $join_table_name = null;
                break;
            case 'manufacturers':
                $sql_association_table = 'manufacturer';
                $table = '`'._DB_PREFIX_.'manufacturer_lang`';
                $table_alias = 'manl';
                $identifier_shop = '';
                $identifier_field = 'id_manufacturer';
                $descriptions_field = array('description');
                $join_table_name = null;
                break;
            case 'products':
            default:
                $sql_association_table = 'product';
                $table = '`'._DB_PREFIX_.'product_lang`';
                $table_alias = 'pl';
                $identifier_shop = 'pl.`id_shop`';
                $identifier_field = 'id_product';
                if ($this->_description_field == 'both_description') {
                    $descriptions_field = array('description', 'description_short');
                } else {
                    $descriptions_field = array($this->_description_field);
                }
                $join_table_name = null;
                break;
        }
        $exclude_headings = (bool)$this->_exclude_headings;
        if (session_id() != '') {
            if (!isset($_SESSION['ajax_to_clean'])) {
                $_SESSION['ajax_to_clean'] = array();
            }
            $to_clean = $_SESSION['ajax_to_clean'];
        } else {
            $to_clean = array();
        }
        $columns_description = array();
        foreach ($descriptions_field as $description_field) {
            $columns_description[] = '`'.$description_field.'`';
        }
        $columnsContent = array();
        $hashReplaceQueryList = array();
        foreach ($word_combinaisons as $word_array) {
            $id_value = (int)$word_array[$identifier_field];
            $id_lang = (int)$word_array['id_lang'];
            if (isset($word_array['id_shop'])) {
                $id_shop = (int)$word_array['id_shop'];
            } else {
                $id_shop = false;
            }
            $word = $word_array['expression_content'];
            $url = $word_array['associated_url'];
            $url_title_sha1 = sha1('PMSIL-URL-' . $word_array['url_title']);
            $url_title = htmlentities($word_array['url_title'], ENT_COMPAT, 'UTF-8');
            $nofollow = (bool)($word_array['nofollow']);
            $new_window = (bool)($word_array['new_window']);
            $link_position = (int)$word_array['link_position'];
            $columns_condition = array();
            foreach ($descriptions_field as $description_field) {
                $columns_condition[] = '(`'.$description_field.'` LIKE "%'.pSQL($word).'%" OR `'.$description_field.'` LIKE "%'.pSQL(htmlentities($word, ENT_COMPAT, 'UTF-8'), true).'%")';
            }
            $sql = 'SELECT DISTINCT '. ($id_shop != false && !empty($identifier_shop) ? $identifier_shop.', ' : '') . $table_alias.'.`'.$identifier_field.'`, `id_lang`, '.implode($columns_description, ', ').'
            FROM ' . $table  . ' ' . $table_alias
            . (isset($word_array['id_shop']) ? self::addSqlAssociation($sql_association_table, $table_alias, $identifier_field, true, null, array($id_shop), $join_table_name, true) : '') .
            ' WHERE `id_lang`="'.$id_lang.'"
            AND '.$table_alias.'.`'.$identifier_field.'`="'.$id_value.'" '
            . (!empty($identifier_shop) && $sql_association_table == 'product' && isset($word_array['id_shop']) ? ' AND '.$table_alias.'_shop.`id_shop`='.$identifier_shop : '')
            . (!empty($identifier_shop) && $sql_association_table == 'category' && isset($word_array['id_shop']) ? ' AND '.$table_alias.'_shop.`id_shop`='.$identifier_shop : '') .
            'AND ('.implode($columns_condition, ' OR ').')'
            ;
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
            if ($result && is_array($result)) {
                foreach ($result as $key => $row) {
                    $rowIdentifier = $field_type . '-' . (isset($word_array['id_shop']) ? $word_array['id_shop'] : 0) . '-' . $row['id_lang'] . '-' . $row[$identifier_field];
                    if (!isset($to_clean[$rowIdentifier]) && $clean_first == true) {
                        $to_clean[$rowIdentifier] = true;
                    }
                    foreach ($descriptions_field as $description_field) {
                        if (!isset($columnsContent[$rowIdentifier][$description_field])) {
                            $columnsContent[$rowIdentifier][$description_field] = $row[$description_field];
                        }
                    }
                    if (isset($to_clean[$rowIdentifier]) && $to_clean[$rowIdentifier] == true) {
                        foreach ($descriptions_field as $description_field) {
                            $columnsContent[$rowIdentifier][$description_field] = $this->deleteAllLinks($columnsContent[$rowIdentifier][$description_field]);
                        }
                    }
                    if ((bool)$this->_exclude_headings == true) {
                        foreach ($descriptions_field as $description_field) {
                            $columnsContent[$rowIdentifier][$description_field] = Tools::stripslashes(preg_replace_callback($this->_preg_pattern_headings, 'self::headingsDoNotTouchStart', $columnsContent[$rowIdentifier][$description_field]));
                        }
                    }
                    foreach ($descriptions_field as $description_field) {
                        $update_links_result = $this->updateLinks(
                            $columnsContent[$rowIdentifier][$description_field],
                            '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))\b('.preg_quote($word).')\b/imu',
                            '<a href="'.$url.'" class="pmsil"'. (($nofollow) ? ' rel="nofollow"' : '') . (($url_title_sha1 != '') ? ' title="'.$url_title_sha1.'"' : '') . (($new_window) ? ' target="_blank"' : '') .'>$1</a>',
                            $word,
                            $link_position
                        );
                        if (!$update_links_result) {
                            if (Tools::substr(htmlentities($word, ENT_COMPAT, 'UTF-8'), -1) == ';') {
                                $update_links_result = $this->updateLinks(
                                    $columnsContent[$rowIdentifier][$description_field],
                                    '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))(\b'.preg_quote(rtrim(htmlentities($word, ENT_COMPAT, 'UTF-8'), ';')).'\b;)/imu',
                                    '<a href="'.$url.'" class="pmsil"'. (($nofollow) ? ' rel="nofollow"' : '') . (($url_title_sha1 != '') ? ' title="'.$url_title_sha1.'"' : '') . (($new_window) ? ' target="_blank"' : '') .'>$1</a>',
                                    htmlentities($word, ENT_COMPAT, 'UTF-8'),
                                    $link_position,
                                    true
                                );
                            } else {
                                $update_links_result = $this->updateLinks(
                                    $columnsContent[$rowIdentifier][$description_field],
                                    '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))\b('.preg_quote(htmlentities($word, ENT_COMPAT, 'UTF-8')).')\b/imu',
                                    '<a href="'.$url.'" class="pmsil"'. (($nofollow) ? ' rel="nofollow"' : '') . (($url_title_sha1 != '') ? ' title="'.$url_title_sha1.'"' : '') . (($new_window) ? ' target="_blank"' : '') .'>$1</a>',
                                    htmlentities($word, ENT_COMPAT, 'UTF-8'),
                                    $link_position,
                                    true
                                );
                            }
                        }
                        if ($update_links_result != false) {
                            $columnsContent[$rowIdentifier][$description_field] = $update_links_result;
                        }
                    }
                    if ((bool)$this->_exclude_headings == true) {
                        foreach ($descriptions_field as $description_field) {
                            $columnsContent[$rowIdentifier][$description_field] = Tools::stripslashes(preg_replace_callback($this->_preg_pattern_headings, 'self::headingsDoNotTouchEnd', $columnsContent[$rowIdentifier][$description_field]));
                        }
                    }
                    foreach ($descriptions_field as $description_field) {
                        if (empty($columnsContent[$rowIdentifier][$description_field])) {
                            continue;
                        }
                        $hashReplaceQueryList[] = 'UPDATE '.$table.' SET '.$description_field.'=REPLACE('.$description_field.', "'. pSQL($url_title_sha1) .'", "'. pSQL($url_title) .'") WHERE '.$identifier_field.'="'.pSQL($row[$identifier_field]).'" AND id_lang="'.pSQL($row['id_lang']).'"'.(isset($row['id_shop']) && $field_type != 'editorial' && $field_type != 'cms' ? ' AND id_shop="'.pSQL($row['id_shop']).'"' : '');
                        $sql_update = 'UPDATE '.$table.' SET '.$description_field.'="'.pSQL($columnsContent[$rowIdentifier][$description_field], true).'" WHERE '.$identifier_field.'="'.pSQL($row[$identifier_field]).'" AND id_lang="'.pSQL($row['id_lang']).'"'.(isset($row['id_shop']) && $field_type != 'editorial' && $field_type != 'cms' ? ' AND id_shop="'.pSQL($row['id_shop']).'"' : '');
                        $result_update = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_update);
                    }
                    $to_clean[$rowIdentifier] = false;
                }
            }
        }
        $_SESSION['ajax_to_clean'] = $to_clean;
        if (sizeof($hashReplaceQueryList)) {
            $hashReplaceQueryList = array_unique($hashReplaceQueryList);
            foreach ($hashReplaceQueryList as $hashReplaceQuery) {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($hashReplaceQuery);
            }
        }
    }
    public function cleanAllHTMLField($field_type = 'products', $from_crontab = false, $id_identifier = null)
    {
        switch ($field_type) {
            case 'editorial':
                if (Module::isInstalled('editorial')) {
                    $sql_association_table = 'editorial_lang';
                    $table = '`'._DB_PREFIX_.'editorial_lang`';
                    $table_alias = 'el';
                    $descriptions_field = array('body_paragraph');
                    $identifier_shop = '';
                    $identifier_field = 'id_editorial';
                    $shop_condition = '';
                    $shop_inclusion = self::addSqlAssociation($sql_association_table, $table_alias, $identifier_field, true, null, ($from_crontab ? 'all' : false), 'editorial');
                } elseif (Module::isInstalled('ps_customtext')) {
                    $sql_association_table = 'info';
                    $table = '`'._DB_PREFIX_.'info_lang`';
                    $table_alias = 'il';
                    $descriptions_field = array('text');
                    $identifier_shop = 'il.`id_shop`';
                    $identifier_field = 'id_info';
                    $shop_condition = '';
                    $shop_inclusion = '';
                } else {
                    return;
                }
                break;
            case 'cms':
                $sql_association_table = 'cms';
                $table = '`'._DB_PREFIX_.'cms_lang`';
                $table_alias = 'cmsl';
                $descriptions_field = array('content');
                $identifier_shop = '';
                $identifier_field = 'id_cms';
                $shop_condition = '';
                $shop_inclusion = self::addSqlAssociation($sql_association_table, $table_alias, $identifier_field, true, null, ($from_crontab ? 'all' : false));
                break;
            case 'categories':
                $sql_association_table = 'category';
                $table = '`'._DB_PREFIX_.'category_lang`';
                $table_alias = 'catl';
                $descriptions_field = array('description');
                $identifier_shop = 'catl.`id_shop`';
                $identifier_field = 'id_category';
                $shop_condition = self::addShopCondition($identifier_shop, $from_crontab);
                if (!empty($shop_condition)) {
                    $shop_condition = ' AND '. $shop_condition;
                }
                $shop_inclusion = '';
                break;
            case 'manufacturers':
                $sql_association_table = 'manufacturer';
                $table = '`'._DB_PREFIX_.'manufacturer_lang`';
                $table_alias = 'manl';
                $descriptions_field = array('description');
                $identifier_shop = '';
                $identifier_field = 'id_manufacturer';
                $shop_condition = '';
                $shop_inclusion = self::addSqlAssociation($sql_association_table, $table_alias, $identifier_field, true, null, ($from_crontab ? 'all' : false));
                break;
            case 'products':
            default:
                $sql_association_table = 'product';
                $table = '`'._DB_PREFIX_.'product_lang`';
                $table_alias = 'pl';
                $descriptions_field = array('description', 'description_short');
                $identifier_shop = 'pl.`id_shop`';
                $identifier_field = 'id_product';
                $shop_condition = self::addShopCondition($identifier_shop, $from_crontab);
                if (!empty($shop_condition)) {
                    $shop_condition = ' AND '. $shop_condition;
                }
                $shop_inclusion = '';
                break;
        }
        $exclude_headings = (bool)$this->_exclude_headings;
        $to_clean = array();
        $columns_description = array();
        $columns_condition = array();
        foreach ($descriptions_field as $description_field) {
            $columns_description[] = '`'.$description_field.'`';
            $columns_condition[] = '`'.$description_field.'` LIKE "%pmsil%"';
        }
        ini_set('memory_limit', $this->_big_sql_results_memory_limit);
        $sql = '
            SELECT DISTINCT '.(Shop::isFeatureActive() && !empty($identifier_shop) ? $identifier_shop.', ' : '').$table_alias.'.`'.$identifier_field.'`, '.$table_alias.'.`id_lang`, '.implode($columns_description, ', ').'
            FROM '.$table.' '.$table_alias.' '
            . $shop_inclusion . '
            WHERE ('.implode($columns_condition, ' OR ') . ') ' . $shop_condition;
        if ($field_type == 'products' && $id_identifier != null && is_numeric($id_identifier)) {
            $sql .= ' AND '.$table_alias.'.`id_product`='.(int)$id_identifier;
        } elseif ($field_type == 'categories' && $id_identifier != null && is_numeric($id_identifier)) {
            $sql .= ' AND '.$table_alias.'.`id_category`='.(int)$id_identifier;
        } elseif ($field_type == 'manufacturers' && $id_identifier != null && is_numeric($id_identifier)) {
            $sql .= ' AND '.$table_alias.'.`id_manufacturer`='.(int)$id_identifier;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        if ($result && is_array($result)) {
            foreach ($result as $key => $row) {
                foreach ($descriptions_field as $description_field) {
                    $row[$description_field] = $this->deleteAllLinks($row[$description_field]);
                    $sql_update = 'UPDATE '.$table.' SET '.$description_field.'="'.pSQL($row[$description_field], true).'" WHERE '.$identifier_field.'="'.pSQL($row[$identifier_field]).'" AND id_lang="'.pSQL($row['id_lang']).'"';
                    if (isset($row['id_shop'])) {
                        $sql_update .= ' AND id_shop="'.(int)$row['id_shop'].'"';
                    }
                    $result_update = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_update);
                }
            }
        }
    }
    public function hookCategoryUpdate($params)
    {
        $category = $params['category'];
        if ($category) {
            $this->updateCategoryDescription($category);
        }
        return false;
    }
    public function hookActionProductSave($params)
    {
        $product = new Product($params['id_product']);
        if ($product) {
            return $this->updateProductDescription($product);
        }
        return false;
    }
    public function hookActionObjectManufacturerAddAfter($params)
    {
        $manufacturer = $params['object'];
        if ($manufacturer) {
            return $this->updateManufacturerDescription($manufacturer);
        }
        return false;
    }
    public function hookActionObjectManufacturerUpdateAfter($params)
    {
        return $this->hookActionObjectManufacturerAddAfter($params);
    }
    public static function addSqlAssociation($table, $alias, $identifier, $inner_join = true, $on = null, $shops = false, $join_table = null, $active_check = false)
    {
        if (Shop::isFeatureActive()) {
            if ($join_table == null || empty($join_table)) {
                $join_table = _DB_PREFIX_.$table.'_shop';
            } else {
                $join_table = _DB_PREFIX_.$join_table;
            }
            if ($shops == 'all') {
                $ids_shop = array_values(Shop::getCompleteListOfShopsID());
            } elseif (is_array($shops) && sizeof($shops)) {
                $ids_shop = array_values($shops);
            } elseif (is_numeric($shops)) {
                $ids_shop = array($shops);
            } else {
                $ids_shop = array_values(Shop::getContextListShopID());
            }
            if ($active_check) {
                foreach ($ids_shop as $key_shop => $id_shop) {
                    if (!self::_isModuleActive('pm_seointernallinking', $id_shop)) {
                        unset($ids_shop[$key_shop]);
                    }
                }
                if (!sizeof($ids_shop)) {
                    $ids_shop = array(0);
                }
            }
            $table_alias = $alias.'_shop';
            if (strpos($table, '.') !== false) {
                list($table_alias, $table) = explode('.', $table);
            }
            $sql = (($inner_join) ? ' INNER' : ' LEFT').' JOIN `'.$join_table.'` '.$table_alias.'
                        ON '.$table_alias.'.'.$identifier.' = '.$alias.'.'.$identifier.'
                        AND '.$table_alias.'.id_shop IN ('.implode(', ', $ids_shop).') '
                        .(($on) ? ' AND '.$on : '');
            return $sql;
        }
        return;
    }
    public static function addShopCondition($identifier = 'id_shop', $shops = false)
    {
        if (Shop::isFeatureActive()) {
            if ($shops == 'all') {
                $ids_shop = array_values(Shop::getCompleteListOfShopsID());
            } elseif (is_array($shops) && sizeof($shops)) {
                $ids_shop = array_values($shops);
            } elseif (is_numeric($shops)) {
                $ids_shop = array($shops);
            } else {
                $ids_shop = array_values(Shop::getContextListShopID());
            }
            $sql = ' '.$identifier.' IN ('.implode(', ', $ids_shop).') ';
            return $sql;
        }
        return;
    }
    public static function addSqlGroupBy($alias, $identifier)
    {
        if (Shop::isFeatureActive()) {
            return ' GROUP BY '.$alias.'.'.$identifier.' ';
        }
        return;
    }
    public function showShopContextWarning()
    {
        if (Shop::isFeatureActive() && sizeof(Shop::getContextListShopID()) > 1) {
            return $this->_showWarning($this->l('You are working on more than one shop at the same time... be careful !'));
        }
    }
}
