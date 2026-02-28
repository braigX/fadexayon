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

function upgrade_module_3_3_1()
{
    $columns_to_add = [
        [
            'table' => 'ed_orders',
            'name' => 'is_virtual',
            'type' => 'TINYINT(1)',
            'extras' => 'NOT NULL DEFAULT 0',
        ],
        [
            'table' => 'ed_orders',
            'name' => 'is_release',
            'type' => 'TINYINT(1)',
            'extras' => 'NOT NULL DEFAULT 0',
        ],
        [
            'table' => 'ed_orders',
            'name' => 'is_available',
            'type' => 'TINYINT(1)',
            'extras' => 'NOT NULL DEFAULT 0',
        ],
    ];
    foreach ($columns_to_add as $column) {
        $sql = 'SELECT COLUMN_NAME as name FROM INFORMATION_SCHEMA.`COLUMNS` WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\' AND TABLE_NAME = \'' . _DB_PREFIX_ . bqSQL($column['table']) . '\' AND COLUMN_NAME = "' . bqSQL($column['name']) . '"';
        $exists = Db::getInstance()->executeS($sql);
        if ($exists === false || count($exists) == 0) {
            $sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'ed_orders ADD COLUMN `' . bqSQL($column['name']) . '` ' . bqSQL($column['type']) . ($column['extras'] != '' ? ' ' . pSQL($column['extras']) : '');
            if (Db::getInstance()->execute($sql) === false) {
                return false;
            }
        }
    }

    return true;
}
