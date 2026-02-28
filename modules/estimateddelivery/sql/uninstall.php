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

$sql = [];
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ed_holidays`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ed_orders`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ed_carriers`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ed_prod`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ed_prod_combi`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ed_cat`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ed_supplier`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ed_manufacturer`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ed_warehouse`';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

// Search for older versions and remove the params for the carrier database
$search = ['shippingdays', 'min', 'max', 'picking_days', 'picking_limit', 'ed_active', 'ed_alias'];
$drop = [];
$sql = 'SELECT COLUMN_NAME as column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\' AND TABLE_NAME = \'' . _DB_PREFIX_ . 'carrier\'';
$results = Db::getInstance()->executeS($sql);
if (!empty($results)) {
    foreach ($results as $result) {
        if (in_array($result['column_name'], $search)) {
            $drop[] = $results['column_name'];
        }
    }
    if (!empty($drop)) {
        $sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'carrier DROP COLUMN ' . implode(', DROP COLUMN ', $drop);
        Db::getInstance()->execute(pSQL($sql));
    }
}
