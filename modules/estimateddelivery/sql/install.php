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
 * Registered Trademark & Property of Smart-Modules.prpo
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_holidays` (
    `id_holidays` int(11) NOT NULL AUTO_INCREMENT,
    `holiday_name` VARCHAR(255) NOT NULL,
    `holiday_start` DATE NOT NULL,
    `holiday_end` DATE NOT NULL,
    `repeat` TINYINT(1),
    PRIMARY KEY  (`id_holidays`),
    CONSTRAINT ed_holiday UNIQUE (holiday_start, holiday_end, `repeat`, `holiday_name`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_holidays_shop` (
        `id_holidays` int(11) NOT NULL,
        `id_shop` int(11) NOT NULL,
        `active` TINYINT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`id_holidays`, `id_shop`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_orders` (
    `id_order` int(11) NOT NULL,
    `id_carrier` int(11) NOT NULL,
    `delivery_min` TEXT NOT NULL,
    `delivery_max` TEXT NOT NULL,
    `picking_day` TEXT NOT NULL,
    `individual_dates` TINYINT(1) NOT NULL DEFAULT 0,
    `undefined_delivery` TINYINT(1) NOT NULL DEFAULT 0,
    `calendar_date` DATE NOT NULL DEFAULT \'1000-01-01\',
    `is_definitive` TINYINT(1) NOT NULL DEFAULT 0,
    `is_virtual` TINYINT(1) NOT NULL DEFAULT 0,
    `is_release` TINYINT(1) NOT NULL DEFAULT 0,
    `is_available` TINYINT(1) NOT NULL DEFAULT 0,
    `shipped` TINYINT(1) NOT NULL DEFAULT 0,
    `admin_notified` TINYINT(1) NOT NULL DEFAULT 0,
    `client_notified` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id_order`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_order_individual` (
    `id_order` int(11) NOT NULL,
    `id_order_detail` int(11) NOT NULL,
    `delivery_min` TEXT NOT NULL,
    `delivery_max` TEXT NOT NULL,
    `picking_day` TEXT NOT NULL,
    `is_virtual` TINYINT(1) NOT NULL DEFAULT 0,
    `is_release` TINYINT(1) NOT NULL DEFAULT 0,
    `is_available` TINYINT(1) NOT NULL DEFAULT 0,
    `undefined_delivery` TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT ed_individual UNIQUE (id_order, id_order_detail)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_carriers` (
    `id_reference` int(11) NOT NULL,
    `id_shop` int(11) NOT NULL,
    `shippingdays` TEXT(7) NOT NULL,
    `min` int(11) NOT NULL DEFAULT 0,
    `max` int(11) NOT NULL DEFAULT 0,
    `picking_days` TEXT(7) NOT NULL,
    `picking_limit` TEXT NOT NULL,
    `ignore_picking` tinyint(1) DEFAULT 0,
    `ed_active` TEXT NOT NULL,
    `ed_alias` TEXT NOT NULL,
    CONSTRAINT ed_ref_shop UNIQUE (id_reference, id_shop)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

// Create the product table in database
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_prod` (
    `id_product` int(11) NOT NULL,
    `id_shop` int(11) NOT NULL,
    `delay` int(4) DEFAULT 0,
    `release_date` TEXT,
    `picking_days` int(4) DEFAULT 0,
    `customization_days` int(4) DEFAULT 0,
    `disabled` TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT ed_prod UNIQUE (id_product, id_shop)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_prod_combi` (
    `id_product` int(11) NOT NULL,
    `id_product_attribute` int(11) NOT NULL,
    `id_shop` int(11) NOT NULL,
    `restock_date` TEXT,
    `delay` int(4) DEFAULT 0,
    `picking_days` int(4) DEFAULT 0,
    `release_date` TEXT,
    `customization_days` int(4) DEFAULT 0,
    `disabled` TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT ed_prod UNIQUE (id_product_attribute, id_shop)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_cat` (
    `id_category` int(11) NOT NULL,
    `id_shop` int(11) NOT NULL,
    `delay` int(4) DEFAULT 0,
    `picking_days` int(4) DEFAULT 0,
    `customization_days` int(4) DEFAULT 0,
    `excluded` int(2) NOT NULL DEFAULT 0,
    CONSTRAINT ed_cat UNIQUE (id_category, id_shop)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_supplier` (
    `id_supplier` int(11) NOT NULL,
    `id_shop` int(11) NOT NULL,
    `picking_days` int(4),
    `customization_days` int(4) DEFAULT 0,
    `delay` int(4),
    `undefined_delivery` TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT ed_sup UNIQUE (id_supplier,id_shop)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_manufacturer` (
    `id_manufacturer` int(11) NOT NULL,
    `id_shop` int(11) NOT NULL,
    `picking_days` int(4),
    `customization_days` int(4) DEFAULT 0,
    `delay` int(4),
    `undefined_delivery` TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT ed_brand UNIQUE (id_manufacturer,id_shop)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

// TODO Make the delivery zones compatible with multishops?
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_delivery_zones` (
    `id_reference` int(11) NOT NULL,
    `id_zone` int(11) NOT NULL,
    `delivery_min` TEXT NOT NULL,
    `delivery_max` TEXT NOT NULL,
    CONSTRAINT ed_delivery_zone UNIQUE (id_reference, id_zone)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_warehouse` (
    `id_warehouse` int(11) NOT NULL,
    `id_supplier` int(11) NOT NULL,
    `id_manufacturer` int(11) NOT NULL,
    `id_shop` int(11) NOT NULL,
    `picking_days` int(4),
    CONSTRAINT ed_war UNIQUE (id_warehouse,id_supplier,id_manufacturer,id_shop)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$err = false;
foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) === false) {
        $this->context->controller->errors[] = Db::getInstance()->getMsgError() . ' <br> ' . $query;
        $err = true;
    }
}
if ($err) {
    return false;
}
