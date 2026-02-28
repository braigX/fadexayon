<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

/**
 * @param $object Ets_reviews
 */
function upgrade_module_1_1_6()
{
    Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_rv_staff_activity` (
          `id_employee` int(11) UNSIGNED NOT NULL,
          `id_ets_rv_activity` int(11) UNSIGNED NOT NULL DEFAULT 0,
          `read` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
          PRIMARY KEY (`id_employee`, `id_ets_rv_activity`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
    ');

    $execCmd = Db::getInstance()->execute('
        INSERT IGNORE INTO `' . _DB_PREFIX_ . 'ets_rv_staff_activity` (`id_employee`, `id_ets_rv_activity`, `read`)
        SELECT e.`id_employee`, a.`id_ets_rv_activity`, a.`read`
        FROM `' . _DB_PREFIX_ . 'ets_rv_activity` a
        CROSS JOIN `' . _DB_PREFIX_ . 'employee` e INNER JOIN `' . _DB_PREFIX_ . 'ets_rv_staff` rs ON (rs.`id_employee` = e.`id_employee`)
        WHERE a.`read` = 1 AND (rs.`enabled` = 1 OR e.id_profile=1)
    ');
    if ($execCmd)
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_activity` DROP `read`;');

    return true;
}