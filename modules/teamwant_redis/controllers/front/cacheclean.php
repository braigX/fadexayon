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

/**
 * generate cache for sensitive places
 * curl -k -L --max-redirs 10000 'http://localhost/index.php?redis_healthcheck_key=1eb5a801a3f1b558a6cdb5c3aaa932c492509df07fdb3f9472433a96466a96b6&fc=module&module=teamwant_redis&controller=cron&o'
 * curl -k -L --max-redirs 10000 'http://localhost/index.php?redis_healthcheck_key=1eb5a801a3f1b558a6cdb5c3aaa932c492509df07fdb3f9472433a96466a96b6&fc=module&module=teamwant_redis&controller=cron&offse=&offs4=&offse34=&redis_cron_type=categories'
 */
class Teamwant_RediscachecleanModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        try {
            /** @var string $redis_healthcheck_key */
            $redis_healthcheck_key = Configuration::get(Redis::REDIS_HEALTHCHECK_KEY);

            if (!$redis_healthcheck_key
                || (!Tools::getValue('redis_healthcheck_key', false))
                || (!((string)Tools::getValue('redis_healthcheck_key') === (string)$redis_healthcheck_key))
            ) {
                header('HTTP/1.0 401 Unauthorized');
                die(json_encode(['msg' => 'HTTP/1.0 401 Unauthorized']));
            }

            $parameters = include _PS_ROOT_DIR_ . '/app/config/parameters.php';

            if (!(is_array($parameters) &&
                !empty($parameters['parameters']) &&
                !empty($parameters['parameters']['ps_caching']) &&
                !empty($parameters['parameters']['ps_cache_enable']) &&
                $parameters['parameters']['ps_caching'] === 'Redis' &&
                $parameters['parameters']['ps_cache_enable'] === true
            )) {
                header('HTTP/1.0 403 Unauthorized');
                die(json_encode(['msg' => 'Redis is not enabled']));
            }

            Cache::getInstance()->flush();
            echo json_encode(['success' => true]);
            exit;
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
}
