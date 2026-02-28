<?php
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @version 3.5.4
 *
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                    V 3.5.4                      *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_5_5($module)
{
    Configuration::updateValue('dd_test_mode', 0);
    Configuration::updateValue('dd_test_orders', '');

    $sql = [];
    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_delivery_zones` (`id_reference` int(11) NOT NULL, `id_zone` int(11) NOT NULL, `delivery_min` TEXT NOT NULL, `delivery_max` TEXT NOT NULL, CONSTRAINT ed_delivery_zone UNIQUE (id_reference, id_zone)) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    foreach ($sql as $query) {
        if (DB::getInstance()->execute(pSQL($query)) === false) {
            Tools::DisplayError($module->l('Could not create the database. Error upgrading'));

            return false;
        }
    }

    return true;
}
