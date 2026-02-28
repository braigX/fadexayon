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
if (!defined('_PS_VERSION_')) {
    exit;
}

// todo: poogarniac consty w wolnej chwili
require_once _PS_MODULE_DIR_ . 'teamwant_redis/autoload.php';

use Teamwant\Prestashop17\Redis\Classes\Cache\Redis as TwRedis;
use Teamwant\Prestashop17\Redis\Classes\FileManager;
use Teamwant\Prestashop17\Redis\Classes\Validator;

class AdminRedisConfigurationController extends ModuleAdminController
{
    /**
     * @var FileManager
     */
    private $fileManager;

    public function __construct()
    {
        parent::__construct();
        $this->fileManager = new FileManager();
    }

    public function ajaxProcessGetConfigurationTable()
    {
        $this->returnWrongMethod('GET');
        $this->returnSuperadminPermissions();
        $this->renderTable();
    }

    private function returnWrongMethod($type)
    {
        if ($_SERVER['REQUEST_METHOD'] !== $type) {
            header('Content-Type: application/json');
            exit(json_encode([
                'success' => 0,
                'data' => Teamwant_Redis::staticModuleTranslate('Wrong method, allowed: %s', [
                    $type,
                ]),
                'type' => 'alert',
            ]));
        }
    }

    private function returnSuperadminPermissions()
    {
        if (!$this->context->employee->isSuperAdmin()) {
            $template_file = TEAMWANT_REDIS_ROOT_DIR . 'views/templates/admin/error/superadmin.tpl';

            try {
                $content = $this->context->smarty->fetch($template_file);
            } catch (SmartyException $e) {
                exit(json_encode([
                    'success' => 0,
                    'data' => 'SmartyException ' . __FILE__ . ':' . __LINE__,
                    'type' => 'alert',
                ]));
            } catch (Exception $e) {
                exit(json_encode([
                    'success' => 0,
                    'data' => 'Exception ' . __FILE__ . ':' . __LINE__,
                    'type' => 'alert',
                ]));
            }

            header('Content-Type: application/json');
            exit(json_encode([
                'success' => 0,
                'data' => $content,
                'type' => 'html',
            ]));
        }
    }

    private function renderTable($customConfig = null)
    {
        /** @var array $content */
        $config = json_decode(
            $customConfig ? $customConfig : $this->fileManager->parseConfigFile('_RedisConfiguration.php'),
            true
        );

        $redis_healthcheck_key = Configuration::get(TwRedis::REDIS_HEALTHCHECK_KEY);

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

            \Configuration::updateValue(TwRedis::REDIS_HEALTHCHECK_KEY, $redis_healthcheck_key);
        }

        $url_healthcheck = $this->context->link->getModuleLink(
            'teamwant_redis',
            'healthCheck',
            [
                'redis_healthcheck_key' => $redis_healthcheck_key,
            ],
            Configuration::get('PS_SSL_ENABLED')
        );

        $this->setDefaultConfigValuesIsEmpty($config);

        $enableEngineSwitch = false;

