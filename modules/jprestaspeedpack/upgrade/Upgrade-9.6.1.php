<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/*
 * Compress pagecache_cachekey_usergroups value
 */
function upgrade_module_9_6_1($module)
{
    $ret = true;

    Configuration::deleteByName('pagecache_cachekey_usergroups');
    Jprestaspeedpack::updateCacheKeyForUserGroups();

    return $ret;
}
