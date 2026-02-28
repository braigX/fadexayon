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

use Teamwant\Prestashop17\Redis\Classes\Cache\Redis;
use Teamwant\Prestashop17\Redis\Classes\FileManager;

class Teamwant_RedishealthCheckModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        /** @var string $redis_healthcheck_key */
        $redis_healthcheck_key = Configuration::get(Redis::REDIS_HEALTHCHECK_KEY);

        if (!$redis_healthcheck_key
            || (!Tools::getIsset('redis_healthcheck_key'))
            || (!Tools::getValue('redis_healthcheck_key'))
            || (!((string) Tools::getValue('redis_healthcheck_key') === (string) $redis_healthcheck_key))
        ) {
            header('HTTP/1.0 401 Unauthorized');
            exit(json_encode(['msg' => 'HTTP/1.0 401 Unauthorized']));
        }

        $fileManager = new FileManager();
        $data = [
            'status' => true,
            'use_cache_redis' => false,
            'entries' => [],
        ];

        $parameters = include _PS_ROOT_DIR_ . '/app/config/parameters.php';

        if (is_array($parameters) &&
            !empty($parameters['parameters']) &&
            !empty($parameters['parameters']['ps_caching']) &&
            !empty($parameters['parameters']['ps_cache_enable']) &&
            $parameters['parameters']['ps_caching'] === 'Redis' &&
            $parameters['parameters']['ps_cache_enable'] === true
        ) {
            $data['use_cache_redis'] = true;
        }

        /** @var array $config */
        $config = json_decode($fileManager->parseConfigFile('_RedisConfiguration.php'), true);

        if (!empty($config['_servers'])) {
            foreach ($config['_servers'] as $item) {
                try {
                    /** @var bool $connect */
                    $connect = Redis::testConnection(
                        $item['scheme'],
                        $item['host'],
                        $item['port'],
                        ($item['alias'] ?? null),
                        ($item['username'] ?? null),
                        ($item['password'] ?? null),
                        ($item['database'] ?? null)
                    );
                } catch (Throwable $e) {
                    $connect = false;
                }

                if (!$connect) {
                    $data['entries'][$item['alias']] = [
                        'status' => false,
                    ];

                    $data['status'] = false;

                    continue;
                }

                $data['entries'][$item['alias']] = [
                    'status' => true,
                ];

                try {
                    $command = implode(' ', [
                        'redis-benchmark -q -n 200000 -t set,get',

                        '-p',
                        $item['port'],

                        ($item['scheme'] === 'unix' ? '-s ' : '-h '),
                        $item['host'],

                        ($item['password'] ?? null) ? '-a ' . $item['password'] : null,
                        ($item['username'] ?? null) ? '--user ' . $item['username'] : null,
                        // ($item['password'] ?? null) ? '--pass ' . $item['password'] : null,

                        ($item['database'] ?? null) ? '--dbnum ' . $item['database'] : null,

                    ]);

                    $data['entries'][$item['alias']]['benchmark'] = shell_exec($command);
                } catch (Throwable $e) {
                }
            }
        } else {
            $data['status'] = false;
            $data['msg'] = 'Your configuration don\'t have any redis connection information. Please add minimum one redis server.';
        }

        header('Content-type: application/json');
        exit(json_encode($data));
    }
}
