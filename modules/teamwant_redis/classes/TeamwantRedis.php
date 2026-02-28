<?php
/**
 * Redis Cache
 * Version: 3.0.0
 * Copyright (c) 2020-2023. Mateusz Szymański Teamwant
 * https://teamwant.pl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Teamwant <kontakt@teamwant.pl>
 * @copyright Copyright 2020-2023 © Teamwant Mateusz Szymański All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  Teamwant
 */

namespace Teamwant\Prestashop17\Redis\Classes;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Teamwant\Prestashop17\Redis\Classes\Cache\Redis;
use Teamwant_redis\OverrideSrc\OverrideHookFile;
use Teamwant_redis\OverrideSrc\OverrideSrcParametersFile;

trait TeamwantRedis
{
    public $prestashopVersion = 800;
    protected $config_form = false;

    public function disableBrowserCacheForAdmin()
    {
        if (!empty(\Context::getContext()->controller) && \Context::getContext()->controller->php_self === 'AdminPerformance') {
            try {
                // czesto po zapisaniu ustawien w performance, przegladarka laduje syf z cache
                header('Cache-Control: no-cache, no-store, must-revalidate');
                header('Pragma: no-cache');
                header('Expires: 0');
            } catch (\Throwable $e) {
            }
        }
    }

    public function initRawTranslations()
    {
        $this->l("Can't write file %s, please check \"rw\" permissions on this file");
        $this->l('Actions');
        $this->l('Add server');
        $this->l('Alias');
        $this->l('Any record is not valid');
        $this->l('Connection failed');
        $this->l('Error!');
        $this->l('Host');
        $this->l('Password');
        $this->l('Please enter required fields, Host and port');
        $this->l('Please input any record');
        $this->l('Port');
        $this->l('Remove row');
        $this->l('Save success.');
        $this->l('Save');
        $this->l('Scheme');
        $this->l('Test connection');
        $this->l('Url to healthcheck:');
        $this->l('Username');
        $this->l('Wrong method, allowed: %s');
        $this->l('You need administrator rights to make changes on this page.');
        $this->l('Are you sure to save the changes? The redis configuration has not been saved.');
        $this->l('Server %d is duplicated');
        $this->l('File %s must be writable.');
        $this->l('File %s must be readable.');
        $this->l('Can\'t create file %s.');
        $this->l('File %s must be writable. Please change permissions on this file and reinstal Redis cache module.');
        $this->l('File %s must be readable. Please change permissions on this file and reinstal Redis cache module.');
        $this->l('Show More');
        $this->l('Show Less');
        $this->l('Index');

        // 1.3.0
        $this->l('Payload is invalid.');
        $this->l('Configuration is not valid');
        $this->l('Field %s is required');
        $this->l('Field %s must be true or false');
        $this->l('Additional configuration');
        $this->l('Use cache in admin panel?');
        $this->l('Use prefix for keys?');
        $this->l('Key prefix');
        $this->l('Leave blank to generate automatically');

        // 3.0
        $this->l('If your site is still running slowly, try changing your server settings to <b><a target="_blank" href="https://gist.github.com/zixxus/6d7b1a8af93dec53d5ce22cbbdd313c0">unix socket</a></b>. You can also change your database engine to <b><a target="_blank" href="https://docs.keydb.dev/docs/download">KeyDB</a></b> or <b><a target="_blank" href="https://www.dragonflydb.io/docs/getting-started">Dragonflydb</a></b>.');
        $this->l('Default cache timeout');
        $this->l('blank or 0 for unlimited (minutes)');
        $this->l('If your store uses multistore, checking this option can resolve duplicate data errors.');
        $this->l('We recommend that this option be turned off, it can cause problems with invoice numbering, etc.');
        $this->l('Disable cache for webservice (API)');
        $this->l('Disable cache for product listing (category, new product, best sales etc.)');
        $this->l('Update product after order create (When a product is purchased, its product page will be refreshed)');
        $this->l('CRON for categories');
        $this->l('CRON for products');
        $this->l('Url for clear cache');
        $this->l('If these settings are not enough, you can add code in the PHP function or method:');
        $this->l('Ignored module controllers');
        $this->l('Enter the controller class to be skipped, such as psgdprExportDataToCsvModuleFrontController, Ps_EmailAlertsAccountModuleFrontController, etc.');
        $this->l('Disable cache for modules');
        $this->l('[BETA] New cache refresh method');

    }

