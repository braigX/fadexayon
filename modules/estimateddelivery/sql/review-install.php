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

$columns = [
    'shippingdays' => 'ADD `shippingdays` TEXT NOT NULL',
    'min' => 'ADD `min` int(11) NOT NULL DEFAULT 0',
    'max' => 'ADD `max` int(11) NOT NULL DEFAULT 0',
    'picking_days' => 'ADD `picking_days` TEXT NOT NULL',
    'picking_limit' => 'ADD `picking_limit` TEXT NOT NULL',
    'ed_active' => 'ADD `ed_active` TEXT NOT NULL',
    'ed_alias' => 'ADD `ed_alias` TEXT NOT NULL',
    'release_date' => 'ADD `release_date` TEXT',
    'ppicking_days' => 'ADD `picking_days` int(4) DEFAULT 0',
    'id_carrier' => 'ADD `id_carrier` int(11) NOT NULL',
];

$sql = '';
if ($columnNames == '') {
    switch ($case) {
        case 'ed_holidays':
            $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_holidays` (
                    `id_holidays` int(11) NOT NULL AUTO_INCREMENT,
                    `holiday_name` text NOT NULL,
                    `active` int(2) NOT NULL,   
                    `holiday_start` text NOT NULL,
                    `holiday_end` text NOT NULL,
                    `repeat` TINYINT(1),
                    PRIMARY KEY  (`id_holidays`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
            break;
        case 'ed_orders':
            $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_orders` (
                `id_order` int(11) NOT NULL,
                `id_carrier` int(11) NOT NULL,
                `delivery_min` TEXT NOT NULL,
                `delivery_max` TEXT NOT NULL,
                `is_definitive` TINYINT(1) NOT NULL DEFAULT 0,
                PRIMARY KEY  (`id_order`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
            break;
        case 'ed_carriers':
            $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_carriers` (
                `id_reference` int(11) NOT NULL,
                `id_shop` int(11) NOT NULL,
                `shippingdays` TEXT NOT NULL,
                `min` int(11) NOT NULL DEFAULT 0,
                `max` int(11) NOT NULL DEFAULT 0,
                `picking_days` TEXT NOT NULL,
                `picking_limit` TEXT NOT NULL,
                `ignore_picking` tinyint(1) DEFAULT 0,
                `ed_active` TEXT NOT NULL,
                `ed_alias` TEXT NOT NULL,
                CONSTRAINT ed_ref_shop UNIQUE (id_reference, id_shop)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
            break;
        case 'ed_cat':
            $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_cat` (
                `id_category` int(11) NOT NULL,
                `id_shop` int(11) NOT NULL,
                `delay` int(4) DEFAULT 0,
                `picking_days` int(4) DEFAULT 0,
                `excluded` int(2) NOT NULL DEFAULT 0,
                CONSTRAINT ed_cat UNIQUE (id_category, id_shop)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
            break;
        case 'ed_prod':
            // Create the product table in database
            $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_prod` (
                `id_product` int(11) NOT NULL,
                `id_shop` int(11) NOT NULL,
                `delay` int(4) DEFAULT 0,
                `release_date` TEXT,
                `picking_days` int(4) DEFAULT 0,
                CONSTRAINT ed_prod UNIQUE (id_product, id_shop)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
            break;
        case 'ed_prod_combi':
            // Crea
            $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_prod_combi` (
                `id_product` int(11) NOT NULL,
                `id_product_attribute` int(11) NOT NULL,
                `id_shop` int(11) NOT NULL,
                `restock_date` TEXT,
                `delay` int(4) DEFAULT 0,
                `picking_days` int(4) DEFAULT 0,
                `release_date` TEXT,
                CONSTRAINT ed_prod UNIQUE (id_product_attribute, id_shop)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
            break;
        case 'ed_supplier':
            $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_supplier` (
                        `id_supplier` int(11) NOT NULL,
                        `id_shop` int(11) NOT NULL,
                        `picking_days` int(4),
                        `delay` int(4),
                        CONSTRAINT ed_sup UNIQUE (id_supplier,id_shop)
                    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
            break;
        case 'ed_manufacturer':
            $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_manufacturer` (
                    `id_manufacturer` int(11) NOT NULL,
                    `id_shop` int(11) NOT NULL,
                    `picking_days` int(4),
                    `delay` int(4),
                    CONSTRAINT ed_brand UNIQUE (id_manufacturer,id_shop)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
            break;
    }
} else {
    if (is_array($columnNames)) {
        $sql = 'ALTER TABLE ' . _DB_PREFIX_ . bqSQL($case) . ' ';
        foreach ($columnNames as $value) {
            $sql .= $columns[$value] . ', ';
        }
        $sql = rtrim($sql, ', ');
    }
}
if ($sql != '') {
    // echo "\n".$sql;
    if (Db::getInstance()->execute($sql) === false) {
        return false;
    } else {
        return true;
    }
}
