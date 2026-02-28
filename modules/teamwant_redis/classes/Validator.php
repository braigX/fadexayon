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

// todo: poogarniac consty w wolnej chwili
require_once _PS_MODULE_DIR_ . 'teamwant_redis/const.php';

require_once TEAMWANT_REDIS_ROOT_DIR . '/teamwant_redis.php';

class Validator
{
    public function validateAjaxProcessSaveConfigurationTable(array $payload)
    {
        if (empty($payload['teamwant_redis_row']) || empty($payload['twredis'])) {
            return [
                'result' => false,
                'stopProcess' => true,
                'items' => [],
                'msg' => \Teamwant_Redis::staticModuleTranslate('Payload is invalid.'),
            ];
        }

        $teamwant_redis_row = $payload['teamwant_redis_row'];
        $twredis = $payload['twredis'];

        if (count($teamwant_redis_row) === 0) {
            return [
                'result' => false,
                'stopProcess' => true,
                'items' => [],
                'msg' => \Teamwant_Redis::staticModuleTranslate('Please input any record'),
            ];
        }

        if (count($teamwant_redis_row) === 0) {
            return [
                'result' => false,
                'stopProcess' => true,
                'items' => [],
                'msg' => \Teamwant_Redis::staticModuleTranslate('Please input any record'),
            ];
        }

        $other_list = [];

        $i = 0;

        foreach ($teamwant_redis_row as $key => &$row) {
            ++$i; // increment na poczatku ponieważ możemy mieć przypadek gdzie mamy x rekordow takich samych.

            $row = $this->validateRedisRow($row);
            $clone = $row;

            if (!$clone) {
                unset($teamwant_redis_row[$key]);

                continue;
            }

            sort($clone);
            $unique_key = md5(implode('||', $clone));

            if (is_int(array_search($unique_key, $other_list, true))) {
                return [
                    'result' => false,
                    'stopProcess' => true,
                    'items' => [],
                    'msg' => \Teamwant_Redis::staticModuleTranslate('Server %d is duplicated', [
                        $i,
                    ]),
                ];
            }

            $other_list[] = $unique_key;

            if (!$row) {
                unset($teamwant_redis_row[$key]);
            }
        }

        if (count($teamwant_redis_row) === 0) {
            return [
                'result' => false,
                'stopProcess' => true,
                'items' => [],
                'msg' => \Teamwant_Redis::staticModuleTranslate('Any record is not valid'),
            ];
        }

        if (empty($twredis)) {
            return [
                'result' => false,
                'stopProcess' => true,
                'items' => [],
                'msg' => \Teamwant_Redis::staticModuleTranslate('Configuration is not valid'),
            ];
        } else {
            if (!isset($twredis['use_cache_admin'])) {
                return [
                    'result' => false,
                    'stopProcess' => true,
                    'items' => [],
                    'msg' => \Teamwant_Redis::staticModuleTranslate('Field %s is required', ['use_cache_admin']),
                ];
            }

            if (!isset($twredis['use_prefix'])) {
                return [
                    'result' => false,
                    'stopProcess' => true,
                    'items' => [],
                    'msg' => \Teamwant_Redis::staticModuleTranslate('Field %s is required', ['use_prefix']),
                ];
            }

            if (!isset($twredis['prefix'])) {
                return [
                    'result' => false,
                    'stopProcess' => true,
                    'items' => [],
                    'msg' => \Teamwant_Redis::staticModuleTranslate('Field %s is required', ['prefix']),
                ];
            }

            if (!$this->validBool($twredis['use_cache_admin'])) {
                return [
                    'result' => false,
                    'stopProcess' => true,
                    'items' => [],
                    'msg' => \Teamwant_Redis::staticModuleTranslate('Field %s must be true or false', ['use_cache_admin']),
                ];
            }
            $twredis['use_cache_admin'] = $this->convertToBool($twredis['use_cache_admin']);

            if (!$this->validBool($twredis['use_prefix'])) {
                return [
                    'result' => false,
                    'stopProcess' => true,
                    'items' => [],
                    'msg' => \Teamwant_Redis::staticModuleTranslate('Field %s must be true or false', ['use_prefix']),
                ];
            }
            $twredis['use_prefix'] = $this->convertToBool($twredis['use_prefix']);

            if (!$this->validBool($twredis['use_multistore'])) {
                return [
                    'result' => false,
                    'stopProcess' => true,
                    'items' => [],
                    'msg' => \Teamwant_Redis::staticModuleTranslate('Field %s must be true or false', ['use_multistore']),
                ];
            }
            $twredis['use_multistore'] = $this->convertToBool($twredis['use_multistore']);

            if (!$twredis['prefix'] && $twredis['use_prefix']) {
                $twredis['prefix'] = $this->generateRandomString(4) . '_';
            }

            $twredis['defalut_ttl'] = isset($twredis['defalut_ttl']) && $twredis['defalut_ttl'] ? (int) $twredis['defalut_ttl'] : null;
            $twredis['limit_data_size'] = isset($twredis['limit_data_size']) && $twredis['limit_data_size'] ? (bool) $twredis['limit_data_size'] : false;
        }

        return [
            'result' => true,
            'items' => [
                '_servers' => $teamwant_redis_row,
                '_config' => $twredis,
            ],
            'msg' => null,
        ];
    }

    public function validateRedisRow(array $item)
    {
        $optionalFields = [
            'alias',
            'scheme',
            'username',
            'password',
            'database',
        ];

        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') === true
            && version_compare(_PS_VERSION_, '1.7.3.0', '<') === true
        ) {
            if (!$item) {
                return false;
            }
        }

        if (!$this->requiredFields($item, [
            'host',
            'port',
        ])) {
            return false;
        }

        $this->optionalFields($item, $optionalFields);

        if (!$item['port'] || (int) $item['port'] <= 0) {
            // dodajemy obsluge typu sock dla redisa wtedy pozwoli nam na 0
            if (isset($item['scheme']) && $item['scheme'] === 'unix') {
                if ($item['port'] !== 0) {
                    $item['port'] = 0;
                }
            } else {
                $item['port'] = 6379;
            }
        }

        if (!$item['alias']) {
            $item['alias'] = uniqid('RDS_');
        }

        if (!$item['scheme']) {
            $item['scheme'] = 'tcp';
        }

        $return = [
            'host' => $item['host'],
            'port' => $item['port'],
        ];

        // to ma nas ratowac przed bledami z redisem, czyli wrzucaniem pustego username + password
        foreach ($optionalFields as $k => $v) {
            if (
                !($item[$v] === null || is_null($item[$v]))
                && !(is_string($item[$v]) && $item[$v] === '' && !is_int($item[$v]))
            ) {
                $return[$v] = $item[$v];
            }
        }

        return $return;
    }

    private function requiredFields(array $item, array $required_fields)
    {
        foreach ($required_fields as $field) {
            if (!isset($item[$field]) || (string) $item[$field] === '' || $item[$field] === null) {
                return false;
            }
        }

        return true;
    }

    private function optionalFields(&$item, array $required_fields)
    {
        foreach ($required_fields as $field) {
            if (!isset($item[$field])) {
                $item[$field] = null;
            }
        }
    }

    private function validBool($var)
    {
        if (in_array($var, [1, 0, '1', '0', true, false, 'on', 'off', 'On', 'Off'], true)) {
            return true;
        }

        return false;
    }

    private function convertToBool($field)
    {
        if (in_array($field, [1, '1', true, 'on', 'On'], true)) {
            return true;
        }

        return false;
    }

    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = \Tools::strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
