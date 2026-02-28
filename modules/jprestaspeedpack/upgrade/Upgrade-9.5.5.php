<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

/*
 * Add an index on URL in ps_jm_pagecache
 */
function upgrade_module_9_5_5($module)
{
    $ret = true;

    if (!JprestaUtils::dbIndexExists(_DB_PREFIX_ . PageCacheDAO::TABLE, 'url')) {
        $ret = JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
            ADD INDEX `url` (`url`)
        ');
    }

    return $ret;
}
