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

function upgrade_module_3_8_1()
{
    $rename_columns = [];
    $rename_columns[] = [
        'table' => 'ed_supplier',
        'original_name' => 'undefined_date',
        'new_name' => 'undefined_delivery',
        'column_params' => 'TINYINT(1) NOT NULL DEFAULT 0',
    ];
    foreach ($rename_columns as $rename_column) {
        $tmp_sql = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\' AND TABLE_NAME = \'' . _DB_PREFIX_ . bqSQL($rename_column['table']) . '\' AND COLUMN_NAME = \'' . bqSQL($rename_column['original_name']) . '\'';

        $res = DB::getInstance()->executeS($tmp_sql);
        if (!empty($res) || count($res) > 0) {
            $sql = 'ALTER TABLE `' . _DB_PREFIX_ . bqSQL($rename_column['table']) . '` CHANGE `' . $rename_column['original_name'] . '` `' . $rename_column['new_name'] . '` ' . $rename_column['column_params'];
            if (!DB::getInstance()->execute($sql)) {
                return false;
            }
        }
    }

    $columns_to_add = [
        [
            'table' => 'ed_manufacturer',
            'name' => 'undefined_delivery',
            'type' => 'TINYINT(1)',
            'extras' => 'NOT NULL DEFAULT 0',
        ],
    ];
    foreach ($columns_to_add as $column) {
        $sql = 'SELECT COLUMN_NAME as name FROM INFORMATION_SCHEMA.`COLUMNS` WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\' AND TABLE_NAME = \'' . _DB_PREFIX_ . bqSQL($column['table']) . '\' AND COLUMN_NAME = "' . bqSQL($column['name']) . '"';
        $exists = Db::getInstance()->executeS($sql);
        if ($exists === false || count($exists) == 0) {
            $sql = 'ALTER TABLE ' . _DB_PREFIX_ . bqSQL($column['table']) . ' ADD COLUMN `' . bqSQL($column['name']) . '` ' . bqSQL($column['type']) . ($column['extras'] != '' ? ' ' . pSQL($column['extras']) : '');
            if (Db::getInstance()->execute($sql) === false) {
                return false;
            }
        }
    }

    return true;
}
