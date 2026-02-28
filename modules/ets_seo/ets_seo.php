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

use PrestaShopBundle\Event\ModuleManagementEvent;

if (!defined('_ETS_SEO_MODULE_')) {
    define('_ETS_SEO_MODULE_', 'ets_seo');
}

define('ETS_TOTAL_SEO_RULE_SCORE', 23);
define('ETS_TOTAL_READABILITY_RULE_SCORE', 8);
define('ETS_SEO_ENABLE_CONTENT_ANALYSIS_BASE64', 'ETS_SEO_ENABLE_CONTENT_ANALYSIS_BASE64');

if (file_exists(_PS_ROOT_DIR_ . '/app/AppKernel.php')) {
    require_once _PS_ROOT_DIR_ . '/app/AppKernel.php';
}
if (version_compare(_PS_VERSION_, '8.0', '>=')) {
    require_once __DIR__ . '/src/FormType/SeoType.php';
}
require_once __DIR__ . '/classes/utils/EtsSeoStrHelper.php';
require_once __DIR__ . '/classes/EtsSeoArrayHelper.php';
require_once __DIR__ . '/classes/traits/EtsSeoGridHookListenersTrait.php';
require_once __DIR__ . '/classes/traits/EtsSeoOverrideTrait.php';
require_once __DIR__ . '/classes/traits/EtsSeoRequestControllerTrait.php';
require_once __DIR__ . '/classes/EtsSeoProduct.php';
require_once __DIR__ . '/classes/EtsSeoCms.php';
require_once __DIR__ . '/classes/EtsSeoCmsCategory.php';
require_once __DIR__ . '/classes/EtsSeoMeta.php';
require_once __DIR__ . '/classes/EtsSeoCategory.php';
require_once __DIR__ . '/classes/EtsSeoRedirect.php';
require_once __DIR__ . '/classes/EtsSeoManufacturer.php';
require_once __DIR__ . '/classes/EtsSeoSupplier.php';
require_once __DIR__ . '/classes/EtsSeoRating.php';
require_once __DIR__ . '/classes/EtsSeoUpdating.php';
require_once __DIR__ . '/classes/Ets_Seo_Sitemap.php';
require_once __DIR__ . '/classes/EtsSeoSetting.php';
require_once __DIR__ . '/classes/EtsSeoTranslation.php';
require_once __DIR__ . '/classes/EtsImportTranslation.php';
require_once __DIR__ . '/classes/EtsSeoAnalysis.php';
require_once __DIR__ . '/classes/EtsSeoNotFoundUrl.php';
require_once __DIR__ . '/classes/EtsSeoJsDefHelper.php';
require_once __DIR__ . '/classes/EtsSeoChatGpt.php';
require_once __DIR__ . '/classes/EtsSeoGptMessage.php';
require_once __DIR__ . '/classes/EtsSeoGptTemplate.php';
require_once __DIR__ . '/defines.php';

/**
 * Class Ets_Seo.
 *
 * @property \Context $context
 *
 * @mixin \ModuleCore
 */
class Ets_Seo extends Module
{
    use EtsSeoGridHookListenersTrait;
    use EtsSeoOverrideTrait;
    use EtsSeoRequestControllerTrait;
    public $is_configurable = 1;
    /**
     * @var bool
     */
    public $is176;
    /**
     * @var bool
     */
    public $is178;
    /**
     * @var bool
     */
    public $is175;
    /**
     * @var bool
     */
    public $gte820;
    /**
     * @var bool
     */
    public $is8e;

    /**
     * @var string
     */
    public $_html;

    /**
     * @var string
     */
    public $template_dir;
    /**
     * @var \EtsSeoJsDefHelper
     */
    private $jsDefHelper;
    /**
     * @var array
     */
    private $currentMetaData;

    /**
     * __construct.
     *
     * @return void
     */
    public function __construct()
    {
        $this->name = 'ets_seo';
        $this->tab = 'seo';
        $this->version = '3.1.3';
        $this->author = 'PrestaHero';
        $this->bootstrap = true;
        $this->module_key = '94b6a05e5e754eced6bfd9e2d22d4b60';
        parent::__construct();
        $this->displayName = $this->l('SEO Audit');
        $this->description = $this->l('Make SEO easy for everyone! All you need for On-Page SEO including SEO analysis with up-to-date rank math, SEO-friendly URL (remove IDs), ratings & snippet, social media, auto sitemap, RSS, meta template and more!');
        $this->ps_versions_compliancy = ['min' => '1.7.4.0', 'max' => _PS_VERSION_];
        $this->is176 = version_compare(_PS_VERSION_, '1.7.6.0', '>=');
        $this->is175 = version_compare(_PS_VERSION_, '1.7.5.0', '>=');
        $this->is178 = version_compare(_PS_VERSION_, '1.7.8.0', '>=');
        $this->gte820 = version_compare(_PS_VERSION_, '8.2.0', '>=');
        $this->is8e = version_compare(_PS_VERSION_, '8.0.0', '>=') && Module::isEnabled('ps_edition_basic');
        $this->_html = '';
        $this->template_dir = '../../../../modules/' . $this->name . '/views/templates/';
    }

    /**
     * install.
     *
     * @return bool
     */
    public function install()
    {

        if (self::isInstalled('ets_awesomeurl')) {
            throw new PrestaShopException($this->l('The module ETS Awesome URL has been installed'));
        }
        if (self::isInstalled('etsdynamicsitemap')) {
            throw new PrestaShopException($this->l('The module ETS Dynamic Sitemap has been installed'));
        }
        if (Module::isEnabled('prettyurls')) {
            throw new \PrestaShopException($this->l('Pretty Url module is installed & enabled. You cannot enable this module'));
        }

        Ets_Seo_Define::getInstance()->installDb();
        return $this->copySymfonyServices()
            && parent::install()
            && $this->_registerHooks()
            && $this->_installTabs()
            && $this->setDefaultConfig()
            && $this->_installOverried()
            && $this->setRootSeoUrlConfig()
            && $this->setSitemap()
            && $this->copyTranslations()
            && $this->importNewTranslation()
            && EtsSeoManufacturer::updateLinkRewriteManufacturer()
            && EtsSeoSupplier::updateLinkRewriteSupplier()
            && EtsSeoChatGpt::installDefaultGptTemplates();
    }

    /**
     * @return bool
     */
    private function _registerHooks()
    {
        foreach (Ets_Seo_Define::getInstance()->getHooks() as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }
        return true;
    }

