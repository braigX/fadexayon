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

/**
 * @var $module Jprestaspeedpack
 *
 * @return bool
 */
function upgrade_module_7_9_23($module)
{
    $ret = true;

    if (JprestaUtils::startsWith(_PS_CACHE_DIR_, _PS_ROOT_DIR_ . '/var/cache/')
        && file_exists(_PS_CACHE_DIR_ . Jprestaspeedpack::PAGECACHE_DIR)) {
        // Moving the cache to its new location (it doesn't matter if it fails)
        JprestaUtils::rename(_PS_CACHE_DIR_ . Jprestaspeedpack::PAGECACHE_DIR, _PS_ROOT_DIR_ . '/var/cache/' . Jprestaspeedpack::PAGECACHE_DIR);
    }

    return (bool) $ret;
}
