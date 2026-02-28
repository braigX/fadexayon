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

function upgrade_module_2_6_1()
{
    $sql = [];

    // Add the table for the Supplier additional Picking Days.
    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_supplier_picking` (
    `id_supplier_picking` int(11) NOT NULL AUTO_INCREMENT,
    `id_supplier` int(11) NOT NULL UNIQUE,
    `id_shop` int(11) NOT NULL,
    `picking_days` int(4),
    PRIMARY KEY  (`id_supplier_picking`),
    CONSTRAINT ed_sup UNIQUE (id_supplier,id_shop)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    foreach ($sql as $query) {
        Db::getInstance()->execute($query);
    }

    // All done if we get here the upgrade is successfull
    return true;
}