    /**
     * uninstall.
     *
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall()
            && Ets_Seo_Define::getInstance()->uninstallDb()
            && $this->restoreSeoUrlConfig()
            && $this->_uninstallOverried()
            && $this->_uninstallTabs()
            && $this->removeAllConfigs()
            && $this->removeSitemap()
            && $this->removeNewTranslation();
    }

    public function disable($force_all = false)
    {
        if (method_exists($this, 'get') && $dispatcher = $this->get('event_dispatcher')) {
            /* @var \Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher|\Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher */
            $dispatcher->addListener(\PrestaShopBundle\Event\ModuleManagementEvent::DISABLE, function (ModuleManagementEvent $event) {
                if (Ets_Seo_Define::checkEnableOtherShop($this->id)) {
                    try {
                        $this->getOverrides() && $this->installOverrides() && $this->_installTabs();
                    } catch (Exception $e) {
                        // Do nothing
                    }
                } else {
                    $this->_uninstallOverried() && $this->removeSitemap() && Ets_Seo_Define::getInstance()->restoreTrafficSeoTabs();
                }
            });
        }

        $parentResult = parent::disable($force_all)
            && $this->restoreSeoUrlConfig();

        return $parentResult;
    }

    public function enable($force_all = false)
    {
        if (Module::isEnabled('ets_awesomeurl')) {
            throw new \PrestaShopException($this->l('ETS Awesome URL module is installed & enabled. You cannot enable this module'));
        }
        if (Module::isEnabled('etsdynamicsitemap')) {
            throw new \PrestaShopException($this->l('ETS Dynamic Sitemap module is installed & enabled. You cannot enable this module'));
        }
        if (Module::isEnabled('prettyurls')) {
            throw new \PrestaShopException($this->l('Pretty Url module is installed & enabled. You cannot enable this module'));
        }
        $this->checkOverrideDir();
        if (!$force_all && Ets_Seo_Define::checkEnableOtherShop($this->id) && null != $this->getOverrides()) {
            try {
                $this->uninstallOverrides();
            } catch (Exception $e) {
            }
        }
        $parentResult = parent::enable($force_all)
            && $this->_installOverried()
            && $this->setSitemap()
            && $this->_installTabs()
            && $this->copySymfonyServices();

        return $parentResult;
    }

    /**
     * __installTabs.
     *
     * @return bool
     */
    public function _installTabs()
    {
        $languages = Language::getLanguages(false);
        $seoTabId = Tab::getIdFromClassName('AdminEtsSeo');
        $tab = $seoTabId ? new Tab($seoTabId) : new Tab();
        $tab->class_name = 'AdminEtsSeo';
        $tab->module = $this->name;
        $tab->id_parent = 0;
        foreach ($languages as $lang) {
            $tab->name[$lang['id_lang']] = ($tabName = Ets_Seo_Define::getTextLang('SEO Audit', $lang, 'defines')) ? $tabName : $this->l('SEO Audit');
        }
        $tab->enabled = true;
        $tab->save();
        $seoTabId = $seoTabId ?: $tab->id;
        if ($seoTabId) {
            $seoDef = Ets_Seo_Define::getInstance();
            // Set tab Traffic seo
            $idTabParentMeta = Tab::getIdFromClassName('AdminParentMeta');
            // Disable parent meta tab
            if ($idTabParentMeta) {
                $parentMeta = new Tab($idTabParentMeta);
                $parentMeta->active = false;
                $parentMeta->enabled = false;
                $parentMeta->save();
            }
            // SEO tabs
            foreach ($seoDef->get_menus() as $tabArg) {
                // Set parent for each meta tab
                if (!($idCurrentTab = Tab::getIdFromClassName($tabArg['controller']))) {
                    $tab = new Tab();
                } else {
                    $tab = new Tab($idCurrentTab);
                }
                $id_parent = $seoTabId;
                if (isset($tabArg['parent_controller']) && $tabArg['parent_controller']) {
                    $id_parent = Tab::getIdFromClassName($tabArg['parent_controller']);
                }
                $tab->class_name = $tabArg['controller'];
                $tab->module = $this->name;
                $tab->id_parent = $id_parent;
                $tab->icon = $tabArg['icon'];
                $tab->enabled = true;
                foreach ($languages as $lang) {
                    $tab->name[$lang['id_lang']] = isset($tabArg['origin']) && ($tabName = Ets_Seo_Define::getTextLang($tabArg['origin'], $lang, 'defines')) ? $tabName : $tabArg['title'];
                }
                $tab->save();
                if ('AdminEtsSeoUrlAndRemoveId' == $tabArg['controller']) {
                    $urlTabId = Tab::getIdFromClassName('AdminEtsSeoUrlAndRemoveId');
                    $idTabMeta = Tab::getIdFromClassName('AdminMeta');
                    if ($urlTabId && $idTabMeta) {
                        $trafficSeo = new Tab($idTabMeta);
                        $trafficSeo->id_parent = $urlTabId;
                        $oldNames = [];
                        foreach ($languages as $lang) {
                            $oldNames[$lang['id_lang']] = $trafficSeo->name[$lang['id_lang']];
                            $trafficSeo->name[$lang['id_lang']] = ($tabName = Ets_Seo_Define::getTextLang('URL structure and remove IDs', $lang)) ? $tabName : $this->l('URL structure and remove IDs');
                        }
                        $trafficSeo->enabled = true;
                        $trafficSeo->save();
                        Configuration::updateValue('ETS_SEO_SEO_AND_URL_NAME', $oldNames);
                    }
                } elseif ('AdminEtsSeoTraffic' == $tabArg['controller']) {
                    $traffic = Tab::getIdFromClassName('AdminEtsSeoTraffic');

                    $idTabMeta = Tab::getIdFromClassName('AdminSearchEngines');
                    if ($traffic && $idTabMeta) {
                        $trafficSeo = new Tab($idTabMeta);
                        $trafficSeo->id_parent = $traffic;
                        $trafficSeo->enabled = true;
                        $trafficSeo->save();
                    }

                    $idTabMeta = Tab::getIdFromClassName('AdminReferrers');
                    if ($traffic && $idTabMeta) {
                        $trafficSeo = new Tab($idTabMeta);
                        $trafficSeo->id_parent = $traffic;
                        $trafficSeo->enabled = true;
                        $trafficSeo->save();
                    }
                }
            }

            $tabAjaxId = Tab::getIdFromClassName('AdminEtsSeoAjax');
            $tabAjax = $tabAjaxId ? new Tab($tabAjaxId) : new Tab();
            $tabAjax->class_name = 'AdminEtsSeoAjax';
            $tabAjax->id_parent = 0;
            $tabAjax->module = $this->name;
            $tabAjax->active = false;
            $tabAjax->enabled = true;
            foreach ($languages as $lang) {
                $tabAjax->name[$lang['id_lang']] = 'Seo ajax';
            }
            $tabAjax->save();
        }

        return true;
    }

    /**
     * __uninstallTabs.
     *
     * @return bool
     */
    private function _uninstallTabs()
    {
        $seoDef = Ets_Seo_Define::getInstance();
        $seoDef->restoreTrafficSeoTabs();
        $menus = $seoDef->get_menus();

        foreach ($menus as $key => $tabItem) {
            if ($tabItem) {
                if ($tabId = Tab::getIdFromClassName($key)) {
                    $tab = new Tab($tabId);
                    if ($tab->id ) {
                        $tab->delete();
                    }
                }
            }
        }
        $tabAjaxId = Tab::getIdFromClassName('AdminEtsSeoAjax');
        if ((int) $tabAjaxId) {
            $tabAjax = new Tab($tabAjaxId);
            if ($tabAjax->id) {
                $tabAjax->delete();
            }
        }
        // Remove tabs seo
        if ($tabSeo = Tab::getIdFromClassName('AdminEtsSeo')) {
            $tab = new Tab($tabSeo);
            if ($tab->id ) {
                $tab->delete();
            }
        }

        $idTabParentMeta = Tab::getIdFromClassName('AdminParentMeta');
        // enable parent meta tab
        if ($idTabParentMeta) {
            $parentMeta = new Tab($idTabParentMeta);
            $parentMeta->active = true;
            $parentMeta->enabled = true;
            $parentMeta->save();
        }
        return true;
    }

    public function importNewTranslation()
    {
        if (@file_exists(_PS_MODULE_DIR_ . 'ets_seo/translations/translation.xml')) {
            $trans = new EtsImportTranslation($this->name, _PS_MODULE_DIR_ . 'ets_seo/translations/translation.xml');
            $trans->import();
        }

        return true;
    }

    public function removeNewTranslation()
    {
        $trans = new EtsImportTranslation($this->name, _PS_MODULE_DIR_ . 'ets_seo/translations/translation.xml');
        $trans->removeTranslation();

        return true;
    }

    /**
     * getContent.
     *
     * @return void
     */
    public function getContent()
    {
        $moduleLink = $this->context->link->getAdminLink('AdminEtsSeoGeneralDashboard');
        Tools::redirectAdmin($moduleLink);
    }

    /* Use new translate system */
    public function isUsingNewTranslationSystem()
    {
        return false;
//        if ($this->is176) {
////            return (int) Configuration::get('ETS_SEO_ENABLE_NEW_TRANS');
//            return true;
//        }
//
//        return false;
    }

    /**
     * hookActionProductSave.
     *
     * @param array $params
     *
     * @return void
     */
    public function hookActionProductSave($params)
    {
        EtsSeoProduct::updateSeoProduct($params);
    }

    /**
     * Assign common variables to smarty (Context Scope)
     */
    private function _assignCommonSmartyVarsBackOffice()
    {
        $trace = debug_backtrace(~1, 2);
        static $isAssigned = false;
        static $languages = null;
        $currentController = ($currentController = Tools::getValue('controller')) && Validate::isControllerName($currentController) ? $currentController : '';
        $seoDef = Ets_Seo_Define::getInstance();
        if (!$languages) {
            $languages = array_values(Language::getLanguages(false));
        }
        $ets_languages = [];
        foreach ($languages as $lang) {
            $ets_languages[$lang['iso_code']] = $lang['id_lang'];
        }
        if ('hookDisplayBackOfficeHeader' === $trace[1]['function']) {
            $this->getJsDefHelper()->addBo('languages', $languages)
                ->addBo('currentActiveLangId', 'AdminProducts' == $currentController ? $this->context->language->id : Configuration::get('PS_LANG_DEFAULT'));
        }
        if ($this->is176) {
            $is_cms_category = 0;
            if (null !== ($request = self::getRequestContainer())) {
                if ('admin_cms_pages_category_create' == $request->get('_route') || 'admin_cms_pages_category_edit' == $request->get('_route')) {
                    $is_cms_category = 1;
                }
            }
        } else {
            $is_cms_category = Tools::getIsset('updatecms_category') || Tools::getIsset('addcms_category') ? 1 : 0;
        }

        $linkRewriteRules = [];
        if ($this->isMetaController()) {
            $linkRewriteRules = $seoDef->url_rules();
        }
        $id = $this->getIdCurrentPage();
        $type = null;
        switch ($currentController) {
            case 'AdminProducts':
                $type = 'product';
                break;
            case 'AdminCategories':
                $type = 'category';
                break;
            case 'AdminCmsContent':
                $type = 'cms';
                if ($is_cms_category) {
                    $type = 'cms_category';
                }
                break;
            case 'AdminManufacturers':
                $type = 'manufacturer';
                break;
            case 'AdminSuppliers':
                $type = 'supplier';
                break;
            case 'AdminMeta':
                $type = 'meta';
                break;
        }
        $seoData = $type ? EtsSeoSetting::getAnalysisScore($type, $id) : [];
        $seoDef = Ets_Seo_Define::getInstance();
        $enableBase64ContentAnalysis = Configuration::get(ETS_SEO_ENABLE_CONTENT_ANALYSIS_BASE64);
        if ($enableBase64ContentAnalysis === false) {
            $enableBase64ContentAnalysis = 1;
        }
        $assign = [
            'ets_seo_defined' => [
                'is176' => (int) $this->is176,
                'is175' => (int) $this->is175,
                'isSf' => (bool) self::getRequestContainer(),
                'seo_analysis_rules' => array_merge($seoDef->seo_analysis_rules($currentController, $is_cms_category), $seoDef->seo_analysis_rules_meta()),
                'readability_rules' => $seoDef->readability_rules(),
                'transition_words' => $seoDef->transition_words(),
                'placeholder_meta' => $seoDef->getPlaceholderPage($currentController, $is_cms_category),
                'id_current_page' => $id,
                'meta_short_code_btn' => 'AdminProducts' == $currentController ? $this->getMetaCodeTemplate('product', false) : null,
                'meta_template_configured' => EtsSeoSetting::isMetaTemplateConfigured($currentController, $is_cms_category),
                'TOTAL_SEO_RULE_SCORE' => ETS_TOTAL_SEO_RULE_SCORE,
                'TOTAL_READABILITY_RULE_SCORE' => ETS_TOTAL_READABILITY_RULE_SCORE,
            ],
            'seoData' => $seoData,
            'is_cms_category' => $is_cms_category,
            'ets_languages' => $ets_languages,
            'meta_codes' => $this->getMetaCodes(),
            'link_rewrite_rules' => $linkRewriteRules,
            'is_use_module' => (false !== strpos($currentController, 'AdminEtsSeo') || in_array($currentController, $seoDef->listControllerAction())) && $this->active,
            'is_no_referrer' => in_array($currentController, $this->whiteListControllers()),
            'ets_seo_enable_content_analysis_base64' => (int) $enableBase64ContentAnalysis,
        ];
        $this->context->smarty->assign($assign);
        if ($isAssigned) {
            return;
        }
        $this->context->smarty->assign(
            [
                'link_admin_js' => $this->_path . 'views/js/admin.js',
                'link_helpers_js' => $this->_path . 'views/js/helpers.js',
                'link_admin_all_js' => $this->_path . 'views/js/admin_js_all.js',
                'link_analysis_js' => $this->_path . 'views/js/analysis.js',
                'link_select2_js' => $this->_path . 'views/js/select2.min.js',
                'link_page_js' => $this->_path . 'views/js/page.js',
                'link_module' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name,
                'current_lang_id' => $this->context->language->id,
                'is_multilang_active' => Language::isMultiLanguageActivated(),
                'ets_seo_link_img' => $this->context->shop->getBaseURL(true, true) . 'img/social/',
            ]
        );
        $isAssigned = true;
    }

    /**
     * @param string|string[] $component
     * @param string $theme
     *
     * @return bool
     */
    public function addJqueryUICss($component, $theme = 'base')
    {
        $folder = _PS_JS_DIR_ . 'jquery/ui/';
        $addedCount = 0;
        if (!is_array($component)) {
            $component = [$component];
        }

        foreach ($component as $ui) {
            if (isset(Media::$jquery_ui_dependencies[$ui]) && Media::$jquery_ui_dependencies[$ui]['theme']) {
                $themeCss = Media::getCSSPath($folder . 'themes/' . $theme . '/jquery.ui.theme.css');
                $compCss = Media::getCSSPath($folder . 'themes/' . $theme . '/jquery.' . $ui . '.css');
                if (!empty($themeCss)) {
                    $this->context->controller->addCSS($themeCss);
                    ++$addedCount;
                }
                if (!empty($compCss)) {
                    $this->context->controller->addCSS($compCss);
                    ++$addedCount;
                }
            }
        }

        return $addedCount > 0;
    }

    private function _mappingPageTypeToJsDef($currentController)
    {
        $ctl = '';
        $maps = [
            'AdminProducts' => 'product',
            'AdminCategories' => 'category',
            'AdminCmsContent' => 'cms',
            'AdminCmsCategory' => 'cms_category',
            'AdminManufacturers' => 'manufacturer',
            'AdminSuppliers' => 'supplier',
            'AdminMeta' => 'meta',
        ];
        $routeMaps = [
            'admin_product_catalog' => 'AdminProducts',
            'admin_categories_index' => 'AdminCategories',
            'admin_cms_pages_index' => 'AdminCmsContent',
            'admin_manufacturers_index' => 'AdminManufacturers',
            'admin_suppliers_index' => 'AdminSuppliers',
            'admin_metas_index' => 'AdminMeta',
        ];
        if (self::getRequestContainer() && ($route = self::getRequestContainer()->get('_route')) && array_key_exists($route, $routeMaps)) {
            $ctl = $routeMaps[$route];
        }
        if (!$ctl && array_key_exists($currentController, $maps)) {
            if ('AdminProducts' === $currentController && !Tools::getValue('id_product')) {
                $ctl = 'AdminProducts';
            }
            if ('AdminCategories' === $currentController && !Tools::getValue('id_category')) {
                $ctl = 'AdminCategories';
            }
            if ('AdminCmsContent' === $currentController && (!Tools::getValue('id_cms') && Tools::getValue('id_cms_category'))) {
                $ctl = 'AdminCmsContent';
            }
            if ('AdminManufacturers' === $currentController && !Tools::getValue('id_manufacturer')) {
                $ctl = 'AdminManufacturers';
            }
            if ('AdminSuppliers' === $currentController && !Tools::getValue('id_supplier')) {
                $ctl = 'AdminSuppliers';
            }
            if ('AdminMeta' === $currentController && !Tools::getValue('id_meta')) {
                $ctl = 'AdminMeta';
            }
        }
        if ($ctl) {
            $this->getJsDefHelper()->addBo('analysisPageType', $maps[$ctl]);
            $this->getJsDefHelper()->addBo('transMsg.analyzeMissingPage', $this->l('Analyze missing pages'));
        }
    }

    /**
     * displayBackOfficeHeader.
     *
     * @return string
     */
    public function hookDisplayBackOfficeHeader()
    {
        $currentController = ($currentController = Tools::getValue('controller')) && Validate::isControllerName($currentController) ? $currentController : '';
        $seoDef = Ets_Seo_Define::getInstance();
        $this->context->controller->addCSS($this->_path . 'views/css/all.css');
        $this->getJsDefHelper()->setBo(null, [
            'isEnable' => $this->active,
            'version' => $this->version,
            'currentController' => $currentController,
            'is178' => $this->is178,
            'transMsg' => [
                'imageDoesNotExistOnServer' => $this->l('This image does not exist on server. Please upload a new one!', __FILE__),
                'saveToFinishUpload' => $this->l('Click "Save" to finish uploading!', __FILE__),
                'anErrorOccur' => $this->l('There is an error occurred.', __FILE__),
                'saveSuccessful' => $this->l('Save successfully', __FILE__),
            ],
            'dashboardUri' => $this->context->link->getAdminLink('AdminEtsSeoGeneralDashboard'),
            'ajaxCtlUri' => $this->context->link->getAdminLink('AdminEtsSeoAjax'),
        ]);
        $this->_mappingPageTypeToJsDef($currentController);
        $shops = Shop::getShops();
        $urlShops = [];
        foreach ($shops as $shop) {
            $urlShop = (new \Shop($shop['id_shop']))->getBaseURL();
            if (false !== $urlShop) {
                $urlShops[] = str_replace('http://', '', $urlShop);
            }
        }
        $this->getJsDefHelper()->addBo('shopUrls', $urlShops);
        $chatGptInstance = EtsSeoChatGpt::getInstance();
        if ($this->active && $chatGptInstance->isActive()) {
            $activeChatGpt = false;
            if ('AdminProducts' == $currentController && $pid = (int) $this->getIdCurrentPage()) {
                $this->getJsDefHelper()->addBo('isEditingProduct', true)->addBo('idProduct', (int) $pid);
                $this->getJsDefHelper()->addBo('gptAppendFields', [
                    ['field' => 'description', 'title' => $this->l('Description', __FILE__)],
                    ['field' => 'description_short', 'title' => $this->l('Summary', __FILE__)],
                    ['field' => 'meta_title', 'title' => $this->l('Meta title', __FILE__)],
                    ['field' => 'meta_description', 'title' => $this->l('Meta description', __FILE__)],
                ]);
                if ($this->isProductV2EditPage()) {
                    $this->getJsDefHelper()->addBo('gptFieldSelectorPrefix', [
                        'description' => '#product_description_description',
                        'description_short' => '#product_description_description_short',
                    ]);
                } else {
                    $this->getJsDefHelper()->addBo('gptFieldSelectorPrefix', [
                        'description' => '#description',
                        'description_short' => '#description_short',
                    ]);
                }
                if ($this->isProductV2EditPage()) {
                    $this->getJsDefHelper()->addBo('gptContentShortCodeSelectorPrefix', [
                        'product_name' => '.product-header-form [id$="name_${idLang}"]', 'meta_title' => '[id$="meta_title_${idLang}"]', 'meta_description' => '[id$="meta_description_${idLang}"]', 'default_category' => '#product_description_categories_default_category_id', 'language' => '#form_switch_language', 'brand' => 'select[id="product_description_manufacturer"]',
                    ]);
                } else {
                    $this->getJsDefHelper()->addBo('gptContentShortCodeSelectorPrefix', [
                        'product_name' => '#form_step1_names [id$="name_${idLang}"]', 'meta_title' => '[id$="meta_title_${idLang}"]', 'meta_description' => '[id$="meta_description_${idLang}"]', 'default_category' => '#categories input.default-category:checked', 'language' => '#form_switch_language', 'brand' => 'select[id="form_step1_id_manufacturer"]',
                    ]);
                }
                $activeChatGpt = true;
            }
            if ('AdminCategories' === $currentController) {
                $isAddCategory = false;
                if (($cid = Tools::getValue('id_category')) || ($isAddCategory = (self::getRequestContainer() && 'admin_categories_create' === self::getRequestContainer()->get('_route')))) {
                    $this->getJsDefHelper()->addBo('isEditingCategory', true)->addBo('idCategory', $isAddCategory ? 0 : (int) $cid);
                    $appendFields = [
                        ['field' => 'category_description', 'title' => $this->l('Description', __FILE__)],
                        ['field' => 'meta_title', 'title' => $this->l('Meta title', __FILE__)],
                        ['field' => 'meta_description', 'title' => $this->l('Meta description', __FILE__)],
                    ];
                    if (version_compare(_PS_VERSION_, '8.0', '>=')) {
                        $appendFields[] = ['field' => 'category_additional_description', 'title' => $this->l('Additional description', __FILE__)];
                    }
                    $this->getJsDefHelper()->addBo('gptAppendFields', $appendFields);
                    $this->getJsDefHelper()->addBo('gptFieldSelectorPrefix', [
                        'category_description' => '.gpt-field-category_description',
                        'category_additional_description' => '.gpt-field-category_additional_description',
                    ]);
                    $this->getJsDefHelper()->addBo('gptContentShortCodeSelectorPrefix', [
                        'category_name' => 'input[id="category_name_${idLang}"]', 'meta_title' => '[id$="meta_title_${idLang}"]', 'meta_description' => '[id$="meta_description_${idLang}"]', 'language' => '#etsSeoChatGptBox select[name="langIdToApply"]:first',
                    ]);
                    $activeChatGpt = true;
                }
            }
            if ('AdminCmsContent' === $currentController) {
                if (Tools::getValue('id_cms') || (self::getRequestContainer() && 'admin_cms_pages_create' === self::getRequestContainer()->get('_route'))) {
                    $activeChatGpt = true;
                    $this->getJsDefHelper()->addBo('gptAppendFields', [
                        ['field' => 'cms_page_content', 'title' => $this->l('Page Content', __FILE__)],
                        ['field' => 'meta_title', 'title' => $this->l('Meta title', __FILE__)],
                        ['field' => 'meta_description', 'title' => $this->l('Meta description', __FILE__)],
                    ]);
                    $this->getJsDefHelper()->addBo('gptContentShortCodeSelectorPrefix', [
                        'page_name' => 'input[id="cms_page_title_${idLang}"]', 'meta_title' => '[id$="meta_title_${idLang}"]', 'meta_description' => '[id$="meta_description_${idLang}"]', 'language' => '#etsSeoChatGptBox select[name="langIdToApply"]:first',
                    ]);
                }
            }
            $this->getJsDefHelper()->addBo('isChatGptAvailable', $activeChatGpt);
            if ($activeChatGpt) {
                if ('AdminProducts' === $currentController) {
                    $this->addJqueryUICss(['ui.core', 'ui.mouse', 'ui.draggable', 'ui.resizable']);
                }
                $this->context->controller->addJqueryUI(['ui.core', 'ui.mouse', 'ui.draggable', 'ui.resizable'], 'base', 'AdminProducts' !== $currentController);
                $this->getJsDefHelper()->addBo('transMsg.messageRequire', $this->l('Message is required.', __FILE__));
                $this->getJsDefHelper()->addBo('transMsg.applyBtn', $this->l('Apply', __FILE__));
                $this->getJsDefHelper()->addBo('transMsg.applyLabel', $this->l('Apply content for', __FILE__));
                $this->getJsDefHelper()->addBo('transMsg.applyConfirm', $this->l('Apply successfully.', __FILE__));
                $this->getJsDefHelper()->addBo('transMsg.clearAllConfirm', $this->l('This will clear all messages. Are you sure ?', __FILE__));
                $this->getJsDefHelper()->addBo('transMsg.allLangLabel', $this->l('All Languages', __FILE__));
                $this->getJsDefHelper()->addBo('chatGptAdminUrl', $this->context->link->getAdminLink('AdminEtsSeoChatGpt'));
                $gptJsPath = $this->_path . 'views/js/chatgpt-admin.js';
                $this->context->controller->addJS($gptJsPath);
            }
        }
        if ('AdminCmsContent' === $currentController) {
            $isAddCms = false;
            if (($cid = Tools::getValue('id_cms')) || ($isAddCms = (self::getRequestContainer() && 'admin_cms_pages_create' === self::getRequestContainer()->get('_route')))) {
                $this->getJsDefHelper()->addBo('isEditingCms', true)->addBo('idCms', $isAddCms ? 0 : (int) $cid);
                $this->getJsDefHelper()->addBo('transMsg.searchEngineOptimize', $this->l('Search Engine Optimization', __FILE__));
                $this->getJsDefHelper()->addBo('transMsg.searchEngineOptimizeHelp', $this->l('Improve your ranking and how your product page will appear in search engines results.', __FILE__));
            }
        }
        $this->_assignCommonSmartyVarsBackOffice();
        if ($this->active && (false !== strpos($currentController, 'AdminEtsSeo') || in_array($currentController, $seoDef->listControllerAction()))) {
            $this->context->controller->addCSS($this->_path . 'views/css/tagify.css');
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
            $this->context->controller->addCSS($this->_path . 'views/css/other.css');
            $this->context->controller->addJS($this->_path . 'views/js/tagify.min.js');
            $this->context->controller->addJS($this->_path . 'views/js/tagify.polyfills.min.js');

            if (!self::getRequestContainer()) {
                $this->context->controller->addCSS($this->_path . 'views/css/select2.min.css');
            }
            if (false !== strpos($currentController, 'AdminEtsSeo')) {
                $this->context->controller->addCSS($this->_path . 'views/css/admin_module.css');
            }
            if ($this->is8e) {
                $this->context->controller->addCSS($this->_path . 'views/css/admin8e.css');
            }
        }

        if ('AdminEtsSeoGeneralDashboard' == $currentController) {
            $this->context->controller->addCSS($this->_path . 'views/css/nv.d3.css');
            $this->context->controller->addCSS($this->_path . 'views/css/Chart.min.css');
            $this->context->controller->addCSS($this->_path . 'views/css/dashboard.css');
            $this->context->controller->addJS($this->_path . 'views/js/Chart.min.js');
            $this->context->controller->addJS($this->_path . 'views/js/d3.v3.min.js');
            $this->context->controller->addJS($this->_path . 'views/js/nv.d3.min.js');
        }
        if (version_compare(_PS_VERSION_, '8.0.0', '<')) {
            $this->addTwigVar('ETS_SEO_TWIG_TRANS', $this->transTwig());
            $this->addTwigVar('ETS_SEO_IS_178', (bool) $this->is178);
            if ('AdminCmsContent' == $currentController && ($this->getIdCurrentPage() || $this->isCreatePage())) {
                if ($this->isCmsCategoryPage()) {
                    $this->seo_cms_category_html();
                } else {
                    $this->seo_cms_html();
                }
            }
            if ($this->isMetaController()) {
                $this->seo_meta_html();
            } elseif ('AdminCategories' == $currentController && ($this->getIdCurrentPage() || $this->isCreatePage())) {
                $this->seo_category_html();
            } elseif ('AdminManufacturers' == $currentController && ($this->getIdCurrentPage() || $this->isCreatePage())) {
                $this->seo_manufacturer_html();
            } elseif ('AdminSuppliers' == $currentController && ($this->getIdCurrentPage() || $this->isCreatePage())) {
                $this->seo_supplier_html();
            } elseif ('AdminProducts' == $currentController) {
                if (!Tools::isSubmit('ajax') && self::getRequestContainer()) {
                    $this->addTwigVar('ets_seo_is175', $this->is175);
                    $this->addTwigVar('ets_seo_seo_enabled', 1);
                    $this->addTwigVar('ets_seo_readability_enabled', $this->active);
                }
                if ($this->getIdCurrentPage()) {
                    $this->seo_product_html();
                }
            }
        }

        if ($errorLinkRewrite = $this->context->cookie->__get('ets_seo_error_link_rewrite')) {
            $this->context->controller->errors = [$errorLinkRewrite];
            $this->context->cookie->__unset('ets_seo_error_link_rewrite');
        }

        return $this->display(__FILE__, 'admin_head.tpl');
    }

    public function hookActionProductAdd($params)
    {
        if (isset($params['id_product_old'], $params['product']) && $params['id_product_old'] && $params['product']) {
            /** @var \Product|\ProductCore $p */
            $p = $params['product'];
            foreach ($p->link_rewrite as $k => $item) {
                $pattern = '/\-(?P<num>\d+)$/';
                if ($hasNum = preg_match($pattern, $item, $m)) {
                    $append = '-' . ++$m['num'];
                    $num = $m['num'];
                    $tmpRewrite = preg_replace($pattern, $append, $item);
                } else {
                    $append = '-2';
                    $num = 2;
                    $tmpRewrite = $item . $append;
                }
                if (EtsSeoSetting::validateLinkRewrite('product', [$k => $item])) {
                    try {
                        $append = '-copy-' . random_int($num * 5, 128000000);
                    } catch (Exception $e) {
                        /** @noinspection RandomApiMigrationInspection */
                        $append = '-copy-' . rand($num * 5, 128000000);
                    }
                    $tmpRewrite = $hasNum ? preg_replace($pattern, $append, $item) : $item . $append;
                }
                $p->link_rewrite[$k] = $tmpRewrite;
            }
            $p->save();
        }
    }

    public function hookActionDispatcherBefore($params)
    {
        $context = $this->context;
        $controller = Tools::getValue('controller');
        $tab = Tab::getInstanceFromClassName($controller);
        if(in_array($controller,['AdminDashboard']) || $tab->module)
            return true;
        // Kiểm tra: Chỉ chạy khi route là admin_product_form và không phải AJAX
        $request = self::getRequestContainer();
        if (!$request || Tools::isSubmit('ajax')) {
            return true;
        }
        if (isset($params['controller_type'], $context->employee->id) && Dispatcher::FC_ADMIN == $params['controller_type'] && $context->employee->id && $context->cookie->passwd && $context->employee->isLoggedBack()) {
            if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
                if (Tools::isSubmit('updateNewFeatureFlag')) {
                    self::updateNewFeatureFlag();
                    exit('updateNewFeatureFlag');
                }
                $this->_assignCommonSmartyVarsBackOffice();
                if ($twigs = $this->getTwigVars()) {
                    $this->assignTwigVar(
                        $twigs
                    );
                }
            }
        }
    }

    public function getTwigVars()
    {
        $controller = Tools::getValue('controller');
        $this->context->smarty->assign(
            array(
                'gt900' => version_compare(_PS_VERSION_, '9.0.0', '>=')
            )
        );
        $twigs = [
            'ETS_SEO_TWIG_TRANS' => $this->transTwig(),
            'ETS_SEO_IS_178' => (bool) $this->is178,
        ];
        if ('AdminCmsContent' == $controller && ($this->getIdCurrentPage() || $this->isCreatePage())) {
            if ($this->isCmsCategoryPage()) {
                $this->assignPageParams('cms_category');
                $twigs['ets_cms_category_seo_analysis_html'] = $this->display(__FILE__, 'page/seo_analysis.tpl') ;
                $twigs['ets_cms_category_seo_setting_html'] =  $this->display(__FILE__, 'page/seo_setting.tpl');
                $twigs['ets_seo_preview_analysis'] = $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl');
            } else {
                $this->assignPageParams('cms');
                $twigs['ets_cms_seo_analysis_html'] =$this->display(__FILE__, 'page/seo_analysis.tpl');
                $twigs['ets_cms_seo_setting_html'] =  $this->display(__FILE__, 'page/seo_setting.tpl') ;
                $twigs['ets_seo_preview_analysis'] = $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl');
            }
        }
        if ($this->isMetaController()) {
            $twigs['ETS_SEO_ENABLE_REMOVE_ID_IN_URL'] = (int) Configuration::get('ETS_SEO_ENABLE_REMOVE_ID_IN_URL');
            $twigs['ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS'] = (int) Configuration::get('ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS');
            $twigs['ETS_SEO_ENABLE_REMOVE_ID_ATTR_ALIAS'] = (int) Configuration::get('ETS_SEO_ENABLE_REMOVE_ID_ATTR_ALIAS');
            $twigs['ETS_SEO_ENABLE_REDRECT_NOTFOUND'] = (int) Configuration::get('ETS_SEO_ENABLE_REDRECT_NOTFOUND');
            $twigs['ETS_SEO_REDIRECT_STATUS_CODE'] = (int) Configuration::get('ETS_SEO_REDIRECT_STATUS_CODE');
            $twigs['ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL'] = (int) Configuration::get('ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL');
            $twigs['titleRemoveLangCode'] = $this->l('Remove ISO code in URL for default language');
            $twigs['titleRemoveAttrAlias'] = $this->l('Remove attribute alias in URL');
            $twigs['titleRemoveIdAttrAlias'] = $this->l('Remove ID attribute alias in URL');
            $cacheId = $this->_getCacheId(['seo_meta_html' => $this->getIdCurrentPage()]);
            $settingTpl = 'page/seo_setting.tpl';
            $analysisTpl = 'page/seo_analysis.tpl';
            $metaTpl = 'page/meta_title.tpl';
            $previewTpl = 'parts/_preview_seo_analysis.tpl';
            if (!$this->isCached($settingTpl, $cacheId) || !$this->isCached($analysisTpl, $cacheId) || !$this->isCached($metaTpl, $cacheId) || !$this->isCached($previewTpl, $cacheId)) {
                $this->assignPageParams('meta');
            }
            $twigs['ets_meta_seo_setting_html'] =  $this->display(__FILE__, $settingTpl, $cacheId) ;
            $twigs['ets_meta_seo_analysis_html'] =  $this->display(__FILE__, $analysisTpl, $cacheId);
            $twigs['ets_meta_seo_meta_title'] =  $this->display(__FILE__, $metaTpl, $cacheId);
            $twigs['ets_seo_preview_analysis'] = $this->display(__FILE__, $previewTpl, $cacheId);
        }
        if ('AdminCategories' == $controller && ($this->getIdCurrentPage() || $this->isCreatePage())) {
            $this->assignPageParams('category');
            $twigs['ets_category_seo_setting_html'] = $this->display(__FILE__, 'page/seo_setting.tpl');
            $twigs['ets_category_seo_analysis_html'] =  $this->display(__FILE__, 'page/seo_analysis.tpl') ;
            $twigs['ets_seo_preview_analysis'] = $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl');
        }
        if ('AdminManufacturers' == $controller && ($this->getIdCurrentPage() || $this->isCreatePage())) {
            $this->assignPageParams('manufacturer');
            $twigs['ets_manufacturer_seo_setting_html'] =  $this->display(__FILE__, 'page/seo_setting.tpl') ;
            $twigs['ets_manufacturer_seo_analysis_html'] =  $this->display(__FILE__, 'page/seo_analysis.tpl') ;
            $twigs['ets_seo_preview_analysis'] = $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl');
        }
        if ('AdminSuppliers' == $controller && ($this->getIdCurrentPage() || $this->isCreatePage())) {
            $this->assignPageParams('supplier');
            $twigs['ets_supplier_seo_setting_html'] =  $this->display(__FILE__, 'page/seo_setting.tpl');
            $twigs['ets_supplier_seo_analysis_html'] =  $this->display(__FILE__, 'page/seo_analysis.tpl') ;
            $twigs['ets_seo_preview_analysis'] = $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl');
        }
        if ('AdminProducts' == $controller || (self::getRequestContainer() && in_array(self::getRequestContainer()->get('_route'), ['admin_product_form', 'admin_product_catalog']))) {
            if (!Tools::isSubmit('ajax')) {
                $twigs['ets_seo_is175'] = $this->is175;
                $twigs['ets_seo_seo_enabled'] = 1;
                $twigs['ets_seo_readability_enabled'] = $this->active;
                if ($idProduct = $this->getIdCurrentPage()) {
                    $this->seo_product_html();
                    $cacheId = $this->_getCacheId(['seo_product_html' => $idProduct]);
                    $twigs['ets_seo_product_seo_analysis'] = $this->display(__FILE__, 'page/seo_analysis.tpl', $cacheId);
                    $twigs['ets_seo_preview_analysis'] = $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl', $cacheId);
                    if (version_compare(_PS_VERSION_, '8.1.0', '>=')) {
                        $languages = Language::getLanguages(false);
                        $current_lang = [
                            'id' => $this->context->language->id,
                            'iso_code' => $this->context->language->iso_code,
                        ];
                        $ratingConfig = Configuration::get('ETS_SEO_RATING_PAGES') ? explode(',', Configuration::get('ETS_SEO_RATING_PAGES')) : [];
                        $seoDef = Ets_Seo_Define::getInstance();
                        $seoAdvancedData = $seoDef->seo_advanced('product', $idProduct, $this->context);
                        $ratingSettingData = EtsSeoRating::getRating('product', $idProduct);

                        $this->smarty->assign([
                            'seo_data' => $seoDef->key_phrase_input('product', $idProduct, $this->context),
                            'ets_seo_languages' => $languages,
                            'gte820' => $this->gte820,
                            'is_new_theme' => (bool) self::getRequestContainer(),
                            'current_lang' => $current_lang,
                            'seo_advanced' => $seoAdvancedData,
                            'ets_seo_advanced_data' => $seoAdvancedData,
                            'enable_force_rating' => in_array('product', $ratingConfig),
                            'rating_setting' => $ratingSettingData,
                            'ets_seo_rating_setting' => $ratingSettingData,
                            'in_product_page' => true,
                        ]);
                        $twigs['ets_seo_social'] = $this->display(__FILE__, 'parts/_tab_social.tpl', $cacheId);
                        $twigs['ets_seo_advanced'] = $this->display(__FILE__, 'parts/_seo_advanced.tpl', $cacheId);
                        $twigs['ets_seo_rating'] = $this->display(__FILE__, 'parts/_rating.tpl', $cacheId);
                    }
                }
            }
        }

        return $twigs;
    }

    /**
     * New method to work with TWIG in PS 8.x.
     *
     * @param $vars
     */
    public function assignTwigVar($vars)
    {
        if (!class_exists('EtsTwigExtension')) {
            require_once __DIR__ . '/classes/utils/EtsTwigExtension.php';
        }
        if ($sfContainer = self::getSfContainer()) {
            try {
                if (version_compare(_PS_VERSION_, '9.0.0', '>=')) {
                    $tw = (isset($this->context->controller) && method_exists($this->context->controller, 'getTwig'))
                        ? $this->context->controller->getTwig()
                        : null;
                } else {
                    $tw = $sfContainer->get('twig');
                }

                if ($tw) {
                    $firstKey = array_keys($vars)[0];
                    if (!array_key_exists($firstKey, $tw->getGlobals())) {
                        $_twigExtension = new \EtsTwigExtension($vars);
                        $tw->addExtension($_twigExtension);
                    }
                }
            } catch (\Twig\Error\RuntimeError $e) {
                // do nothing
            }
        }
        if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            $request = self::getRequestContainer();
            if ($request) {
                $route = $request->get('_route');
                if ('admin_product_catalog' == $route || 'admin_products_index' == $route) {
                    $id_product = $request->get('id') ?: $request->get('productId');
                    if (!$id_product && $this->active) {
                        if (!Tools::isSubmit('updateNewFeatureFlag') && !Tools::isSubmit('getFormListView')) {
                            return self::updateOldFeatureFlag();
                        }
                    }
                }
            }
            self::updateNewFeatureFlag();
        }
    }

    public function hookActionProductUpdate($params)
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>=') && isset($params['product']) && ($product = $params['product']) && 0 == $product->state) {
            self::updateNewProduct($product->id);
        }
    }

    public function whiteListControllers()
    {
        return ['AdminProducts', 'AdminMeta', 'AdminCategories', 'AdminCmsContent', 'AdminManufacturers', 'AdminSuppliers'];
    }

    public function getMetaCodes()
    {
        $seoDef = Ets_Seo_Define::getInstance();
        $result = [
            'title' => [],
            'desc' => [],
        ];
        if (($controller = Tools::getValue('controller')) && Validate::isControllerName($controller)) {
            $request = self::getRequestContainer();
            $id_lang = $this->context->language->id;
            if ('AdminProducts' == $controller) {
                if ($request) {
                    $id_product = (int) $request->get('id');
                    $product = $id_product ? new Product($id_product, false, $id_lang) : null;
                    $cate = $product ? new Category($product->id_category_default, $id_lang) : '';
                    $result['title'] = $seoDef->get_meta_codes('product', [
                        'post_title' => $product ? $product->name : '',
                        'price' => $product ? number_format($product->price, 1, '.', '') : '',
                        'category' => $product ? $cate->name : '',
                        'is_title' => true,
                    ]);
                    $result['desc'] = $seoDef->get_meta_codes('product', [
                        'post_title' => $product ? $product->name : '',
                        'price' => $product ? number_format($product->price, 2, '.', '') : '',
                        'category' => $product ? $cate->name : '',
                        'description' => $product ? $product->description_short : '',
                    ]);
                }
            } elseif ('AdminCategories' == $controller) {
                $id_category = (int) Tools::getValue('id_category');
                if ($request) {
                    $id_category = (int) $request->get('categoryId');
                }
                $cate = $id_category ? new Category($id_category, $id_lang) : null;
                $result['title'] = $seoDef->get_meta_codes('category', [
                    'post_title' => $cate ? $cate->name : '',
                    'is_title' => true,
                ]);
                $result['desc'] = $seoDef->get_meta_codes('category', [
                    'post_title' => $cate ? $cate->name : '',
                    'description' => $cate ? $cate->description : '',
                ]);
            } elseif ('AdminCmsContent' == $controller) {
                if (!$this->isCmsCategoryPage()) {
                    $id_cms = (int) Tools::getValue('id_cms');
                    if ($request) {
                        $id_cms = (int) $request->get('cmsPageId');
                    }
                    $cms = $id_cms ? new CMS($id_cms, $id_lang) : null;
                    $cmsCategory = $cms ? new CMSCategory($cms->id_cms_category, $id_lang) : null;
                    $result['title'] = $seoDef->get_meta_codes('cms', [
                        'post_title' => $cms && isset($cms->head_seo_title) ? $cms->head_seo_title : '',
                        'category' => $cmsCategory ? $cmsCategory->name : '',
                        'is_title' => true,
                    ]);
                    $result['desc'] = $seoDef->get_meta_codes('cms', [
                        'post_title' => $cms && isset($cms->head_seo_title) ? $cms->head_seo_title : '',
                        'description' => $cms ? $cms->meta_description : '',
                    ]);
                } else {
                    $id_cms_category = (int) Tools::getValue('id_cms_category');
                    if ($request) {
                        $id_cms_category = (int) $request->get('cmsCategoryId');
                    }
                    $cmsCate = $id_cms_category ? new CMSCategory($id_cms_category, $id_lang) : null;
                    $result['title'] = $seoDef->get_meta_codes('cms_category', [
                        'post_title' => $cmsCate ? $cmsCate->name : '',
                        'is_title' => true,
                    ]);
                    $result['desc'] = $seoDef->get_meta_codes('cms_category', [
                        'post_title' => $cmsCate ? $cmsCate->name : '',
                        'description' => $cmsCate ? $cmsCate->description : '',
                    ]);
                }
            } elseif ('AdminMeta' == $controller) {
                $id_meta = (int) Tools::getValue('id_meta');
                if ($request) {
                    $id_meta = (int) $request->get('metaId');
                }

                $meta = $id_meta ? new Meta($id_meta, $id_lang) : null;
                $result['title'] = $seoDef->get_meta_codes('meta', [
                    'post_title' => $meta ? $meta->title : '',
                    'is_title' => true,
                ]);
                $result['desc'] = $seoDef->get_meta_codes('meta', [
                    'post_title' => $meta ? $meta->title : '',
                    'description' => $meta ? $meta->description : '',
                ]);
            } elseif ('AdminManufacturers' == $controller) {
                $id_manufacturer = (int) Tools::getValue('id_manufacturer');
                if ($request) {
                    $id_manufacturer = (int) $request->get('manufacturerId');
                }
                $manufacturer = $id_manufacturer ? new Manufacturer($id_manufacturer, $id_lang) : null;
                $result['title'] = $seoDef->get_meta_codes('manufacturer', [
                    'post_title' => $manufacturer ? $manufacturer->name : '',
                    'is_title' => true,
                ]);
                $result['desc'] = $seoDef->get_meta_codes('manufacturer', [
                    'post_title' => $manufacturer ? $manufacturer->name : '',
                    'description' => $manufacturer ? $manufacturer->short_description : '',
                    'description2' => $manufacturer ? $manufacturer->description : '',
                ]);
            } elseif ('AdminSuppliers' == $controller) {
                $id_supplier = (int) Tools::getValue('id_supplier');
                if ($request) {
                    $id_supplier = (int) $request->get('supplierId');
                }
                $supplier = $id_supplier ? new Supplier($id_supplier, $id_lang) : null;
                $result['title'] = $seoDef->get_meta_codes('supplier', [
                    'post_title' => $supplier ? $supplier->name : '',
                    'is_title' => true,
                ]);
                $result['desc'] = $seoDef->get_meta_codes('supplier', [
                    'post_title' => $supplier ? $supplier->name : '',
                    'description' => $supplier ? $supplier->description : '',
                ]);
            }
        }

        return $result;
    }

    /**
     * seo_cms_html.
     *
     * @return void
     */
    public function seo_cms_html()
    {
        $this->assignPageParams('cms');
        $controller = ($controller = Tools::getValue('controller')) && Validate::isControllerName($controller) ? $controller : '';
        if (version_compare(_PS_VERSION_, '8.0.0', '<') && self::getRequestContainer()) {
            if ('AdminCmsContent' == $controller) {
                $this->addTwigVar('ets_cms_seo_analysis_html',  $this->display(__FILE__, 'page/seo_analysis.tpl') );
                $this->addTwigVar('ets_cms_seo_setting_html',  $this->display(__FILE__, 'page/seo_setting.tpl') );
                $this->addTwigVar('ets_seo_preview_analysis', $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl'));
            }
        } else {
            $this->context->smarty->assign([
                'ets_cms_seo_setting_html' =>  $this->display(__FILE__, 'page/seo_setting.tpl'),
                'ets_cms_seo_analysis_html' =>  $this->display(__FILE__, 'page/seo_analysis.tpl') ,
                'ets_seo_preview_analysis' =>  $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl'),
            ]);
        }
    }

    public function seo_cms_category_html()
    {
        $this->assignPageParams('cms_category');
        $controller = ($controller = Tools::getValue('controller')) && Validate::isControllerName($controller) ? $controller : '';
        if (version_compare(_PS_VERSION_, '8.0.0', '<') && self::getRequestContainer()) {
            if ('AdminCmsContent' == $controller) {
                $this->addTwigVar('ets_cms_category_seo_analysis_html',  $this->display(__FILE__, 'page/seo_analysis.tpl') );
                $this->addTwigVar('ets_cms_category_seo_setting_html', $this->display(__FILE__, 'page/seo_setting.tpl') );
                $this->addTwigVar('ets_seo_preview_analysis', $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl'));
            }
        } else {
            $this->context->smarty->assign([
                'ets_cms_category_seo_setting_html' => $this->display(__FILE__, 'page/seo_setting.tpl'),
                'ets_cms_category_seo_analysis_html' => $this->display(__FILE__, 'page/seo_analysis.tpl'),
                'ets_seo_preview_analysis' =>  $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl'),
            ]);
        }
    }

    /**
     * assignPageParams.
     *
     * @param string $type : cms, meta, category
     *
     * @return void
     */
    public function assignPageParams($type)
    {
        $id = $this->getIdCurrentPage();
        if (!$id && !$this->isCreatePage() && !$this->isMetaController()) {
            return;
        }
        $enableRating = false;
        $ratingConfig = Configuration::get('ETS_SEO_RATING_PAGES') ? explode(',', Configuration::get('ETS_SEO_RATING_PAGES')) : [];
        $metaTitleConfigName = '';
        $metaDescConfigName = '';
        $forceUseMetaTemplate = 0;
        $pageTitleTrans = '';
        switch ($type) {
            case 'cms':
                $objPage = 'CMS';
                $enableRating = in_array('cms', $ratingConfig);
                $metaTitleConfigName = 'ETS_SEO_CMS_META_TITLE';
                $metaDescConfigName = 'ETS_SEO_CMS_META_DESC';
                $forceUseMetaTemplate = (int) Configuration::get('ETS_SEO_CMS_FORCE_USE_META_TEMPLATE');
                $pageTitleTrans = EtsSeoTranslation::trans('cms_title');
                break;

            case 'cms_category':
                $objPage = 'CMSCategory';
                $enableRating = in_array('cms_category', $ratingConfig);
                $metaTitleConfigName = 'ETS_SEO_CMS_CATE_META_TITLE';
                $metaDescConfigName = 'ETS_SEO_CMS_CATE_META_DESC';
                $forceUseMetaTemplate = (int) Configuration::get('ETS_SEO_CMS_CATE_FORCE_USE_META_TEMPLATE');
                $pageTitleTrans = EtsSeoTranslation::trans('cms_category_title');
                break;

            case 'meta':
                $objPage = 'Meta';
                $enableRating = in_array('meta', $ratingConfig);
                $pageTitleTrans = EtsSeoTranslation::trans('meta_title');
                break;

            case 'category':
                $objPage = 'Category';
                $metaTitleConfigName = 'ETS_SEO_CATEGORY_META_TITLE';
                $metaDescConfigName = 'ETS_SEO_CATEGORY_META_DESC';
                $forceUseMetaTemplate = (int) Configuration::get('ETS_SEO_CATEGORY_FORCE_USE_META_TEMPLATE');
                $enableRating = in_array('category', $ratingConfig);
                $pageTitleTrans = EtsSeoTranslation::trans('category_name');
                break;
            case 'manufacturer':
                $objPage = 'Manufacturer';
                $metaTitleConfigName = 'ETS_SEO_MANUFACTURER_META_TITLE';
                $metaDescConfigName = 'ETS_SEO_MANUFACTURER_META_DESC';
                $forceUseMetaTemplate = (int) Configuration::get('ETS_SEO_MANUFACTURER_FORCE_USE_META_TEMPLATE');
                $enableRating = in_array('manufacturer', $ratingConfig);
                $pageTitleTrans = EtsSeoTranslation::trans('manufacturer_name');
                break;
            case 'supplier':
                $objPage = 'Supplier';
                $enableRating = in_array('supplier', $ratingConfig);
                $metaTitleConfigName = 'ETS_SEO_SUPPLIER_META_TITLE';
                $metaDescConfigName = 'ETS_SEO_SUPPLIER_META_DESC';
                $forceUseMetaTemplate = (int) Configuration::get('ETS_SEO_SUPPLIER_FORCE_USE_META_TEMPLATE');
                $pageTitleTrans = EtsSeoTranslation::trans('supplier_name');
                break;
        }

        $languages = Language::getLanguages(false);
        $metaConfig = [];
        foreach ($languages as $lang) {
            $metaConfig[$lang['id_lang']] = [
                'title' => $metaTitleConfigName ? (string) Configuration::get($metaTitleConfigName, $lang['id_lang']) : '',
                'desc' => $metaDescConfigName ? (string) Configuration::get($metaDescConfigName, $lang['id_lang']) : '',
            ];
        }

        $current_lang = [
            'id' => $this->context->language->id,
            'iso_code' => $this->context->language->iso_code,
        ];
        if (count($languages)) {
            $langDefault = Language::getLanguage((int) Configuration::get('PS_LANG_DEFAULT'));
            $current_lang = [
                'id' => $langDefault ? $langDefault['id_lang'] : $languages[0]['id_lang'],
                'iso_code' => $langDefault ? $langDefault['iso_code'] : $languages[0]['iso_code'],
            ];
        }
        $seoDef = Ets_Seo_Define::getInstance();

        $seo_cms = [
            'link' => [],
            'link_rewrite' => [],
            'meta_description' => [],
            'meta_title' => [],
            'key_phrase' => [],
        ];
        $page_name = '';

        if ($id &&  !empty($objPage)) {
            foreach ($languages as $lang) {
                $page = new $objPage($id, $lang['id_lang']);
                if ('meta' == $type) {
                    $link = $page->url_rewrite ? $this->getPageLink($page->page, (int) $lang['id_lang']) : '';
                } elseif ('cms' == $type) {
                    $link = $this->context->link->getCMSLink($page, null, null, (int) $lang['id_lang']);
                } elseif ('cms_category' == $type) {
                    $link = $this->context->link->getCMSCategoryLink($page, null, null, (int) $lang['id_lang']);
                } elseif ('category' == $type) {
                    $link = $this->context->link->getCategoryLink($page, $page->link_rewrite, $lang['id_lang']);
                } elseif ('manufacturer' == $type) {
                    $link = $this->context->link->getManufacturerLink($page, null, (int) $lang['id_lang'], $this->context->shop->id);
                } elseif ('supplier' == $type) {
                    $link = $this->context->link->getSupplierLink($page, null, (int) $lang['id_lang'], $this->context->shop->id);
                }

                $seo_cms['link'][$lang['id_lang']] = !empty($link) ? (is_array($link) ? $link[0]['link'] : $link) : '';
                $seo_cms['link_rewrite'][$lang['id_lang']] = isset($page->link_rewrite) ? $page->link_rewrite : $page->url_rewrite;
                $seo_cms['meta_title'][$lang['id_lang']] = isset($page->meta_title) && $page->meta_title ? $page->meta_title : (isset($page->title) ? $page->title : '');
                $seo_cms['meta_description'][$lang['id_lang']] = isset($page->meta_description) && $page->meta_description ? $page->meta_description : (isset($page->description) ? $page->description : '');
                if (!$seo_cms['meta_title'][$lang['id_lang']] && isset($page->name) && $page->name) {
                    $seo_cms['meta_title'][$lang['id_lang']] = $page->name;
                }
                if ('manufacturer' == $type && !$page->meta_description) {
                    $seo_cms['meta_description'][$lang['id_lang']] = $page->short_description;
                }

                if (isset($page->name)) {
                    $page_name = $page->name;
                } elseif (isset($page->title)) {
                    $page_name = $page->title;
                } elseif (isset($page->meta_title)) {
                    $page_name = $page->meta_title;
                }
            }
        }

        $seo_cms['meta_title'] = $this->formatSeoMeta($seo_cms['meta_title'], ['post_title' => $page_name, 'is_title' => true, 'description' => '', 'description2' => '', 'category' => '', 'price' => ''], $type);
        $seo_cms['meta_description'] = $this->formatSeoMeta($seo_cms['meta_description'], ['post_title' => $page_name, 'description' => '', 'description2' => '', 'category' => '', 'price' => ''], $type);

        $this->smarty->assign([
            'ets_seo_languages' => $languages,
            'tmp_dir' => __DIR__ . '/views/templates',
            'seo_data' => $seoDef->key_phrase_input($type, $id, $this->context),
            'current_lang' => $current_lang,
            'seo_cms' => $seo_cms,
            'seo_advanced' => $seoDef->seo_advanced($type, $id, $this->context),
            'analysis_types' => $seoDef->analysis_types(),
            'seo_enabled' => 1,
            'enable_force_rating' => $enableRating,
            'readability_enabled' => 1,
            'gte820' => $this->gte820,
            'is_new_theme' => self::getRequestContainer() ? true : false,
            'enable_rating' => $this->isInstalledRatingModule(),
            'rating_config' => EtsSeoRating::getRatingConfig($type, $id),
            'rating_setting' => EtsSeoRating::getRating($type, $id),
            'meta_config' => $metaConfig,
            'forceUseMetaTemplate' => $forceUseMetaTemplate,
            'show_friendly_url' => 'manufacturer' == $type || 'supplier' == $type ? 0 : 1,
            'message_explain' => EtsSeoTranslation::getAllTrans(),
            'page_title_trans' => $pageTitleTrans,
            'show_readability' => 'meta' == $type ? false : true,
            'seo_score_data' => null,
            'isAutoAnalysis' => (int) Configuration::get('ETS_SEO_ENABLE_AUTO_ANALYSIS'),
        ]);
    }

    /**
     * seo_cms_html.
     *
     * @return void
     */
    public function seo_meta_html()
    {
        $this->addTwigVar('ETS_SEO_ENABLE_REMOVE_ID_IN_URL', (int) Configuration::get('ETS_SEO_ENABLE_REMOVE_ID_IN_URL'));
        $this->addTwigVar('ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS', (int) Configuration::get('ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS'));
        $this->addTwigVar('ETS_SEO_ENABLE_REMOVE_ID_ATTR_ALIAS', (int) Configuration::get('ETS_SEO_ENABLE_REMOVE_ID_ATTR_ALIAS'));
        $this->addTwigVar('ETS_SEO_ENABLE_REDRECT_NOTFOUND', (int) Configuration::get('ETS_SEO_ENABLE_REDRECT_NOTFOUND'));
        $this->addTwigVar('ETS_SEO_REDIRECT_STATUS_CODE', (int) Configuration::get('ETS_SEO_REDIRECT_STATUS_CODE'));
        $this->addTwigVar('ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL', (int) Configuration::get('ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL'));
        $this->addTwigVar('titleRemoveLangCode', $this->l('Remove ISO code in URL for default language'));
        $this->addTwigVar('titleRemoveAttrAlias', $this->l('Remove attribute alias in URL'));
        $this->addTwigVar('titleRemoveIdAttrAlias', $this->l('Remove ID attribute alias in URL'));
        $cacheId = $this->_getCacheId(['seo_meta_html' => $this->getIdCurrentPage()]);
        $settingTpl = 'page/seo_setting.tpl';
        $analysisTpl = 'page/seo_analysis.tpl';
        $metaTpl = 'page/meta_title.tpl';
        $meta3Tpl = 'page/meta_title_b3.tpl';
        $previewTpl = 'parts/_preview_seo_analysis.tpl';
        $cacheB3 = !version_compare(_PS_VERSION_, '8.0.0', '<') && !self::getRequestContainer() && !$this->isCached($meta3Tpl, $cacheId);
        if (!$this->isCached($settingTpl, $cacheId) || !$this->isCached($analysisTpl, $cacheId) || !$this->isCached($metaTpl, $cacheId) || $cacheB3 || !$this->isCached($previewTpl, $cacheId)) {
            $this->assignPageParams('meta');
        }
        if (version_compare(_PS_VERSION_, '8.0.0', '<') && self::getRequestContainer()) {
            if ($this->isMetaController()) {
                $this->addTwigVar('ets_meta_seo_setting_html',  $this->display(__FILE__, 'page/seo_setting.tpl', $cacheId));
                $this->addTwigVar('ets_meta_seo_analysis_html', $this->display(__FILE__, 'page/seo_analysis.tpl', $cacheId) );
                $this->addTwigVar('ets_meta_seo_meta_title', $this->display(__FILE__, 'page/meta_title.tpl', $cacheId) );
                $this->addTwigVar('ets_seo_preview_analysis', $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl', $cacheId));
            }
        } else {
            $this->context->smarty->assign([
                'ets_meta_seo_setting_html' =>  $this->display(__FILE__, 'page/seo_setting.tpl', $cacheId) ,
                'ets_meta_seo_analysis_html' => $this->display(__FILE__, 'page/seo_analysis.tpl', $cacheId),
                'ets_meta_seo_meta_title' =>  $this->display(__FILE__, 'page/meta_title_b3.tpl', $cacheId) ,
                'ets_seo_preview_analysis' =>  $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl', $cacheId),
            ]);
        }
    }

    public function seo_category_html()
    {
        $this->assignPageParams('category');
        $controller = ($controller = Tools::getValue('controller')) && Validate::isControllerName($controller) ? $controller : '';
        if (version_compare(_PS_VERSION_, '8.0.0', '<') && self::getRequestContainer()) {
            if ('AdminCategories' == $controller) {
                $this->addTwigVar('ets_category_seo_setting_html',  $this->display(__FILE__, 'page/seo_setting.tpl'));
                $this->addTwigVar('ets_category_seo_analysis_html',  $this->display(__FILE__, 'page/seo_analysis.tpl'));
                $this->addTwigVar('ets_seo_preview_analysis', $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl'));
            }
        } else {
            $this->context->smarty->assign([
                'ets_category_seo_setting_html' =>  $this->display(__FILE__, 'page/seo_setting.tpl'),
                'ets_category_seo_analysis_html' =>  $this->display(__FILE__, 'page/seo_analysis.tpl') ,
                'ets_seo_preview_analysis' =>  $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl') ,
            ]);
        }
    }

    public function seo_manufacturer_html()
    {
        $this->assignPageParams('manufacturer');
        $controller = ($controller = Tools::getValue('controller')) && Validate::isControllerName($controller) ? $controller : '';
        if (version_compare(_PS_VERSION_, '8.0.0', '<') && self::getRequestContainer()) {
            if ('AdminManufacturers' == $controller) {
                $this->addTwigVar('ets_manufacturer_seo_setting_html',  $this->display(__FILE__, 'page/seo_setting.tpl'));
                $this->addTwigVar('ets_manufacturer_seo_analysis_html',  $this->display(__FILE__, 'page/seo_analysis.tpl'));
                $this->addTwigVar('ets_seo_preview_analysis', $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl'));
            }
        } else {
            $this->context->smarty->assign([
                'ets_manufacturer_seo_setting_html' =>  $this->display(__FILE__, 'page/seo_setting.tpl') ,
                'ets_manufacturer_seo_analysis_html' =>  $this->display(__FILE__, 'page/seo_analysis.tpl') ,
                'ets_seo_preview_analysis' => $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl') ,
            ]);
        }
    }

    public function seo_supplier_html()
    {
        $this->assignPageParams('supplier');
        $controller = ($controller = Tools::getValue('controller')) && Validate::isControllerName($controller) ? $controller : '';
        if (version_compare(_PS_VERSION_, '8.0.0', '<') && self::getRequestContainer()) {
            if ('AdminSuppliers' == $controller) {
                $this->addTwigVar('ets_supplier_seo_setting_html', $this->display(__FILE__, 'page/seo_setting.tpl') );
                $this->addTwigVar('ets_supplier_seo_analysis_html',  $this->display(__FILE__, 'page/seo_analysis.tpl') );
                $this->addTwigVar('ets_seo_preview_analysis', $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl'));
            }
        } else {
            $this->context->smarty->assign([
                'ets_supplier_seo_setting_html' =>  $this->display(__FILE__, 'page/seo_setting.tpl') ,
                'ets_supplier_seo_analysis_html' =>  $this->display(__FILE__, 'page/seo_analysis.tpl') ,
                'ets_seo_preview_analysis' =>  $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl') ,
            ]);
        }
    }

    public function seo_product_html()
    {
        $id_product = $this->getIdCurrentPage();
        $id_lang = $this->context->language->id;
        if (!$id_product) {
            return;
        }
        $cacheId = $this->_getCacheId(['seo_product_html' => $id_product, 'id_lang' => $id_lang]);
        $tplAnalysis = 'page/seo_analysis.tpl';
        $tplPreview = 'parts/_preview_seo_analysis.tpl';
        if (!$this->isCached($tplAnalysis, $cacheId) || !$this->isCached($tplPreview, $cacheId)) {
            $languages = Language::getLanguages(true);
            $seo_product = [
                'link' => [],
                'link_rewrite' => [],
                'meta_description' => [],
                'meta_title' => [],
                'key_phrase' => [],
            ];
            $images = [];
            $metaConfig = [];
            foreach ($languages as $lang) {
                $product = new Product($id_product, false, $lang['id_lang']);
                $seo_product['link'][$lang['id_lang']] = $this->context->link->getProductLink($product, null, null, null, $lang['id_lang']);
                $seo_product['link_rewrite'][$lang['id_lang']] = $product->link_rewrite;
                $seo_product['meta_title'][$lang['id_lang']] = $product->meta_title ? $product->meta_title : $product->name;
                $seo_product['meta_description'][$lang['id_lang']] = $product->meta_description ? $product->meta_description : $product->description_short;
                $images[$lang['id_lang']] = $product->getImages($lang['id_lang'], $this->context);
                $metaConfig[$lang['id_lang']] = [
                    'title' => (string) Configuration::get('ETS_SEO_PROD_META_TITLE', $lang['id_lang']),
                    'desc' => (string) Configuration::get('ETS_SEO_PROD_META_DESC', $lang['id_lang']),
                ];
            }
            $current_lang = [
                'id' => $this->context->language->id,
                'iso_code' => $this->context->language->iso_code,
            ];
            if (count($languages)) {
                $current_lang = [
                    'id' => $languages[0]['id_lang'],
                    'iso_code' => $languages[0]['iso_code'],
                ];
            }
            $ratingConfig = Configuration::get('ETS_SEO_RATING_PAGES') ? explode(',', Configuration::get('ETS_SEO_RATING_PAGES')) : [];
            $seoDef = Ets_Seo_Define::getInstance();
            $currentProduct = new Product($id_product);
            $currentCategoryName = null;
            $currentCategory = new Category($currentProduct->id_category_default);
            if ($currentCategory->id) {
                $currentCategoryName = $currentCategory->name;
            }
            $this->smarty->assign([
                'ets_seo_languages' => $languages,
                'tmp_dir' => __DIR__ . '/views/templates',
                'seo_data' => $seoDef->key_phrase_input('product', $id_product, $this->context),
                'current_lang' => $current_lang,
                'analysis_types' => $seoDef->analysis_types(),
                'seo_cms' => $seo_product,
                'id_product' => $id_product,
                'gte820' => $this->gte820,
                'is_new_theme' => self::getRequestContainer() ? true : false,
                'seo_enabled' => 1,
                'enable_force_rating' => in_array('product', $ratingConfig) ? true : false,
                'readability_enabled' => 1,
                'enable_rating' => $this->isInstalledRatingModule(),
                'rating_config' => EtsSeoRating::getRatingConfig('product', $id_product),
                'rating_setting' => EtsSeoRating::getRating('product', $id_product),
                'seo_advanced' => $seoDef->seo_advanced('product', $id_product, $this->context),
                'in_product_page' => true,
                'comment_product_data' => EtsSeoProduct::getCommentProductData($id_product),
                'product_image_data' => $images,
                'meta_config' => $metaConfig,
                'forceUseMetaTemplate' => (int) Configuration::get('ETS_SEO_PROD_FORCE_USE_META_TEMPLATE'),
                'show_friendly_url' => true,
                'message_explain' => EtsSeoTranslation::getAllTrans(),
                'page_title_trans' => EtsSeoTranslation::trans('product_name'),
                'seo_score_data' => null,
                'isAutoAnalysis' => (int) Configuration::get('ETS_SEO_ENABLE_AUTO_ANALYSIS'),
                'defaultCategoryName' => $currentCategoryName,
                'gt900' => version_compare(_PS_VERSION_, '9.0.0', '>=')
            ]);
        }

        if (!Tools::isSubmit('ajax') && version_compare(_PS_VERSION_, '8.0.0', '<') && self::getRequestContainer()) {
            $this->addTwigVar('ets_seo_product_seo_analysis', $this->display(__FILE__, 'page/seo_analysis.tpl', $cacheId));
            $this->addTwigVar('ets_seo_preview_analysis', $this->display(__FILE__, 'parts/_preview_seo_analysis.tpl', $cacheId));
        }
    }

    /**
     * hookDisplayCustomAdminProductsSeoStepBottom.
     *
     * @param mixed $params
     *
     * @return string
     */
    public function hookDisplayCustomAdminProductsSeoStepBottom($params)
    {
        if (!isset($params['id_product']) || !@$params['id_product']) {
            return '';
        }
        $id_product = $params['id_product'];
        $cacheId = $this->_getCacheId(['displayCustomAdminProductsSeoStepBottom' => $id_product]);
        if (!$this->isCached('page/seo_setting.tpl', $cacheId)) {
            $languages = Language::getLanguages(false);
            $current_lang = [
                'id' => $this->context->language->id,
                'iso_code' => $this->context->language->iso_code,
            ];

            $seoDef = Ets_Seo_Define::getInstance();
            $ratingConfig = Configuration::get('ETS_SEO_RATING_PAGES') ? explode(',', Configuration::get('ETS_SEO_RATING_PAGES')) : [];

            $this->smarty->assign([
                'seo_data' => $seoDef->key_phrase_input('product', $id_product, $this->context),
                'ets_seo_languages' => $languages,
                'is_new_theme' => (bool) self::getRequestContainer(),
                'gte820' => $this->gte820,
                'current_lang' => $current_lang,
                'analysis_types' => $seoDef->analysis_types(),
                //'seo_advanced' => $seoDef->seo_advanced('product', $id_product, $this->context),
                'seo_enabled' => 1,
                //'enable_force_rating' => in_array('product', $ratingConfig),
                //'enable_rating' => $this->isInstalledRatingModule(),
                //'rating_config' => EtsSeoRating::getRatingConfig('product', $id_product),
                //'rating_setting' => EtsSeoRating::getRating('product', $id_product),
            ]);
        }
        return $this->display(__FILE__, 'page/seo_setting.tpl', $cacheId);
    }

    public function isInstalledRatingModule()
    {
        if (Module::isInstalled('productcomments') && Module::isEnabled('productcomments')) {
            return true;
        }

        return false;
    }

    /**
     * hookActionObjectAddAfter.
     *
     * @param array $params
     *
     * @return void
     */
    public function hookActionObjectAddAfter($params)
    {
        EtsSeoCms::updateSeoCms($params);
        EtsSeoMeta::updateSeoMeta($params);
        EtsSeoCategory::updateSeoCategory($params);
        EtsSeoCmsCategory::updateSeoCmsCategory($params);
        EtsSeoManufacturer::updateSeoManufacturer($params);
        EtsSeoSupplier::updateSeoSupplier($params);

        if (isset($params['object'])) {
            $object = $params['object'];
            if ($object instanceof Manufacturer) {
                EtsSeoManufacturer::updateLinkRewriteManufacturer($object->id, $object->name);
            }
            if ($object instanceof Supplier) {
                EtsSeoSupplier::updateLinkRewriteSupplier($object->id, $object->name);
            }
        }
    }

    /**
     * hookActionObjectUpdateAfter.
     *
     * @param array $params
     *
     * @return void
     */
    public function hookActionObjectUpdateAfter($params)
    {
        $this->_clearCacheWhenObjectUpdated($params['object']);
        if ($params['object'] instanceof Product) {
            static $hidePrice;
            if (!isset($hidePrice)) {
                $hidePrice = (bool) Configuration::get('ETS_SEO_HIDE_PRICE_FROM_SCHEMA');
            }
            $cacheId = $this->_getCacheId(['displayProductAdditionalInfo' => ['id' => $params['object']->id, 'hidePrice' => (int) $hidePrice]], true);
            $this->_clearCache('*', $cacheId);
            $this->_clearCache('*', $this->_getCacheId(['seo_product_html' => $params['object']->id], true));
        }
        EtsSeoCms::updateSeoCms($params);
        EtsSeoMeta::updateSeoMeta($params);
        EtsSeoCategory::updateSeoCategory($params);
        EtsSeoCmsCategory::updateSeoCmsCategory($params);
        EtsSeoManufacturer::updateSeoManufacturer($params);
        EtsSeoSupplier::updateSeoSupplier($params);
        $removeId = (int) Tools::getValue('ETS_SEO_ENABLE_REMOVE_ID_IN_URL');
        if (!self::getRequestContainer() && $removeId) {
            $this->processAfterSaveConfig();
        }
        if (isset($params['object'])) {
            $object = $params['object'];
            if ($object instanceof Manufacturer) {
                EtsSeoManufacturer::updateLinkRewriteManufacturer($object->id, $object->name);
            }
            if ($object instanceof Supplier) {
                EtsSeoSupplier::updateLinkRewriteSupplier($object->id, $object->name);
            }
        }
    }

    public function hookActionObjectAddBefore($params)
    {
        if (!defined('_PS_ADMIN_DIR_')) {
            return;
        }
        if (self::getRequestContainer()) {
            // Removed
        } else {
            $this->validateLinkRewrite($params);
        }
    }

    public function hookActionObjectUpdateBefore($params)
    {
        if (!defined('_PS_ADMIN_DIR_')) {
            return;
        }
        if (self::getRequestContainer()) {
            if (isset($params['object']) && $params['object'] instanceof Product && preg_match('/sell\/catalog\/products/', $_SERVER['REQUEST_URI'])) {
                if ($error = EtsSeoSetting::validateLinkRewrite('product', $params['object']->link_rewrite, (int) $params['object']->id, $this->context)) {
                    throw new PrestaShopException($this->l('The Friendly url "' . $error . '" ' . $this->l('has been taken')));
                }
                $error = null;
                $seoAdvanced = ($seoAdvanced = Tools::getValue('ets_seo_advanced')) && is_array($seoAdvanced) ? $seoAdvanced : [];
                if (!empty($seoAdvanced['canonical_url'])) {
                    foreach ($seoAdvanced['canonical_url'] as $id_lang => $url) {
                        if ($url && !self::_isAbsoluteUrl($url)) {
                            $error = '[' . Language::getIsoById($id_lang) . '] ' . $this->l('The Canonical url must start with http:// or https:// ');
                            break;
                        }
                    }
                }
                if (!$error && ($minorKeyphrase = Tools::getValue('ets_seo_minor_keyphrase')) && ($keyphrase = Tools::getValue('ets_seo_key_phrase'))) {
                    $seoSetting = EtsSeoSetting::getInstance();
                    if (is_array($keyphrase)) {
                        foreach ($keyphrase as $id_lang => $key) {
                            if (isset($minorKeyphrase[$id_lang]) && $minorKeyphrase[$id_lang] && $key) {
                                $minor = $seoSetting->getMinorKeyphrase($minorKeyphrase[$id_lang]);
                                if ($minor && in_array(trim($key), explode(',', $minor))) {
                                    $error = '[' . Language::getIsoById($id_lang) . '] ' . $this->l('The related keyphrase is the same as focus keyphrase');
                                    break;
                                }
                            }
                        }
                    }
                }
                if ($error) {
                    throw new PrestaShopException($error);
                }
            }
        } else {
            $this->validateLinkRewrite($params);
        }
    }
    public static function _isAbsoluteUrl($url)
    {
        if (!empty($url)) {
            return preg_match('/^(https?:)?\/\/[$~:;#,%&_=\(\)\[\]\.\? \+\-\p{Arabic}@\/a-zA-Z0-9]+$/u', $url);
        }

        return true;
    }
    public function formHandleLinkRewrite($params, $type)
    {
        $id = isset($params['id']) ? (int) $params['id'] : null;
        $redirectUrl = null;
        switch ($type) {
            case 'category':
                if ($id) {
                    $redirectUrl = $this->context->link->getAdminLink(
                        'AdminCategories',
                        true,
                        ['route' => 'admin_categories_edit', 'categoryId' => $id],
                        ['id_category' => $id, 'updatecategory' => true]
                    );
                } else {
                    $idParent = (int) $params['form_data']['id_parent'];
                    $redirectUrl = $this->context->link->getAdminLink(
                        'AdminCategories',
                        true,
                        ['route' => 'admin_categories_create', 'id_parent' => $idParent],
                        ['addcategory' => true]
                    );
                }
                break;
            case 'cms':
                if ($id) {
                    $redirectUrl = $this->context->link->getAdminLink(
                        'AdminCms',
                        true,
                        ['route' => 'admin_cms_pages_edit', 'cmsPageId' => $id],
                        ['id_cms' => $id, 'updatecms' => true]
                    );
                } else {
                    $idParent = (int) @$params['form_data']['id_cms_category'] ?: @$params['form_data']['page_category_id'];
                    if ($idParent) {
                        $redirectUrl = $this->context->link->getAdminLink(
                            'AdminCms',
                            true,
                            ['route' => 'admin_cms_pages_create', 'id_cms_category' => $idParent],
                            ['addcms' => true, 'id_cms_category' => $idParent]
                        );
                    }
                }
                break;
            case 'cms_category':
                if ($id) {
                    $redirectUrl = $this->context->link->getAdminLink(
                        'AdminCmsCategories',
                        true,
                        ['route' => 'admin_cms_pages_category_edit', 'cmsCategoryId' => $id],
                        ['id_cms_category' => $id, 'updatecms_category' => true]
                    );
                } else {
                    $redirectUrl = $this->context->link->getAdminLink(
                        'AdminCmsCategories',
                        true,
                        ['route' => 'admin_cms_pages_category_create'],
                        ['addcms_category' => true]
                    );
                }
                break;
            case 'meta':
                if ($id) {
                    $redirectUrl = $this->context->link->getAdminLink(
                        'AdminMeta',
                        true,
                        ['route' => 'admin_metas_edit', 'metaId' => $id],
                        ['id_meta' => $id, 'updatemeta' => true]
                    );
                } else {
                    $redirectUrl = $this->context->link->getAdminLink(
                        'AdminMeta',
                        true,
                        ['route' => 'admin_metas_create'],
                        ['addmeta' => true]
                    );
                }
                break;
        }
        // fom_data
        $linkRewrites = '';
        if (isset($params['form_data']['link_rewrite'])) {
            $linkRewrites = $params['form_data']['link_rewrite'];
        } elseif (isset($params['form_data']['url_rewrite'])) {
            $linkRewrites = $params['form_data']['url_rewrite'];
        } elseif (isset($params['form_data']['friendly_url'])) {
            $linkRewrites = $params['form_data']['friendly_url'];
        }
        if ('category' == $type && isset($params['form_data']['id_parent']) && $params['form_data']['id_parent']) {
            $parentCat = new \Category($params['form_data']['id_parent']);
            $isRootCat = (1 == $parentCat->id_parent || 0 == $parentCat->id_parent);
            $error = $isRootCat ? EtsSeoSetting::validateLinkRewrite($type, $linkRewrites, $id, $this->context) : false;
        } else {
            $error = EtsSeoSetting::validateLinkRewrite($type, $linkRewrites, $id, $this->context);
        }
        if ($error) {
            $this->l('The link rewrite') . ' "' . $error . '" ' . $this->l('has been taken');
        }
        $seoAdvanced = Tools::getValue('ets_seo_advanced');
        if (isset($seoAdvanced['canonical_url']) && !$error) {
            foreach ($seoAdvanced['canonical_url'] as $id_lang => $url) {
                if ($url && !self::_isAbsoluteUrl($url)) {
                    $error = '[' . Language::getIsoById($id_lang) . '] ' . $this->l('The Canonical url must start with http:// or https:// ');
                    break;
                }
            }
        }
        if (!$error && ($minorKeyphrase = Tools::getValue('ets_seo_minor_keyphrase')) && ($keyphrase = Tools::getValue('ets_seo_key_phrase'))) {
            $seoSetting = EtsSeoSetting::getInstance();
            if (is_array($keyphrase)) {
                foreach ($keyphrase as $id_lang => $key) {
                    if (isset($minorKeyphrase[$id_lang]) && $minorKeyphrase[$id_lang] && $key) {
                        $minor = $seoSetting->getMinorKeyphrase($minorKeyphrase[$id_lang]);
                        if ($minor && in_array(trim($key), explode(',', $minor))) {
                            $error = '[' . Language::getIsoById($id_lang) . '] ' . $this->l('The related keyphrase is the same as focus keyphrase');
                            break;
                        }
                    }
                }
            }
        }

        if ($error) {
            $params['form_data']['ets_seo_error'] = $error;
            $fileName = time() . rand(1111, 99999) . '.json';
            file_put_contents(__DIR__ . '/cache/' . $fileName, json_encode($params['form_data']));
            $this->context->cookie->__set('ets_seo_form_validate_data', $fileName);
            Tools::redirectAdmin($redirectUrl);
        }
    }

    public function setFormBuilderModifier(&$params)
    {
        if ($fileData = $this->context->cookie->__get('ets_seo_form_validate_data')) {
            $data = [];
            if (file_exists(__DIR__ . '/cache/' . $fileData)) {
                $json = Tools::file_get_contents(__DIR__ . '/cache/' . $fileData);
                $data = json_decode($json, true);
                unlink(__DIR__ . '/cache/' . $fileData);
            }
            $error = '';
            if (isset($data['ets_seo_error'])) {
                $error = $data['ets_seo_error'];
                unset($data['ets_seo_error']);
            }
            $params['data'] = array_merge($params['data'], $data);
            $params['form_builder']->setData($params['data']);
            $this->context->cookie->__unset('ets_seo_form_validate_data');
            $this->context->cookie->__set('ets_seo_error_link_rewrite', $error);
        }
    }

    /* == Category === */
    public function hookActionBeforeUpdateCategoryFormHandler($params)
    {
        $this->formHandleLinkRewrite($params, 'category');
    }

    public function hookActionBeforeCreateCategoryFormHandler($params)
    {
        // fom_data
        $this->formHandleLinkRewrite($params, 'category');
    }

    public function hookActionCategoryFormBuilderModifier($params)
    {
        $this->setFormBuilderModifier($params);
    }

    // Root category
    public function hookActionBeforeUpdateRootCategoryFormHandler($params)
    {
        $this->formHandleLinkRewrite($params, 'category');
    }

    public function hookActionBeforeCreateRootCategoryFormHandler($params)
    {
        // fom_data
        $this->formHandleLinkRewrite($params, 'category');
    }

    public function hookActionRootCategoryFormBuilderModifier($params)
    {
        $this->setFormBuilderModifier($params);
    }

    /* = CMS == */
    public function hookActionBeforeUpdateCmsPageFormHandler($params)
    {
        $this->formHandleLinkRewrite($params, 'cms');
    }

    public function hookActionBeforeCreateCmsPageFormHandler($params)
    {
        // fom_data
        $this->formHandleLinkRewrite($params, 'cms');
    }

    public function hookActionCmsPageFormBuilderModifier($params)
    {
        $this->setFormBuilderModifier($params);
    }

    /* = CMS Category == */
    public function hookActionBeforeUpdateCmsPageCategoryFormHandler($params)
    {
        $this->formHandleLinkRewrite($params, 'cms_category');
    }

    public function hookActionBeforeCreateCmsPageCategoryFormHandler($params)
    {
        // fom_data
        $this->formHandleLinkRewrite($params, 'cms_category');
    }

    public function hookActionCmsPageCategoryFormBuilderModifier($params)
    {
        $this->setFormBuilderModifier($params);
    }

    /* = Meta == */
    public function hookActionBeforeUpdateMetaFormHandler($params)
    {
        $this->formHandleLinkRewrite($params, 'meta');
    }

    public function hookActionBeforeCreateMetaFormHandler($params)
    {
        // fom_data
        $this->formHandleLinkRewrite($params, 'meta');
    }

    public function hookActionMetaFormBuilderModifier($params)
    {
        $this->setFormBuilderModifier($params);
    }

    public function validateLinkRewrite($params)
    {
        if (!(int) Configuration::get('ETS_SEO_ENABLE_REMOVE_ID_IN_URL')) {
            return;
        }
        if (isset($params['object'])) {
            $type = null;
            $link_rewrites = null;
            $obj = $params['object'];
            $idCol = '';
            if ($obj instanceof Product) {
                $type = 'product';
                $link_rewrites = $obj->link_rewrite;
                $idCol = 'id_product';
            } elseif ($obj instanceof Category) {
                $type = 'category';
                $link_rewrites = $obj->link_rewrite;
                $idCol = 'id_category';
            } elseif ($obj instanceof CMS) {
                $type = 'cms';
                $link_rewrites = $obj->link_rewrite;
                $idCol = 'id_cms';
            } elseif ($obj instanceof CMSCategory) {
                $type = 'cms_category';
                $link_rewrites = $obj->link_rewrite;
                $idCol = 'id_cms_category';
            } elseif ($obj instanceof Meta) {
                $type = 'meta';
                $link_rewrites = $obj->url_rewrite;
                $idCol = 'id_meta';
            }

            if (!$type) {
                return;
            }
            $error = EtsSeoSetting::validateLinkRewrite($type, $link_rewrites, $obj->id, $this->context);
            if ($error) {
                $error = $this->l('The Friendly url') . ' ' . $error . ' ' . $this->l(' has been taken');
            }
            $seoAdvanced = Tools::getValue('ets_seo_advanced');
            if (isset($seoAdvanced['canonical_url']) && !$error) {
                foreach ($seoAdvanced['canonical_url'] as $id_lang => $url) {
                    if ($url && !self::_isAbsoluteUrl($url)) {
                        $error = '[' . Language::getIsoById($id_lang) . '] ' . $this->l('The Canonical url must start with http:// or https:// ');
                        break;
                    }
                }
            }
            if (!$error && ($minorKeyphrase = Tools::getValue('ets_seo_minor_keyphrase')) && ($keyphrase = Tools::getValue('ets_seo_key_phrase'))) {
                $seoSetting = EtsSeoSetting::getInstance();
                if (is_array($keyphrase)) {
                    foreach ($keyphrase as $id_lang => $key) {
                        if (isset($minorKeyphrase[$id_lang]) && $minorKeyphrase[$id_lang] && $key) {
                            $minor = $seoSetting->getMinorKeyphrase($minorKeyphrase[$id_lang]);
                            if ($minor && in_array(trim($key), explode(',', $minor))) {
                                $error = '[' . Language::getIsoById($id_lang) . '] ' . $this->l('The related keyphrase is the same as focus keyphrase');
                                break;
                            }
                        }
                    }
                }
            }
            if ($error) {
                if ('cms' !== $type) {
                    throw new PrestaShopException($error);
                } else {
                    $controller = ($controller = Tools::getValue('controller')) && Validate::isControllerName($controller) ? $controller : '';
                    $this->context->cookie->__set('ets_seo_error_link_rewrite', $error);
                    if ($obj->id) {
                        $redirectUrl = $this->context->link->getAdminLink($controller, true, [], [
                            $idCol => $obj->id,
                            'updatecms' => true,
                        ]);
                    } else {
                        $redirectUrl = $this->context->link->getAdminLink($controller, true, [], [
                            $idCol => $obj->id,
                            'id_cms_category' => $obj->id_cms_category,
                            'addcms' => true,
                        ]);
                    }

                    Tools::redirectAdmin($redirectUrl);
                }
            }
        }
    }

    public function processAfterSaveConfig()
    {
        Tools::clearCache();
        /* Update product has duplicate link_rewrite */
        $removeID = (int) Tools::getValue('ETS_SEO_ENABLE_REMOVE_ID_IN_URL');
        if ($removeID) {
            if (!(int) Configuration::get('ETS_SEO_UPDATE_DUPLICATE_REWRITE')) {
                $seoUpdating = new EtsSeoUpdating();
                $seoUpdating->updateDuplicateProduct();
                $seoUpdating->updateDuplicateCategory();
                $seoUpdating->updateDuplicateCMS();
                $seoUpdating->updateDuplicateCMSCategory();
                $seoUpdating->updateDuplicateMeta();
                Configuration::updateValue('ETS_SEO_UPDATE_DUPLICATE_REWRITE', 1);
            }
        }
    }

    /**
     * hookDisplayAdminAfterHeader.
     *
     * @return string
     */
    public function hookDisplayAdminAfterHeader()
    {
        $seoDef = Ets_Seo_Define::getInstance();
        $controller = ($controller = Tools::getValue('controller')) && Validate::isControllerName($controller) ? $controller : '';
        $cacheId = $this->_getCacheId(['hookDisplayAdminAfterHeader' => $controller]);
        if (!$this->isCached('admin_menu.tpl', $cacheId) || !$this->isCached('admin_breadcrumb.tpl', $cacheId)) {
            $menus = [];
            $submenus = [];
            $showMenu = false;
            $all_menus = $seoDef->get_menus();

            if ($trafficControllers = $seoDef->traffic_seo_tabs()) {
                if (in_array($controller, $trafficControllers)) {
                    $showMenu = true;
                }
            }
            $tabArray = [];
            foreach ($all_menus as $k => $menu) {
                if (!$showMenu && $controller == $k) {
                    $showMenu = true;
                }
                if (isset($menu['parent_controller']) && $menu['parent_controller']) {
                    $submenus[$k] = $menu;
                } else {
                    $menus[$k] = $menu;
                }
                if ('AdminEtsSeoUrlAndRemoveId' == $k) {
                    $submenus['AdminMeta'] = [
                        'title' => $this->l('URL structure and remove IDs'),
                        'controller' => 'AdminMeta',
                        'parent_controller' => 'AdminEtsSeoUrlAndRemoveId',
                        'icon' => 'code',
                        'menu_icon' => 'menu-icon-meta',
                        'link' => $this->context->link->getAdminLink('AdminEtsSeoUrlAndRemoveId', true),
                    ];
                    $tabArray['AdminMeta'] = $submenus['AdminMeta'];
                } elseif ('AdminEtsSeoTraffic' == $k) {
                    $psTabs = ['AdminSearchEngines' => 'menu-icon-search-engines', 'AdminReferrers' => 'menu-icon-referrers'];
                    foreach ($psTabs as $psTab => $menuIcon) {
                        $id_tab = Tab::getIdFromClassName($psTab);
                        if ($id_tab) {
                            $tab = Tab::getTab($this->context->language->id, $id_tab);
                            $submenus[$psTab] = [
                                'title' => $tab['name'],
                                'controller' => $psTab,
                                'link' => $this->context->link->getAdminLink($psTab, true),
                                'parent_controller' => 'AdminEtsSeoTraffic',
                                'menu_icon' => $menuIcon,
                                'icon' => 'code',
                            ];
                            $tabArray[$psTab] = $submenus[$psTab];
                        }
                    }
                }
            }
            if (!$showMenu) {
                return '';
            }

            $all_menus = array_merge($tabArray, $all_menus);
            $parent_controller = null;
            if (isset($all_menus[$controller]['parent_controller']) && $all_menus[$controller]['parent_controller']) {
                $parent_controller = $all_menus[$controller]['parent_controller'];
            }
            $page_name = '';
            if ('AdminMeta' == $controller) {
                if ($request = self::getRequestContainer()) {
                    $metaId =   $request->get('metaId');
                    if ($metaId) {
                        $meta = new Meta($metaId, $this->context->language->id);
                        $page_name = $this->formatSeoMeta($meta->title, ['post_title' => '', 'is_title' => true], 'meta');
                    }
                } else {
                    if ($metaId = (int) Tools::getValue('id_meta')) {
                        $meta = new Meta($metaId, $this->context->language->id);
                        $page_name = $this->formatSeoMeta($meta->title, ['post_title' => '', 'is_title' => true], 'meta');
                    }
                }
            }
            $this->smarty->assign([
                'all_menus' => $all_menus,
                'menus' => $menus,
                'is178' => $this->is178,
                'submenus' => $submenus,
                'current_controller' => $controller,
                'parent_controller' => $parent_controller,
                'page_name' => $page_name,
                'controller_link' => $page_name ? $this->context->link->getAdminLink($controller) : '',
                'dashboard_controller' => $all_menus['AdminEtsSeoGeneralDashboard'],
                'refsLink' => isset($this->refs) ? $this->refs . $this->context->language->iso_code : false,
            ]);
        }

        return $this->display(__FILE__, 'admin_menu.tpl', $cacheId) . $this->display(__FILE__, 'admin_breadcrumb.tpl', $cacheId);
    }

    public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 60)
    {
        if (null == $stream_context && preg_match('/^https?:\/\//', $url)) {
            $stream_context = stream_context_create([
                'http' => [
                    'timeout' => $curl_timeout,
                    'max_redirects' => 101,
                    'header' => 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
                ],
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);
        }
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => html_entity_decode($url),
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => $curl_timeout,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_FOLLOWLOCATION => true,
            ]);
            $content = curl_exec($curl);
            curl_close($curl);

            return $content;
        }

        if (in_array(ini_get('allow_url_fopen'), ['On', 'on', '1']) || !preg_match('/^https?:\/\//', $url)) {
            return Tools::file_get_contents($url, $use_include_path, $stream_context);
        }

        return false;
    }

    /**
     * array_merge.
     *
     * @param string $sort_name
     *
     * @return array
     */
    public function get_fields_list_page($sort_name)
    {
        return [
            'seo_score' => [
                'title' => $this->l('SEO Score'),
                'align' => 'text-center',
                'float' => true,
                'type' => 'select',
                'filter_key' => $sort_name . '!seo_score',
                'orderby' => false,
                'list' => [
                    'bad' => $this->l('SEO: Not good'),
                    'ok' => $this->l('SEO: Acceptable'),
                    'good' => $this->l('SEO: Excellent'),
                    'na' => $this->l('SEO: No Focus or Related key phrases'),
                    'noindex' => $this->l('SEO: No Index'),
                ],
            ],
            'readability_score' => [
                'title' => $this->l('Readability Score'),
                'align' => 'text-center',
                'float' => true,
                'type' => 'select',
                'orderby' => false,
                'filter_key' => $sort_name . '!readability_score',
                'list' => [
                    'bad' => $this->l('Readability: Not good'),
                    'ok' => $this->l('Readability: Acceptable'),
                    'good' => $this->l('Readability: Excellent'),
                ],
            ],
        ];
    }

    /**
     * @param $params
     */
    public function hookActionFrontControllerRedirectBefore($params)
    {
        if ('404' == $params['redirect_after']) {
            EtsSeoNotFoundUrl::checkAndUpdatePageNotFoundUrl($params['controller'], true);
        }
    }

    public function hookDisplayHeader()
    {
        $ctlName = ($ctlName = Tools::getValue('controller')) && Validate::isControllerName($ctlName) ? $ctlName : '';
        if(Configuration::get('PS_REWRITING_SETTINGS') && (bool)Configuration::get('ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL'))
        {
            $this->context->controller->addJS($this->_path . '/views/js/editLink.js');
        }
        if (EtsSeoNotFoundUrl::checkAndUpdatePageNotFoundUrl($this->context->controller)) {
            return '';
        }
        $isModuleCtl = Tools::getValue('fc') && 'module' === Tools::getValue('fc');
        if ($isModuleCtl && 'preview' === $ctlName && 'creativeelements' === Tools::getValue('module')) {
            return '';
        }
        $meta = null;
        $this->getJsDefHelper()->setFo(null, [
            'currentController' => $ctlName,
            'conf' => [
                'removeId' => (bool) Configuration::get('ETS_SEO_ENABLE_REMOVE_ID_IN_URL'),
            ],
        ]);
        if (!$isModuleCtl && in_array($ctlName, Ets_Seo_Define::getInstance()->getMetaOverriddenControllers(), true)) {
            if ('manufacturer' === $ctlName && ($id = Tools::getValue('id_manufacturer'))) {
                $meta = $this->getSeoMetaDataArray($ctlName, $id);
            } elseif ('supplier' === $ctlName && ($id = Tools::getValue('id_supplier'))) {
                $meta = $this->getSeoMetaDataArray($ctlName, $id);
            } else {
                if ('cms' === $ctlName) {
                    $ctlName = 'cms_category';
                    $this->getJsDefHelper()->setFo('currentController', $ctlName);
                }
                $meta = $this->getSeoMetaDataArray($ctlName);
            }
            $frontJsAdded = false;
            if ('product' === $ctlName && ($prodGroups = $this->context->smarty->getTemplateVars('groups'))) {
                $this->getJsDefHelper()->setFo('productHasGroups', true);
                $groups = [];
                $anchorSeparator = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');
                $this->getJsDefHelper()->setFo('anchorSeparator', $anchorSeparator);
                /**
                 * Get attribute Url function
                 *
                 * @param string $v
                 *
                 * @return string
                 */
                $_getAttrUrl = static function ($v) use ($anchorSeparator) {
                    return str_replace($anchorSeparator, '_', Tools::str2url($v));
                };
                foreach ($prodGroups as $idGroup => $prodGroup) {
                    $name = $_getAttrUrl($prodGroup['group_name']);
                    $curGroup = ['idGroup' => $idGroup, 'type' => 'select' === $prodGroup['group_type'] ? 'select' : 'input', 'attributes' => []];
                    foreach ($prodGroup['attributes'] as $idAttr => $attribute) {
                        $curGroup['attributes'][] = [
                            'id' => $idAttr,
                            'idAttribute' => $idAttr,
                            'name' => $attribute['name'],
                            'url' => $_getAttrUrl($attribute['name']),
                            'idGroup' => $idGroup,
                        ];
                    }
                    $groups[$name] = $curGroup;
                }
                $this->getJsDefHelper()->setFo('productGroups', $groups)->setFo('productId', Tools::getValue('id_product'));
                $this->context->controller->addJS($this->_path . '/views/js/helpers.js');
                $this->context->controller->addJS($this->_path . '/views/js/front.js');
            }
        }
        if ((int) Configuration::get('ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL')) {
            $isoCode = $this->context->language->id == Configuration::get('PS_LANG_DEFAULT') ? '' : $this->context->language->iso_code;
            $this->context->smarty->assign([
                'ets_seo_base_url' => $this->context->shop->getBaseURL(true) . ($isoCode ? $isoCode . '/' : ''),
            ]);
        }
        @$this->getSeoMetaData(false, $meta);
        if ('product' != $ctlName) {
            return $this->display(__FILE__, 'head.tpl');
        }
    }

    /**
     * setDefaultConfig.
     *
     * @return bool
     */
    public function setDefaultConfig()
    {
        $seoDef = Ets_Seo_Define::getInstance();
        $groups = $seoDef->fields_config();
        $languages = Language::getLanguages(false);
        foreach ($groups as $configs) {
            foreach ($configs as $key => $config) {
                if (isset($config['default']) && '' !== $config['default']) {
                    Configuration::updateGlobalValue($key, $config['default']);
                } else {
                    if (isset($config['type']) && ('textLang' == $config['type'] || 'textareaLang' == $config['type'] || 'selectLang' == $config['type'])) {
                        $value = [];
                        foreach ($languages as $lang) {
                            $value[$lang['id_lang']] = '';
                        }
                        Configuration::updateGlobalValue($key, $value);
                    } else {
                        Configuration::updateGlobalValue($key, '');
                    }
                }
            }
        }
        // Create image folder
        if (!is_dir(_PS_ROOT_DIR_ . '/img/social')) {
            @mkdir(_PS_ROOT_DIR_ . '/img/social', 0755, true);
            @copy(__DIR__ . '/index.php', _PS_ROOT_DIR_ . '/img/social/index.php');
        }

        // Create cache folder
        if (!is_dir(_PS_ROOT_DIR_ . '/cache/' . $this->name)) {
            @mkdir(_PS_ROOT_DIR_ . '/cache/' . $this->name, 0755, true);
        }
        if (!@file_exists(_PS_ROOT_DIR_ . '/cache/' . $this->name . '/index.php')) {
            @copy(__DIR__ . '/index.php', _PS_ROOT_DIR_ . '/cache/' . $this->name . '/index.php');
        }

        // Allow turning off base64 encode/decode for content_analysis (ModSecurity workaround)
        Configuration::updateGlobalValue(ETS_SEO_ENABLE_CONTENT_ANALYSIS_BASE64, 0);

        return true;
    }

    public function removeAllConfigs()
    {
        $seoDef = Ets_Seo_Define::getInstance();
        $groups = $seoDef->fields_config();
        foreach ($groups as $configs) {
            foreach ($configs as $key => $config) {
                if ($config) {
                    Configuration::deleteByName($key);
                }
            }
        }
        Configuration::deleteByName('ETS_SEO_ENABLE_REMOVE_ID_IN_URL');
        Configuration::deleteByName('ETS_SEO_UPDATE_DUPLICATE_REWRITE');
        Configuration::deleteByName('ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS');
        Configuration::deleteByName('ETS_SEO_ENABLE_REMOVE_ID_ATTR_ALIAS');
        Configuration::deleteByName('ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL');
        Configuration::deleteByName('ETS_SEO_SET_REMOVE_ID');
        Configuration::deleteByName(ETS_SEO_ENABLE_CONTENT_ANALYSIS_BASE64);

        return true;
    }

    public function addTwigVar($key, $value)
    {
        if ($sfContainer = self::getSfContainer()) {
            /** @var \Twig\Environment $tw */
            $tw = $sfContainer->get('twig');
            $tw->addGlobal($key, $value);
        }
    }

    public function generateGraphWebData()
    {
        $socialConfigs = [
            'ETS_SEO_URL_FACEBOOK',
            'ETS_SEO_URL_TWITTER',
            'ETS_SEO_URL_INSTA',
            'ETS_SEO_URL_LINKEDIN',
            'ETS_SEO_URL_MYSPACE',
            'ETS_SEO_URL_PINTEREST',
            'ETS_SEO_URL_YOUTUBE',
            'ETS_SEO_URL_WIKI',
        ];

        $data = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    '@id' => $this->context->shop->getBaseURL(true, true) . '#website',
                    'url' => $this->context->shop->getBaseURL(true, true),
                    'name' => Configuration::get('PS_SHOP_NAME'),
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => $this->context->shop->getBaseURL(true, true) . 'search?s={search_term_string}',
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
            ],
        ];

        $socialLinks = [];
        foreach ($socialConfigs as $name) {
            if ($link = Configuration::get($name)) {
                $socialLinks[] = $link;
            }
        }
        $controller = ($controller = Tools::getValue('controller')) && Validate::isControllerName($controller) ? $controller : '';
        $id_product = (int) Tools::getValue('id_product');
        $id_category = (int) Tools::getValue('id_category');
        $id_cms = (int) Tools::getValue('id_cms');
        $id_cms_category = (int) Tools::getValue('id_cms_category');
        $id_manufacturer = (int) Tools::getValue('id_manufacturer');
        $id_supplier = (int) Tools::getValue('id_supplier');
        $id_meta = (int) Tools::getValue('id_meta');
        if ($typeWebsite = Configuration::get('ETS_SEO_SITE_OF_PERSON_OR_COMP')) {
            if ('PERSON' == $typeWebsite) {
                $name = Configuration::get('ETS_SEO_SITE_PERSON_NAME');
                $image = Configuration::get('ETS_SEO_SITE_PERSON_AVATAR');
                if ($name) {
                    $data['@graph'][] = [
                        '@type' => ['Person', 'Organization'],
                        '@id' => $this->context->shop->getBaseURL(true, true) . '#/schema/person/' . md5($name),
                        'name' => addslashes($name),
                        'url' => $this->context->shop->getBaseURL(true, true),
                        'sameAs' => [],

                        'image' => [
                            '@type' => 'ImageObject',
                            '@id' => $this->context->shop->getBaseURL(true, true) . '#personlogo',
                            'url' => $this->context->shop->getBaseURL(true, true) . 'img/social/' . ($image ? $image : 'default_avatar.png'),
                            'caption' => addslashes($name),
                        ],
                        'logo' => [
                            '@id' => $this->context->shop->getBaseURL(true, true) . '#personlogo',
                        ],
                    ];
                }
            } else {
                $name = Configuration::get('ETS_SEO_SITE_ORIG_NAME');
                $image = Configuration::get('ETS_SEO_SITE_ORIG_LOGO');
                if ($name && $image) {
                    $data['@graph'][] = [
                        '@type' => 'Organization',
                        '@id' => $this->context->shop->getBaseURL(true, true) . '#organization',
                        'name' => addslashes($name),
                        'url' => $this->context->shop->getBaseURL(true, true),
                        'sameAs' => $socialLinks,

                        'logo' => [
                            '@type' => 'ImageObject',
                            '@id' => $this->context->shop->getBaseURL(true, true) . '#logo',
                            'url' => $this->context->shop->getBaseURL(true, true) . 'img/social/' . $image,
                            'caption' => addslashes($name),
                        ],
                        'image' => [
                            '@id' => $this->context->shop->getBaseURL(true, true) . '#logo',
                        ],
                    ];
                }
            }
        }

        if ((int) Configuration::get('ETS_SEO_BREADCRUMB_ENABLED')) {
            $breadcrumb = [
                '@type' => 'BreadcrumbList',
                '@id' => $this->context->shop->getBaseURL(true, true) . '#breadcrumb',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'item' => [
                            '@type' => 'WebPage',
                            'name' => Configuration::get('ETS_SEO_BREADCRUMB_ANCHOR_TEXT_HOME', $this->context->language->id),
                            '@id' => $this->getPageLink('index', $this->context->language->id),
                            'url' => $this->getPageLink('index', $this->context->language->id),
                        ],
                    ],
                ],
            ];

            $params = [
                'link' => '',
                'title' => '',
            ];
            if ('product' == $controller && $id_product) {
                $product = new Product($id_product, false, $this->context->language->id);
                if ( !$product->id) {
                    header('X-Seo-Redirected: 1');
                    Tools::redirect('index.php');
                }
                $params['link'] = $product->getLink();
                $params['title'] = $product->name;
                if ('category' == Configuration::get('ETS_SEO_BREADCRUMB_PRODUCT')) {
                    $cate = new Category($product->id_category_default, $this->context->language->id);
                    if (Validate::isLoadedObject($cate)) {
                        $params['category_title'] = $cate->name;
                        $params['category_link'] = $cate->getLink($this->context->link, $this->context->language->id);
                    }
                }
            } elseif ('category' == $controller && $id_category) {
                $category = new Category($id_category, $this->context->language->id);
                if ( !$category->id) {
                    header('X-Seo-Redirected: 1');
                    Tools::redirect('index.php');
                }
                $params['link'] = $category->getLink($this->context->link, $this->context->language->id);
                $params['title'] = $category->name;
            } elseif ('cms' == $controller && $id_cms) {
                $cms = new CMS($id_cms, $this->context->language->id);
                if ( !$cms->id) {
                    header('X-Seo-Redirected: 1');
                    Tools::redirect('index.php');
                }
                $params['link'] = $this->context->link->getCMSLink($cms, null, null, $this->context->language->id);
                $params['title'] = $cms->meta_title;

                if ('category' == Configuration::get('ETS_SEO_BREADCRUMB_CMS')) {
                    $cmsCategory = new CMSCategory($cms->id_cms_category, $this->context->language->id);
                    if ( !$cmsCategory->id) {
                        header('X-Seo-Redirected: 1');
                        Tools::redirect('index.php');
                    }
                    $params['category_link'] = $this->context->link->getCMSCategoryLink($cmsCategory, null, $this->context->language->id);
                    $params['category_title'] = $cmsCategory->name;
                }
            } elseif ('cms_category' == $controller && $id_cms_category) {
                $cmsCategory = new CMSCategory($id_cms_category, $this->context->language->id);
                if ( !$cmsCategory->id) {
                    header('X-Seo-Redirected: 1');
                    Tools::redirect('index.php');
                }
                $params['link'] = $this->context->link->getCMSCategoryLink($cmsCategory, null, $this->context->language->id);
                $params['title'] = $cmsCategory->name;
            } elseif ('manufacturer' == $controller && $id_manufacturer) {
                $manufacturer = new Manufacturer($id_manufacturer, $this->context->language->id);
                if ( !$manufacturer->id) {
                    header('X-Seo-Redirected: 1');
                    Tools::redirect('index.php');
                }
                $params['link'] = $this->context->link->getManufacturerLink($manufacturer, null, $this->context->language->id);
                $params['title'] = $manufacturer->name;
            } elseif ('supplier' == $controller && $id_supplier) {
                $supplier = new Supplier($id_supplier, $this->context->language->id);
                if (!$supplier->id) {
                    header('X-Seo-Redirected: 1');
                    Tools::redirect('index.php');
                }
                $params['link'] = $this->context->link->getSupplierLink($supplier, null, $this->context->language->id);
                $params['title'] = $supplier->name;
            } elseif ($this->context->controller) {
                if ('index' !== $this->context->controller->php_self) {
                    $meta = Meta::getMetaByPage($this->context->controller->php_self, $this->context->language->id);
                    if ($meta) {
                        $params['link'] = $this->getPageLink($meta['page'], $this->context->language->id);
                        $params['title'] = $meta['title'];
                        if ('search' == $this->context->controller->php_self) {
                            $params['title'] = Configuration::get('ETS_SEO_BREADCRUMB_PREFIX_SEARCH', $this->context->language->id);
                            $search = ($search = Tools::getValue('s')) && Validate::isCleanHtml($search) ? $search : '';
                            if ($search) {
                                $params['title'] .= ' "' . $search . '"';
                                $params['link'] .= '?s=' . $search;
                            }
                        } elseif ('pagenotfound' == $this->context->controller->php_self) {
                            $params['title'] = Configuration::get('ETS_SEO_BREADCRUMB_404_PAGE', $this->context->language->id);
                        }
                    }
                }
            }

            $postion = 1;
            if (isset($params['category_title'], $params['category_link'])) {
                ++$postion;
                $breadcrumb['itemListElement'][] = [
                    '@type' => 'ListItem',
                    'position' => $postion,
                    'item' => [
                        '@type' => 'WebPage',
                        'name' => $params['category_title'],
                        '@id' => $params['category_link'],
                        'url' => $params['category_link'],
                    ],
                ];
            }
            if ($params['title'] && $params['link']) {
                ++$postion;
                $breadcrumb['itemListElement'][] = [
                    '@type' => 'ListItem',
                    'position' => $postion,
                    'item' => [
                        '@type' => 'WebPage',
                        'name' => $params['title'],
                        '@id' => $params['link'],
                        'url' => $params['link'],
                    ],
                ];
            }

            $data['@graph'][] = $breadcrumb;
        }
        if ($controller) {
            $allowControllers = ['product', 'cms', 'meta', 'category', 'cms_category', 'manufacturer', 'supplier'];
            if (in_array($controller, $allowControllers) || ($idMeta = (int) EtsSeoMeta::getIdMetaByController($controller))) {
                $id = null;
                $type = 'Product';
                $name = '';
                $brand = '';
                $desc = '';
                $image = '';
                $sku = '';
                $post_title = null;
                $category_name = null;
                $price = null;
                $shortDesc = null;
                $desc2 = null;
                switch ($controller) {
                    case 'product':
                        $type = 'Product';
                        $id = $id_product;
                        $p = new Product($id_product, false, $this->context->language->id);
                        $name = $p->name;
                        if ($p->id_manufacturer) {
                            $manufacturer = new Manufacturer($p->id_manufacturer, $this->context->language->id);
                            if ($manufacturer->id) {
                                $brand = $manufacturer->name;
                            }
                        }
                        $desc = $p->meta_description ? strip_tags($p->meta_description) : strip_tags($p->description_short);
                        $cover = Product::getCover($p->id);
                        $image = $this->context->link->getImageLink($p->link_rewrite, isset($cover['id_image']) ? $cover['id_image'] : '', ImageType::getFormattedName('home'));
                        $sku = $p->reference;
                        $post_title = $p->name;
                        $shortDesc = $p->description_short;
                        $id_customer = ($this->context->customer->id) ? (int) ($this->context->customer->id) : 0;
                        $id_group = null;
                        if ($id_customer) {
                            $id_group = Customer::getDefaultGroupId((int) $id_customer);
                        }
                        if (!$id_group) {
                            $id_group = (int) Group::getCurrent()->id;
                        }
                        $group = new Group($id_group);
                        if ($group->price_display_method) {
                            $tax = false;
                        } else {
                            $tax = true;
                        }
                        $price = Tools::displayPriceSmarty(['price'=> $p->getPrice($tax, null),'currency'=> $this->context->currency->id], $this->context->smarty);
                        if ($p->id_category_default) {
                            $cateProduct = new Category($p->id_category_default, $this->context->language->id);
                            if ($cateProduct->id && $cateProduct->name) {
                                $category_name = $cateProduct->name;
                            }
                        }
                        break;
                    case 'cms':
                        $type = 'Product';
                        $id = $id_cms;
                        $p = new CMS($id_cms, $this->context->language->id);
                        $name = $p->meta_title;
                        $desc = $p->meta_description ? strip_tags($p->meta_description) : '';
                        $post_title = $p->meta_title;
                        $shortDesc = strip_tags($p->meta_description);
                        if ($p->id_cms_category) {
                            $cateCms = new CMSCategory($p->id_cms_category, $this->context->language->id);
                            if ($cateCms->id && $cateCms->name) {
                                $category_name = $cateCms->name;
                            }
                        }
                        break;
                    case 'meta':
                        $type = 'Product';
                        $id = $id_meta;
                        $p = new Meta($id_meta, $this->context->language->id);
                        $name = $p->title;
                        $desc = $p->description ? strip_tags($p->description) : '';
                        $post_title = $p->title;
                        $shortDesc = $p->description ? strip_tags($p->description) : '';
                        break;
                    case 'category':
                        $type = 'Product';
                        $id = $id_category;
                        $p = new Category($id_category, $this->context->language->id);
                        $name = $p->name;
                        $desc = $p->meta_description ? strip_tags($p->meta_description) : strip_tags($p->description);
                        $image = $this->context->link->getCatImageLink($p->link_rewrite, $p->id/* , 'category_default' */);
                        $post_title = $p->name;
                        $shortDesc = $p->description ? strip_tags($p->description) : '';
                        break;
                    case 'cms_category':
                        $type = 'Product';
                        $id = $id_cms_category;
                        $p = new CMSCategory($id_cms_category, $this->context->language->id);
                        $name = $p->name;
                        $desc = $p->meta_description ? strip_tags($p->meta_description) : strip_tags($p->description);
                        $post_title = $p->name;
                        $shortDesc = '';
                        break;
                    case 'manufacturer':
                        $type = 'Product';
                        $id = $id_manufacturer;
                        $p = new Manufacturer($id_manufacturer, $this->context->language->id);
                        $name = $p->name;
                        $desc = $p->meta_description ? strip_tags($p->meta_description) : strip_tags($p->description);
                        $post_title = $p->name;
                        $shortDesc = strip_tags($p->short_description);
                        $desc2 = strip_tags($p->description);
                        if (file_exists(_PS_ROOT_DIR_ . '/img/m/' . $id_manufacturer . '.jpg')) {
                            $image = $this->context->shop->getBaseURL(true, true) . 'img/m/' . $id_manufacturer . '.jpg';
                        }
                        break;
                    case 'supplier':
                        $type = 'Product';
                        $id = $id_supplier;
                        $p = new Supplier($id_supplier, $this->context->language->id);
                        $name = $p->name;
                        $desc = $p->meta_description ? strip_tags($p->meta_description) : '';
                        $post_title = $p->name;
                        $shortDesc = strip_tags($p->description);
                        if (file_exists(_PS_ROOT_DIR_ . '/img/s/' . $id_supplier . '.jpg')) {
                            $image = $this->context->shop->getBaseURL(true, true) . 'img/s/' . $id_supplier . '.jpg';
                        }
                        break;
                    default:
                        if (!empty($idMeta)) {
                            $id = $idMeta;
                            $type = 'Product';
                            $p = new Meta($id, $this->context->language->id);
                            $name = $p->title;
                            $desc = $p->description ? strip_tags($p->description) : '';
                            $post_title = $p->title;
                            $shortDesc = strip_tags($p->description);
                        }
                        break;
                }

                $name = $this->formatSeoMeta($name, ['post_title' => $post_title, 'is_title' => true, 'category' => $category_name, 'price' => $price, 'description' => $shortDesc], $controller);
                $desc = $this->formatSeoMeta($desc, ['post_title' => $post_title, 'category' => $category_name, 'price' => $price, 'description' => $shortDesc, 'description2' => $desc2], $controller);

                $ratingSeo = EtsSeoRating::getRatingConfig(in_array($controller, $allowControllers) ? $controller : 'meta', $id);

                if ($ratingSeo) {
                    $ratingGraph = [
                        '@type' => $type,
                        'name' => $name,
                        'aggregateRating' => [
                            '@type' => 'AggregateRating',
                            'ratingValue' => $ratingSeo['avg_rating'],
                            'ratingCount' => $ratingSeo['rating_count'],
                            'bestRating' => $ratingSeo['best_rating'] ? $ratingSeo['best_rating'] : 5,
                            'worstRating' => $ratingSeo['worst_rating'] ? $ratingSeo['worst_rating'] : 1,
                        ],
                    ];
                    if ($brand) {
                        $ratingGraph['brand'] = $brand;
                    }
                    if ($desc) {
                        $ratingGraph['description'] = $desc;
                    }
                    if ($image) {
                        $ratingGraph['image'] = $image;
                    }
                    if ($sku) {
                        $ratingGraph['sku'] = $sku;
                    }
                    $data['@graph'][] = $ratingGraph;
                }
            }
        }

        return $data;
    }

    public function hookDisplayOverrideTemplate($params)
    {
        if (isset($params['template_file']) && 'catalog/product' == $params['template_file']) {
            if (!Module::isEnabled('ets_product_slideshow')) {
                @$this->getSeoMetaData(true, $this->getSeoMetaDataArray('product', Tools::getValue('id_product')));

                return $this->getTemplatePath('catalog/product.tpl');
            }
        }
    }

    /**
     * @param string $type
     * @param int|null $id
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function getSeoMetaDataArray($type = 'product', $id = null)
    {
        if (('manufacturer' === $type || 'supplier' === $type) && !$id) {
            return [];
        }
        if ('meta' !== $type && !$id && !Tools::getValue('id_' . $type)) {
            return [];
        }
        if ('meta' !== $type && !in_array($type, Ets_Seo_Define::getInstance()->getMetaOverriddenControllers(), true)) {
            return [];
        }
        if ($this->currentMetaData) {
            return $this->currentMetaData;
        }

        $objMap = [
            'product' => \Product::class,
            'category' => \Category::class,
            'cms' => \CMS::class,
            'cms_category' => \CMSCategory::class,
            'manufacturer' => \Manufacturer::class,
            'supplier' => \Supplier::class,
        ];
        $tplVarMap = [
            'product' => 'product',
            'category' => 'category',
            'cms' => 'cms',
            'cms_category' => 'cms_category',
            'manufacturer' => null,
            'supplier' => null,
        ];
        $cfgKeyMap = [
            'product' => 'PROD', 'category' => 'CATEGORY', 'cms' => 'CMS', 'manufacturer' => 'MANUFACTURER', 'cms_category' => 'CMS_CATE', 'supplier' => 'SUPPLIER',
        ];
        $cfgNameMap = [
            'force' => 'ETS_SEO_%s_FORCE_USE_META_TEMPLATE',
            'title' => 'ETS_SEO_%s_META_TITLE',
            'desc' => 'ETS_SEO_%s_META_DESC',
            'img_alt' => 'ETS_SEO_%s_META_IMG_ALT',
        ];
        $_getCfgKey = static function ($type, $name) use ($cfgKeyMap, $cfgNameMap) {
            return sprintf($cfgNameMap[$name], $cfgKeyMap[$type]);
        };
        $paramKeyMap = [
            'product' => [
                'product-name' => 'post_title',
                'price' => 'price',
                'discount-price' => 'discount_price',
                'brand' => 'brand',
                'category' => 'category',
                'summary' => 'description_short',
                'ean13' => 'ean13',
                'description' => 'description',
            ],
            'category' => [
                'category-name' => 'post_title',
                'description' => 'description',
            ],
            'cms' => [
                'cms-title' => 'post_title',
                'cms-category' => 'category',
            ],
            'cms_category' => [
                'cms-category-title' => 'post_title',
                'description' => 'description',
            ],
            'meta' => [
                'title' => 'post_title',
            ],
            'manufacturer' => [
                'brand-name' => 'post_title',
                'short-description' => 'description',
                'description' => 'description2',
            ],
            'supplier' => [
                'supplier-name' => 'post_title',
                'description' => 'description',
            ],
        ];
        $curLangId = $this->context->language->id;
        if ('product' === $type) {
            $object = $id ? new \Product($id, true, $curLangId) : $this->context->smarty->getTemplateVars($tplVarMap[$type]);
            if (!$object) {
                $object = new \Product(Tools::getValue('id_product'), true, $curLangId);
            }
        } elseif ('cms' === $type) {
            if (!$id) {
                $id = $this->context->smarty->getTemplateVars($tplVarMap[$type])['id'];
            }
            $object = new $objMap[$type]($id, $curLangId);
        } else {
            $object = $id ? new $objMap[$type]($id, $curLangId) : $this->context->smarty->getTemplateVars($tplVarMap[$type]);
        }
        if (!$object && Tools::getValue('id_' . $type)) {
            $id = (int) Tools::getValue('id_' . $type);
            $object = new $objMap[$type]($id, $curLangId);
        }
        $meta = [];
        $isForce = true;
        if ('meta' !== $type) {
            $isForce = Configuration::get($_getCfgKey($type, 'force'));
            $meta = [
                'title' => Configuration::get($_getCfgKey($type, 'title'), $curLangId),
                'description' => Configuration::get($_getCfgKey($type, 'desc'), $curLangId),
                'isForce' => (bool) $isForce,
                'img_alt' => Configuration::get($_getCfgKey($type, 'img_alt'), $curLangId),
            ];
        }
        switch ($type) {
            case 'product':
            default:
                if (is_array($object) || $object instanceof \PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray) {
                    $array = !is_array($object) ? $object->jsonSerialize() : $object;
                    $array['id_product'] = $array['id'];
                    $meta['canonical'] = $this->context->link->getProductLink($array['id']);
                    if (isset($array['cover']['large']['url'])) {
                        $meta['image'] = $array['cover']['large']['url'];
                    }
                } else {
                    $meta['canonical'] = $this->context->link->getProductLink($object);
                    if ($images = (method_exists(Product::class, 'coreGetImages') ? $object->coreGetImages($curLangId) : $object->getImages($curLangId))) {
                        $img = null;
                        foreach ($images as $imgItem) {
                            if (!$img) {
                                $img = $imgItem;
                            }
                            if (isset($imgItem['cover']) && $imgItem['cover']) {
                                $img = $imgItem;
                                break;
                            }
                        }
                        $meta['image'] = $this->context->link->getImageLink($object->link_rewrite, $img['id_image'], ImageType::getFormattedName('large'));
                    }
                }
                /* @var \Product|\PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray $object */
                if (!$meta['title'] || (!$isForce && $object->meta_title)) {
                    $meta['title'] = $object->meta_title ?: $object->name;
                }
                if (!$meta['description'] || (!$isForce && $object->meta_description)) {
                    $meta['description'] = strip_tags($object->meta_description ?: $object->description_short);
                }
                $params = ['shop_name' => $this->context->shop->name, 'separator' => html_entity_decode((string) Configuration::get('ETS_SEO_TITLE_SEPARATOR'))];
                foreach ($meta as $index => $value) {
                    if ('img_alt' === $index && !$isForce) {
                        $imgAlts = [];
                        $orgValue = $value;
                        if (!method_exists(Product::class, 'coreGetImages')) {
                            continue;
                        }
                        if (!isset($images)) {
                            $images = isset($array['id']) ? (new Product($array['id'], true, $curLangId))->getImages($curLangId) : $object->getImages($curLangId);
                        }
                        foreach ($images as $image) {
                            if (isset($image['cover']) && $image['cover']) {
                                $imgAlts['cover'] = $image['legend'];
                            }
                            $imgAlts[$image['id_image']] = $image['legend'];
                            $value .= $image['legend'];
                        }
                    }
                    if (preg_match_all('/\%([a-z0-9-]+)\%/i', (string) $value, $m)) {
                        $prKeys = $paramKeyMap['product'];
                        foreach ($m[1] as $item) {
                            $item = strtolower($item);
                            if (array_key_exists($item, $prKeys) && !isset($params[$prKeys[$item]])) {
                                switch ($item) {
                                    case 'product-name': $params[$prKeys[$item]] = $object->name;
                                        break;
                                    case 'discount-price': $params[$prKeys[$item]] = Tools::displayPriceSmarty(['price'=> $object->price, 'currency'=> $this->context->currency->id], $this->context->smarty);
                                        break;
                                    case 'price':
                                        $group = new Group($this->context->customer->id ? $this->context->customer->id_default_group : Group::getCurrent()->id);
                                        if ($object instanceof \Product) {
                                            $price = $object->getPriceWithoutReduct((bool) $group->price_display_method);
                                        } else {
                                            $price = (bool) $group->price_display_method ? $object->price_without_reduction_without_tax : $object->price_without_reduction;
                                        }
                                        $params[$prKeys[$item]] = Tools::displayPriceSmarty(['price'=> $price, 'currency'=> $this->context->currency->id], $this->context->smarty);
                                        break;
                                    case 'category': $params[$prKeys[$item]] = $object instanceof \Product ? (new Category($object->id_category_default, $curLangId))->name : $object->category_name;
                                        break;
                                    case 'brand':
                                        $params[$prKeys[$item]] = null;
                                        if(($object->id_manufacturer))
                                        {
                                            $brand = new Manufacturer($object->id_manufacturer, $this->context->language->id);
                                            if($brand->id)
                                                $params[$prKeys[$item]] = $brand->name;
                                        }
                                        break;
                                    case 'summary':
                                    case 'description':
                                        $params[$prKeys[$item]] = strip_tags($object->{$prKeys[$item]});
                                        break;
                                    case 'ean13': $params[$prKeys[$item]] = $object->ean13;
                                        break;
                                }
                            }
                        }
                    }
                    if ('img_alt' === $index && isset($imgAlts)) {
                        foreach ($imgAlts as $idImage => $alt) {
                            if (!$alt && isset($orgValue)) {
                                $imgAlts[$idImage] = $this->formatSeoMeta($orgValue, $params, $type);
                            } else {
                                $imgAlts[$idImage] = $this->formatSeoMeta($alt, $params, $type);
                            }
                        }
                        $meta[$index] = $imgAlts;
                    } else {
                        $meta[$index] = $this->formatSeoMeta($value, $params, $type);
                    }
                }
                $meta['dataSeo'] = EtsSeoProduct::getSeoProduct($object->id, $this->context, $curLangId);
                $meta['params'] = $params;
                break;

            case 'category':
            case 'cms_category':
            case 'supplier':
                if ('supplier' === $type) {
                    $meta['canonical'] = $this->context->link->getSupplierLink($object);
                    $meta['dataSeo'] = EtsSeoSupplier::getSeoSupplier($object->id, $this->context, $curLangId);
                    if (defined('_PS_SUPP_IMG_DIR_') && @file_exists(sprintf('%s%d.jpg', _PS_SUPP_IMG_DIR_, $object->id))) {
                        $meta['image'] = $this->context->link->getMediaLink(sprintf('/img/su/%d.jpg', $object->id));
                    }
                }
                if ($object instanceof \ObjectModel) {
                    $object = get_object_vars($object);
                }
                if (!$meta['title'] || (!$isForce && @$object['meta_title'])) {
                    $metaTitle = isset($object['meta_title']) ? $object['meta_title'] : null;
                    $objectName = isset($object['name']) ? $object['name'] : '';
                    $titleSource = $metaTitle ?: $objectName;
                    $meta['title'] = $this->sanitizeStringValue($titleSource);
                }
                if (!$meta['description'] || (!$isForce && @$object['meta_description'])) {
                    $metaDescription = isset($object['meta_description']) ? $object['meta_description'] : null;
                    $objectDescription = isset($object['description']) ? $object['description'] : null;
                    $descriptionSource = $metaDescription ?: $objectDescription ?: '';
                    $meta['description'] = strip_tags($this->sanitizeStringValue($descriptionSource));
                }
                $params = [];
                foreach ($meta as $index => $value) {
                    if ('dataSeo' === $index) {
                        continue;
                    }
                    $valueForMatch = $this->sanitizeStringValue($value);
                    if ('' !== $valueForMatch && preg_match_all('/\%([a-z0-9-]+)\%/i', $valueForMatch, $m)) {
                        $prKeys = $paramKeyMap[$type];
                        foreach ($m[1] as $item) {
                            $item = strtolower($item);
                            if (array_key_exists($item, $prKeys) && !isset($params[$prKeys[$item]])) {
                                switch ($item) {
                                    case 'cms-category-title':
                                    case 'category-name':
                                    case 'supplier-name': $params[$prKeys[$item]] = $this->sanitizeStringValue($object['name'] ?? '');
                                        break;
                                    case 'description': $params[$prKeys[$item]] = $this->sanitizeStringValue($object['description'] ?? '');
                                        break;
                                }
                            }
                        }
                    }
                    $meta[$index] = $this->formatSeoMeta($value, $params, $type);
                }
                if ('category' === $type && @$object['id']) {
                    $object['id_category'] = $object['id'];
                    $meta['canonical'] = $this->context->link->getCategoryLink($object['id']);
                    $meta['dataSeo'] = EtsSeoCategory::getSeoCategory($object['id'], $this->context, $curLangId);
                    if (isset($object['image']['large']['url'])) {
                        $meta['image'] = $object['image']['large']['url'];
                    }
                }
                if ('cms_category' === $type) {
                    $meta['canonical'] = $this->context->link->getCMSCategoryLink($object['id']);
                    $meta['dataSeo'] = EtsSeoCmsCategory::getSeoCmsCategory($object['id'], $this->context, $curLangId);
                }
                $meta['params'] = $params;
                break;

            case 'cms':
                if ($object instanceof \ObjectModel) {
                    $object = get_object_vars($object);
                }
                if (!$meta['title'] || (!$isForce && @$object['head_seo_title'])) {
                    $meta['title'] = $object['head_seo_title'] ?: $object['meta_title'];
                }
                if (!$meta['description'] || (!$isForce && @$object['meta_description'])) {
                    $meta['description'] = strip_tags($object['meta_description']);
                }
                $params = [];
                foreach ($meta as $index => $value) {
                    if (preg_match_all('/\%([a-z0-9-]+)\%/i', (string) $value, $m)) {
                        $prKeys = $paramKeyMap[$type];
                        foreach ($m[1] as $item) {
                            $item = strtolower($item);
                            if (array_key_exists($item, $prKeys) && !isset($params[$prKeys[$item]])) {
                                switch ($item) {
                                    case 'cms-title': $params[$prKeys[$item]] = $object['meta_title'];
                                        break;
                                    case 'cms-category': $params[$prKeys[$item]] = (new \CMSCategory($object['id_cms_category'], $curLangId))->name;
                                        break;
                                }
                            }
                        }
                    }
                    $meta[$index] = $this->formatSeoMeta($value, $params, $type);
                }
                $meta['canonical'] = $this->context->link->getCMSLink($object['id']);
                $meta['dataSeo'] = EtsSeoCms::getSeoCms($object['id'], $this->context, $curLangId);
                $meta['params'] = $params;
                break;

            case 'manufacturer':
                $meta['canonical'] = $this->context->link->getManufacturerLink($object);
                if (defined('_PS_MANU_IMG_DIR_') && @file_exists(sprintf('%s%d.jpg', _PS_MANU_IMG_DIR_, $object->id))) {
                    $meta['image'] = $this->context->link->getMediaLink(sprintf('/img/m/%d.jpg', $object->id));
                }
                if ($object instanceof \ObjectModel) {
                    $object = get_object_vars($object);
                }
                if (!$meta['title'] || (!$isForce && @$object['meta_title'])) {
                    $meta['title'] = $object['meta_title'] ?: $object['name'];
                }
                if (!$meta['description'] || (!$isForce && @$object['meta_description'])) {
                    $meta['description'] = strip_tags($object['meta_description'] ?: $object['short_description']);
                }
                $params = [];
                foreach ($meta as $index => $value) {
                    if (preg_match_all('/\%([a-z0-9-]+)\%/i', (string) $value, $m)) {
                        $prKeys = $paramKeyMap[$type];
                        foreach ($m[1] as $item) {
                            $item = strtolower($item);
                            if (array_key_exists($item, $prKeys) && !isset($params[$prKeys[$item]])) {
                                switch ($item) {
                                    case 'brand-name': $params[$prKeys[$item]] = $object['name'];
                                        break;
                                    case 'short-description': $params[$prKeys[$item]] = $object['short_description'];
                                        break;
                                    case 'description': $params[$prKeys[$item]] = $object['description'];
                                        break;
                                }
                            }
                        }
                    }
                    $meta[$index] = $this->formatSeoMeta($value, $params, $type);
                }
                $meta['dataSeo'] = EtsSeoManufacturer::getSeoManufacturer($object['id'], $this->context, $curLangId);
                $meta['params'] = $params;
                break;
            case 'meta':
                $pageName = $this->context->controller->php_self;
                $smartyPageVar = $this->context->smarty->getTemplateVars('page');
                if (!$pageName && isset($smartyPageVar['page_name']) && $smartyPageVar['page_name']) {
                    $pageName = $smartyPageVar['page_name'];
                }
                $metaObj = Meta::getMetaByPage($pageName, $curLangId);
                if ($metaObj) {
                    $meta['title'] = $metaObj['title'];
                    $meta['description'] = $metaObj['description'];
                    $meta['dataSeo'] = EtsSeoMeta::getSeoMeta((int) $metaObj['id_meta'], $this->context, $curLangId);
                    $meta['canonical'] = $this->getPageLink($metaObj['page'], $curLangId);
                }
                break;
        }

        return $this->currentMetaData = $meta;
    }

    /**
     * @return array
     */
    public function getCurrentMetaData()
    {
        return $this->currentMetaData;
    }

    public function getSeoMetaData($isProductPage = false, $seoMetaData = null)
    {
        $page = $isProductPage ? [] : $this->context->controller->getTemplateVarPage();
        $controller = ($controller = Tools::getValue('controller')) && Validate::isControllerName($controller) ? $controller : '';
        if (!$seoMetaData && $controller) {
            if (!in_array($controller, Ets_Seo_Define::getInstance()->getMetaOverriddenControllers(), true)) {
                $seoMetaData = $this->getSeoMetaDataArray('meta');
            } else {
                $seoMetaData = $this->getSeoMetaDataArray($controller, Tools::getValue('id_' . $controller));
            }
        }
        $seo_social = [];
        if ($controller) {
            $dataSeo = isset($seoMetaData['dataSeo']) ? $seoMetaData['dataSeo'] : [];
            $page['meta']['title'] = isset($seoMetaData['title']) && $seoMetaData['title'] ? $seoMetaData['title'] : @$page['meta']['title'];
            $page['meta']['description'] = isset($seoMetaData['description']) && $seoMetaData['description'] ? $seoMetaData['description'] : @$page['meta']['description'];
            $page['meta']['title'] = strip_tags($page['meta']['title']);
            $page['meta']['description'] = strip_tags($page['meta']['description']);
            if ((int) Configuration::get('PS_PRODUCT_ATTRIBUTES_IN_TITLE') && ($id_product = Tools::getValue('id_product'))) {
                $idProductAttribute = Product::getDefaultAttribute($id_product);
                $page['meta']['title'] .= ' ' . EtsSeoProduct::getProductAttributeName($idProductAttribute);
            }

            $meta_robot_default = isset($page['meta']['robots']) && $page['meta']['robots'] ? explode(',', $page['meta']['robots']) : [];
            $allow_search = $dataSeo ? (int) $dataSeo['allow_search'] : 1;
            $allow_flw_link = $dataSeo ? (int) $dataSeo['allow_flw_link'] : 1;
            $canonical_url = isset($dataSeo['canonical_url']) && $dataSeo['canonical_url'] ? $dataSeo['canonical_url'] : $seoMetaData['canonical'];

            $meta_robot = $dataSeo ? $dataSeo['meta_robots_adv'] : '';
            $meta_robot = explode(',', $meta_robot);

            if (in_array('', $meta_robot)) {
                if ('product' == $controller) {
                    $meta_robot_default[] = 'index';
                }
            } elseif (in_array('none', $meta_robot)) {
                $meta_robot_default = [];
            } else {
                $meta_robot_default = [];
                if (in_array('noarchive', $meta_robot)) {
                    $meta_robot_default[] = 'noarchive';
                }
                if (in_array('nosnippet', $meta_robot)) {
                    $meta_robot_default[] = 'nosnippet';
                }
                if (in_array('noimageindex', $meta_robot)) {
                    $meta_robot_default[] = 'noimageindex';
                }
            }
            $index = null;
            $config_allow_search = (int) Configuration::get('ETS_SEO_PROD_SHOW_IN_SEARCH_RESULT');
            if (!$allow_search || (2 == $allow_search && !$config_allow_search)) {
                $index = 'noindex';
                foreach ($meta_robot_default as $k => $rb) {
                    if ('index' == $rb) {
                        unset($meta_robot_default[$k]);
                    }
                }
            } elseif ((2 == $allow_search && $config_allow_search) || 1 == $allow_search) {
                $index = 'index';
            }
            if ($index && false === array_search('index', $meta_robot_default)) {
                $noindexPost = array_search('noindex', $meta_robot_default);
                if (false !== $noindexPost) {
                    unset($meta_robot_default[$noindexPost]);
                }
                $meta_robot_default[] = $index;
            }
            if (!$allow_flw_link) {
                $meta_robot_default[] = 'nofollow';
                foreach ($meta_robot_default as $k => $rb) {
                    if ('follow' == $rb) {
                        unset($meta_robot_default[$k]);
                    }
                }
            }
            $page['meta']['robots'] = implode(',', $meta_robot_default);
            if (trim($canonical_url)) {
                $page['canonical'] = $canonical_url;
                $seo_social['canonical'] = $canonical_url;
            }
            if (isset($dataSeo['social_title']) && $dataSeo['social_title']) {
                $dataSeo['social_title'] = $this->formatSeoMeta($dataSeo['social_title'], $seoMetaData['params'], $controller);
            }
            if (isset($dataSeo['social_desc']) && $dataSeo['social_desc']) {
                $dataSeo['social_desc'] = $this->formatSeoMeta($dataSeo['social_desc'], $seoMetaData['params'], $controller);
            }
            $seo_social['title'] = $dataSeo && $dataSeo['social_title'] ? $dataSeo['social_title'] : $page['meta']['title'];
            $seo_social['desc'] = $dataSeo && $dataSeo['social_desc'] ? $dataSeo['social_desc'] : $page['meta']['description'];
            $seo_social['url'] = $this->context->shop->getBaseURL(true, false) . $_SERVER['REQUEST_URI'];
            $seo_social['facebook_og'] = (int) Configuration::get('ETS_SEO_FACEBOOK_ENABLE_OG');
            $seo_social['twitter_card'] = (int) Configuration::get('ETS_SEO_TWITTER_ENABLE_CARD_META');
            $seo_social['twitter_card_type'] = Configuration::get('ETS_SEO_TWITTER_DEFAULT_CARD_TYPE');
            $seo_social['facebook_page'] = Configuration::get('ETS_SEO_URL_FACEBOOK');
            $seo_social['twitter_name'] = Configuration::get('ETS_SEO_URL_TWITTER');
            if ($seo_social['twitter_name']) {
                $seo_social['twitter_name'] = str_replace(' ', '', $seo_social['twitter_name']);
            }

            $seo_social['image'] = isset($seoMetaData['image']) ? $seoMetaData['image'] : '';
            if ($dataSeo && $dataSeo['social_img']) {
                $seo_social['image'] = $this->context->shop->getBaseURL(true, true) . 'img/social/' . $dataSeo['social_img'];
            }

            if (!$seo_social['image']) {
                if ($img_default = Configuration::get('ETS_SEO_FACEBOOK_DEFULT_IMG_URL')) {
                    if (file_exists(_PS_IMG_DIR_ . 'social/' . $img_default)) {
                        $seo_social['image'] = $this->context->shop->getBaseURL(true, true) . 'img/social/' . $img_default;
                    }
                } elseif ($img_front = Configuration::get('ETS_SEO_FACEBOOK_FP_IMG_URL')) {
                    if (file_exists(_PS_IMG_DIR_ . 'social/' . $img_front)) {
                        $seo_social['image'] = $this->context->shop->getBaseURL(true, true) . 'img/social/' . $img_front;
                    }
                } elseif ($img_auth = Configuration::get('ETS_SEO_SITE_ORIG_LOGO')) {
                    if (file_exists(_PS_IMG_DIR_ . 'social/' . $img_auth)) {
                        $seo_social['image'] = $this->context->shop->getBaseURL(true, true) . 'img/social/' . $img_auth;
                    }
                }
            }

            $seo_social['pinterest_verification'] = Configuration::get('ETS_SEO_PINTEREST_CONFIRM');
            $seo_social['baidu_verification'] = Configuration::get('ETS_SEO_BAIDU_VERIFY_CODE');
            $seo_social['bing_verification'] = Configuration::get('ETS_SEO_BING_VERIFY_CODE');
            $seo_social['google_verification'] = Configuration::get('ETS_SEO_GOOGLE_VERIFY_CODE');
            $seo_social['yandex_verification'] = Configuration::get('ETS_SEO_YANDEX_VERIFY_CODE');
            $seo_social['social_verified'] = (int) Configuration::get('ETS_SEO_VERIFIED_BY_USING_OTHER_METHODS');
            $page['meta']['title'] = $seo_social['title'];
            $page['meta']['description'] = $seo_social['desc'];
        }

        $this->context->smarty->assign([
            'page' => $page,
        ]);
        if ('product' == $controller) {
            $this->context->smarty->assign([
                'seo_social' => $seo_social,
                'ets_seo_social' => $seo_social,
                'is178' => $this->is178,
                'ets_seo_graph_knowledge' => json_encode($this->generateGraphWebData(), JSON_UNESCAPED_SLASHES),
            ]);
        } else {
            $this->smarty->assign([
                'seo_social' => $seo_social,
                'graph_knowledge' => json_encode($this->generateGraphWebData(), JSON_UNESCAPED_SLASHES),
            ]);
        }
    }

    public function getPageLink($page, $id_lang)
    {
        try {
            return $this->context->link->getPageLink($page, null, $id_lang, $this->getParamsPage());
        } catch (Exception $ex) {
            // If still fails, return a fallback or log the error
                return '#'; // or some default URL
        }
    }

    public function getParamsPage()
    {
        $params = Tools::getAllValues();
        if (isset($params['id_lang'])) {
            unset($params['id_lang']);
        }
        if (isset($params['controller'])) {
            unset($params['controller']);
        }
        if (isset($params['isolang'])) {
            unset($params['isolang']);
        }

        return $params;
    }

    public function getMetaCodeTemplate($type = null, $is_title = false)
    {
        $cacheId = $this->_getCacheId(['getMetaCodeTemplate' => ['type' => $type, 'title' => (int) $is_title]]);
        if (!$this->isCached('parts/meta_code.tpl', $cacheId)) {
            $seoDef = Ets_Seo_Define::getInstance();
            $this->smarty->assign([
                'list_meta_codes' => $seoDef->get_meta_codes($type, ['is_title' => $is_title]),
            ]);
        }

        return $this->display(__FILE__, 'parts/meta_code.tpl', $cacheId);
    }
    public function formatSeoMeta($str, $params = [], $type = null)
    {
        $seoDef = Ets_Seo_Define::getInstance();

        // If $str is an array, apply replacements recursively and return array
        if (is_array($str)) {
            foreach ($str as $k => $v) {
                $str[$k] = $this->formatSeoMeta($v, $params, $type);
            }

            return $str;
        }

        // Ensure we have a string (avoid passing null or unsupported types to str_replace)
        $str = $this->sanitizeStringValue($str);

        foreach ($seoDef->get_meta_codes($type, $params) as $item) {
            $search = $this->sanitizeStringValue(isset($item['code']) ? $item['code'] : '');
            if ('' === $search) {
                continue;
            }
            $replace = $this->sanitizeStringValue(isset($item['value']) ? $item['value'] : '');
            $str = str_replace($search, $replace, $str);
        }

        return $str;
    }

    private function sanitizeStringValue($value)
    {
        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        return '';
    }

    public function setRootSeoUrlConfig()
    {
        $seoDef = Ets_Seo_Define::getInstance();
        $urlSchemaConfigs = $seoDef->seo_url_schema_configs();
        foreach ($urlSchemaConfigs as $k => $config) {
            Configuration::updateGlobalValue($config['root_name'], Configuration::get('PS_ROUTE_' . $k));
        }

        return true;
    }

    public function restoreSeoUrlConfig()
    {
        Configuration::updateValue('ETS_SEO_ENABLE_REMOVE_ID_IN_URL', 0);
        Configuration::updateValue('ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL', 0);
        Configuration::updateValue('ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS', 0);
        Configuration::updateValue('ETS_SEO_ENABLE_REMOVE_ID_ATTR_ALIAS', 0);
        Configuration::updateValue('ETS_SEO_UPDATE_DUPLICATE_REWRITE', 0);
        Configuration::updateValue('ETS_SEO_SET_REMOVE_ID', 0);
        $seoDef = Ets_Seo_Define::getInstance();
        foreach ($seoDef->seo_url_schema_configs() as $rule => $name) {
            foreach (Shop::getShops() as $shop) {
                if ($configRule = Configuration::get($name['root_name'], null, null, $shop['id_shop'])) {
                    if ('module' !== $rule && false !== strpos($configRule, '{id}')) {
                        Configuration::updateValue('PS_ROUTE_' . $rule, $configRule, false, null, $shop['id_shop']);
                    } else {
                        Configuration::updateValue('PS_ROUTE_' . $rule, $name['default'], false, null, $shop['id_shop']);
                    }
                } else {
                    Configuration::updateValue('PS_ROUTE_' . $rule, $name['default'], false, null, $shop['id_shop']);
                }
            }
        }

        return true;
    }

    public function hookActionMetaPageSave($params)
    {
        if (isset($params['errors']) && !$params['errors']) {
            $this->updateConfigSeoNoid();
            $this->_clearCache('*');
        }
        $settings = null;
        if (self::getRequestContainer()) {
            $settings = ($settings = Tools::getValue('meta_settings_form')) && self::validateArray($settings) ? $settings : [];
        }
        $errors = [];
        if ($settings && isset($settings['url_schema'])) {
            if (count(array_unique($settings['url_schema'])) !== count($settings['url_schema'])) {
                $errors[] = $this->l('Each route in schema of URLs must be unique');
            }
        }
        if ($errors && !$params['errors']) {
            $params['errors'] = $errors;
        }
    }

    public function hookActionAdminMetaControllerUpdate_optionsBefore()
    {
        $this->updateConfigSeoNoid();
        $this->_clearCache('*');
    }

    public static function validateArray($array, $validate = 'isCleanHtml')
    {
        if (!is_array($array)) {
            if (method_exists('Validate', $validate)) {
                if (!Validate::$validate($array)) {
                    return false;
                }
            }

            return true;
        }
        if (method_exists('Validate', $validate)) {
            if ($array && is_array($array)) {
                $ok = true;
                foreach ($array as $val) {
                    if (!is_array($val)) {
                        if ($val && !Validate::$validate($val)) {
                            $ok = false;
                            break;
                        }
                    } else {
                        $ok = self::validateArray($val, $validate);
                    }
                }

                return $ok;
            }
        }

        return true;
    }

    public function updateConfigSeoNoid()
    {
        if (Tools::getIsset('ETS_SEO_ENABLE_REMOVE_ID_IN_URL')) {
            $params = null;
            $meta_settings_form = ($meta_settings_form = Tools::getValue('meta_settings_form')) && self::validateArray($meta_settings_form) ? $meta_settings_form : null;
            $meta_settings_url_schema_form = ($meta_settings_url_schema_form = Tools::getValue('meta_settings_url_schema_form')) && self::validateArray($meta_settings_url_schema_form) ? $meta_settings_url_schema_form : null;
            $ETS_SEO_ENABLE_REMOVE_ID_IN_URL = (int) Tools::getValue('ETS_SEO_ENABLE_REMOVE_ID_IN_URL');
            $ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS = (int) Tools::getValue('ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS');
            $ETS_SEO_ENABLE_REMOVE_ID_ATTR_ALIAS = (int) Tools::getValue('ETS_SEO_ENABLE_REMOVE_ID_ATTR_ALIAS');
            $ETS_SEO_ENABLE_REDRECT_NOTFOUND = (int) Tools::getValue('ETS_SEO_ENABLE_REDRECT_NOTFOUND');
            $ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL = (int) Tools::getValue('ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL');
            $ETS_SEO_REDIRECT_STATUS_CODE = (int) Tools::getValue('ETS_SEO_REDIRECT_STATUS_CODE');
            if (self::getRequestContainer()) {
                if ($meta_settings_form) {
                    $params = $meta_settings_form;
                } elseif ($meta_settings_url_schema_form) {
                    $params = [];
                    $params['url_schema'] = $meta_settings_url_schema_form;
                }
            }

            $seoDef = Ets_Seo_Define::getInstance();
            $urlSchemaConfigs = $seoDef->seo_url_schema_configs();
            /* UPDATE schema configs */
            if (!(int) Configuration::get('ETS_SEO_ENABLE_REMOVE_ID_IN_URL') && $ETS_SEO_ENABLE_REMOVE_ID_IN_URL) {
                $cacheId = $this->_getCacheId(['seo_meta_html' => 0], true);
                $this->_clearCache('*', $cacheId);
                if (!(int) Configuration::get('ETS_SEO_SET_REMOVE_ID')) {
                    Configuration::updateValue('ETS_SEO_SET_REMOVE_ID', 1);
                }

                foreach ($urlSchemaConfigs as $k => $config) {
                    if ($this->is178 && 'layered_rule' == $k) {
                        continue;
                    }

                    if ($params && isset($params['url_schema'][$k])) {
                        $dataConfig = $params['url_schema'][$k];
                    } else {
                        $dataConfig = ($routeConfig = Tools::getValue('PS_ROUTE_' . $k)) && Validate::isCleanHtml($routeConfig) ? $routeConfig : '';
                    }

                    if ($dataConfig) {
                        $prevConfig = Configuration::get('PS_ROUTE_' . $k);
                        if (!$prevConfig && isset($config['default']) && $config['default']) {
                            $prevConfig = $config['default'];
                        }
                        Configuration::updateValue($config['no_id'], $dataConfig);
                        Configuration::updateValue($config['name'], $prevConfig);
                        $oldConfig = Configuration::get($config['old_name']);
                        $rootConfig = Configuration::get($config['root_name']);
                        if (!$oldConfig || ('module' != $k && !preg_match('/\{id\}/', $oldConfig))) {
                            Configuration::updateValue($config['old_name'], $prevConfig);
                        }
                        if (!$rootConfig || ('module' != $k && !preg_match('/\{id\}/', $rootConfig))) {
                            Configuration::updateValue($config['root_name'], $prevConfig);
                        }
                    }
                }
            }
            Configuration::updateValue('ETS_SEO_ENABLE_REMOVE_ID_IN_URL', $ETS_SEO_ENABLE_REMOVE_ID_IN_URL);
            Configuration::updateValue('ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS', $ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS);
            Configuration::updateValue('ETS_SEO_ENABLE_REMOVE_ID_ATTR_ALIAS', $ETS_SEO_ENABLE_REMOVE_ID_ATTR_ALIAS);
            Configuration::updateValue('ETS_SEO_ENABLE_REDRECT_NOTFOUND', $ETS_SEO_ENABLE_REDRECT_NOTFOUND);
            Configuration::updateValue('ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL', $ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL);
            if ($ETS_SEO_REDIRECT_STATUS_CODE) {
                Configuration::updateValue('ETS_SEO_REDIRECT_STATUS_CODE', $ETS_SEO_REDIRECT_STATUS_CODE);
            }
        }
    }

    public function hookActionProductFormBuilderModifier($params)
    {
        if (isset($params['form_builder'], $params['id']) && $params['form_builder'] instanceof \Symfony\Component\Form\FormBuilder && $params['id']) {
            /** @var \Symfony\Component\Form\FormBuilder|\Symfony\Component\Form\FormBuilder[] $builder */
            $builder = $params['form_builder'];
            require_once __DIR__ . '/src/FormType/SeoType.php';
            require_once __DIR__ . '/src/FormType/AnalysisType.php';
            if (@file_exists(__DIR__ . '/../ets_marketplace/src/FormType/DescType.php')) {
                require_once __DIR__ . '/../ets_marketplace/src/FormType/DescType.php';
            }
            $appends = [];
            $seoIdx = null;
            $count = 0;
            foreach ($builder as $child) {
                ++$count;
                if ('seo' == $child->getName()) {
                    $seoIdx = $count;
                }
                if ($seoIdx && $count > $seoIdx) {
                    $appends[] = $child;
                    $builder->remove($child->getName());
                }
            }
            $builder->add('seo_analysis', \Ets\Seo\FormType\AnalysisType::class, ['product_id' => $params['id']]);
            foreach ($appends as $append) {
                $builder->add($append);
            }
        }
    }


    public function hookActionAdminEtsSeoUrlRedirectFormModifier($params)
    {
        if (isset($this->context->cookie->ets_seo_redirect_values)) {
            $redirectValues = json_decode($this->context->cookie->__get('ets_seo_redirect_values'), true);
            if (!is_array($redirectValues)) {
                $redirectValues = [];
            }

            if (!isset($params['fields_value']) || !is_array($params['fields_value'])) {
                $params['fields_value'] = [];
            }

            $params['fields_value'] = array_merge($params['fields_value'], $redirectValues);

            $this->context->cookie->__unset('ets_seo_redirect_values');
        }
    }

    public function transTwig()
    {
        return [
            'Content' => $this->l('Content'),
            'SEO settings' => $this->l('SEO settings'),
            'SEO analysis' => $this->l('SEO analysis'),
            'SEO score' => $this->l('SEO score'),
            'Readability score' => $this->l('Readability score'),
            'Remove ID in URL' => $this->l('Remove ID in URL'),
            'Remove ISO code in URL for default language' => $this->l('Remove ISO code in URL for default language'),
            'Remove attribute alias in URL' => $this->l('Remove attribute alias in URL'),
            'Redirect all old URLs to new URLs (keep your page rankings and backlinks)' => $this->l('Redirect all old URLs to new URLs (keep your page rankings and backlinks)'),
            'Redirect type' => $this->l('Redirect type'),
            '302 Moved Temporarily (recommended while setting up your store)' => $this->l('302 Moved Temporarily (recommended while setting up your store)'),
            '301 Moved Permanently (recommended once you have gone live)' => $this->l('301 Moved Permanently (recommended once you have gone live)'),
            'All SEO Scores' => $this->l('All SEO Scores'),
            'SEO: Not good' => $this->l('SEO: Not good'),
            'SEO: Acceptable' => $this->l('SEO: Acceptable'),
            'SEO: Excellent' => $this->l('SEO: Excellent'),
            'SEO: No Focus or Related key phrases' => $this->l('SEO: No Focus or Related key phrases'),
            'SEO: No Index' => $this->l('SEO: No Index'),
            'All Readability Scores' => $this->l('All Readability Scores'),
            'Readability: Not good' => $this->l('Readability: Not good'),
            'Readability: Acceptable' => $this->l('Readability: Acceptable'),
            'Readability: Excellent' => $this->l('Readability: Excellent'),
        ];
    }

    public static function isLink($inputLink)
    {
        if (0 === Tools::strpos($inputLink, 'http')) {
            $link_validation = '/(http|https)\:\/\/[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/';
            if (preg_match($link_validation, $inputLink)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \EtsSeoJsDefHelper
     */
    public function getJsDefHelper()
    {
        if (!$this->jsDefHelper instanceof \EtsSeoJsDefHelper) {
            $this->jsDefHelper = \EtsSeoJsDefHelper::getInstance();
        }

        return $this->jsDefHelper;
    }
    public function _getCacheId($params = null, $onlySuffix = false)
    {
        $suffix = '';
        if ($params) {
            $depthLimit = 8;
            $maxLengthLimit = 255;
            if (is_array($params)) {
                $params = EtsSeoArrayHelper::dot($params);
                foreach ($params as $key => $value) {
                    if (is_numeric($key)) {
                        $v = (string) $value;
                    } else {
                        $v = sprintf('%s|%s', $key, $value);
                    }
                    $suffix .= '|' . $v;
                }
            } else {
                $suffix .= '|' . $params;
            }
            if (strlen($suffix) > $maxLengthLimit || count(explode('|', $suffix)) > $depthLimit) {
                $suffix = '|' . md5($suffix);
            }
        }

        if ($onlySuffix) {
            return $this->name . $suffix;
        }
        $cacheId = $this->getCacheId($this->name);
        if (defined('_PS_ADMIN_DIR_') && Ets_Seo::getContextStatic()->employee->isLoggedBack()) {
            $cacheId .= '|' . Ets_Seo::getContextStatic()->employee->id;
        }
        $cacheId = str_replace($this->name, '', $cacheId);

        return $this->name . $suffix . $cacheId;
    }

    /**
     * @param string $template
     *
     * @return static
     */
    public function _clearQueuedCache($template)
    {
        $confKey = 'ETS_SEO_QUEUE_CLEAR_TPL_CACHE';
        $arr = Configuration::get($confKey) ?: '[]';
        $arr = json_decode($arr, true);
        $items = isset($arr[$template]) ? $arr[$template] : null;
        if ($items) {
            if (is_array($items)) {
                foreach ($items as $cacheId) {
                    $this->_clearCache($template, $cacheId);
                }
            } else {
                $this->_clearCache($template);
            }
        }
        unset($arr[$template]);
        Configuration::updateValue($confKey, json_encode($arr));

        return $this;
    }

    /**
     * @param string $template
     * @param string|null $cacheId
     *
     * @return static
     */
    public function _addToQueueClearCache($template, $cacheId = null)
    {
        $confKey = 'ETS_SEO_QUEUE_CLEAR_TPL_CACHE';
        $arr = Configuration::get($confKey) ?: '[]';
        $arr = json_decode($arr, true);
        if (isset($arr[$template]) && '*' === $arr[$template]) {
            return $this;
        }
        if (null === $cacheId) {
            $arr[$template] = '*';
        } else {
            if (isset($arr[$template])) {
                $items = ('*' === $arr[$template]) ? $arr[$template] : [];
            } else {
                $items = [];
            }
            if (is_array($items)) {
                /* @noinspection UnsupportedStringOffsetOperationsInspection */
                $items[] = $cacheId;
            }
            $arr[$template] = $items;
        }
        Configuration::updateValue($confKey, json_encode($arr));

        return $this;
    }
    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        if ($cache_id === null)
            $cache_id = $this->name;
        if ($template == '*') {
            return Tools::clearCache($this->context->smarty, false, null, null);
        } else {
            return Tools::clearCache($this->context->smarty, $this->getTemplatePath($template), $cache_id, $compile_id);
        }
    }

    /**
     * @param \ObjectModelCore $object
     */
    private function _clearCacheWhenObjectUpdated(ObjectModelCore $object)
    {
        $configs = [
            Meta::class    => [
                'k'   => 'seo_meta_html',
                'tpl' => [
                    'page/seo_setting.tpl',
                    'page/seo_analysis.tpl',
                    'page/meta_title.tpl',
                    'parts/_preview_seo_analysis.tpl',
                ],
            ],
            Product::class => [
                'k'   => 'seo_product_html',
                'wId' => true,
                'tpl' => [
                    'page/seo_analysis.tpl',
                    'parts/_preview_seo_analysis.tpl',
                    'parts/_tab_social.tpl',
                    'parts/_seo_advanced.tpl',
                    'parts/_rating.tpl',
                ],
            ],
        ];
        foreach ($configs as $className => $cacheInfo) {
            if ($object instanceof $className) {
                $template = $cacheInfo['tpl'];
                $prKey = $cacheInfo['k'];
                if (isset($cacheInfo['wId']) && $object->id) {
                    $prKey = [$cacheInfo['k'] => $object->id];
                }
                $key = $this->_getCacheId($prKey, true);
                foreach ($template as $tpl) {
                    $this->_clearCache($tpl, $key);
                }
                break;
            }
        }
    }

    /**
     * @param string $template
     *
     * @return string|null
     */
    public function getTemplatePath($template)
    {
        if (@file_exists($template)) {
            return $template;
        }

        return parent::getTemplatePath($template);
    }
    public function hookFilterProductContent($params)
    {
        if(isset($params['object']) && isset($params['object']->id) && isset($params['object']->productComments) && ($rating = EtsSeoRating::getRating('product',$params['object']->id)) && $rating['enable'])
        {
            $params['object']->productComments = [
                'averageRating' => $rating['average_rating'],
                'nbComments' => $rating['rating_count'],
            ];
        }
        return $params;
    }
    public function getListModels(){
        $gpt = new EtsSeoChatGpt();
        return $gpt->getListModels();
    }
    public function _getContext()
    {
        return $this->context;
    }
    public static function getContextStatic(){
        /** @var Ets_Seo $module */
        $module = Module::getInstanceByName('ets_seo');
        return $module->_getContext();
    }
    
    /**
     * Decode base64 encoded strings trong content_analysis để tránh ModSecurity chặn
     * 
     * @param array $contentAnalysis Mảng content_analysis từ POST
     * @return array Mảng đã được decode
     */
    public static function decodeBase64InContentAnalysis($contentAnalysis)
    {
        $enableBase64 = Configuration::get(ETS_SEO_ENABLE_CONTENT_ANALYSIS_BASE64);
        if ($enableBase64 === false) {
            $enableBase64 = 1;
        }
        if (!(int) $enableBase64) {
            return $contentAnalysis;
        }

        if (!is_array($contentAnalysis)) {
            return $contentAnalysis;
        }
        $prefix = '__BASE64__';
        
        foreach ($contentAnalysis as $langId => $rules) {
            if (!is_array($rules)) {
                continue;
            }
            foreach ($rules as $ruleKey => $ruleData) {
                if (!is_array($ruleData) || !isset($ruleData['text']) || !is_string($ruleData['text'])) {
                    continue;
                }
                
                if (strpos($ruleData['text'], $prefix) !== 0) {
                    continue;
                }
                
                $base64Str = substr($ruleData['text'], strlen($prefix));
                if (empty($base64Str)) {
                    continue;
                }
                
                $decoded = base64_decode($base64Str, true);
                if ($decoded === false || $decoded === '') {
                    continue;
                }
                
                $uriEncoded = '';
                for ($i = 0; $i < strlen($decoded); $i++) {
                    $uriEncoded .= '%' . strtoupper(str_pad(dechex(ord($decoded[$i])), 2, '0', STR_PAD_LEFT));
                }
                
                $decoded = urldecode($uriEncoded);

                if (!mb_check_encoding($decoded, 'UTF-8')) {
                    $decoded = mb_convert_encoding($decoded, 'UTF-8', 'ISO-8859-1');
                    if (!mb_check_encoding($decoded, 'UTF-8')) {
                        continue;
                    }
                }
                
                $contentAnalysis[$langId][$ruleKey]['text'] = $decoded;
            }
        }
        
        return $contentAnalysis;
    }
}