    public function validateFilePrivilagesForTeamwantRedisModule()
    {
        load_Teamwant_redis_OverrideSrc();

        if (!is_writable(OverrideSrcParametersFile::FILE)) {
            $this->adminDisplayWarning(self::staticModuleTranslate('File %s must be writable. Please change permissions on this file and reinstal Redis cache module.', [
                OverrideSrcParametersFile::FILE,
            ]));
        }

        if (!is_readable(OverrideSrcParametersFile::FILE)) {
            $this->adminDisplayWarning(self::staticModuleTranslate('File %s must be readable. Please change permissions on this file and reinstal Redis cache module.', [
                OverrideSrcParametersFile::FILE,
            ]));
        }

        if (version_compare(_PS_VERSION_, '1.7.7.5', '<=')) {
            if (!is_writable(\Teamwant_redis\OverrideSrc\OverrideSrcCachingType::FILE)) {
                $this->adminDisplayWarning(self::staticModuleTranslate('File %s must be writable. Please change permissions on this file and reinstal Redis cache module.', [
                    \Teamwant_redis\OverrideSrc\OverrideSrcCachingType::FILE,
                ]));
            }

            if (!is_readable(\Teamwant_redis\OverrideSrc\OverrideSrcCachingType::FILE)) {
                $this->adminDisplayWarning(self::staticModuleTranslate('File %s must be readable. Please change permissions on this file and reinstal Redis cache module.', [
                    \Teamwant_redis\OverrideSrc\OverrideSrcCachingType::FILE,
                ]));
            }
        }

        if (!is_writable(OverrideHookFile::FILE)) {
            $this->adminDisplayWarning(self::staticModuleTranslate('File %s must be writable. Please change permissions on this file and reinstal Redis cache module.', [
                OverrideHookFile::FILE,
            ]));
        }

        if (!is_readable(OverrideHookFile::FILE)) {
            $this->adminDisplayWarning(self::staticModuleTranslate('File %s must be readable. Please change permissions on this file and reinstal Redis cache module.', [
                OverrideHookFile::FILE,
            ]));
        }

        if (!file_exists(TEAMWANT_REDIS_ROOT_DIR . '/config/_RedisConfiguration.php')) {
            @file_put_contents(TEAMWANT_REDIS_ROOT_DIR . '/config/_RedisConfiguration.php', '');
        }

        if (file_exists(TEAMWANT_REDIS_ROOT_DIR . '/config/_RedisConfiguration.php')) {
            if (!is_writable(TEAMWANT_REDIS_ROOT_DIR . '/config/_RedisConfiguration.php')) {
                $this->adminDisplayWarning(self::staticModuleTranslate('File %s must be writable.', [
                    TEAMWANT_REDIS_ROOT_DIR . '/config/_RedisConfiguration.php',
                ]));
            }

            if (!is_readable(TEAMWANT_REDIS_ROOT_DIR . '/config/_RedisConfiguration.php')) {
                $this->adminDisplayWarning(self::staticModuleTranslate('File %s must be readable.', [
                    TEAMWANT_REDIS_ROOT_DIR . '/config/_RedisConfiguration.php',
                ]));
            }
        } else {
            $this->adminDisplayWarning(self::staticModuleTranslate('Can\'t create file %s.', [
                TEAMWANT_REDIS_ROOT_DIR . '/config/_RedisConfiguration.php',
            ]));
        }
    }

    public static function staticModuleTranslate($string, $sprintf = null)
    {
        $r = \Translate::getModuleTranslation(
            'teamwant_redis',
            $string,
            'teamwant_redis'
        );

        if (!$sprintf) {
            return $r;
        }

        if (is_array($sprintf)) {
            return vsprintf($r, $sprintf);
        }

        return sprintf($r, $sprintf);
    }

    public function install()
    {
        // create override dir - ten katalog wiele razy powodowal problemy
        if (!file_exists(_PS_ROOT_DIR_ . '/override/controllers/front/listing')) {
            mkdir(_PS_ROOT_DIR_ . '/override/controllers/front/listing');
        }

        $this->createCustomOverride($this->prestashopVersion);

        // custom override presta files
        load_Teamwant_redis_OverrideSrc();

        $out = parent::install()
            && $this->registerHook('backOfficeHeader')
            && $this->registerHook('actionClearCompileCache');

        // custom override presta files
        $this->registerAdminControllers();

        // tworzenie startowego pliku
        $this->createDefaultConfigFile();

        // from 1.6.2
        OverrideHookFile::install();

        return $out;
    }

