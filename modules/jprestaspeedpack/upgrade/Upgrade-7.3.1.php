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
 * Change controller column from strnig to int
 *
 * @var $module Jprestaspeedpack
 *
 * @return bool
 */
function upgrade_module_7_3_1($module)
{
    $ret = true;

    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
        ADD COLUMN `id_controller` TINYINT(1) UNSIGNED DEFAULT NULL AFTER `url`,
        ADD INDEX `id_controller_object` (`id_controller`,`id_object`)
        ');

    $ret &= JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` SET `id_controller` = 1 WHERE `controller` = \'index\'');
    $ret &= JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` SET `id_controller` = 2 WHERE `controller` = \'category\'');
    $ret &= JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` SET `id_controller` = 3 WHERE `controller` = \'product\'');
    $ret &= JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` SET `id_controller` = 4 WHERE `controller` = \'cms\'');
    $ret &= JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` SET `id_controller` = 5 WHERE `controller` = \'newproducts\'');
    $ret &= JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` SET `id_controller` = 6 WHERE `controller` = \'bestsales\'');
    $ret &= JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` SET `id_controller` = 7 WHERE `controller` = \'supplier\'');
    $ret &= JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` SET `id_controller` = 8 WHERE `controller` = \'manufacturer\'');
    $ret &= JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` SET `id_controller` = 9 WHERE `controller` = \'contact\'');
    $ret &= JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` SET `id_controller` = 10 WHERE `controller` = \'pricesdrop\'');
    $ret &= JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` SET `id_controller` = 11 WHERE `controller` = \'sitemap\'');
    if (!$ret) {
        try {
            // Cannot update datas so we delete all
            $module->clearCacheAndStats('Upgrade 7.3.1');
        } catch (Throwable $e) {
            // Ignore
        }
    }

    JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
        DROP COLUMN `controller`,
        DROP INDEX `controller`
        ');

    try {
        if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>')) {
            $module->unregisterHook('actionAjaxDieBefore');
        } elseif (JprestaUtils::version_compare(_PS_VERSION_, '1.6.0.12', '>=')) {
            $module->unregisterHook('actionBeforeAjaxDie');
        }
    } catch (Throwable $e) {
        // Ignore
    }

    return (bool) $ret;
}
