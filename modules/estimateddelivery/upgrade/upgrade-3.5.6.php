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

function upgrade_module_3_5_6()
{
    // Add columns with a preventive check
    $add_columns = [
        [
            'table' => 'ed_orders',
            'column_name' => 'picking_day',
            'parameters' => 'TEXT NOT NULL AFTER `delivery_max`',
        ],
        [
            'table' => 'ed_order_individual',
            'column_name' => 'picking_day',
            'parameters' => 'TEXT NOT NULL AFTER `delivery_max`',
        ],
    ];
    foreach ($add_columns as $col) {
        if (Db::getInstance()->getValue('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = \'' . _DB_PREFIX_ . bqSQL($col['table']) . '\' AND COLUMN_NAME = \'' . bqSQL($col['column_name']) . '\'') === false) {
            continue;
        }
        if (Db::getInstance()->getValue('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = \'' . _DB_PREFIX_ . bqSQL($col['table']) . '\' AND COLUMN_NAME = \'' . bqSQL($col['column_name']) . '\'') === false) {
            // Colum doesn't exist, add it
            if (Db::getInstance()->execute(pSQL('ALTER TABLE ' . _DB_PREFIX_ . bqSQL($col['table']) . ' ADD COLUMN `' . bqSQL($col['column_name']) . '` ' . pSQL($col['parameters']))) === false) {
                return false;
            }
        }
    }

    return true;
}
