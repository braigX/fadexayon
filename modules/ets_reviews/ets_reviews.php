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

require_once dirname(__FILE__) . '/classes/EtsRVSmartyCache.php';
require_once dirname(__FILE__) . '/classes/EtsRVCore.php';
require_once dirname(__FILE__) . '/classes/EtsRVTools.php';
require_once dirname(__FILE__) . '/classes/EtsRVLink.php';
require_once dirname(__FILE__) . '/classes/EtsRVModel.php';
require_once dirname(__FILE__) . '/classes/EtsRVEmailQueue.php';
require_once dirname(__FILE__) . '/classes/EtsRVTracking.php';
require_once dirname(__FILE__) . '/classes/EtsRVEmailTemplate.php';
require_once dirname(__FILE__) . '/classes/EtsRVStaff.php';
require_once dirname(__FILE__) . '/classes/EtsRVUnsubscribe.php';

require_once dirname(__FILE__) . '/classes/EtsRVProductCommentOrder.php';
require_once dirname(__FILE__) . '/classes/EtsRVProductCommentImage.php';
require_once dirname(__FILE__) . '/classes/EtsRVProductCommentVideo.php';
require_once dirname(__FILE__) . '/classes/EtsRVProductCommentCustomer.php';
require_once dirname(__FILE__) . '/classes/EtsRVProductCommentCriterion.php';
require_once dirname(__FILE__) . '/classes/EtsRVProductComment.php';
require_once dirname(__FILE__) . '/classes/EtsRVComment.php';
require_once dirname(__FILE__) . '/classes/EtsRVReplyComment.php';
require_once dirname(__FILE__) . '/classes/EtsRVCartRule.php';
require_once dirname(__FILE__) . '/classes/EtsRVMail.php';

require_once dirname(__FILE__) . '/src/Repository/EtsRVProductCommentRepository.php';
require_once dirname(__FILE__) . '/src/Repository/EtsRVProductCommentCriterionRepository.php';
require_once dirname(__FILE__) . '/src/Repository/EtsRVCommentRepository.php';
require_once dirname(__FILE__) . '/src/Repository/EtsRVReplyCommentRepository.php';
require_once dirname(__FILE__) . '/classes/EtsRVActivity.php';
require_once dirname(__FILE__) . '/classes/EtsRVDefines.php';

require_once dirname(__FILE__) . '/src/Entity/EtsRVEntity.php';
require_once dirname(__FILE__) . '/src/Entity/EtsRVProductCommentEntity.php';
require_once dirname(__FILE__) . '/src/Entity/EtsRVCommentEntity.php';
require_once dirname(__FILE__) . '/src/Entity/EtsRVReplyCommentEntity.php';
require_once dirname(__FILE__) . '/src/Entity/EtsRVActivityEntity.php';

class Ets_reviews extends Module
{
    const DEFAULT_MAX_SIZE = 104857600;
    const INSTALL_SQL_FILE = 'install.sql';
    const UNINSTALL_SQL_FILE = 'uninstall.sql';
    const TAB_PREFIX = 'AdminEtsRV';
    const DEFAULT_MAX_COLOR = 5;
    const _DIR_IMG_ = [
        'a',
        'r'
    ];
    public $is17 = 0;
    public $ps1760 = 0;
    public $backOffice;
    public $employee;
    public $secure_key;

    public function __construct()
    {
        $this->name = 'ets_reviews';
        $this->tab = 'front_office_features';
        $this->version = '2.5.2';
        $this->author = 'PrestaHero';
        $this->module_key = 'a5caad3049534ce42e31c97dd3ee9c7b';
        $this->need_instance = 0;
        $this->bootstrap = true;
        if (version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true;
        parent::__construct();
        $this->secure_key = Tools::encrypt($this->name);
        $this->displayName = $this->l('Product Reviews - Ratings, Google Snippets, Q&A');
        $this->description = $this->l('Fully managed reviews, ratings & FAQ system to make your product reviews editable, multi-languages and easy to manage.');
$this->refs = 'https://prestahero.com/';
        $this->ps_versions_compliancy = array('min' => '1.6.0', 'max' => _PS_VERSION_);
        $this->is17 = version_compare(_PS_VERSION_, '1.7', '>=') ? 1 : 0;
        $this->ps1760 = version_compare(_PS_VERSION_, '1.7.6.0', '>=');

        $this->employee = isset($this->context->employee->id) && $this->context->employee->id ? $this->context->employee->id : 0;
        $this->backOffice = $this->isBackOffice();
    }

    public function isBackOffice($id_customer = 0)
    {
        return $this->employee || $this->isStaffLogged($id_customer) ? 1 : 0;
    }

    public function isStaffLogged($id_customer = 0)
    {
        if (trim($id_customer) !== '' && !Validate::isUnsignedInt($id_customer))
            return false;

        if ($id_customer < 1 && $this->isCustomerLogged())
            $id_customer = $this->context->customer->id;

        return $id_customer > 0 && EtsRVProductCommentCustomer::isGrandStaff($id_customer);
    }

    public function install($keep = true)
    {
        Configuration::updateValue('ETS_RV_INSTALL_TIME', time());

        if (!@is_dir(_PS_IMG_DIR_ . $this->name))
            @mkdir(_PS_IMG_DIR_ . $this->name, 0755);

        if (self::_DIR_IMG_) {
            foreach (self::_DIR_IMG_ as $dir) {
                if (!@is_dir(_PS_IMG_DIR_ . $this->name . DIRECTORY_SEPARATOR . $dir))
                    @mkdir(_PS_IMG_DIR_ . $this->name . DIRECTORY_SEPARATOR . $dir, 0755);
            }
        }

        if (Module::isEnabled('productcomments'))
            Module::getInstanceByName('productcomments')->disable(true);
        if ($keep) {
            if (!EtsRVTools::executeSQL(self::INSTALL_SQL_FILE))
                return false;
        }

        EtsRVTools::getInstance()->initEmailTemplate();
        EtsRVStaff::initSupperAdmin();

        Configuration::updateValue('ETS_RV_FREE_DOWNLOADS_ENABLED', Module::isEnabled('ets_free_downloads') ? 1 : 0);

        self::_clearLogByCronjob();

        Configuration::deleteByName('PS_ROUTE_module-' . $this->name . '-ajax');
        Configuration::deleteByName('PS_ROUTE_module-' . $this->name . '-comment');
        Configuration::deleteByName('PS_ROUTE_module-' . $this->name . '-detail');
        Configuration::deleteByName('PS_ROUTE_module-' . $this->name . '-activity');
        Configuration::deleteByName('PS_ROUTE_module-' . $this->name . '-all');

        if (parent::install() == false ||
            !$this->registerHook('displayHome') || //Back Office
            !$this->registerHook('displayBackOfficeHeader') || //Back Office
            !$this->registerHook('displayFooterProduct') || //Product page footer
            !$this->registerHook('displayHeader') || //Adds css and javascript on front
            !$this->registerHook('displayProductListReviews') || //Product list miniature
            !$this->registerHook('displayProductAdditionalInfo') || //Display info in checkout column
            !$this->registerHook('displayRightColumnProduct') || //Display info in checkout column
            !$this->registerHook('registerGDPRConsent') ||
            !$this->registerHook('actionDeleteGDPRCustomer') ||
            !$this->registerHook('actionExportGDPRData') ||
            !$this->registerHook('productTab') ||
            !$this->registerHook('productTabContent') ||
            !$this->registerHook('actionObjectLanguageAddAfter') ||
            !$this->registerHook('displayFooter') ||
            !$this->registerHook('displayCustomerAccount') ||
            !$this->registerHook('displayCustomerAccountBlock') ||
            !$this->registerHook('moduleRoutes') ||
            !$this->registerHook('actionValidateOrder') ||
            !$this->registerHook('actionOrderStatusUpdate') ||
            !$this->registerHook('displayCustomETSReviews') ||
            !$this->registerHook('actionFrontControllerInitAfter') ||
            !$this->registerHook('actionObjectEmployeeAddAfter') ||
            !$this->registerHook('displayMicrodataAggregateRating') ||
            !$this->registerHook('displayCategoryAggregateRating') ||
            !$this->registerHook('actionObjectShopDeleteAfter') ||
            !$this->registerHook('actionObjectShopAddAfter') ||
            !$this->registerHook('filterProductContent') ||
            !$this->registerHook('displayProductActions') ||
            !$this->registerHook('displayProductPriceBlock') ||
            !$this->registerHook('displayReassurance') ||
            !$this->registerHook('displayCustomerAccountForm') ||

            !$this->_installConfigs() ||
            !$this->installQuickTabs() ||
            !$this->installRoutes() ||
            !$this->_copyMailTmp()
        ) {
            return false;
        }

        return true;
    }

    public function hookRegisterGDPRConsent($params)
    {
    }

    public function hookActionObjectEmployeeAddAfter($params)
    {
        if (isset($params['object']) && Validate::isLoadedObject($params['object']) && $params['object'] instanceof Employee && $params['object']->id_profile == _PS_ADMIN_PROFILE_) {
            EtsRVStaff::initSupperAdmin($params['object']->id);
        }
    }

    public function hookDisplayHome()
    {
        if ((int)Configuration::get('ETS_RV_DISPLAY_ON_HOME') && (int)Configuration::get('ETS_RV_REVIEW_ENABLED')) {
            if (!(int)Configuration::get('ETS_RV_SLICK_LIBRARY_DISABLED')) {
                $this->context->controller->addJS($this->_path . 'views/js/slick.js');
                $this->context->controller->addCSS([
                    $this->_path . 'views/css/slick.css',
                    $this->_path . 'views/css/slick-theme.css',
                ]);
            }
            $this->context->controller->addJS($this->_path . 'views/js/home.js');
            $cacheLifeTimeBefore = (int)Configuration::get('ETS_RV_CACHE_LIFETIME_BEFORE');
            $cacheLifeTime = Configuration::get('ETS_RV_CACHE_LIFETIME');
            if ($cacheLifeTime !== '' && ((time() - $cacheLifeTimeBefore) >= (int)$cacheLifeTime * 3600)) {
                $cacheLifeTimeBefore = time();
            } else {
                $cacheLifeTimeBefore = null;
            }
            if (($cache_id = $this->getCacheId('home', null, $cacheLifeTimeBefore)) == null || !$this->isCached('home.tpl', $cache_id)) {
                if ($cache_id !== null && $cacheLifeTimeBefore !== null) {
                    Configuration::updateValue('ETS_RV_CACHE_LIFETIME_BEFORE', $cacheLifeTimeBefore);
                    EtsRVSmartyCache::clearCacheFoSmarty('*', 'home');
                }
                $tpl_vars = [
                    'average_grade' => EtsRVProductComment::getAverageRate(),
                    'nb_reviews' => EtsRVProductComment::getNbReviews(),
                    'latest_reviews' => EtsRVProductComment::getLatestReviews(),
                    'ETS_RV_DESIGN_COLOR1' => Configuration::get('ETS_RV_DESIGN_COLOR1'),
                    'ETS_RV_DESIGN_COLOR2' => Configuration::get('ETS_RV_DESIGN_COLOR2'),
                    'ETS_RV_DESIGN_COLOR3' => Configuration::get('ETS_RV_DESIGN_COLOR3'),
                    'ETS_RV_DESIGN_COLOR4' => Configuration::get('ETS_RV_DESIGN_COLOR4'),
                    'ETS_RV_DESIGN_COLOR5' => Configuration::get('ETS_RV_DESIGN_COLOR5'),
                    'link_all_reviews' => $this->context->link->getModuleLink($this->name, 'all', [], Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')),
                ];
                $this->smarty->assign($tpl_vars);
            }
            return $this->display(__FILE__, 'home.tpl', $cache_id);
        }
    }

    public function clearCacheTime($cache_id, $date)
    {
        $path = _PS_CACHE_DIR_ . '/smarty/cache/' . str_replace('|', DIRECTORY_SEPARATOR, $cache_id) . DIRECTORY_SEPARATOR;
        if ($dirs = scandir($path)) {
            foreach ($dirs as $dir) {
                if (is_dir($path . $dir) && $dir != '.' && $dir != '..' && strtotime($dir) < strtotime($date)) {
                    $this->clearCache('*', $cache_id . '|' . $dir);
                    @rmdir($path . $dir);
                }
            }
        }
    }

    /**
     * @param Language $language
     * @return bool
     */
    public function _copyMailTmp($language = null)
    {
        if ($language !== null) {
            $this->_recurseCopy(dirname(__FILE__) . '/mails/en', dirname(__FILE__) . '/mails/' . $language->iso_code);
        } elseif ($languages = Language::getLanguages(false)) {
            foreach ($languages as $l) {
                $path_email = dirname(__FILE__) . '/mails/';
                if (!@file_exists($path_email . trim($l['iso_code'])) || !glob($path_email . trim($l['iso_code']) . '/*')) {
                    $this->_recurseCopy($path_email . 'en', $path_email . trim($l['iso_code']));
                }
            }
        }
        return true;
    }

    public function _recurseCopy($src, $dst)
    {
        if (!@file_exists($src)) {
            return false;
        }
        $dir = opendir($src);
        if (!@mkdir($dst)) {
            return false;
        }
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->_recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    @copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function cleanUploadImages()
    {
        if (self::_DIR_IMG_) {
            foreach (self::_DIR_IMG_ as $dir) {
                $this->removeTree(_PS_IMG_DIR_ . $this->name . DIRECTORY_SEPARATOR . $dir);
            }
        }
    }

    /**
     * Remove Directory and all Files
     * @param $dir
     * @return bool
     */
    public function removeTree($dir, $rmdir = true)
    {
        if (@is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                $each = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($each))
                    $this->removeTree($each);
                elseif (@file_exists($each))
                    @unlink($each);
            }
            if ($rmdir)
                @rmdir($dir);
        }

        return true;
    }

    public function uninstall($keep = true)
    {
        Configuration::deleteByName('ETS_RV_INSTALL_TIME');
        self::_clearLogByCronjob();
        $this->cleanUploadImages();

        Configuration::deleteByName('ETS_RV_FREE_DOWNLOADS_ENABLED');

        if (self::_DIR_IMG_) {
            foreach (self::_DIR_IMG_ as $dir) {
                if (@is_dir(_PS_IMG_DIR_ . $this->name . DIRECTORY_SEPARATOR . $dir))
                    @rmdir(_PS_IMG_DIR_ . $this->name . DIRECTORY_SEPARATOR . $dir);
            }
        }

        if (@is_dir(_PS_IMG_DIR_ . $this->name))
            @rmdir(_PS_IMG_DIR_ . $this->name);

        if (@file_exists(($dest = _PS_THEME_DIR_ . ($this->is17 ? 'assets' : '') . '/cache/productcomments.color.css')))
            @unlink($dest);

        Configuration::deleteByName('PS_ROUTE_module-' . $this->name . '-ajax');
        Configuration::deleteByName('PS_ROUTE_module-' . $this->name . '-comment');
        Configuration::deleteByName('PS_ROUTE_module-' . $this->name . '-detail');
        Configuration::deleteByName('PS_ROUTE_module-' . $this->name . '-activity');
        Configuration::deleteByName('PS_ROUTE_module-' . $this->name . '-all');
        $this->clearCache('*');

        if (!parent::uninstall() ||
            ($keep && !EtsRVTools::executeSQL(self::UNINSTALL_SQL_FILE)) ||
            !$this->_uninstallConfigs() ||
            !$this->uninstallQuickTabs() ||
            !$this->uninstallRoutes()
        ) {
            return false;
        }

        return true;
    }

    public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install(false)) {
            return false;
        }

