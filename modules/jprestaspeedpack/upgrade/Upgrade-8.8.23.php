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
 * Add indexes to TABLE_PERFS
 */
function upgrade_module_8_8_23($module)
{
    $ret = true;

    if (!JprestaUtils::dbColumnExists(_DB_PREFIX_ . PageCacheDAO::TABLE_PERFS, 'day_add')) {
        $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PERFS . '` ADD `day_add` DATE DEFAULT NULL AFTER `date_add`');
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PERFS . '` ADD INDEX(`day_add`)');
        $ret &= JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PERFS . '` SET day_add=DATE(date_add) WHERE day_add IS NULL');
    }
    if ($ret && !JprestaUtils::dbIndexExists(_DB_PREFIX_ . PageCacheDAO::TABLE_PERFS, ['id_shop', 'day_add', 'id_controller'])) {
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PERFS . '` ADD INDEX `idx_sdc` (id_shop, day_add, id_controller)');
    }
    if ($ret && !JprestaUtils::dbIndexExists(_DB_PREFIX_ . PageCacheDAO::TABLE_PERFS, ['id_shop', 'day_add'])) {
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PERFS . '` ADD INDEX `idx_sd` (id_shop, day_add)');
    }

    return $ret;
}
