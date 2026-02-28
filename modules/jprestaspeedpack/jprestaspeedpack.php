<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;
use JPresta\SpeedPack\JprestaWebpModule;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'classes/PageCacheURLNormalizer.php';
require_once 'classes/JprestaCacheKey.php';
require_once 'classes/JprestaCacheKeySpecifics.php';
require_once 'classes/JprestaCacheWarmerSettings.php';
require_once 'classes/JprestaApi.php';
require_once 'classes/JprestaUtils.php';
require_once 'classes/JprestaUtilsModule.php';
require_once 'classes/JprestaUtilsTaxManager.php';
require_once 'classes/JprestaSystemInfos.php';
require_once 'classes/PageCacheCache.php';
require_once 'classes/PageCacheCacheSimpleFS.php';
require_once 'classes/PageCacheCacheMultiStore.php';
require_once 'classes/PageCacheCacheStatic.php';
require_once 'classes/PageCacheCacheZipFS.php';
require_once 'classes/PageCacheCacheMemcache.php';
require_once 'classes/PageCacheCacheMemcached.php';
require_once 'classes/JprestaCustomer.php';
require_once 'classes/PageCacheDAO.php';
require_once 'vendor/http_build_url.php';
// SPEEDPACK
require_once 'classes/JprestaWebpModule.php';
require_once 'classes/JprestaSQLProfilerModule.php';
require_once 'classes/JprestaLazyLoading.php';
require_once 'classes/JprestaDbOptimizer.php';
// SPEEDPACK£

require_once 'autoload-deps.php';

class Jprestaspeedpack extends Module
{
    const PAGECACHE_DIR = 'pagecache';
    const HOOK_TYPE_MODULE = 'm';
    const HOOK_TYPE_WIDGET = 'w';
    const HOOK_TYPE_WIDGET_BLOCK = 'b';
    const PROFILING_MAX_RECORD = 1000;
    const FLUSH_MAX_SECONDS = 30;
    const HTTP_HEADER_CACHE_INFO = 'X-JPresta-Cache-Infos';
    const IGNORED_URL_PARAMS = 'fbclid,gclid,utm_id,utm_campaign,utm_content,utm_medium,utm_source,utm_term,_openstat,cm_cat,cm_ite,cm_pla,cm_ven,owa_ad,owa_ad_type,owa_campaign,owa_medium,owa_source,pk_campaign,pk_kwd,WT.mc_t,kwkuniv,srsltid,gad_source,_gl,_ga,cto_pld,cjdata,cjevent,publishername,mc_cid,mc_eid,gbraid,wbraid,mot_tcid';

    const DEVICE_COMPUTER = 1;
    const DEVICE_TABLET = 2;
    const DEVICE_MOBILE = 3;

    /** Declare it to avoid warning */
    public $author_address;

    private $pre_display_html;
    private static $skipUpdateCacheKey = true; // Always skip it to do it once at the end (dispatcherAfter)
    private static $needUpdateCacheKey = false;
    // Temporary disable browser cache; can be used by third party modules (for exemple: until RGPD choice has been made)
    private static $disableBrowserCache = false;
    // A faster way to know which modules have been used to generate the page
    private static $executed_modules = [];

    /**
     * @var bool true once the dispatcher has been instanciated
     */
    private static $initialised = false;

    /**
     * @var string Reason of the status of the cache used to send HTTP header
     */
    private static $status_reason = '';

    public $jpresta_submodules = [];

    public static $managed_controllers_default = [
        'index' => ['id' => 1, 'object_class' => null, 'module' => null],
        'category' => ['id' => 2, 'object_class' => null, 'module' => null],
        'product' => ['id' => 3, 'object_class' => null, 'module' => null],
        'cms' => ['id' => 4, 'object_class' => null, 'module' => null],
        'newproducts' => ['id' => 5, 'object_class' => null, 'module' => null],
        'bestsales' => ['id' => 6, 'object_class' => null, 'module' => null],
        'supplier' => ['id' => 7, 'object_class' => null, 'module' => null],
        'manufacturer' => ['id' => 8, 'object_class' => null, 'module' => null],
        'contact' => ['id' => 9, 'object_class' => null, 'module' => null],
        'pricesdrop' => ['id' => 10, 'object_class' => null, 'module' => null],
        'sitemap' => ['id' => 11, 'object_class' => null, 'module' => null]];
    public static $managed_controllers;
    public static $managed_object_classes;

    private static $default_dyn_hooks = [
        'displayproducttabcontent',
        'displayrightcolumn',
        'displayleftcolumn',
        'displaytop',
        'displaynav',
        'displayproducttab',
        'actionproductoutofstock',
        'displayfooterproduct',
        'displayleftcolumnproduct',
        'displayhome',
        'displayfooter',
        'displaysidebarright',
        'displayrightbar'];

    private static $default_dyn_modules = [
        'blockuserinfo',
        'blockviewed',
        'blockmyaccount',
        'favoriteproducts',
        'blockwishlist',
        'blockviewed_mod',
        'stcompare',
        'ps_shoppingcart',
        'ps_customersignin',
    ];

    /**
     * @var string[] Name of cookies to preserve
     */
    protected static $cookies_to_preserve = [
        // From Prestashop
        'id_currency' => 'id_currency',
        'id_lang' => 'id_lang',
        'no_mobile' => 'no_mobile',
        'iso_code_country' => 'iso_code_country',
        'detect_language' => 'detect_language',
        // From autolanguagecurrency module
        'autolocation' => 'autolocation',
        'autolocation_isocode' => 'autolocation_isocode',
        'id_currency_by_location' => 'id_currency_by_location',
        'id_language_by_location' => 'id_language_by_location',
        // From thcountryselector module (Thecon)
        'th_country_selected' => 'th_country_selected',
        // From stthemeeditor module
        'st_category_columns_nbr' => 'st_category_columns_nbr',
        // From gdprpro module
        'gdpr_conf' => 'gdpr_conf',
        'gdpr_windows_was_opened' => 'gdpr_windows_was_opened',
        // From cookiesplus (Idnovate)
        'psnotice' => 'psnotice',
        'psnoticeexiry' => 'psnoticeexiry',
        'cookiesplus' => 'cookiesplus',
        // From megacookies (presta.design)
        'megacookie_consents' => 'megacookie_consents',
        // From medcookiefirst (Mediacom87)
        'cookiefirst-consent' => 'cookiefirst-consent',
        // From APC poup (Idnovate)
        'apc_popup' => 'apc_popup',
        // From Age Verify module
        'age_verify' => 'age_verify',
        // VAT number validate
        'guest_taxes' => 'guest_taxes',
        // Amazon Pay - Login and Pay with Amazon by patworx
        'customer_firstname' => '',
        'customer_lastname' => '',
        // From hicookie module
        'hiThirdPartyCookies' => 'hiThirdPartyCookies',
        // From dm_cookies module
        'DmCookiesAnalytics' => 'DmCookiesAnalytics',
        'DmCookiesMarketing' => 'DmCookiesMarketing',
        'DmCookiesAccepted' => 'DmCookiesAccepted',
        // From idxcookies module
        'idxcookiesWarningCheck' => 'idxcookiesWarningCheck',
        // From iqitthemeeditor
        'product_list_view' => 'product_list_view',
        // From module app4less, see ticket #4376
        'isApp4Less' => 'isApp4Less',
        'app4less' => 'app4less',
        'tiendaApp4Less' => 'tiendaApp4Less',
        'timestampApp4Less' => 'timestampApp4Less',
    ];

    /**
     * @var string[] Prefix of cookies to preserve
     */
    private static $cookies_to_preserve_prefix = [
        // From hicookie module
        'hiThirdPartyCookies_' => 'hiThirdPartyCookies_',
    ];

    const JPRESTA_PROTO = 'http://';
    const JPRESTA_DOMAIN = 'jpresta';

    public function __construct()
    {
        $this->name = 'jprestaspeedpack';
        $this->tab = 'administration';
        $this->version = '10.1.0';
        $this->author = 'JPresta.com';
        $this->module_key = '789f6f690a206d81fa7bd262e70e6ee3';
        $this->ps_versions_compliancy = ['min' => '1.7.1.0', 'max' => '9.999.999'];
        $this->bootstrap = true;

        parent::__construct();
        $this->displayName = 'JPresta - Speed Pack';
        $this->description = $this->l('Includes the famous and so efficient Page Cache Ultimate module, lazy loading of images, image compression with WEBP and database optimisation');

        // Check tokens
        $token_enabled = (int) Configuration::get('PS_TOKEN_ENABLE') == 1 ? true : false;
        if ($token_enabled) {
            $this->warning = $this->l('You must disable tokens in order for cached pages to do ajax call.');
        }
        // Check for bvkdispatcher module
        if (Module::isInstalled('bvkseodispatcher')) {
            $this->warning = $this->l('Module "SEO Pretty URL Module" (bvkseodispatcher) is not compatible with PageCache because it does not respect Prestashop standards. You have to choose between this module and PageCache.');
        }
        // Check for overrides (after an upgrade it is disabled)
        if (!self::isOverridesEnabled()) {
            $this->warning = $this->l('Overrides are disabled in Performances tab so PageCache is disabled.');
        }

        // SPEEDPACK
        // Create submodules instances
        $this->jpresta_submodules['JprestaWebpModule'] = new JprestaWebpModule($this);
        $this->jpresta_submodules['JprestaSQLProfiler'] = new JprestaSQLProfilerModule($this);
        $this->jpresta_submodules['JprestaLazyLoading'] = new JprestaLazyLoading($this);
        $this->jpresta_submodules['JprestaDbOptimizer'] = new JprestaDbOptimizer($this);
        // SPEEDPACK£

        if (!self::isNotLogout() && JprestaUtils::getConfigurationOfCurrentShop('pagecache_logout_nocache')) {
            // Add a dummy parameter to avoid the browser cache to be used when logging out
            if (array_key_exists('HTTP_REFERER', $_SERVER)) {
                if (strpos($_SERVER['HTTP_REFERER'], '?') !== false) {
                    $_SERVER['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'] . '&nocache=' . time();
                } else {
                    $_SERVER['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'] . '?nocache=' . time();
                }
            }
        }
    }

    public static function getModulesToCheck()
    {
        static $modulesName = null;
        if ($modulesName === null) {
            $modulesName = [];
            foreach (self::getManagedControllersNames() as $controller) {
                $modulesName = array_merge($modulesName, explode(' ', Configuration::get('pagecache_' . $controller . '_a_mods')));
                $modulesName = array_merge($modulesName, explode(' ', Configuration::get('pagecache_' . $controller . '_u_mods')));
                $modulesName = array_merge($modulesName, explode(' ', Configuration::get('pagecache_' . $controller . '_d_mods')));
            }
            $modulesName = array_merge($modulesName, explode(' ', Configuration::get('pagecache_product_home_a_mods')));
            $modulesName = array_merge($modulesName, explode(' ', Configuration::get('pagecache_product_home_u_mods')));
            $modulesName = array_merge($modulesName, explode(' ', Configuration::get('pagecache_product_home_d_mods')));
            $modulesName = array_unique($modulesName);
        }

        return $modulesName;
    }

    public function install()
    {
        // Be aware that only the last message in _errors will be displayed.

        // Check PS version compliancy first
        if (method_exists($this, 'checkCompliancy') && !$this->checkCompliancy()) {
            $this->_errors[] = $this->l('The version of your module is not compliant with your PrestaShop version.');

            return false;
        }

        // Be sure the script will end correctly (not sure if it's taken into account)
        set_time_limit(300);

        // Check buggy version 1.6.0.8
        if (JprestaUtils::version_compare(_PS_VERSION_, '1.6.0.8', '=')) {
            // Check that a fix has been applied
            $moduleClass = Tools::file_get_contents(_PS_CLASS_DIR_ . 'module/Module.php');
            if (substr_count($moduleClass, '#^\s*<\?(?:php)?#') != 4) {
                $this->_errors[] = $this->l('Prestashop 1.6.0.8 has a bug (http://forge.prestashop.com/browse/PSCSX-2500) that must be fixed in order to install PageCache. Please upgrade your shop or apply a patch (replace 4 occurences of "#^\s*<\?(?:php)?\s#" by "#^\s*<\?(?:php)?#" in file ' . _PS_CLASS_DIR_ . 'module/Module.php).');

                return false;
            }
        }

        // Check for similar modules (split string to avoid the build to replace it)
        if ($this->name !== 'jprestaspeedpack' && (Module::isInstalled('jprestaspeedpack') || file_exists(_PS_MODULE_DIR_ . 'jprestaspeedpack'))) {
            $this->_errors[] = $this->l('Before installing this module you must uninstall "Speed Pack" module and delete its directory') . ': ' . _PS_MODULE_DIR_ . 'jprestaspeedpack';

            return false;
        }
        if ($this->name !== 'pagecache' && (Module::isInstalled('pagecache') || file_exists(_PS_MODULE_DIR_ . 'pagecache'))) {
            if (!file_exists(_PS_MODULE_DIR_ . 'pagecache')) {
                Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'module` WHERE `name` = \'pagecache\'');
            } else {
                $this->_errors[] = $this->l('Before installing this module you must uninstall "Page Cache Ultimate" module and delete its directory') . ': ' . _PS_MODULE_DIR_ . 'pagecache';

                return false;
            }
        }
        if ($this->name !== 'pagecachestd' && (Module::isInstalled('pagecachestd') || file_exists(_PS_MODULE_DIR_ . 'pagecachestd'))) {
            $this->_errors[] = $this->l('Before installing this module you must uninstall "Page Cache Standard" module and delete its directory') . ': ' . _PS_MODULE_DIR_ . 'pagecachestd';

            return false;
        }

        // Check for bvkdispatcher module
        if (Module::isInstalled('bvkseodispatcher')) {
            $this->_errors[] = $this->l('Module "SEO Pretty URL Module" (bvkseodispatcher) is not compatible with PageCache because it does not respect Prestashop standards. You have to choose between this module and PageCache.');

            return false;
        }

        // Check for expresscache module
        if (Module::isInstalled('expresscache') && file_exists(_PS_MODULE_DIR_ . 'expresscache')) {
            $this->_errors[] = $this->l('Module "Express Cache" (expresscache) cannot be used with Page Cache because you can have only one HTML cache module. In order to install Page Cache you must uninstall Express Cache.');

            return false;
        }

        // Check for stadvancedcache module
        if (Module::isInstalled('stadvancedcache') && file_exists(_PS_MODULE_DIR_ . 'stadvancedcache')) {
            $this->_errors[] = $this->l('Module "Advanced page cache" (stadvancedcache) cannot be used with Page Cache because you can have only one HTML cache module. In order to install Page Cache you must uninstall Advanced page cache.');

            return false;
        }

        // Install module
        $install_ok = parent::install();
        if (!$install_ok) {
            foreach (Tools::scandir($this->getLocalPath() . 'override', 'php', '', true) as $file) {
                $class = basename($file, '.php');
                if (JprestaUtils::version_compare(_PS_VERSION_, '1.6', '>=')) {
                    if (PrestaShopAutoload::getInstance()->getClassPath($class . 'Core')) {
                        $this->removeOverride($class);
                    }
                } else {
                    if (Autoload::getInstance()->getClassPath($class . 'Core')) {
                        $this->removeOverride($class);
                    }
                }
            }
            // Retry after uninstalling overrides with our own method
            $install_ok = parent::install();
        }

        if ($install_ok) {
            try {
                if (!defined('JprestaMigPCU2SP')) {
                    // Make sure old database tables are deleted
                    PageCacheDAO::dropTables();
                    // Create database tables
                    PageCacheDAO::createTables();
                    $this->_setDefaultConfiguration();
                    $this->patchSmartyConfigFront();
                    $this->patchSmartyConfigFrontWidgetBlock();
                    $this->installOverridesForModules();
                    JprestaApi::setPrestashopIsClone(false);
                }
                $this->installTabs();
                $this->registerHooks();
                $this->installStaticCode();

                JprestaUtils::dbCreateIndexIfNotExists(_DB_PREFIX_ . 'specific_price_rule', ['id_country', 'to']);
                JprestaUtils::dbCreateIndexIfNotExists(_DB_PREFIX_ . 'specific_price_rule', ['id_group', 'to']);
                JprestaUtils::dbCreateIndexIfNotExists(_DB_PREFIX_ . 'specific_price', ['id_country', 'to']);
                JprestaUtils::dbCreateIndexIfNotExists(_DB_PREFIX_ . 'specific_price', ['id_group', 'to']);

                // SPEEDPACK
                // Install submodules
                $this->jpresta_submodules['JprestaWebpModule']->install();
                $this->jpresta_submodules['JprestaSQLProfiler']->install();
                $this->jpresta_submodules['JprestaLazyLoading']->install();
                $this->jpresta_submodules['JprestaDbOptimizer']->install();
                // SPEEDPACK£
            } catch (PrestaShopException $e) {
                $install_ok = false;
                $this->_errors[] = $e->getMessage() . '. ' . $this->l('Please, contact the support of this module with this error message.');
                try {
                    // An error occured while setting up the module, uninstall it to avoid a bad installation
                    parent::uninstall();
                } catch (PrestaShopException $e2) {
                    JprestaUtils::addLog('PageCache | Cannot uninstall module ' . $this->name . ' after having this error during installation: "' . $e->getMessage() . '"" -> Got this error: ' . $e2->getMessage(), 4);
                }
            }
        }
        if (defined('JprestaMigPCU2SP')) {
            // We need to clear the cache because there are references to pagecache files in HTLM
            $this->clearCache('migration');
        }
        if ((bool) $install_ok) {
            JprestaUtils::addLog('PageCache | Module ' . $this->name . ' version ' . $this->version . ' installed', 1);
        } else {
            JprestaUtils::addLog('PageCache | Install of module ' . $this->name . ' version ' . $this->version . ' failed', 3);
        }

