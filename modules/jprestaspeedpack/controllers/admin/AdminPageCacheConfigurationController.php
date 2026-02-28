<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'AdminPageCacheDatasController.php';

class AdminPageCacheConfigurationController extends ModuleAdminController
{
    const INSTALL_STEP_INSTALL = 1;
    const INSTALL_STEP_BUY_FROM = 2;
    const INSTALL_STEP_IN_ACTION = 3;
    const INSTALL_STEP_AUTOCONF = 4;
    const INSTALL_STEP_CART = 5;
    const INSTALL_STEP_LOGGED_IN = 6;
    const INSTALL_STEP_EU_COOKIE = 7;
    const INSTALL_STEP_VALIDATE = 8;
    const LAST_INSTALL_STEP = 9;
    const INSTALL_STEP_BACK_TO_TEST = self::INSTALL_STEP_BUY_FROM;

    const DOC_PROTO = 'https://';
    const DOC_DOMAIN = 'docs.google';
    const DOC_URL_FR = '.com/document/d/18AboJ_CGq24Q7Y96NlaWTYwpfWwSSUcrRumhUfTOPdM/edit?usp=sharing';
    const DOC_URL_EN = '.com/document/d/1cMVk6zn2xb3B2PA3UvRsy8rHCCfjzU1fb05vWww9ia8/edit?usp=sharing';

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->bootstrap = true;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        if (method_exists($this, 'addJQuery')) {
            $this->addJquery();
        }
        $this->addJS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/js/bootstrap-slider.js');
        $this->addJS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/js/countUp.js');
        $this->addJS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/js/jquery.dataTables.min.js');
        $this->addJS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/js/dataTables.buttons.min.js');
        $this->addCSS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/css/bootstrap-slider.min.css');
        $this->addCSS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/css/jquery.dataTables.min.css');
        $this->addCSS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/css/buttons.dataTables.min.css');
        $this->addCSS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/css/back.css');
    }

    public function postProcess()
    {
        $msg_errors = [];
        $msg_warnings = [];
        $msg_success = [];
        $msg_infos = [];

        // If we try to update the settings
        if (Tools::isSubmit('submitModule')) {
            $trigered_events = [
                'pagecache_cms_a' => [
                    'title' => $this->module->l('On new CMS', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => false,
                ],
                'pagecache_cms_u' => [
                    'title' => $this->module->l('On CMS update', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => true,
                ],
                'pagecache_cms_d' => [
                    'title' => $this->module->l('On CMS deletion', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => true,
                ],
                'pagecache_supplier_a' => [
                    'title' => $this->module->l('On new supplier', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => false,
                ],
                'pagecache_supplier_u' => [
                    'title' => $this->module->l('On supplier update', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => true,
                ],
                'pagecache_supplier_d' => [
                    'title' => $this->module->l('On supplier deletion', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => true,
                ],
                'pagecache_manufacturer_a' => [
                    'title' => $this->module->l('On new manufacturer', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => false,
                ],
                'pagecache_manufacturer_u' => [
                    'title' => $this->module->l('On manufacturer update', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => true,
                ],
                'pagecache_manufacturer_d' => [
                    'title' => $this->module->l('On manufacturer deletion', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => true,
                ],
                'pagecache_product_a' => [
                    'title' => $this->module->l('On new product', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => false,
                ],
                'pagecache_product_u' => [
                    'title' => $this->module->l('On product update', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => true,
                ],
                'pagecache_product_d' => [
                    'title' => $this->module->l('On product deletion', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => true,
                ],
                'pagecache_product_home_a' => [
                    'title' => $this->module->l('On new home featured product', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => false,
                ],
                'pagecache_product_home_u' => [
                    'title' => $this->module->l('On home featured product update', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => true,
                ],
                'pagecache_product_home_d' => [
                    'title' => $this->module->l('On home featured product deletion', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => true,
                ],
                'pagecache_category_a' => [
                    'title' => $this->module->l('On new category', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => false,
                ],
                'pagecache_category_u' => [
                    'title' => $this->module->l('On category update', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => true,
                ],
                'pagecache_category_d' => [
                    'title' => $this->module->l('On category deletion', 'jprestaspeedpack'),
                    'desc' => '',
                    'bl' => true,
                ],
            ];

            if (_PS_MODE_DEMO_ && !$this->context->employee->isSuperAdmin()) {
                $msg_errors[] = $this->module->l('In DEMO mode you cannot modify the Page Cache configuration.', 'jprestaspeedpack');
            } else {
                $reinstallCache = false;
                $typecacheBefore = JprestaUtils::getConfigurationAllShop('pagecache_typecache');

                //
                // Update Pages and timeouts
                //
                if (Tools::getIsset('submitModuleTimeouts')) {
                    $reinstallCache = true;

                    foreach (Jprestaspeedpack::getManagedControllersNames() as $controller) {
                        $timeoutValue = (int) Tools::getValue('pagecache_' . $controller . '_timeout', 3);
                        if ($timeoutValue === 8) {
                            $timeoutValue = 14;
                        }
                        if ($timeoutValue === 9) {
                            $timeoutValue = 30;
                        }
                        if ($timeoutValue === 0) {
                            Configuration::updateValue('pagecache_' . $controller, 0);
                            Configuration::updateValue('pagecache_' . $controller . '_timeout', 0);
                            Configuration::updateValue('pagecache_' . $controller . '_expires', 0);
                        } else {
                            Configuration::updateValue('pagecache_' . $controller, 1);
                            if ($timeoutValue === 10) {
                                Configuration::updateValue('pagecache_' . $controller . '_timeout', -1);
                            } else {
                                Configuration::updateValue('pagecache_' . $controller . '_timeout',
                                    $timeoutValue * 1440);
                            }
                            Configuration::updateValue('pagecache_' . $controller . '_expires',
                                max(0, min(60, Tools::getValue('pagecache_' . $controller . '_expires', 15))));
                        }
                    }
                    Configuration::updateValue('pagecache_static_expires',
                        max(0, min(60, Tools::getValue('pagecache_static_expires', 15))));

                    $msg_success[] = $this->module->l('Pages and timeouts have been updated', 'jprestaspeedpack');
                }
                //
                // Action: Clear cache
                //
                elseif (Tools::getIsset('submitModuleClearCache')) {
                    if ($this->module->clearCache('manual')) {
                        $msg_success[] = $this->module->l('Cache has been deleted', 'jprestaspeedpack');
                    } else {
                        $msg_errors[] = $this->module->l('Cache has not been completly cleared, please, check the logs for more informations', 'jprestaspeedpack');
                    }
                }
                //
                // Install steps
                //
                elseif (Tools::getIsset('pagecache_install_step')) {
                    $reinstallCache = true;

                    // Disable tokens if requested
                    if (strcmp(Tools::getValue('pagecache_disable_tokens', 'false'), 'true') == 0) {
                        Configuration::updateValue('PS_TOKEN_ENABLE', 0);
                        $msg_success[] = $this->module->l('Tokens have been disabled', 'jprestaspeedpack');
                    }
                    if (Tools::getIsset('pagecache_seller')) {
                        Configuration::updateValue('pagecache_seller', Tools::getValue('pagecache_seller', 'jpresta'));
                    }
                    $pagecache_disable_loggedin = (int) Tools::getValue('pagecache_disable_loggedin', 0);
                    if ($pagecache_disable_loggedin != 0) {
                        // Enable / Disable cache for logged in users
                        Configuration::updateValue('pagecache_skiplogged',
                            $pagecache_disable_loggedin > 0 ? true : false);
                    } else {
                        // New install step
                        Configuration::updateValue('pagecache_install_step',
                            Tools::getValue('pagecache_install_step', self::INSTALL_STEP_BUY_FROM));
                        if (Tools::getValue('pagecache_install_step',
                            self::INSTALL_STEP_BUY_FROM) < self::LAST_INSTALL_STEP) {
                            // Stay or go in test mode
                            Configuration::updateValue('pagecache_debug', 1);
                        } else {
                            // Go in production mode
                            Configuration::updateValue('pagecache_debug', 0);
                        }
                    }
                    if (strcmp(Tools::getValue('pagecache_autoconf', 'false'), 'true') == 0) {
                        $this->autoconf($msg_infos, $msg_warnings, $msg_errors);
                    }
                }
                //
                // Update dynamics hooks
                //
                elseif (Tools::getIsset('submitModuleDynhooks')) {
                    $pagecache_dyn_hooks = '';
                    $pagecache_dyn_widgets = '';
                    if (Tools::getValue('pagecache_hooks') !== false) {
                        $hooks = Tools::getValue('pagecache_hooks');
                        if (is_array($hooks)) {
                            foreach ($hooks as $value) {
                                list($hook_name, $module_name) = explode('|', $value);
                                $empty_box = (int) Tools::getValue('pagecache_hooks_empty_' . $hook_name . '_' . $module_name,
                                    0);
                                $pagecache_dyn_hooks .= $hook_name . '|' . $module_name . '|' . $empty_box . ',';
                                $aliases = JprestaUtils::getCanonicalHookNames();
                                foreach ($aliases as $alias => $newname) {
                                    if (Tools::strtolower($newname) === $hook_name) {
                                        $pagecache_dyn_hooks .= $alias . '|' . $module_name . '|' . $empty_box . ',';
                                    }
                                }
                            }
                        } else {
                            list($hook_name, $module_name) = explode('|', $hooks);
                            $empty_box = (int) Tools::getValue('pagecache_hooks_empty_' . $hook_name . '_' . $module_name,
                                0);
                            $pagecache_dyn_hooks .= $hook_name . '|' . $module_name . '|' . $empty_box . ',';
                            $aliases = JprestaUtils::getCanonicalHookNames();
                            foreach ($aliases as $alias => $newname) {
                                if (Tools::strtolower($newname) === $hook_name) {
                                    $pagecache_dyn_hooks .= $alias . '|' . $module_name . '|' . $empty_box . ',';
                                }
                            }
                        }
                    }
                    if (Tools::getValue('pagecache_dynwidgets') !== false) {
                        $widgets = Tools::getValue('pagecache_dynwidgets');
                        if (is_array($widgets)) {
                            foreach ($widgets as $value) {
                                list($widget_name, $hook_name, $empty_box) = explode('|', $value);
                                $pagecache_dyn_widgets .= Tools::strtolower($widget_name) . '|' . Tools::strtolower($hook_name) . '|' . ($empty_box ? '1' : '0') . ',';
                            }
                        } else {
                            list($widget_name, $hook_name, $empty_box) = explode('|', $widgets);
                            $pagecache_dyn_widgets .= Tools::strtolower($widget_name) . '|' . Tools::strtolower($hook_name) . '|' . ($empty_box ? '1' : '0') . ',';
                        }
                    }
                    Configuration::updateValue('pagecache_dyn_hooks', $pagecache_dyn_hooks);
                    Configuration::updateValue('pagecache_dyn_widgets', $pagecache_dyn_widgets);
                    Configuration::updateValue('pagecache_cfgadvancedjs', trim(Tools::getValue('cfgadvancedjs', '')));
                    $msg_success[] = $this->module->l('Dynamics hooks and javascript to execute have been updated', 'jprestaspeedpack');
                }
                //
                // Datas
                //
                elseif (Tools::getIsset('submitModuleResetDatas')) {
                    // Reset default contexts key
                    Configuration::deleteByName('pagecache_default_context_key');
                    Configuration::deleteByName('pagecache_default_context_key_mobile');
                    $reinstallCache = true;
                    // Reset datas
                    if ($this->module->clearCacheAndStats('manual')) {
                        $msg_success[] = $this->module->l('All datas have been cleared and cache has been deleted', 'jprestaspeedpack');
                    } else {
                        $msg_errors[] = $this->module->l('Cache has not been completly cleared, please, check the logs for more informations', 'jprestaspeedpack');
                    }
                } elseif (Tools::getIsset('submitModulePurgeDatas')) {
                    // Purge datas
                    if ($this->module->purgeCache(Shop::getContextShopID(), 'manual')) {
                        $msg_success[] = $this->module->l('Old cached pages have been cleared', 'jprestaspeedpack');
                    } else {
                        $msg_errors[] = $this->module->l('Some old cached pages have been cleared but it was too long so you need to run the purge again', 'jprestaspeedpack');
                    }
                }
                //
                // OP Cache
                //
                elseif (Tools::getIsset('submitModuleResetOpcache')) {
                    if (opcache_reset()) {
                        $msg_success[] = $this->module->l('OP Cache has been reset', 'jprestaspeedpack');
                    } else {
                        $msg_errors[] = $this->module->l('Unable to reset OP Cache', 'jprestaspeedpack');
                    }
                }
                //
                // Profiling
                //
                elseif (Tools::getIsset('submitModuleResetProfiling')) {
                    // Reset profiling
                    PageCacheDAO::clearProfiling();
                    JprestaUtils::saveConfigurationAllShop('pagecache_profiling_max_reached', false);
                    $msg_success[] = $this->module->l('Profiling datas have been deleted', 'pagecache');
                } elseif (Tools::getIsset('submitModuleOnOffProfiling')) {
                    // Enable / disable profiling
                    JprestaUtils::saveConfigurationAllShop('pagecache_profiling', !JprestaUtils::getConfigurationAllShop('pagecache_profiling'));
                } elseif (Tools::getIsset('submitModuleProfilingMinMs')) {
                    // Enable / disable profiling
                    $minMs = (int) Tools::getValue('pagecache_profiling_min_ms');
                    JprestaUtils::saveConfigurationAllShop('pagecache_profiling_min_ms', $minMs);
                    PageCacheDAO::clearProfiling($minMs);
                    JprestaUtils::saveConfigurationAllShop('pagecache_profiling_max_reached', false);
                }
                //
                // Caching system
                //
                elseif (Tools::getIsset('submitModuleTypeCache')) {
                    $reinstallCache = true;

                    $type = Tools::getValue('pagecache_typecache', 'std');
                    if (strcmp('memcache', $type) === 0) {
                        if (!PageCacheCacheMemcache::isCompatible()) {
                            $msg_errors[] = $this->module->l('PHP Memcache is not available on your hosting; you must install extension', 'jprestaspeedpack');
                        } else {
                            $memcache = new PageCacheCacheMemcache(Tools::getValue('pagecache_typecache_memcache_host'),
                                (int) Tools::getValue('pagecache_typecache_memcache_port'));
                            if ($memcache->isConnected()) {
                                JprestaUtils::saveConfigurationAllShop('pagecache_typecache', 'memcache');
                                JprestaUtils::saveConfigurationAllShop('pagecache_typecache_memcache_host', Tools::getValue('pagecache_typecache_memcache_host'));
                                JprestaUtils::saveConfigurationAllShop('pagecache_typecache_memcache_port', (int) Tools::getValue('pagecache_typecache_memcache_port'));
                                $msg_success[] = $this->module->l("Now using 'PHP memcache' caching system. Cache has been cleared.", 'jprestaspeedpack');
                            } else {
                                $msg_errors[] = $this->module->l('Cannot connect to Memcache server', 'jprestaspeedpack') . ' : ' . error_get_last()['message'];
                            }
                        }
                    } else {
                        if (strcmp('memcached', $type) === 0) {
                            if (!PageCacheCacheMemcached::isCompatible()) {
                                $msg_errors[] = $this->module->l('PHP Memcached is not available on your hosting; you must install extension', 'jprestaspeedpack');
                            } else {
                                $memcached = new PageCacheCacheMemcached(Tools::getValue('pagecache_typecache_memcached_host'),
                                    (int) Tools::getValue('pagecache_typecache_memcached_port'));
                                if ($memcached->isConnected(Tools::getValue('pagecache_typecache_memcached_host'),
                                    (int) Tools::getValue('pagecache_typecache_memcached_port'))) {
                                    JprestaUtils::saveConfigurationAllShop('pagecache_typecache', 'memcached');
                                    JprestaUtils::saveConfigurationAllShop('pagecache_typecache_memcached_host', Tools::getValue('pagecache_typecache_memcached_host'));
                                    JprestaUtils::saveConfigurationAllShop('pagecache_typecache_memcached_port', (int) Tools::getValue('pagecache_typecache_memcached_port'));
                                    $msg_success[] = $this->module->l("Now using 'PHP memcached' caching system. Cache has been cleared.", 'jprestaspeedpack');
                                } else {
                                    $msg_errors[] = $this->module->l('Cannot connect to Memcached server', 'jprestaspeedpack') . ' : ' . $memcached->getResultMessage();
                                }
                            }
                        } else {
                            if (strcmp('stdzip', $type) === 0) {
                                if (!PageCacheCacheZipFS::isCompatible()) {
                                    $msg_errors[] = $this->module->l('ZipArchive is not available on your hosting; it must run at least PHP 5 >= 5.2.0, PHP 7, PECL zip >= 1.1.0', 'jprestaspeedpack');
                                } else {
                                    JprestaUtils::saveConfigurationAllShop('pagecache_typecache', 'stdzip');
                                    $msg_success[] = $this->module->l("Now using 'Zipped Standard file system' caching system. Cache has been cleared.", 'jprestaspeedpack');
                                }
                            } else {
                                if (strcmp('static', $type) === 0) {
                                    if (!PageCacheCacheStatic::isCompatible()) {
                                        $msg_errors[] = $this->module->l('Prestashop Static is not compatible with your hosting', 'jprestaspeedpack');
                                    } else {
                                        JprestaUtils::saveConfigurationAllShop('pagecache_typecache', 'static');
                                        $msg_success[] = $this->module->l("Now using 'Prestashop Static' caching system. Cache has been cleared.", 'jprestaspeedpack');
                                    }
                                } else {
                                    JprestaUtils::saveConfigurationAllShop('pagecache_typecache', 'std');
                                    $msg_success[] = $this->module->l("Now using 'Standard file system' caching system. Cache has been cleared.", 'jprestaspeedpack');
                                }
                            }
                        }
                    }
                    $this->module->clearCache('new caching system');
                }
                //
                // Cache management
                //
                elseif (Tools::getIsset('submitModuleCacheManagement')) {
                    foreach (array_keys($trigered_events) as $key) {
                        Configuration::updateValue($key . '_mods', Tools::getValue($key . '_mods', ''));
                        Configuration::updateValue($key . '_bl', Tools::getValue($key . '_bl', false));
                    }
                    $msg_success[] = $this->module->l('Configuration updated', 'jprestaspeedpack');
                }
                //
                // Cache warmer
                //
                elseif (Tools::getIsset('submitModuleCacheWarmerSettings')) {
                    $settings = JprestaCacheWarmerSettings::get(Tools::getValue('cachewarmer_id_shop'));
                    $settings->checkControllers(Tools::getValue('warmup_controllers'));
                    $settings->contexts = Tools::getValue('contexts');
                    $settings->contexts_auto = Tools::getValue('pagecache_cw_create_auto', false) ? 1 : 0;
                    $filter_products_cats_ids = [];
                    $cats_ids_str = explode(',', Tools::getValue('pagecache_cw_filter_products_cats_ids', ''));
                    if (is_array($cats_ids_str)) {
                        foreach ($cats_ids_str as $cat_id_str) {
                            $cat_id = (int) $cat_id_str;
                            if ($cat_id) {
                                $filter_products_cats_ids[$cat_id] = $cat_id;
                            }
                        }
                    }
                    if (count($filter_products_cats_ids) > 0) {
                        $settings->filter_products_cats_ids = implode(',', $filter_products_cats_ids);
                    } else {
                        $settings->filter_products_cats_ids = '';
                    }
                    $settings->save();
                    $msg_success[] = $this->module->l('Settings for cache-warmer have been updated. They will be used in the next warm-up.', 'jprestaspeedpack');
                }
                //
                // JPresta Account Key
                //
                elseif (Tools::getIsset('submitModuleJak')) {
                    $jprestaApi = new JprestaApi(Tools::getValue('jprestaAccountKey'), JprestaApi::getPrestashopToken());
                    $psType = Tools::getValue('prestashopType') === 'test' ? 'test' : 'prod';
                    $res = $jprestaApi->attach($psType === 'test');
                    if ($res === true) {
                        JprestaApi::setPrestashopType($psType);
                        JprestaApi::setJPrestaAccountKey(Tools::getValue('jprestaAccountKey'));
                        $msg_success[] = $this->module->l('Your JPresta Account Key has been saved', 'jprestaspeedpack');
                    } else {
                        $msg_errors[] = $this->module->l('Cannot attach JPresta Account Key', 'jprestaspeedpack') . ' ' . Tools::getValue('jprestaAccountKey') . ': ' . $res;
                    }
                } elseif (Tools::getIsset('submitModuleJakDetach')) {
                    $jprestaApi = new JprestaApi(JprestaApi::getJPrestaAccountKey(), JprestaApi::getPrestashopToken());
                    $res = $jprestaApi->detach();
                    if ($res === true) {
                        JprestaApi::setPrestashopType(null);
                        JprestaApi::setJPrestaAccountKey(null);
                        $msg_success[] = $this->module->l('Your JPresta Account has been detached', 'jprestaspeedpack');
                    } else {
                        $msg_errors[] = $this->module->l('Cannot detach your JPresta Account', 'jprestaspeedpack') . ' ' . Tools::getValue('jprestaAccountKey') . ': ' . $res;
                    }
                } elseif (Tools::getIsset('submitModuleConfirmClone') || Tools::getIsset('submitModuleJakReset')) {
                    JprestaApi::setPrestashopIsClone(true);
                    $msg_success[] = $this->module->l('Thank you, you can now attach this Prestashop instance to your JPresta account', 'jprestaspeedpack');
                } elseif (Tools::getIsset('submitModuleNotAClone')) {
                    JprestaApi::setPrestashopIsClone(false);
                    $msg_success[] = $this->module->l('Thank you, this Prestashop instance is considered the same', 'jprestaspeedpack');
                }
                //
                // Other options
                //
                elseif (Tools::getIsset('submitModuleOtherOptions')) {
                    Configuration::updateValue('pagecache_minifyhtml', Tools::getValue('pagecache_minifyhtml', false));
                    $msg_success[] = $this->module->l('Configuration updated', 'jprestaspeedpack');
                }
                //
                // Options
                //
                elseif (Tools::getIsset('submitModuleOptions')) {
                    $reinstallCache = true;

                    Configuration::updateValue('pagecache_cache_customizable',
                        Tools::getValue('pagecache_cache_customizable', false));
                    Configuration::updateValue('pagecache_always_infosbox',
                        Tools::getValue('pagecache_always_infosbox', false));
                    Configuration::updateValue('pagecache_skiplogged', Tools::getValue('pagecache_skiplogged', false));
                    Configuration::updateValue('pagecache_logs', Tools::getValue('pagecache_logs', false));
                    Configuration::updateValue('pagecache_normalize_urls',
                        Tools::getValue('pagecache_normalize_urls', false));
                    Configuration::updateValue('pagecache_logout_nocache',
                        Tools::getValue('pagecache_logout_nocache', false));
                    Configuration::updateValue('pagecache_exec_header_hook', Tools::getValue('pagecache_exec_header_hook', true));
                    Configuration::updateValue('pagecache_statsttfb', Tools::getValue('pagecache_statsttfb', true));
                    Configuration::updateValue('pagecache_instockisadd', Tools::getValue('pagecache_instockisadd', false));
                    Configuration::updateValue('pagecache_use_dispatcher_hook',
                        Tools::getValue('pagecache_use_dispatcher_hook', true));
                    Configuration::updateValue('pagecache_product_refreshEveryX',
                        Tools::getValue('pagecache_product_refreshEveryX', 1));
                    Configuration::updateValue('pagecache_max_exec_time', min(8 * 60, max(1, (int) Tools::getValue('pagecache_max_exec_time', 30))));
                    Configuration::updateValue('pagecache_maxrows', max(0, (int) Tools::getValue('pagecache_maxrows', 0)));
                    Configuration::updateValue('pagecache_currencies_to_cache',
                        implode(',', Tools::getValue('pagecache_currencies_to_cache', [])));
                    Configuration::updateValue('pagecache_ignore_before_pattern',
                        JprestaUtils::encodeConfiguration(Tools::getValue('pagecache_ignore_before_pattern', '')));
                    Configuration::updateValue('pagecache_ignore_after_pattern',
                        JprestaUtils::encodeConfiguration(Tools::getValue('pagecache_ignore_after_pattern', '')));
                    JprestaUtils::saveConfigurationAllShop('pagecache_ignore_referers', Tools::getValue('pagecache_ignore_referers', ''));
                    $regex = Tools::getValue('pagecache_ignore_url_regex', '');
                    if ($regex) {
                        if (@preg_match('/' . $regex . '/', '') === false) {
                            $msg_errors[] = $this->module->l('Invalid regular expression ' . $regex . ', read https://www.php.net/manual/en/reference.pcre.pattern.syntax.php for more informations. Use https://regex101.com/ to test your regular expression.', 'jprestaspeedpack');
                        } else {
                            Configuration::updateValue('pagecache_ignore_url_regex',
                                JprestaUtils::encodeConfiguration(Tools::getValue('pagecache_ignore_url_regex', '')));
                        }
                    } else {
                        Configuration::updateValue('pagecache_ignore_url_regex', '');
                    }

                    $ignored_params_str = '';
                    $ignored_params = explode(',', Tools::getValue('pagecache_ignored_params', ''));
                    foreach ($ignored_params as $ignored_param) {
                        $p = Tools::strtolower(trim($ignored_param));
                        if (!empty($p)) {
                            if (!empty($ignored_params_str)) {
                                $ignored_params_str .= ',';
                            }
                            $ignored_params_str .= $p;
                        }
                    }
                    Configuration::updateValue('pagecache_ignored_params', $ignored_params_str);
                    $msg_success[] = $this->module->l('Configuration updated', 'jprestaspeedpack');
                }
                //
                // Cache key
                //
                elseif (Tools::getIsset('submitModuleCacheKey')) {
                    Configuration::updateValue('pagecache_depend_on_device_auto',
                        Tools::getValue('pagecache_depend_on_device_auto', true));

                    Configuration::updateValue('pagecache_tablet_is_mobile',
                        Tools::getValue('pagecache_tablet_is_mobile', true));

                    Configuration::updateValue('pagecache_depend_on_css_js',
                        Tools::getValue('pagecache_depend_on_css_js', true));

                    // Countries
                    $currentCacheKeyCountryConf = json_decode(JprestaUtils::getConfigurationByShopId('pagecache_cachekey_countries', Shop::getContextShopID(), '{}'), true);
                    $checkedCountries = Tools::getValue('pagecache_cachekey_countries');
                    foreach ($currentCacheKeyCountryConf as $id_country => &$country_conf) {
                        if (!$country_conf['has_impact']) {
                            $country_conf['specific_cache'] = $checkedCountries && array_key_exists($id_country, $checkedCountries);
                        }
                    }
                    JprestaUtils::saveConfigurationByShopId('pagecache_cachekey_countries', json_encode($currentCacheKeyCountryConf), Shop::getContextShopID());

                    // User groups
                    $currentCacheKeyUserGroupConf = Jprestaspeedpack::getCacheKeyForUserGroups(Shop::getContextShopID());
                    $checkedUserGroups = Tools::getValue('pagecache_cachekey_usergroups');
                    foreach ($currentCacheKeyUserGroupConf as $id_group => &$group_conf) {
                        if (!$group_conf['has_impact_as_default']) {
                            $group_conf['specific_cache'] = $checkedUserGroups && array_key_exists($id_group, $checkedUserGroups);
                        }
                    }
                    Jprestaspeedpack::saveCacheKeyForUserGroups(Shop::getContextShopID(), $currentCacheKeyUserGroupConf);

                    Configuration::updateValue('pagecache_depend_on_other_groups',
                        Tools::getValue('pagecache_depend_on_other_groups', true));

                    $msg_success[] = $this->module->l('Configuration updated', 'jprestaspeedpack');
                }
                //
                // Multistore
                //
                elseif (Tools::getIsset('submitModuleShopsinfos')) {
                    $id_shops = Tools::getValue('id_shops', []);
                    if (count($id_shops) > 0) {
                        $currentShopId = (int) Shop::getContextShopID(true);
                        $copiedShopIds = implode(', ', $id_shops);
                        $rows = JprestaUtils::dbSelectRows('SELECT `name`, `value` FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` LIKE \'pagecache_%\' and `name` <> \'pagecache_cron_token\' and id_shop = ' . $currentShopId);
                        $updatedRows = 0;
                        foreach ($rows as $row) {
                            foreach ($id_shops as $id_shop) {
                                JprestaUtils::saveConfigurationByShopId($row['name'], $row['value'], (int) $id_shop);
                                ++$updatedRows;
                            }
                        }
                        if ($updatedRows > 0) {
                            $msg_success[] = $this->module->l('Configuration has been copied to selected shops', 'jprestaspeedpack') . " ($updatedRows settings from shop $currentShopId to shops $copiedShopIds)";
                        } else {
                            $msg_errors[] = $this->module->l('Cannot copy the configuration, an unknown error occured, see logs for more details.', 'jprestaspeedpack');
                        }
                    }
                }

                // static.config.php need to be refreshed
                if ($reinstallCache) {
                    $this->module->uninstallCache($typecacheBefore);
                    $this->module->installCache();
                }
            }
        } else {
            foreach (Jprestaspeedpack::getManagedControllersNames() as $controller) {
                if (!Configuration::hasKey('pagecache_' . $controller, null, Shop::getContextShopGroupID(true), Shop::getContextShopID(true))) {
                    Configuration::updateValue('pagecache_' . $controller, 1);
                }
                if (!Configuration::hasKey('pagecache_' . $controller . '_timeout', null, Shop::getContextShopGroupID(true), Shop::getContextShopID(true))) {
                    Configuration::updateValue('pagecache_' . $controller . '_timeout', 60 * 24 * 1);
                }
            }
            if (!Configuration::hasKey('pagecache_show_stats', null, Shop::getContextShopGroupID(true), Shop::getContextShopID(true))) {
                Configuration::updateValue('pagecache_show_stats', true);
            }
        }
        $infos = [];
        $infos['msg_success'] = $msg_success;
        $infos['msg_infos'] = $msg_infos;
        $infos['msg_warnings'] = $msg_warnings;
        $infos['msg_errors'] = $msg_errors;

        $this->context->smarty->assign($infos);

        return true;
    }

    public function initHeader()
    {
        parent::initHeader();

        $admin_webpath = str_ireplace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_);
        $admin_webpath = preg_replace('/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '', $admin_webpath);

        $this->context->controller->addJS([
            _PS_JS_DIR_ . 'vendor/d3.v3.min.js',
            __PS_BASE_URI__ . $admin_webpath . '/themes/' . $this->context->employee->bo_theme . '/js/vendor/nv.d3.min.js',
        ]);
        $this->context->controller->addCSS(__PS_BASE_URI__ . $admin_webpath . '/themes/' . $this->context->employee->bo_theme . '/css/vendor/nv.d3.css');
    }

    /**
     * Check if the module needs to be upgraded (scripts)
     */
    public function init()
    {
        if ($this->module->needsUpgrade()) {
            $this->module->upgradeIfNeeded();
            $this->confirmations = array_merge($this->confirmations, $this->module->getConfirmations());
            $this->errors = array_merge($this->errors, $this->module->getErrors());
        }
        if (method_exists($this->module, 'checkInstallCache')) {
            // Check method existance to avoid error on the very first load, after the module has been upgraded #3273
            $this->module->checkInstallCache();
        }
        if (method_exists($this->module, 'checkStaticCode')) {
            // Check method existance to avoid error on the very first load, after the module has been upgraded #3273
            $this->module->checkStaticCode();
        }
        parent::init();
    }

    public function renderList()
    {
        $this->module->checkTabAccesses('AdminPageCacheProfilingDatas');
        $this->module->checkTabAccesses('AdminPageCacheDatas');
        $this->module->checkTabAccesses('AdminPageCacheMemcacheTest');
        $this->module->checkTabAccesses('AdminPageCacheMemcachedTest');
        $this->module->checkTabAccesses('AdminJprestaSQLProfilerQuery');
        $this->module->checkTabAccesses('AdminJprestaSQLProfiler');

        // Update cache key for countries and user groups
        Jprestaspeedpack::updateCacheKeyForCountries();
        Jprestaspeedpack::updateCacheKeyForUserGroups();

        if (Shop::isFeatureActive() && !Shop::getContextShopID()) {
            return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/only-active-in-shop-context.tpl');
        }

        $msg_errors = $this->context->smarty->getTemplateVars('msg_errors');
        $msg_warnings = $this->context->smarty->getTemplateVars('msg_warnings');
        $msg_success = $this->context->smarty->getTemplateVars('msg_success');
        $msg_infos = $this->context->smarty->getTemplateVars('msg_infos');

        $installedModules = Module::getModulesInstalled(0);
        $instances = [];
        foreach ($installedModules as $module) {
            if ($tmp_instance = JprestaUtils::getModuleInstanceById($module['id_module'])) {
                $instances[$tmp_instance->id] = $tmp_instance;
            }
        }

        // To display advanced options add URL parameter "adv"
        $advanced_mode = Tools::getIsset('adv');
        if (strstr($_SERVER['REQUEST_URI'], '#') !== false) {
            $advanced_mode_url = str_replace('#', '&adv#', $_SERVER['REQUEST_URI']);
        } else {
            $advanced_mode_url = $_SERVER['REQUEST_URI'] . '&adv';
        }

        // Fix tokens because it cannot be done via admin for multi-store anymore
        $token_enabled = (int) Configuration::get('PS_TOKEN_ENABLE') == 1 ? true : false;
        if ($token_enabled) {
            Configuration::updateValue('PS_TOKEN_ENABLE', 0);
            $msg_infos[] = $this->module->l('Front end tokens have been disabled in order for cached pages to do ajax call.');
        }

        // Check errors or compatiblity problem
        $installErrors = $this->getInstallationErrors();
        if (!empty($installErrors)) {
            $msg_errors = array_merge($msg_errors, $installErrors);
            // Back to install step 1 and test mode to resolve errors
            Configuration::updateValue('pagecache_debug', true);
            Configuration::updateValue('pagecache_install_step', self::INSTALL_STEP_INSTALL);
        } else {
            $cur_step = (int) Configuration::get('pagecache_install_step');
            if ($cur_step <= 1) {
                // Validate step 1 because there is no error
                Configuration::updateValue('pagecache_install_step', self::INSTALL_STEP_BACK_TO_TEST);
            }
        }

        // Some Prestashop settings advises
        $advices = $this->getAdvices();
        $msg_warnings = array_merge($msg_warnings, $advices);

        $diagnostic = $this->getDiagnostic();

        // Variable for smarty
        $infos = [];
        $infos['jpresta_language_isocode'] = $this->context->language->iso_code;
        if (!in_array($infos['jpresta_language_isocode'], ['en', 'fr', 'es', 'it', 'de', 'pl'])) {
            $infos['jpresta_language_isocode'] = 'en';
        }
        $infos['jpresta_account_key'] = JprestaApi::getJPrestaAccountKey();
        $infos['jpresta_ps_token'] = JprestaApi::getPrestashopToken();
        $infos['jpresta_ps_type'] = JprestaApi::getPrestashopType();
        $infos['jpresta_api_url_licenses'] = JprestaApi::getLicensesURL();
        $infos['jpresta_api_url_cw'] = JprestaApi::getCacheWarmerDashboardURL();
        $infos['avec_bootstrap'] = JprestaUtils::version_compare(_PS_VERSION_, '1.6', '>=');
        $infos['module_name'] = $this->module->name;
        $infos['module_displayName'] = $this->module->name === 'jprestaspeedpack' ? 'Page Cache Ultimate' : $this->module->displayName;
        $infos['module_version'] = $this->module->version;
        $infos['module_enabled'] = JprestaUtils::isModuleEnabled($this->module->name);
        $infos['shop_name'] = $this->context->shop->name;
        $infos['is_multistores'] = Shop::isFeatureActive();
        $infos['maybe_a_clone'] = JprestaApi::getPrestashopIsClone();
        $infos['prestashop_version'] = _PS_VERSION_;
        $infos['pctab'] = Tools::getValue('pctab', 'install');
        $infos['advanced_mode'] = $advanced_mode;
        $infos['advanced_mode_url'] = $advanced_mode_url;
        $infos['diagnostic_count'] = (int) $diagnostic['count'];
        $infos['diagnostic'] = $diagnostic;
        $infos['cur_step'] = (int) Configuration::get('pagecache_install_step');
        $infos['shop_link_debug'] = $this->context->shop->getBaseURL(true) . JprestaUtils::getUrlLang($this->context->shop->id) . '?dbgpagecache=1';
        $infos['doc_proto'] = self::DOC_PROTO;
        $infos['doc_domain'] = self::DOC_DOMAIN;
        $infos['doc_url_fr'] = self::DOC_URL_FR;
        $infos['doc_url_en'] = self::DOC_URL_EN;
        $infos['contact_url'] = $this->module->getContactUrl();
        $infos['request_uri'] = Jprestaspeedpack::getServerValue('REQUEST_URI');
        $infos['INSTALL_STEP_AUTOCONF'] = self::INSTALL_STEP_AUTOCONF;
        $infos['INSTALL_STEP_BACK_TO_TEST'] = self::INSTALL_STEP_BACK_TO_TEST;
        $infos['INSTALL_STEP_BUY_FROM'] = self::INSTALL_STEP_BUY_FROM;
        $infos['INSTALL_STEP_CART'] = self::INSTALL_STEP_CART;
        $infos['INSTALL_STEP_EU_COOKIE'] = self::INSTALL_STEP_EU_COOKIE;
        $infos['INSTALL_STEP_IN_ACTION'] = self::INSTALL_STEP_IN_ACTION;
        $infos['INSTALL_STEP_INSTALL'] = self::INSTALL_STEP_INSTALL;
        $infos['INSTALL_STEP_LOGGED_IN'] = self::INSTALL_STEP_LOGGED_IN;
        $infos['INSTALL_STEP_VALIDATE'] = self::INSTALL_STEP_VALIDATE;
        $infos['performances'] = PageCacheDAO::getPerformances($this->context->shop->id);
        $infos['pagecache_cron_urls'] = $this->getCronClearCacheURL();
        $infos['pagecache_cron_domain'] = $this->context->shop->domain;
        if (isset($this->context->shop->theme)) {
            $infos['pagecache_cron_theme'] = $this->context->shop->theme->get('name');
        } else {
            $infos['pagecache_cron_theme'] = $this->context->shop->theme_name;
        }
        $infos['pagecache_cron_base'] = $this->context->shop->getBaseURL(true);
        $infos['pagecache_debug'] = Configuration::get('pagecache_debug');
        $infos['pagecache_seller'] = Configuration::get('pagecache_seller');
        $infos['pagecache_skiplogged'] = Configuration::get('pagecache_skiplogged');
        $infos['pagecache_minifyhtml'] = Configuration::get('pagecache_minifyhtml');
        $infos['pagecache_minifyhtml_enabled'] = Jprestaspeedpack::canMinifyHtml();
        $infos['pagecache_statsttfb'] = Configuration::get('pagecache_statsttfb');
        $infos['pagecache_instockisadd'] = Configuration::get('pagecache_instockisadd');
        $infos['pagecache_typecache'] = Configuration::get('pagecache_typecache');

        $infos['pagecache_typecache_stdzip'] = PageCacheCacheZipFS::isCompatible();
        $infos['pagecache_typecache_static'] = PageCacheCacheStatic::isCompatible();

        $infos['pagecache_typecache_memcache'] = PageCacheCacheMemcache::isCompatible();
        $infos['pagecache_typecache_memcache_host'] = Configuration::get('pagecache_typecache_memcache_host');
        $infos['pagecache_typecache_memcache_port'] = Configuration::get('pagecache_typecache_memcache_port');
        $infos['pagecache_typecache_memcache_testurl'] = $this->context->link->getAdminLink('AdminPageCacheMemcacheTest');

        $infos['pagecache_typecache_memcached'] = PageCacheCacheMemcached::isCompatible();
        $infos['pagecache_typecache_memcached_host'] = Configuration::get('pagecache_typecache_memcached_host');
        $infos['pagecache_typecache_memcached_port'] = Configuration::get('pagecache_typecache_memcached_port');
        $infos['pagecache_typecache_memcached_testurl'] = $this->context->link->getAdminLink('AdminPageCacheMemcachedTest');

        $infos['pagecache_ignored_params'] = Configuration::get('pagecache_ignored_params');
        $infos['pagecache_logs'] = Configuration::get('pagecache_logs');
        $infos['pagecache_depend_on_device_auto'] = Configuration::get('pagecache_depend_on_device_auto');
        $infos['pagecache_tablet_is_mobile'] = Configuration::get('pagecache_tablet_is_mobile');
        $infos['pagecache_depend_on_other_groups'] = Configuration::get('pagecache_depend_on_other_groups');
        $infos['pagecache_depend_on_css_js'] = Configuration::get('pagecache_depend_on_css_js');
        $infos['pagecache_exec_header_hook'] = Configuration::get('pagecache_exec_header_hook');
        $infos['pagecache_use_dispatcher_hook'] = Configuration::get('pagecache_use_dispatcher_hook');
        $infos['pagecache_max_exec_time'] = Configuration::get('pagecache_max_exec_time');
        $infos['pagecache_maxrows'] = (int) Configuration::get('pagecache_maxrows');
        $infos['pagecache_ignore_before_pattern'] = JprestaUtils::decodeConfiguration(Configuration::get('pagecache_ignore_before_pattern'));
        $infos['pagecache_ignore_after_pattern'] = JprestaUtils::decodeConfiguration(Configuration::get('pagecache_ignore_after_pattern'));
        $infos['pagecache_ignore_url_regex'] = JprestaUtils::decodeConfiguration(Configuration::get('pagecache_ignore_url_regex'));
        $infos['pagecache_ignore_referers'] = JprestaUtils::getConfigurationAllShop('pagecache_ignore_referers');
        $infos['pagecache_cache_customizable'] = Configuration::get('pagecache_cache_customizable');
        $infos['pagecache_product_refreshEveryX'] = Configuration::get('pagecache_product_refreshEveryX');
        $infos['pagecache_datas_dbinfos'] = $this->getDatasDatabases();
        $infos['pagecache_datas_url'] = $this->context->link->getAdminLink('AdminPageCacheDatas');
        $infos['pagecache_profiling'] = JprestaUtils::getConfigurationAllShop('pagecache_profiling');
        $infos['pagecache_profiling_not_available'] = JprestaUtils::version_compare(_PS_VERSION_, '1.7', '<');
        $infos['pagecache_profiling_min_ms'] = JprestaUtils::getConfigurationAllShop('pagecache_profiling_min_ms');
        $infos['pagecache_profiling_max_reached'] = JprestaUtils::getConfigurationAllShop('pagecache_profiling_max_reached');
        $infos['pagecache_profiling_max'] = Jprestaspeedpack::PROFILING_MAX_RECORD;
        $infos['pagecache_profiling_datas_url'] = $this->context->link->getAdminLink('AdminPageCacheProfilingDatas');
        $infos['pagecache_normalize_urls'] = Configuration::get('pagecache_normalize_urls');
        $infos['pagecache_logout_nocache'] = Configuration::get('pagecache_logout_nocache');
        $infos['pagecache_always_infosbox'] = Configuration::get('pagecache_always_infosbox');
        $infos['pagecache_cfgadvancedjs'] = Configuration::get('pagecache_cfgadvancedjs');
        $infos['pagecache_cw_url'] = $this->getCacheWarmerURL();
        $infos['pagecache_cw_contexts'] = JprestaCacheWarmerSettings::get(Shop::getContextShopID());
        $infos['pagecache_contexts_languages'] = [];
        foreach (JprestaUtils::dbSelectRows('SELECT DISTINCT c.id_lang, l.name FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '` c INNER JOIN `' . _DB_PREFIX_ . 'lang` l ON c.id_lang=l.id_lang WHERE c.id_shop=' . (int) Shop::getContextShopID() . ' AND c.active=1') as $row) {
            $infos['pagecache_contexts_languages'][(int) $row['id_lang']] = $row['name'];
        }
        $infos['pagecache_contexts_currencies'] = [];
        foreach (JprestaUtils::dbSelectRows('SELECT DISTINCT c.id_currency, l.iso_code FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '` c INNER JOIN `' . _DB_PREFIX_ . 'currency` l ON c.id_currency=l.id_currency WHERE c.id_shop=' . (int) Shop::getContextShopID() . ' AND c.active=1') as $row) {
            $infos['pagecache_contexts_currencies'][(int) $row['id_currency']] = $row['iso_code'];
        }
        $infos['pagecache_contexts_specs'] = [];
        foreach (JprestaUtils::dbSelectRows('SELECT DISTINCT c.id_specifics, l.details FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '` c INNER JOIN `' . _DB_PREFIX_ . PageCacheDAO::TABLE_DETAILS . '` l ON c.id_specifics=l.id WHERE c.id_shop=' . (int) Shop::getContextShopID() . ' AND c.active=1') as $row) {
            $infos['pagecache_contexts_specs'][(int) $row['id_specifics']] = $row['details'];
        }
        $infos['pagecache_contexts_countries'] = [];
        foreach (JprestaUtils::dbSelectRows('SELECT DISTINCT c.id_country, l.name FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '` c INNER JOIN `' . _DB_PREFIX_ . 'country_lang` l ON c.id_country=l.id_country WHERE c.id_shop=' . (int) Shop::getContextShopID() . ' AND c.active=1 AND l.id_lang=' . (int) $this->context->language->id) as $row) {
            $infos['pagecache_contexts_countries'][(int) $row['id_country']] = $row['name'];
        }
        $infos['pagecache_contexts_groups'] = [];
        foreach (JprestaUtils::dbSelectRows('SELECT DISTINCT c.id_fake_customer FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '` c WHERE c.id_shop=' . (int) Shop::getContextShopID() . ' AND c.active=1') as $row) {
            $jCustomer = new JprestaCustomer((int) $row['id_fake_customer']);
            $infos['pagecache_contexts_groups'][(int) $row['id_fake_customer']] = $jCustomer->getLabel();
        }

        if ($advanced_mode) {
            $infos['pagecache_cache_key_countries'] = json_decode(JprestaUtils::getConfigurationByShopId('pagecache_cachekey_countries', Shop::getContextShopID(), '{}'), true);
            foreach ($infos['pagecache_cache_key_countries'] as $id_country => $country_conf) {
                $infos['pagecache_cache_key_countries'][$id_country]['name'] = Country::getNameById($this->context->language->id, $id_country);
            }
            $infos['pagecache_cache_key_usergroups'] = Jprestaspeedpack::getCacheKeyForUserGroups(Shop::getContextShopID());
            foreach ($infos['pagecache_cache_key_usergroups'] as $id_group => $country_conf) {
                $group = new Group($id_group, $this->context->language->id);
                $infos['pagecache_cache_key_usergroups'][$id_group]['name'] = $group->name;
            }
        }
        $infos['pagecache_currencies_to_cache'] = [];
        $pagecache_currencies_to_cache = array_flip(explode(',', Configuration::get('pagecache_currencies_to_cache')));
        foreach (Currency::getCurrenciesByIdShop(Shop::getContextShopID()) as $currency) {
            if ($currency['active']) {
                $infos['pagecache_currencies_to_cache'][$currency['iso_code']] = array_key_exists($currency['iso_code'], $pagecache_currencies_to_cache) ? 1 : 0;
            }
        }

        $infos['pagecache_static_expires'] = Configuration::get('pagecache_static_expires');
        $infos['managed_controllers'] = [];
        foreach (Jprestaspeedpack::getManagedControllers() as $controller => $controllerInfos) {
            // Expires
            $infos['managed_controllers'][$controller]['expires'] = Configuration::get('pagecache_' . $controller . '_expires');

            // Timeout
            $timeoutValue = (int) Configuration::get('pagecache_' . $controller . '_timeout');
            if ($timeoutValue === 14 * 1440) {
                $timeoutValue = 8;
            } elseif ($timeoutValue === 30 * 1440) {
                $timeoutValue = 9;
            } elseif ($timeoutValue === -1) {
                $timeoutValue = 10;
            } else {
                $timeoutValue = $timeoutValue / 1440;
            }
            $infos['managed_controllers'][$controller]['timeout'] = $timeoutValue;
            $infos['managed_controllers'][$controller]['warmer'] = true;

            // Title
            switch ($controller) {
                case 'index':
                    $infos['managed_controllers'][$controller]['title'] = $this->module->l('Home page', 'jprestaspeedpack');
                    break;
                case 'category':
                    $infos['managed_controllers'][$controller]['title'] = $this->module->l('Category page', 'jprestaspeedpack');
                    break;
                case 'product':
                    $infos['managed_controllers'][$controller]['title'] = $this->module->l('Product page', 'jprestaspeedpack');
                    break;
                case 'cms':
                    $infos['managed_controllers'][$controller]['title'] = $this->module->l('CMS page', 'jprestaspeedpack');
                    break;
                case 'newproducts':
                    $infos['managed_controllers'][$controller]['title'] = $this->module->l('New products page', 'jprestaspeedpack');
                    break;
                case 'bestsales':
                    $infos['managed_controllers'][$controller]['title'] = $this->module->l('Best sales page', 'jprestaspeedpack');
                    break;
                case 'supplier':
                    $infos['managed_controllers'][$controller]['title'] = $this->module->l('Suppliers page', 'jprestaspeedpack');
                    break;
                case 'manufacturer':
                    $infos['managed_controllers'][$controller]['title'] = $this->module->l('Manufacturers page', 'jprestaspeedpack');
                    break;
                case 'contact':
                    $infos['managed_controllers'][$controller]['title'] = $this->module->l('Contact form page', 'jprestaspeedpack');
                    break;
                case 'pricesdrop':
                    $infos['managed_controllers'][$controller]['title'] = $this->module->l('Prices drop page', 'jprestaspeedpack');
                    break;
                case 'sitemap':
                    $infos['managed_controllers'][$controller]['title'] = $this->module->l('Sitemap page', 'jprestaspeedpack');
                    break;
                default:
                    $infos['managed_controllers'][$controller]['title'] = $this->module->l('Pages generated by', 'jprestaspeedpack') . ' ' . $controller;
                    $infos['managed_controllers'][$controller]['warmer'] = JprestaUtilsModule::canBeWarmed($controller);
                    break;
            }
            // ID
            $infos['managed_controllers'][$controller]['id'] = $controllerInfos['id'];
        }

        $infos['widgets'] = [];
        $allModules = Module::getModulesInstalled();
        foreach ($allModules as $module) {
            $moduleInstance = JprestaUtils::getModuleInstanceById($module['id_module']);
            if ($moduleInstance instanceof PrestaShop\PrestaShop\Core\Module\WidgetInterface) {
                $infos['widgets'][$moduleInstance->name]['id_module'] = $moduleInstance->id;
                $infos['widgets'][$moduleInstance->name]['name'] = $moduleInstance->name;
                $infos['widgets'][$moduleInstance->name]['description'] = $moduleInstance->description;
                $infos['widgets'][$moduleInstance->name]['display_name'] = $moduleInstance->displayName;
                $infos['widgets'][$moduleInstance->name]['version'] = $moduleInstance->version;
                $infos['widgets'][$moduleInstance->name]['author'] = $moduleInstance->author;
            }
        }
        $infos['dynamic_widgets'] = self::getDynamicWidgets();
        $infos['module_list'] = Hook::getHookModuleExecList();
        $infos['modules_hooks'] = [];
        $infos['dynamic_hooks'] = self::getDynamicHooks();
        foreach ($infos['module_list'] as $hook_name => &$modules) {
            if (!Jprestaspeedpack::canBeMarkedAsDynamic($hook_name)) {
                continue;
            }
            foreach ($modules as &$module) {
                if (strcmp($this->module->name, $module['module']) !== 0 && is_array($module)) {
                    if (!array_key_exists($module['module'], $infos['modules_hooks'])) {
                        $moduleInfos = [];
                        $moduleInfos['hooks'] = [];
                        $moduleInfos['id_module'] = $module['id_module'];
                        $moduleInfos['name'] = $module['module'];
                        if (isset($instances[$module['id_module']])) {
                            $moduleInfos['description'] = $instances[$module['id_module']]->description;
                            $moduleInfos['display_name'] = $instances[$module['id_module']]->displayName;
                            $moduleInfos['version'] = $instances[$module['id_module']]->version;
                            $moduleInfos['author'] = $instances[$module['id_module']]->author;
                        } else {
                            $moduleInfos['description'] = ' ';
                            $moduleInfos['display_name'] = $module['module'];
                            $moduleInfos['version'] = 0;
                            $moduleInfos['author'] = ' ';
                        }
                    } else {
                        $moduleInfos = $infos['modules_hooks'][$module['module']];
                    }
                    $moduleInfos['hooks'][$hook_name] = [];
                    $moduleInfos['hooks'][$hook_name]['is_standard'] = true;
                    $moduleInfos['hooks'][$hook_name]['dyn_is_checked'] = false;
                    $moduleInfos['hooks'][$hook_name]['empty_option_checked'] = false;
                    if (isset($infos['dynamic_hooks'][$hook_name]) && isset($infos['dynamic_hooks'][$hook_name][$module['module']])) {
                        $moduleInfos['hooks'][$hook_name]['dyn_is_checked'] = true;
                        if ($infos['dynamic_hooks'][$hook_name][$module['module']]['empty_box']) {
                            $moduleInfos['hooks'][$hook_name]['empty_option_checked'] = true;
                        }
                    }
                    $infos['modules_hooks'][$module['module']] = $moduleInfos;
                }
            }
        }
        $infos['pagecache_shopsinfos'] = $this->getShopsInfos();
        $infos['pagecache_contexts'] = PageCacheDAO::getAllContexts(Shop::getContextShopID());
        $infos['pagecache_clone_detected'] = JprestaApi::getPrestashopIsClone();
        $infos['systemInfos'] = new JprestaSystemInfos();
        $infos['op_cache'] = function_exists('opcache_get_status') && (bool) @opcache_get_status(false);

        $infos['latest_version'] = false;
        $latestVersions = JprestaApi::getLatestVersion($this->module->name);
        if ($latestVersions && version_compare($this->module->version, $latestVersions['version']) < 0) {
            $infos['latest_version'] = $latestVersions;
            $infos['latest_version']['upgrade_link'] = null;
            if (JprestaUtils::isModuleEnabled('jprestaupgrade')) {
                $infos['latest_version']['upgrade_link'] = $this->context->link->getAdminLink('AdminJprestaUpgrade');
            }
        }
        $infos['jpresta_shop_url'] = JprestaApi::JPRESTA_PROTO . JprestaApi::JPRESTA_DOMAIN . JprestaApi::JPRESTA_DOMAIN_EXT;

        $infos['msg_success'] = $msg_success;
        $infos['msg_infos'] = $msg_infos;
        $infos['msg_warnings'] = $msg_warnings;
        $infos['msg_errors'] = $msg_errors;

        $this->context->smarty->assign($infos);

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/get-content.tpl');
    }

    private function getShopsInfos()
    {
        $shops = [];
        if (Shop::isFeatureActive()) {
            $shops = Shop::getShops(false);
            foreach ($shops as &$shop) {
                $device_count = (int) JprestaUtils::dbGetValue('SELECT count(*) FROM `' . _DB_PREFIX_ . 'module_shop` WHERE id_module=' . (int) $this->module->id . ' AND id_shop=' . (int) $shop['id_shop']);
                $shop['module_enabled'] = $device_count > 0;
                $shop['module_install_step'] = JprestaUtils::getConfigurationByShopId('pagecache_install_step', (int) $shop['id_shop']);
                $shop['is_current'] = (int) $shop['id_shop'] === Shop::getContextShopID();
            }
        }

        return $shops;
    }

    private function getAdvices()
    {
        $warnings = [];

        return $warnings;
    }

    private function isProtocolH2OrH3Used()
    {
        static $isProtocolH2OrH3Used = null;
        if ($isProtocolH2OrH3Used === null) {
            $url = $this->context->shop->getBaseURL(true) . 'modules/' . $this->module->name . '/logo.png';
            $ch = curl_init();
            if (!defined('CURL_HTTP_VERSION_2_0')) {
                // For old version of cURL
                define('CURL_HTTP_VERSION_2_0', 3);
            }
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_HEADER => true,
                CURLOPT_NOBODY => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0, // cURL will attempt to make an HTTP/2.0 request (can downgrade to HTTP/1.1)
            ]);
            $response = curl_exec($ch);
            if ($response !== false && (strpos($response, 'HTTP/2') === 0 || strpos($response, 'HTTP/3') === 0)) {
                $isProtocolH2OrH3Used = true;
            } elseif ($response !== false) {
                $isProtocolH2OrH3Used = false;
            } else {
                // something else happened causing the request to fail
                $isProtocolH2OrH3Used = false;
            }
            curl_close($ch);
        }

        return $isProtocolH2OrH3Used;
    }

    /**
     * Return an array[info/warn/error][messages[]]
     */
    private function getDiagnostic()
    {
        $count = 0;
        $diagnostic = [];
        $diagnostic['info'] = [];
        $diagnostic['warn'] = [];
        $diagnostic['error'] = [];
        if ((int) Configuration::get('PS_SMARTY_CACHE') === 0) {
            $diagnostic['error'][$count] = [];
            $diagnostic['error'][$count]['msg'] = $this->module->l('You must enable smarty cache; keep it disabled only when developping or modifying your theme or a module', 'jprestaspeedpack');
            $diagnostic['error'][$count]['link'] = $this->context->link->getAdminLink('AdminPerformance');
            $diagnostic['error'][$count]['link_title'] = $this->module->l('Resolve this issue in Performances page', 'jprestaspeedpack');
            ++$count;
        } elseif ((int) Configuration::get('PS_SMARTY_FORCE_COMPILE') === _PS_SMARTY_FORCE_COMPILE_) {
            $diagnostic['error'][$count] = [];
            $diagnostic['error'][$count]['msg'] = $this->module->l('You must not use "Force compilation"; keep it enabled only when developping or modifying your theme or a module', 'jprestaspeedpack');
            $diagnostic['error'][$count]['link'] = $this->context->link->getAdminLink('AdminPerformance');
            $diagnostic['error'][$count]['link_title'] = $this->module->l('Resolve this issue in Performances page', 'jprestaspeedpack');
            ++$count;
        }
        if (!$this->isProtocolH2OrH3Used()) {
            if (!Configuration::get('PS_CSS_THEME_CACHE')) {
                $diagnostic['warn'][$count] = [];
                $diagnostic['warn'][$count]['msg'] = $this->module->l('You should enable smart cache (CCC) for CSS', 'jprestaspeedpack');
                $diagnostic['warn'][$count]['link'] = $this->context->link->getAdminLink('AdminPerformance');
                $diagnostic['warn'][$count]['link_title'] = $this->module->l('Resolve this issue in Performances page', 'jprestaspeedpack');
                ++$count;
            }
            if (!Configuration::get('PS_JS_THEME_CACHE')) {
                $diagnostic['warn'][$count] = [];
                $diagnostic['warn'][$count]['msg'] = $this->module->l('You should enable smart cache (CCC) for Javascript', 'jprestaspeedpack');
                $diagnostic['warn'][$count]['link'] = $this->context->link->getAdminLink('AdminPerformance');
                $diagnostic['warn'][$count]['link_title'] = $this->module->l('Resolve this issue in Performances page', 'jprestaspeedpack');
                ++$count;
            }
        }

        if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '<')) {
            if (!Configuration::get('PS_HTML_THEME_COMPRESSION')) {
                $diagnostic['warn'][$count] = [];
                $diagnostic['warn'][$count]['msg'] = $this->module->l('You should enable HTML compression', 'jprestaspeedpack');
                $diagnostic['warn'][$count]['link'] = $this->context->link->getAdminLink('AdminPerformance');
                $diagnostic['warn'][$count]['link_title'] = $this->module->l('Resolve this issue in Performances page', 'jprestaspeedpack');
                ++$count;
            }
            if (!Configuration::get('PS_JS_HTML_THEME_COMPRESSION')) {
                $diagnostic['warn'][$count] = [];
                $diagnostic['warn'][$count]['msg'] = $this->module->l('You should enable Javascript compression in HTML', 'jprestaspeedpack');
                $diagnostic['warn'][$count]['link'] = $this->context->link->getAdminLink('AdminPerformance');
                $diagnostic['warn'][$count]['link_title'] = $this->module->l('Resolve this issue in Performances page', 'jprestaspeedpack');
                ++$count;
            }
            if (JprestaUtils::version_compare(_PS_VERSION_, '1.6', '>')) {
                if (!Configuration::get('PS_JS_DEFER')) {
                    $diagnostic['warn'][$count] = [];
                    $diagnostic['warn'][$count]['msg'] = $this->module->l('You should defer Javascript at the bottom of the page', 'jprestaspeedpack');
                    $diagnostic['warn'][$count]['link'] = $this->context->link->getAdminLink('AdminPerformance');
                    $diagnostic['warn'][$count]['link_title'] = $this->module->l('Resolve this issue in Performances page', 'jprestaspeedpack');
                    ++$count;
                }
            }
        }
        if (!Configuration::get('PS_HTACCESS_CACHE_CONTROL') && (!isset($_SERVER['SERVER_SOFTWARE']) || stripos($_SERVER['SERVER_SOFTWARE'], 'nginx') === false)) {
            $diagnostic['error'][$count] = [];
            $diagnostic['error'][$count]['msg'] = $this->module->l('You must enable Apache optimisations in order for images to be cached by browsers', 'jprestaspeedpack');
            $diagnostic['error'][$count]['link'] = $this->context->link->getAdminLink('AdminPerformance');
            $diagnostic['error'][$count]['link_title'] = $this->module->l('Resolve this issue in Performances page', 'jprestaspeedpack');
            ++$count;
        }
        if (_PS_CACHE_ENABLED_) {
            $diagnostic['info'][$count] = [];
            $diagnostic['info'][$count]['msg'] = $this->module->l('When using a caching system make sure that it is faster, do some tests because sometimes it\'s slower.', 'jprestaspeedpack');
            ++$count;
        }
        if (Configuration::get('PS_DETECT_COUNTRY')) {
            $diagnostic['warn'][$count] = [];
            $diagnostic['warn'][$count]['msg'] = $this->module->l('You should disable the option that selects the default country based on the browser language. This may result in incorrect tax rules being applied. This option is in menu International - Localization', 'jprestaspeedpack');
            ++$count;
        }
        $diagnostic['count'] = $count;

        return $diagnostic;
    }

    private function autoconf(&$msg_infos, &$msg_warnings, &$msg_errors)
    {
        $datas = [];
        $datas[] = '';
        $datas['jprestaspeedpackEdition'] = $this->module->name;
        $datas['jprestaspeedpackVersion'] = $this->module->version;
        $datas['prestashopVersion'] = _PS_VERSION_;
        $datas['shopUrl'] = $this->context->shop->getBaseURL(true);
        $datas['shopName'] = $this->context->shop->name;
        $datas['adminName'] = '';
        $datas['adminEmail'] = '';
        $datas['language'] = $this->context->language->iso_code;
        $admins = Employee::getEmployeesByProfile(_PS_ADMIN_PROFILE_, true);
        if (!empty($admins)) {
            $datas['adminName'] = $admins[0]['firstname'] . ' ' . $admins[0]['lastname'];
            $datas['adminEmail'] = $admins[0]['email'];
        }
        $datas['theme'] = [];
        if (isset($this->context->shop->theme)) {
            $datas['theme']['name'] = $this->context->shop->theme->get('name');
            $datas['theme']['displayName'] = $this->context->shop->theme->get('display_name');
            $datas['theme']['version'] = $this->context->shop->theme->get('version');
            $datas['theme']['author'] = $this->context->shop->theme->get('author.name');
        } else {
            $datas['theme']['name'] = $this->context->shop->theme_name;
            $datas['theme']['displayName'] = $this->context->shop->theme_name;
            $datas['theme']['version'] = 0;
            $datas['theme']['author'] = '';
        }
        $datas['modules'] = [];
        $modules = Module::getModulesInstalled();
        foreach ($modules as $module) {
            $moduleInstance = Module::getInstanceByName($module['name']);
            if ($moduleInstance !== false) {
                $datas['modules'][$module['name']] = [];
                $datas['modules'][$module['name']]['displayName'] = $moduleInstance->displayName;
                $datas['modules'][$module['name']]['version'] = $module['version'];
                $datas['modules'][$module['name']]['active'] = $module['active'];
                $datas['modules'][$module['name']]['author'] = $moduleInstance->author;
                $datas['modules'][$module['name']]['description'] = $moduleInstance->description;
            }
        }

        // use key 'http' even if you send the request to https://...
        $options = [
            'http' => [
                'header' => "Content-type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($datas),
            ],
        ];
        $context = stream_context_create($options);
        $result = Tools::file_get_contents(JprestaApi::getAutoconfURL(), false, $context);
        if ($result === false) {
            $result = JprestaUtils::file_get_contents_curl(JprestaApi::getAutoconfURL(), 10, $options);
        }
        if ($result !== false) {
            $conf = json_decode($result, true);
            if ($conf !== null) {
                // Javascript to execute
                Configuration::updateValue('pagecache_cfgadvancedjs', $conf['javascript']);
                // Cache for logged in visitors?
                if (array_key_exists('cacheForLoggedInUsers', $conf['options'])) {
                    Configuration::updateValue('pagecache_skiplogged',
                        !empty($conf['options']['cacheForLoggedInUsers']) ? true : false);
                } else {
                    Configuration::updateValue('pagecache_skiplogged', false);
                }
                // Dynamic modules
                $pagecache_dyn_hooks = '';
                $pagecache_dyn_widgets = '';
                foreach ($conf['modules'] as $moduleName => $moduleConf) {
                    // Hooks
                    if (array_key_exists('hooks', $moduleConf) && is_array($moduleConf['hooks'])) {
                        foreach ($moduleConf['hooks'] as $hookName => $hookConf) {
                            if ($hookConf['dynamic']) {
                                $empty = array_key_exists('empty', $hookConf) && !empty($hookConf['empty']) ? 1 : 0;
                                $pagecache_dyn_hooks .= $hookName . '|' . $moduleName . '|' . $empty . ',';
                                $aliases = JprestaUtils::getCanonicalHookNames();
                                foreach ($aliases as $alias => $newname) {
                                    if (Tools::strtolower($newname) === $hookName) {
                                        $pagecache_dyn_hooks .= $alias . '|' . $moduleName . '|' . $empty . ',';
                                    }
                                }
                            }
                        }
                    }
                    // Widgets
                    if (array_key_exists('widgets', $moduleConf) && is_array($moduleConf['widgets'])) {
                        foreach ($moduleConf['widgets'] as $hookName => $hookConf) {
                            if ($hookConf['dynamic']) {
                                $empty = array_key_exists('empty', $hookConf) && !empty($hookConf['empty']) ? 1 : 0;
                                $pagecache_dyn_widgets .= $moduleName . '|' . $hookName . '|' . $empty . ',';
                            }
                        }
                    }
                }
                Configuration::updateValue('pagecache_dyn_hooks', $pagecache_dyn_hooks);
                Configuration::updateValue('pagecache_dyn_widgets', $pagecache_dyn_widgets);
                // Messages
                foreach ($conf['messages'] as $message) {
                    if (array_key_exists('message', $message) && !empty($message['message']) && array_key_exists('type',
                        $message) && !empty($message['type'])) {
                        if ($message['type'] === 'WARN') {
                            $msg_warnings[] = $message['message'];
                        } elseif ($message['type'] === 'ERROR') {
                            $msg_infos[] = $message['message'];
                        } elseif ($message['type'] === 'INFO') {
                            $msg_errors[] = $message['message'];
                        }
                    }
                }
            } else {
                $msg_warnings[] = $this->module->l('Cannot reach the auto-configuration server (error), sorry but you have to configure the module manually.', 'jprestaspeedpack');
            }
        // Ignore errors
        } else {
            $msg_warnings[] = $this->module->l('Cannot reach the auto-configuration server, sorry but you have to configure the module manually.', 'jprestaspeedpack');
        }
        // Ignore errors
    }

    private static function getDynamicWidgets()
    {
        $dynWidgets = [];
        $dyn_widgets_cfg = Configuration::get('pagecache_dyn_widgets', '');
        $dyn_widgets = explode(',', $dyn_widgets_cfg);
        foreach ($dyn_widgets as $dyn_widget) {
            if (!empty($dyn_widget)) {
                list($widget_name, $hook_name, $empty_box) = array_pad(explode('|', $dyn_widget), 3, 0);
                $widgetinstance = Module::getInstanceByName($widget_name);
                if ($widgetinstance) {
                    $dynWidgets[] = [
                        'id_module' => $widgetinstance->id,
                        'display_name' => $widgetinstance->displayName,
                        'name' => $widget_name,
                        'version' => $widgetinstance->version,
                        'author' => $widgetinstance->author,
                        'description' => $widgetinstance->description,
                        'hook' => $hook_name,
                        'empty_box' => $empty_box ? 1 : 0,
                    ];
                }
            }
        }

        return $dynWidgets;
    }

    private static function getDynamicHooks()
    {
        $hooksModules = [];
        $dyn_hooks = Configuration::get('pagecache_dyn_hooks', '');
        $hooks_modules = explode(',', $dyn_hooks);
        foreach ($hooks_modules as $hook_module) {
            if (!empty($hook_module)) {
                list($hook, $module, $empty_box) = array_pad(explode('|', $hook_module), 3, 0);
                if (!isset($hooksModules[$hook])) {
                    $hooksModules[$hook] = [];
                }
                $hooksModules[$hook][$module] = ['empty_box' => $empty_box];
            }
        }

        return $hooksModules;
    }

    private function getCronClearCacheURL()
    {
        $urls = [];
        foreach (Shop::getContextListShopID() as $shopId) {
            $shop = new Shop($shopId);
            $url = $shop->getBaseURL(true);
            if (JprestaUtils::strlen($url) > 0) {
                $urls[] = $url . '?fc=module&module=' . $this->module->name . '&controller=clearcache&token=' . JprestaUtils::getSecurityToken($shopId);
            }
        }

        return $urls;
    }

    private function getCacheWarmerURL()
    {
        return $this->context->shop->getBaseURL(true) . '?fc=module&module=' . $this->module->name . '&controller=cachewarmer&action=GetShopInfos&shopId=' . $this->context->shop->id . '&token=' . JprestaUtils::getSecurityToken();
    }

    /**
     * @return array List of tables used by the module with name, size, row count
     */
    private function getDatasDatabases()
    {
        $dbname = JprestaUtils::getDatabaseName();
        $sql = 'SELECT table_name AS `Table`,table_rows as `Row count`, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS `Size in MB`
            FROM information_schema.TABLES
            WHERE table_schema = \'' . $dbname . '\' AND table_name like \'' . _DB_PREFIX_ . 'jm\\_%\';';

        return Db::getInstance()->executeS($sql);
    }

    private function getInstallationErrors()
    {
        $errors = [];

        // Check tokens
        $token_enabled = (int) Configuration::get('PS_TOKEN_ENABLE') == 1 ? true : false;
        if ($token_enabled) {
            $errors[] = $this->module->l('You must disable tokens in order for cached pages to do ajax call. Go in general preferences and disable "Improve front security" option.', 'jprestaspeedpack');
        }

        // Check for bvkdispatcher module
        if (Module::isInstalled('bvkseodispatcher')) {
            $errors[] = $this->module->l('Module "SEO Pretty URL Module" (bvkseodispatcher) is not compatible with PageCache because it does not respect Prestashop standards. You have to choose between this module and PageCache.', 'jprestaspeedpack');
        }

        // Check for overrides (after an upgrade it is disabled)
        if (!Jprestaspeedpack::isOverridesEnabled()) {
            $errors[] = $this->module->l('Overrides are disabled in Performances tab so PageCache is disabled.', 'jprestaspeedpack');
        }

        return $errors;
    }
}
