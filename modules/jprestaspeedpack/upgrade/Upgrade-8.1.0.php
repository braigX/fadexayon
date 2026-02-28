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
function upgrade_module_8_1_0($module)
{
    $ret = PageCacheDAO::createTableStatsPerf();

    if (!JprestaUtils::dbColumnExists(_DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS, 'date_add')) {
        $ret = $ret && JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '`
            ADD COLUMN `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `count_missed`
            ');
    }

    @unlink(_PS_MODULE_DIR_ . $module->name . '/views/js/pagecache-v8.js');

    $module->clearCache();

    return (bool) $ret;
}
