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
 * Add a primary key to backlink table and replace and index
 */
function upgrade_module_9_5_0($module)
{
    $ret = true;

    // Unicity will be better handled on cache_key than on url_id_context which is truncated
    if (JprestaUtils::dbGetValue('SELECT EXISTS(
        SELECT 1
        FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
        GROUP BY cache_key
        HAVING COUNT(1) > 1
    ) AS has_duplicates;')) {
        // This is weird and probably rare but some shops have duplicates :-(
        $module->clearCacheAndStats('Upgrade 9.5.0');
    }
    if (!JprestaUtils::dbHasUniqueIndex(_DB_PREFIX_ . PageCacheDAO::TABLE, 'cache_key')) {
        $ret = JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
            ADD UNIQUE `cache_key` (`cache_key`)
        ');
    }

    // Ignore if this one fails
    $cols = JprestaUtils::dbGetIndexColumns(_DB_PREFIX_ . PageCacheDAO::TABLE, 'url_id_context');
    if (count($cols) > 0) {
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
            DROP INDEX `url_id_context`
        ');
    }

    // Add a primary key to backlink table
    if (!JprestaUtils::dbHasPrimaryKey(_DB_PREFIX_ . PageCacheDAO::TABLE_BACKLINK)) {
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_BACKLINK . '` ADD `id_backlink` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
    }

    return $ret;
}
