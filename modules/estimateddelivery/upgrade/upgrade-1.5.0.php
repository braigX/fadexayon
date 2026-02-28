<?php
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_5_0($module)
{
    // Remove the Keypage searches as now it tracks all searches
    $sql = [];
    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'edcarriers` (
    `id_carrier` int(11) NOT NULL AUTO_INCREMENT,
    `shippingdays` TEXT NOT NULL,
    `min` int(11) NOT NULL,
    `max` int(11) NOT NULL,
    `picking_days` TEXT NULL,
    `ed_active` TEXT NULL,
    `ed_alias` TEXT NULL,
    PRIMARY KEY  (`id_holidays`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    $sql[] = 'INSERT INTO `' . _DB_PREFIX_ . 'edcarriers` (id_carrier,shippingdays,min,max)
SELECT id_carrier,shippingdays,min,max FROM `' . _DB_PREFIX_ . 'carrier`';

    foreach ($sql as $query) {
        if (DB::getInstance()->execute(pSQL($query)) === false) {
            Tools::DisplayError($module->l('Could not create the database. Error upgrading'));

            return false;
        }
    }

    // All done if we get here the upgrade is successfull
    return $module;
}