    public function createCustomOverride($version)
    {
        if (!file_exists(TEAMWANT_REDIS_ROOT_DIR . '/override')) {
            mkdir(TEAMWANT_REDIS_ROOT_DIR . '/override');
            $this->dirCopy(
                TEAMWANT_REDIS_ROOT_DIR . '/overrideVersions/' . $version . '/',
                TEAMWANT_REDIS_ROOT_DIR . '/override/'
            );
        }
    }

    public function dirCopy(string $src, string $dest)
    {
        foreach (scandir($src) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $newFile = $src . '/' . $file;

            if (!is_readable($newFile)) {
                continue;
            }

            if (is_dir($newFile)) {
                if (!file_exists($dest . '/' . $file)) {
                    @mkdir($dest . '/' . $file);
                }
                $this->dirCopy($newFile . '/', $dest . '/' . $file . '/');
            } else {
                copy($newFile, $dest . '/' . $file);
            }
        }
    }

    public function registerAdminControllers()
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from('tab', 't');
        $sql->where('t.class_name = "AdminRedisConfiguration"');
        $sql->orderBy('position');

        if (!\Db::getInstance()->executeS($sql)) {
            $tab = new \Tab();
            $tab->active = true;
            $tab->class_name = 'AdminRedisConfiguration';
            $tab->id_parent = -1;
            $tab->module = $this->name;

            $tab->name = [];

            foreach (\Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = 'AdminRedisConfiguration';
            }

            $tab->add();
        }
    }

    private function createDefaultConfigFile()
    {
        $template_file = TEAMWANT_REDIS_ROOT_DIR . 'config/_RedisConfiguration.php';

        if (
            !file_exists($template_file)
            || (file_exists($template_file) && \Tools::file_get_contents($template_file) === '')
        ) {
            $fileManager = new FileManager();
            $validator = new Validator();
            $payload = [
                'teamwant_redis_row' => [
                    [
                        'host' => '127.0.0.1',
                        'port' => '6379',
                        'scheme' => 'tcp',
                    ],
                ],
                'twredis' => [
                    'use_cache_admin' => false,
                    'prefix' => 'default',
                    'use_prefix' => false,
                    'use_multistore' => false,
                ],
            ];

            $items = $validator->validateAjaxProcessSaveConfigurationTable($payload);

            if ($items['result']) {
                $newConfig = json_encode(
                    $items['items'],
                    JSON_UNESCAPED_UNICODE
                );

                $fileManager->saveConfigFile('_RedisConfiguration.php', $newConfig);
            }
        }

        return true;
    }

    public function uninstall()
    {
        // custom override presta files
        load_Teamwant_redis_OverrideSrc();
        OverrideSrcParametersFile::uninstall();

        // from 1.6.2
        OverrideHookFile::uninstall();

        $this->uninstallAdminControllers();
        $this->removeCustomOverride();

        return parent::uninstall();
    }

    public function uninstallAdminControllers()
    {
        return \Db::getInstance()->query('
            DELETE FROM `' . _DB_PREFIX_ . "tab` WHERE `module` = 'teamwant_redis'
        ");
    }

    /**
     * @deprecated
     *
     * @return void
     */
    public function removeCustomOverride()
    {
        // if (file_exists(TEAMWANT_REDIS_ROOT_DIR . '/override')) {
        //     $this->deleteDirectory(TEAMWANT_REDIS_ROOT_DIR . '/override');
        // }
    }

    private function deleteDirectory($dir)
    {
        if (file_exists($dir)) {
            if (is_dir($dir)) {
                $files = scandir($dir);

                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    $this->deleteDirectory($dir . '/' . $file);
                }

                rmdir($dir);
            } elseif (is_file($dir)) {
                unlink($dir);
            }
        }
    }

    public function enable($force_all = false)
    {
        $this->createCustomOverride($this->prestashopVersion);

        // custom override presta files
        load_Teamwant_redis_OverrideSrc();
        $this->registerHook('backOfficeHeader');
        $this->registerHook('actionClearCompileCache');
        $this->registerAdminControllers();

        // from 1.6.2
        OverrideHookFile::install();

        return parent::enable($force_all);
    }

    public function disable($force_all = false)
    {
        // custom override presta files
        load_Teamwant_redis_OverrideSrc();
        OverrideSrcParametersFile::uninstall();
        $this->uninstallAdminControllers();
        $this->removeCustomOverride();

        // from 1.6.2
        OverrideHookFile::uninstall();
        \Tools::clearAllCache();

        return parent::disable($force_all);
    }

    public function hookDisplayBackOfficeHeader()
    {
        // custom js variables
        \Media::addJsDef([
            'token_AdminRedisConfiguration' => \Tools::getAdminTokenLite('AdminRedisConfiguration'),
        ]);

        $controller = \Tools::getValue('controller', '');

        if ($controller === 'AdminPerformance' || \Tools::getValue('configure', '') === 'teamwant_redis') {
            // custom js variables
            \Media::addJsDef([
                'tw_redis_lang_save_change_on_performance' => $this->l('Are you sure to save the changes? The redis configuration has not been saved.'),
                'tw_redis_langs' => [
                    0 => $this->l('Are you sure?'),
                ],
            ]);

            $this->context->controller->addjQueryPlugin([
                'select2',
            ]);

            // custom js
            if (version_compare(_PS_VERSION_, '1.7.8.0', '<')) {
                $this->context->controller->addJs($this->_path . 'views/js/redis-admin-1770.js');
            } elseif (version_compare(_PS_VERSION_, '8.0.0.0', '<')) {
                $this->context->controller->addJs($this->_path . 'views/js/redis-admin-1780.js');
            } elseif (version_compare(_PS_VERSION_, '9.0.0.0', '<')) {
                $this->context->controller->addJs($this->_path . 'views/js/redis-admin-80.js');
            } else {
                $this->context->controller->addJs($this->_path . 'views/js/redis-admin.js');
            }

            // custom css (zostawiam wersje 1770)
            $this->context->controller->addCSS($this->_path . 'views/css/redis-admin.css');
        }
    }

    public function hookActionClearCompileCache()
    {
        if (
            defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_
            && defined('_PS_CACHING_SYSTEM_') && _PS_CACHING_SYSTEM_ === 'Redis'
        ) {
            if (!class_exists(Redis::class)) {
                require_once _PS_MODULE_DIR_ . 'teamwant_redis/vendor/autoload.php';
            }

            try {
                Redis::flushAllDb();
            } catch (\Exception $e) {
                if (_PS_MODE_DEV_) {
                    throw new \PrestaShopException('Warning: Failed to clear the Redis Cache. The error was: ' . $e->getMessage());
                }
            }
        }
    }

    public function hookTwReloadConfigurationCache()
    {
        if (empty(\Cache::getInstance()) || get_class(\Cache::getInstance()) !== 'Teamwant\Prestashop17\Redis\Classes\Cache\Redis') {
            return;
        }
        if (isset($_REQUEST['_DISABLE_REDIS_']) && $_REQUEST['_DISABLE_REDIS_'] === true) {
            return;
        }
        $sql = 'SELECT c.`name`, cl.`id_lang`, IF(cl.`id_lang` IS NULL, c.`value`, cl.`value`) AS value, c.id_shop_group, c.id_shop
               FROM `' . _DB_PREFIX_ . bqSQL('configuration') . '` c
               LEFT JOIN `' . _DB_PREFIX_ . bqSQL('configuration') . '_lang` cl ON (c.`' . bqSQL(
            'id_configuration'
        ) . '` = cl.`' . bqSQL('id_configuration') . '`)';

        \Cache::getInstance()->delete(\Cache::getInstance()->getQueryHash($sql));
    }

    public function getBackofficeConfigurationContentData()
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        $redis_healthcheck_key = \Configuration::get(Redis::REDIS_HEALTHCHECK_KEY);

        if (!$redis_healthcheck_key) {
            $length = 32;

            if (function_exists('random_bytes')) {
                try {
                    $redis_healthcheck_key = bin2hex(random_bytes($length));
                } catch (\Exception $e) {
                    if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
                        @trigger_error($e, E_USER_NOTICE);
                    }
                }
            } else if (function_exists('openssl_random_pseudo_bytes')) {
                $redis_healthcheck_key = bin2hex(openssl_random_pseudo_bytes($length));
            } else if (function_exists('mcrypt_create_iv')) {
                $redis_healthcheck_key = bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
            }

            if (!$redis_healthcheck_key) {
                $redis_healthcheck_key = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $length)), 0, $length);
            }

            \Configuration::updateValue(Redis::REDIS_HEALTHCHECK_KEY, $redis_healthcheck_key);
        }

        return [
            // Use cache for stock manager
            'twredis_use_cache_for_stock_manager' => \Tools::getValue(
                'twredis_use_cache_for_stock_manager',
                \Configuration::get('twredis_use_cache_for_stock_manager', null, null, null, 0)
            ),

            // Use cache for hook list
            'twredis_use_cache_for_hook_list' => \Tools::getValue(
                'twredis_use_cache_for_hook_list',
                \Configuration::get('twredis_use_cache_for_hook_list', null, null, null, 0)
            ),

            // twredis_disable_product_price_cache
            'twredis_disable_product_price_cache' => \Tools::getValue(
                'twredis_disable_product_price_cache',
                \Configuration::get('twredis_disable_product_price_cache', null, null, null, 0)
            ),

            // twredis_disable_cache_for_order_page
            'twredis_disable_cache_for_order_page' => \Tools::getValue(
                'twredis_disable_cache_for_order_page',
                \Configuration::get('twredis_disable_cache_for_order_page', null, null, null, 1)
            ),

            // twredis_disable_cache_for_address_payment_and_carrier
            'twredis_disable_cache_for_address_payment_and_carrier' => \Tools::getValue(
                'twredis_disable_cache_for_address_payment_and_carrier',
                \Configuration::get('twredis_disable_cache_for_address_payment_and_carrier', null, null, null, 1)
            ),

            // twredis_disable_webservice
            'twredis_disable_webservice' => \Tools::getValue(
                'twredis_disable_webservice',
                \Configuration::get('twredis_disable_webservice', null, null, null, 1)
            ),

            // twredis_disable_productlisting
            'twredis_disable_productlisting' => \Tools::getValue(
                'twredis_disable_productlisting',
                \Configuration::get('twredis_disable_productlisting', null, null, null, 0)
            ),

            // twredis_update_product_after_order
            'twredis_update_product_after_order' => \Tools::getValue(
                'twredis_update_product_after_order',
                \Configuration::get('twredis_update_product_after_order', null, null, null, 0)
            ),

            // twredis_new_cache_refresh
            'twredis_new_cache_refresh' => \Tools::getValue(
                'twredis_new_cache_refresh',
                \Configuration::get('twredis_new_cache_refresh', null, null, null, 0)
            ),

            // twredis_ignoredmodulecontrollers
            'twredis_ignoredmodulecontrollers' => \Tools::getValue(
                'twredis_ignoredmodulecontrollers',
                \Configuration::get('twredis_ignoredmodulecontrollers', null, null, null, '')
            ),

            // twredis_ignoredmodules
            'twredis_ignoredmodules' => \Tools::getValue(
                'twredis_ignoredmodules',
                \Configuration::get('twredis_ignoredmodules', null, null, null, '')
            ),

            // twredis_blacklist
            'twredis_blacklist' => \Tools::getValue(
                'twredis_blacklist',
                \Configuration::get('twredis_blacklist', null, null, null, '')
            ),

            'cron-for-categories' => '/usr/bin/curl -k -L --max-redirs 10000 "' . $this->context->link->getModuleLink(
                'teamwant_redis',
                'cron',
                [
                    'redis_healthcheck_key' => $redis_healthcheck_key,
                    'redis_cron_type' => 'categories',
                ],
                \Configuration::get('PS_SSL_ENABLED')
            ) . '" ',

            'cron-for-products' => '/usr/bin/curl -k -L --max-redirs 10000 "' . $this->context->link->getModuleLink(
                'teamwant_redis',
                'cron',
                [
                    'redis_healthcheck_key' => $redis_healthcheck_key,
                    'redis_cron_type' => 'products',
                ],
                \Configuration::get('PS_SSL_ENABLED')
            ) . '" ',

            'url-for-cacheclean' => $this->context->link->getModuleLink(
                'teamwant_redis',
                'cacheclean',
                [
                    'redis_healthcheck_key' => $redis_healthcheck_key,
                ],
                \Configuration::get('PS_SSL_ENABLED')
            ),

        ];
    }

    /**
     * @throws PrestaShopException
     *
     * @return string
     */
    public function getBackofficeConfigurationContent()
    {
        $this->bootstrap = true;

        [$cacheConfigHelper, $cacheConfigForm] = $this->getCacheConfigForm();

        $this->context->smarty->assign([
            'cache_config' => $cacheConfigHelper->generateForm([$cacheConfigForm]),
            'cache_disable' => [
                'action' => 'submit_twredis_cache_disable',
                'url' => $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name,
                'token' => \Tools::getAdminTokenLite('AdminModules'),
                'data' => $this->getBackofficeConfigurationContentData(),
            ],
            'cache_blacklist' => [
                'action' => 'submit_twredis_cache_blacklist',
                'url' => $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name,
                'token' => \Tools::getAdminTokenLite('AdminModules'),
                'data' => $this->getBackofficeConfigurationContentData(),
            ],
        ]);

        return $this->context->smarty->fetch(TEAMWANT_REDIS_ROOT_DIR . 'views/templates/admin/configuration.tpl');
    }

    private function getCacheConfigForm()
    {
        $fieldsValue = $this->getBackofficeConfigurationContentData();
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Cache configuration'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Use cache for stock manager'),
                        'name' => 'twredis_use_cache_for_stock_manager',
                        'is_bool' => true,
                        'required' => true,
                        'values' => [
                            [
                                'id' => 'use_cache_for_stock_manager_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'use_cache_for_stock_manager_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Use cache for hook list'),
                        'name' => 'twredis_use_cache_for_hook_list',
                        'is_bool' => true,
                        'required' => true,
                        'values' => [
                            [
                                'id' => 'use_cache_for_hook_list_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'use_cache_for_hook_list_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Disable product price cache'),
                        'name' => 'twredis_disable_product_price_cache',
                        'is_bool' => true,
                        'required' => true,
                        'values' => [
                            [
                                'id' => 'twredis_disable_product_price_cache_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'twredis_disable_product_price_cache_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Disable cache for order page'),
                        'name' => 'twredis_disable_cache_for_order_page',
                        'is_bool' => true,
                        'required' => true,
                        'values' => [
                            [
                                'id' => 'twredis_disable_cache_for_order_page_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'twredis_disable_cache_for_order_page_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Disable cache for address, payment methods and carriers'),
                        'name' => 'twredis_disable_cache_for_address_payment_and_carrier',
                        'is_bool' => true,
                        'required' => true,
                        'values' => [
                            [
                                'id' => 'twredis_disable_cache_for_address_payment_and_carrier_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'twredis_disable_cache_for_address_payment_and_carrier_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Disable cache for webservice (API)'),
                        'name' => 'twredis_disable_webservice',
                        'is_bool' => true,
                        'required' => true,
                        'values' => [
                            [
                                'id' => 'twredis_disable_webservice_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'twredis_disable_webservice_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Disable cache for product listing (category, new product, best sales etc.)'),
                        'name' => 'twredis_disable_productlisting',
                        'is_bool' => true,
                        'required' => true,
                        'values' => [
                            [
                                'id' => 'twredis_disable_productlisting_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'twredis_disable_productlisting_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Update product after order create (When a product is purchased, its product page will be refreshed)'),
                        'name' => 'twredis_update_product_after_order',
                        'is_bool' => true,
                        'required' => true,
                        'values' => [
                            [
                                'id' => 'twredis_update_product_after_order_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'twredis_update_product_after_order_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('[BETA] New cache refresh method'),
                        'name' => 'twredis_new_cache_refresh',
                        'is_bool' => true,
                        'required' => true,
                        'values' => [
                            [
                                'id' => 'twredis_new_cache_refresh_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'twredis_new_cache_refresh_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', [], 'Admin.Global'),
                            ],
                        ],
                    ],

                    [
                        'type' => 'text',
                        'label' => $this->l('CRON for categories'),
                        'name' => 'cron-for-categories',
                        'required' => false,
                        'disabled' => true,
                        'value' => 'asd',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('CRON for products'),
                        'name' => 'cron-for-products',
                        'required' => false,
                        'disabled' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Url for clear cache'),
                        'name' => 'url-for-cacheclean',
                        'required' => false,
                        'disabled' => true,
                    ],
                ],
                'submit' => [
                    'name' => 'twredis_cache_config',
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
                'buttons' => [],
            ],
        ];

        $helper = new \HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_twredis_cache_config';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = \Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $fieldsValue,
        ];

        $sfContainer = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance();

        if (null !== $sfContainer) {
            /** @var UrlGeneratorInterface $sfRouter */
            $sfRouter = $sfContainer->get('router');
            // $this->context->smarty->assign([
            //    'performance_url' => $sfRouter->generate('admin_performance', []),
            // ]);

            $form['form']['buttons'][] = [
                'href' => $sfRouter->generate('admin_performance', []),
                'title' => $this->l('Edit redis configuration'),
            ];
        }

        return [$helper, $form];
    }

    /**
     * @return false|array
     */
    public static function getCacheConfiguration()
    {
        if (method_exists(self::class, 'getCacheConfigurationData')) {
            return self::getCacheConfigurationData();
        }

        return false;
    }
}
