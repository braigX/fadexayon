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

namespace Teamwant_redis\OverrideSrc;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OverrideHookFile implements OverrideSrc
{
    public const FILE = _PS_ROOT_DIR_ . '/classes/Hook.php';

    public static function install()
    {
        $filecontent = \Tools::file_get_contents(self::FILE);
        $override = 0;

        $re = '/load teamwant redis use_cache_for_hook_list/s';
        preg_match($re, $filecontent, $matches);

        if (!count($matches)) {
            $override = 1;
            $re = '/(function getAllHookIds(.*?))\$result(.*?)\;/s';
            $subst = '$1
        // load teamwant redis use_cache_for_hook_list
        $useTwCache = false;
        try {
            if (_PS_CACHE_ENABLED_) {
                if (_PS_CACHING_SYSTEM_ === \'Redis\') {
                    $use_cache_for_hook_list = Teamwant_Redis::getCacheConfiguration()[\'use_cache_for_hook_list\'];
    
                    if ($use_cache_for_hook_list) {
                        $useTwCache = true;
                        $cache = Cache::getInstance();
                        if (($r = $cache->get($cache->getQueryHash($sql))) !== false) {
                            return $r;
                        }
                    }
                }
            }
        } catch (Throwable $thr) {
            if (defined(\'_PS_MODE_DEV_\') && _PS_MODE_DEV_) {
                throw new \PrestaShopException($thr->getMessage());
            }
        }
        
        $result = $db->executeS($sql, false, true);
';
            $filecontent = preg_replace($re, $subst, $filecontent, 1);
        }

        $re = '/load teamwant redis \$cache->getQueryHash/s';
        preg_match($re, $filecontent, $matches);

        if (!count($matches)) {
            $override = 1;
            $re = '/function getAllHookIds(.*?)Cache::store\(\$cacheId\, \$hookIds\)\;/s';
            $subst = '$0
        // load teamwant redis $cache->getQueryHash
        if ($useTwCache) {
            $cache = Cache::getInstance();
            $cache->set($cache->getQueryHash($sql), $hookIds);
        }
';
            $filecontent = preg_replace($re, $subst, $filecontent, 1);
        }

        if ($override) {
            file_put_contents(self::FILE, $filecontent);
        }
    }

    public static function uninstall()
    {
        $filecontent = \Tools::file_get_contents(self::FILE);

        $filecontent = str_replace('
        // load teamwant redis use_cache_for_hook_list
        $useTwCache = false;
        try {
            if (_PS_CACHE_ENABLED_) {
                if (_PS_CACHING_SYSTEM_ === \'Redis\') {
                    $use_cache_for_hook_list = Teamwant_Redis::getCacheConfiguration()[\'use_cache_for_hook_list\'];
    
                    if ($use_cache_for_hook_list) {
                        $useTwCache = true;
                        $cache = Cache::getInstance();
                        if (($r = $cache->get($cache->getQueryHash($sql))) !== false) {
                            return $r;
                        }
                    }
                }
            }
        } catch (Throwable $thr) {
            if (defined(\'_PS_MODE_DEV_\') && _PS_MODE_DEV_) {
                throw new \PrestaShopException($thr->getMessage());
            }
        }', '', $filecontent);

        $filecontent = str_replace('
        // load teamwant redis $cache->getQueryHash
        if ($useTwCache) {
            $cache = Cache::getInstance();
            $cache->set($cache->getQueryHash($sql), $hookIds);
        }', '', $filecontent);

        file_put_contents(self::FILE, $filecontent);
    }
}