        if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
            $enableEngineSwitch = true;
        }

        $this->context->smarty->assign([
            'config' => $config,
            'url_healthcheck' => $url_healthcheck,
            'phpredis_enable' => (extension_loaded('redis')) ? true : false,
            'enableEngineSwitch' => $enableEngineSwitch,
        ]);

        header('Content-Type: application/json');
        exit(json_encode([
            'success' => 1,
            'data' => $this->returnHtmlContentForAdminFile('performance-table.tpl'),
            'type' => 'html',
        ]));
    }

    private function setDefaultConfigValuesIsEmpty(&$config)
    {
        if (empty($config['_servers'])) {
            $config['_servers'] = [];
        }

        if (empty($config['_config'])) {
            $config['_config'] = [];
        }

        if (!isset($config['_config']['use_cache_admin'])) {
            $config['_config']['use_cache_admin'] = 0;
        }

        if (!isset($config['_config']['use_prefix'])) {
            $config['_config']['use_prefix'] = 0;
        }

        if (!isset($config['_config']['use_multistore'])) {
            $config['_config']['use_multistore'] = 0;
        }

        if (!isset($config['_config']['prefix'])) {
            $config['_config']['prefix'] = null;
        }
    }

    private function returnHtmlContentForAdminFile($file)
    {
        try {
            // czesto po zapisaniu ustawien w performance, przegladarka laduje syf z cache
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
        } catch (Throwable $e) {
        }

        $template_file = TEAMWANT_REDIS_ROOT_DIR . 'views/templates/admin/' . $file;

        try {
            $content = $this->context->smarty->fetch($template_file);
        } catch (SmartyException $e) {
            exit(json_encode([
                'success' => 0,
                'data' => 'SmartyException ' . __FILE__ . ':' . __LINE__,
                'type' => 'alert',
            ]));
        } catch (Exception $e) {
            exit(json_encode([
                'success' => 0,
                'data' => 'Exception ' . __FILE__ . ':' . __LINE__ . '(file: ' . $file . ')',
                'type' => 'alert',
            ]));
        }

        return $content;
    }

    public function ajaxProcessGetConfigurationTableRow()
    {
        $this->returnWrongMethod('GET');
        $this->returnSuperadminPermissions();

        $this->context->smarty->assign([
            'uniq' => uniqid(),
            'phpredis_enable' => (extension_loaded('redis')) ? true : false,
        ]);

        header('Content-Type: application/json');
        exit(json_encode([
            'success' => 1,
            'data' => $this->returnHtmlContentForAdminFile('performance-table-row.tpl'),
            'type' => 'html',
        ]));
    }

    public function ajaxProcessSaveConfigurationTable()
    {
        $this->returnWrongMethod('POST');
        $this->returnSuperadminPermissions();

        $validator = new Validator();

        $params = [];
        parse_str(Tools::getValue('data'), $params);

        $payload = [
            'teamwant_redis_row' => !empty($params['teamwant_redis_row']) ? $params['teamwant_redis_row'] : [],
            'twredis' => !empty($params['form']['twredis']) ? $params['form']['twredis'] : [],
        ];

        $items = $validator->validateAjaxProcessSaveConfigurationTable($payload);

        if ($items['result']) {
            $newConfig = json_encode(
                $items['items'],
                JSON_UNESCAPED_UNICODE
            );

            $this->fileManager->saveConfigFile('_RedisConfiguration.php', $newConfig);
            
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }

            return $this->renderTable($newConfig);
        } else {
            exit(json_encode([
                'success' => 0,
                'data' => $items['msg'],
                'type' => 'alert',
                'stopProcess' => $items['stopProcess'] ? $items['stopProcess'] : false,
            ]));
        }

        $this->renderTable();
    }

    public function ajaxProcessTestAdminRedisConfigurationHost()
    {
        $this->returnWrongMethod('POST');
        $this->returnSuperadminPermissions();

        $validator = new Validator();

        if (Tools::getIsset('data') && Tools::getValue('data') && ($params = $validator->validateRedisRow(Tools::getValue('data')))) {
            $result = TwRedis::testConnection(
                $params['scheme'],
                $params['host'],
                $params['port'],
                ($params['alias'] ?? null),
                ($params['username'] ?? null),
                ($params['password'] ?? null),
                ($params['database'] ?? null)
            );

            if ($result) {
                exit(json_encode([
                    'success' => 1,
                    'data' => Teamwant_Redis::staticModuleTranslate('Save success.'),
                    'type' => 'alert',
                ]));
            } else {
                exit(json_encode([
                    'success' => 0,
                    'data' => Teamwant_Redis::staticModuleTranslate('Connection failed'),
                    'type' => 'alert',
                ]));
            }
        }

        exit(json_encode([
            'success' => 0,
            'data' => Teamwant_Redis::staticModuleTranslate('Please enter required fields, Host and port'),
            'type' => 'alert',
        ]));
    }
}