        return (bool) $install_ok;
    }

    private function disableCommonOverridesWithThemeConfigurator() {
        $relPathHook = 'classes/Hook.php';
        $overrideFullPathHook = _PS_MODULE_DIR_ . $this->name . '/override/' . $relPathHook;
        $relPathContext = 'classes/Context.php';
        $overrideFullPathContext = _PS_MODULE_DIR_ . $this->name . '/override/' . $relPathContext;
        if (JprestaUtils::isModuleEnabled('jprestathemeconfigurator')) {
            if (file_exists($overrideFullPathHook)) {
                rename($overrideFullPathHook, $overrideFullPathHook . '.off');
            }
            if (file_exists($overrideFullPathContext)) {
                rename($overrideFullPathContext, $overrideFullPathContext . '.off');
            }
        }
    }

    private function restoreCommonOverridesWithThemeConfigurator() {
        $relPathHook = 'classes/Hook.php';
        $overrideFullPathHook = _PS_MODULE_DIR_ . $this->name . '/override/' . $relPathHook;
        $relPathContext = 'classes/Context.php';
        $overrideFullPathContext = _PS_MODULE_DIR_ . $this->name . '/override/' . $relPathContext;
        if (JprestaUtils::isModuleEnabled('jprestathemeconfigurator')) {
            if (file_exists($overrideFullPathHook . '.off')) {
                rename($overrideFullPathHook . '.off', $overrideFullPathHook);
            }
            if (file_exists($overrideFullPathContext . '.off')) {
                rename($overrideFullPathContext . '.off', $overrideFullPathContext);
            }
        }
    }

    /**
     * Disable / enable Hook and Context override if jprestathemeconfigurator is enabled
     *
     * @return bool
     */
    public function installOverrides()
    {
        $this->disableCommonOverridesWithThemeConfigurator();
        $ret = parent::installOverrides();
        $this->restoreCommonOverridesWithThemeConfigurator();
        return $ret;
    }

    public function installOverridesForModules($replace = false, $module_name = null)
    {
        if ($module_name !== null) {
            if (Validate::isModuleName($module_name)) {
                JprestaUtils::copyFiles(_PS_MODULE_DIR_ . $this->name . '/override/modules_/' . $module_name, _PS_OVERRIDE_DIR_ . 'modules/' . $module_name, $replace);
            } else {
                JprestaUtils::addLog("installOverridesForModules: $module_name is not a valid module name", 2);
            }
        } else {
            JprestaUtils::copyFiles(_PS_MODULE_DIR_ . $this->name . '/override/modules_', _PS_OVERRIDE_DIR_ . 'modules', $replace);
        }
    }

    public function installTab($adminController, $name = false, $id_parent = -1)
    {
        $isUpdate = true;
        $tab = Tab::getInstanceFromClassName($adminController);
        if (!$tab || !$tab->id) {
            $tab = new Tab();
            $tab->class_name = $adminController;
            $isUpdate = false;
        }
        $tab->active = 1;
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            // Translation for modules are cached in a global variable but the local is ignored >:(
            if (is_array($name)) {
                if (array_key_exists($lang['iso_code'], $name)) {
                    $trans = $name[$lang['iso_code']];
                } elseif (array_key_exists('en', $name)) {
                    $trans = $name['en'];
                }
            } else {
                $trans = $name;
            }
            $tab->name[$lang['id_lang']] = !$trans ? $this->name : $trans;
        }
        $tab->id_parent = $id_parent;
        $tab->module = $this->name;
        if ($isUpdate) {
            return $tab->update();
        } else {
            return $tab->add();
        }
    }

    public function uninstallTab($adminController)
    {
        $id_tab = (int) Tab::getIdFromClassName($adminController);
        if ($id_tab) {
            $tab = new Tab($id_tab);
            if (Validate::isLoadedObject($tab)) {
                return $tab->delete();
            } else {
                $return = false;
            }
        } else {
            $return = true;
        }

        return $return;
    }

    private function uninstallAllTab()
    {
        $tabs = Tab::getCollectionFromModule($this->name);
        if (JprestaUtils::isIterable($tabs)) {
            foreach ($tabs as $tab) {
                $tab->delete();
            }

            return true;
        }
    }

    public function checkTabAccesses($adminController)
    {
        try {
            if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>=')) {
                $slug = Access::sluggifyTab(['class_name' => $adminController], 'READ');
                $granted = Access::isGranted($slug, $this->context->employee->id_profile);
                if (!$granted) {
                    $id_role = JprestaUtils::dbGetValue('SELECT `id_authorization_role` FROM `' . _DB_PREFIX_ . 'authorization_role` WHERE slug = \'' . pSql($slug) . '\'');
                    if ($id_role) {
                        $sql = '
                        INSERT IGNORE INTO `' . _DB_PREFIX_ . 'access` (`id_profile`, `id_authorization_role`)
                        VALUES (' . (int) $this->context->employee->id_profile . ',' . (int) $id_role . ')
                    ';
                        Db::getInstance()->execute($sql);
                    }
                }
            } else {
                $id_tab = Tab::getIdFromClassName($adminController);
                $profile = Profile::getProfileAccess($this->context->employee->id_profile, $id_tab);
                if (!$profile['view']) {
                    $sql = 'UPDATE `' . _DB_PREFIX_ . 'access` SET `view`=1, `add`=1, `edit`=1, `delete`=1 WHERE id_profile=' . (int) $this->context->employee->id_profile . ' AND id_tab=' . (int) $id_tab;
                    Db::getInstance()->execute($sql);
                }
            }
        } catch (Throwable $e) {
            // ignore
            JprestaUtils::addLog('PageCache | Error in checkTabAccesses(): ' . $e->getMessage(), 1);
        }
    }

    public function runUpgradeModule()
    {
        $startTime = microtime(true);
        $oldVersion = $newVersion = $this->version;
        $upgrade = parent::runUpgradeModule();
        if ($upgrade['upgraded_to']) {
            $newVersion = $upgrade['upgraded_to'];
        }
        JprestaUtils::addLog('PageCache | Upgrading module ' . $this->name . " from version $oldVersion to version $newVersion in " . number_format(microtime(true) - $startTime, 3) . ' second(s)', 1, null, null, null, true);

        return $upgrade;
    }

    /**
     * To be called first
     */
    public static function init()
    {
        if (!self::$initialised) {
            // Avoid doing it multiple times and also recursively
            self::$initialised = true;

            $controller = self::getControllerName();

            self::checkModuleController();

            if (!JprestaUtils::isAjax()
                && self::isGetRequest()
                && !defined('_PS_ADMIN_DIR_')
                && self::isCacheEnabledForController($controller)) {
                if (JprestaUtils::getConfigurationAllShop('pagecache_cachekey_usergroups_upd', false)) {
                    self::updateCacheKeyForUserGroups();
                }

                if (self::isCacheWarmer()) {
                    // Setup the context for cache warmer
                    self::setCacheWarmerContext();
                }

                // We must set the country before calling self::preDisplayStats() or getAddressForTaxes() will be called
                // and the country will not be correctly set.
                // This will also set the restrictedCountry variable of the controller
                $country = self::getCountry(Context::getContext());
                if ($country) {
                    Context::getContext()->country = $country;
                }
            }
        }
    }

    public function needsUpgrade()
    {
        if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>')) {
            $database_version = JprestaUtils::dbGetValue('SELECT version FROM `' . _DB_PREFIX_ . 'module` WHERE name=\'' . pSQL($this->name) . '\'');

            return JprestaUtils::version_compare($this->version, $database_version, '>');
        }

        return false;
    }

    public function hookDisplayAdminAfterHeader()
    {
        if ($this->needsUpgrade()) {
            try {
                $database_version = JprestaUtils::dbGetValue('SELECT version FROM `' . _DB_PREFIX_ . 'module` WHERE name=\'' . pSQL($this->name) . '\'');
                $smarty = Context::getContext()->smarty;
                $smarty->assign('jpresta_module_name', $this->displayName);
                $smarty->assign('jpresta_module_new_version', $this->version);
                $smarty->assign('jpresta_module_current_version', $database_version);
                if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>')) {
                    return $this->fetch('module:' . $this->name . '/views/templates/admin/need-upgrade.tpl');
                } else {
                    return $this->display(__FILE__, '/views/templates/admin/need-upgrade.tpl');
                }
            } catch (Throwable $e) {
                // Just ignore
            }
        }
        if (JprestaApi::getPrestashopIsClone()) {
            if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>')) {
                return $this->fetch('module:' . $this->name . '/views/templates/admin/need-confirm-clone.tpl');
            } else {
                return $this->display(__FILE__, '/views/templates/admin/need-confirm-clone.tpl');
            }
        }

        return '';
    }

    public function upgradeIfNeeded()
    {
        if ($this->needsUpgrade()) {
            $moduleManagerBuilder = PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder::getInstance();
            $moduleManager = $moduleManagerBuilder->build();

            if (method_exists($moduleManager, 'setActionParams')) {
                // Does not exist in PS8
                // Clearing the cache is really long so I disable it. If needed the user will do it manually.
                $moduleManager->setActionParams(['cacheClearEnabled' => false]);
            }

            try {
                // Upgrade the module
                $startTime = microtime(true);
                $oldVersion = JprestaUtils::dbGetValue('SELECT version FROM `' . _DB_PREFIX_ . 'module` WHERE name=\'' . pSQL($this->name) . '\'');
                if ($moduleManager->upgrade($this->name)) {
                    $this->_confirmations[] = $this->l('The module has been upgraded to version', 'jprestaspeedpack') . ' ' . $this->version;
                    $newVersion = $this->version;
                    JprestaUtils::addLog('PageCache | Upgrading module ' . $this->name . " from version $oldVersion to version $newVersion in " . number_format(microtime(true) - $startTime, 3) . ' second(s)', 1, null, null, null, true);
                } else {
                    $this->_errors[] = $this->l('The module could not be upgraded correctly, contact the support with above error messages', 'jprestaspeedpack');
                }
            } catch (Throwable $e) {
                JprestaUtils::addLog('PageCache | Error while upgrading the module: ' . JprestaUtils::jTraceEx($e), 2);
            }
        }
    }

    public static function getCache($id_shop = false)
    {
        $cacheInstance = null;
        if (!$id_shop) {
            $id_shop = Shop::getContextShopID();
        }
        if ($id_shop === null) {
            // Happens in back office when a group of shop is selected. Is used during hooks for cache refreshment.
            $ids_shops = Shop::getShops(true, Shop::getContextShopGroupID(), true);
            $cacheInstance = new PageCacheCacheMultiStore();
            foreach ($ids_shops as $id_shop) {
                $cacheInstance->addCache(self::getCacheInstance($id_shop));
            }
        } else {
            $cacheInstance = self::getCacheInstance($id_shop);
        }

        return $cacheInstance;
    }

    public static function getParentCacheDirectory()
    {
        if (JprestaUtils::startsWith(_PS_CACHE_DIR_, _PS_ROOT_DIR_ . '/var/cache/')) {
            // Prestashop clears the cache everytime a module is enabled/disabled and we don't want this behavior.
            // Also having different cache for DEV and PROD is not needed for this module.
            return _PS_ROOT_DIR_ . '/var/cache/' . self::PAGECACHE_DIR;
        } else {
            return _PS_CACHE_DIR_ . self::PAGECACHE_DIR;
        }
    }

    /**
     * @return void
     */
    public function checkInstallCache()
    {
        $typecache = JprestaUtils::getConfigurationAllShop('pagecache_typecache');
        $allShopIds = JprestaUtils::getCompleteListOfShopsID();
        foreach ($allShopIds as $shopId) {
            self::getCacheInstance((int) $shopId, $typecache)->checkInstall($this);
        }
    }

    /**
     * @return void
     */
    public function installCache()
    {
        $typecache = JprestaUtils::getConfigurationAllShop('pagecache_typecache');
        $allShopIds = JprestaUtils::getCompleteListOfShopsID();
        foreach ($allShopIds as $shopId) {
            self::getCacheInstance((int) $shopId, $typecache)->install($this);
        }
    }

    /**
     * @param $typecache string
     *
     * @return void
     */
    public function uninstallCache($typecache)
    {
        $allShopIds = JprestaUtils::getCompleteListOfShopsID();
        foreach ($allShopIds as $shopId) {
            self::getCacheInstance($shopId, $typecache)->uninstall($this);
        }
    }

    /**
     * @param $id_shop int
     * @param $type string
     *
     * @return PageCacheCacheMemcache|PageCacheCacheMemcached|PageCacheCacheSimpleFS|PageCacheCacheStatic|PageCacheCacheZipFS
     */
    private static function getCacheInstance($id_shop, $type = null)
    {
        if ($type === null) {
            $type = JprestaUtils::getConfigurationAllShop('pagecache_typecache');
        }
        $key = $type . (int) $id_shop;

        static $cacheInstances = [];
        if (array_key_exists($key, $cacheInstances)) {
            return $cacheInstances[$key];
        }

        if (strcmp('static', $type) === 0 && PageCacheCacheStatic::isCompatible()) {
            $cachedir = self::getParentCacheDirectory() . '/static';
            $cacheInstances[$key] = new PageCacheCacheStatic($cachedir, Configuration::get('pagecache_logs', null, null, $id_shop) > 1);
        } elseif (strcmp('stdzip', $type) === 0 && PageCacheCacheZipFS::isCompatible()) {
            $cachedir = self::getParentCacheDirectory() . '/stdzip/' . $id_shop;
            $cacheInstances[$key] = new PageCacheCacheZipFS($cachedir, Configuration::get('pagecache_logs', null, null, $id_shop) > 1);
        } elseif (strcmp('memcache', $type) === 0 && PageCacheCacheMemcache::isCompatible()) {
            $cacheInstances[$key] = new PageCacheCacheMemcache(Configuration::get('pagecache_typecache_memcache_host'), (int) Configuration::get('pagecache_typecache_memcache_port'));
        } elseif (strcmp('memcached', $type) === 0 && PageCacheCacheMemcached::isCompatible()) {
            $cacheInstances[$key] = new PageCacheCacheMemcached(Configuration::get('pagecache_typecache_memcached_host'), (int) Configuration::get('pagecache_typecache_memcached_port'));
        }
        if (!array_key_exists($key, $cacheInstances)) {
            $cachedir = self::getParentCacheDirectory() . '/std/' . $id_shop;
            $cacheInstances[$key] = new PageCacheCacheSimpleFS($cachedir, Configuration::get('pagecache_logs', null, null, $id_shop) > 1);
        }

        return $cacheInstances[$key];
    }

    public static function getWidgetBlockDir()
    {
        return _PS_MODULE_DIR_ . 'jprestaspeedpack/widget_blocks/';
    }

    private static function getWidgetBlockTemplate($blockKey)
    {
        return self::getWidgetBlockDir() . $blockKey . '.tpl';
    }

    public static function setWidgetBlockTemplate($blockKey, $content)
    {
        $cachedir = self::getWidgetBlockDir();
        if (!file_exists($cachedir)) {
            // Creates subdirectory with 777 to be sure it will work
            $grants = 0777;
            if (!@mkdir($cachedir, $grants, true)) {
                $mkdirErrorArray = error_get_last();
                if (!file_exists($cachedir)) {
                    if ($mkdirErrorArray !== null) {
                        JprestaUtils::addLog('PageCache | Cannot create directory ' . $cachedir . " with grants $grants: " . $mkdirErrorArray['message'], 4);
                    } else {
                        JprestaUtils::addLog('PageCache | Cannot create directory ' . $cachedir . " with grants $grants", 4);
                    }
                }
            }
        }
        if (preg_match('/[^a-zA-Z0-9]/', $blockKey)) {
            JprestaUtils::addLog('PageCache | Invalid blockey ' . $blockKey, 4);
        } else {
            $cachefile = $cachedir . $blockKey . '.tpl';
            $write_ok = file_put_contents($cachefile, $content);
            if ($write_ok === false) {
                $mkdirErrorArray = error_get_last();
                if ($mkdirErrorArray !== null) {
                    JprestaUtils::addLog("PageCache | Cannot write file $cachefile: " . $mkdirErrorArray['message'], 4);
                } else {
                    JprestaUtils::addLog("PageCache | Cannot write file $cachefile", 4);
                }
            }
        }
    }

    /**
     * Override Module::updateModuleTranslations()
     */
    public function updateModuleTranslations()
    {
        // Speeds up installation: do nothing because PageCache translation are not in Prestashop language pack
    }

    public function disable($force_all = false)
    {
        $ret = parent::disable($force_all);
        $this->uninstallCache(JprestaUtils::getConfigurationAllShop('pagecache_typecache'));
        // SPEEDPACK
        // disable submodules
        $ret &= $this->jpresta_submodules['JprestaWebpModule']->disable();
        $ret &= $this->jpresta_submodules['JprestaSQLProfiler']->disable();
        $ret &= $this->jpresta_submodules['JprestaLazyLoading']->disable();
        $ret &= $this->jpresta_submodules['JprestaDbOptimizer']->disable();

        // SPEEDPACK£
        return (bool) $ret;
    }

    public function enable($force_all = false)
    {
        $this->disableCommonOverridesWithThemeConfigurator();

        $ret = parent::enable($force_all);
        $this->installCache();

        self::updateCacheKeyForCountries();
        self::updateCacheKeyForUserGroups();

        // Disable tokens on the front end
        Configuration::updateValue('PS_TOKEN_ENABLE', 0);

        if (JprestaUtils::isModuleEnabled('lgcookieslaw')) {
            // Enable compatibility with LG Cookies law module
            Configuration::updateValue('PS_LGCOOKIES_PUC_COMPATIBILITY', 1);
        }

        // SPEEDPACK
        // enable submodules
        $ret &= $this->jpresta_submodules['JprestaWebpModule']->enable();
        $ret &= $this->jpresta_submodules['JprestaSQLProfiler']->enable();
        $ret &= $this->jpresta_submodules['JprestaLazyLoading']->enable();
        $ret &= $this->jpresta_submodules['JprestaDbOptimizer']->enable();

        // SPEEDPACK£
        return (bool) $ret;
    }

    public function uninstall()
    {
        if (!defined('JprestaMigPCU2SP')) {
            try {
                $this->clearCache('uninstall');
                JprestaCustomer::deleteAllFakeUsers();
            } catch (Throwable $e) {
                // Ignore because it's not a big deal if cache is not cleared
            }
            Configuration::deleteByName('pagecache_install_step');
            Configuration::deleteByName('pagecache_always_infosbox');
            Configuration::deleteByName('pagecache_debug');
            Configuration::deleteByName('pagecache_maxrows');
            Configuration::deleteByName('pagecache_skiplogged');
            Configuration::deleteByName('pagecache_normalize_urls');
            Configuration::deleteByName('pagecache_logout_nocache');
            Configuration::deleteByName('pagecache_logs');
            Configuration::deleteByName('pagecache_depend_on_device_auto');
            Configuration::deleteByName('pagecache_depend_on_css_js');
            Configuration::deleteByName('pagecache_depend_on_other_groups');
            Configuration::deleteByName('pagecache_tablet_is_mobile');
            Configuration::deleteByName('pagecache_exec_header_hook');
            Configuration::deleteByName('pagecache_use_dispatcher_hook');
            Configuration::deleteByName('pagecache_stats');
            Configuration::deleteByName('pagecache_profiling');
            Configuration::deleteByName('pagecache_typecache');
            Configuration::deleteByName('pagecache_show_stats');
            Configuration::deleteByName('pagecache_groups');
            Configuration::deleteByName('pagecache_seller');
            Configuration::deleteByName('pagecache_ignored_params');
            Configuration::deleteByName('pagecache_dyn_hooks');
            Configuration::deleteByName('pagecache_dyn_widgets');
            foreach (self::getManagedControllersNames() as $controller) {
                Configuration::deleteByName('pagecache_' . $controller);
                Configuration::deleteByName('pagecache_' . $controller . '_timeout');
                Configuration::deleteByName('pagecache_' . $controller . '_expires');
                Configuration::deleteByName('pagecache_' . $controller . '_u_bl');
                Configuration::deleteByName('pagecache_' . $controller . '_d_bl');
                Configuration::deleteByName('pagecache_' . $controller . '_a_mods');
                Configuration::deleteByName('pagecache_' . $controller . '_u_mods');
                Configuration::deleteByName('pagecache_' . $controller . '_d_mods');
            }
            Configuration::deleteByName('pagecache_static_expires');
            Configuration::deleteByName('pagecache_product_home_u_bl');
            Configuration::deleteByName('pagecache_product_home_d_bl');
            Configuration::deleteByName('pagecache_product_home_a_mods');
            Configuration::deleteByName('pagecache_product_home_u_mods');
            Configuration::deleteByName('pagecache_product_home_d_mods');

            Configuration::deleteByName('pagecache_cache_warmer_settings');
            Configuration::deleteByName('pagecache_profiling_min_ms');
            Configuration::deleteByName('pagecache_statsttfb');
            Configuration::deleteByName('pagecache_stats_perf');
            Configuration::deleteByName('pagecache_ignore_after_pattern');
            Configuration::deleteByName('pagecache_ignore_before_pattern');
            Configuration::deleteByName('pagecache_currencies_to_cache');
            Configuration::deleteByName('pagecache_max_exec_time');
            Configuration::deleteByName('pagecache_ignore_url_regex');
            Configuration::deleteByName('pagecache_ignore_referers');
            Configuration::deleteByName('pagecache_cachekey_usergroups');
            Configuration::deleteByName('pagecache_cachekey_countries');
            Configuration::deleteByName('pagecache_cfgadvancedjs');

            PageCacheDAO::dropTables();
        }
        $this->uninstallAllTab();
        $this->uninstallStaticCode();

        $ret = parent::uninstall();

        // Clean cache in case of a reset
        Cache::clean('Module::getModuleIdByName_' . pSQL($this->name));

        // SPEEDPACK
        // Install submodules
        $this->jpresta_submodules['JprestaWebpModule']->uninstall();
        $this->jpresta_submodules['JprestaSQLProfiler']->uninstall();
        $this->jpresta_submodules['JprestaLazyLoading']->uninstall();
        $this->jpresta_submodules['JprestaDbOptimizer']->uninstall();
        // SPEEDPACK£

        if ((bool) $ret) {
            JprestaUtils::addLog('PageCache | Module ' . $this->name . ' version ' . $this->version . ' uninstalled' . (defined('JprestaMigPCU2SP') ? ' (with migration to SP)' : ''), 1);
        } else {
            JprestaUtils::addLog('PageCache | Uninstall of module ' . $this->name . ' version ' . $this->version . ' failed' . (defined('JprestaMigPCU2SP') ? ' (with migration to SP)' : ''), 3);
        }

        return (bool) $ret;
    }

    public function isSpeedPack()
    {
        return $this->name === 'jprestaspeedpack';
    }

    private function _setDefaultConfiguration($id_shop_group = null, $id_shop = null)
    {
        // Use backlink heuristic...
        Configuration::updateValue('pagecache_cms_u_bl', true, false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_cms_d_bl', true, false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_supplier_u_bl', true, false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_supplier_d_bl', true, false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_manufacturer_u_bl', true, false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_manufacturer_d_bl', true, false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_product_u_bl', true, false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_product_d_bl', true, false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_product_home_u_bl', true, false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_product_home_d_bl', true, false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_category_u_bl', true, false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_category_d_bl', true, false, $id_shop_group, $id_shop);

        // Default impacted modules
        Configuration::updateValue('pagecache_category_a_mods', 'blockcategories ps_categorytree iqitcontentcreator', false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_category_u_mods', 'blockcategories ps_categorytree iqitcontentcreator', false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_category_d_mods', 'blockcategories ps_categorytree iqitcontentcreator', false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_supplier_a_mods', 'blocksupplier', false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_supplier_u_mods', 'blocksupplier', false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_supplier_d_mods', 'blocksupplier', false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_manufacturer_a_mods', 'blockmanufacturer', false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_manufacturer_u_mods', 'blockmanufacturer', false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_manufacturer_d_mods', 'blockmanufacturer', false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_product_a_mods', 'newsellerincategory sthomenew blocknewproducts ps_newproducts posnewproduct zonehomeblocks wtnewproducts iqitcontentcreator', false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_product_home_a_mods', 'homefeatured ps_featuredproducts', false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_product_home_u_mods', 'homefeatured ps_featuredproducts', false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_product_home_d_mods', 'homefeatured ps_featuredproducts', false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_cms_a_mods', 'blockcms', false, $id_shop_group, $id_shop);

        // Enable cache on all managed_controllers and timeout = 7 days
        foreach (self::getManagedControllersNames() as $controller) {
            Configuration::updateValue('pagecache_' . $controller, 1, false, $id_shop_group, $id_shop);
            Configuration::updateValue('pagecache_' . $controller . '_timeout', 60 * 24 * 7, false, $id_shop_group, $id_shop);
        }
        // Do not cache contact form by default anymore (anti-spam system)
        Configuration::updateValue('pagecache_contact', 0, false, $id_shop_group, $id_shop);
        Configuration::updateValue('pagecache_contact_timeout', 0, false, $id_shop_group, $id_shop);

        // Set default dynamic hooks
        $pagecache_dyn_hooks = '';
        $module_list = Hook::getHookModuleExecList();
        if (JprestaUtils::isIterable($module_list)) {
            foreach ($module_list as $hook_name => $modules) {
                foreach ($modules as $module) {
                    if (in_array($hook_name, self::$default_dyn_hooks) && in_array($module['module'],
                        self::$default_dyn_modules)) {
                        $pagecache_dyn_hooks .= $hook_name . '|' . $module['module'] . ',';
                    } /* Special case: blockcart will be dynamic if ajax is disabled */
                    elseif (in_array($hook_name, self::$default_dyn_hooks) && strcmp($module['module'],
                        'blockcart') == 0) {
                        if (!(int) Configuration::get('PS_BLOCK_CART_AJAX')) {
                            $pagecache_dyn_hooks .= $hook_name . '|' . $module['module'] . ',';
                        }
                    }
                }
            }
        }
        Configuration::updateValue('pagecache_dyn_hooks', $pagecache_dyn_hooks, false, $id_shop_group, $id_shop);

        // Set default javascript to execute (empty since autoconf)
        Configuration::updateValue('pagecache_cfgadvancedjs', '', false, $id_shop_group, $id_shop);

        // First install step is 0 (none)
        Configuration::updateValue('pagecache_install_step', 0, false, $id_shop_group, $id_shop);

        // Do not always display infos box by default
        Configuration::updateValue('pagecache_always_infosbox', false, false, $id_shop_group, $id_shop);

        // Not in production by default
        Configuration::updateValue('pagecache_debug', true, false, $id_shop_group, $id_shop);

        // Cache logged in users by default
        Configuration::updateValue('pagecache_skiplogged', false, false, $id_shop_group, $id_shop);

        // Set HTML minification to disabled by default to encourage testing before activation
        Configuration::updateValue('pagecache_minifyhtml', false, false, $id_shop_group, $id_shop);

        // Normalize URLs by default
        Configuration::updateValue('pagecache_normalize_urls', true, false, $id_shop_group, $id_shop);

        // Disable logs by default
        Configuration::updateValue('pagecache_logs', false, false, $id_shop_group, $id_shop);

        // Auto detect mobile version
        Configuration::updateValue('pagecache_depend_on_device_auto', true, false, $id_shop_group, $id_shop);

        // Tablet is not considered as mobile by default
        Configuration::updateValue('pagecache_tablet_is_mobile', false, false, $id_shop_group, $id_shop);

        // Do not add CSS and JS version in the cache key by default
        Configuration::updateValue('pagecache_depend_on_css_js', false, false, $id_shop_group, $id_shop);

        // Must we call header hook for dynamic request
        Configuration::updateValue('pagecache_exec_header_hook', false, false, $id_shop_group, $id_shop);

        // Ignore all backlinks before tag /header>
        Configuration::updateValue('pagecache_ignore_before_pattern',
            JprestaUtils::encodeConfiguration('/header>'), $id_shop_group, $id_shop);

        // Ignore all backlinks after tag /footer>
        Configuration::updateValue('pagecache_ignore_after_pattern',
            JprestaUtils::encodeConfiguration('/footer>'), $id_shop_group, $id_shop);

        // Ignore faceted searches and currency change
        Configuration::updateValue('pagecache_ignore_url_regex',
            JprestaUtils::encodeConfiguration('.*[\?&]q=.*|.*SubmitCurrency=1.*'), $id_shop_group, $id_shop);

        // Disable profiling by default
        JprestaUtils::saveConfigurationAllShop('pagecache_profiling', false);
        JprestaUtils::saveConfigurationAllShop('pagecache_profiling_min_ms', 100);
        JprestaUtils::saveConfigurationAllShop('pagecache_profiling_max_reached', false);

        // Enable static cache system by default
        Configuration::updateValue('pagecache_typecache', 'static', false, $id_shop_group, $id_shop);

        // Disable cache for customizable products by default
        Configuration::updateValue('pagecache_cache_customizable', false, false, $id_shop_group, $id_shop);

        // Default browser cache to 15 minutes
        foreach (self::getManagedControllersNames() as $controller) {
            Configuration::updateValue('pagecache_' . $controller . '_expires', 15, false, $id_shop_group, $id_shop);
        }
        Configuration::updateValue('pagecache_static_expires', 15, false, $id_shop_group, $id_shop);

        // Default ad tracking parameters
        Configuration::updateValue('pagecache_ignored_params', self::IGNORED_URL_PARAMS, false, $id_shop_group, $id_shop);

        // Max execution time for cache warmer
        Configuration::updateValue('pagecache_max_exec_time', min(90, max(10, (int) ini_get('max_execution_time') - 5)), false, $id_shop_group, $id_shop);

        // Disable tokens on front
        Configuration::updateValue('PS_TOKEN_ENABLE', 0, false, $id_shop_group, $id_shop);

        // Enable stats on TTFB by default
        Configuration::updateValue('pagecache_statsttfb', true, false, $id_shop_group, $id_shop);

        // Enable all currencies for cache
        $this->enableAllCurrencies();
    }

    /**
     * Add missing parameters that must be ignored (defined in self::IGNORED_URL_PARAMS)
     *
     * @return void
     */
    public static function updateIgnoredUrlParameters()
    {
        $mustHave = explode(',', self::IGNORED_URL_PARAMS);
        foreach (JprestaUtils::getCompleteListOfShopsID() as $id_shop) {
            $currentParams = explode(',', JprestaUtils::getConfigurationByShopId('pagecache_ignored_params', $id_shop, ''));
            $newList = array_merge($mustHave, $currentParams);
            $newList = array_unique($newList);
            JprestaUtils::saveConfigurationByShopId('pagecache_ignored_params', implode(',', $newList), $id_shop);
        }
    }

    public function enableAllCurrencies()
    {
        $pagecache_currencies_to_cache = [];
        foreach (Currency::getCurrenciesByIdShop() as $currency) {
            if ($currency['active']) {
                $pagecache_currencies_to_cache[] = $currency['iso_code'];
            }
        }
        JprestaUtils::saveConfigurationAllShop('pagecache_currencies_to_cache', implode(',', $pagecache_currencies_to_cache));
    }

    public function patchSmartyConfigFront()
    {
        if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>')) {
            // This modification has been accepted on github https://github.com/PrestaShop/PrestaShop/pull/8744
            $smartyFrontCongigFile = _PS_CONFIG_DIR_ . '/smartyfront.config.inc.php';
            $str = Tools::file_get_contents($smartyFrontCongigFile);
            if (strpos($str, '$widget->renderWidget(null, $params)') !== false) {
                file_put_contents($smartyFrontCongigFile . '.before_' . $this->name, $str);
                $str = str_replace('$widget->renderWidget(null, $params)', "Hook::coreRenderWidget(\$widget, isset(\$params['hook']) ? \$params['hook'] : null, \$params)", $str);
                file_put_contents($smartyFrontCongigFile, $str);
            } elseif (strpos($str, "\$widget->renderWidget(isset(\$params['hook']) ? \$params['hook'] : null, \$params)") !== false) {
                file_put_contents($smartyFrontCongigFile . '.before_' . $this->name, $str);
                $str = str_replace("\$widget->renderWidget(isset(\$params['hook']) ? \$params['hook'] : null, \$params)", "Hook::coreRenderWidget(\$widget, isset(\$params['hook']) ? \$params['hook'] : null, \$params)", $str);
                file_put_contents($smartyFrontCongigFile, $str);
            }
        }
    }

    public function patchSmartyConfigFrontWidgetBlock()
    {
        if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>')) {
            $smartyFrontCongigFile = _PS_CONFIG_DIR_ . '/smartyfront.config.inc.php';
            $str = Tools::file_get_contents($smartyFrontCongigFile);
            if (strpos($str, 'smartyWidgetBlockPageCache') === false) {
                file_put_contents($smartyFrontCongigFile . '.before_' . $this->name . '_widget_block', $str);
                $str = preg_replace(
                    "/smartyRegisterFunction\s*\(\s*\\\$smarty\s*,\s*'block'\s*,\s*'widget_block'\s*,\s*'smartyWidgetBlock'\s*\)\s*;/",
                    "if (Module::isEnabled('" . $this->name . "')) {\n\trequire_once _PS_MODULE_DIR_ . '" . $this->name . '/' . $this->name . ".php';\n\tsmartyRegisterFunction(\$smarty, 'block', 'widget_block', array('" . get_class($this) . "', 'smartyWidgetBlockPageCache'));\n\t\$smarty->registerFilter('pre', array('" . get_class($this) . "', 'smartyWidgetBlockPageCachePrefilter'));\n} else {\n\tsmartyRegisterFunction(\$smarty, 'block', 'widget_block', 'smartyWidgetBlock');\n}",
                    $str);
            } else {
                // Make sure it uses the correct class
                $str = str_replace('\'pagecachestd/pagecachestd.php\'', '\'' . $this->name . '/' . $this->name . '.php\'', $str);
                $str = str_replace('\'pagecachestd\'', '\'' . $this->name . '\'', $str);
                $str = str_replace('\'PageCacheStd\'', '\'' . get_class($this) . '\'', $str);
                $str = str_replace('\'pagecache/pagecache.php\'', '\'' . $this->name . '/' . $this->name . '.php\'', $str);
                $str = str_replace('\'pagecache\'', '\'' . $this->name . '\'', $str);
                $str = str_replace('\'PageCache\'', '\'' . get_class($this) . '\'', $str);
                $str = str_replace('\'jprestaspeedpack/jprestaspeedpack.php\'', '\'' . $this->name . '/' . $this->name . '.php\'', $str);
                $str = str_replace('\'jprestaspeedpack\'', '\'' . $this->name . '\'', $str);
                $str = str_replace('\'Jprestaspeedpack\'', '\'' . get_class($this) . '\'', $str);
            }
            file_put_contents($smartyFrontCongigFile, $str);
            // Now clear the cache to recompile everything
            Tools::clearCompile();
        }
    }

    public function installStaticCode()
    {
        $path = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'index.php';

        // Uninstall first to be sure we install the latest code
        $this->uninstallStaticCode();
        $scriptCode = '// ~~start-pagecacheultimate~~ Do not remove this comment, ' . $this->name . ' will update it automatically
$staticCacheScript = dirname(__FILE__).\'/modules/' . $this->name . '/static.config.php\';
if (file_exists($staticCacheScript)) {
    try {
        require_once $staticCacheScript;
    } catch (Throwable $e) {
        error_log("Page Cache Ultimate - Cannot use the static cache, an error occured: " . $e->getMessage());
    }
}
// ~~end-pagecacheultimate~~ Do not remove this comment, ' . $this->name . ' will update it automatically

';
        if (file_exists($path)) {
            $content = Tools::file_get_contents($path);
            if ($content) {
                $newContent = preg_replace('#require[\s(]+dirname\s*\(\s*__FILE__\s*\)\s*\.\s*\'/config/config.inc.php\'[\s)]*;\s*\r?\n#', $scriptCode . '$0', $content);
                file_put_contents($path, $newContent);
            }
        }
    }

    public function checkStaticCode()
    {
        $path = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'index.php';
        if (file_exists($path)) {
            $content = Tools::file_get_contents($path);
            if (JprestaUtils::strpos($content, '~~start-pagecacheultimate~~') === false) {
                $this->installStaticCode();
            }
        }
    }

    private function uninstallStaticCode()
    {
        $path = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'index.php';
        if (file_exists($path)) {
            $content = Tools::file_get_contents($path);
            if ($content) {
                $newContent = preg_replace('#// ~~start-pagecacheultimate~~.*~~end-pagecacheultimate~~[^\n\r]*\r?\n#s', '', $content);
                file_put_contents($path, $newContent);
            }
        }
    }

    public function getContent()
    {
        $link = $this->context->link->getAdminLink('AdminPageCacheConfiguration');
        if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>')) {
            Tools::redirect($link);
        } else {
            // There is a bug in redirect and getAdminLink in PS1.5 and PS1.6 so we do it ourselves
            $path = parse_url($_SERVER['REQUEST_URI'])['path'];
            header('Location: //' . $_SERVER['HTTP_HOST'] . dirname($path) . '/' . $link);
            exit;
        }
    }

    public function hookDisplayHeader()
    {
        // Forward to sub modules
        foreach ($this->jpresta_submodules as $jpresta_submodule) {
            $jpresta_submodule->displayHeader();
        }

        if (self::canBeCached() || self::isDisplayStats()) {
            // A bug in PS 1.6.0.6 insert jquery multiple times in CCC mode
            $already_inserted = false;
            foreach ($this->context->controller->js_files as $js_uri) {
                $already_inserted = $already_inserted || (strstr($js_uri, 'jquery-') !== false) || (strstr($js_uri, 'jquery.js') !== false);
            }
            if (!$already_inserted && method_exists($this->context->controller, 'addJquery')) {
                $this->context->controller->addJquery();
            }

            $this->context->controller->addJS($this->_path . 'views/js/pagecache-v9-5-9.js');
            if (self::isDisplayStats()) {
                $this->context->controller->addCSS($this->_path . 'views/css/pagecache.css');
            }

            if (JprestaUtils::version_compare(_PS_VERSION_, '1.6', '<=')) {
                // Make sure pagecache will be the first javascript to be loaded. This avoid
                // other javascript errors to block pagecache treatments. So we place it just after
                // jquery.
                $new_js_files = [];
                $pagecache_js_file = null;
                $jquery_js_files = [];
                foreach ($this->context->controller->js_files as $js_file) {
                    if (strstr($js_file, '/js/jquery/') !== false || strstr($js_file, 'jquery.js') !== false) {
                        $jquery_js_files[] = $js_file;
                    } elseif (empty($pagecache_js_file) && strstr($js_file, 'pagecache-v9-5-9.js') !== false) {
                        $pagecache_js_file = $js_file;
                    } else {
                        $new_js_files[] = $js_file;
                    }
                }
                if (!empty($pagecache_js_file)) {
                    array_unshift($new_js_files, $pagecache_js_file);
                }
                $jquery_js_files = array_reverse($jquery_js_files);
                foreach ($jquery_js_files as $jquery_js_file) {
                    array_unshift($new_js_files, $jquery_js_file);
                }
                $this->context->controller->js_files = $new_js_files;
            }

            if (self::canBeCached()) {
                // There is no escape method available to allow to display javascript code
                // so we cannot use a template
                $js = trim(Configuration::get('pagecache_cfgadvancedjs'));
                if (JprestaUtils::isModuleEnabled('creativeelements')) {
                    Context::getContext()->smarty->assign('jpresta_cart_module', 'creativeelements');
                    $modifiedJs = str_replace("$.ajax({url:prestashop.urls.pages.cart,method:'POST',dataType:'json',data:{ajax:1,action:'update'}}).then(function(a){a.success&&a.cart&&prestashop.emit('updateCart',{reason:{linkAction:'refresh'},resp:a})});", '// Cart is refreshed from HTML to be faster', $js);
                } else {
                    Context::getContext()->smarty->assign('jpresta_cart_module', 'default');
                    $modifiedJs = str_replace("setTimeout(\"prestashop.emit('updateCart', {reason: {linkAction: 'refresh'}, resp: {errors:[]}});\", 10);", '// Cart is refreshed from HTML to be faster', $js);
                }
                $updateCartInTpl = $js != $modifiedJs;
                $dynJs = '<scr' . 'ipt>
                var jprestaUpdateCartDirectly = ' . ($updateCartInTpl ? '1' : '0') . ';
                var jprestaUseCreativeElements = ' . (JprestaUtils::isModuleEnabled('creativeelements') ? '1' : '0') . ';
                </script><scr' . 'ipt>
pcRunDynamicModulesJs = function() {
'; // Let the new line here!
                if (!empty($modifiedJs)) {
                    $dynJs .= $modifiedJs;
                }
                $dynJs .= '
};</script>'; // Let the new line here!

                return $dynJs;
            } else {
                return '';
            }
        } elseif (Configuration::get('pagecache_skiplogged') && Context::getContext()->customer->isLogged()) {
            // User want to disable cache for logged in users so we add a random URL parameter
            // to all links to disable previous cache done by browser
            if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>')) {
                return $this->fetch('module:' . $this->name . '/views/templates/hook/pagecache-disablecache.tpl');
            } else {
                return $this->display(__FILE__, '/views/templates/hook/pagecache-disablecache.tpl');
            }
        } else {
            return '';
        }
    }

    public function hookdisplayMobileHeader()
    {
        $this->hookDisplayHeader();
    }

    public function hookActionShopDataDuplication($params)
    {
        // (int)$params['new_id_shop']
        // (int)$params['old_id_shop']
        $new_id_shop = (int) $params['new_id_shop'];
        $this->_setDefaultConfiguration(Shop::getGroupFromShop($new_id_shop), $new_id_shop);
    }

    public function hookActionOutputHTMLBefore($params)
    {
        // SPEEDPACK
        $this->jpresta_submodules['JprestaWebpModule']->hookActionOutputHTMLBefore($params);
        // SPEEDPACK£

        if (self::canBeCached()) {
            if (self::canMinifyHtml() && Configuration::get('pagecache_minifyhtml')) {
                $htmlMin = new voku\helper\HtmlMin();
                $params['html'] = $htmlMin->minify($params['html']);
            }

            // Save the generated HTML into a file => create a cache
            $this->cacheThis($params['html']);
            if (self::isCacheWarmer() || self::isStatusChecker()) {
                // Reduce the size of the response to the minimum (save bandwidth and time)
                if (!headers_sent()) {
                    if (!self::isNotCode200()) {
                        // Here our cache-warmer/status-checker do not care about these headers, just remove them
                        header_remove();
                        // Indicates that there is no content so it removes "Content-Length" and "Content-Type" headers
                        header('HTTP/1.1 204 CACHE CREATED');
                    }
                    // Unset PHP session (to avoid a useless cookie)
                    session_abort();
                    // Don't send any cookies
                    Context::getContext()->cookie->disallowWriting();
                }
                exit;
            }
        }
    }

    // SPEEDPACK
    public function hookActionAjaxDieSearchControllerdoProductSearchBefore($params)
    {
        $this->jpresta_submodules['JprestaWebpModule']->hookActionAjaxDieSearchControllerdoProductSearchBefore($params);
    }

    public function hookActionAjaxDieCategoryControllerdoProductSearchBefore($params)
    {
        $this->jpresta_submodules['JprestaWebpModule']->hookActionAjaxDieCategoryControllerdoProductSearchBefore($params);
    }

    public function hookActionOnImageResizeAfter($params)
    {
        $dstFile = $params['dst_file'];
        $this->jpresta_submodules['JprestaWebpModule']->onImageModification($dstFile);
    }
    // SPEEDPACK£

    public function hookActionDispatcherBefore()
    {
        self::init();

        $this->pre_display_html = self::preDisplayStats();
        if (!Configuration::get('pagecache_use_dispatcher_hook') && self::displayCacheIfExists()) {
            self::displayStats(true, $this->pre_display_html);
            exit;
        }
    }

    public function hookActionAdminProductsControllerSaveAfter($params)
    {
        if (self::$updatingProductFromAdminController) {
            // Many datas are saved AFTER the postProcess of AdminProductsController so we use this hook for PS < 1.7.
            self::$updatingProductFromAdminController = false;
            $this->hookActionObjectProductUpdateAfter(['object' => new Product(Tools::getValue('id_product'))]);
        }
    }

    public function hookActionDispatcherAfter()
    {
        self::sendContextCookie();

        self::$skipUpdateCacheKey = false;
        if (self::$needUpdateCacheKey) {
            self::updateCacheKeyForCountries();
            self::updateCacheKeyForUserGroups();
            // In case it is called multiple times
            self::$needUpdateCacheKey = false;
        }
        if (self::$updatingProductFromAdminController) {
            // Many datas are saved AFTER the postProcess of AdminProductsController so we use this hook.
            self::$updatingProductFromAdminController = false;
            $this->hookActionObjectProductUpdateAfter(['object' => new Product(Tools::getValue('id_product'))]);
        }
        self::displayStats(false, $this->pre_display_html);
    }

    /**
     * Update the context after customer has been created
     */
    public function hookActionCustomerAccountAdd()
    {
        self::sendContextCookie();
    }

    /**
     * Update the context after customer has logged in
     */
    public function hookActionAuthentication()
    {
        self::sendContextCookie();
    }

    /**
     * Update the context after customer has logged out
     */
    public function hookActionCustomerLogoutAfter()
    {
        self::sendContextCookie();
    }

    public function hookActionDispatcher()
    {
        if (self::canBeCached()) {
            if (Configuration::get('pagecache_use_dispatcher_hook') && self::displayCacheIfExists()) {
                self::displayStats(true, $this->pre_display_html);
                exit;
            }

            // Remove cookie, cart and customer informations to cache
            // a 'standard' page

            Tools::setCookieLanguage($this->context->cookie);

            // Write cookie if needed (language changed, etc.) before we remove it
            $this->context->cookie->write();

            $anonymousCookie = new Cookie($this->name, '', 1);
            $anonymousCookie->id_lang = $this->context->language->id;
            unset($anonymousCookie->detect_language);

            foreach (self::$cookies_to_preserve as $cookie_name => $cookie_value) {
                if (isset($this->context->cookie->{$cookie_name})) {
                    if ($cookie_name === $cookie_value) {
                        $anonymousCookie->{$cookie_name} = $this->context->cookie->{$cookie_name};
                    } else {
                        $anonymousCookie->{$cookie_name} = $cookie_value;
                    }
                }
            }
            if (method_exists('Cookie', 'getAll')) {
                // Some cookies are set in header like for autolanguagecurrency module. We need to preserve them and remove
                // the others
                foreach ($anonymousCookie->getAll() as $anonymousCookieName => $anonymousCookieValue) {
                    if (!array_key_exists($anonymousCookieName, self::$cookies_to_preserve)) {
                        unset($anonymousCookie->{$anonymousCookieName});
                    }
                }
                // Add cookies preserved by prefix
                foreach ($this->context->cookie->getAll() as $cookieName => $cookieValue) {
                    foreach (self::$cookies_to_preserve_prefix as $prefix) {
                        if (JprestaUtils::strpos($cookieName, $prefix) === 0) {
                            $anonymousCookie->{$cookieName} = $cookieValue;
                        }
                    }
                }
            }

            $anonymousCustomer = JprestaCustomer::getOrCreateCustomerWithSameGroups($this->context->customer);
            $addressForTaxes = $this->getAddressForTaxes($this->context);
            $this->context->customer = $anonymousCustomer;
            if ($addressForTaxes) {
                $this->context->customer->geoloc_id_country = $addressForTaxes->id_country;
                $this->context->customer->id_state = $addressForTaxes->id_state;
                $this->context->customer->postcode = $addressForTaxes->postcode;
                // The address of current customer will be used to generate the cache
                // We cheat the memory cache (restricted to the execution of this script) to get the correct address
                // while computing taxes.
                Cache::store('Address::getFirstCustomerAddressId_' . (int) $this->context->customer->id . '-' . (bool) true,
                    $addressForTaxes->id);
                Cache::store('Address::initialize_' . md5((int) $this->context->customer->geoloc_id_country . '-' . (int) $addressForTaxes->id_state . '-' . $addressForTaxes->postcode),
                    $addressForTaxes);
            }
            $this->context->cookie = $anonymousCookie;
            $this->context->cart = new Cart();
            $this->context->cart->id_lang = (int) $this->context->cookie->id_lang;
            $this->context->cart->id_currency = (int) $this->context->cookie->id_currency;
            $this->context->cart->id_guest = (int) $this->context->cookie->id_guest;
            $this->context->cookie->id_customer = $this->context->customer->id;
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !headers_sent()) {
                // Be sure that the cache directive is set to improve GTMetrix and PageSpeed Insight score
                header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            }
        }
    }

    private static function sendContextCookie()
    {
        static $cookieSent = false;
        if (!$cookieSent
            && !defined('_PS_ADMIN_DIR_')
            && !headers_sent()
            && self::getCache()->needsContextCookie()
        ) {
            $cookieSent = true;
            if (!self::isNotLogout() || self::getControllerName() == 'leoquicklogin__leocustomer') {
                // Delete the cookie because the module connect or disconnect the customer after this part
                setcookie('jpresta_cache_context', '', time() - 3600, '/');
            } else {
                // Set the cache context in the cookies that will be used by the static cache
                if (PHP_VERSION_ID <= 50200) { /* PHP version > 5.2.0 */
                    setcookie('jpresta_cache_context', PageCacheDAO::getContextKeyById(PageCacheDAO::getOrCreateContextId(self::getCacheKeyInfos(true))), time() + 60 * 60 * 1, '/', '', 0);
                } else {
                    setcookie('jpresta_cache_context', PageCacheDAO::getContextKeyById(PageCacheDAO::getOrCreateContextId(self::getCacheKeyInfos(true))), time() + 60 * 60 * 1, '/', '', 0, false);
                }
            }
        }
    }

    /**
     * Create a cache key depending on address used to determine taxes. This cache key can be configured to reduce the
     * number of different value.
     */
    public static function getCountryStateZipcodeForTaxes($context)
    {
        static $current_loc_tax_key = null;
        if ($current_loc_tax_key === null) {
            $current_loc_tax_key = '-/-/-';
            // Taxes are determined by country, state and zipcode of the delivery or invoice address
            // If there is no cart or no address defined in cart then standard localization will be used for taxes
            $addressForTaxes = self::getAddressForTaxes($context);
            if ($addressForTaxes) {
                $cacheKey = '';
                if ((int) $addressForTaxes->id_country > 0) {
                    $country = new Country((int) $addressForTaxes->id_country);
                    $cacheKey .= $country->getFieldByLang('name') . '/';
                } else {
                    $cacheKey .= '*/';
                }
                if ((int) $addressForTaxes->id_state > 0) {
                    $state = new State((int) $addressForTaxes->id_state);
                    $cacheKey .= $state->getFieldByLang('name') . '/';
                } else {
                    $cacheKey .= '*/';
                }
                if ($addressForTaxes->postcode) {
                    $cacheKey .= $addressForTaxes->postcode;
                } else {
                    $cacheKey .= '*';
                }
                // Only set it once it is complete
                $current_loc_tax_key = $cacheKey;
            }
        }

        return $current_loc_tax_key;
    }

    public static function getTaxManagerDetails($context)
    {
        static $current_tax_manager_details = null;
        if ($current_tax_manager_details === null) {
            $current_tax_manager_details = false;
            $addressForTaxes = self::getAddressForTaxes($context);
            if ($addressForTaxes && (bool) Configuration::get('PS_TAX')) {
                $json = JprestaUtilsTaxManager::toJson($context->shop->id, $addressForTaxes);
                $current_tax_manager_details = PageCacheDAO::getOrCreateDetailsId($json);
            }
        }

        return $current_tax_manager_details;
    }

    protected static function getAddressForTaxes($context)
    {
        static $current_tax_address = null;
        if ($current_tax_address === null) {
            $current_tax_address = false;
            // Taxes are determined by country, state and zipcode of the delivery or invoice address
            // If there is no cart or no address defined in cart then standard localization will be used for taxes

            if ($context->cookie->jpresta_id_adress_for_taxes) {
                // Set by the Cache-Warmer
                $current_tax_address = Address::initialize($context->cookie->jpresta_id_adress_for_taxes);
            } else {
                /* Cart is initialized in FrontController::init which is after first call to this function */
                if ((int) $context->cookie->id_cart) {
                    $cart = new Cart($context->cookie->id_cart);
                    $id_address = $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                    /* If address is not set then FrontController::init will set the it with the first address of the customer */
                    if (!isset($id_address) || $id_address == 0) {
                        $id_address = (int) Address::getFirstCustomerAddressId($cart->id_customer);
                    }
                    if ($id_address) {
                        $current_tax_address = Address::initialize($id_address);
                        if ($current_tax_address->id_customer != $context->cookie->id_customer) {
                            // In some cases the address associated to the cart does not belong to the current user, maybe when using a superuser module
                            header('x-jpresta-fix-address: 1');
                            $current_tax_address = false;
                            $id_address = (int) Address::getFirstCustomerAddressId($context->cookie->id_customer);
                            if ($id_address) {
                                $current_tax_address = Address::initialize($id_address);
                            }
                        }
                    }
                } else {
                    if ($context->cookie->id_customer) {
                        /* There is no cart but a customer is logged in */
                        $id_address = (int) Address::getFirstCustomerAddressId($context->cookie->id_customer);
                        if ($id_address) {
                            /* Take his first address */
                            $current_tax_address = Address::initialize($id_address);
                        }
                    }
                }
            }
            if (!$current_tax_address) {
                // As it is done in Product::getPriceStatic when there is no adress given
                $current_tax_address = Address::initialize(null, true);
            }
        }

        return $current_tax_address;
    }

    private static function getGroupsIds($context)
    {
        if (isset($context->customer) && $context->customer->isLogged()) {
            // Compute groups IDs like in dispatcher hook
            if ((int) $context->customer->id === 0) {
                // Handle it here because it is not in PS1.5
                $groupsIds = [(int) Configuration::get('PS_UNIDENTIFIED_GROUP')];
            } else {
                if (!Group::isFeatureActive()) {
                    $groupsIds = [(int) $context->customer->id_default_group];
                } else {
                    $groupsIds = Customer::getGroupsStatic((int) $context->customer->id);
                }
                // Put the default group at the beginning
                foreach ($groupsIds as $arrayKey => $groupId) {
                    if ($groupId === (int) $context->customer->id_default_group) {
                        $groupsIds[$arrayKey] = $groupsIds[0];
                        $groupsIds[0] = (int) $context->customer->id_default_group;
                    }
                }
            }
        } else {
            $groupsIds = [(int) Configuration::get('PS_UNIDENTIFIED_GROUP')];
        }

        return $groupsIds;
    }

    private static function _getDynamicHookInfos($hookName, $module)
    {
        if (!self::canBeCached()) {
            return false;
        }
        $dyn_hooks = Configuration::get('pagecache_dyn_hooks', '');
        $dyn_hook_part = strstr($dyn_hooks, Tools::strtolower($hookName) . '|' . $module);
        if ($dyn_hook_part !== false) {
            $comma_pos = strpos($dyn_hook_part, ',');
            if ($comma_pos !== false) {
                $dyn_hook_part = Tools::substr($dyn_hook_part, 0, $comma_pos);
            }
            $dyn_hook_part_array = array_pad(explode('|', $dyn_hook_part), 3, 0);
            $dyn_hook_part = ['empty_box' => $dyn_hook_part_array[2]];
        }

        return $dyn_hook_part;
    }

    private static function _getHookCacheDirectives($moduleName, $hookName)
    {
        $directives = ['wrapper' => false, 'content' => true];

        // Remove 'hook' prefix
        $hookName = str_replace('hook', '', $hookName);

        $infos = self::_getDynamicHookInfos($hookName, $moduleName);
        if ($infos !== false) {
            $directives['wrapper'] = true;
            $directives['content'] = !$infos['empty_box'];
        }

        return $directives;
    }

    private static function getDynamicWidgetInfos($moduleName, $hookName)
    {
        if (!self::canBeCached()) {
            return false;
        }
        $dyn_widgets = Configuration::get('pagecache_dyn_widgets', '');
        $dyn_widget_part = strstr($dyn_widgets, Tools::strtolower($moduleName) . '|' . Tools::strtolower($hookName));
        if ($dyn_widget_part === false) {
            // Kept for compatibility reason (before empty box for widget)
            $dyn_widget_part = strstr($dyn_widgets, Tools::strtolower($moduleName) . '|,');
        }
        if ($dyn_widget_part === false) {
            $dyn_widget_part = strstr($dyn_widgets, Tools::strtolower($moduleName) . '||');
        }
        if ($dyn_widget_part !== false) {
            $comma_pos = strpos($dyn_widget_part, ',');
            if ($comma_pos !== false) {
                $dyn_widget_part = Tools::substr($dyn_widget_part, 0, $comma_pos);
            }
            $dyn_widget_part_array = array_pad(explode('|', $dyn_widget_part), 3, 0);
            $dyn_widget_part = ['empty_box' => $dyn_widget_part_array[2]];
        }

        return $dyn_widget_part;
    }

    private static function _getWidgetCacheDirectives($moduleName, $hookName)
    {
        $directives = ['wrapper' => false, 'content' => true];
        $infos = self::getDynamicWidgetInfos($moduleName, $hookName);
        if ($infos !== false) {
            $directives['wrapper'] = true;
            $directives['content'] = !$infos['empty_box'];
        }

        return $directives;
    }

    public static function getManagedControllers()
    {
        if (self::$managed_controllers === null) {
            self::$managed_controllers = json_decode(JprestaUtils::getConfigurationOfCurrentShop('pagecache_managed_ctrl'), true);
            if (!self::$managed_controllers) {
                self::$managed_controllers = self::$managed_controllers_default;
            }
            self::$managed_object_classes = array_flip(
                array_filter(
                    array_map(
                        function ($eltToMap) {
                            return $eltToMap['object_class'];
                        },
                        self::$managed_controllers
                    )
                )
            );
        }

        return self::$managed_controllers;
    }

    /**
     * @return string[]
     */
    public static function getManagedControllersNames()
    {
        return array_keys(self::getManagedControllers());
    }

    private static function getCurrentObjectId()
    {
        $id_object = null;
        $controllerName = self::getControllerName();
        if ($controllerName) {
            // Make sure managed controllers are initialized
            self::getManagedControllers();
            $controllerClassName = isset(self::$managed_controllers[$controllerName]['ctrl_class']) ? self::$managed_controllers[$controllerName]['ctrl_class'] : null;
            if ($controllerClassName && !class_exists($controllerClassName) && JprestaUtils::strpos($controllerName, '__') !== false) {
                // Sometimes the override class is not loaded so we do it here
                list($moduleName, $controllerShortName) = explode('__', $controllerName, 2);
                $overrideFile = _PS_OVERRIDE_DIR_ . 'modules/' . $moduleName . '/controllers/front/' . $controllerShortName . '.php';
                if (file_exists($overrideFile)) {
                    include_once $overrideFile;
                }
            }
            if ($controllerClassName && class_exists($controllerClassName)) {
                $controllerInstance = Controller::getController($controllerClassName);
                if ($controllerInstance && method_exists($controllerInstance, 'getJprestaModelObjectId')) {
                    try {
                        $id_object = $controllerInstance->getJprestaModelObjectId();
                    } catch (Exception $e) {
                        JprestaUtils::addLog("PageCache | An exception occured while executing $controllerClassName::getJprestaModelObjectId(): " . $e->getMessage());
                    }
                }
            } else {
                $id_object = Tools::getValue('id_' . $controllerName);
            }
        }

        return $id_object;
    }

    public static function getManagedControllerId($controllerName)
    {
        // Make sure managed controllers are initialized
        self::getManagedControllers();
        if (array_key_exists($controllerName, self::$managed_controllers)) {
            return self::$managed_controllers[$controllerName]['id'];
        } else {
            return null;
        }
    }

    public static function getManagedControllerNameById($controllerId)
    {
        // Make sure managed controllers are initialized
        self::getManagedControllers();
        foreach (self::$managed_controllers as $controllerName => $controllerInfos) {
            if ($controllerId == $controllerInfos['id']) {
                return $controllerName;
            }
        }

        return 'Unknown controller';
    }

    public static function addManagedControllerName($controllerName, $objectClass, $moduleName, $controllerClassName)
    {
        // Make sure managed controllers are initialized
        self::getManagedControllers();
        if (!in_array($controllerName, self::$managed_controllers)) {
            self::$managed_controllers[$controllerName] = [];
            self::$managed_controllers[$controllerName]['id'] = count(self::$managed_controllers);
            self::$managed_controllers[$controllerName]['object_class'] = $objectClass;
            self::$managed_controllers[$controllerName]['module'] = $moduleName;
            self::$managed_controllers[$controllerName]['ctrl_class'] = $controllerClassName;
            JprestaUtils::saveConfigurationOfCurrentShop('pagecache_managed_ctrl', json_encode(self::$managed_controllers));
        }
    }

    public static function removeManagedControllerName($controllerName)
    {
        // Make sure managed controllers are initialized
        self::getManagedControllers();
        if (array_key_exists($controllerName, self::$managed_controllers)) {
            unset(self::$managed_controllers[$controllerName]);
            JprestaUtils::saveConfigurationOfCurrentShop('pagecache_managed_ctrl', json_encode(self::$managed_controllers));
        }
    }

    public static function canBeCached()
    {
        // static variable avoid computing the canBeCached multiple times
        static $canBeCached = null;
        if ($canBeCached === null) {
            if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'EcommApp') !== false) {
                $canBeCached = false;
                self::$status_reason = 'EcommApp';
            } elseif (JprestaUtils::isAjax()) {
                $canBeCached = false;
                self::$status_reason = 'ajax';
            } elseif (Tools::getIsset('jprestalivetoken')) {
                $canBeCached = false;
                self::$status_reason = 'live-theme-configurator';
            } elseif (Tools::getIsset('adtoken')) {
                $canBeCached = false;
                self::$status_reason = 'preview';
            } elseif (defined('_PS_ADMIN_DIR_')) {
                $canBeCached = false;
                self::$status_reason = 'not-front-controller';
            } elseif (Tools::getIsset('open') && JprestaUtils::isModuleEnabled('gsnippetsreviews')) {
                $canBeCached = false;
                self::$status_reason = 'open-gsnippetsreviews';
            } else {
                if (!Configuration::get('pagecache_debug') && !Configuration::get('pagecache_always_infosbox') && (Tools::getIsset('dbgpagecache') || Tools::getIsset('delpagecache'))) {
                    // Remove module's parameters in production mode to avoid them to be referenced in search engines
                    $url = self::getCurrentURL();
                    $url = preg_replace('/&?dbgpagecache=[0-1]?/', '', $url);
                    $url = preg_replace('/&?delpagecache=[0-1]?/', '', $url);
                    $url = str_replace('?&', '?', $url);
                    $url = preg_replace('/\?$/', '', $url);
                    header('Status: 301 Moved Permanently', false, 301);
                    Tools::redirect($url);
                }

                $canBeCached = self::isGetRequest()
                    && self::isCacheEnabledOrDebugOn()
                    && self::isTokenDisabled()
                    && self::isOverridesEnabled()
                    && !self::isCustomerWithSpecificPricesOrPermissions()
                    && !self::isGoingToBeRedirected()
                    && !self::isRestrictedCountry()
                    && !self::isExcludedByRegex()
                    && !self::isExcludedByReferer()
                    && self::isCacheEnabledForCurrency()
                    && self::isNotLogout()
                    && self::isNotSkipLoggedUsers()
                    && !self::isUsingAffiliateCode()
                    && self::isCacheEnabledForController(self::getControllerName())
                    && !self::isCustomizedProduct(self::getControllerName())
                    && !self::isInEditionWithCreativeElements()
                ;
            }
        }

        return $canBeCached;
    }

    public static function canMinifyHtml()
    {
        return class_exists("voku\helper\HtmlMin");
    }

    /**
     * If the page is generated by a module controller, then check if this controller can be cached
     */
    private static function checkModuleController()
    {
        $controllerFullName = self::getControllerName();
        if (JprestaUtils::getConfigurationAllShop('pagecache_' . $controllerFullName, 'no') === 'no' || Tools::getIsset('initcache')) {
            if (JprestaUtilsModule::isModuleController($controllerFullName)) {
                // Force the update of the values
                self::removeManagedControllerName($controllerFullName);

                if (!JprestaUtilsModule::canBeCached($controllerFullName)) {
                    Configuration::updateValue('pagecache_' . $controllerFullName, 0);
                } else {
                    Configuration::updateValue('pagecache_' . $controllerFullName, 1);
                    Configuration::updateValue('pagecache_' . $controllerFullName . '_timeout', 60 * 24 * 7);
                    Configuration::updateValue('pagecache_' . $controllerFullName . '_expires', 15);
                    self::addManagedControllerName($controllerFullName,
                        JprestaUtilsModule::getModelObjectClassName($controllerFullName),
                        JprestaUtilsModule::getModuleName($controllerFullName),
                        JprestaUtilsModule::getControllerClassName($controllerFullName));
                }
            }
        }
    }

    /**
     * @param $url string URL to check. If not given then the current URL will be checked
     *
     * @return bool true if the URL is excluded from the cache
     */
    public static function isExcludedByRegex($url = null)
    {
        $regex = JprestaUtils::decodeConfiguration(Configuration::get('pagecache_ignore_url_regex'));
        if ($regex) {
            $ret = preg_match('/' . $regex . '/', $url === null ? self::getCurrentURL() : $url);
            if ($ret === 1) {
                self::$status_reason = 'excluded-by-regex';

                return true;
            } elseif ($ret === false) {
                JprestaUtils::addLog('PageCache | Error #' . preg_last_error() . ' in the regex "' . $regex . '"', 2);
            }
        }

        return false;
    }

    /**
     * @return bool true if the URL is excluded because of the referer
     */
    public static function isExcludedByReferer()
    {
        // Get the list of URLs to ignore as a comma-separated string
        $ignoreReferers = JprestaUtils::getConfigurationAllShop('pagecache_ignore_referers');

        // Check if the list is empty
        if (empty($ignoreReferers)) {
            return false; // No URLs to ignore, caching is allowed
        }

        // Split the list into an array of URLs
        $ignoreReferersList = array_map('trim', explode(',', $ignoreReferers));

        // Get the current referer
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

        // Check if the referer matches any of the URLs in the list
        foreach ($ignoreReferersList as $ignoreUrl) {
            if (strpos($referer, $ignoreUrl) !== false) {
                self::$status_reason = 'excluded-by-referer';

                return true; // Match found, caching should be disabled
            }
        }

        return false; // No match found, caching is allowed
    }

    private static function isGetRequest()
    {
        $isGet = strcmp(self::getServerValue('REQUEST_METHOD'), 'GET') == 0;
        if (!$isGet) {
            self::$status_reason = 'not-a-get-request';
        }

        return $isGet;
    }

    private static function isCacheEnabledForController($controller)
    {
        $isEnabled = Configuration::get('pagecache_' . $controller);
        if (!$isEnabled) {
            self::$status_reason = 'disabled-controller: ' . $controller;
        }

        return $isEnabled;
    }

    /**
     * @param $iso_code string ISO code of the currency to check. If not given the current currency will be checked.
     *
     * @return bool true if the currency can be cached
     */
    public static function isCacheEnabledForCurrency($iso_code = null)
    {
        if ($iso_code === null) {
            $context = Context::getContext();
            // Make sure the currency is set
            self::getCurrencyId($context);
            $isEnabled = !$context->currency || array_key_exists($context->currency->iso_code,
                array_flip(explode(',', Configuration::get('pagecache_currencies_to_cache'))));
        } else {
            $isEnabled = array_key_exists($iso_code, array_flip(explode(',', Configuration::get('pagecache_currencies_to_cache'))));
        }
        if (!$isEnabled) {
            self::$status_reason = 'disabled-currency';
        }

        return $isEnabled;
    }

    private static function isCacheEnabledOrDebugOn()
    {
        $isEnabled = !Configuration::get('pagecache_debug') || ((int) Tools::getValue('dbgpagecache', 0) == 1);
        if (!$isEnabled) {
            self::$status_reason = 'test-mode';
        }

        return $isEnabled;
    }

    private static function isTokenDisabled()
    {
        $isDisabled = (int) Configuration::get('PS_TOKEN_ENABLE') != 1;
        if (!$isDisabled) {
            self::$status_reason = 'tokens-enabled';
        }

        return $isDisabled;
    }

    private static function isNotLogout()
    {
        $isLogout = Tools::getIsset('logout') || Tools::getIsset('mylogout') || Tools::getIsset('sso_logout') || Tools::getIsset('opnf_logout');
        if ($isLogout) {
            self::$status_reason = 'logout';
        }
        return !$isLogout;
    }

    private static function isNotSkipLoggedUsers()
    {
        $isNotSkipLoggedUsers = !Configuration::get('pagecache_skiplogged') || !Context::getContext()->customer->isLogged();
        if (!$isNotSkipLoggedUsers) {
            self::$status_reason = 'skip-logged-users';
        }

        return $isNotSkipLoggedUsers;
    }

    /**
     * Customization is not a module and therefore cannot be refreshed. The workaround is to disable
     * cache for these products
     *
     * @param string $controller Controller name
     *
     * @return bool true if current page is a customized product
     */
    private static function isCustomizedProduct($controller)
    {
        if (strcmp($controller, 'product') != 0
            || !Customization::isFeatureActive()
            || Configuration::get('pagecache_cache_customizable')) {
            return false;
        }
        if ($id_product = (int) Tools::getValue('id_product')) {
            $customizationFieldCount = (int) JprestaUtils::dbGetValue('
                SELECT COUNT(*)
                FROM `' . _DB_PREFIX_ . 'customization_field`
                WHERE `id_product` = ' . (int) $id_product);
            if ($customizationFieldCount > 0) {
                self::$status_reason = 'customized-product';

                return true;
            }
        }

        return false;
    }

    /**
     * Do not cache if status code is not 200
     *
     * @return bool true if user will be redirected to an other page or if statuts is not 200
     */
    private static function isGoingToBeRedirected()
    {
        $reason = 'redirect';
        $redirect = self::isNotCode200() || self::isSSLRedirected() || self::isMaintenanceEnabled();
        if (!$redirect && JprestaUtils::isModuleEnabled('autolanguagecurrency') && Configuration::get('AUTOCURRLANG_ENABLED')) {
            if (class_exists('AutoLanguageCurrency') && method_exists('AutoLanguageCurrency', 'needsGeolocate')) {
                $redirect = AutoLanguageCurrency::needsGeolocate();
            } else {
                $context = Context::getContext();
                if (!isset($context->cookie->autolocation) || $context->cookie->autolocation == '0' || !$context->cookie->autolocation) {
                    $redirect = true;
                }
            }
            $reason = 'redirect-autolanguagecurrency';
        }
        if (!$redirect && JprestaUtils::isModuleEnabled('configb2b')) {
            $module = Module::getInstanceByName('configb2b');
            if (method_exists($module, 'isB2b')) {
                if (Dispatcher::getInstance()->getController() == 'index' && $module->isB2b() && Tools::getValue('mylogout') !== '1') {
                    $redirect = true;
                }
                if (Dispatcher::getInstance()->getController() == 'cms') {
                    if (in_array(Tools::getValue('id_cms'), explode(' ', Configuration::get('CONFIGB2B_CMS'))) && $module->isB2b() == false) {
                        $redirect = true;
                    }
                }
            }
            $reason = 'redirect-configb2b';
        }
        if (!$redirect && JprestaUtils::isModuleEnabled('pkamp')) {
            $context = Context::getContext();
            if (!class_exists('Promokit\Module\Pkamp\Classes\Amp') && file_exists(_PS_MODULE_DIR_ . 'pkamp/classes/Amp.php')) {
                // Force the loading of the classes of the module
                include_once _PS_MODULE_DIR_ . 'pkamp/classes/Amp.php';
            }
            if (class_exists('Promokit\Module\Pkamp\Classes\Amp')) {
                $ampClass = new Promokit\Module\Pkamp\Classes\Amp();
                $ampConfig = $ampClass->getAmpConfiguration();

                if ((isset($ampConfig['general_force_amp']) && $ampConfig['general_force_amp']) && $context->isMobile() && !$context->isTablet()) {
                    $redirect = true;
                }
                if (isset($ampConfig['general_force_ontablet']) && @$ampConfig['general_force_ontablet'] && ($context->isTablet() || stristr(@$_SERVER['HTTP_USER_AGENT'], 'iPad'))) {
                    $redirect = true;
                }
                $reason = 'redirect-pkamp';
            }
            else if (class_exists('Promokit\Module\Pkamp\Hooks\DisplayHeader')) {
                // Since version 4 pkamp uses Symphony services but Container used in Module::get are not yet initialized
                // so we just consider that it will redirect all mobile devices
                if ($context->isMobile()) {
                    $redirect = true;
                    $reason = 'redirect-pkamp-v4';
                }
            }
        }
        if ($redirect) {
            self::$status_reason = $reason;
        }

        return $redirect;
    }

    private static function isNotCode200()
    {
        if (function_exists('http_response_code') && !defined('HHVM_VERSION')) {
            $code = http_response_code();
            if (!empty($code)) {
                if (http_response_code() !== 200) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function isSSLRedirected()
    {
        return Configuration::get('PS_SSL_ENABLED') && self::getServerValue('REQUEST_METHOD') != 'POST' && Configuration::get('PS_SSL_ENABLED_EVERYWHERE') && !Tools::usingSecureMode();
    }

    public static function isMaintenanceEnabled()
    {
        $isMaintenanceEnabled = false;
        if (!(int) Configuration::get('PS_SHOP_ENABLE')) {
            $isMaintenanceEnabled = true;

            // Check if admins can display the site and if current user is an admin
            $maintenance_allow_admins = (bool) Configuration::get('PS_MAINTENANCE_ALLOW_ADMINS');
            $is_admin = (int) (new Cookie('psAdmin'))->id_employee;
            if ($is_admin && $maintenance_allow_admins) {
                $isMaintenanceEnabled = false;
            }

            // Check if current IP is allowed to display the site
            $allowed_ips = array_map('trim', explode(',', Configuration::get('PS_MAINTENANCE_IP')));
            if (class_exists('IpUtils')
                && method_exists('IpUtils', 'checkIp')
                && IpUtils::checkIp(Tools::getRemoteAddr(), $allowed_ips)) {
                $isMaintenanceEnabled = false;
            } elseif (in_array(Tools::getRemoteAddr(), $allowed_ips)) {
                $isMaintenanceEnabled = false;
            }
        }

        return $isMaintenanceEnabled;
    }

    /**
     * Must be called after geolocalisation has been done
     *
     * @return bool true if the visitor is located in a restricted country
     */
    private static function isRestrictedCountry()
    {
        $restrictedCountry = false;
        $controller_instance = self::getControllerInstance();
        if ($controller_instance !== false && method_exists($controller_instance, 'isRestrictedCountry')) {
            $restrictedCountry = $controller_instance->isRestrictedCountry();
            if ($restrictedCountry) {
                self::$status_reason = 'restricted-country';
            }
        }

        return $restrictedCountry;
    }

    /**
     * Cache must be disabled if a customer has a specific price, discount, permissions, etc.
     */
    private static function isCustomerWithSpecificPricesOrPermissions()
    {
        $context = Context::getContext();
        $id_customer = $context->customer ? $context->customer->id : 0;
        if ($id_customer > 0) {
            $now = date('Y-m-d H:i:00');
            $count_existing = 'SELECT count(*) FROM `' . _DB_PREFIX_ . 'specific_price` WHERE id_customer=' . (int) $id_customer .
                ' AND (`from` = \'0000-00-00 00:00:00\' OR \'' . pSQL($now) . '\' >= `from`)' .
                ' AND (`to` = \'0000-00-00 00:00:00\' OR \'' . pSQL($now) . '\' <= `to`)'
            ;
            if ((int) JprestaUtils::dbGetValue($count_existing) > 0) {
                // Current customer has specific prices for him so cache must be disabled
                self::$status_reason = 'customer-specific-prices';

                return true;
            }
        }
        // Compatibility with superuser module by MassonVincent
        if (JprestaUtils::isModuleEnabled('superuser')) {
            $ips = Configuration::get('superuser_ips');
            $ip = explode(',', $ips);
            if (!defined('_PS_ADMIN_DIR_')
                && (in_array('*', $ip) || in_array(Tools::getRemoteAddr(), $ip) && strstr($_SERVER['REQUEST_URI'], 'mentions-legales'))
            ) {
                // Disable cache so Super User module can work
                self::$status_reason = 'superuser';

                return true;
            }
        }
        // Compatibility with atssuperuser module by ATSinfosystem Sotwares
        if (Tools::getIsset('superuser') && JprestaUtils::isModuleEnabled('atssuperuser')) {
            self::$status_reason = 'atssuperuser';

            return true;
        }
        // Compatibility with wkadminloginascustomer module by Webkul
        if (Tools::getIsset('id_customer') && JprestaUtils::isModuleEnabled('wkadminloginascustomer')) {
            self::$status_reason = 'wkadminloginascustomer';

            return true;
        }
        // Compatibility with groupinc module by Idnovate
        if (JprestaUtils::isModuleEnabled('groupinc')) {
            $count_rules_with_customers = 'SELECT count(*) FROM `' . _DB_PREFIX_ . 'groupinc_configuration` WHERE customers <> \'\'';
            if ((int) JprestaUtils::dbGetValue($count_rules_with_customers) > 0) {
                // Some rules are specific to some customers
                $count_rules_with_this_customer = 'SELECT count(*) FROM `' . _DB_PREFIX_ . 'groupinc_configuration` WHERE customers = \'' . (int) $id_customer . '\' OR customers like \'' . (int) $id_customer . ';%\' or customers like \'%;' . (int) $id_customer . ';%\' or customers like \'%;' . (int) $id_customer . '\'';
                if ((int) JprestaUtils::dbGetValue($count_rules_with_this_customer) > 0) {
                    self::$status_reason = 'groupinc';

                    return true;
                }
            }
        }
        // Compatibility with shaim_gdpr module by Dominik Shaim
        if (JprestaUtils::isModuleEnabled('shaim_gdpr')) {
            if ((int) Configuration::get('shaim_gdpr_zpetny_souhlas_active') == 1 && $id_customer > 0) {
                $active = Db::getInstance()->executeS('SELECT `shaim_gdpr_active` FROM `' . _DB_PREFIX_ . 'customer` WHERE `id_customer` = ' . (int) $id_customer . ';');
                $active = (isset($active[0]['shaim_gdpr_active']) ? (int) $active[0]['shaim_gdpr_active'] : 0);
                if (!$active) {
                    self::$status_reason = 'shaim_gdpr';

                    return true;
                }
            }
        }
    }

    /**
     * Cache must be disabled if the visitor is using an affiliate code ()
     */
    private static function isUsingAffiliateCode()
    {
        if ((Tools::getIsset('affp') || Context::getContext()->cookie->__get('eam_aff_customer_cookie')) && JprestaUtils::isModuleEnabled('ets_affiliatemarketing')) {
            self::$status_reason = 'using-affiliate-code';

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private static function isInEditionWithCreativeElements()
    {
        if (JprestaUtils::isModuleEnabled('creativeelements') && Configuration::get('elementor_frontend_edit')) {
            $lifetime = max((int) Configuration::get('PS_COOKIE_LIFETIME_BO'), 1);
            $cookie = new Cookie('psAdmin', '', time() + $lifetime * 3600);
            $id_employee = isset($cookie->id_employee) ? (int) $cookie->id_employee : 0;
            if ($id_employee) {
                if ((bool) glob(_PS_ROOT_DIR_ . '/*/filemanager', GLOB_ONLYDIR)) {
                    self::$status_reason = 'editing-with-creative-element';

                    return true;
                }
            }
        }

        return false;
    }

    public static function isCacheWarmer()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'JPresta-Cache-Warmer') === 0;
    }

    public static function isStatusChecker()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'JPresta-Cache-Warmer/Status-Checker') === 0;
    }

    private static function setCacheWarmerContext()
    {
        if (Configuration::get('pagecache_debug')) {
            exit('Module is in test mode, warmup ignored.');
        }

        $context = Context::getContext();

        // Disable redirect from autolanguagecurrency module
        $context->cookie->autolocation = 1;

        // Currency
        $currencyIsoCode = JprestaUtils::getRequestHeaderValue('jpresta-currency');
        if ($currencyIsoCode) {
            $id_currency = Currency::getIdByIsoCode($currencyIsoCode);
            if ($id_currency) {
                $context->cookie->id_currency = $id_currency;
            } else {
                exit('Currency ' . $currencyIsoCode . ' not found, currency is not available anymore, warmup ignored.');
            }
        }

        // Country
        $countryIsoCode = JprestaUtils::getRequestHeaderValue('jpresta-country');
        if ($countryIsoCode) {
            if (!Validate::isLanguageIsoCode($countryIsoCode)) {
                exit('Country ' . $countryIsoCode . ' not found, warmup ignored.');
            }
            $id_country = Country::getByIso($countryIsoCode);
            if ($id_country) {
                if (Configuration::get('PS_GEOLOCATION_ENABLED')) {
                    // Disable geolocalization
                    $_SERVER['REMOTE_ADDR'] = 'localhost';
                }
                // In case PS detects country from browser language
                $_SERVER['HTTP_ACCEPT_LANGUAGE'] = Tools::strtolower($countryIsoCode);
                // Set country
                $context->cookie->iso_code_country = $countryIsoCode;
                $context->country = new Country($id_country);
            } else {
                exit('Country ' . $countryIsoCode . ' not found, country is not available anymore, warmup ignored.');
            }
        }

        // Device: handled with a different User-Agent by the cache-warmer

        // Customer groups
        $fakeUserEmail = JprestaUtils::getRequestHeaderValue('jpresta-group');
        if ($fakeUserEmail) {
            $customer = new Customer();
            if ($customer->getByEmail($fakeUserEmail)) {
                if (!JprestaCustomer::isVisitor($customer->id)) {
                    // Set a JprestaCustomer object so the isLogged() method returns true
                    $context->customer = JprestaCustomer::getOrCreateCustomerWithSameGroups($customer, true);
                    $context->cookie->id_customer = $customer->id;
                }
            } else {
                exit('User-group ' . $fakeUserEmail . ' not found, fake user has probably been deleted, warmup ignored.');
            }
        }

        // Taxes: handled in hookTaxManager()

        // Specifics
        $id_specifics = JprestaUtils::getRequestHeaderValue('jpresta-id-specifics');
        if ($id_specifics) {
            $specifics = PageCacheDAO::getDetailsById($id_specifics);
            if ($specifics) {
                $jcks = new JprestaCacheKeySpecifics($specifics);
                self::restoreJprestaCacheKeySpecifics($jcks);
            } else {
                exit('No specific context found, cache has probably been reset shortly, warmup ignored.');
            }
        }
    }

    /**
     * @param $params array {'address' => address of the customer, 'params' => id_tax_rules_group / type}
     *
     * @return TaxManagerInterface|false
     */
    public function hookActionTaxManager($params)
    {
        static $tax_manager = [];
        $id_tax_rules_group = $params['params'];
        if ($id_tax_rules_group && !array_key_exists($id_tax_rules_group, $tax_manager)) {
            $tax_manager[$id_tax_rules_group] = false;
            if (self::isCacheWarmer()) {
                $id_tax_manager = JprestaUtils::getRequestHeaderValue('jpresta-id-tax-manager');
                if ($id_tax_manager) {
                    $taxManagerJson = PageCacheDAO::getDetailsById($id_tax_manager);
                    if ($taxManagerJson) {
                        try {
                            $tax_manager[$id_tax_rules_group] = new JprestaUtilsTaxManager($taxManagerJson, $id_tax_rules_group);
                        } catch (Exception $e) {
                            exit('Cannot build the tax manager from context, cache has probably been reset shortly, warmup ignored (Error: ' . $e->getMessage() . ')');
                        }
                    } else {
                        exit('No tax manager context found, cache has probably been reset shortly, warmup ignored.');
                    }
                }
            }
        }
        if (!$id_tax_rules_group) {
            return false;
        }

        return $tax_manager[$id_tax_rules_group];
    }

    public static function isOverridesEnabled()
    {
        $isOverridesEnabled = JprestaUtils::version_compare(_PS_VERSION_, '1.6', '<') || ((int) Configuration::get('PS_DISABLE_OVERRIDES') != 1);
        if (!$isOverridesEnabled) {
            self::$status_reason = 'overrides-disabled';
        }

        return $isOverridesEnabled;
    }

    /**
     * return true if it is available, false otherwise
     */
    public static function displayCacheIfExists()
    {
        $cache = false;
        $can_be_cached = self::canBeCached();
        if ($can_be_cached) {
            // Before checking cache, lets check cache refreshment triggers (specific prices)
            PageCacheDAO::triggerReffreshment();

            $controller = self::getControllerName();
            $cache_ttl = 60 * ((int) Configuration::get('pagecache_' . $controller . '_timeout'));
            $jprestaCacheKey = self::getCacheKeyInfos();

            if (Tools::getIsset('delpagecache') && self::isDisplayStats()) {
                self::$disableBrowserCache = true;
                self::getCache()->delete($jprestaCacheKey->get('url'), PageCacheDAO::getContextKeyById(PageCacheDAO::getOrCreateContextId($jprestaCacheKey)));
            }

            $cache = self::getCache()->get($jprestaCacheKey->get('url'), PageCacheDAO::getContextKeyById(PageCacheDAO::getOrCreateContextId($jprestaCacheKey)), $cache_ttl);

            if (self::isCacheWarmer()) {
                // Remember that this context is used by the cache-warmer
                PageCacheDAO::setContextUsedByCacheWarmer(PageCacheDAO::getOrCreateContextId($jprestaCacheKey));
            }

            $cache_age = 0;
            if ($cache !== false) {
                $stats = PageCacheDAO::getStats($jprestaCacheKey);
                $cache_age = $stats['age'];
                if (!$cache_age) {
                    // This should not happen BUT it happens sometimes, when the file still exists but is not in
                    // database anymore. In this case it is not up-to-date so we delete it
                    self::getCache()->delete($jprestaCacheKey->get('url'), PageCacheDAO::getContextKeyById(PageCacheDAO::getOrCreateContextId($jprestaCacheKey)));
                    $cache = false;
                }
            }

            if ($cache !== false && self::isCacheWarmer()) {
                // Force cache re-generation if the request is done by the cache warmer and TTL is less than 1 day
                $ttl = PageCacheDAO::getTtl($jprestaCacheKey, $cache_ttl / 60);
                if ($cache_ttl >= 0 && $ttl < (24 * 60) && !headers_sent()) {
                    header(self::HTTP_HEADER_CACHE_INFO . ': status=on, reason=cache-warmer-regenerate, age=0');

                    return false;
                }
                // Cache already warmed up
                // Reduce the size of the response to the minimum (save bandwidth and time)
                if (!headers_sent()) {
                    // Here our cache-warmer/status-checker do not care about these headers, just remove them
                    header_remove();
                    // Indicates that there is no content so it removes "Content-Length" and "Content-Type" headers
                    header('HTTP/1.1 204 CACHE EXISTS');
                    header('x-jpresta-exists: 1');
                    // Unset PHP session (to avoid a useless cookie)
                    session_abort();
                    // Don't send any cookies
                    Context::getContext()->cookie->disallowWriting();
                }
                exit;
            }

            if (JprestaUtils::isSearchEngine()) {
                PageCacheDAO::incrementCountBot($jprestaCacheKey);
            }

            // Store cache used in a readable cookie (0=no cache; 1=server cache; 2=browser cache, 3=static cache, 4=back/forward cache)
            $cache_type = 0; // no cache available
            if ($cache !== false) {
                // Server cache
                $cache_type = 1;
            }
            if (!headers_sent()) {
                header('Server-Timing: jpresta_cache;desc=' . $cache_type);
                header('Timing-Allow-Origin: *');
                if (Tools::getIsset('dbgpagecache') || Tools::getIsset('delpagecache')) {
                    // Don't want the debug URL to be indexed
                    header('X-Robots-Tag: noindex');
                }
            }

            // Set up the browser cache directives
            if (!$cache && session_status() == PHP_SESSION_NONE && !headers_sent() && !self::isCacheWarmer() && !self::isStatusChecker()) {
                // We do it here otherwise the cache directives will be removed
                session_start();
            }
            $offset = 60 * Configuration::get('pagecache_' . $controller . '_expires', 0);
            if ($offset > 0) {
                if (headers_sent()) {
                    JprestaUtils::addLog('PageCache | Cannot use browser cache because headers have already been sent', 3);
                } elseif (!self::$disableBrowserCache && !PageCacheDAO::hasTriggerIn2H()) {
                    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT');
                    header('Cache-Control: max-age=' . $offset . ', private');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
                    header_remove('Pragma');
                } else {
                    // Browser cache is disabled, force the browser to not use it (specially for back/forward cache)
                    header('Expires: Wed, 19 Oct 1977 18:00:00 GMT');
                    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
                }
            } else {
                // Browser cache is disabled, force the browser to not use it (specially for back/forward cache)
                header('Expires: Wed, 19 Oct 1977 18:00:00 GMT');
                header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            }

            // Display the cached HTML if any
            if ($cache !== false) {
                if (!headers_sent()) {
                    header(self::HTTP_HEADER_CACHE_INFO . ': status=on, reason=' . self::$status_reason . ', age=' . $cache_age);
                }

                self::sendContextCookie();

                // Compatibility with module softizyconditionalcache (Ticket #421)
                $softizyconditionalcache = Module::getInstanceByName('softizyconditionalcache');
                if ($softizyconditionalcache && method_exists($softizyconditionalcache, 'hookActionDispatcherAfter')) {
                    $softizyconditionalcache->hookActionDispatcherAfter();
                }

                echo $cache;

                return true;
            } else {
                if (!headers_sent()) {
                    header(self::HTTP_HEADER_CACHE_INFO . ': status=on, reason=' . self::$status_reason . ', age=0');
                }
            }
        }
        if (!headers_sent()) {
            if ((Tools::substr(self::getCurrentURL(), -JprestaUtils::strlen('hicookielaw/cookie')) === 'hicookielaw/cookie')
                || (Tools::substr(self::getCurrentURL(), -JprestaUtils::strlen('idxcookies/ajax')) === 'idxcookies/ajax')
            ) {
                // Delete the context
                setcookie('jpresta_cache_context', '', time() - 3600, '/');
            }
        }
        if (!$can_be_cached && !headers_sent()) {
            header('Server-Timing: jpresta_cache;desc=-1');
            header('Timing-Allow-Origin: *');
            if (Tools::getIsset('dbgpagecache') || Tools::getIsset('delpagecache')) {
                // Don't want the debug URL to be indexed
                header('X-Robots-Tag: noindex');
            }
            header(self::HTTP_HEADER_CACHE_INFO . ': status=off, reason=' . self::$status_reason);
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        }

        return $cache;
    }

    /**
     * Generates a key for the cache depending on URL, currency, user group, country, etc.
     * Return array[0]=hashed key (int), array[1]=cache key infos (array)
     *
     * @return JprestaCacheKey
     */
    public static function getCacheKeyInfos($refresh = false)
    {
        /**
         * @var JprestaCacheKey
         */
        static $current_cache_key_infos = false;
        if ($current_cache_key_infos === false || $refresh) {
            $context = Context::getContext();
            $cacheKey = new JprestaCacheKey();

            //
            // SHOP
            //
            $cacheKey->add('id_shop', $context->shop->id);

            //
            // URL
            //

            // Normalize the URL
            $normalized_url = self::normalizeUrl(self::getCurrentURL());

            // Remove HTML anchor
            $anchorPos = strpos($normalized_url, '#');
            if ($anchorPos !== false) {
                $normalized_url = Tools::substr($normalized_url, 0, $anchorPos);
            }

            // Strip ignored parameters (tracking data that do not change page content)
            // and sort them
            $ignored_params = explode(',', Configuration::get('pagecache_ignored_params'));
            $ignored_params[] = 'delpagecache';
            $ignored_params[] = 'dbgpagecache';
            $ignored_params[] = 'cfgpagecache';
            $query_string = parse_url($normalized_url, PHP_URL_QUERY);
            $new_query_string = self::filterAndSortParams($query_string, $ignored_params);
            if ($new_query_string) {
                $normalized_url = http_build_url($normalized_url, ['query' => $new_query_string]);
            } else {
                $normalized_url = http_build_url($normalized_url, [], HTTP_URL_STRIP_QUERY);
            }
            $cacheKey->add('url', $normalized_url);

            //
            // CURRENCY
            //
            $cacheKey->add('id_currency', self::getCurrencyId($context));

            //
            // LANGUAGE
            //
            $cacheKey->add('id_lang', $context->language->id);

            //
            // CUSTOMER GROUP
            //
            $anonymousCustomer = JprestaCustomer::getOrCreateCustomerWithSameGroups($context->customer);
            $cacheKey->add('id_fake_customer', $anonymousCustomer ? $anonymousCustomer->id : null);

            //
            // DEVICE (computer, mobile, tablet)
            //
            if (self::isDependsOnDevice()) {
                if (method_exists($context, 'getDevice')) {
                    if ($context->getDevice() === Context::DEVICE_MOBILE || (Configuration::get('pagecache_tablet_is_mobile') && $context->getDevice() === Context::DEVICE_TABLET)) {
                        $cacheKey->add('id_device', self::DEVICE_MOBILE);
                    } else {
                        $cacheKey->add('id_device', self::DEVICE_COMPUTER);
                    }
                } elseif ($context->getMobileDevice() == true) {
                    $cacheKey->add('id_device', self::DEVICE_MOBILE);
                } else {
                    $cacheKey->add('id_device', self::DEVICE_COMPUTER);
                }
            } else {
                $cacheKey->add('id_device', self::DEVICE_COMPUTER);
            }

            //
            // COUNTRY
            //
            $country = self::getCountry($context);
            if ($country) {
                $currentCacheKeyCountryConf = json_decode(JprestaUtils::getConfigurationByShopId('pagecache_cachekey_countries', Shop::getContextShopID(), '{}'), true);
                if (!is_array($currentCacheKeyCountryConf)) {
                    JprestaUtils::addLog('Bad configuration value for pagecache_cachekey_countries for shop#' . Shop::getContextShopID() . ': ' . JprestaUtils::getConfigurationByShopId('pagecache_cachekey_countries', Shop::getContextShopID(), '{}'), 2);
                    $currentCacheKeyCountryConf = [];
                }
                if (array_key_exists($country->id, $currentCacheKeyCountryConf)
                    && $currentCacheKeyCountryConf[$country->id]['specific_cache']) {
                    // Only create a specific cache if it is configured like that
                    $cacheKey->add('id_country', $country->id);
                } else {
                    // Otherwise set the country key as 'other' (null)
                    $cacheKey->add('id_country', null);
                }
            } else {
                // Normally we should not be here because getCountry() will return the default country
                $cacheKey->add('id_country', null);
            }

            //
            // TAXES MANAGER
            //
            $tax_manager_details = self::getTaxManagerDetails($context);
            if ($tax_manager_details) {
                $cacheKey->add('id_tax_manager', $tax_manager_details);
            }

            //
            // RGPD and other specific determinants
            //
            $cacheKey->add('id_specifics', PageCacheDAO::getOrCreateDetailsId(self::getJprestaCacheKeySpecifics()));

            //
            // Other determinants
            //

            // Version of CSS and JS to avoid cache to reference old CSS and JS files
            if (Configuration::get('pagecache_depend_on_css_js')) {
                $cacheKey->add('css_version', Configuration::get('PS_CCCCSS_VERSION'));
                $cacheKey->add('js_version', Configuration::get('PS_CCCJS_VERSION'));
            }

            $current_cache_key_infos = $cacheKey;
        }

        return $current_cache_key_infos;
    }

    /**
     * @param $url string URL of the backlink
     *
     * @return int Cache key as an unsigned integer
     */
    public static function getCacheKeyForBacklink($url)
    {
        // We supposed that URLs into our shop are well formatted

        // Remove HTML anchor
        $anchorPos = strpos($url, '#');
        if ($anchorPos !== false) {
            $url = Tools::substr($url, 0, $anchorPos);
        }

        // Remove protocol as it is done in JprestaUtils::parseLinks for backlinks
        $url = preg_replace('/https?:\/\//', '//', $url);

        $jprestaCacheKey = new JprestaCacheKey();
        $jprestaCacheKey->add('url', $url);

        return $jprestaCacheKey->toInt();
    }

    private static function normalizeUrl($url)
    {
        $normalized_url = html_entity_decode($url);
        $un = new PageCacheURLNormalizer();
        $un->setUrl($normalized_url);
        $normalized_url = $un->normalize();

        return $normalized_url;
    }

    private static function getCookieValue($cookieName, $defaultValue = '')
    {
        if (array_key_exists($cookieName, $_COOKIE)) {
            // Necessary to avoid errors in Prestashop Addons validator
            foreach ($_COOKIE as $key => $cookieValue) {
                if ($key === $cookieName) {
                    return $cookieValue;
                }
            }
        }

        return $defaultValue;
    }

    /**
     * @param $specifics JprestaCacheKeySpecifics
     */
    private static function restoreJprestaCacheKeySpecifics($specifics)
    {
        // Restore cookies and sessions datas
        $specifics->restoreCookies();

        // Now restore other specifics behavior for specific modules
        $context = Context::getContext();

        // For gdprpro (2.1.1) module by PrestaChamps
        if (JprestaUtils::isModuleEnabled('gdprpro')) {
            if (file_exists(_PS_MODULE_DIR_ . 'gdprpro/src/GdprProConfig.php')
                && file_exists(_PS_MODULE_DIR_ . 'gdprpro/src/GdprProCookie.php')) {
                require_once _PS_MODULE_DIR_ . 'gdprpro/src/GdprProConfig.php';
                require_once _PS_MODULE_DIR_ . 'gdprpro/src/GdprProCookie.php';
                if (class_exists('GdprProCookie') && method_exists('GdprProCookie', 'getInstance')) {
                    // The cookie is read before any hook in getHookModuleExecList() so we need to read it again
                    GdprProCookie::getInstance()->content = json_decode($context->cookie->gdpr_conf, true);
                }
            }
        }

        // Handle vat_view (for shop cinelight.eu)
        if ($context->customer && property_exists($context->customer, 'vat_view')) {
            $context->customer->vat_view = $specifics->getValue('vat_view');
        }

        if (JprestaUtils::isModuleEnabled('lgcookieslaw')) {
            if (Configuration::hasKey('PS_LGCOOKIES_COOKIE_NAME', 0, (int) Shop::getContextShopID(true), (int) Shop::getContextShopGroupID(true))) {
                // lgcookieslaw v2
                $lgModule = Module::getInstanceByName('lgcookieslaw');
                if ($lgModule) {
                    if ($specifics->getValue('lgcookieslaw') == 'access not granted') {
                        // Simulate Googlebot to force lgcookieslaw to block the access
                        $_SERVER['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] . ' - Googlebot';
                    } else {
                        $lgcookieslaw_cookie_name = Configuration::get('PS_LGCOOKIES_COOKIE_NAME');
                        $lgcookieslaw_cookie_value = $specifics->getValue($lgcookieslaw_cookie_name);
                        if (method_exists($lgModule, 'encryptCookie')) {
                            $lgcookieslaw_cookie_values = $lgModule->encryptCookie($lgcookieslaw_cookie_value);
                        } else {
                            $lgcookieslaw_cookie_values = base64_encode(json_encode($lgcookieslaw_cookie_value));
                        }
                        if (Configuration::get('PS_LGCOOKIES_USE_COOKIE_VAR')) {
                            $_COOKIE[$lgcookieslaw_cookie_name] = $lgcookieslaw_cookie_values;
                        } else {
                            $context->cookie->__set($lgcookieslaw_cookie_name, $lgcookieslaw_cookie_values);
                        }
                    }
                }
            } else {
                // lgcookieslaw v1
                $lgcookieslaw_cookie_value = $specifics->getValue('lgcookieslaw_bots');
                if ($lgcookieslaw_cookie_value) {
                    // Simulate Googlebot to force lgcookieslaw to block the access
                    $_SERVER['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] . ' - Googlebot';
                }
            }
        }

        // For iubenda module by iubenda
        if (Module::isEnabled('iubenda')) {
            $purposes = $specifics->getValue('iubenda_purposes');
            if ($purposes) {
                $_COOKIE['_iub_cs-s-cache-warmer'] = json_encode(['purposes' => json_decode($purposes, true)]);
            }
        }

        //
        // WEBP
        //
        $acceptWebp = true;

        // For webpgenerator module by PrestaChamps
        if (JprestaUtils::isModuleEnabled('webpgenerator')) {
            $acceptWebp = $specifics->getValue('webpgenerator') === 'acceptwebp' || (bool) $specifics->getValue('webp');
        }

        // For jprestaspeedpack and jprestawebp modules by JPresta
        if ((JprestaUtils::isModuleEnabled('jprestaspeedpack') || JprestaUtils::isModuleEnabled('jprestawebp'))
            && (int) Configuration::get('SPEED_PACK_WEBP_ENABLE')
            && (int) Configuration::get('SPEED_PACK_WEBP_FORCE_EXTENSION')) {
            $acceptWebp = (bool) $specifics->getValue('webp');
        }

        // For ultimateimagetool module by advancedplugins
        if (JprestaUtils::isModuleEnabled('ultimateimagetool')
            && (int) Configuration::get('uit_use_webp') == 1
            && (int) Configuration::get('uit_use_webp_termination') == 1) {
            $acceptWebp = (bool) $specifics->getValue('webp');
        }

        if ($acceptWebp) {
            $_SERVER['HTTP_ACCEPT'] = (isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '') . ',image/webp';
        } else {
            $_SERVER['HTTP_ACCEPT'] = str_replace('image/webp', 'image/jpg', isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '');
        }

        if ($specifics->getValue('display_applepay')) {
            // Simulate an Apple device
            $_SERVER['HTTP_USER_AGENT'] .= ' Version/10 Safari/x';
        }

        // Restore context handled by modules
        $hookParams = $specifics->getValue('hooks');
        if ($hookParams) {
            foreach ($hookParams as $moduleName => $hookParam) {
                Hook::exec('actionJPrestaRestoreSpecificCacheKeyInfos', ['specifics' => $hookParam], Module::getModuleIdByName($moduleName));
            }
        }
    }

    /**
     * @return JprestaCacheKeySpecifics|null
     */
    private static function getJprestaCacheKeySpecifics()
    {
        $specifics = new JprestaCacheKeySpecifics();
        $context = Context::getContext();

        // For gdprpro (2.1.1) module by PrestaChamps
        if (JprestaUtils::isModuleEnabled('gdprpro')) {
            $specifics->keepPsCookie('gdpr_conf');
            $specifics->keepCookie('gdpr_windows_was_opened');
        }

        // For generaldataprotectionregulation (2.0.11) module by Active Design
        if (JprestaUtils::isModuleEnabled('generaldataprotectionregulation')) {
            $specifics->keepCookie('Accepted');
            $specifics->keepCookie('cookiesDenied');
            $specifics->keepCookie('cookiesAccepted');
        }

        // For ageverify module by Musaffar Patel
        if (JprestaUtils::isModuleEnabled('ageverify')) {
            // session based (each visit) check
            if (Configuration::get('av_display_frequency') == 'each_visit') {
                $specifics->keepOtherPsCookie('age_verify_session');
            } else {
                $specifics->keepPsCookie('age_verify');
            }
        }

        // For ageverifyer module by Simon Agostini
        if (JprestaUtils::isModuleEnabled('ageverifyer')) {
            // To uncomment if needed (forbidden by Prestashop Addons)
            // session_start();
            $specifics->keepSessionProperty('over18');
        }

        // For kbgdpr module by Knowband
        if (JprestaUtils::isModuleEnabled('kbgdpr')) {
            $cookie_law_settings = json_decode(Configuration::get('GDPR_COOKIE_LAW_SETTINGS'), true);
            $specifics->keepCookie($cookie_law_settings['cookie_name']);
        }

        // For uecookie module by MyPresta.eu
        if (JprestaUtils::isModuleEnabled('uecookie')) {
            $specifics->keepCookie('cookie_ue');
        }

        // For idxcookies module by Idnovate
        if (JprestaUtils::isModuleEnabled('idxcookies')) {
            $specifics->keepCookie('idxcookiesWarningCheck');
            if (isset($specifics->cookies['idxcookiesWarningCheck'])) {
                $cookieValue = json_decode($specifics->cookies['idxcookiesWarningCheck'], true);
                $cookieValue['date'] = '2000-01-01 00:00:00';
                $specifics->cookies['idxcookiesWarningCheck'] = json_encode($cookieValue);
            }
            $specifics->keepPsCookie('idxcookiesWarningCheck');
            if (isset($specifics->psCookies['idxcookiesWarningCheck'])) {
                $cookieValue = json_decode($specifics->psCookies['idxcookiesWarningCheck'], true);
                $cookieValue['date'] = '2000-01-01 00:00:00';
                $specifics->psCookies['idxcookiesWarningCheck'] = json_encode($cookieValue);
            }
        }

        // For ec_cookies module by 4EC.EU
        if (JprestaUtils::isModuleEnabled('ec_cookies')) {
            $specifics->keepCookie('ec_cookie_agreement');
            if (isset($specifics->cookies['ec_cookie_agreement'])) {
                $cookieValue = json_decode($specifics->cookies['ec_cookie_agreement']);
                if (isset($cookieValue[0]->timestamp)) {
                    // Set a fix value
                    $cookieValue[0]->timestamp = 1725460957;
                    $specifics->cookies['ec_cookie_agreement'] = json_encode(array_values($cookieValue));
                }
            }
        }

        // For validatevatnumber module by ActiveDesign
        if (JprestaUtils::isModuleEnabled('validatevatnumber')) {
            $specifics->keepPsCookie('guest_taxes');
        }

        // For megacookies module by presta.design
        if (JprestaUtils::isModuleEnabled('megacookies')) {
            $var = Context::getContext()->cookie->megacookie_consents;
            if (!$var) {
                // Avoid duplicate contexts
                Context::getContext()->cookie->megacookie_consents = 'denied';
            }
            $specifics->keepPsCookie('megacookie_consents');
        }

        // For deluxecookies module by innovadeluxe
        if (JprestaUtils::isModuleEnabled('deluxecookies')) {
            $specifics->keepCookie('deluxecookies');
            $specifics->keepCookie('deluxecookiesWarningCheck');
            if (isset($specifics->cookies['deluxecookiesWarningCheck'])) {
                $cookieValue = json_decode($specifics->cookies['deluxecookiesWarningCheck'], true);
            } else {
                $cookieValue = [];
            }
            $cookieValue['date'] = '2000-01-01 00:00:00';
            $specifics->cookies['deluxecookiesWarningCheck'] = json_encode($cookieValue);
        }

        // For tnzcookie (1.6.6) module by Tanzo
        if (JprestaUtils::isModuleEnabled('tnzcookie')) {
            $specifics->keepOtherPsCookie('TNZCOOKIE_COOKIE');
        }

        // For lgcookieslaw module by Línea Gráfica
        if (JprestaUtils::isModuleEnabled('lgcookieslaw')) {
            if (Configuration::hasKey('PS_LGCOOKIES_COOKIE_NAME', 0, (int) Shop::getContextShopID(true), (int) Shop::getContextShopGroupID(true))) {
                // lgcookieslaw v2
                $lgModule = Module::getInstanceByName('lgcookieslaw');
                if ($lgModule) {
                    if (!self::isCacheWarmer() && !$lgModule->checkAccessGranted($context)) {
                        //$specifics->keepValue('lgcookieslaw', 'access not granted');
                        // Reduce the number of context by considering bots as normal visitors
                        //$specifics->keepValue('lgcookieslaw', []);
                    } else {
                        $lgcookieslaw_cookie_name = Configuration::get('PS_LGCOOKIES_COOKIE_NAME');
                        if (method_exists($lgModule, 'getCookieValues')) {
                            $lgcookieslaw_cookie_values = (array) $lgModule->getCookieValues();
                        } else {
                            $lgcookieslaw_cookie_value = $context->cookie->__get($lgcookieslaw_cookie_name);
                            if (JprestaUtils::startsWith($lgcookieslaw_cookie_value, '{')) {
                                $lgcookieslaw_cookie_values = json_decode($context->cookie->__get($lgcookieslaw_cookie_name),
                                    true);
                            } else {
                                $lgcookieslaw_cookie_values = json_decode(base64_decode($context->cookie->__get($lgcookieslaw_cookie_name)),
                                    true);
                            }
                        }
                        if (is_array($lgcookieslaw_cookie_values) && count($lgcookieslaw_cookie_values) > 0) {
                            // Override other values not usefull for the cache key
                            if (isset($lgcookieslaw_cookie_values['lgcookieslaw_user_consent_consent_date'])) {
                                $lgcookieslaw_cookie_values['lgcookieslaw_user_consent_consent_date'] = '2000-01-01 00:00:00';
                            }
                            if (isset($lgcookieslaw_cookie_values['lgcookieslaw_user_consent_ip_address'])) {
                                $lgcookieslaw_cookie_values['lgcookieslaw_user_consent_ip_address'] = '::1';
                            }
                            if (isset($lgcookieslaw_cookie_values['lgcookieslaw_user_consent_download_url'])) {
                                $lgcookieslaw_cookie_values['lgcookieslaw_user_consent_download_url'] = 'https://foo.com';
                            }
                            if (isset($lgcookieslaw_cookie_values['download_hash'])) {
                                $lgcookieslaw_cookie_values['download_hash'] = 'foo';
                            }
                            if (isset($lgcookieslaw_cookie_values['lgcookieslaw_user_consent_download_hash'])) {
                                $lgcookieslaw_cookie_values['lgcookieslaw_user_consent_download_hash'] = 'foo';
                            }
                            // Do not preserve the value if it is empty to reduce the number of context
                            $specifics->keepValue($lgcookieslaw_cookie_name, $lgcookieslaw_cookie_values);
                        } else {
                            // Disable browser cache until the visitor select cookies that he want
                            self::$disableBrowserCache = true;
                        }
                        // Preserve this cookie
                        self::$cookies_to_preserve[$lgcookieslaw_cookie_name] = $lgcookieslaw_cookie_name;
                    }
                }
            } else {
                // lgcookieslaw v1
                $specifics->keepCookie(Configuration::get('PS_LGCOOKIES_NAME'));
                if (isset($_SERVER['HTTP_USER_AGENT'])) {
                    $bots = Configuration::get('PS_LGCOOKIES_BOTS');
                    $botlist = explode(',', $bots);
                    foreach ($botlist as $bot) {
                        if ($bot && strpos($_SERVER['HTTP_USER_AGENT'], $bot) !== false) {
                            $specifics->keepValue('lgcookieslaw_bots', 'Bot detected');
                        }
                    }
                }
                if (Configuration::get('PS_LGCOOKIES_TESTMODE') == 1 && Configuration::get('PS_LGCOOKIES_IPTESTMODE') == $_SERVER['REMOTE_ADDR']) {
                    $specifics->keepValue('lgcookieslaw_mode', 'Test mode');
                }
            }
        }

        // For cookiesplus by idnovate
        if (JprestaUtils::isModuleEnabled('cookiesplus')) {
            $cookiesplus = Module::getInstanceByName('cookiesplus');
            $cookieName = 'cookiesplus';
            $consentDefaults = [
                'consent_date' => '2000-01-01 00:00:00',
                'consent_ip' => '::1',
                'consent_hash' => 'foo',
                'consent_link' => 'foo',
                'cookiesplus-finality-1' => false,
                'cookiesplus-finality-2' => 'na',
                'cookiesplus-finality-3' => 'na',
                'cookiesplus-finality-4' => 'na',
                'C_P_DISPLAY_MODAL' => false, // Does not matter as it is dynamic
                'expiry' => 1924988399, // 2030-12-31 23:59:59
            ];

            if (JprestaUtils::version_compare($cookiesplus->version, '1.3', '>=')) {
                $cookieSource = (JprestaUtils::version_compare($cookiesplus->version, '1.5.2', '>=') || Configuration::get('C_P_COOKIE'))
                    ? 'cookies'
                    : 'psCookies';

                $cookieSource == 'cookies' ? $specifics->keepCookie($cookieName) : $specifics->keepPsCookie($cookieName);

                if (isset($specifics->{$cookieSource}[$cookieName])) {
                    $cookieValue = json_decode($specifics->{$cookieSource}[$cookieName], true);
                    $cookieValue = array_merge($cookieValue, $consentDefaults);

                    $atLeastOneTrue = false;
                    foreach ($cookieValue as $key => $value) {
                        if (strpos($key, 'cookiesplus-finality') === 0
                            && $key !== 'cookiesplus-finality-1' // required (technical)
                            && ($value === true || $value == 'on')) {
                            $atLeastOneTrue = true;
                            break;
                        }
                    }

                    if (!$atLeastOneTrue && isset($cookieValue['consents'])) {
                        foreach ($cookieValue['consents'] as $key => $value) {
                            if (strpos($key, 'cookiesplus-finality') === 0
                                && $key !== 'cookiesplus-finality-1' // required (technical)
                                && ($value === true || $value == 'on')) {
                                $atLeastOneTrue = true;
                                break;
                            }
                        }
                    }

                    if ($atLeastOneTrue) {
                        $specifics->{$cookieSource}[$cookieName] = json_encode($cookieValue);
                    } else {
                        unset($specifics->{$cookieSource}[$cookieName]);
                    }
                }
            } else {
                if (Configuration::get('C_P_ENABLE')
                    && (!isset($context->cookie->psnotice) || $context->cookie->psnotice != '2')
                    && (!isset($_SERVER['HTTP_USER_AGENT']) || !preg_match('/' . Configuration::get('C_P_BOTS') . '/i', $_SERVER['HTTP_USER_AGENT']))
                    && !in_array(Tools::getRemoteAddr(), explode('|', Configuration::get('C_P_IPS')))) {
                    $specifics->keepPsCookie('psnotice');
                    $specifics->keepValue($cookieName, 'withoutcookie');
                } else {
                    $specifics->keepValue($cookieName, 'withcookie');
                }
            }
        }

        // For medcookiefirst by Mediacom87
        if (JprestaUtils::isModuleEnabled('medcookiefirst')) {
            $specifics->keepCookie('cookiefirst-consent');
            if (isset($specifics->cookies['cookiefirst-consent'])) {
                $cookieValue = json_decode($specifics->cookies['cookiefirst-consent'], true);
                $cookieValue['timestamp'] = 1704067200;
                $specifics->cookies['cookiefirst-consent'] = json_encode($cookieValue);
            }
        }

        // For systemina_employeefilter module by Systemina (support@systemina.dk)
        if (JprestaUtils::isModuleEnabled('systemina_employeefilter')) {
            $cookie = new Cookie('psAdmin', '', (int) Configuration::get('PS_COOKIE_LIFETIME_BO'));
            $employee = new Employee((int) $cookie->id_employee);

            if (Validate::isLoadedObject($employee) && $employee->checkPassword((int) $cookie->id_employee, $cookie->passwd)
                && (!isset($cookie->remote_addr) || $cookie->remote_addr == ip2long(Tools::getRemoteAddr()) || !Configuration::get('PS_COOKIE_CHECKIP'))) {
                $specifics->keepValue('systemina_employeefilter', 'EmployeeLoggedin');
            } else {
                $specifics->keepValue('systemina_employeefilter', 'EmployeeNotLoggedin');
            }
        }

        // For pm_advancedcookiebanner module by Presta-Module
        if (JprestaUtils::isModuleEnabled('pm_advancedcookiebanner') && class_exists('AcbCookie') && method_exists('AcbCookie', 'getConsentLevel')) {
            $pmCookieContent = self::getCookieValue(AcbCookie::COOKIE_NAME, false);
            $pmConfigMode = Configuration::get('PM_ACB_CONFIG_MODE');
            $pmCmsPage = Tools::getIsset('acb_cms') || (Tools::getIsset('id_cms') && Tools::getValue('id_cms') == Configuration::get('PM_ACB_CMS'));
            $pmGdprMode = Configuration::get('PM_ACB_GDPR_MODE');

            $specifics->keepCookie(AcbCookie::COOKIE_NAME);
            if ($pmGdprMode == 1) {
                $specifics->keepValue('pm_advancedcookiebanner_gdpr', 1);
            }
            if (!$pmConfigMode) {
                if ($pmCookieContent === false && !$pmCmsPage) {
                    $specifics->keepValue('pm_advancedcookiebanner_mode', 1);
                } else {
                    $specifics->keepValue('pm_advancedcookiebanner_mode', 0);
                }
            } else {
                $maintenance_ips = explode(',', Configuration::get('PS_MAINTENANCE_IP'));
                if (in_array(Tools::getRemoteAddr(), $maintenance_ips)) {
                    $specifics->keepValue('pm_advancedcookiebanner_mode', 1);
                } else {
                    $specifics->keepValue('pm_advancedcookiebanner_mode', 0);
                }
            }
        }

        // For deluxecookies module by innovadeluxe
        if (JprestaUtils::isModuleEnabled('deluxecookies')) {
            $module = Module::getInstanceByName('deluxecookies');
            $specifics->keepValue('deluxecookies_disabled_mods', $module->getDisabledModules());
            if (self::getCookieValue('deluxecookiesWarningCheck', false)) {
                $specifics->keepValue('deluxecookies_dialog', 1);
            }
        }

        // For cookiebanner module by Prestaddons
        if (JprestaUtils::isModuleEnabled('cookiebanner')) {
            $specifics->keepPsCookie('disabled_modules_list');
        }

        // For webpgenerator module by PrestaChamps
        if (JprestaUtils::isModuleEnabled('webpgenerator') && JprestaUtils::currentVisitorAcceptWebp()) {
            $specifics->keepValue('webp', 1);
        }

        // For jprestaspeedpack and jprestawebp modules by JPresta
        if ((JprestaUtils::isModuleEnabled('jprestaspeedpack') || JprestaUtils::isModuleEnabled('jprestawebp'))
            && (int) Configuration::get('SPEED_PACK_WEBP_ENABLE')
            && (int) Configuration::get('SPEED_PACK_WEBP_FORCE_EXTENSION')
            && JprestaUtils::currentVisitorAcceptWebp()) {
            $specifics->keepValue('webp', 1);
        }

        // For ultimateimagetool module by advancedplugins
        if (JprestaUtils::isModuleEnabled('ultimateimagetool')
            && (int) Configuration::get('uit_use_webp') == 1
            && (int) Configuration::get('uit_use_webp_termination') == 1
            && JprestaUtils::currentVisitorAcceptWebp()) {
            $specifics->keepValue('webp', 1);
        }

        // For hicookielaw module by hipresta
        if (JprestaUtils::isModuleEnabled('hicookielaw')) {
            /** @var Hicookielaw $module */
            $module = Module::getInstanceByName('hicookielaw');
            if (method_exists($module, 'isIPWhiteListed') && $module->isIPWhiteListed()
                || method_exists($module, 'isBot') && $module->isBot()) {
                $context->cookie->hiThirdPartyCookies = 1;
            }
            if (isset($context->cookie->hiThirdPartyCookies)) {
                // Force the same type to reduce the context count
                $context->cookie->hiThirdPartyCookies = (int) $context->cookie->hiThirdPartyCookies;
            }
            $specifics->keepPsCookieByPrefix('hiThirdPartyCookies_', '0');
            $specifics->keepPsCookie('hiThirdPartyCookies');
        }

        // For cm_cookielaw by caleydon.com
        if (JprestaUtils::isModuleEnabled('cm_cookielaw')) {
            if (isset($context->cookie->cmcookie)) {
                // Force the same type to reduce the context count
                $context->cookie->cmcookie = (int) $context->cookie->cmcookie;
            }
            $specifics->keepPsCookie('cmcookie', 1);
            if (isset($context->cookie->hiThirdPartyCookies)) {
                // Force the same type to reduce the context count
                $context->cookie->hiThirdPartyCookies = (int) $context->cookie->hiThirdPartyCookies;
            }
            $specifics->keepPsCookie('hiThirdPartyCookies', 0);
            if (isset($context->cookie->hiMarketPartyCookies)) {
                // Force the same type to reduce the context count
                $context->cookie->hiMarketPartyCookies = (int) $context->cookie->hiMarketPartyCookies;
            }
            $specifics->keepPsCookie('hiMarketPartyCookies', 0);
        }

        // Handle vat_view (for shop cinelight.eu)
        if ($context->customer && property_exists($context->customer, 'vat_view')) {
            $specifics->keepValue('vat_view', $context->customer->vat_view);
        }

        // For iubenda module by iubenda
        if (Module::isEnabled('iubenda')) {
            $purposes = [];
            if (!empty($_COOKIE)) {
                foreach ($_COOKIE as $key => $value) {
                    if (JprestaUtils::startsWith($key, '_iub_cs-s') || JprestaUtils::startsWith($key, '_iub_cs')) {
                        $consent_data = json_decode($value, true);
                        // read purposes if given
                        if (!empty($consent_data['purposes']) && is_array($consent_data['purposes'])) {
                            $purposes = $consent_data['purposes'];
                            $specifics->keepValue('iubenda_purposes', json_encode($purposes));
                        }
                    }
                }
            }
        }

        // For dm_cookies module by David Mrózek
        if (JprestaUtils::isModuleEnabled('dm_cookies')) {
            $specifics->keepCookie('DmCookiesAnalytics');
            $specifics->keepCookie('DmCookiesMarketing');
            $specifics->keepCookie('DmCookiesAccepted');
        }

        // For groupinc module by Idnovate
        if (JprestaUtils::isModuleEnabled('groupinc') && file_exists(_PS_MODULE_DIR_ . 'groupinc/classes/GroupincConfiguration.php')) {
            include_once _PS_MODULE_DIR_ . 'groupinc/classes/GroupincConfiguration.php';
            if (method_exists('GroupincConfiguration', 'isShowableBySchedule')) {
                $today = date('Y-m-d H:i:s');
                $datefilters = ' AND (date_from <= "' . $today . '" OR date_from = "0000-00-00 00:00:00") AND (date_to >= "' . $today . '" OR date_to = "0000-00-00 00:00:00")';
                $orderby = ' ORDER BY `priority`, `id_groupinc_configuration` ASC';
                $groupincConfigs = JprestaUtils::dbSelectRows('SELECT * FROM `' . _DB_PREFIX_ . 'groupinc_configuration` WHERE `active`=1 AND `id_shop`=' . (int) $context->shop->id . $datefilters . $orderby);
                $ids = '';
                foreach ($groupincConfigs as $groupincConfig) {
                    if (GroupincConfiguration::isShowableBySchedule($groupincConfig)) {
                        $ids .= $groupincConfig['id_groupinc_configuration'] . ' (' . $groupincConfig['date_upd'] . ')';
                    }
                }
                // Just store the md5 or it may be larger than 65535 chars, also we don't restore it, only add it to the cache key
                $md5 = md5($ids);
                $specifics->keepValue('groupinc.configIds', $md5);
                $lastMd5 = JprestaUtils::getConfigurationOfCurrentShop('pagecache_groupinc_last');
                if ($lastMd5 === false || $lastMd5 !== $md5) {
                    $count = 0;
                    $contexts = PageCacheDAO::getAllContextsAndDetails((int) $context->shop->id);
                    foreach ($contexts as $id_ctx => $details) {
                        if (JprestaUtils::strpos($details, 'groupinc.configIds') !== false
                            && JprestaUtils::strpos($details, $md5) === false) {
                            // This context is now obsolete
                            PageCacheDAO::disableContext($id_ctx);
                            ++$count;
                        }
                    }
                    if ($count > 0) {
                        JprestaUtils::addLog("Disabled $count contexts because configuration of GroupInc changed ($lastMd5 => $md5)", 1);
                    }
                    JprestaUtils::saveConfigurationOfCurrentShop('pagecache_groupinc_last', $md5);
                } else {
                    $count = 0;
                    $contexts = PageCacheDAO::getAllContextsAndDetails((int) $context->shop->id, false);
                    foreach ($contexts as $id_ctx => $details) {
                        if (JprestaUtils::strpos($details, 'groupinc.configIds') !== false
                            && JprestaUtils::strpos($details, $md5) !== false) {
                            // This context is back in the business
                            PageCacheDAO::enableContext($id_ctx);
                            ++$count;
                        }
                    }
                    if ($count > 0) {
                        JprestaUtils::addLog("Enabled $count contexts back because configuration of GroupInc changed ($lastMd5 => $md5)", 1);
                    }
                }
            }
        }

        // For nxtalpricetaxswitcher by Nxtal
        if (JprestaUtils::isModuleEnabled('nxtalpricetaxswitcher')) {
            $defaultDisplay = (int) Group::getPriceDisplayMethod((int) Group::getCurrent()->id, true);
            $cookieDisplay = self::getCookieValue('nxtalpricetaxswitcher', false);
            if ($cookieDisplay !== false && $defaultDisplay != $cookieDisplay) {
                $specifics->keepCookie('nxtalpricetaxswitcher');
            }
        }

        // For pm_applepay module by Presta-Module
        if (JprestaUtils::isModuleEnabled('pm_applepay')) {
            $module = Module::getInstanceByName('pm_applepay');
            if ($module && method_exists($module, 'isPaymentAvailable')) {
                $specifics->keepValue('display_applepay', (int) $module->isPaymentAvailable());
            } else {
                $specifics->keepValue('display_applepay', 0);
            }
        }

        // For eugeneraldatapro module by PrestaBucket
        if (JprestaUtils::isModuleEnabled('eugeneraldatapro')) {
            $specifics->keepCookie('EUGDPRmodulesAccepted');
            $specifics->keepCookie('EUGDPRmodulesDenied');
        }

        // For wkexitpopup module by Webkul
        if (JprestaUtils::isModuleEnabled('wkexitpopup')) {
            $specifics->keepCookie('do_not_show_again');
        }

        // For iqitthemeeditor module by IQIT-COMMERCE.COM
        if (JprestaUtils::isModuleEnabled('iqitthemeeditor')) {
            $specifics->keepPsCookie('product_list_view', 'grid');
        }

        // Allow modules to handle their specifics contextual datas
        $hookParams = Hook::exec('actionJPrestaGetSpecificCacheKeyInfos', [], null, true);
        if ($hookParams && count($hookParams) > 0) {
            $specifics->keepValue('hooks', $hookParams);
        }

        return $specifics->isEmpty() ? null : $specifics;
    }

    public static function updateCacheKeyForCountries()
    {
        $allShopIds = JprestaUtils::getCompleteListOfShopsID();
        foreach ($allShopIds as $shopId) {
            $currentConf = json_decode(JprestaUtils::getConfigurationByShopId('pagecache_cachekey_countries', $shopId, '{}'), true);
            if (!is_array($currentConf)) {
                JprestaUtils::addLog("Bad configuration value for pagecache_cachekey_countries for shop#$shopId: " . JprestaUtils::getConfigurationByShopId('pagecache_cachekey_countries', $shopId, '{}'), 2);
                $currentConf = [];
            }
            $checkedCountries = [];

            $countryRows = JprestaUtils::dbSelectRows('SELECT c.id_country
                FROM `' . _DB_PREFIX_ . 'country` c
                LEFT JOIN `' . _DB_PREFIX_ . 'country_shop` cs ON (cs.`id_country`= c.`id_country`)
                WHERE c.active=1 AND id_shop=' . (int) $shopId);
            foreach ($countryRows as $countryRow) {
                if (!array_key_exists((int) $countryRow['id_country'], $currentConf)) {
                    $currentConf[$countryRow['id_country']] = ['specific_cache' => false, 'has_impact' => false];
                }
                $currentConf[$countryRow['id_country']]['has_impact'] = self::hasImpactForCountries($countryRow['id_country'], $shopId);
                if ($currentConf[$countryRow['id_country']]['has_impact']) {
                    // Force a specific cache to be created if any constraint exists
                    $currentConf[$countryRow['id_country']]['specific_cache'] = true;
                }
                $checkedCountries[$countryRow['id_country']] = true;
            }
            // Remove old countries from the configuration
            foreach ($currentConf as $id_country => $val) {
                if (!array_key_exists($id_country, $checkedCountries)) {
                    unset($currentConf[$id_country]);
                }
            }
            JprestaUtils::saveConfigurationByShopId('pagecache_cachekey_countries', json_encode($currentConf), $shopId);
        }
    }

    public static function getCacheKeyForUserGroups($shopId)
    {
        static $cache = [];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($cache[$shopId])) {
            return $cache[$shopId];
        }

        $encodedConf = JprestaUtils::getConfigurationByShopId('pagecache_cachekey_usergroups', $shopId, '');
        if (!$encodedConf) {
            return $cache[$shopId] = [];
        }

        $compressedConf = @base64_decode($encodedConf, true);
        if ($compressedConf === false) {
            JprestaUtils::addLog("Bad Base64 configuration value for pagecache_cachekey_usergroups for shop#$shopId: " . $encodedConf, 2);
            return $cache[$shopId] = [];
        }

        $decompressedConf = @gzuncompress($compressedConf);
        if ($decompressedConf === false) {
            JprestaUtils::addLog("Bad compressed configuration value for pagecache_cachekey_usergroups for shop#$shopId", 2);
            return $cache[$shopId] = [];
        }

        $currentConf = @json_decode($decompressedConf, true);
        if (!is_array($currentConf)) {
            JprestaUtils::addLog("Bad JSON configuration value for pagecache_cachekey_usergroups for shop#$shopId", 2);
            return $cache[$shopId] = [];
        }

        return $cache[$shopId] = $currentConf;
    }

    public static function saveCacheKeyForUserGroups($shopId, $conf)
    {
        $jsonConf = json_encode($conf);
        if ($jsonConf === false) {
            JprestaUtils::addLog("Failed to JSON encode configuration for pagecache_cachekey_usergroups for shop#$shopId", 2);
            return false;
        }

        // Compress the serialized data using GZIP because sometimes it is very huge
        $compressedConf = gzcompress($jsonConf, 9);
        if ($compressedConf === false) {
            JprestaUtils::addLog("Failed to compress configuration for pagecache_cachekey_usergroups for shop#$shopId", 2);
            return false;
        }

        // Encode the compressed data into a Base64 string for UTF-8 compatibility
        $encodedConf = base64_encode($compressedConf);

        // Save the encoded configuration in the database
        JprestaUtils::saveConfigurationByShopId('pagecache_cachekey_usergroups', $encodedConf, $shopId);

        return true;
    }

    public static function updateCacheKeyForUserGroups()
    {
        $allShopIds = JprestaUtils::getCompleteListOfShopsID();
        foreach ($allShopIds as $shopId) {
            $currentConf = self::getCacheKeyForUserGroups($shopId);
            $checkedUserGroups = [];

            $userGroupRows = JprestaUtils::dbSelectRows('SELECT *
                FROM `' . _DB_PREFIX_ . 'group` g
                LEFT JOIN `' . _DB_PREFIX_ . 'group_shop` gs ON (gs.`id_group`= g.`id_group`)
                WHERE gs.id_shop=' . (int) $shopId);
            foreach ($userGroupRows as $userGroupRow) {
                if (!array_key_exists((int) $userGroupRow['id_group'], $currentConf)) {
                    $currentConf[$userGroupRow['id_group']] = ['specific_cache' => false, 'has_impact_as_default' => false];
                }
                $currentConf[$userGroupRow['id_group']]['has_impact_as_default'] = self::hasImpactForUserGroupAsDefault($userGroupRow['id_group'], $shopId);

                // The display_key will be used to find similar user group when has_impact_as_default=false
                $currentConf[$userGroupRow['id_group']]['display_key'] = $userGroupRow['price_display_method'] . '|' . $userGroupRow['show_prices'];
                $currentConf[$userGroupRow['id_group']]['display_key'] .= '|' . JprestaUtils::dbGetValue('SELECT MD5(GROUP_CONCAT(id_module SEPARATOR \'|\')) FROM `' . _DB_PREFIX_ . 'module` WHERE id_module NOT IN (SELECT id_module FROM `' . _DB_PREFIX_ . 'module_group` WHERE id_group=' . (int) $userGroupRow['id_group'] . ') ORDER BY id_module ASC');
                $currentConf[$userGroupRow['id_group']]['display_key'] .= '|' . JprestaUtils::dbGetValue('SELECT MD5(GROUP_CONCAT(id_category SEPARATOR \'|\')) FROM `' . _DB_PREFIX_ . 'category` WHERE id_parent<> 0 AND id_category NOT IN (SELECT id_category FROM `' . _DB_PREFIX_ . 'category_group` WHERE id_group=' . (int) $userGroupRow['id_group'] . ') ORDER BY id_category ASC');
                if ((int) $userGroupRow['id_group'] === (int) Configuration::get('PS_UNIDENTIFIED_GROUP')
                    || (int) $userGroupRow['id_group'] === (int) Configuration::get('PS_GUEST_GROUP')) {
                    $currentConf[$userGroupRow['id_group']]['display_key'] .= '|not_connected';
                } else {
                    $currentConf[$userGroupRow['id_group']]['display_key'] .= '|connected';
                }

                if ($currentConf[$userGroupRow['id_group']]['has_impact_as_default']) {
                    // Force a specific cache to be created if any constraint exists
                    $currentConf[$userGroupRow['id_group']]['specific_cache'] = true;
                }
                $checkedUserGroups[$userGroupRow['id_group']] = true;
            }
            // Remove old user groups from the configuration
            foreach ($currentConf as $id_group => $val) {
                if (!array_key_exists($id_group, $checkedUserGroups)) {
                    unset($currentConf[$id_group]);
                }
            }
            self::saveCacheKeyForUserGroups($shopId, $currentConf);
        }
        JprestaUtils::saveConfigurationAllShop('pagecache_cachekey_usergroups_upd', false);
    }

    /**
     * @param int $id_country
     * @param int $id_shop
     *
     * @return bool true if this country has impact on the specified shop
     */
    private static function hasImpactForCountries($id_country, $id_shop)
    {
        $andShopIdClause = '';
        if (Shop::isFeatureActive()) {
            $andShopIdClause = ' AND id_shop=' . (int) $id_shop;
        }
        // Price rules for catalog
        $count = (int) JprestaUtils::dbGetValue('SELECT 1
                FROM `' . _DB_PREFIX_ . 'specific_price_rule`
                WHERE id_country=' . (int) $id_country . $andShopIdClause . ' AND (`to` IS NULL OR `to` > CURRENT_TIMESTAMP)');
        // Price rules for a specific product
        // TODO Test with specific price on shop group
        if ($count === 0) {
            $count += (int) JprestaUtils::dbGetValue('SELECT 1
                FROM `' . _DB_PREFIX_ . 'specific_price`
                WHERE id_country=' . (int) $id_country . $andShopIdClause . ' AND (`to` IS NULL OR `to` > CURRENT_TIMESTAMP)');
        }

        // Cart rules => cart rules do not change the price which is displayed so they do not change the cache content, they can be ignored
        return $count > 0;
    }

    /**
     * @param int $id_group
     * @param int $id_shop
     *
     * @return bool true if this user group has an impact on the specified shop
     */
    private static function hasImpactForUserGroupAsDefault($id_group, $id_shop)
    {
        $count = 0;
        $andShopIdClause = '';
        if (Shop::isFeatureActive()) {
            $andShopIdClause = ' AND id_shop=' . (int) $id_shop;
        }

        // Discount for the group
        $count = (int) JprestaUtils::dbGetValue('SELECT 1
                FROM `' . _DB_PREFIX_ . 'group` g
                LEFT JOIN `' . _DB_PREFIX_ . 'group_shop` gs ON (gs.`id_group`= g.`id_group`)
				WHERE g.reduction>0.0 AND gs.id_shop=' . (int) $id_shop . ' AND g.id_group=' . (int) $id_group);
        // Discount for the group on categories
        if ($count === 0) {
            $count += (int) JprestaUtils::dbGetValue('SELECT 1
                FROM `' . _DB_PREFIX_ . 'group_reduction`
                WHERE id_group=' . (int) $id_group);
        }
        // Price rules for catalog
        if ($count === 0) {
            $count += (int) JprestaUtils::dbGetValue('SELECT 1
                FROM `' . _DB_PREFIX_ . 'specific_price_rule`
                WHERE id_group=' . (int) $id_group . $andShopIdClause . ' AND (`to` IS NULL OR `to` > CURRENT_TIMESTAMP)');
        }
        // Price rules for a specific product
        // TODO Test with specific price on shop group
        if ($count === 0) {
            $count += (int) JprestaUtils::dbGetValue('SELECT 1
                FROM `' . _DB_PREFIX_ . 'specific_price`
                WHERE id_group=' . (int) $id_group . $andShopIdClause . ' AND (`to` IS NULL OR `to` > CURRENT_TIMESTAMP)');
        }

        // Cart rules => cart rules do not change the price which is displayed so they do not change the cache content, they can be ignored
        return $count > 0;
    }

    public function hookActionObjectSpecificPriceRuleAddAfter()
    {
        if (!self::$skipUpdateCacheKey) {
            self::updateCacheKeyForCountries();
            self::updateCacheKeyForUserGroups();
        } else {
            self::$needUpdateCacheKey = true;
        }
    }

    public function hookActionObjectSpecificPriceRuleUpdateAfter()
    {
        if (!self::$skipUpdateCacheKey) {
            self::updateCacheKeyForCountries();
            self::updateCacheKeyForUserGroups();
        } else {
            self::$needUpdateCacheKey = true;
        }
    }

    public function hookActionObjectSpecificPriceRuleDeleteAfter()
    {
        if (!self::$skipUpdateCacheKey) {
            self::updateCacheKeyForCountries();
            self::updateCacheKeyForUserGroups();
        } else {
            self::$needUpdateCacheKey = true;
        }
    }

    public function hookActionObjectGroupAddAfter()
    {
        // Some datas are set after the hook is called :-(
        JprestaUtils::saveConfigurationAllShop('pagecache_cachekey_usergroups_upd', true);
    }

    public function hookActionObjectGroupUpdateAfter()
    {
        // Some datas are set after the hook is called :-(
        JprestaUtils::saveConfigurationAllShop('pagecache_cachekey_usergroups_upd', true);
    }

    public function hookActionObjectGroupDeleteAfter()
    {
        // Some datas are set after the hook is called :-(
        JprestaUtils::saveConfigurationAllShop('pagecache_cachekey_usergroups_upd', true);
    }

    public function hookActionObjectSpecificPriceDeleteAfter()
    {
        if (!self::$skipUpdateCacheKey) {
            self::updateCacheKeyForCountries();
            self::updateCacheKeyForUserGroups();
        } else {
            self::$needUpdateCacheKey = true;
        }
    }

    /**
     * Execute all module hook/widget/widget_block for the dynamic ajax request
     */
    public static function execDynamicHooks($controllerInstance = false)
    {
        $result = [];

        if (Tools::getIsset('cache_source')) {
            $cache_source = (int) Tools::getValue('cache_source');
            $ttfb = (int) Tools::getValue('ttfb', -1);

            PageCacheDAO::incrementCountHitMissed(self::getCacheKeyInfos(), $cache_source);

            if (Configuration::get('pagecache_statsttfb') && $ttfb >= 0) {
                if ($cache_source === 2 || $cache_source === 4 || $ttfb > 10) {
                    // Googlebots and maybe others provide suspicious performance timing so we ignore them when
                    // TTFB is less than 10ms when it is not a browser side cache
                    PageCacheDAO::addStatsPerf(Shop::getContextShopID(), self::getControllerName(), $ttfb, $cache_source);
                }
            }
        }

        if (Tools::getIsset('stats') || JprestaUtils::isSearchEngine()) {
            // A short hand to return faster when only doing stats
            return $result;
        }

        // Execute header hook to get JS definitions
        if ($controllerInstance && $controllerInstance instanceof ProductListingFrontController && Configuration::get('pagecache_exec_header_hook')) {
            Tools::setCookieLanguage(Context::getContext()->cookie);
            Hook::exec('displayHeader');
        }
        // Execute header hook to get JS definitions
        Hook::exec('actionFrontControllerSetMedia', []);

        if (isset($_SERVER['HTTP_X_JPRESTA_REFERER'])) {
            // Replace by the original referer so statistics are corrects
            $_SERVER['HTTP_REFERER'] = $_SERVER['HTTP_X_JPRESTA_REFERER'];
        }

        $index = 0;
        do {
            $val = Tools::getValue('hk_' . $index);
            if ($val !== false) {
                // Make it safe
                $val = htmlentities($val);

                list($hookId, $hookType, $id_module, $hook_name, $hook_args) = explode('|', $val);
                $moduleInstance = JprestaUtils::getModuleInstanceById($id_module);
                if ($moduleInstance) {
                    try {
                        // Initialize parameters from ids if any (product, category, etc.)
                        $args = [];
                        if (!empty($hook_args)) {
                            $argvalues = explode('^', $hook_args);
                            if (is_array($argvalues)) {
                                foreach ($argvalues as $argvalue) {
                                    if (strpos($argvalue, '=') !== false) {
                                        list($arg, $value) = explode('=', $argvalue);
                                        if (strcmp('pc_ipa', $arg) === 0) {
                                            $args['product'] = (array) new Product((int) $value);
                                            $args['product']['id_product'] = $value;
                                            $args['product']['quantity'] = Product::getQuantity(
                                                (int) $value,
                                                0,
                                                isset($args['product']['cache_is_pack']) ? $args['product']['cache_is_pack'] : null,
                                                Context::getContext()->cart
                                            );
                                            if (!array_key_exists('id_product_attribute', $args['product']) || $args['product']['id_product_attribute'] === null) {
                                                $args['product']['id_product_attribute'] = Product::getDefaultAttribute($value);
                                            }
                                            $args['product']['quantity_all_versions'] = $args['product']['quantity'];
                                        } elseif (strcmp('pc_ipl', $arg) === 0) {
                                            // This is called multiple time for the same product so we cache it
                                            static $lazyProducts = [];
                                            if (isset($lazyProducts[(int) $value])) {
                                                $args['product'] = $lazyProducts[(int) $value];
                                            } else {
                                                $factoryProduct = new ProductPresenterFactory(Context::getContext(),
                                                    new TaxConfiguration());
                                                $product = (new ProductAssembler(Context::getContext()))
                                                    ->assembleProduct(['id_product' => (int) $value]);
                                                $args['product'] = $factoryProduct->getPresenter()->present(
                                                    $factoryProduct->getPresentationSettings(),
                                                    $product,
                                                    Context::getContext()->language
                                                );
                                                $lazyProducts[(int) $value] = $args['product'];
                                            }
                                        } elseif (strcmp('pc_ip', $arg) === 0) {
                                            $args['product'] = new Product((int) $value);
                                            if (method_exists($args['product'], 'loadStockData')) {
                                                $args['product']->loadStockData();
                                            }
                                        } elseif (strcmp('pc_ica', $arg) === 0) {
                                            $args['category'] = (array) new Category((int) $value);
                                            $args['category']['id_category'] = $value;
                                        } elseif (strcmp('pc_ic', $arg) === 0) {
                                            $args['category'] = new Category((int) $value);
                                        } else {
                                            $args[$arg] = urldecode($value);
                                        }
                                    }
                                }
                            }
                        }

                        if (strpos(self::HOOK_TYPE_MODULE, $hookType) === 0) {
                            // Display a module hook
                            $id_hook = Hook::getIdByName(str_replace('hook', '', $hook_name));
                            if (method_exists('Hook', 'getNameById')) {
                                // Hook::getNameById from PS1.5.5.0
                                $hook_name = Hook::getNameById($id_hook);
                            } else {
                                $hook_name = JprestaUtils::dbGetValue('SELECT `name` FROM `' . _DB_PREFIX_ . 'hook` WHERE `id_hook` = ' . (int) $id_hook);
                            }
                            $array_return = in_array(Tools::strtolower($hook_name), ['displayproductextracontent']);
                            $content = Hook::exec($hook_name, $args, (int) $id_module, $array_return);
                            if (is_string($content)) {
                                $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
                            }
                            $result[$hookId] = $content;
                        } elseif (strpos(self::HOOK_TYPE_WIDGET, $hookType) === 0) {
                            // Display a widget tag
                            $content = $moduleInstance->renderWidget($hook_name, $args);
                            if (is_string($content)) {
                                $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
                            }
                            $result[$hookId] = $content;
                        } elseif (strpos(self::HOOK_TYPE_WIDGET_BLOCK, $hookType) === 0) {
                            // Display a widget_block tag
                            $blockKey = $hook_name;
                            $tpl = self::getWidgetBlockTemplate($blockKey);
                            $scopedVariables = $moduleInstance->getWidgetVariables(null, $args);
                            $smarty = Context::getContext()->smarty;
                            foreach ($scopedVariables as $key => $value) {
                                $smarty->assign($key, $value);
                            }
                            $content = $moduleInstance->fetch($tpl);
                            if (is_string($content)) {
                                $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
                            }
                            $result[$hookId] = $content;
                        }
                        if (is_array($result[$hookId])
                            && array_key_exists($moduleInstance->name, $result[$hookId])
                            && is_array($result[$hookId][$moduleInstance->name])
                            && array_key_exists('pec_idx', $args)
                            && array_key_exists($args['pec_idx'], $result[$hookId][$moduleInstance->name])
                            && $result[$hookId][$moduleInstance->name][$args['pec_idx']] instanceof PrestaShop\PrestaShop\Core\Product\ProductExtraContent) {
                            // Handle the hookDisplayProductExtraContent hook
                            $content = $result[$hookId][$moduleInstance->name][$args['pec_idx']]->getContent();
                            if (is_string($content)) {
                                $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
                            }
                            $result[$hookId] = $content;
                        } elseif (is_array($result[$hookId])
                            && array_key_exists(0, $result[$hookId])
                            && $result[$hookId][0] instanceof PrestaShop\PrestaShop\Core\Product\ProductExtraContent) {
                            // Handle the hookDisplayProductExtraContent hook
                            $content = $result[$hookId][0]->getContent();
                            if (is_string($content)) {
                                $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
                            }
                            $result[$hookId] = $content;
                        }
                    } catch (Exception $e) {
                        $result[$hookId] = '<!-- Error during hook (' . $moduleInstance->name . '): ' . $e->getMessage() . '-->';
                    }
                }
            }
            ++$index;
        } while ($val !== false);

        if (JprestaUtils::version_compare(_PS_VERSION_, '1.7.1.0', '<')) {
            // From PS 1.7.1 the cookie will be sent in ActionDispatcherAfter
            self::sendContextCookie();
        }

        return $result;
    }

    private static function saveProfiling($moduleInstance, $description, $duration)
    {
        static $profiling = null;
        if ($profiling === null) {
            $profiling = (bool) JprestaUtils::getConfigurationAllShop('pagecache_profiling');
        }
        static $profilingMaxReached = null;
        if ($profilingMaxReached === null) {
            $profilingMaxReached = (bool) JprestaUtils::getConfigurationAllShop('pagecache_profiling_max_reached');
        }
        static $profilingTriggerMinMs = null;
        if ($profilingTriggerMinMs === null) {
            $profilingTriggerMinMs = (int) JprestaUtils::getConfigurationAllShop('pagecache_profiling_min_ms');
        }
        if ($profiling && !$profilingMaxReached && $duration >= $profilingTriggerMinMs) {
            if (!PageCacheDAO::addProfiling($moduleInstance->id, $description, $duration, self::PROFILING_MAX_RECORD)) {
                JprestaUtils::saveConfigurationAllShop('pagecache_profiling_max_reached', true);
            }
        }
    }

    public static function execHook($hookType, $moduleInstance, $hookName, $hookArgs)
    {
        $returnValue = '';
        if (self::preHook($returnValue, $hookType, $moduleInstance, $hookName, $hookArgs)) {
            try {
                // Do it only once to optimize
                static $langIsSet = null;
                if ($langIsSet === null) {
                    $langIsSet = true;
                    Tools::setCookieLanguage(Context::getContext()->cookie);
                }
            } catch (ErrorException $e) {
                // MoneticoPaiement module has its own error handler so Tools::setCookieLanguage may throw an exception
                // that must be ignored
            }
            $hookValue = false;
            $startExecutionTime = microtime(true);
            if ($hookType === self::HOOK_TYPE_MODULE) {
                $hookValue = $moduleInstance->{$hookName}($hookArgs);

                // Code added by/for Idnovate (ticket #785)
                if (JprestaUtils::isModuleEnabled('cookiesplus')) {
                    $cookiesPlus = Module::getInstanceByName('cookiesplus');
                    if ($cookiesPlus && method_exists($cookiesPlus, 'blockModuleCode')) {
                        $cookiesPlus->blockModuleCode([
                            'display' => &$hookValue,
                            'module' => &$moduleInstance,
                            'hookName' => &$hookName,
                            'params' => &$hookArgs,
                        ]);
                    }
                }

                // Do profiling (if enabled)
                self::saveProfiling($moduleInstance, "$hookName()", (microtime(true) - $startExecutionTime) * 1000);
            } elseif ($hookType === self::HOOK_TYPE_WIDGET) {
                $context = Context::getContext();
                if (!Module::isEnabled($moduleInstance->name) || (method_exists('Module', 'isEnabledForMobileDevices') && $context->isMobile() && !Module::isEnabledForMobileDevices($moduleInstance->name))) {
                    return null;
                }
                $hookValue = $moduleInstance->renderWidget($hookName, $hookArgs);

                // Code added by/for Idnovate (ticket #785)
                if (JprestaUtils::isModuleEnabled('cookiesplus')) {
                    $cookiesPlus = Module::getInstanceByName('cookiesplus');
                    if ($cookiesPlus && method_exists($cookiesPlus, 'blockModuleCode')) {
                        $cookiesPlus->blockModuleCode([
                            'display' => &$hookValue,
                            'module' => &$moduleInstance,
                            'hookName' => &$hookName,
                            'params' => &$hookArgs,
                        ]);
                    }
                }

                // Do profiling (if enabled)
                self::saveProfiling($moduleInstance, "renderWidget('$hookName')", (microtime(true) - $startExecutionTime) * 1000);
            }

            if (is_array($hookValue) && array_key_exists(0, $hookValue) && $hookValue[0] instanceof PrestaShop\PrestaShop\Core\Product\ProductExtraContent) {
                // Handle the hookDisplayProductExtraContent hook
                if (!is_array($hookArgs)) {
                    $hookArgs = [];
                }
                foreach ($hookValue as $pecKey => $pec) {
                    if ($pec instanceof PrestaShop\PrestaShop\Core\Product\ProductExtraContent) {
                        $extraContent = $pec->getContent();
                        if (is_string($extraContent)) {
                            $newExtraContent = '';
                            $hookArgs['pec_idx'] = $pecKey;
                            if (self::preHook($newExtraContent, $hookType, $moduleInstance, $hookName, $hookArgs)) {
                                $newExtraContent = $newExtraContent . $extraContent;
                            }
                            self::postHook($newExtraContent, $hookType, $moduleInstance, $hookName);
                            $pec->setContent($newExtraContent);
                        }
                    }
                }

                return $hookValue;
            } elseif (!is_string($hookValue) && $hookValue !== false && $hookValue !== null) {
                // Handle non string returned values
                return $hookValue;
            } else {
                if ($returnValue === '') {
                    $returnValue = $hookValue;
                } else {
                    $returnValue .= $hookValue;
                }
            }
        }
        self::postHook($returnValue, $hookType, $moduleInstance, $hookName);

        return $returnValue;
    }

    public static function preHook(&$output, $hookType, $moduleInstance, $hookName, $hookArgs)
    {
        // $executed_modules must be set here so it is done for all Prestashop version 1.5, 1.6, 1.7, 8, etc
        if (in_array($moduleInstance->name, self::getModulesToCheck()) && !in_array($hookName, ['hookDisplayHeader', 'hookHeader', 'header'])) {
            self::$executed_modules[$moduleInstance->id] = $moduleInstance->id;
        }
        $displayContent = true;
        if (self::$initialised && self::canBeCached() && !JprestaUtils::isAjax() && self::canBeMarkedAsDynamic($hookName)) {
            if (strcmp(self::HOOK_TYPE_MODULE, $hookType) === 0) {
                $directives = self::_getHookCacheDirectives($moduleInstance->name, $hookName);
            } else {
                $directives = self::_getWidgetCacheDirectives($moduleInstance->name, $hookName);
            }
            if ($directives['wrapper']) {
                $hookToCall = $hookName;
                if (method_exists('Hook', 'normalizeHookName')) {
                    $hookToCall = Hook::normalizeHookName(str_replace('hook', '', $hookName));
                }
                $div = 'div';
                $output .= '<' . $div . ' id="' . uniqid('dyn') . '" class="dynhook pc_' . $hookName . '_' . $moduleInstance->id . '" data-module="' . $moduleInstance->id . '" data-hook="' . $hookToCall . '" data-hooktype="' . $hookType . '" data-hookargs="';
                foreach ($hookArgs as $hookArgName => $hookArgValue) {
                    if (strcmp('product', $hookArgName) === 0) {
                        if ($hookArgs['product'] instanceof PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray) {
                            $output .= 'pc_ipl=' . $hookArgs['product']['id'] . '^';
                        } elseif (is_array($hookArgs['product']) || $hookArgs['product'] instanceof ArrayAccess) {
                            $output .= 'pc_ipa=' . $hookArgs['product']['id_product'] . '^';
                        } elseif (is_object($hookArgs['product']) && property_exists($hookArgs['product'], 'id')) {
                            $output .= 'pc_ip=' . $hookArgs['product']->id . '^';
                        } elseif (is_integer($hookArgs['product'])) {
                            $output .= 'pc_ip=' . $hookArgs['product'] . '^';
                        }
                    }
                    if (strcmp('category', $hookArgName) === 0) {
                        if (is_array($hookArgs['category']) || $hookArgs['category'] instanceof ArrayAccess) {
                            $output .= 'pc_ica=' . $hookArgs['category']['id_category'] . '^';
                        } elseif (is_object($hookArgs['category']) && property_exists($hookArgs['category'], 'id')) {
                            $output .= 'pc_ic=' . $hookArgs['category']->id . '^';
                        } elseif (is_integer($hookArgs['category'])) {
                            $output .= 'pc_ic=' . $hookArgs['category'] . '^';
                        }
                    } elseif (is_int($hookArgValue)) {
                        $output .= $hookArgName . '=' . (int) $hookArgValue . '^';
                    } elseif (is_bool($hookArgValue)) {
                        $output .= $hookArgName . '=' . ($hookArgValue ? '0' : '1') . '^';
                    } elseif (is_string($hookArgValue)) {
                        $output .= $hookArgName . '=' . urlencode($hookArgValue) . '^';
                    }
                }
                $output .= '"><' . $div . ' class="loadingempty"></' . $div . '>';
                $displayContent = $directives['content'];
            }
        }

        return $displayContent;
    }

    public static function postHook(&$output, $hookType, $moduleInstance, $hookName)
    {
        $div = 'div';
        if (self::$initialised && self::canBeCached() && !JprestaUtils::isAjax() && self::canBeMarkedAsDynamic($hookName)) {
            if (strcmp(self::HOOK_TYPE_MODULE, $hookType) === 0) {
                $directives = self::_getHookCacheDirectives($moduleInstance->name, $hookName);
            } else {
                $directives = self::_getWidgetCacheDirectives($moduleInstance->name, $hookName);
            }
            if ($directives['wrapper']) {
                $output .= '</' . $div . '>';
            }
        }
    }

    /**
     * Call preHook and postHook for widget_block
     *
     * @param $params Parameters on widget block tag
     * @param $content HTML content of the widget block
     * @param $smarty Smarty instance
     *
     * @return string Modified content of the widget block
     */
    public static function smartyWidgetBlockPageCache($params, $content, &$smarty)
    {
        $output = '';
        if (null === $content) {
            // Function is called twice: at the opening of the block
            // and when it is closed.
            // This is the first call.
            $output = smartyWidgetBlock($params, $content, $smarty);
        } else {
            // Function gets called for the closing tag of the block.
            $html = smartyWidgetBlock($params, $content, $smarty);

            if (array_key_exists('pckey', $params)) {
                $blockKey = $params['pckey'];
                $moduleName = $params['name'];
                $moduleInstance = Module::getInstanceByName($moduleName);
                if (self::preHook($output, self::HOOK_TYPE_WIDGET_BLOCK, $moduleInstance, $blockKey, $params)) {
                    $output .= $html;
                }
                self::postHook($output, self::HOOK_TYPE_WIDGET_BLOCK, $moduleInstance, $blockKey);
            } else {
                $output = $html;
            }
        }

        return $output;
    }

    /**
     * Called just before smarty compilation. It adds an attribute 'pckey' to all widget_block tag to extract and
     * save the template block into a file to be able to refresh this part separately (with dynamic ajax request
     *
     * @param $source Smarty template content
     * @param $smarty Smarty instance
     *
     * @return string Modified template content
     */
    public static function smartyWidgetBlockPageCachePrefilter($source, $smarty)
    {
        $lastOffset = JprestaUtils::strpos($source, '{widget_block');
        if ($lastOffset !== false) {
            $moduleInstance = Module::getInstanceByName('jprestaspeedpack');
            if ($moduleInstance) {
                $modifiedSource = Tools::substr($source, 0, $lastOffset);
                // Find widget_block blocks, add 'key' attribute, store content of the block in cache
                $pattern = '/({widget_block[\s]+name=\"([a-zA-Z0-9_]+)\"[\s]*)}(.*){\/widget_block}/sU';
                $matches = [];
                preg_match($pattern, $source, $matches);
                while (count($matches) > 0) {
                    $hash = crc32($smarty->source->filepath . $matches[0]);
                    $blockKey = sprintf('%u', $hash);
                    $offset = JprestaUtils::strpos($source, $matches[0]);
                    $modifiedSource .= Tools::substr($source, $lastOffset, $offset - $lastOffset);
                    $modifiedSource .= $matches[1];
                    $modifiedSource .= ' pckey="' . $blockKey . '"}';
                    $modifiedSource .= $matches[3];
                    $modifiedSource .= '{/widget_block}';
                    $lastOffset = $offset + JprestaUtils::strlen($matches[0]);
                    $moduleInstance->setWidgetBlockTemplate($blockKey, $matches[3]);

                    // Next
                    $matches = [];
                    preg_match($pattern, $source, $matches, 0, $lastOffset);
                }
                $modifiedSource .= Tools::substr($source, $lastOffset, JprestaUtils::strlen($source));

                return $modifiedSource;
            }
        }

        return $source;
    }

    public static function getJsDef()
    {
        if (JprestaUtils::version_compare(_PS_VERSION_, '1.6', '>')) {
            $context = Context::getContext();
            Media::addJsDef([
                'isLogged' => (bool) $context->customer->isLogged(),
                'isGuest' => (bool) $context->customer->isGuest(),
                'comparedProductsIds' => $context->smarty->getTemplateVars('compared_products'),
            ]);
            $defs = Media::getJsDef();
            $defs['prestashop_pc'] = $defs['prestashop'];
            if ($context->customer->isLogged() && array_key_exists('customer', $defs['prestashop_pc']) && !array_key_exists('id_customer', $defs['prestashop_pc']['customer'])) {
                // For some modules we need the id of current visitor
                $defs['prestashop_pc']['customer']['id_customer'] = $context->customer->id;
            }
            unset($defs['prestashop']);
            unset($defs['baseDir']);
            unset($defs['baseUrl']);
            // Fix for module Revolition Slider
            unset($defs['SdsJsOnLoadActions']);

            return $defs;
        }

        return [];
    }

    public static function getCurrencyId($context)
    {
        $context->currency = Tools::setCurrency($context->cookie);
        if (!isset($context->cookie->id_currency)) {
            $id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        } else {
            $id_currency = $context->cookie->id_currency;
        }

        return (int) $id_currency;
    }

    public static function getCountry($context)
    {
        // static variable avoid computing the country multiple times
        static $current_country = null;
        if ($current_country == null) {
            $current_country = false;
            if (Configuration::get('PS_GEOLOCATION_ENABLED')) {
                // Detect country now to get it right
                $current_country = self::getCountryByGeolocation($context);
            } elseif (Configuration::get('PS_DETECT_COUNTRY')) {
                $has_currency = isset($context->cookie->id_currency) && (int) $context->cookie->id_currency;
                $has_country = isset($context->cookie->iso_code_country) && $context->cookie->iso_code_country;
                $has_address_type = false;

                if ((int) $context->cookie->id_cart && ($cart = new Cart($context->cookie->id_cart)) && Validate::isLoadedObject($cart)) {
                    $has_address_type = isset($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) && $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                }

                if ((!$has_currency || $has_country) && !$has_address_type) {
                    $id_country = $has_country && Validate::isLanguageIsoCode($context->cookie->iso_code_country) ?
                        (int) Country::getByIso(Tools::strtoupper($context->cookie->iso_code_country)) : (int) Tools::getCountry();

                    try {
                        $country = new Country($id_country, (int) $context->cookie->id_lang);
                        if (validate::isLoadedObject($country)) {
                            $current_country = $country;
                        }
                    } catch (PrestaShopException $e) {
                        // Ignore
                    }
                }
            } elseif ($context->country) {
                $current_country = $context->country;
            }

            // Address of customer, if any, has higher priority
            $current_tax_address = false;
            if ((int) $context->cookie->id_cart) {
                $cart = new Cart($context->cookie->id_cart);
                $id_address = $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                /* If address is not set then FrontController::init will set the it with the first address of the customer */
                if ($cart->id_customer && (!isset($id_address) || $id_address == 0)) {
                    $id_address = (int) Address::getFirstCustomerAddressId($cart->id_customer);
                }
                if ($id_address) {
                    $current_tax_address = Address::initialize($id_address);
                }
            } else {
                if ($context->cookie->id_customer) {
                    /* There is no cart but a customer is logged in */
                    $id_address = (int) Address::getFirstCustomerAddressId($context->cookie->id_customer);
                    if ($id_address) {
                        /* Take his first address */
                        $current_tax_address = Address::initialize($id_address);
                    }
                }
            }
            if ($current_tax_address && $current_tax_address->id_country) {
                $current_country = new Country($current_tax_address->id_country);
            }

            // No country found? Then return default country of the shop.
            if (!$current_country) {
                $current_country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
            }
        }

        return $current_country;
    }

    private static function getCountryByGeolocation($context)
    {
        $country = null;
        $controller_instance = self::getControllerInstance();
        if ($controller_instance !== false && method_exists($controller_instance, 'geolocationManagementPublic')) {
            if (($newDefault = $controller_instance->geolocationManagementPublic($context->country)) && Validate::isLoadedObject($newDefault)) {
                $country = $newDefault;
            }
        }

        return $country;
    }

    private static function isDependsOnDevice()
    {
        static $depends_on_devices = null;
        if ($depends_on_devices == null) {
            $val = Configuration::get('pagecache_depend_on_device_auto');
            if ($val) {
                $depends_on_devices = true;
            } else {
                $depends_on_devices = false;
            }
        }

        return $depends_on_devices;
    }

    private static function getControllerName()
    {
        static $controller = false;
        if (self::$initialised && !$controller) {
            $request = null;
            if (array_key_exists('request', $GLOBALS) && $GLOBALS['request'] instanceof Symfony\Component\HttpFoundation\Request) {
                $request = $GLOBALS['request'];
            }
            $controller = Dispatcher::getInstance($request)->getController();
            if (Tools::getIsset('fc') && Tools::getValue('fc') === 'module') {
                $controller = Tools::getValue('module') . '__' . $controller;
            }
            if ($controller === 'pagenotfound' && JprestaUtils::isModuleEnabled('smartseourl')) {
                $smartseourlInstance = Module::getInstanceByName('smartseourl');
                $controllerClass = $smartseourlInstance->dispatch();
                $controller = str_replace('controller', '', Tools::strtolower($controllerClass));
            }
        }

        return $controller;
    }

    private static function getControllerInstance()
    {
        // static variable avoid computing the controller multiple times
        static $controller = null;
        if ($controller == null) {
            $controller = false;
            // Load controllers classes
            $controllers = Dispatcher::getControllers([_PS_FRONT_CONTROLLER_DIR_, _PS_OVERRIDE_DIR_ . 'controllers/front/']);
            $controllers['index'] = 'IndexController';
            // Get controller name
            $controller_name = self::getControllerName();
            if (isset($controllers[Tools::strtolower($controller_name)])) {
                // Create controller instance
                $controller_class = $controllers[Tools::strtolower($controller_name)];
                $context = Context::getContext();
                if ($context->controller) {
                    $controller = $context->controller;
                } else {
                    if (!isset($context->link)) {
                        /* Link should be initialized in the context but sometimes it is not */
                        $https_link = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
                        $context->link = new Link($https_link, $https_link);
                    }
                    if (JprestaUtils::isModuleEnabled('smartseourl') && in_array($controller_class, ['ProductController', 'CategoryController', 'CmsController', 'SupplierController', 'ManufacturerController'])) {
                        $controller = Controller::getController('PageNotFoundController');
                    } else {
                        $controller = Controller::getController($controller_class);
                    }
                }
            }
        }

        return $controller;
    }

    public static function getCurrentURL()
    {
        $https = self::getServerValue('HTTPS');
        if (!empty($https) && $https !== 'off' || self::getServerValue('SERVER_PORT') == 443) {
            $pageURL = 'https://' . $_SERVER['HTTP_HOST'] . urldecode($_SERVER['REQUEST_URI']);
        } else {
            $proto = self::getServerValue('HTTP_X_FORWARDED_PROTO');
            if ($proto) {
                $pageURL = $proto . '://' . $_SERVER['HTTP_HOST'] . urldecode($_SERVER['REQUEST_URI']);
            } else {
                $pageURL = 'http://' . $_SERVER['HTTP_HOST'] . urldecode($_SERVER['REQUEST_URI']);
            }
        }

        return $pageURL;
    }

    public static function filterAndSortParams($query_string, $ignored_params)
    {
        $new_query_string = '';
        if ($query_string) {
            $keyvalues = explode('&', $query_string);
            sort($keyvalues);
            foreach ($keyvalues as $keyvalue) {
                if ($keyvalue !== '') {
                    $key = '';
                    $value = '';
                    $current_key_value = explode('=', $keyvalue);
                    if (count($current_key_value) > 0) {
                        $key = Tools::strtolower($current_key_value[0]);
                    }
                    if (count($current_key_value) > 1) {
                        $value = $current_key_value[1];
                    }
                    if (!in_array($key, $ignored_params)) {
                        $new_query_string .= '&' . $key . '=' . $value;
                    }
                }
            }
            if ($new_query_string !== '') {
                $new_query_string = Tools::substr($new_query_string, 1);
            }
        }

        return $new_query_string;
    }

    public static function cacheThis($html)
    {
        if (self::isNotCode200()) {
            return;
        }

        $maxrows = (int) Configuration::get('pagecache_maxrows');
        if ($maxrows > 0 && PageCacheDAO::getMainRowsCount() > $maxrows) {
            // Try to purge the cache
            PageCacheDAO::deleteCachedPages(PageCacheDAO::getCachedPages(24, 10, null), true);
            if (!headers_sent()) {
                header(self::HTTP_HEADER_CACHE_INFO . ': status=off, reason=maxrows-reach');
            }

            return;
        }

        // Some old theme are calling smartyOutputContent multiple times
        static $cumulHtml = false;
        if ($cumulHtml === false || JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $cumulHtml = $html;
        } else {
            $cumulHtml .= $html;
            $html = $cumulHtml;
        }

        // Save the html into the cache
        $controller = self::getControllerName();
        $cache_ttl = 60 * ((int) Configuration::get('pagecache_' . $controller . '_timeout'));
        $jprestaCacheKey = self::getCacheKeyInfos();
        self::getCache()->set($jprestaCacheKey->get('url'), PageCacheDAO::getContextKeyById(PageCacheDAO::getOrCreateContextId($jprestaCacheKey)), $html, $cache_ttl);

        // Parse this file to find all backlinks
        $backlinks = [];
        $shop_url = new ShopUrl(Shop::getContextShopID());
        $base = $shop_url->getURL();
        $links = JprestaUtils::parseLinks($html, $base, self::getManagedControllersNames(), '*PCIGN*', '**PCIGN**', JprestaUtils::decodeConfiguration(Configuration::get('pagecache_ignore_before_pattern')), JprestaUtils::decodeConfiguration(Configuration::get('pagecache_ignore_after_pattern')));
        foreach ($links as $link) {
            $linkCacheKey = self::getCacheKeyForBacklink($link);
            $backlinks[$linkCacheKey] = $linkCacheKey;
        }

        // Insert in database
        PageCacheDAO::insert(
            $jprestaCacheKey,
            $controller,
            Shop::getContextShopID(),
            self::getCurrentObjectId(),
            self::$executed_modules,
            $backlinks,
            Configuration::get('pagecache_logs'),
            !self::isCacheWarmer() && !self::isStatusChecker());

        // Reduce the cache continuously (remove 2 expired row when it adds one new row)
        PageCacheDAO::deleteCachedPages(PageCacheDAO::getCachedPages(24, 2, null), true);
    }

    public static function preDisplayStats()
    {
        if (JprestaUtils::isAjax()) {
            // Skip useless work
            return [];
        }

        $infos = [];
        if (self::isDisplayStats()) {
            $context = Context::getContext();
            $currency = new Currency(self::getCurrencyId($context));
            $controller = self::getControllerName();
            if (in_array($controller, self::getManagedControllersNames())) {
                $country = self::getCountry($context);
                $infos['cacheable'] = self::canBeCached() ? 'true' : 'false';
                $infos['cacheable_reason'] = self::$status_reason;
                $timeoutValue = (int) Configuration::get('pagecache_' . $controller . '_timeout');
                if ($timeoutValue === 0) {
                    $timeoutValue = 'Disabled';
                } elseif ($timeoutValue === -1) {
                    $timeoutValue = 'Never';
                } else {
                    $timeoutValue = ($timeoutValue / 1440) . ' day(s)';
                }
                $expiresValue = (int) Configuration::get('pagecache_' . $controller . '_expires');
                if ($expiresValue === 0) {
                    $expiresValue = 'Disabled';
                } else {
                    $expiresValue = $expiresValue . ' minute(s)';
                }
                $infos['loc_tax'] = self::getCountryStateZipcodeForTaxes($context);
                $infos['timeout_server'] = $timeoutValue;
                $infos['timeout_browser'] = $expiresValue;
                $infos['controller'] = $controller;
                $infos['currency'] = $currency->name;
                if ($country) {
                    $infos['country'] = $country->getFieldByLang('name');
                } else {
                    $infos['country'] = '-';
                }
                $infos['cache_key'] = json_encode(self::getCacheKeyInfos()->compute()->infos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            }
        }

        return $infos;
    }

    public static function displayStats($from_cache, $infos)
    {
        if (self::isDisplayStats()) {
            $controller = self::getControllerName();
            if (in_array($controller, self::getManagedControllersNames())) {
                // Prepare datas
                $context = Context::getContext();
                $infos['groups'] = '';
                $groupsIds = self::getGroupsIds($context);
                foreach ($groupsIds as $arrayKey => $groupId) {
                    if (((int) $groupId) > 0) {
                        $group = new Group($groupId);
                        $infos['groups'] = $infos['groups'] . $group->name[$context->language->id] . ($arrayKey === 0 ? '*' : '') . ', ';
                    }
                }
                $infos['from_cache'] = $from_cache;
                $stats = PageCacheDAO::getStats(self::getCacheKeyInfos());
                if ($stats['hit'] != -1) {
                    $infos['hit'] = $stats['hit'];
                    $infos['missed'] = $stats['missed'];
                    $infos['perfs'] = ($stats['hit'] + $stats['missed'] !== 0) ? number_format(100 * $stats['hit'] / ($stats['hit'] + $stats['missed']), 1) . '%' : '-';
                } else {
                    $infos['hit'] = '-';
                    $infos['missed'] = '-';
                    $infos['perfs'] = '-';
                }
                $infos['pagehash'] = self::getCacheKeyInfos()->toString();

                $infos['url_on_off'] = http_build_url(self::getCleanURL(), ['query' => 'dbgpagecache=' . ((int) Tools::getValue('dbgpagecache', 0) == 0 ? 1 : 0)], HTTP_URL_JOIN_QUERY);
                $infos['url_on_off_disabled'] = Configuration::get('pagecache_always_infosbox') && !Configuration::get('pagecache_debug');
                $infos['url_del'] = http_build_url(self::getCleanURL(), ['query' => 'dbgpagecache=' . Tools::getValue('dbgpagecache', 0) . '&delpagecache=1'], HTTP_URL_JOIN_QUERY);
                $infos['url_reload'] = http_build_url(self::getCleanURL(), ['query' => 'dbgpagecache=' . Tools::getValue('dbgpagecache', 1)], HTTP_URL_JOIN_QUERY);
                $infos['url_close'] = self::getCleanURL();
                $infos['url_close_disabled'] = Configuration::get('pagecache_always_infosbox');
                $infos['dbgpagecache'] = (int) Tools::getValue('dbgpagecache', 0) || !Configuration::get('pagecache_debug') ? 1 : 0;
                $infos['base_dir'] = _PS_BASE_URL_ . __PS_BASE_URI__;

                // Display the box
                $context->smarty->assign($infos);
                $context->smarty->display(_PS_MODULE_DIR_ . basename(__FILE__, '.php') . '/views/templates/hook/pagecache-infos.tpl');
            }
        }
    }

    public static function getCleanURL($url = null)
    {
        if ($url == null) {
            $url = self::getCurrentURL();
        }
        $new_query = '';
        $query = parse_url($url, PHP_URL_QUERY);
        if ($query != null) {
            $query = html_entity_decode($query);
            $keyvals = explode('&', $query);
            foreach ($keyvals as $keyval) {
                $x = explode('=', $keyval);
                if (strcmp($x[0], 'dbgpagecache') != 0 && strcmp($x[0], 'delpagecache') != 0) {
                    $new_query .= '&' . $x[0] . '=' . (count($x) > 1 ? $x[1] : '');
                }
            }
        }
        $un = new PageCacheURLNormalizer();
        $un->setUrl(http_build_url($url, ['query' => $new_query], HTTP_URL_REPLACE));

        return $un->normalize();
    }

    /**
     * @return bool true if the cache was correctly cleared
     */
    public static function clearCache($reason = 'unknown')
    {
        $startTime = microtime(true);
        $clearOK = true;

        // Delete cache of current shop(s)
        if (Shop::isFeatureActive()) {
            foreach (Shop::getContextListShopID() as $id_shop) {
                $clearOK = $clearOK && self::getCache($id_shop)->flush(self::FLUSH_MAX_SECONDS);
            }
        } else {
            $clearOK = $clearOK && self::getCache()->flush(self::FLUSH_MAX_SECONDS);
        }
        // TODO pourquoi on vide tout et pas par boutique?
        PageCacheDAO::clearAllCache();

        try {
            $softizyconditionalcache = Module::getInstanceByName('softizyconditionalcache');
            if ($softizyconditionalcache) {
                if (method_exists($softizyconditionalcache, 'hookActionClearCache')) {
                    $softizyconditionalcache->hookActionClearCache();
                } elseif (method_exists($softizyconditionalcache, 'hookActionClearSf2Cache')) {
                    $softizyconditionalcache->hookActionClearSf2Cache();
                }
            }
        } catch (Exception $e) {
            // Ignore
        }

        if (Configuration::get('pagecache_logs') > 0) {
            $msg = '';
            $stacks = debug_backtrace();
            for ($i = 0; $i < count($stacks); ++$i) {
                if (array_key_exists('file', $stacks[$i])) {
                    $msg .= $stacks[$i]['function'] . '(' . basename($stacks[$i]['file']) . ':' . $stacks[$i]['line'] . ')';
                } else {
                    $msg .= $stacks[$i]['function'] . '(?)';
                }
                if ($i + 1 < count($stacks)) {
                    $msg .= ' - ';
                }
            }
            JprestaUtils::addLog("PageCache | clearCache($reason) | $msg = " . number_format(microtime(true) - $startTime, 3) . ' second(s)', 1, null, null, null, true);
        } else {
            JprestaUtils::addLog("PageCache | clearCache($reason) | " . number_format(microtime(true) - $startTime, 3) . ' second(s)', 1, null, null, null, true);
        }

        // Update database stats
        PageCacheDAO::analyzeTables();

        return $clearOK;
    }

    public function clearCacheAndStats($reason = 'unknown')
    {
        $startTime = microtime(true);
        $clearOK = true;

        // Delete cache and stats of current shop(s)
        if (Shop::isFeatureActive()) {
            foreach (Shop::getContextListShopID() as $id_shop) {
                $clearOK = $clearOK && self::getCache($id_shop)->flush(self::FLUSH_MAX_SECONDS);
            }
            PageCacheDAO::resetCache(Shop::getContextListShopID());
            if (!Configuration::get('pagecache_statsttfb')) {
                PageCacheDAO::resetStatPerfs(Shop::getContextListShopID());
            }
        } else {
            $clearOK = $clearOK && self::getCache()->flush(self::FLUSH_MAX_SECONDS);
            PageCacheDAO::resetCache();
            if (!Configuration::get('pagecache_statsttfb')) {
                PageCacheDAO::resetStatPerfs();
            }
        }

        // Update database stats
        PageCacheDAO::analyzeTables();

        JprestaUtils::addLog("PageCache | reset($reason) | " . number_format(microtime(true) - $startTime, 3) . ' second(s)', 1, null, null, null, true);

        return $clearOK;
    }

    public function purgeCache($id_shop, $reason = 'unknown')
    {
        $clearOK = true;
        $deleteDuration = 0;
        $round = 1;
        $startTime = microtime(true);

        $obsoleteContextsIds = PageCacheDAO::getObsoleteContextsIds($id_shop);

        $rowsTodelete = PageCacheDAO::getCachedPages(24, 500, true, $obsoleteContextsIds);
        $searchDuration = microtime(true) - $startTime;

        while ($clearOK && count($rowsTodelete) > 0) {
            $startTimeDelete = microtime(true);
            PageCacheDAO::deleteCachedPages($rowsTodelete, true);
            $deleteDuration += microtime(true) - $startTimeDelete;

            // Check duration to avoid timeouts
            if ((microtime(true) - $startTime) > self::FLUSH_MAX_SECONDS) {
                $clearOK = false;
                break;
            }
            ++$round;

            $startTimeSearch = microtime(true);
            $rowsTodelete = PageCacheDAO::getCachedPages(24, 500, true, $obsoleteContextsIds);
            $searchDuration += microtime(true) - $startTimeSearch;
        }

        PageCacheDAO::deleteUnusedContexts();
        PageCacheDAO::deleteUnusedFakeUsers();

        JprestaUtils::addLog("PageCache | purge($reason) done in " . $round . ' rounds: search during ' . round($searchDuration,
            3) . 's, deletion tooks ' . round($deleteDuration, 3) . 's');

        // Update database stats
        PageCacheDAO::analyzeTables();

        if ((microtime(true) - $startTime) < self::FLUSH_MAX_SECONDS) {
            $this->getCacheInstance($id_shop)->purge(self::FLUSH_MAX_SECONDS - (microtime(true) - $startTime));
        }

        return $clearOK;
    }

    private function _clearCacheModules($event, $action_origin = '')
    {
        $val = Configuration::get($event . '_mods');
        if ($val) {
            $mods = explode(' ', $val);
            foreach ($mods as $mod) {
                $module_name = trim($mod);
                if (JprestaUtils::strlen($mod) > 0) {
                    PageCacheDAO::clearCacheOfModule($module_name, $action_origin,
                        Configuration::get('pagecache_logs'));
                }
            }
        }
    }

    public function hookActionAttributeDelete($params)
    {
        $this->hookActionAttributeSave($params);
    }

    public function hookActionAttributeSave($params)
    {
        if (isset($params['id_attribute'])) {
            // An attribute has been modified, it can be its label, its URL, etc. so all products using it must
            // be refreshed (only the product page)

            $productsIds = Db::getInstance()->executeS('
                SELECT DISTINCT pa.id_product
                FROM ' . _DB_PREFIX_ . 'product_attribute pa
                LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON (pac.id_product_attribute = pa.id_product_attribute)
                WHERE pac.id_attribute = ' . (int) $params['id_attribute']
            );
            foreach ($productsIds as $productId) {
                $this->onProductUpdate($productId['id_product'], 'modification/deletion of Attribute#' . $params['id_attribute']);
            }
        }
    }

    public function hookActionAttributeGroupDelete($params)
    {
        $this->hookActionAttributeGroupSave($params);
    }

    public function hookActionAttributeGroupSave($params)
    {
        if (isset($params['id_attribute_group'])) {
            // An attribute group has been modified, it can be its label, its URL, etc. so all products using it must
            // be refreshed (only the product page)

            $productsIds = Db::getInstance()->executeS('
                SELECT DISTINCT pa.id_product
                FROM ' . _DB_PREFIX_ . 'product_attribute pa
                LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON (pac.id_product_attribute = pa.id_product_attribute)
                LEFT JOIN ' . _DB_PREFIX_ . 'attribute a ON (a.id_attribute = pac.id_attribute)
                WHERE a.id_attribute_group = ' . (int) $params['id_attribute_group']
            );
            foreach ($productsIds as $productId) {
                $this->onProductUpdate($productId['id_product'], 'modification/deletion of AttributeGroup#' . $params['id_attribute_group']);
            }
        }
    }

    public function hookActionFeatureDelete($params)
    {
        $this->hookActionFeatureSave($params);
    }

    public function hookActionFeatureSave($params)
    {
        if (isset($params['id_feature'])) {
            // An feature has been modified, it can be its label, etc. so all products using it must
            // be refreshed (only the product page)

            $id_feature = $params['id_feature'];
            $productsIds = Db::getInstance()->executeS('
                SELECT DISTINCT p.id_product
                FROM ' . _DB_PREFIX_ . 'product p
                LEFT JOIN ' . _DB_PREFIX_ . 'feature_product f ON (f.id_product = p.id_product)
                WHERE f.id_feature = ' . (int) $id_feature
            );
            foreach ($productsIds as $productId) {
                $this->onProductUpdate($productId['id_product'], 'modification/deletion of Feature#' . $id_feature);
            }
        }
    }

    public function hookActionFeatureValueDelete($params)
    {
        $this->hookActionFeatureValueSave($params);
    }

    public function hookActionFeatureValueSave($params)
    {
        if (isset($params['id_feature_value'])) {
            // An feature value has been modified, it can be its label, etc. so all products using it must
            // be refreshed (only the product page)

            $id_feature_value = $params['id_feature_value'];
            $productsIds = Db::getInstance()->executeS('
                SELECT DISTINCT p.id_product
                FROM ' . _DB_PREFIX_ . 'product p
                LEFT JOIN ' . _DB_PREFIX_ . 'feature_product fp ON (fp.id_product = p.id_product)
                WHERE fp.id_feature = ' . (int) $id_feature_value
            );
            foreach ($productsIds as $productId) {
                $this->onProductUpdate($productId['id_product'], 'modification/deletion of FeatureValue#' . $id_feature_value);
            }
        }
    }

    public function hookActionObjectCmsAddAfter($params)
    {
        if (isset($params['object'])) {
            PageCacheDAO::clearCacheOfObject('cms', $params['object']->id, false, 'creation of CMS page #' . $params['object']->id, Configuration::get('pagecache_logs'));
        }
        $this->_clearCacheModules('pagecache_cms_a', 'creation of CMS page #' . $params['object']->id);
    }

    public function hookActionObjectCmsUpdateAfter($params)
    {
        if (isset($params['object'])) {
            PageCacheDAO::clearCacheOfObject('cms', $params['object']->id, Configuration::get('pagecache_cms_u_bl'), 'modification of CMS page #' . $params['object']->id, Configuration::get('pagecache_logs'));
        }
        $this->_clearCacheModules('pagecache_cms_u', 'modification of CMS page #' . $params['object']->id);
    }

    public function hookActionObjectCmsDeleteBefore($params)
    {
        if (isset($params['object'])) {
            PageCacheDAO::clearCacheOfObject('cms', $params['object']->id, Configuration::get('pagecache_cms_d_bl'), 'deletion of CMS page #' . $params['object']->id, Configuration::get('pagecache_logs'));
        }
        $this->_clearCacheModules('pagecache_cms_d', 'deletion of CMS page #' . $params['object']->id);
    }

    public function hookActionObjectManufacturerAddAfter($params)
    {
        if (isset($params['object'])) {
            PageCacheDAO::clearCacheOfObject('manufacturer', $params['object']->id, false, 'hookActionObjectManufacturerAddAfter', Configuration::get('pagecache_logs'));
            // Also clear the page with all manufacturers
            PageCacheDAO::clearCacheOfObject('manufacturer', null, false, 'hookActionObjectManufacturerAddAfter', Configuration::get('pagecache_logs'));
        }
        $this->_clearCacheModules('pagecache_manufacturer_a', 'hookActionObjectManufacturerAddAfter');
    }

    public function hookActionObjectManufacturerUpdateAfter($params)
    {
        if (isset($params['object'])) {
            PageCacheDAO::clearCacheOfObject('manufacturer', $params['object']->id, Configuration::get('pagecache_manufacturer_u_bl'), 'hookActionObjectManufacturerUpdateAfter', Configuration::get('pagecache_logs'));
            // Also clear the page with all manufacturers
            PageCacheDAO::clearCacheOfObject('manufacturer', null, Configuration::get('pagecache_manufacturer_u_bl'), 'hookActionObjectManufacturerUpdateAfter', Configuration::get('pagecache_logs'));
        }
        $this->_clearCacheModules('pagecache_manufacturer_u', 'hookActionObjectManufacturerUpdateAfter');
    }

    public function hookActionObjectManufacturerDeleteBefore($params)
    {
        if (isset($params['object'])) {
            PageCacheDAO::clearCacheOfObject('manufacturer', $params['object']->id, Configuration::get('pagecache_manufacturer_d_bl'), 'hookActionObjectManufacturerDeleteBefore', Configuration::get('pagecache_logs'));
            // Also clear the page with all manufacturers
            PageCacheDAO::clearCacheOfObject('manufacturer', null, Configuration::get('pagecache_manufacturer_d_bl'), 'hookActionObjectManufacturerDeleteBefore', Configuration::get('pagecache_logs'));
        }
        $this->_clearCacheModules('pagecache_manufacturer_d', 'hookActionObjectManufacturerDeleteBefore');
    }

    private static $lastStockAvailableCanOrder;
    private static $lastStockAvailableQuantity;

    /**
     *  Called when a warehouse is associated to a product with advanced stock management enabled
     */
    public function hookActionObjectWarehouseProductLocationAddBefore($params)
    {
        if (isset($params['object'])) {
            $newWarehouseProductLocation = $params['object'];
            $product = new Product($newWarehouseProductLocation->id_product);
            $product->id_product_attribute = (int) $newWarehouseProductLocation->id_product_attribute;
            self::$lastStockAvailableCanOrder = $product->checkQty(1);
            self::$lastStockAvailableQuantity = StockManagerFactory::getManager()->getProductRealQuantities($product->id, $product->id_product_attribute, null, true);
        }
    }

    /**
     *  Called when a new warehouse is associated to a product with advanced stock management enabled
     */
    public function hookActionObjectWarehouseProductLocationAddAfter($params)
    {
        if (isset($params['object'])) {
            $newWarehouseProductLocation = $params['object'];
            $product = new Product($newWarehouseProductLocation->id_product);
            $product->id_product_attribute = (int) $newWarehouseProductLocation->id_product_attribute;
            $newQuantity = StockManagerFactory::getManager()->getProductRealQuantities($product->id, $product->id_product_attribute, null, true);
            $this->handleStock($product, $product->id_product_attribute, $newQuantity);
        }
    }

    /**
     *  Called when a warehouse is disassociated to a product with advanced stock management enabled
     */
    public function hookActionObjectWarehouseProductLocationDeleteBefore($params)
    {
        if (isset($params['object'])) {
            $deletedWarehouseProductLocation = $params['object'];
            $product = new Product($deletedWarehouseProductLocation->id_product);
            $product->id_product_attribute = (int) $deletedWarehouseProductLocation->id_product_attribute;
            self::$lastStockAvailableCanOrder = $product->checkQty(1);
            self::$lastStockAvailableQuantity = StockManagerFactory::getManager()->getProductRealQuantities($product->id, $product->id_product_attribute, null, true);
        }
    }

    /**
     *  Called when a new warehouse is disassociated to a product with advanced stock management enabled
     */
    public function hookActionObjectWarehouseProductLocationDeleteAfter($params)
    {
        if (isset($params['object'])) {
            $deletedWarehouseProductLocation = $params['object'];
            $product = new Product($deletedWarehouseProductLocation->id_product);
            $product->id_product_attribute = (int) $deletedWarehouseProductLocation->id_product_attribute;
            $newQuantity = StockManagerFactory::getManager()->getProductRealQuantities($product->id, $product->id_product_attribute, null, true);
            $this->handleStock($product, $product->id_product_attribute, $newQuantity);
        }
    }

    /**
     *  Called when a new warehouse is created with advanced stock management enabled
     */
    public function hookActionObjectStockAddBefore($params)
    {
        if (isset($params['object'])) {
            $newStock = $params['object'];
            $product = new Product($newStock->id_product);
            $product->id_product_attribute = (int) $newStock->id_product_attribute;
            self::$lastStockAvailableCanOrder = $product->checkQty(1);
            self::$lastStockAvailableQuantity = StockManagerFactory::getManager()->getProductRealQuantities($product->id, $product->id_product_attribute, null, true);
        }
    }

    /**
     *  Called when a new warehouse is created with advanced stock management enabled
     */
    public function hookActionObjectStockAddAfter($params)
    {
        if (isset($params['object'])) {
            $newStock = $params['object'];
            $product = new Product($newStock->id_product);
            $product->id_product_attribute = (int) $newStock->id_product_attribute;
            $newQuantity = StockManagerFactory::getManager()->getProductRealQuantities($product->id, $product->id_product_attribute, null, true);
            $this->handleStock($product, $product->id_product_attribute, $newQuantity);
        }
    }

    /**
     *  Called when stock is modified with advanced stock management enabled
     */
    public function hookActionObjectStockUpdateBefore($params)
    {
        if (isset($params['object'])) {
            $newStock = $params['object'];
            $product = new Product($newStock->id_product);
            $product->id_product_attribute = (int) $newStock->id_product_attribute;
            self::$lastStockAvailableCanOrder = $product->checkQty(1);
            self::$lastStockAvailableQuantity = StockManagerFactory::getManager()->getProductRealQuantities($product->id, $product->id_product_attribute, null, true);
        }
    }

    /**
     *  Called when stock is modified with advanced stock management enabled
     */
    public function hookActionObjectStockUpdateAfter($params)
    {
        if (isset($params['object'])) {
            $newStock = $params['object'];
            $product = new Product($newStock->id_product);
            $product->id_product_attribute = (int) $newStock->id_product_attribute;
            $newQuantity = StockManagerFactory::getManager()->getProductRealQuantities($product->id, $product->id_product_attribute, null, true);
            $this->handleStock($product, $product->id_product_attribute, $newQuantity);
        }
    }

    /**
     *  Called when stock is modified with standard stock management
     */
    public function hookActionObjectStockAvailableUpdateBefore($params)
    {
        if (isset($params['object'])) {
            $newStock = $params['object'];
            $product = new Product($newStock->id_product);
            $product->id_product_attribute = (int) $newStock->id_product_attribute;
            self::$lastStockAvailableCanOrder = $product->checkQty(1);
            $currentStockId = StockAvailable::getStockAvailableIdByProductId($product->id, $product->id_product_attribute, ((int) $newStock->id_shop) === 0 ? null : (int) $newStock->id_shop);
            if ($currentStockId) {
                $currentStock = new StockAvailable($currentStockId);
                self::$lastStockAvailableQuantity = $currentStock->quantity;
            }
        }
    }

    /**
     *  Called when stock is modified with standard stock management
     */
    public function hookActionObjectStockAvailableUpdateAfter($params)
    {
        if (isset($params['object'])) {
            $newStock = $params['object'];
            $product = new Product($newStock->id_product);
            $product->id_product_attribute = (int) $newStock->id_product_attribute;
            // Clear the cache to get the actual current value
            Cache::clean('StockAvailable::getQuantityAvailableByProduct_' . (int) $product->id . '*');
            $this->handleStock($product, $product->id_product_attribute, $newStock->quantity);
        }
    }

    private function handleStock($product, $id_product_attribute, $newQuantity)
    {
        if (self::$lastStockAvailableQuantity !== null && self::$lastStockAvailableCanOrder !== null) {
            $deltaQuantity = $newQuantity - self::$lastStockAvailableQuantity;
            $inStockStateChange = ((int) $newQuantity === 0 || (int) self::$lastStockAvailableQuantity === 0) && $deltaQuantity !== 0;
            $product->id_product_attribute = (int) $id_product_attribute;
            $canOrder = $product->checkQty(1);
            if ($canOrder !== self::$lastStockAvailableCanOrder || $inStockStateChange) {
                if ($canOrder) {
                    // Refresh like a product update
                    if (Configuration::get('pagecache_instockisadd')) {
                        $this->onProductAdd($product, 'Product#' . $product->id . ($id_product_attribute ? '-' . $id_product_attribute : '') . ' is now available for order. Qty: ' . self::$lastStockAvailableQuantity . ' => ' . $newQuantity . ' - canOrder: ' . ($canOrder ? 'true' : 'false') . ' => ' . (self::$lastStockAvailableCanOrder ? 'true' : 'false'));
                    } else {
                        $this->onProductUpdate($product, 'Product#' . $product->id . ($id_product_attribute ? '-' . $id_product_attribute : '') . ' is now available for order. Qty: ' . self::$lastStockAvailableQuantity . ' => ' . $newQuantity . ' - canOrder: ' . ($canOrder ? 'true' : 'false') . ' => ' . (self::$lastStockAvailableCanOrder ? 'true' : 'false'), false);
                    }
                } else {
                    // Refresh like a product deletion
                    $this->onProductDelete($product, 'Product#' . $product->id . ($id_product_attribute ? '-' . $id_product_attribute : '') . ' is no more available for order.. Qty: ' . self::$lastStockAvailableQuantity . ' => ' . $newQuantity . ' - canOrder: ' . ($canOrder ? 'true' : 'false') . ' => ' . (self::$lastStockAvailableCanOrder ? 'true' : 'false'));
                }
            } else {
                if ($deltaQuantity !== 0) {
                    $lastItemsQuantities = max(1, (int) Configuration::get('PS_LAST_QTIES'));
                    if (JprestaUtils::version_compare(_PS_VERSION_, '1.7.3.0', '>=')) {
                        $lastItemsQuantities = max(1, (int) $product->low_stock_threshold, $lastItemsQuantities);
                        if (((int) $id_product_attribute) > 0) {
                            $lastItemsQuantitiesAttribute = (int) JprestaUtils::dbGetValue(
                                'SELECT pa.low_stock_threshold' .
                                ' FROM `' . _DB_PREFIX_ . 'product_attribute` pa' .
                                ' WHERE pa.`id_product` = ' . (int) $product->id . ' AND  pa.`id_product_attribute` = ' . (int) $id_product_attribute
                            );
                            $lastItemsQuantities = max(1, $lastItemsQuantities, $lastItemsQuantitiesAttribute);
                        }
                    }
                    if (self::$lastStockAvailableQuantity > $lastItemsQuantities && $newQuantity > $lastItemsQuantities) {
                        // It was and it is still over the limit of alert so we only refresh the product page
                        // every X sales (by default X = 1)
                        $everyX = max(1, (int) Configuration::get('pagecache_product_refreshEveryX', null, null, null, 1));
                        if ((($newQuantity - $lastItemsQuantities) % $everyX) === 0) {
                            $this->onProductUpdate($product, 'stock update (every X=' . $everyX . ')');
                        }
                    } else {
                        // It is or it was under the limit of alert so we refresh the product page
                        $this->onProductUpdate($product, 'stock update (alert=' . $lastItemsQuantities . ')');
                    }
                }
            }
        }
        self::$lastStockAvailableCanOrder = null;
        self::$lastStockAvailableQuantity = null;
    }

    public function hookActionObjectAddAfter($params)
    {
        $controllers = self::getManagedControllers();
        $objectClassName = get_class($params['object']);
        if (array_key_exists($objectClassName, self::$managed_object_classes)) {
            foreach ($controllers as $controllerName => $controllerInfos) {
                if (isset($controllerInfos['object_class']) && $controllerInfos['object_class'] === $objectClassName) {
                    PageCacheDAO::clearCacheOfObject($controllerName, $params['object']->id, false,
                        'creation of page by module "' . $controllerInfos['module'] . '" for object ' . $objectClassName . '#' . $params['object']->id,
                        Configuration::get('pagecache_logs'));
                }
            }
        }
        if ($objectClassName === 'GroupincConfiguration') {
            $this->onGroupincConfigurationChange($params['object']->id);
        }
        if ($objectClassName === 'StBlogCommentClass') {
            PageCacheDAO::clearCacheOfObject('stblog__article', $params['object']->id_st_blog, false, 'new comment', Configuration::get('pagecache_logs'));
        } elseif ($objectClassName === 'StBlogClass') {
            PageCacheDAO::clearCacheOfObject('stblog__category', $params['object']->id_st_blog_category_default, false, 'new article', Configuration::get('pagecache_logs'));
            PageCacheDAO::clearCacheOfObject('stblog__default', null, false, 'new article', Configuration::get('pagecache_logs'));
            PageCacheDAO::clearCacheOfObject('stblogarchives__default', null, false, 'new article', Configuration::get('pagecache_logs'));
        } elseif ($objectClassName === 'StBlogCategory') {
            PageCacheDAO::clearCacheOfObject('stblog__default', null, false, 'new category', Configuration::get('pagecache_logs'));
            PageCacheDAO::clearCacheOfObject('stblogarchives__default', null, false, 'new category', Configuration::get('pagecache_logs'));
        }
    }

    public function hookActionObjectUpdateBefore($params)
    {
        $objectClassName = get_class($params['object']);
        if ($objectClassName === 'GroupincConfiguration') {
            $this->onGroupincConfigurationChange($params['object']->id);
        }
    }

    public function hookActionObjectUpdateAfter($params)
    {
        $controllers = self::getManagedControllers();
        $objectClassName = get_class($params['object']);
        if (array_key_exists($objectClassName, self::$managed_object_classes)) {
            foreach ($controllers as $controllerName => $controllerInfos) {
                if (isset($controllerInfos['object_class']) && $controllerInfos['object_class'] === $objectClassName) {
                    PageCacheDAO::clearCacheOfObject($controllerName, $params['object']->id, true,
                        'modification of page by module "' . $controllerInfos['module'] . '" for object ' . $objectClassName . '#' . $params['object']->id,
                        Configuration::get('pagecache_logs'));
                }
            }
        }
        if ($objectClassName === 'GroupincConfiguration') {
            $this->onGroupincConfigurationChange($params['object']->id);
        }
        if ($objectClassName === 'StBlogCommentClass') {
            PageCacheDAO::clearCacheOfObject('stblog__article', $params['object']->id_st_blog, false, 'update comment', Configuration::get('pagecache_logs'));
        } elseif ($objectClassName === 'StBlogClass') {
            PageCacheDAO::clearCacheOfObject('stblog__category', $params['object']->id_st_blog_category_default, false, 'update article', Configuration::get('pagecache_logs'));
            PageCacheDAO::clearCacheOfObject('stblog__default', null, false, 'update article', Configuration::get('pagecache_logs'));
            PageCacheDAO::clearCacheOfObject('stblogarchives__default', null, false, 'update article', Configuration::get('pagecache_logs'));
        } elseif ($objectClassName === 'StBlogCategory') {
            PageCacheDAO::clearCacheOfObject('stblog__default', null, false, 'update category', Configuration::get('pagecache_logs'));
            PageCacheDAO::clearCacheOfObject('stblogarchives__default', null, false, 'update category', Configuration::get('pagecache_logs'));
        }
    }

    private function onGroupincConfigurationChange($id_config) {
        try {
            $config = new GroupincConfiguration((int)$id_config);
            if (\Validate::isLoadedObject($config) && method_exists($config, 'getProductsExport')) {
                $list_products = GroupincConfiguration::getProductsExport($config);
                foreach ($list_products as $product) {
                    $this->onProductUpdate($product['id_product'], 'groupinc', false);
                }
            }
        }
        catch (Exception $e) {
            // Ignore it
        }
    }

    public function hookActionObjectDeleteBefore($params)
    {
        $objectClassName = get_class($params['object']);
        if ($objectClassName === 'GroupincConfiguration') {
            $this->onGroupincConfigurationChange($params['object']->id);
        }
    }

    public function hookActionObjectDeleteAfter($params)
    {
        $this->hookActionObjectUpdateAfter($params);
    }

    public function hookActionObjectAddressAddAfter($params)
    {
        if (isset($params['object']) && !empty($params['object']->id_supplier)) {
            $this->_clearCacheModules('pagecache_supplier_a', 'hookActionObjectAddressAddAfter');
        }
    }

    public function hookActionObjectAddressUpdateAfter($params)
    {
        if (isset($params['object']) && !empty($params['object']->id_supplier)) {
            PageCacheDAO::clearCacheOfObject('supplier', $params['object']->id_supplier, Configuration::get('pagecache_supplier_u_bl'), 'hookActionObjectAddressUpdateAfter', Configuration::get('pagecache_logs'));
            $this->_clearCacheModules('pagecache_supplier_u', 'hookActionObjectAddressUpdateAfter');
        }
    }

    public function hookActionObjectAddressDeleteBefore($params)
    {
        if (isset($params['object']) && !empty($params['object']->id_supplier)) {
            PageCacheDAO::clearCacheOfObject('supplier', $params['object']->id_supplier, Configuration::get('pagecache_supplier_d_bl'), 'hookActionObjectAddressDeleteBefore', Configuration::get('pagecache_logs'));
            $this->_clearCacheModules('pagecache_supplier_d', 'hookActionObjectAddressDeleteBefore');
        }
    }

    public function hookActionCategoryAdd($params)
    {
        if (isset($params['category'])) {
            PageCacheDAO::clearCacheOfObject('category', $params['category']->id, false, 'creation of Category#' . $params['category']->id, Configuration::get('pagecache_logs'));
            $this->_checkRootCategory($params['category']->id, 'a', 'creation of Category#' . $params['category']->id);
            $this->_clearCacheModules('pagecache_category_a', 'creation of Category#' . $params['category']->id);
        }
    }

    public function hookActionCategoryUpdate($params)
    {
        if (isset($params['category'])) {
            if (is_int($params['category'])) {
                // StoreCommander send the ID
                $id_category = $params['category'];
            }
            else {
                $id_category = (int) $params['category']->id;
            }
            PageCacheDAO::clearCacheOfObject('category', $id_category, Configuration::get('pagecache_category_u_bl'), 'modification of Category#' . $id_category, Configuration::get('pagecache_logs'));
            $this->_checkRootCategory($id_category, 'u', 'modification of Category#' . $id_category);
            $this->_clearCacheModules('pagecache_category_u', 'modification of Category#' . $id_category);
        }
    }

    public function hookActionCategoryDelete($params)
    {
        if (isset($params['category'])) {
            PageCacheDAO::clearCacheOfObject('category', $params['category']->id, Configuration::get('pagecache_category_d_bl'), 'deletion of Category#' . $params['category']->id, Configuration::get('pagecache_logs'));
            $this->_checkRootCategory($params['category']->id, 'd', 'deletion of Category#' . $params['category']->id);
            $this->_clearCacheModules('pagecache_category_d', 'deletion of Category#' . $params['category']->id);
        }
    }

    public function onProductAdd($product, $logMessage)
    {
        // New products pages
        PageCacheDAO::clearCacheOfObject('newproducts', null, false, $logMessage, Configuration::get('pagecache_logs'));

        // Categories of the new product
        $categoriesIds = $product->getCategories();
        foreach ($categoriesIds as $categoryId) {
            PageCacheDAO::clearCacheOfObject('category', $categoryId, false, $logMessage, Configuration::get('pagecache_logs'));
            $this->_checkRootCategory($categoryId, 'a', $logMessage);
        }

        // Supplier pages
        PageCacheDAO::clearCacheOfObject('supplier', $product->id_supplier, false, $logMessage, Configuration::get('pagecache_logs'));

        // Manufacturer pages
        PageCacheDAO::clearCacheOfObject('manufacturer', $product->id_manufacturer, false, $logMessage, Configuration::get('pagecache_logs'));

        // Modules attached to this hook
        $this->_clearCacheModules('pagecache_product_a', $logMessage);
    }

    public function onProductUpdate($product, $logMessage, $onlyProductPage = true)
    {
        if (is_numeric($product)) {
            $productId = $product;
        } else {
            $productId = $product->id;
        }

        // Product page
        PageCacheDAO::clearCacheOfObject('product', $productId, !$onlyProductPage, $logMessage, Configuration::get('pagecache_logs'));

        if (!$onlyProductPage) {
            if (is_numeric($product)) {
                $product = new Product($productId);
            }

            // New products pages
            PageCacheDAO::clearCacheOfObject('newproducts', null, false, $logMessage, Configuration::get('pagecache_logs'));

            // Categories of the new product
            $categoriesIds = $product->getCategories();
            foreach ($categoriesIds as $categoryId) {
                PageCacheDAO::clearCacheOfObject('category', $categoryId, false, $logMessage, Configuration::get('pagecache_logs'));
                $this->_checkRootCategory($categoryId, 'a', $logMessage);
            }

            // Supplier pages
            PageCacheDAO::clearCacheOfObject('supplier', $product->id_supplier, false, $logMessage, Configuration::get('pagecache_logs'));

            // Manufacturer pages
            PageCacheDAO::clearCacheOfObject('manufacturer', $product->id_manufacturer, false, $logMessage, Configuration::get('pagecache_logs'));

            // Modules attached to this hook
            $this->_clearCacheModules('pagecache_product_u', $logMessage);
        }
    }

    public function onProductDelete($product, $logMessage)
    {
        // Product page
        PageCacheDAO::clearCacheOfObject('product', $product->id, Configuration::get('pagecache_product_d_bl'), $logMessage, Configuration::get('pagecache_logs'));

        // Categories of the new product
        $categoriesIds = $product->getCategories();
        foreach ($categoriesIds as $categoryId) {
            PageCacheDAO::clearCacheOfObject('category', $categoryId, false, $logMessage, Configuration::get('pagecache_logs'));
            $this->_checkRootCategory($categoryId, 'd', $logMessage);
        }

        // Supplier pages
        PageCacheDAO::clearCacheOfObject('supplier', $product->id_supplier, false, $logMessage, Configuration::get('pagecache_logs'));

        // Manufacturer pages
        PageCacheDAO::clearCacheOfObject('manufacturer', $product->id_manufacturer, false, $logMessage, Configuration::get('pagecache_logs'));

        // Modules attached to this hook
        $this->_clearCacheModules('pagecache_product_d', $logMessage);
    }

    private static $updatingProductFromAdminController = false;
    private static $lastUpdatedProduct;
    private static $lastUpdatedProductFeatures;
    private static $lastUpdatedProductStockAvailable;

    public function hookActionAdminProductsControllerSaveBefore($params)
    {
        $this->hookActionObjectProductUpdateBefore(['object' => (object) ['id' => Tools::getValue('id_product')]]);
        self::$updatingProductFromAdminController = true;
        // The 'after' will be done in hookActionDispatcherAfter
    }

    public function hookActionAdminSaveBefore($params)
    {
        // The updateCacheKey will be done in the "hookActionAdminSaveAfter" hook
        self::$skipUpdateCacheKey = true;
    }

    public function hookActionObjectProductUpdateBefore($params)
    {
        if (isset($params['object']) && !self::$updatingProductFromAdminController) {
            $newProduct = $params['object'];
            // Load the current product from the database and keep it for hookActionObjectProductUpdateAfter
            self::$lastUpdatedProduct = new Product($newProduct->id);
            self::$lastUpdatedProductFeatures = Product::getFrontFeaturesStatic((int) Configuration::get('PS_LANG_DEFAULT'), $newProduct->id);
            self::$lastUpdatedProductStockAvailable = null;
            if (Tools::getIsset('out_of_stock')) {
                // Check if out_of_stock has been modified
                $currentStockId = (int) StockAvailable::getStockAvailableIdByProductId((int) $newProduct->id);
                if ($currentStockId) {
                    // 'out_of_stock' is not stored in ps_product table but in StockAvailable
                    self::$lastUpdatedProductStockAvailable = new StockAvailable($currentStockId);
                }
            }
        }
    }

    public function hookActionObjectProductUpdateAfter($params)
    {
        if (isset($params['object']) && !self::$updatingProductFromAdminController) {
            $updatedProduct = $params['object'];
            // Compare with database version because of boolean stored as int, null as empty, integer being formatted, etc.
            $productFromDb = new Product($updatedProduct->id);
            $diffs = JprestaUtils::getObjectDifferences(self::$lastUpdatedProduct, $productFromDb);
            if (!is_array($diffs) || count($diffs) === 0) {
                // Check modifications in features if necessary
                if (method_exists('Product', 'resetStaticCache')) {
                    Product::resetStaticCache();
                }
                $diffsInFeatures = !JprestaUtils::arraysAreIdentical(self::$lastUpdatedProductFeatures, Product::getFrontFeaturesStatic((int) Configuration::get('PS_LANG_DEFAULT'), $productFromDb->id));
            } else {
                $diffsInFeatures = false;
            }
            if (self::$lastUpdatedProductStockAvailable
                && self::$lastUpdatedProductStockAvailable->out_of_stock != Tools::getValue('out_of_stock')) {
                $diffs['out_of_stock'] = JprestaUtils::toString(self::$lastUpdatedProductStockAvailable->out_of_stock) . ' <> ' . JprestaUtils::toString(Tools::getValue('out_of_stock'));
            }
            self::$lastUpdatedProduct = null;
            if ((is_array($diffs) && count($diffs) > 0) || $diffsInFeatures) {
                if (array_key_exists('active', $diffs)) {
                    if ($updatedProduct->active) {
                        // Product is back, act like a new product
                        $this->onProductAdd($updatedProduct, 'activation of Product#' . $updatedProduct->id);
                    } else {
                        // Product is disabled, act like a deletion
                        $this->onProductDelete($updatedProduct, 'deactivation of Product#' . $updatedProduct->id);
                    }
                } else {
                    // The product has been modified, it can be the name, description, price, url, etc. so we need to
                    // refresh all pages where the product is listed or displayed
                    $this->onProductUpdate($updatedProduct, 'modification of Product#' . $updatedProduct->id, false);
                }
            }
        }
    }

    public function hookActionProductUpdate($params)
    {
        // Only refresh cache if we are in StoreCommander, otherwise it will be done in hookActionObjectProductUpdateAfter
        if (JprestaUtils::strpos($_SERVER['REQUEST_URI'], 'storecommander') !== false) {
            if (!isset($params['product']) && isset($params['id_product'])) {
                $params['product'] = new Product($params['id_product']);
            }
            if (isset($params['product'])) {
                $updatedProduct = $params['product'];
                // The product has been modified, it can be the name, description, price, url, etc. so we need to
                // refresh all pages where the product is listed or displayed
                $this->onProductUpdate($updatedProduct, 'modification of Product#' . $updatedProduct->id . ' via StoreCommander', false);
            }
        }
    }

    public function hookActionProductAdd($params)
    {
        if (!isset($params['product']) && isset($params['id_product'])) {
            $params['product'] = new Product($params['id_product']);
        }
        if (isset($params['product'])) {
            $product = $params['product'];
            $this->onProductAdd($product, 'creation of new Product#' . $product->id);
        }
    }

    public function hookActionObjectProductDeleteBefore($params)
    {
        if (isset($params['object'])) {
            $product = $params['object'];
            $this->onProductDelete($params['object'], 'deletion of Product#' . $product->id);
        }
    }

    private static $lastUpdatedProductCombination;

    public function hookActionObjectCombinationUpdateBefore($params)
    {
        if (isset($params['object'])) {
            $newCombination = $params['object'];
            // Load the current product from the database and keep it for hookActionObjectCombinationUpdateAfter
            self::$lastUpdatedProductCombination = new Combination($newCombination->id);
        }
    }

    public function hookActionObjectCombinationUpdateAfter($params)
    {
        if (isset($params['object'])) {
            $updatedCombination = $params['object'];
            $combinationFromDb = new Combination($updatedCombination->id);
            $diffs = JprestaUtils::getObjectDifferences(self::$lastUpdatedProductCombination, $combinationFromDb);
            self::$lastUpdatedProductCombination = null;
            if (is_array($diffs) && count($diffs) > 0) {
                // A combination has been modified (impact on price, weight, minimal quantity, etc. so we just need
                // to refresh the product page, no pages that list this product.

                // Product page
                PageCacheDAO::clearCacheOfObject('product', $updatedCombination->id_product, false, 'modification of Combination#' . $updatedCombination->id, Configuration::get('pagecache_logs'));
            }
        }
    }

    public function hookActionObjectCombinationDeleteAfter($params)
    {
        if (isset($params['object'])) {
            $deletedCombination = $params['object'];

            // A combination has been deleted so we just need
            // to refresh the product page, no pages that list this product.

            // Product page
            PageCacheDAO::clearCacheOfObject('product', $deletedCombination->id_product, false, 'deletion of Combination#' . $deletedCombination->id, Configuration::get('pagecache_logs'));
        }
    }

    public function hookActionObjectSpecificPriceAddAfter($params)
    {
        if (isset($params['object'])) {
            $sp = $params['object'];
            PageCacheDAO::insertSpecificPrice($sp->id, $sp->id_product, $sp->from, $sp->to);
            if ($sp->id_product === 0) {
                // Specific case where the specific rule is global (all products are concerned)
                PageCacheDAO::triggerReffreshment();
            } else {
                $this->onProductUpdate($sp->id_product, 'creation of specific price #' . $sp->id);
            }
        }
        if (!self::$skipUpdateCacheKey) {
            self::updateCacheKeyForCountries();
            self::updateCacheKeyForUserGroups();
        } else {
            self::$needUpdateCacheKey = true;
        }
    }

    public function hookActionObjectSpecificPriceUpdateAfter($params)
    {
        if (isset($params['object'])) {
            $sp = $params['object'];
            PageCacheDAO::updateSpecificPrice($sp->id, $sp->id_product, $sp->from, $sp->to);
            if ($sp->id_product === 0) {
                // Specific case where the specific rule is global (all products are concerned)
                PageCacheDAO::triggerReffreshment();
            } else {
                $this->onProductUpdate($sp->id_product, 'modification of specific price #' . $sp->id);
            }
        }
        if (!self::$skipUpdateCacheKey) {
            self::updateCacheKeyForCountries();
            self::updateCacheKeyForUserGroups();
        } else {
            self::$needUpdateCacheKey = true;
        }
    }

    public function hookActionObjectSpecificPriceDeleteBefore($params)
    {
        if (isset($params['object'])) {
            $sp = $params['object'];
            PageCacheDAO::deleteSpecificPrice($sp->id);
            if ($sp->id_product === 0) {
                // Specific case where the specific rule is global (all products are concerned)
                PageCacheDAO::triggerReffreshment();
            } else {
                $this->onProductUpdate($sp->id_product, 'deletion of specific price #' . $sp->id);
            }
        }
    }

    public function hookActionObjectImageAddAfter($params)
    {
        if (isset($params['object'])) {
            $img = $params['object'];
            $this->onProductUpdate($img->id_product, 'new image', !$img->cover);
        }
    }

    public function hookActionObjectImageUpdateAfter($params)
    {
        if (isset($params['object'])) {
            $img = $params['object'];
            $this->onProductUpdate($img->id_product, 'modification of an image', !$img->cover);
        }
    }

    public function hookActionObjectImageDeleteBefore($params)
    {
        if (isset($params['object'])) {
            $img = $params['object'];
            $this->onProductUpdate($img->id_product, 'deletion of an image', !$img->cover);
        }
    }

    private function _checkRootCategory($id_category, $suffix, $origin_action = '')
    {
        if ((bool) JprestaUtils::dbGetValue('SELECT `id_shop` FROM `' . _DB_PREFIX_ . 'shop` WHERE `id_category` = ' . (int) $id_category)) {
            $this->_clearCacheModules('pagecache_product_home_' . $suffix, $origin_action);
        }
    }

    public function hookActionHtaccessCreate($params)
    {
        // SPEEDPACK
        $this->jpresta_submodules['JprestaWebpModule']->updateHtaccessFile();
        // SPEEDPACK£
        $this->clearCache('hookActionHtaccessCreate');
    }

    public function hookActionObjectShopUrlAddAfter($params)
    {
        $this->hookActionHtaccessCreate($params);
    }

    public function hookActionObjectShopUrlUpdateAfter($params)
    {
        $this->hookActionHtaccessCreate($params);
    }

    public function hookActionObjectShopUrlDeleteAfter($params)
    {
        $this->hookActionHtaccessCreate($params);
    }

    public function hookActionAdminPerformanceControllerAfter($params)
    {
        $this->clearCache('hookActionAdminPerformanceControllerAfter');
    }

    /**
     * $params['controller'] : the name of the controller as it appears in the statistics table (for modules it is <module_name>__<controller_name>)
     * $params['id'] : ID of the object as it appears in the statistics table. Can be null.
     * $params['delete_linking_pages'] : true if pages having a link on modified pages should also be refreshed
     * $params['action_origin'] : For debug purpose, just tells shortly why the cache is refreshed
     *
     * @param $params array
     */
    public function hookActionJPrestaClearCache($params)
    {
        if ($params && is_array($params)) {
            $controller_name = isset($params['controller']) ? $params['controller'] : null;
            if ($controller_name) {
                $id_object = isset($params['id']) ? $params['id'] : null;
                $delete_linking_pages = isset($params['delete_linking_pages']) ? (bool) $params['delete_linking_pages'] : true;
                $action_origin = 'Hook ActionJPrestaClearCache' . (isset($params['action_origin']) ? ': ' . $params['action_origin'] : '');
                $log_level = Configuration::get('pagecache_logs');
                PageCacheDAO::clearCacheOfObject($controller_name, $id_object, $delete_linking_pages, $action_origin,
                    $log_level);
            }
        }
    }

    public function removeOverride($class_name)
    {
        static $already_done = [];
        if (array_key_exists($class_name, $already_done)) {
            return true;
        }
        $already_done[$class_name] = true;
        if (JprestaUtils::version_compare(_PS_VERSION_, '1.6', '<')
            && !file_exists(_PS_MODULE_DIR_ . '/' . $this->name . '/override/controllers/front/' . $class_name . '.php')
            && !file_exists(_PS_MODULE_DIR_ . '/' . $this->name . '/override/classes/' . $class_name . '.php')
            && !file_exists(_PS_MODULE_DIR_ . '/' . $this->name . '/override/classes/controller/' . $class_name . '.php')
            && !file_exists(_PS_MODULE_DIR_ . '/' . $this->name . '/override/classes/module/' . $class_name . '.php')
        ) {
            // In PS 1.5 we cannot remove an override that is not defined in /overrides directory
            // So they stay installed but it's better than an error during upgrade
            return true;
        }

        return parent::removeOverride($class_name);
    }

    public function upgradeOverride($class_name)
    {
        // Avoid calling this method multiple times (or it will fail)
        static $already_done = [];
        if (array_key_exists($class_name, $already_done)) {
            return true;
        }
        $already_done[$class_name] = true;

        if (!file_exists(_PS_MODULE_DIR_ . '/' . $this->name . '/override/controllers/front/' . $class_name . '.php')
            && !file_exists(_PS_MODULE_DIR_ . '/' . $this->name . '/override/classes/' . $class_name . '.php')
            && !file_exists(_PS_MODULE_DIR_ . '/' . $this->name . '/override/classes/controller/' . $class_name . '.php')
            && !file_exists(_PS_MODULE_DIR_ . '/' . $this->name . '/override/classes/module/' . $class_name . '.php')
        ) {
            // The override does not exist anymore, just ignore it. It can happen in old upgrade file.
            return true;
        }

        $reset_ok = true;
        if (JprestaUtils::version_compare(_PS_VERSION_, '1.6', '>=')
            || (!class_exists($class_name . 'OverrideOriginal') && (!class_exists($class_name . 'OverrideOriginal_remove')))) {
            $reset_ok = $this->removeOverride($class_name) && $this->addOverride($class_name);
        }

        return $reset_ok;
    }

    /** @return bool true if infos block must be displayed on front end */
    private static function isDisplayStats()
    {
        if (JprestaUtils::isAjax()
            || strcmp(self::getServerValue('REQUEST_METHOD'), 'GET') != 0
            || defined('_PS_ADMIN_DIR_')
        ) {
            return false;
        }

        return Configuration::get('pagecache_always_infosbox')
            || (Configuration::get('pagecache_debug') && Tools::getIsset('dbgpagecache'));
    }

    public function getContactUrl()
    {
        $seller = Configuration::get('pagecache_seller');
        if (isset($seller) && strcmp($seller, 'addons') === 0) {
            // Contact URL
            if (strcmp('fr', Language::getIsoById($this->context->language->id)) == 0) {
                return 'https://addons.prestashop.com/fr/ecrire-au-developpeur?id_product=7939';
            } else {
                return 'https://addons.prestashop.com/en/write-to-developper?id_product=7939';
            }
        } else {
            // Contact URL
            if (strcmp('fr', Language::getIsoById($this->context->language->id)) == 0) {
                return self::JPRESTA_PROTO . self::JPRESTA_DOMAIN . '.com/fr/contactez-nous';
            } else {
                return self::JPRESTA_PROTO . self::JPRESTA_DOMAIN . '.com/en/contact-us';
            }
        }
    }

    /**
     * Used in case script is run with a command line
     *
     * @param string $key Variable name
     *
     * @return string Value of variable or empty string
     */
    public static function getServerValue($key)
    {
        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }

        return '';
    }

    public function l($string, $specific = false, $locale = null)
    {
        if ($specific === 'Admin.Global') {
            $parameters['legacy'] = 'htmlspecialchars';

            return $this->getTranslator()->trans($string, $parameters, $specific, $locale);
        } else {
            return parent::l($string, $specific, $locale);
        }
    }

    /**
     * @deprecated Just needed when upgrading to 4.00, do not remove it
     */
    public static function getCacheFile()
    {
        return false;
    }

    /**
     * @deprecated Just needed when upgrading to 4.25, do not remove it
     */
    public static function getDynamicHookInfos()
    {
        return false;
    }

    /**
     * @deprecated Just needed when upgrading to 4.25, do not remove it
     */
    public static function getHookCacheDirectives()
    {
        return ['wrapper' => false, 'content' => true];
    }

    /**
     * @deprecated Just needed when upgrading to 4.25, do not remove it
     */
    public static function getWidgetCacheDirectives()
    {
        return ['wrapper' => false, 'content' => true];
    }

    /**
     * @deprecated Just needed when upgrading, do not remove it
     */
    public static function isDynamicHooks()
    {
        return false;
    }

    /**
     * @return void
     */
    public function installTabs()
    {
        if ($this->isSpeedPack()) {
            $this->installTab('AdminParentSpeedPack', 'JPresta - Speed pack', (int) Tab::getIdFromClassName('AdminAdvancedParameters'));
            $this->installTab('AdminPageCacheConfiguration', 'Page Cache Ultimate', (int) Tab::getIdFromClassName('AdminParentSpeedPack'));
            $this->installTab('AdminJprestaWebpConfiguration', [
                'en' => 'Compression of images',
                'fr' => 'Compression des images',
                'es' => 'Compresión de imágenes',
            ], (int) Tab::getIdFromClassName('AdminParentSpeedPack'));
            $this->installTab('AdminJprestaSQLProfilerConfiguration', [
                'en' => 'SQL Profiler',
                'fr' => 'Profilage SQL',
                'es' => 'SQL Profiler',
            ], (int) Tab::getIdFromClassName('AdminParentSpeedPack'));
            $this->installTab('AdminJprestaLazyLoadingConfiguration', [
                'en' => 'Lazy load of images',
                'fr' => 'Chargement différé des images',
                'es' => 'Carga bajo demanda de imágenes',
            ], (int) Tab::getIdFromClassName('AdminParentSpeedPack'));
            $this->installTab('AdminJprestaDbOptimizerConfiguration', [
                'en' => 'Database optimisation',
                'fr' => 'Nettoyage de la base de données',
                'es' => 'Limpieza de la base de datos',
            ], (int) Tab::getIdFromClassName('AdminParentSpeedPack'));
        } else {
            if (JprestaUtils::version_compare(_PS_VERSION_, '1.6', '>')) {
                $idTab = (int) Tab::getIdFromClassName('AdminAdvancedParameters');
                if (!$idTab) {
                    $idTab = (int) Tab::getIdFromClassName('AdminTools');
                }
                $this->installTab('AdminPageCacheConfiguration', 'JPresta - Page Cache Ultimate', $idTab);
            } elseif (JprestaUtils::version_compare(_PS_VERSION_, '1.5', '>')) {
                $this->installTab('AdminPageCacheConfiguration', 'JPresta - Page Cache Ultimate', 17);
            }
        }
        $this->installTab('AdminPageCacheMemcachedTest');
        $this->installTab('AdminPageCacheMemcacheTest');
        $this->installTab('AdminPageCacheProfilingDatas');
        $this->installTab('AdminPageCacheDatas');
    }

    public static function canBeMarkedAsDynamic($hook_name)
    {
        $hookName = Tools::strtolower($hook_name);
        if (strpos($hookName, 'hook') === 0) {
            // remove 'hook' prefix
            $hookName = Tools::substr($hookName, 4);
        }

        return !(
            (JprestaUtils::strpos($hookName, 'action') === 0 && strcmp($hookName, 'actionproductoutofstock') !== 0)
            || JprestaUtils::strpos($hookName, 'dashboard') === 0
            || JprestaUtils::strpos($hookName, 'displayadmin') === 0
            || JprestaUtils::strpos($hookName, 'displaybackoffice') === 0
            || in_array($hookName, [
                'header',
                'displayheader',
                'displaypaymentreturn',
                'registergdprconsent',
                'moduleroutes',
                'overridelayouttemplate',
                'displayoverridetemplate',
                'additionalcustomerformfields',
                'payment',
                'gridengine', 'graphengine', 'productsearchprovider', 'filterproductcontent', 'deleteproductattribute',
            ])
        );
    }

    /**
     * @return void
     */
    public function registerHooks()
    {
        // Register hooks
        $this->registerHook('displayAdminAfterHeader');
        if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->registerHook('actionDispatcherBefore');
            $this->registerHook('actionDispatcherAfter');
            $this->registerHook('actionOutputHTMLBefore');
            // SPEEDPACK
            $this->registerHook('actionAjaxDieSearchControllerdoProductSearchBefore');
            $this->registerHook('actionAjaxDieCategoryControllerdoProductSearchBefore');
            $this->registerHook('actionOnImageResizeAfter');
            // SPEEDPACK£
        }
        // SPEEDPACK
        $this->registerHook('actionObjectShopUrlAddAfter');
        $this->registerHook('actionObjectShopUrlUpdateAfter');
        $this->registerHook('actionObjectShopUrlDeleteAfter');
        // SPEEDPACK£
        if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->registerHook('actionAdminProductsControllerSaveAfter');
        }
        $this->registerHook('actionDispatcher');
        $this->registerHook('displayHeader');
        if (JprestaUtils::version_compare(_PS_VERSION_, '1.6', '>')) {
            $hookHeaderId = Hook::getIdByName('Header');
            $this->updatePosition($hookHeaderId, 0, 1);
        }
        $this->registerHook('actionTaxManager');
        if (JprestaUtils::version_compare(_PS_VERSION_, '1.6', '>')) {
            $hookHeaderId = Hook::getIdByName('actionTaxManager');
            $this->updatePosition($hookHeaderId, 0, 1);
        }
        $this->registerHook('actionJPrestaClearCache');
        $this->registerHook('displayMobileHeader');
        $this->registerHook('actionAdminSaveBefore');
        $this->registerHook('actionAdminProductsControllerSaveBefore');
        $this->registerHook('actionObjectAddAfter');
        $this->registerHook('actionObjectUpdateBefore');
        $this->registerHook('actionObjectUpdateAfter');
        $this->registerHook('actionObjectDeleteBefore');
        $this->registerHook('actionObjectDeleteAfter');
        $this->registerHook('actionCategoryAdd');
        $this->registerHook('actionCategoryUpdate');
        $this->registerHook('actionCategoryDelete');
        $this->registerHook('actionObjectCmsAddAfter');
        $this->registerHook('actionObjectCmsUpdateAfter');
        $this->registerHook('actionObjectCmsDeleteBefore');
        $this->registerHook('actionObjectStockAvailableUpdateBefore');
        $this->registerHook('actionObjectStockAvailableUpdateAfter');
        $this->registerHook('actionObjectStockAddBefore');
        $this->registerHook('actionObjectStockAddAfter');
        $this->registerHook('actionObjectStockUpdateBefore');
        $this->registerHook('actionObjectStockUpdateAfter');
        $this->registerHook('actionObjectWarehouseProductLocationAddBefore');
        $this->registerHook('actionObjectWarehouseProductLocationAddAfter');
        $this->registerHook('actionObjectWarehouseProductLocationDeleteBefore');
        $this->registerHook('actionObjectWarehouseProductLocationDeleteAfter');
        $this->registerHook('actionObjectManufacturerAddAfter');
        $this->registerHook('actionObjectManufacturerUpdateAfter');
        $this->registerHook('actionObjectManufacturerDeleteBefore');
        $this->registerHook('actionObjectAddressAddAfter');
        $this->registerHook('actionObjectAddressUpdateAfter');
        $this->registerHook('actionObjectAddressDeleteBefore');
        $this->registerHook('actionAttributeSave');
        $this->registerHook('actionAttributeDelete');
        $this->registerHook('actionAttributeGroupDelete');
        $this->registerHook('actionAttributeGroupSave');
        $this->registerHook('actionFeatureSave');
        $this->registerHook('actionFeatureDelete');
        $this->registerHook('actionFeatureValueSave');
        $this->registerHook('actionFeatureValueDelete');
        $this->registerHook('actionProductAdd');
        $this->registerHook('actionObjectProductUpdateBefore');
        $this->registerHook('actionObjectProductUpdateAfter');
        $this->registerHook('actionObjectProductDeleteBefore');
        $this->registerHook('actionProductUpdate');
        $this->registerHook('actionObjectCombinationUpdateBefore');
        $this->registerHook('actionObjectCombinationUpdateAfter');
        $this->registerHook('actionObjectCombinationDeleteAfter');
        $this->registerHook('actionHtaccessCreate');
        $this->registerHook('actionAdminPerformanceControllerAfter');
        // New shop creation
        $this->registerHook('actionShopDataDuplication');
        // Add hook for specific prices
        $this->registerHook('actionObjectSpecificPriceAddAfter');
        $this->registerHook('actionObjectSpecificPriceUpdateAfter');
        $this->registerHook('actionObjectSpecificPriceDeleteBefore');
        $this->registerHook('actionObjectSpecificPriceDeleteAfter');
        // Hook called when images are changed
        $this->registerHook('actionObjectImageAddAfter');
        $this->registerHook('actionObjectImageUpdateAfter');
        $this->registerHook('actionObjectImageDeleteBefore');

        $this->registerHook('actionObjectSpecificPriceRuleAddAfter');
        $this->registerHook('actionObjectSpecificPriceRuleUpdateAfter');
        $this->registerHook('actionObjectSpecificPriceRuleDeleteAfter');

        $this->registerHook('actionObjectGroupAddAfter');
        $this->registerHook('actionObjectGroupUpdateAfter');
        $this->registerHook('actionObjectGroupDeleteAfter');

        $this->registerHook('actionCustomerAccountAdd');
        $this->registerHook('actionAuthentication');
        $this->registerHook('actionCustomerLogoutAfter');
    }
}
