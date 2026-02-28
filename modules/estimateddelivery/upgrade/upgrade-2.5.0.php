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

function upgrade_module_2_5_0($object)
{
    if (!$object->isRegisteredInHook('displayProductButtons')) {
        $object->registerHook('displayProductButtons');
    }
    if (!$object->isRegisteredInHook('displayShoppingCartFooter')) {
        $object->registerHook('displayShoppingCartFooter');
    }
    if (!$object->isRegisteredInHook('displayBeforeCarrier')) {
        $object->registerHook('displayBeforeCarrier');
    }
    if (!$object->isRegisteredInHook('displayAfterCarrier')) {
        $object->registerHook('displayAfterCarrier');
    }
    if (!$object->isRegisteredInHook('actionCarrierUpdate')) {
        $object->registerHook('actionCarrierUpdate');
    }
    if (!$object->isRegisteredInHook('actionValidateOrder')) {
        $object->registerHook('actionValidateOrder');
    }
    if (!$object->isRegisteredInHook('displayOrderDetail')) {
        $object->registerHook('displayOrderDetail');
    }
    if (!$object->isRegisteredInHook('displayAdminOrder')) {
        $object->registerHook('displayAdminOrder');
    }
    $sql = [];

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed` (
        `id_order` int(11) NOT NULL,
        `delivery_min` TEXT NOT NULL,
        `delivery_max` TEXT NOT NULL,
        PRIMARY KEY  (`id_order`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    $tmp_sql = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\' AND TABLE_NAME = \'' . _DB_PREFIX_ . 'ed_cat_oos\' AND COLUMN_NAME = \'picking_days\'';

    $res = DB::getInstance()->executeS($tmp_sql);
    if (empty($res) || count($res) == 0) {
        $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ed_cat_oos` ADD `picking_days` int(4)';
    }

    foreach ($sql as $query) {
        DB::getInstance()->execute($query);
    }

    return true;
}
