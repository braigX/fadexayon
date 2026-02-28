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
function upgrade_module_8_8_4($module)
{
    $ret = true;

    if (!JprestaUtils::dbIndexExists(_DB_PREFIX_ . PageCacheDAO::TABLE_PERFS, 'date_add')) {
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PERFS . '` ADD INDEX `date_add_idx` (`date_add`)');
    }
    if (!JprestaUtils::dbIndexExists(_DB_PREFIX_ . PageCacheDAO::TABLE_PERFS, 'id_shop')) {
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PERFS . '` ADD INDEX `id_shop_idx` (`id_shop`)');
    }

    return $ret;
}