        return true;
    }

    public function installQuickTabs()
    {
        $id_parent = $this->addQuickTab(
            0,
            '',
            'Product Reviews'
        );
        if ($id_parent && ($quick_tabs = EtsRVDefines::getInstance()->getQuickTabs())) {
            foreach ($quick_tabs as $t) {
                if (isset($t['class']) && isset($t['label']) && !($parent_id = $this->addQuickTab($id_parent, $t['class'], $t['origin'])))
                    return false;
                if (isset($t['sub']) && $t['sub'] && isset($parent_id) && $parent_id) {
                    foreach ($t['sub'] as $st) {
                        if (isset($st['class']) && isset($st['label']) && (!isset($st['tab']) || trim($st['tab']) === '') && !$this->addQuickTab($parent_id, $st['class'], $st['origin']))
                            return false;
                    }
                }
            }
        }

        return true;
    }

    public function uninstallQuickTabs()
    {
        if ($this->removeQuickTab()) {
            if ($quick_tabs = EtsRVDefines::getInstance()->getQuickTabs()) {
                foreach ($quick_tabs as $t) {
                    if (isset($t['class']) && !$this->removeQuickTab($t['class']))
                        return false;
                    if (isset($t['sub']) && $t['sub']) {
                        foreach ($t['sub'] as $st) {
                            if (isset($st['class']) && !$this->removeQuickTab($st['class']))
                                return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    public function addQuickTab($id_parent, $class = '', $label = '')
    {
        if ($id_parent && !$class)
            return 0;

        $class_name = trim(self::TAB_PREFIX . $class);
        $id = (int)Tab::getIdFromClassName($class_name);
        if ($id)
            return 0;
        $t = new Tab((int)$id);
        $t->active = 1;
        $t->class_name = $class_name;
        $t->name = array();
        if ($languages = Language::getLanguages(false)) {
            foreach ($languages as $l) {
                $t->name[$l['id_lang']] = EtsRVCore::trans($label, $l['iso_code'], 'EtsRVDefines') ?: $label;
            }
        }
        $t->id_parent = (int)$id_parent;
        $t->module = $this->name;

        return $t->save() ? $t->id : 0;
    }

    public function removeQuickTab($class_name = '')
    {
        $id = (int)Tab::getIdFromClassName(self::TAB_PREFIX . $class_name);
        if (!$id)
            return true;

        $tab = new Tab($id);

        return !$tab->id || $tab->delete();
    }

    public function getCacheId($name = null, $before = null, $after = null)
    {
        if (!(int)Configuration::get('ETS_RV_CACHE_ENABLED'))
            return null;
        $cache_id = $this->name . (trim(Tools::strtolower($name)) ? '|' . trim(Tools::strtolower($name)) : '') . (is_array($before) ? '|' . implode('|', $before) : ($before ? '|' . trim($before, '|') : ''));
        $cache_id = parent::getCacheId($cache_id);

        return $cache_id . (is_array($after) ? '|' . implode('|', $after) : ($after ? '|' . trim($after, '|') : ''));
    }

    public function _installConfigs($configs = [])
    {
        if (!$configs)
            $configs = EtsRVDefines::getInstance()->getALlConfigs();
        if ($configs) {
            $languages = Language::getLanguages(false);
            foreach ($configs as $config) {
                if (!isset($config['name']) || !$config['name'])
                    continue;
                $global = isset($config['global']) && $config['global'] ? 1 : 0;
                if (isset($config['lang']) && $config['lang']) {
                    $values = [];
                    foreach ($languages as $l) {
                        if (!empty($config['init_content_file']))
                            $values[$l['id_lang']] = $this->init_content_file(trim($config['name']), $l['iso_code']);
                        elseif (isset($config['default'])) {
                            if (is_array($config['default']) && count($config['default']) > 0)
                                $values[$l['id_lang']] = !empty($config['default']['og']) ? EtsRVTools::trans($config['default']['og'], $l['iso_code']) : '';
                            else
                                $values[$l['id_lang']] = $config['default'];

                        }
                    }
                    $this->configUpdateValue($config['name'], $global, $values, true);
                } else {
                    $this->configUpdateValue($config['name'], $global, isset($config['default']) ? $config['default'] : '', true);
                }
            }
        }
        Configuration::updateGlobalValue('ETS_RV_IMPORT_PRESTASHOP', 0);
        Configuration::updateGlobalValue('ETS_RV_MAX_ID_IMPORT_PRESTASHOP', 0);
        return true;
    }

    public function init_content_file($key, $iso_code)
    {
        $source_file = dirname(__FILE__) . '/views/init/%s/' . Tools::strtolower($key) . '.html';
        if (@file_exists(sprintf($source_file, $iso_code)))
            return Tools::file_get_contents(sprintf($source_file, $iso_code));
        return file_exists(sprintf($source_file, 'en')) ? Tools::file_get_contents(sprintf($source_file, 'en')) : '';
    }

    public function _uninstallConfigs()
    {
        $configs = EtsRVDefines::getInstance()->getALlConfigs();
        if ($configs) {
            foreach ($configs as $config) {
                if (!isset($config['name']) || !$config['name'])
                    continue;
                Configuration::deleteByName($config['name']);
            }
        }

        Configuration::deleteByName('ETS_RV_IMPORT_PRESTASHOP');
        Configuration::deleteByName('ETS_RV_EMAIL_NOTIFICATIONS');

        return true;
    }

    public function configUpdateValue($key, $global, $values, $html = false)
    {
        return $global ? Configuration::updateGlobalValue($key, $values, $html) : Configuration::updateValue($key, $values, $html);
    }

    public function getContent()
    {
        if (!Configuration::getGlobalValue('ETS_RV_IMPORT_PRESTASHOP')
            && Module::isEnabled('productcomments')
            && EtsRVTools::hasProductComments()
        ) {
            Configuration::updateGlobalValue('ETS_RV_IMPORT_PRESTASHOP', 1);
            $this->smarty->assign(
                array(
                    'link_import' => EtsRVLink::getAdminLink(self::TAB_PREFIX . 'ImportExport', true, [], [], $this->context),
                    'link_review' => EtsRVLink::getAdminLink(self::TAB_PREFIX . 'Reviews', true, [], [], $this->context),
                )
            );
            return $this->display(__FILE__, 'prestashop_comment.tpl');
        } else {
            Configuration::updateGlobalValue('ETS_RV_IMPORT_PRESTASHOP', 1);
            Tools::redirectAdmin(EtsRVLink::getAdminLink(self::TAB_PREFIX . 'Reviews', true, [], [], $this->context));
        }
    }

    public function findProductId($product)
    {
        return $product && is_object($product) && method_exists($product, 'getId') ? $product->getId() : (is_object($product) && property_exists($product, 'id') ? $product->id : (is_array($product) && isset($product['id_product']) ? (int)$product['id_product'] : 0));
    }

    public function hookActionObjectShopDeleteAfter($params)
    {
        if (isset($params['object']) && $params['object'] instanceof Shop && (int)$params['object']->id > 0) {
            EtsRVEmailTemplate::deleteByIdShop((int)$params['object']->id);
        }
    }

    public function hookActionObjectShopAddAfter($params)
    {
        if (isset($params['object']) && $params['object'] instanceof Shop && (int)$params['object']->id > 0) {
            EtsRVEmailTemplate::duplicateByIdShop((int)$params['object']->id);
        }
    }

    public function hookActionValidateOrder($params)
    {
        if (trim(Configuration::get('ETS_RV_EMAIL_TO_CUSTOMER_ORDER_STATUS')) == 'new'
            && !empty($params['order'])
            && $params['order'] instanceof Order
            && Validate::isLoadedObject($params['order'])
        ) {
            $this->invitationEmail($params['order']);
        }
    }

    public function hookActionOrderStatusUpdate($params)
    {
        $orderStatusId = !empty($params['newOrderStatus']) && $params['newOrderStatus'] instanceof OrderState && Validate::isLoadedObject($params['newOrderStatus']) ? (int)$params['newOrderStatus']->id : 0;
        $orderId = !empty($params['id_order']) ? (int)$params['id_order'] : 0;

        if (trim(Configuration::get('ETS_RV_EMAIL_TO_CUSTOMER_ORDER_STATUS')) == 'validated'
            && $orderStatusId > 0
            && $orderId > 0
            && ($verify_purchase = Configuration::get('ETS_RV_VERIFY_PURCHASE'))
        ) {
            $order_status = explode(',', $verify_purchase);
            if (in_array($orderStatusId, $order_status)) {
                $order = new Order((int)$params['id_order']);
                $this->invitationEmail($order);
            }
        }
    }

    public function checkCartRuleValidity()
    {
        if (Tools::getValue('controller') == 'cart'
            && Tools::isSubmit('addDiscount')//1.7
            && Tools::isSubmit('ajax')
            && !Tools::getIsset('fc')
            && !Tools::getIsset('module')
            && !Configuration::get('ETS_RV_USE_OTHER_VOUCHER_SAME_CART')
        ) {
            $code = trim(Tools::getValue('discount_name'));
            $error = $this->checkValidityVoucher($code, null, $this->context);
            if ($error !== '') {
                die(json_encode(array(
                    'errors' => array($error),
                    'hasError' => true,
                    'quantity' => null,
                )));
            }
        }
    }

    public function checkValidityVoucher($code, $error = null, $context = null)
    {
        if ($context == null)
            $context = Context::getContext();
        if ($code !== '' && !Validate::isCleanHtml($code)) {
            $error = $this->l('Your voucher code is invalid');
        } else {
            if (Module::isEnabled('ets_promotion') && EtsRVCartRule::getCartRuleByPromotion($code))
                return $error;
            if ($id_cart_rule = CartRule::getIdByCode($code)) {
                $voucherCode = null;
                if (!EtsRVCartRule::canUseCartRule($context->cart->id, $id_cart_rule, $voucherCode)) {
                    $error = sprintf($this->l('Cannot use voucher code %s with other voucher codes'), $voucherCode);
                }
            } else {
                $error = $this->l('Your voucher code does not exist');
            }
        }

        return $error;
    }

    public function hookActionFrontControllerInitAfter()
    {
        $this->checkCartRuleValidity();
    }

    public function invitationEmail(Order $order)
    {
        if ((int)Configuration::get('ETS_RV_SEND_RATING_INVITATION') < 1 || !(EtsRVEmailTemplate::isEnabled('tocustomer_rating_invitation') || EtsRVEmailTemplate::isEnabled('tocustomer_rating_invitation_getvoucher')) || EtsRVTracking::getTrackingByOrderId($order->id)) {
            return false;
        }
        $customer = new Customer((int)$order->id_customer);
        if (!$customer->id || $customer->is_guest && !EtsRVTools::reviewGrand('guest') || !$customer->is_guest && !EtsRVTools::reviewGrand('purchased')) {
            return false;
        }
        $exclude_id_product = (int)Configuration::getGlobalValue('PH_EXTEND_ID_PRODUCT');
        $languageObj = new Language($customer->id_lang);
        $idLang = $languageObj->id ?: $this->context->language->id;
        $customer_name = $customer->firstname . ' ' . $customer->lastname;

        $products = [];
        $first_product_name = null;

        if ($order_products = $order->getProducts()) {
            foreach ($order_products as $product) {
                if ($exclude_id_product == (int)$product['product_id'])
                    continue;
                $product = new Product((int)$product['product_id'], false, $idLang);
                $image = Product::getCover($product->id);
                $products[] = [
                    'link' => $this->context->link->getProductLink($product, $product->link_rewrite, $product->category, $product->ean13, $idLang),
                    'name' => trim($product->name),
                    'image' => $this->context->link->getImageLink($product->link_rewrite, isset($image['id_image']) ? $image['id_image'] : 0, EtsRVTools::getFormattedName('cart')),
                ];
                if ($first_product_name == null)
                    $first_product_name = $product->name;
            }
        }
        if ($exclude_id_product > 0 && !count($products))
            return false;

        $templateVars = [
            '{customer_name}' => $customer_name,
            '{product_list}' => $products,
            '{product_name}' => $first_product_name,
            '{rate_url}' => $this->context->link->getModuleLink($this->name, 'comment', ['current_tab' => 'waiting_for_review']),
        ];

        if (Configuration::get('ETS_RV_DISCOUNT_ENABLED') && EtsRVEmailTemplate::isEnabled('tocustomer_rating_invitation_getvoucher')) {
            $discount_option = Configuration::get('ETS_RV_DISCOUNT_OPTION');
            $apply_discount = Configuration::get('ETS_RV_APPLY_DISCOUNT');
            $free_shipping = Configuration::get('ETS_RV_FREE_SHIPPING');
            $discount_value = '';
            if ($discount_option == 'auto') {
                switch ($apply_discount) {
                    case 'percent':
                        $discount_value = Configuration::get('ETS_RV_REDUCTION_PERCENT') . '%';
                        break;
                    case 'amount':
                        $discount_value = Tools::displayPrice((float)Configuration::get('ETS_RV_REDUCTION_AMOUNT'), Currency::getCurrencyInstance((int)Configuration::get('ETS_RV_MINIMUM_AMOUNT_CURRENCY')));
                        break;
                }
            } else {
                $cart_rule = new CartRule(CartRule::getIdByCode(Configuration::get('ETS_RV_DISCOUNT_CODE')));
                if ($cart_rule->id) {
                    if ($cart_rule->reduction_percent) {
                        $discount_value = $cart_rule->reduction_percent . '%';
                    } elseif ($cart_rule->reduction_amount) {
                        $discount_value = Tools::displayPrice($cart_rule->reduction_amount, Currency::getCurrencyInstance($cart_rule->reduction_currency));
                    }
                }
            }
            $templateVars['{voucher_value}'] = $discount_value != '' ? sprintf($this->l('with discount of %s'), $discount_value) . ' ' . ($free_shipping ? $this->l('and free shipping') : '') : ($free_shipping ? $this->l('of free shipping') : '');
            return EtsRVMail::send(
                $idLang
                , 'tocustomer_rating_invitation_getvoucher'
                , null
                , $templateVars
                , $customer->email
                , $customer_name
                , true
                , $customer->id
                , 0
                , 0
                , $this->context->shop->id
                , 0
                , isset($cart_rule) && $cart_rule->id > 0 ? $cart_rule->id : 0
                , $order->id
                , true
            );
        }

        if (EtsRVEmailTemplate::isEnabled('tocustomer_rating_invitation')) {
            return EtsRVMail::send(
                $idLang
                , 'tocustomer_rating_invitation'
                , null
                , $templateVars
                , $customer->email
                , $customer_name
                , true
                , $customer->id
                , 0
                , 0
                , $this->context->shop->id
                , 0
                , 0
                , $order->id
                , true
            );
        }
    }

    public function installRoutes($upgrade = false)
    {
        $pages = [
            'comment' => [
                'l' => $this->l('My reviews'),
                'og' => 'My reviews',
                'url_rewrite' => 'my-review',
            ],
            'activity' => [
                'l' => $this->l('Activities'),
                'og' => 'Activities',
                'url_rewrite' => 'review-activities'
            ],
            'all' => [
                'l' => $this->l('All reviews'),
                'og' => 'All reviews',
                'url_rewrite' => 'all-reviews'
            ]
        ];
        $languages = Language::getLanguages(false);
        foreach ($pages as $page => $title) {
            $page_name = 'module-' . $this->name . '-' . $page;
            if (!Meta::getMetaByPage($page_name, $this->context->language->id)) {
                $meta = new Meta();
                $meta->page = $page_name;
                foreach ($languages as $l) {
                    $meta->title[(int)$l['id_lang']] = EtsRVTools::trans($title['og'], $l['iso_code']);
                    $meta->url_rewrite[(int)$l['id_lang']] = Tools::str2url($title['url_rewrite']);
                }
                $meta->add();
            } elseif ($upgrade) {
                $id_meta = (int)EtsRVTools::getMetaIdByPageName($page_name);
                if ($id_meta > 0) {
                    $meta = new Meta($id_meta);
                    foreach ($languages as $l) {
                        $meta->title[(int)$l['id_lang']] = EtsRVTools::trans($title['og'], $l['iso_code']);
                        $meta->url_rewrite[(int)$l['id_lang']] = Tools::str2url($title['url_rewrite']);
                    }
                    $meta->update();
                }
            }
        }

        return true;
    }

    public function uninstallRoutes()
    {
        $pages = ['comment', 'activity', 'all'];
        foreach ($pages as $page => $title) {
            $id_meta = (int)EtsRVTools::getMetaIdByPageName('module-' . $this->name . '-' . $page);
            if ($id_meta > 0) {
                $meta = new Meta($id_meta);
                $meta->delete();
            }
        }
        return true;
    }

    public function hookModuleRoutes()
    {
        $routes = array(
            'module-' . $this->name . '-ajax' => array(
                'controller' => 'comment',
                'rule' => 'module/' . $this->name . '/comment',
                'keywords' => [],
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                ),
            ),
        );
        if ($page_comment = Meta::getMetaByPage('module-' . $this->name . '-comment', $this->context->language->id)) {
            $routes['module-' . $this->name . '-detail'] = [
                'controller' => 'comment',
                'rule' => $page_comment['url_rewrite'] . '/{id_product_comment}-{id_product}',
                'keywords' => [
                    'id_product_comment' => ['regexp' => '[0-9]+', 'param' => 'id_product_comment'],
                    'id_product' => ['regexp' => '[0-9]+', 'param' => 'id_product'],
                ],
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                ),
            ];
        }
        return $routes;
    }

    static $per_pages = [
        20,
        50,
        100,
        300,
        1000
    ];

    static $cache_review_tabs = [];

    public function reviewTabs($id_customer = 0)
    {
        if ($id_customer <= 0 && isset($this->context->customer) && $this->context->customer->id) {
            $id_customer = (int)$this->context->customer->id;
        }
        if (!self::$cache_review_tabs) {
            self::$cache_review_tabs = [];
            if ((int)Configuration::get('ETS_RV_REVIEW_ENABLED')) {
                self::$cache_review_tabs['waiting_for_review'] = array(
                    'title' => $this->l('Waiting for review'),
                    'class' => 'ets_rv_waiting_for_review',
                    'link' => $this->context->link->getModuleLink($this->name, 'comment', ['current_tab' => 'waiting_for_review'])
                );
                self::$cache_review_tabs['my_review'] = array(
                    'title' => $this->l('My reviews'),
                    'class' => 'ets_rv_my_review',
                    'link' => $this->context->link->getModuleLink($this->name, 'comment', ['current_tab' => 'my_review']),
                );
            }
            if ((int)Configuration::get('ETS_RV_QUESTION_ENABLED')) {
                self::$cache_review_tabs['my_question'] = array(
                    'title' => $this->l('My questions'),
                    'class' => 'ets_rv_my_question',
                    'link' => $this->context->link->getModuleLink($this->name, 'comment', ['current_tab' => 'my_question']),
                );
            }
            self::$cache_review_tabs['activity'] = [
                'title' => $this->l('Activities'),
                'class' => 'ets_rv_my_activity',
                'link' => $this->context->link->getModuleLink($this->name, 'activity'),
            ];
            if (EtsRVProductCommentCustomer::isGrandStaff($id_customer)) {
                if ((int)Configuration::get('ETS_RV_REVIEW_ENABLED')) {
                    self::$cache_review_tabs['manager_review'] = array(
                        'title' => $this->l('Manage reviews'),
                        'class' => 'ets_rv_manager_review',
                        'link' => $this->context->link->getModuleLink($this->name, 'comment', ['current_tab' => 'manager_review']),
                    );
                }
                if ((int)Configuration::get('ETS_RV_QUESTION_ENABLED')) {
                    self::$cache_review_tabs['manager_question'] = array(
                        'title' => $this->l('Manage questions'),
                        'class' => 'ets_rv_manager_question',
                        'link' => $this->context->link->getModuleLink($this->name, 'comment', ['current_tab' => 'manager_question']),
                    );
                }
            }
        }
        return self::$cache_review_tabs;
    }

    static $st_products = [];

    public function hookDisplayCustomerAccountBlock($params)
    {
        if ($this->isCustomerLogged() && ((int)Configuration::get('ETS_RV_REVIEW_ENABLED') || (int)Configuration::get('ETS_RV_QUESTION_ENABLED'))) {
            $id_product = (int)Tools::getValue('id_product');
            $current_tab = trim(Tools::getValue('current_tab', Tools::getValue('back')));

            if (!$current_tab || !Validate::isCleanHtml($current_tab)) {
                $current_tab = 'waiting_for_review';
            }
            $idCustomer = isset($this->context->customer) && $this->context->customer->id && $this->context->customer->isLogged() ? $this->context->customer->id : 0;
            if ((trim($current_tab) == 'manager_review' || trim($current_tab) == 'manager_question') && ($idCustomer <= 0 || !EtsRVProductCommentCustomer::isGrandStaff($idCustomer))) {
                if ($idCustomer <= 0)
                    Tools::redirect($this->context->link->getPageLink('my-account'));
                else
                    Tools::redirect($this->context->link->getModuleLink($this->name, 'comment', ['current_tab' => (trim($current_tab) == 'manager_review' ? 'my_review' : 'my_question')]));
            }
            $assigns = array(
                'href' => $this->context->link->getModuleLink($this->name, 'comment'),
                'current_tab' => $current_tab,
                'tabs' => $this->reviewTabs(),
                'ETS_RV_DESIGN_COLOR2' => Configuration::get('ETS_RV_DESIGN_COLOR2'),
            );
            if ($id_product) {
                $id_product_comment = (int)Tools::getValue('id_product_comment');
                $productComment = new EtsRVProductComment($id_product_comment);
                if (!$productComment->id) {
                    Tools::redirect($this->context->link->getPageLink('my-account'));
                }
                $qa = (int)Tools::getValue('qa') || $productComment->question ? 1 : 0;
                $product = new Product($id_product, true, $this->context->language->id);
                if ($product->id > 0) {
                    $product->link = $this->context->link->getProductLink($product, $product->link_rewrite, $product->category, $product->ean13, $this->context->language->id);
                }
                $params = [
                    'id_product' => $id_product,
                    'id_product_comment' => $id_product_comment
                ];
                $assigns = array_merge($assigns, array(
                    'qa' => $qa,
                    'product' => $product,
                    'id_product_comment' => $id_product_comment,
                    'ets_rv_product' => $this->productModal($id_product),
                    'list' => $qa ? $this->displayProductQuestionsList($params) : $this->displayProductCommentsList($params),
                ));
            } else {
                $min_per_page = min(self::$per_pages);
                $page = Tools::getValue('page');
                if (!$page || !Validate::isUnsignedInt($page)) {
                    $page = 1;
                }
                $per_page = Tools::getValue('per_page');
                if (!$per_page || !Validate::isUnsignedInt($per_page)) {
                    $per_page = $min_per_page;
                }
                if (trim($current_tab) == 'waiting_for_review') {
                    $maximum_product_comment = trim(Configuration::get('ETS_RV_MAXIMUM_REVIEW_PER_USER'));
                    $purchasedInTime = EtsRVTools::isCustomerPurchased() && (int)Configuration::get('ETS_RV_REVIEW_AVAILABLE_TIME') > 0 ? (int)Configuration::get('ETS_RV_REVIEW_AVAILABLE_TIME') : 0;
                    $order_states = Configuration::get('ETS_RV_VERIFY_PURCHASE');
                    if (trim($order_states) !== '') {
                        $order_states = explode(',', $order_states);
                        $assigns['order_states'] = EtsRVProductComment::getOrderStateByIds($order_states);
                    }
                    $orders = EtsRVProductComment::getOrders($this->context->customer->id, 0, $page, $per_page, $this->context, $maximum_product_comment, $purchasedInTime);
                    if ($orders) {
                        foreach ($orders as &$order) {
                            $order['image_link'] = $order['id_image'] > 0 ? $this->context->link->getImageLink($order['link_rewrite'], $order['id_image'], EtsRVTools::getFormattedName('cart')) : '';
                            $order['product_link'] = $this->context->link->getProductLink((new Product($order['id_product'])), $order['link_rewrite'], null, null, $this->context->language->id);
                            $order['purchased'] = isset($order['current_state']) && is_array($order_states) && in_array($order['current_state'], $order_states);
                        }
                    }
                    $total_records = EtsRVProductComment::getOrders($this->context->customer->id, 1, 0, 0, $this->context, $maximum_product_comment, $purchasedInTime);
                    $assigns['total_records'] = $total_records;
                    $assigns['orders'] = $orders;
                    $assigns['ETS_RV_DESIGN_COLOR2'] = Configuration::get('ETS_RV_DESIGN_COLOR2');
                    $assigns['ETS_RV_DESIGN_COLOR3'] = Configuration::get('ETS_RV_DESIGN_COLOR3');
                } else {
                    $question = trim($current_tab) == 'my_question' || trim($current_tab) == 'manager_question' ? 1 : 0;
                    $isGrandStaff = (trim($current_tab) == 'manager_review' || trim($current_tab) == 'manager_question') && EtsRVProductCommentCustomer::isGrandStaff($idCustomer);
                    $productComments = EtsRVProductComment::getList($this->context->customer->id, $this->context->language->id, 0, $page, $per_page, $question, $isGrandStaff);
                    if ($productComments) {
                        foreach ($productComments as &$productComment) {
                            $productComment['link'] = $this->context->link->getModuleLink($this->name, 'detail', ['id_product_comment' => (int)$productComment['id_ets_rv_product_comment'], 'id_product' => (int)$productComment['id_product']]);
                            if (isset($productComment['id_product']) && $productComment['id_product'] > 0) {
                                if (!isset(self::$st_products[$productComment['id_product']])) {
                                    $p = new Product($productComment['id_product'], false, $this->context->language->id);
                                    $cover = Product::getCover($p->id, $this->context);
                                    $productComment['image_link'] = isset($cover['id_image']) && $cover['id_image'] > 0 ? $this->context->link->getImageLink($p->link_rewrite, $cover['id_image'], EtsRVTools::getFormattedName('cart')) : '';
                                } else
                                    $productComment['image_link'] = self::$st_products[$productComment['id_product']];
                            } else {
                                $productComment['id_image'] = null;
                            }
                        }
                    }
                    $total_records = EtsRVProductComment::getList($this->context->customer->id, $this->context->language->id, true, 0, 0, $question, $isGrandStaff);
                    $assigns['total_records'] = $total_records;
                    $assigns['productComments'] = $productComments;
                    $assigns['fields_list'] = EtsRVDefines::getInstance()->getFieldsList($question, true);
                    $assigns['isGrandStaff'] = $isGrandStaff;
                }
                $paginates = EtsRVLink::getPagination($this->name, 'comment', $total_records, $page, $per_page, array('current_tab' => $current_tab), 7, $this->context);
                $per_pages = array();
                if (self::$per_pages) {
                    foreach (self::$per_pages as $n) {
                        $per_pages[$n] = $this->context->link->getModuleLink($this->name, 'comment', array('page' => 1, 'per_page' => $n, 'current_tab' => $current_tab));
                    }
                }

                $assigns = array_merge($assigns, array(
                    'paginates' => $paginates,
                    'per_pages' => $per_pages,
                    'current_per_page' => $per_page,
                    'show_footer_btn' => $min_per_page > 0 && ceil($total_records / $min_per_page) > 1,
                ));
            }

            $this->smarty->assign($assigns);

            return $this->display(__FILE__, 'front-comments-list.tpl');
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function hookFilterProductContent($params)
    {
        if (true || empty($params['object']->id) || version_compare(_PS_VERSION_, '1.7.8.0', '<')) {
            return $params;
        }
        $productCommentRepository = EtsRVProductCommentRepository::getInstance();
        $validateOnly = $this->validateOnly();

        $averageGrade = $productCommentRepository->getAverageGrade($params['object']->id, $this->context->language->id, $validateOnly, $this->backOffice, $this->context);
        $commentsNb = $productCommentRepository->getCommentsNumber($params['object']->id, $this->context->language->id, 0, $validateOnly, $this->backOffice, 0, $this->context);

        $params['object']->productComments = [
            'averageRating' => $averageGrade,
            'nbComments' => $commentsNb,
        ];

        return $params;
    }

    public function hookProductTab($params)
    {
        if (!$this->is17 || !(int)Configuration::get('ETS_RV_REVIEW_ENABLED') && !Configuration::get('ETS_RV_QUESTION_ENABLED'))
            return '';
        $product = $params['product'];
        $idProduct = $this->findProductId($product);
        $repository = EtsRVProductCommentRepository::getInstance();
        $validateOnly = $this->validateOnly();

        $nbComment = $repository->getCommentsNumber($idProduct, $this->context->language->id, 0, $validateOnly, $this->backOffice, 0, $this->context);
        $nbQA = $repository->getCommentsNumber($idProduct, $this->context->language->id, 0, $validateOnly, $this->backOffice, 0, $this->context, 1);

        $this->context->smarty->assign([
            'nbComment' => $nbComment,
            'nbQA' => $nbQA,
        ]);

        return $this->display(__FILE__, 'product-tab.tpl');
    }

    public function hookDisplayCustomerAccount()
    {
        if (!isset($this->context->customer->id) || !$this->context->customer->id || !(int)Configuration::get('ETS_RV_REVIEW_ENABLED'))
            return '';
        $this->smarty->assign(array(
            'link' => $this->context->link->getModuleLink($this->name, 'comment'),
            'is17' => $this->is17
        ));

        return $this->display(__FILE__, 'front-block.tpl');
    }

    public function hookActionObjectLanguageAddAfter($params)
    {
        if (isset($params['object']) && Validate::isLoadedObject($params['object'])) {
            EtsRVProductComment::updateNewLanguage((int)$params['object']->id);
            $this->_copyMailTmp($params['object']);
        }
    }

    public function hookActionDeleteGDPRCustomer($customer)
    {
        if (isset($customer['id'])) {
            EtsRVProductCommentRepository::getInstance()->cleanCustomerData($customer['id']);
            EtsRVCommentRepository::getInstance()->cleanCustomerData($customer['id']);
            EtsRVReplyCommentRepository::getInstance()->cleanCustomerData($customer['id']);
        }

        return true;
    }

    public function hookActionExportGDPRData($customer)
    {
        if (isset($customer['id'])) {
            $langId = isset($customer['id_lang']) ? $customer['id_lang'] : $this->context->language->id;

            return json_encode(EtsRVProductCommentRepository::getInstance()->getCustomerData($customer['id'], $langId));
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $controller = Tools::getValue('controller');
        //die($controller);
        if (($controller == 'AdminEtsRVReviewsRatings' || $controller == 'AdminEtsRVReplies' || $controller == 'AdminEtsRVActivity' || $controller == 'AdminEtsRVReviews' || $controller == 'AdminEtsRVComments')) {
            $this->context->controller->addCSS($this->_path . 'views/css/review-media.css');
        }
        $css_files = [
            $this->_path . 'views/css/admin_all.css'
        ];
        if ($this->context->controller instanceof AdminEtsRVBaseController) {
            $this->addJss(true);
        }
        if (trim(Tools::getValue('configure')) == $this->name) {
            $css_files[] = $this->_path . 'views/css/productcomments.admin.css';
        }
        $this->context->controller->addCSS($css_files);
        $this->context->smarty->assign([
            'ETS_RV_REVIEW_LINK' => EtsRVLink::getAdminLink(self::TAB_PREFIX . 'Reviews', true, [], [], $this->context),
            'ETS_RV_ACTIVITY_LINK' => EtsRVLink::getAdminLink(self::TAB_PREFIX . 'Activity', true, [], [], $this->context),
        ]);
        Media::addJsDef([
            'ETS_RV_DELETE_TITLE' => $this->l('Delete'),
            'ETS_RV_CLEAN_LOG_CONFIRM' => $this->l('Do you want to clear all mail logs?')
        ]);
        return $this->display(__FILE__, 'admin-head.tpl');
    }

    public function hookDisplayHeader()
    {
        $controller = Tools::getValue('controller');
        $js_files = [];
        $css_files = [
            '/modules/' . $this->name . '/views/css/productcomments.all.css',
        ];
        if (!$this->is17) {
            $css_files[] = '/modules/' . $this->name . '/views/css/productcomment_16_all.css';
        }
        if ($controller == 'index' && (int)Configuration::get('ETS_RV_DISPLAY_ON_HOME') && (int)Configuration::get('ETS_RV_REVIEW_ENABLED')) {
            $css_files[] = '/modules/' . $this->name . '/views/css/review_home.css';
        }
        if ($controller == 'activity') {
            $css_files[] = '/modules/' . $this->name . '/views/css/review_activity.css';
        }
        if ($controller == 'identity') {
            $css_files[] = '/modules/' . $this->name . '/views/css/review_identity.css';
        }
        if ($controller == 'myaccount') {
            $css_files[] = '/modules/' . $this->name . '/views/css/review_my-account.css';
        }
        $html = '';
        if ($this->context->controller instanceof ProductControllerCore ||
            $this->context->controller instanceof Ets_reviewsCommentModuleFrontController ||
            $this->context->controller instanceof Ets_reviewsActivityModuleFrontController ||
            $this->context->controller instanceof Ets_reviewsAllModuleFrontController
        ) {
            $this->context->controller->addJqueryUI('ui.datepicker');
            $html .= $this->addJss(true);
            if (Configuration::get('ETS_RV_UPLOAD_PHOTO_ENABLED') || Configuration::get('ETS_RV_UPLOAD_VIDEO_ENABLED') || Configuration::get('ETS_RV_DISPLAY_ALL_PHOTO')) {
                $css_files[] = '/modules/' . $this->name . '/views/css/review-media.css';
            }
            if (!(int)Configuration::get('ETS_RV_SLICK_LIBRARY_DISABLED')) {
                $css_files[] = '/modules/' . $this->name . '/views/css/slick-theme.css';
                $css_files[] = '/modules/' . $this->name . '/views/css/slick.css';
                $js_files[] = '/modules/' . $this->name . '/views/js/slick.js';
            }
            $js_files = array_merge($js_files, [
                '/modules/' . $this->name . '/views/js/modal.js',
                'js/jquery/plugins/growl/jquery.growl.js',
                'js/jquery/plugins/fancybox/jquery.fancybox.js',
                '/modules/' . $this->name . '/views/js/jquery.rating.plugin.js',
                '/modules/' . $this->name . '/views/js/function.js',
                '/modules/' . $this->name . '/views/js/list-comments.js',
                '/modules/' . $this->name . '/views/js/front.js'
            ]);
            $css_files = array_merge($css_files, [
                '/js/jquery/plugins/growl/jquery.growl.css',
                '/modules/' . $this->name . '/views/css/productcomments.css',
            ]);
            if (!$this->is17)
                $css_files[] = '/modules/' . $this->name . '/views/css/productcomment_16.css';
        } elseif ($this->context->controller instanceof IdentityController) {
            $js_files = array_merge([
                '/js/jquery/plugins/growl/jquery.growl.js',
                '/modules/' . $this->name . '/views/js/front.js'
            ]);
            $css_files = array_merge($css_files, [
                '/js/jquery/plugins/growl/jquery.growl.css',
            ]);
        }
        $physical_uri = rtrim($this->context->shop->physical_uri, '/');
        if ($css_files) {
            foreach ($css_files as $cssUrl) {
                if ($this->is17 && method_exists($this->context->controller, 'registerStylesheet'))
                    $this->context->controller->registerStylesheet(sha1($cssUrl), $cssUrl, ['media' => 'all', 'priority' => 80]);
                else
                    $this->context->controller->addCSS($physical_uri . $cssUrl, 'all');
            }
        }
        if ($js_files) {
            foreach ($js_files as $jsUrl) {
                if ($this->is17 && method_exists($this->context->controller, 'registerJavascript'))
                    $this->context->controller->registerJavascript(sha1($jsUrl), $jsUrl, ['position' => 'bottom', 'priority' => 80]);
                else
                    $this->context->controller->addJS($physical_uri . $jsUrl);
            }
        }
        if ($this->context->controller instanceof ProductControllerCore ||
            $this->context->controller instanceof Ets_reviewsCommentModuleFrontController ||
            $this->context->controller instanceof Ets_reviewsActivityModuleFrontController ||
            $this->context->controller instanceof IndexController ||
            $this->context->controller instanceof Ets_reviewsAllModuleFrontController)
            $html .= $this->generateColor();

        return $html;
    }

    public function generateColor()
    {
        if (($cache_id = $this->getCacheId('color')) == null || !$this->isCached('color-css.tpl', $cache_id)) {
            $colorCss = Tools::file_get_contents($this->getLocalPath() . 'views/css/productcomments.color.css');
            if (trim($colorCss) !== '') {
                $colors = $this->getColors();
                if (count($colors) > 0)
                    for ($loop = 1; $loop <= self::DEFAULT_MAX_COLOR; $loop++)
                        $colorCss = str_replace('#00000' . $loop, $colors['ETS_RV_DESIGN_COLOR' . $loop], $colorCss);
            }
            $this->smarty->assign('colorCss', $colorCss);
        }
        return $this->display(__FILE__, 'color-css.tpl', $cache_id);
    }

    public function hookDisplayCustomerAccountForm($params)
    {
        if (!empty($this->context->customer->id) && $this->context->controller instanceof IdentityController) {
            $this->smarty->assign(array(
                'avatar' => $this->context->customer->id ? EtsRVProductCommentCustomer::getAvatarByIdCustomer($this->context->customer->id) : '',
                'upload_dir' => $this->getMediaLink(_PS_IMG_ . $this->name . '/a/'),
                'upload_url' => $this->context->link->getModuleLink($this->name, 'upload', ['id_customer' => (int)$this->context->customer->id], Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')),
            ));
            return $this->display(__FILE__, 'footer.tpl');
        }
    }

    public function hookDisplayFooter($params)
    {
    }

    public function getMediaLink($path)
    {
        return $this->employee ? $path : $this->context->link->getMediaLink($path);
    }

    public function hookDisplayFooterProduct($params)
    {
        if ($this->is17) {
            return $this->hookDisplayFrontend($params['product']);
        }
    }

    public function hookDisplayRightColumnProduct($params)
    {
        if (($this->is17 || trim(Tools::getValue('controller')) == 'product' && !(int)Tools::getValue('content_only')) && trim(Configuration::get('ETS_RV_AVERAGE_RATE_POSITION')) == 'product_price') {
            return $this->hookDisplayProductAdditionalInfo(['id_product' => (int)Tools::getValue('id_product'), 'hook' => 'product_price']);
        }
    }

    public function hookDisplayCustomETSReviews()
    {
        if ((trim(Tools::getValue('controller')) == 'product' && !(int)Tools::getValue('content_only')) && trim(Configuration::get('ETS_RV_AVERAGE_RATE_POSITION')) == 'custom') {
            return $this->hookDisplayProductAdditionalInfo(['id_product' => (int)Tools::getValue('id_product'), 'hook' => 'custom']);
        }
    }

    public function hookDisplayProductActions()
    {
        if ((trim(Tools::getValue('controller')) == 'product' && !(int)Tools::getValue('content_only')) && trim(Configuration::get('ETS_RV_AVERAGE_RATE_POSITION')) == 'add_to_cart') {
            return $this->hookDisplayProductAdditionalInfo(['id_product' => (int)Tools::getValue('id_product'), 'hook' => 'add_to_cart']);
        }
    }

    public function hookDisplayProductPriceBlock($params)
    {
        if (!empty($params['type']) && $params['type'] == 'after_price' && (trim(Tools::getValue('controller')) == 'product' && !(int)Tools::getValue('content_only')) && trim(Configuration::get('ETS_RV_AVERAGE_RATE_POSITION')) == 'product_price') {
            return $this->hookDisplayProductAdditionalInfo(['id_product' => (int)Tools::getValue('id_product'), 'hook' => 'product_price']);
        }
    }

    public function hookDisplayReassurance()
    {
        if ((trim(Tools::getValue('controller')) == 'product' && !(int)Tools::getValue('content_only')) && trim(Configuration::get('ETS_RV_AVERAGE_RATE_POSITION')) == 'product_reassurance') {
            return $this->hookDisplayProductAdditionalInfo(['id_product' => (int)Tools::getValue('id_product'), 'hook' => 'product_reassurance']);
        }
    }

    public function hookProductTabContent($params)
    {
        return $this->hookDisplayFrontend($params['product']);
    }

    public function getGradeStats($idProduct, $force_smarty = false)
    {
        $validateOnly = $this->validateOnly();

        $repository = EtsRVProductCommentRepository::getInstance();

        //Reviews:
        $averageGrade = $repository->getAverageGrade($idProduct, $this->context->language->id, $validateOnly, $this->backOffice, $this->context, 0);
        $reviewHasImageVideoNb = $repository->getCommentsNumber($idProduct, $this->context->language->id, 0, $validateOnly, $this->backOffice, 0, $this->context, 0, true);
        $gradesNb = $repository->getGradesNumber($idProduct, $this->context->language->id, $validateOnly, $this->backOffice, $this->context);

        $stats = array(
            '5' => array(
                'id' => 'excellent',
                'name' => $this->l('Excellent')
            ),
            '4' => array(
                'id' => 'good',
                'name' => $this->l('Good')
            ),
            '3' => array(
                'id' => 'medium',
                'name' => $this->l('Medium')
            ),
            '2' => array(
                'id' => 'poor',
                'name' => $this->l('Poor')
            ),
            '1' => array(
                'id' => 'terrible',
                'name' => $this->l('Terrible')
            ),
        );

        foreach ($stats as $grade => &$stat) {
            $gradeNbItem = (int)$repository->getGradesNumber($idProduct, $this->context->language->id, $validateOnly, $this->backOffice, $this->context, $grade);
            $stat['grade_percent'] = $gradesNb > 0 ? $gradeNbItem * 100 / $gradesNb : 0;
            $stat['grade_total'] = $gradeNbItem;
        }
        $array_result = [
            'average_grade' => $averageGrade,
            'grade_stats' => $stats,
            'nb_reviewHasImageVideo' => $reviewHasImageVideoNb,
            'has_video_image' => Configuration::get('ETS_RV_UPLOAD_PHOTO_ENABLED') || Configuration::get('ETS_RV_UPLOAD_VIDEO_ENABLED'),
        ];
        if (!$force_smarty) {
            $array_result['nb_reviews'] = $repository->getCommentsNumber($idProduct, $this->context->language->id, 0, $validateOnly, $this->backOffice, 0, $this->context, 0);
            $array_result['nb_questions'] = $repository->getCommentsNumber($idProduct, $this->context->language->id, 0, $this->validateOnly(1), $this->backOffice, 0, $this->context, 1);
            return $array_result;
        }

        $this->smarty->assign($array_result);
    }

    public function getReCaptchaConfigs()
    {
        return array(
            'ETS_RV_RECAPTCHA_TYPE' => ($recaptcha_type = Configuration::get('ETS_RV_RECAPTCHA_TYPE')),
            'ETS_RV_RECAPTCHA_ENABLED' => (int)Configuration::get('ETS_RV_RECAPTCHA_ENABLED'),
            'ETS_RV_RECAPTCHA_SITE_KEY' => Configuration::get('ETS_RV_RECAPTCHA_SITE_KEY_V' . ($recaptcha_type != 'recaptcha_v3' ? '2' : '3')),
            'ETS_RV_RECAPTCHA_FOR' => Configuration::get('ETS_RV_RECAPTCHA_FOR') ? explode(',', Configuration::get('ETS_RV_RECAPTCHA_FOR')) : array(),
            'ETS_RV_RECAPTCHA_USER_REGISTERED' => (int)Configuration::get('ETS_RV_RECAPTCHA_USER_REGISTERED'),
        );
    }

    public function getCurrentCustomer($force_smarty = false)
    {
        if ($this->employee) {
            $info = EtsRVStaff::getInfos($this->employee);
        } elseif (isset($this->context->customer) && $this->context->customer->id > 0) {
            $info = EtsRVProductCommentCustomer::getCustomer($this->context->customer->id);
        }
        if (isset($info['avatar']) && trim($info['avatar']) !== '')
            $profile_photo = $info['avatar'];
        if (isset($info['display_name']) && trim($info['display_name']) !== '')
            $customer_name = $info['display_name'];

        if (!isset($profile_photo) || trim($profile_photo) == '')
            $profile_photo = $this->employee ? null : ($this->context->customer->id ? EtsRVProductCommentCustomer::getAvatarByIdCustomer($this->context->customer->id) : null);
        if ($profile_photo !== null && trim($profile_photo) !== '')
            $profile_photo = $this->getMediaLink(_PS_IMG_ . $this->name . '/a/' . $profile_photo);

        if (!isset($customer_name) || trim($customer_name) == '')
            $customer_name = $this->employee ? ($this->context->employee->id ? $this->context->employee->firstname . ' ' . $this->context->employee->lastname : $this->l('Administrator')) : (isset($this->context->customer->id) && $this->context->customer->id ? $this->context->customer->firstname . ' ' . $this->context->customer->lastname : $this->l('Guest'));
        $tpl_vars = [
            'profile_photo' => $profile_photo,
            'customer_name' => $customer_name,
            'my_account_link' => $this->context->link->getPageLink('identity', true),
        ];
        if (!$force_smarty)
            return $tpl_vars;
        $this->smarty->assign($tpl_vars);
    }

    public function hookDisplayFrontend($product)
    {
        $idProduct = $this->findProductId($product);
        if (!$idProduct)
            return '';
        $repo = EtsRVProductCommentRepository::getInstance();
        $validateOnly = $this->validateOnly();
        $commentsNb = $repo->getCommentsNumber($idProduct, $this->context->language->id, 0, $validateOnly, $this->backOffice, 0, $this->context);
        $questionsNb = $repo->getCommentsNumber($idProduct, $this->context->language->id, 0, $this->validateOnly(1), $this->backOffice, 0, $this->context, 1);
        $isPostAllowed = $this->backOffice ?: $repo->isPostAllowed($idProduct, (int)$this->context->cookie->id_customer, (int)$this->context->cookie->id_guest);
        $maximum_review = Configuration::get('ETS_RV_MAXIMUM_REVIEW_PER_USER');
        $default_sort_by = trim(Configuration::get('ETS_RV_DEFAULT_SORT_BY')) ?: 'date_add.desc';
        $default_sort_by_question = trim(Configuration::get('ETS_RV_QA_DEFAULT_SORT_BY')) ?: 'date_add.desc';
        $commentUrl = $this->context->link->getModuleLink($this->name, 'ajax', $idProduct ? array('id_product' => $idProduct) : array(), Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
        $params = ['id_product' => $idProduct, 'nb_reviews' => $commentsNb, 'nb_questions' => $questionsNb];
        $tpl_vars = [
            'commentUrl' => $commentUrl,
            'id_product' => $idProduct,
            'back_office' => $this->backOffice,
            'sort_by' => EtsRVDefines::getInstance()->getSortBy(),
            'sort_by_question' => EtsRVDefines::getInstance()->getSortByQuestion(),
            'post_allowed' => $isPostAllowed,
            'question_enabled' => ($question_enabled = (int)Configuration::get('ETS_RV_QUESTION_ENABLED')),
            'review_enabled' => ($review_enabled = (int)Configuration::get('ETS_RV_REVIEW_ENABLED')),
            'nb_comments' => $commentsNb,
            'nb_reviews' => $commentsNb,
            'nb_questions' => $questionsNb,
            'employee' => $this->employee,
            'review_allowed' => trim($maximum_review) === '' || (int)$maximum_review > EtsRVProductComment::getNbReviewsOfUser($idProduct, $this->context),
            'default_sort_by' => $default_sort_by,
            'default_sort_by_info' => EtsRVDefines::getInstance()->getSortBy($default_sort_by),
            'default_sort_by_question' => $default_sort_by_question,
            'default_sort_by_question_info' => EtsRVDefines::getInstance()->getSortByQuestion($default_sort_by_question),
            'PRODUCT_COMMENTS_LIST' => $review_enabled ? $this->displayProductCommentsList($params) : '',
            'PRODUCT_QUESTIONS_LIST' => $question_enabled ? $this->displayProductQuestionsList($params) : '',
            'ETS_RV_DISPLAY_RATE_AND_QUESTION' => Configuration::get('ETS_RV_DISPLAY_RATE_AND_QUESTION'),
        ];
        $tpl_vars['ETS_RV_UPLOAD_PHOTO_ENABLED'] = (int)Configuration::get('ETS_RV_UPLOAD_PHOTO_ENABLED');
        $tpl_vars['ETS_RV_UPLOAD_VIDEO_ENABLED'] = (int)Configuration::get('ETS_RV_UPLOAD_VIDEO_ENABLED');

        $this->getGradeStats($idProduct, true);
        $this->getCurrentCustomer(true);
        $this->smarty->assign($tpl_vars);
        $this->getColors(true);
        $this->displayAllPhotos($idProduct);

        return $this->display(__FILE__, 'product-wrap.tpl');
    }

    public function displayAllPhotos($idProduct, $return = false)
    {
        if (!$idProduct || !Validate::isUnsignedInt($idProduct)) {
            return '';
        }
        $tpl_vars = [];
        $tpl_vars['ETS_RV_DISPLAY_ALL_PHOTO'] = (int)Configuration::get('ETS_RV_DISPLAY_ALL_PHOTO');
        $tpl_vars['ETS_RV_PHOTOS_OF_PRODUCT'] = EtsRVProductComment::getAllImages($idProduct, $this->context);
        $tpl_vars['ETS_RV_UPLOAD_PHOTO_ENABLED'] = (int)Configuration::get('ETS_RV_UPLOAD_PHOTO_ENABLED');
        $tpl_vars['ETS_RV_UPLOAD_VIDEO_ENABLED'] = (int)Configuration::get('ETS_RV_UPLOAD_VIDEO_ENABLED');
        $tpl_vars['photo_path_uri'] = $this->getMediaLink(_PS_IMG_ . $this->name . '/r/');
        $this->smarty->assign($tpl_vars);
        if ($return)
            return $this->display(__FILE__, 'all-photo.tpl');
    }

    public function getColors($force_smarty = false)
    {
        $colors = [];
        for ($ik = 1; $ik <= self::DEFAULT_MAX_COLOR; $ik++)
            $colors['ETS_RV_DESIGN_COLOR' . $ik] = Configuration::get('ETS_RV_DESIGN_COLOR' . $ik);
        if (!$force_smarty)
            return $colors;
        $this->smarty->assign($colors);
    }

    public function displayProductCommentsList($params)
    {
        $idProduct = !empty($params['id_product']) ? (int)$params['id_product'] : 0;
        $qa = !empty($params['qa']) ? 1 : 0;
        if ($qa && !(int)Configuration::get('ETS_RV_QUESTION_ENABLED') || !$qa && !(int)Configuration::get('ETS_RV_REVIEW_ENABLED'))
            return '';
        if ($this->context->controller instanceof AdminEtsRVBaseController)
            $url_params = $params;
        else
            $url_params = ['id_product' => $idProduct];
        if ($qa) {
            $url_params['qa'] = $qa;
        }
        $sf = ($qa ? 'QA_' : '');
        $id_product_comment = isset($params['id_product_comment']) ? (int)$params['id_product_comment'] : 0;
        if ($id_product_comment)
            $url_params['id_product_comment'] = (int)$id_product_comment;
        $refreshController = isset($params['refreshController']) && $params['refreshController'] !== '' ? $params['refreshController'] : false;
        if ($refreshController) {
            $url_params['refreshController'] = $refreshController;
        }
        $ssl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        $commentUrl = $this->context->link->getModuleLink($this->name, 'ajax', $url_params, $ssl);

        $tpl_vars = array(
            'reviews_initial' => (int)Configuration::get('ETS_RV_' . $sf . 'REVIEWS_INITIAL') ?: 1,
            'reviews_per_page' => (int)Configuration::get('ETS_RV_' . $sf . 'REVIEWS_PER_PAGE') ?: 5,
            'comments_initial' => (int)Configuration::get('ETS_RV_' . $sf . 'COMMENTS_INITIAL') ?: 1,
            'comments_per_page' => (int)Configuration::get('ETS_RV_' . $sf . 'COMMENTS_PER_PAGE') ?: 5,
            'replies_initial' => (int)Configuration::get('ETS_RV_' . $sf . 'REPLIES_INITIAL') ?: 1,
            'replies_per_page' => (int)Configuration::get('ETS_RV_' . $sf . 'REPLIES_PER_PAGE') ?: 5,
            'comment_url' => $this->backOffice && $this->employee ? EtsRVLink::getAdminLink(self::TAB_PREFIX . 'Reviews', true, [], $url_params, $this->context) : $commentUrl,
            'qa_comment_url' => $this->backOffice && $this->employee ? EtsRVLink::getAdminLink(self::TAB_PREFIX . 'Questions', true, [], $url_params, $this->context) : $commentUrl,
            'back_office' => $this->backOffice,
            'qa' => $qa,
            'id_product_comment' => $id_product_comment,
            'employee' => $this->employee,
            'nb_reviews' => isset($params['nb_reviews']) ? $params['nb_reviews'] : 0,
            'nb_questions' => isset($params['nb_questions']) ? $params['nb_questions'] : 0,
        );
        if ($this->employee || $id_product_comment) {
            $tpl_vars['nb_' . ($qa ? 'question' : 'review') . 's'] = EtsRVProductCommentRepository::getInstance()->getCommentsNumber($idProduct, $this->context->language->id, 0, $this->validateOnly(), $this->backOffice, 0, $this->context, $qa);
            $this->getCurrentCustomer(true);
            $this->getColors(true);
        }
        $this->smarty->assign($tpl_vars);
        return $this->display(__FILE__, 'product-comments-list.tpl');
    }

    public function displayProductQuestionsList($params)
    {
        $params['qa'] = true;
        return $this->displayProductCommentsList($params);
    }

    public function hookDisplayVerifyPurchase($params)
    {
        $id_product = isset($params['id_product']) && $params['id_product'] > 0 ? $params['id_product'] : (int)Tools::getValue('id_product', 0);
        if ($id_product > 0 && (int)Configuration::get('ETS_RV_FREE_DOWNLOADS_ENABLED')) {
            $product = new Product($id_product, true, $this->context->language->id);
            $params['free_product'] = $product->id > 0 && $product->price <= 0;
        }
        $prop = isset($params['prop']) && trim($params['prop']) !== '' ? $params['prop'] : '';
        $attrs = [
            'class' => $prop . '-order-status verify_label purchased' . (!empty($params['ETS_RV_DESIGN_COLOR4']) ? 'color4' : ''),
        ];
        return EtsRVTools::displayText('@VERIFY_PURCHASE@', 'span', $attrs);
    }

    public function renderTemplateModal($params)//hookRenderTemplateModal
    {
        $idProduct = isset($params['id_product']) && Validate::isUnsignedInt($params['id_product']) ? (int)$params['id_product'] : 0;
        $multiLang = (int)Configuration::get('ETS_RV_MULTILANG_ENABLED');
        $languages = Language::getLanguages(false);
        $ssl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        $commentUrl = $this->context->link->getModuleLink($this->name, 'ajax', $idProduct ? array('id_product' => $idProduct) : array(), $ssl);
        $url_params = array();
        $refreshController = isset($params['refreshController']) && $params['refreshController'] !== '' ? $params['refreshController'] : false;
        if ($refreshController) {
            $url_params['refreshController'] = $refreshController;
            EtsRVProductCommentEntity::getInstance()->extraParams($url_params);
        } elseif ($this->context->controller instanceof AdminEtsRVBaseController) {
            if (Tools::isSubmit('submitFilter' . $this->context->controller->list_id)) {
                $url_params['submitFilter' . $this->context->controller->list_id] = (int)Tools::getValue('submitFilter' . $this->context->controller->list_id);
            } elseif (Tools::getValue('page')) {
                $url_params['submitFilter' . $this->context->controller->list_id] = (int)Tools::getValue('page');
            }
        }
        $fields_form = [
            'content' => array(
                'type' => 'textarea',
                'name' => $this->l('Content'),
                'lang' => $multiLang,
                'required' => true,
                'col' => 10,
            )
        ];
        if ($this->employee)
            $fields_form['date_add'] = [
                'type' => 'datetime',
                'name' => $this->l('Date'),
                'required' => true,
                'default' => date('Y-m-d H:i:s'),
                'form_group_class' => 'date_add',
            ];
        $tpl_vars = array(
            'comment_url' => $this->backOffice && $this->employee ? EtsRVLink::getAdminLink(self::TAB_PREFIX . 'Reviews', true, array(), $url_params, $this->context) : $commentUrl,
            'qa_comment_url' => $this->backOffice && $this->employee ? EtsRVLink::getAdminLink(self::TAB_PREFIX . 'Questions', true, array(), $url_params, $this->context) : $commentUrl,
            'usefulness_enabled' => (int)Configuration::get('ETS_RV_USEFULNESS'),
            'ETS_RV_SHOW_DATE_ADD' => (int)Configuration::get('ETS_RV_SHOW_DATE_ADD') || $this->backOffice,
            'ETS_RV_QA_SHOW_DATE_ADD' => Configuration::get('ETS_RV_QA_SHOW_DATE_ADD') || $this->backOffice,
            'allow_delete_comment' => (int)Configuration::get('ETS_RV_ALLOW_DELETE_COMMENT') || $this->backOffice,
            'allow_edit_comment' => (int)Configuration::get('ETS_RV_ALLOW_EDIT_COMMENT') || $this->backOffice,
            'qa_usefulness_enabled' => Configuration::get('ETS_RV_QA_USEFULNESS'),
            'qa_allow_delete_comment' => (int)Configuration::get('ETS_RV_QA_ALLOW_DELETE_COMMENT') || $this->backOffice,
            'qa_allow_edit_comment' => (int)Configuration::get('ETS_RV_QA_ALLOW_EDIT_COMMENT') || $this->backOffice,
            'upload_dir' => $this->getMediaLink($this->_path . 'views/img/flag/'),
            'back_office' => $this->backOffice,
            'multilang_enabled' => $multiLang,
            'languages' => $languages,
            'defaultFormLanguage' => (int)Configuration::get('PS_LANG_DEFAULT'),
            'form_fields' => $fields_form,
            'press_enter_enabled' => (int)Configuration::get('ETS_RV_PRESS_ENTER_ENABLED'),
            'question_enabled' => (int)Configuration::get('ETS_RV_QUESTION_ENABLED') && (!isset($params['no_qa']) || !$params['no_qa']),
            'review_enabled' => (int)Configuration::get('ETS_RV_REVIEW_ENABLED') || $this->employee,
            'discount_enabled' => (int)Configuration::get('ETS_RV_DISCOUNT_ENABLED'),
            'show_comment_box' => (int)Configuration::get('ETS_RV_SHOW_COMMENT_BOX'),
            'show_reply_box' => (int)Configuration::get('ETS_RV_SHOW_REPLY_BOX'),
            'employee' => $this->employee,
            'logged' => $this->backOffice ?: (bool)$this->context->cookie->id_customer,
            'qa_show_comment_box' => (int)Configuration::get('ETS_RV_QA_SHOW_COMMENT_BOX'),
            'show_answer_box' => (int)Configuration::get('ETS_RV_SHOW_ANSWER_BOX'),
            'date_format' => str_replace(['d', 'm', 'Y'], ['dd', 'mm', 'yy'], $this->context->language->date_format_lite),
            'guest' => (!isset($this->context->customer->id) || !$this->context->customer->id) && isset($this->context->cookie->id_guest) && $this->context->cookie->id_guest,
            'product_id' => $idProduct,
            'photo_enabled' => (int)Configuration::get('ETS_RV_UPLOAD_PHOTO_ENABLED'),
            'video_enabled' => (int)Configuration::get('ETS_RV_UPLOAD_VIDEO_ENABLED'),
        );
        $this->smarty->assign($tpl_vars);
        $this->getColors(true);
        return $this->display(__FILE__, 'product-comment-modal.tpl');
    }

    public function productModal($idProduct)
    {
        if (!$idProduct ||
            !Validate::isUnsignedInt($idProduct)) {
            return 0;
        }
        $cover = Product::getCover($idProduct, $this->context);

        $p = new Product($idProduct, true, $this->context->language->id);
        $p->image = new Image($cover ? (int)$cover['id_image'] : 0, $this->context->language->id);
        $p->image->url = $this->context->link->getImageLink($p->link_rewrite, $p->image->id_image, EtsRVTools::getFormattedName('large'));
        $p->link = $this->context->link->getProductLink($p, $p->link_rewrite, $p->category, $p->ean13, $this->context->language->id);
        $p->description_short = Tools::truncateString($p->description_short);

        return $p;
    }

    public function isCustomerLogged()
    {
        return isset($this->context->customer) && $this->context->customer->id > 0 && $this->context->customer->isLogged();
    }

    public function configsModal($idProduct, $qa = 0)
    {
        $product = $this->productModal($idProduct);
        $requestUri = Tools::getValue('currentUrl', $_SERVER['REQUEST_URI']);
        if (!$this->isCustomerLogged() && !preg_match('/(\?|&)' . ($qa ? 'ets_rv_add_question' : 'ets_rv_add_review') . '\s*=\s*1/i', $requestUri)) {
            $requestUri .= (strpos('?', $requestUri) === false ? '?' : '&') . ($qa ? 'ets_rv_add_question=1' : 'ets_rv_add_review=1');
        }
        $tpl_vars = [
            'link' => $this->context->link,
            'product_modal' => $product,
            'currentUrl' => (Validate::isAbsoluteUrl($requestUri) ? '' : Tools::getCurrentUrlProtocolPrefix() . $_SERVER['HTTP_HOST']) . $requestUri,
            'moderation_active' => (!$qa
                && (
                    !(int)Configuration::get('ETS_RV_MODERATE') ||
                    (int)Configuration::get('ETS_RV_PURCHASED_PRODUCT_APPROVE') && EtsRVProductComment::verifyPurchase($idProduct, $this->context->customer->id)
                ) || $qa && !(int)Configuration::get('ETS_RV_QA_MODERATE')
            ),
            'logged' => $this->isCustomerLogged(),
            'is_block_customer' => $this->isCustomerLogged() && EtsRVProductCommentCustomer::isBlockByIdCustomer($this->context->customer->id),
            'ETS_RV' . ($qa ? '_QA' : '') . '_ALLOW_GUESTS' => $qa ? (int)Configuration::get('ETS_RV_QA_ALLOW_GUESTS') : EtsRVTools::reviewGrand('guest'),
            'ETS_RV_DISPLAY_PRODUCT_INFO' => (int)Configuration::get('ETS_RV_DISPLAY_PRODUCT_INFO'),
        ];
        if (!$qa) {

            $maximumRating = Configuration::get('ETS_RV_MAXIMUM_RATING_PER_USER');
            $nbRated = EtsRVProductComment::getNbReviewsOfUser($idProduct, $this->context, true);

            $freeDownload = (int)Configuration::get('ETS_RV_FREE_DOWNLOADS_ENABLED');
            $purchasedAvailableTime = (int)Configuration::get('ETS_RV_REVIEW_AVAILABLE_TIME');

            $customerPurchasedTime = EtsRVTools::isCustomerPurchased() && $purchasedAvailableTime > 0;
            $purchased = $this->isCustomerLogged() && EtsRVProductComment::isPurchased($this->context->customer->id, $idProduct);
            $purchasedTime = $this->isCustomerLogged() && $customerPurchasedTime && EtsRVProductComment::getLastOrderValid($this->context->customer->id, $idProduct, $purchasedAvailableTime);

            $tpl_vars = array_merge($tpl_vars, [
                'nbRated' => $nbRated,
                'allowRating' => trim($maximumRating) === '' || (int)$maximumRating > $nbRated,
                'orderNotValid' => $this->isCustomerLogged() && EtsRVProductComment::isPurchased($this->context->customer->id, $idProduct, false),
                'purchasedAvailableTime' => $purchasedAvailableTime,
                'purchasedInTime' => $customerPurchasedTime,
                'purchased' => $purchased,
                'purchasedTime' => $purchasedTime || !$customerPurchasedTime && $purchased,
                'freeDownload' => $freeDownload,
                'productPrice' => $product->price,
                'maximumRating' => (int)$maximumRating,
                'ETS_RV_ALLOW_GUESTS_RATE' => EtsRVTools::ratingGrand('guest'),
                'ETS_RV_PURCHASED_PRODUCT' => EtsRVTools::reviewGrand('purchased'),
                'ETS_RV_PURCHASED_PRODUCT_RATE' => EtsRVTools::ratingGrand('purchased'),
                'ETS_RV_CUSTOMER' => EtsRVTools::reviewGrand('no_purchased'),
                'ETS_RV_CUSTOMER_INCL' => EtsRVTools::reviewGrand('no_purchased_incl'),
                'ETS_RV_CUSTOMER_EXCL' => EtsRVTools::reviewGrand('no_purchased_excl'),
                'ETS_RV_CUSTOMER_RATE_INCL' => EtsRVTools::ratingGrand('no_purchased_incl'),
                'ETS_RV_CUSTOMER_RATE_EXCL' => EtsRVTools::ratingGrand('no_purchased_excl'),
                'ETS_RV_CUSTOMER_RATE' => EtsRVTools::ratingGrand('no_purchased'),
                'ETS_RV_DEFAULT_RATE' => (int)Configuration::get('ETS_RV_DEFAULT_RATE'),
            ]);
        }

        return $tpl_vars;
    }

    public function renderProductCommentModal($params)
    {
        $idProduct = !empty($params['id_product']) ? (int)$params['id_product'] : 0;
        $criterions = EtsRVProductCommentCriterionRepository::getInstance()->getByProduct($idProduct, $this->context->language->id);
        $maximum_review = trim(Configuration::get('ETS_RV_MAXIMUM_REVIEW_PER_USER'));

        $tpl_vars = array_merge(
            $this->configsModal($idProduct),
            [
                'post_product_comment_url' => $this->context->link->getModuleLink($this->name, 'ajax', ['id_product' => $idProduct, '__ac' => 'post_product_comment'], Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')),
                'criterions' => $criterions,
                'ETS_RV_REQUIRE_TITLE' => (int)Configuration::get('ETS_RV_REQUIRE_TITLE'),
                'review_allowed' => $maximum_review === '' || (int)$maximum_review > EtsRVProductComment::getNbReviewsOfUser($idProduct, $this->context),
                'maximum_review_per_user' => $maximum_review,
            ]
        );
        $id_order = isset($params['id_order']) && Validate::isUnsignedInt($params['id_order']) ? (int)$params['id_order'] : 0;
        $tpl_vars['id_order'] = $id_order;

        $this->context->smarty->assign($tpl_vars);

        return $this->display(__FILE__, 'post-comment-modal.tpl');
    }

    public function renderProductQuestionModal($params)
    {
        $idProduct = !empty($params['id_product']) ? (int)$params['id_product'] : 0;
        $tpl_vars = $this->configsModal($idProduct, 1);
        $tpl_vars['post_product_question_url'] = $this->context->link->getModuleLink($this->name, 'ajax', ['id_product' => $idProduct, '__ac' => 'post_product_question', 'qa' => 1], Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
        $this->context->smarty->assign($tpl_vars);

        return $this->display(__FILE__, 'post-question-modal.tpl');
    }

    public function renderUploadImage($params = [])
    {
        $id_product_comment = isset($params['product_comment_id']) && (int)$params['product_comment_id'] ? (int)$params['product_comment_id'] : 0;
        $productComment = new EtsRVProductComment((int)$id_product_comment);
        if (!$productComment->question) {
            $count = EtsRVProductCommentImage::getImages($id_product_comment, true);
            $this->context->smarty->assign(array(
                'ETS_RV_UPLOAD_PHOTO_ENABLED' => $count ? 1 : (int)Configuration::get('ETS_RV_UPLOAD_PHOTO_ENABLED'),
                'ETS_RV_MAX_UPLOAD_PHOTO' => max($count, (int)Configuration::get('ETS_RV_MAX_UPLOAD_PHOTO')),
                'ETS_RV_UPLOAD_VIDEO_ENABLED' => $count ? 1 : (int)Configuration::get('ETS_RV_UPLOAD_VIDEO_ENABLED'),
                'ETS_RV_MAX_UPLOAD_VIDEO' => max($count, (int)Configuration::get('ETS_RV_MAX_UPLOAD_VIDEO')),
                'PS_ATTACHMENT_MAXIMUM_SIZE' => EtsRVTools::formatBytes(EtsRVTools::getPostMaxSizeBytes()),
            ));
            return $this->display(__FILE__, 'product-comment-images.tpl');
        }
    }

    public function addJss($addJsDef = false)
    {
        $isLogged = $this->backOffice ?: $this->isCustomerLogged();
        $vars = [
            'language_code' => $this->context->language->iso_code,
            'ETS_RV_PRESS_ENTER_ENABLED' => (int)Configuration::get('ETS_RV_PRESS_ENTER_ENABLED'),
            'isLogged' => $isLogged,
            'ETS_RV_CUSTOMER_IS_LOGGED' => $isLogged,
            'back_office' => $this->backOffice,
            'ETS_RV_REQUIRE_TITLE' => (int)Configuration::get('ETS_RV_REQUIRE_TITLE'),
            'ETS_RV_DEFAULT_RATE' => (int)Configuration::get('ETS_RV_DEFAULT_RATE'),
            'ETS_RV_DESIGN_COLOR1' => (int)Configuration::get('ETS_RV_DESIGN_COLOR1'),
            'ETS_RV_QUESTION_ENABLED' => (int)Configuration::get('ETS_RV_QUESTION_ENABLED'),
            'ETS_RV_REVIEW_ENABLED' => (int)Configuration::get('ETS_RV_REVIEW_ENABLED'),
            'PS_ATTACHMENT_MAXIMUM_SIZE' => EtsRVTools::getPostMaxSizeBytes(),
            'PS_ATTACHMENT_MAXIMUM_SIZE_TEXT' => EtsRVTools::formatBytes(EtsRVTools::getPostMaxSizeBytes()),
            'addJsDef' => !$addJsDef,
            'ETS_RV_DISPLAY_RATE_AND_QUESTION' => Configuration::get('ETS_RV_DISPLAY_RATE_AND_QUESTION')
        ];
        $ETS_RV_RECAPTCHA_ENABLED = (int)Configuration::get('ETS_RV_RECAPTCHA_ENABLED');
        $vars = array_merge($vars, $this->getReCaptchaConfigs());
        if ($ETS_RV_RECAPTCHA_ENABLED)
            $this->smarty->assign($vars);
        if ($addJsDef) {
            $vars = array_merge($vars, [
                'ETS_RV_RECAPTCHA_VALID' => 0,
                'ets_rv_datetime_picker' => json_encode([
                    'prevText' => '',
                    'nextText' => '',
                    'dateFormat' => 'yy-mm-dd',
                    'currentText' => '',
                    'closeText' => '',
                    'ampm' => false,
                    'amNames' => ['AM', 'A'],
                    'pmNames' => ['PM', 'P'],
                    'timeFormat' => 'hh:mm:ss tt',
                    'timeSuffix' => '',
                    'timeOnlyTitle' => '',
                    'timeText' => '',
                    'hourText' => '',
                    'minuteText' => '',
                    'maxDate' => date('Y-m-d H:i:s'),
                ]),
            ]);

            Media::addJsDefL('ets_rv_datetime_picker_currentText', $this->l('Now'));
            Media::addJsDefL('ets_rv_datetime_picker_closeText', $this->l('Done'));
            Media::addJsDefL('ets_rv_datetime_picker_timeOnlyTitle', $this->l('Choose time'));
            Media::addJsDefL('ets_rv_datetime_picker_timeText', $this->l('Time'));
            Media::addJsDefL('ets_rv_datetime_picker_hourText', $this->l('Hour'));
            Media::addJsDefL('ets_rv_datetime_picker_minuteText', $this->l('Minute'));

            Media::addJsDefL('productCommentPostErrorMessage', $this->l('Sorry, your review cannot be posted.'));
            Media::addJsDefL('productCommentUpdatePostErrorMessage', $this->l('Sorry, your review appreciation cannot be sent.'));
            Media::addJsDefL('ets_rv_please_sign_review', $this->l('Please sign in or register to write your review'));
            Media::addJsDefL('ets_rv_please_sign_question', $this->l('Please sign in or register to ask your question'));
            Media::addJsDefL('ets_rv_please_sign_like', $this->l('Please sign in or register to like'));
            Media::addJsDefL('ets_rv_please_sign_dislike', $this->l('Please sign in or register to dislike'));
            Media::addJsDefL('file_is_to_large_text', $this->l('File is too large. Maximum size allowed: %s'));
            Media::addJsDefL('file_not_valid_text', $this->l('File type is not allowed'));
            Media::addJsDefL('ETS_RV_DEFAULT_LANGUAGE_MSG', $this->l('Default language cannot be empty!'));
        }
        Media::addJsDef($vars);
        return $ETS_RV_RECAPTCHA_ENABLED ? $this->display(__FILE__, 'javascript.tpl') : '';
    }

    public function hookRenderReCaptcha($params)
    {
        if ($this->backOffice)
            return '';
        $reCaptchaFor = !empty($params['reCaptchaFor']) ? $params['reCaptchaFor'] : '';
        if (($cache_id = $this->getCacheId('recaptcha', [$reCaptchaFor])) == null || !$this->isCached('product-comment-recaptcha.tpl', $cache_id)) {
            $configs = $this->getReCaptchaConfigs();
            if (!$reCaptchaFor || !(int)$configs['ETS_RV_RECAPTCHA_ENABLED'] || !in_array($reCaptchaFor, $configs['ETS_RV_RECAPTCHA_FOR']) && !in_array('all', $configs['ETS_RV_RECAPTCHA_FOR']) || (int)$configs['ETS_RV_RECAPTCHA_USER_REGISTERED'] && $this->isCustomerLogged())
                return '';
            $configs['reCaptchaFor'] = $reCaptchaFor;
            $configs['class'] = isset($params['class']) && $params['class'] ? $params['class'] : '';
            $this->context->smarty->assign($configs);
        }
        return $this->display(__FILE__, 'product-comment-recaptcha.tpl', $cache_id);
    }

    public function displayPCListImages($id, $json = false)
    {
        if (!$id || !Validate::isUnsignedInt($id) || !($productComment = new EtsRVProductComment($id)))
            return $json ? [] : '';
        $images = EtsRVProductCommentImage::getImages($id);
        $path_uri = $this->getMediaLink(_PS_IMG_ . $this->name . '/r/');
        if ($json) {
            if ($images) {
                foreach ($images as &$image) {
                    $image['url'] = $path_uri . $image['image'] . '-thumbnail.jpg';
                }
            }
            return $images;
        }
        $tpl_vars = [
            'images' => $images,
            'path_uri' => $path_uri,
        ];
        if (!$this->employee && (int)Configuration::get('ETS_RV_UPLOAD_PHOTO_ENABLED') &&
            ($max_upload_photo = (int)Configuration::get('ETS_RV_MAX_UPLOAD_PHOTO')) > count($images) &&
            ($this->backOffice || $productComment->id_customer && (int)$this->context->cookie->id_customer && (int)$productComment->id_customer === (int)$this->context->cookie->id_customer || $productComment->id_guest && (int)$this->context->cookie->id_customer && (int)$productComment->id_guest === (int)$this->context->cookie->id_guest)
        ) {
            $tpl_vars['productComment'] = $productComment;
            $tpl_vars['action'] = $this->context->link->getModuleLink($this->name, 'ajax', ['id_product' => $productComment->id_product, '__ac' => 'post_image', 'id' => $productComment->id], Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            $tpl_vars['ETS_RV_MAX_UPLOAD_PHOTO'] = $max_upload_photo;
        }
        $this->context->smarty->assign($tpl_vars);
        return $this->display(__FILE__, 'product-comment-item-image.tpl');
    }

    public function displayPCListVideos($id, $json = false)
    {
        if (!$id || !Validate::isUnsignedInt($id) || !($productComment = new EtsRVProductComment($id))) {
            return $json ? [] : '';
        }
        $videos = EtsRVProductCommentVideo::getVideos($id);
        $path_uri = $this->getMediaLink(_PS_IMG_ . $this->name . '/r/');
        if ($videos) {
            foreach ($videos as &$video)
                $video['url'] = $path_uri . $video['video'];
        }
        if ($json) {
            return $videos;
        }
        $tpl_vars = [
            'videos' => $videos,
            'path_uri' => $path_uri,
        ];
        if (!$this->employee && (int)Configuration::get('ETS_RV_UPLOAD_VIDEO_ENABLED') &&
            ($max_upload_video = (int)Configuration::get('ETS_RV_MAX_UPLOAD_VIDEO')) > count($videos) &&
            ($this->backOffice || $productComment->id_customer && (int)$this->context->cookie->id_customer && (int)$productComment->id_customer === (int)$this->context->cookie->id_customer || $productComment->id_guest && (int)$this->context->cookie->id_customer && (int)$productComment->id_guest === (int)$this->context->cookie->id_guest)
        ) {
            $tpl_vars['productComment'] = $productComment;
            $tpl_vars['action'] = $this->context->link->getModuleLink($this->name, 'ajax', ['id_product' => $productComment->id_product, '__ac' => 'post_video', 'id' => $productComment->id], Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            $tpl_vars['ETS_RV_MAX_UPLOAD_VIDEO'] = $max_upload_video;
        }
        $this->context->smarty->assign($tpl_vars);
        return $videos ? $this->display(__FILE__, 'product-comment-item-video.tpl') : '';
    }

    public function hookDisplayProductListReviews($params)
    {
        if (!(int)Configuration::get('ETS_RV_REVIEW_ENABLED'))
            return '';
        $product = $params['product'];
        $idProduct = isset($params['id_product']) && $params['id_product'] ? (int)$params['id_product'] : $this->findProductId($product);
        $repository = EtsRVProductCommentRepository::getInstance();
        $validateOnly = $this->validateOnly();

        $commentsNb = $repository->getCommentsNumber($idProduct, $this->context->language->id, 0, $validateOnly, $this->backOffice, 0, $this->context);
        $averageGrade = $repository->getAverageGrade($idProduct, $this->context->language->id, $validateOnly, $this->backOffice, $this->context);

        if (!$this->is17)
            $product['id'] = (int)$idProduct;

        $this->smarty->assign(array(
            'review_enabled' => (int)Configuration::get('ETS_RV_REVIEW_ENABLED'),
            'product' => new Product($idProduct,false,$this->context->language->id),
            'nb_comments' => $commentsNb,
            'average_grade' => $averageGrade,
            'position' => isset($params['page']) ? $params['page'] : '',
        ));

        $this->getColors(true);

        return $this->display(__FILE__, 'product-list-reviews.tpl');
    }

    public function hookDisplayMicrodataAggregateRating($params)
    {
        $idProduct = isset($params['id_product']) && (int)$params['id_product'] ? $params['id_product'] : (int)Tools::getValue('id_product');
        if (!$idProduct)
            return '';
        $productCommentRepository = EtsRVProductCommentRepository::getInstance();
        $validateOnly = $this->validateOnly();

        $averageGrade = $productCommentRepository->getAverageGrade($idProduct, $this->context->language->id, $validateOnly, $this->backOffice, $this->context);
        $commentsNb = $productCommentRepository->getCommentsNumber($idProduct, $this->context->language->id, 0, $validateOnly, $this->backOffice, 0, $this->context);
        $tpl_vars = array(
            'average_grade' => $averageGrade,
            'nb_comments' => $commentsNb,
            'review_enabled' => (int)Configuration::get('ETS_RV_REVIEW_ENABLED'),
        );
        $this->smarty->assign($tpl_vars);

        return $this->display(__FILE__, 'microdata-aggregate-rating.tpl');
    }

    public function hookDisplayCategoryAggregateRating($params)
    {
        $id_category = isset($params['id_category']) && (int)$params['id_category'] ? $params['id_category'] : (int)Tools::getValue('id_category');
        if (!$id_category)
            return '';
        $productCommentRepository = EtsRVProductCommentRepository::getInstance();
        $validateOnly = $this->validateOnly();

        $averageGrade = $productCommentRepository->getCategoryAverageGrade($id_category, $this->context->language->id, $validateOnly, $this->backOffice, $this->context);
        $commentsNb = $productCommentRepository->getCategoryCommentsNumber($id_category, $this->context->language->id, 0, $validateOnly, $this->backOffice, 0, $this->context);
        $tpl_vars = array(
            'average_grade' => $averageGrade,
            'nb_comments' => $commentsNb,
            'review_enabled' => (int)Configuration::get('ETS_RV_REVIEW_ENABLED'),
        );
        $this->smarty->assign($tpl_vars);

        return $this->display(__FILE__, 'category-aggregate-rating.tpl');
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        if (trim(Tools::getValue('controller')) == 'product' && trim(Tools::getValue('action')) == 'quickview' || empty($params['hook']) && trim(Configuration::get('ETS_RV_AVERAGE_RATE_POSITION')) !== 'product_additional_info')
            return '';
        $product = $this->is17 && isset($params['product']) ? $params['product'] : (int)Tools::getValue('id_product');
        $idProduct = isset($params['id_product']) && (int)$params['id_product'] ? $params['id_product'] : $this->findProductId($product);
        if (!$idProduct)
            return '';
        $productCommentRepository = EtsRVProductCommentRepository::getInstance();
        $validateOnly = $this->validateOnly();

        $averageGrade = $productCommentRepository->getAverageGrade($idProduct, $this->context->language->id, $validateOnly, $this->backOffice, $this->context);
        $commentsNb = $productCommentRepository->getCommentsNumber($idProduct, $this->context->language->id, 0, $validateOnly, $this->backOffice, 0, $this->context);
        $isPostAllowed = $productCommentRepository->isPostAllowed($idProduct, (int)$this->context->cookie->id_customer, (int)$this->context->cookie->id_guest);
        $questionsNb = $productCommentRepository->getCommentsNumber($idProduct, $this->context->language->id, 0, $this->validateOnly(1), $this->backOffice, 0, $this->context, 1);
        $maximum_review_per_user = Configuration::get('ETS_RV_MAXIMUM_REVIEW_PER_USER');

        $tpl_vars = array(
            'average_grade' => $averageGrade,
            'nb_comments' => $commentsNb,
            'post_allowed' => $isPostAllowed,
            'review_enabled' => (int)Configuration::get('ETS_RV_REVIEW_ENABLED'),
            'question_enabled' => (int)Configuration::get('ETS_RV_QUESTION_ENABLED'),
            'nb_questions' => $questionsNb,
            'maximum_review_allowed' => trim($maximum_review_per_user) === '' || (int)$maximum_review_per_user > EtsRVProductComment::getNbReviewsOfUser($idProduct, $this->context),
            'displaySchema' => $commentsNb > 0 && $averageGrade > 0,//version_compare(_PS_VERSION_, '1.7.8.0', '<')
            'ETS_RV_DISPLAY_RATE_AND_QUESTION' => Configuration::get('ETS_RV_DISPLAY_RATE_AND_QUESTION')
        );
        $this->smarty->assign($tpl_vars);
        $this->getColors(true);

        return $this->display(__FILE__, 'product-additional-info.tpl');
    }

    public function ajaxRender($value = null)
    {
        die($value);
    }

    public function postProcess()
    {
        $qa = (int)Tools::getValue('qa') ? 1 : 0;
        $productCommentEntity = EtsRVProductCommentEntity::getInstance()
            ->setEmployee($this->employee)
            ->setBackOffice($this->backOffice)
            ->setQA($qa);

        $commentEntity = EtsRVCommentEntity::getInstance()
            ->setEmployee($this->employee)
            ->setBackOffice($this->backOffice)
            ->setQA($qa);

        $replyEntity = EtsRVReplyCommentEntity::getInstance()
            ->setEmployee($this->employee)
            ->setBackOffice($this->backOffice)
            ->setQA($qa);

        if (($action = Tools::getValue('__ac')) && Validate::isCleanHtml($action)) {
            switch ($action) {
                // Check guest:
                case 'is_guest_login':
                    $productCommentEntity->isGuestLogin();
                    break;
                case 'list_product_comment':
                    $productCommentEntity->getProductComments();
                    break;
                case 'post_product_question':
                case 'post_product_comment':
                    $productCommentEntity->postProductComment();
                    break;
                case 'post_image':
                    $productCommentEntity->postProductCommentImages();
                    break;
                case 'delete_product_comment':
                    $productCommentEntity->deleteProductComment();
                    break;
                case 'delete_product_comment_image':
                    $productCommentEntity->deleteProductCommentImage();
                    break;
                case 'delete_product_comment_video':
                    $productCommentEntity->deleteProductCommentVideo();
                    break;
                case 'useful_product_comment':
                    $productCommentEntity->updateProductCommentUsefulness();
                    break;
                case 'private_product_comment':
                    $productCommentEntity->privateProductComment();
                    break;
                case 'approve_product_comment':
                    $productCommentEntity->approveProductComment();
                    break;
                case 'update_date_product_comment':
                    $productCommentEntity->updateDateProductComment();
                    break;
                // Comments
                case 'comment':
                    $commentEntity->getComment();
                    break;
                case 'list_comment':
                    $commentEntity->getComments();
                    break;
                case 'post_comment':
                    $commentEntity->postComment();
                    break;
                case 'delete_comment':
                    $commentEntity->deleteComment();
                    break;
                case 'private_comment':
                    $commentEntity->privateComment();
                    break;
                case 'approve_comment':
                    $commentEntity->approveComment();
                    break;
                case 'update_date_comment':
                    $commentEntity->updateDateComment();
                    break;
                case 'useful_comment':
                    $commentEntity->updateCommentUsefulness();
                    break;
                case 'useful_answer':
                    $commentEntity->updateAnswerUsefulness();
                    break;
                // Reply comments
                case 'reply_comment':
                    $replyEntity->getReplyComment();
                    break;
                case 'list_reply_comment':
                    $replyEntity->getReplyComments();
                    break;
                case 'post_reply_comment':
                    $replyEntity->postReplyComment();
                    break;
                case 'delete_reply_comment':
                    $replyEntity->deleteReplyComment();
                    break;
                case 'private_reply_comment':
                    $replyEntity->privateReplyComment();
                    break;
                case 'approve_reply_comment':
                    $replyEntity->approveReplyComment();
                    break;
                case 'update_date_reply_comment':
                    $replyEntity->updateDateReplyComment();
                    break;
                case 'useful_reply_comment':
                    $replyEntity->updateReplyCommentUsefulness();
                    break;
            }
        }
        $action = Tools::getValue('action');
        if (trim($action) === 'formPostComment') {
            $id_product = Tools::getValue('product_id');
            if (!$id_product) {
                $this->_errors[] = $this->l('Product does not exist.');
            } elseif (!Validate::isUnsignedInt($id_product)) {
                $this->_errors[] = $this->l('Product is invalid.');
            }
            $id_order = Tools::getValue('id_order');
            if ($id_order == '' || !Validate::isUnsignedInt($id_order)) {
                $id_order = 0;
            }
            if (!count($this->_errors) && $id_order > 0) {
                if (!$this->isCustomerLogged()) {
                    $this->_errors[] = $this->l('Customer does not log in');
                } elseif (EtsRVProductCommentCustomer::isBlockByIdCustomer((int)$this->context->customer->id)) {
                    $this->_errors[] = $this->l('The customer has been blocked.');
                } elseif (!($order = new Order($id_order)) || (int)$order->id <= 0) {
                    $this->_errors[] = $this->l('The order does not exist.');
                } elseif ((int)$order->id_customer <= 0 || (int)$order->id_customer !== (int)$this->context->customer->id) {
                    $this->_errors[] = $this->l('You do not have permission to access.');
                } else {
                    $order_detail = $order->getOrderDetailList();
                    if (!$order_detail || !is_array($order_detail) || !count($order_detail)) {
                        $this->_errors[] = $this->l('You do not have permission to review the product.');
                    } else {
                        $product_id = 0;
                        foreach ($order_detail as $od) {
                            if ((int)$od['product_id'] === (int)$id_product) {
                                $product_id = (int)$od['product_id'];
                                break;
                            }
                        }
                        if ($product_id <= 0) {
                            $this->_errors[] = $this->l('You do not have permission to review the product.');
                        }
                    }
                }
            }
            $has_error = count($this->_errors);
            die(json_encode(array(
                'errors' => $has_error ? implode(PHP_EOL, $this->_errors) : false,
                'form' => !$has_error ? $this->renderProductCommentModal(array('id_product' => $id_product, 'id_order' => $id_order)) . $this->renderTemplateModal(array('id_product' => $id_product)) : '',
            )));
        } elseif (trim($action) === 'renderTemplateModal') {
            $id_product = (int)Tools::getValue('id_product');
            $params = ['id_product' => $id_product];
            die(json_encode(array(
                'infos' => $this->getCurrentCustomer(),
                'html' => htmlentities($this->renderProductCommentModal($params) . $this->renderProductQuestionModal($params) . $this->renderTemplateModal($params)),
            )));
        }
    }

    public function toLink($href, $title, $target = '_blank', $content = null)
    {
        if (!$href)
            return '';
        if ($content === null) {
            $content = $title;
        }
        $attrs = [
            'href' => $href,
            'title' => $title,
            'target' => $target
        ];

        return EtsRVTools::displayText($content, 'a', $attrs);
    }

    public function validateOnly($qa = 0)
    {
        return $this->backOffice || (!$qa && !(int)Configuration::get('ETS_RV_MODERATE') || $qa && !(int)Configuration::get('ETS_RV_QA_MODERATE')) ? null : 1;//ETS_RV_QA_AUTO_APPROVE
    }

    public static function _clearLogByCronjob()
    {
        Configuration::deleteByName('ETS_RV_LAST_CRONJOB');
        if (@file_exists(($dest = _PS_ROOT_DIR_ . '/var/logs/ets_reviews.cronjob.log')))
            @unlink($dest);
    }

    public function hookDisplayCronjobInfo()
    {
        if (!Module::isEnabled($this->name))
            return '';
        if ($this->context->controller instanceof AdminEtsRVQueueController ||
            $this->context->controller instanceof AdminEtsRVEmailController ||
            $this->context->controller instanceof AdminEtsRVTrackingController ||
            $this->context->controller instanceof AdminEtsRVCronjobController ||
            $this->context->controller instanceof AdminEtsRVActivityController
        ) {
            $cache_time = Configuration::get('ETS_RV_INSTALL_TIME');
            if (!$cache_time) {
                $cache_time = Configuration::updateValue('ETS_RV_INSTALL_TIME', time());
            }
            $last_cronjob = Configuration::getGlobalValue('ETS_RV_LAST_CRONJOB');
            $overtime = $last_cronjob ? (time() - (strtotime($last_cronjob) + 43200)) : (time() - ($cache_time + 86400));
            if ($last_cronjob && ($seconds = (time() - strtotime($last_cronjob))) <= 86400) {
                $dt1 = new DateTime("@0");
                $dt2 = new DateTime("@" . $seconds);
                if ($seconds > 3600)
                    $format = $this->l('%h hours, %i minutes and %s seconds');
                elseif ($seconds > 60)
                    $format = $this->l('%i minutes and %s seconds');
                else
                    $format = $this->l('%s seconds');
                $last_cronjob = $dt1->diff($dt2)->format($format);
            }
            $this->smarty->assign(array(
                'ETS_RV_LAST_CRONJOB' => $last_cronjob,
                'ETS_RV_OVERTIME' => $overtime,
                'automationLink' => $this->context->link->getAdminLink(self::TAB_PREFIX . 'Cronjob')
            ));
            return $this->display(__FILE__, 'bo-cronjob.tpl');
        }
    }

    public function displayProductInfo($idProduct, $grade, $idLang = null, $idShop = null)
    {
        if (!$idProduct || !Validate::isUnsignedId($idProduct) || (int)$grade <= 0)
            return '';

        if ($idLang == null)
            $idLang = $this->context->language->id;

        if ($idShop == null)
            $idShop = $this->context->shop->id;

        $product = new Product($idProduct, false, $idLang, $idShop);
        $imageCover = Product::getCover($product->id);
        $productGrade = Tools::ps_round(($grade * 2)) / 2;

        return [
            'productName' => $product->name,
            'productLink' => $this->context->link->getProductLink($product, $product->link_rewrite, $product->category, $product->ean13, $idLang, $idShop),
            'productGrade' => $productGrade,
            'productCover' => isset($imageCover['id_image']) && (int)$imageCover['id_image'] > 0 ? $this->context->link->getImageLink($product->link_rewrite, (int)$imageCover['id_image'], EtsRVTools::getFormattedName('home')) : '',
            'image_dir' => $productGrade > 0 && @file_exists(dirname(__FILE__) . '/views/img/star/' . $productGrade . '-star.png') ? $this->context->link->getMediaLink($this->_path . 'views/img/star/' . $productGrade . '-star.png') : '',
        ];
    }

    public function addOverride($classname)
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>=')
            && !file_exists(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'override' . DIRECTORY_SEPARATOR . 'index.php')
            && file_exists(_PS_CONFIG_DIR_ . 'index.php')
        ) {
            $directoryPath = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'override';
            if (is_dir($directoryPath)) {
                @copy(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'index.php', _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'override' . DIRECTORY_SEPARATOR . 'index.php');
            } else {
                $fs = new Symfony\Component\Filesystem\Filesystem();
                $fs->mkdir($directoryPath, PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem::DEFAULT_MODE_FOLDER);
                @copy(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'index.php', $directoryPath . DIRECTORY_SEPARATOR . 'index.php');
            }
        }
        if (trim($classname) === 'CartRule' && Module::isEnabled('ets_abandonedcart')) {
            return true;
        }
        if (trim($classname) === 'CartRule' && Module::isEnabled('etsdiscountcombinations')) {
            @file_put_contents(_PS_OVERRIDE_DIR_ . '/classes/CartRule.php', preg_replace('/(function\s+checkValidity\s*)\(/', '$1Override(', Tools::file_get_contents(_PS_OVERRIDE_DIR_ . '/classes/CartRule.php')));
        }
        return parent::addOverride($classname);
    }

    public function removeOverride($classname)
    {
        if (trim($classname) === 'CartRule' && Module::isEnabled('ets_abandonedcart')) {
            return true;
        }
        $res = parent::removeOverride($classname);
        if ($res && $classname == 'CartRule' && Module::isEnabled('etsdiscountcombinations')) {
            @file_put_contents(_PS_OVERRIDE_DIR_ . '/classes/CartRule.php', preg_replace('/(function\s+checkValidity)Override\s*\(/', '$1(', Tools::file_get_contents(_PS_OVERRIDE_DIR_ . '/classes/CartRule.php')));
        }
        return $res;
    }

    public function clearCache($template, $cache_id = null, $compile_id = null)
    {
        if ($compile_id === null) {
            $compile_id = $this->getDefaultCompileId();
        }

        if (static::$_batch_mode) {
            if ($cache_id === null) {
                $cache_id = $this->name;
            }

            $key = $template . '-' . $cache_id . '-' . $compile_id;
            if (!isset(static::$_defered_clearCache[$key])) {
                static::$_defered_clearCache[$key] = [$this->getTemplatePath($template), $cache_id, $compile_id];
            }
        } else {
            if ($cache_id === null) {
                $cache_id = $this->name;
            }

            Tools::enableCache();
            $number_of_template_cleared = Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath($template), $cache_id, $compile_id);
            Tools::restoreCacheSettings();

            return $number_of_template_cleared;
        }
    }

    public function getDefaultCompileId()
    {
        return Context::getContext()->shop->theme_name;
    }

    public function uninstallOverrides()
    {
        if (parent::uninstallOverrides()) {
            require_once(dirname(__FILE__) . '/classes/OverrideUtil');
            $class = 'Ets_rv_overrideUtil';
            $method = 'restoreReplacedMethod';
            call_user_func_array(array($class, $method), array($this));
            return true;
        }
        return false;
    }

    public function installOverrides()
    {
        require_once(dirname(__FILE__) . '/classes/OverrideUtil');
        $class = 'Ets_rv_overrideUtil';
        $method = 'resolveConflict';
        call_user_func_array(array($class, $method), array($this));
        if (parent::installOverrides()) {
            call_user_func_array(array($class, 'onModuleEnabled'), array($this));
            return true;
        }
        return false;
    }

    public function disable($force_all = false)
    {
        $res = parent::disable($force_all);
        if ($res && !$force_all && EtsRVTools::checkEnableOtherShop($this->id)) {
            if ($this->getOverrides() != null) {
                try {
                    $this->installOverrides();
                } catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
            if (method_exists($this, 'get') && $dispatcher = $this->get('event_dispatcher')) {
                /** @var \Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher|\Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher */
                $dispatcher->addListener(\PrestaShopBundle\Event\ModuleManagementEvent::DISABLE, function (\PrestaShopBundle\Event\ModuleManagementEvent $event) {
                    EtsRVTools::activeTab($this->name);
                });
            }
        }
        return $res;
    }

    public function enable($force_all = false)
    {
        if (!$force_all && EtsRVTools::checkEnableOtherShop($this->id) && $this->getOverrides() != null) {
            try {
                $this->uninstallOverrides();
            } catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
            }
        }
        return parent::enable($force_all);
    }
}
