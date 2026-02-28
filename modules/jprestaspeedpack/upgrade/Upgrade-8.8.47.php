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
 * Add column 'active' to contexts table
 */
function upgrade_module_8_8_47($module)
{
    $ret = true;
    if (!JprestaUtils::dbColumnExists(_DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS, 'active')) {
        $ret = $ret && JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '`
            ADD COLUMN `active` TINYINT UNSIGNED NOT NULL DEFAULT 1
        ');
    }
    if (!JprestaUtils::dbIndexExists(_DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS, 'active')) {
        $ret = $ret && JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '`
            ADD INDEX `idx_active_context` (active) USING BTREE
        ');
    }

    return $ret;
}
