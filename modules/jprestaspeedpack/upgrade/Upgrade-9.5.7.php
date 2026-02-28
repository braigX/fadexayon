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
function upgrade_module_9_5_7($module)
{
    $ret = true;

    if (JprestaUtils::dbIndexExists(_DB_PREFIX_ . PageCacheDAO::TABLE, 'url')) {
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
            DROP INDEX `url`
        ');
    }

    // Ignore if this one fails
    $cols = JprestaUtils::dbGetIndexColumns(_DB_PREFIX_ . PageCacheDAO::TABLE, 'url_id_context');
    if (count($cols) > 0) {
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
            DROP INDEX `url_id_context`
        ');
    }

    $ret = JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
        MODIFY COLUMN `url` TEXT NOT NULL
    ');
    if (!JprestaUtils::dbHasUniqueIndex(_DB_PREFIX_ . PageCacheDAO::TABLE, 'cache_key')) {
        if (JprestaUtils::dbGetValue('SELECT EXISTS(
            SELECT 1
            FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
            GROUP BY cache_key
            HAVING COUNT(1) > 1
        ) AS has_duplicates;')) {
            // This is weird and probably rare but some shops have duplicates :-(
            $module->clearCacheAndStats('Upgrade 9.5.7');
        }
        $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
            ADD UNIQUE `cache_key` (`cache_key`)
        ');
    }

    return $ret;
}
