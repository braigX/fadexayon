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

function upgrade_module_3_6_1()
{
    $context = Context::getContext();
    $sql = [];

    // Remove duplicates
    $sql[] = 'DELETE t1 FROM `' . _DB_PREFIX_ . 'ed_holidays` t1
                    INNER JOIN `' . _DB_PREFIX_ . 'ed_holidays` t2
                    WHERE 
                        t1.id_holidays < t2.id_holidays AND 
                        t1.holiday_start = t2.holiday_start AND
                        t1.holiday_start = t2.holiday_start AND
                        t1.repeat = t2.repeat';

    // Update the column types
    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ed_holidays` MODIFY COLUMN holiday_name TEXT(65), MODIFY COLUMN holiday_start DATE, MODIFY COLUMN holiday_end DATE';

    foreach ($sql as $query) {
        if (Db::getInstance()->execute($query) === false) {
            $context->controller->errors[] = Db::getInstance()->getMsgError();
            echo Db::getInstance()->getMsgError();

            return false;
        }
    }

    $sql = 'SELECT * FROM information_schema.statistics 
    WHERE table_schema = \'' . _DB_NAME_ . '\'
    AND table_name = \'' . _DB_PREFIX_ . 'ed_holidays\' AND column_name = \'holiday_start\'';
    $results = Db::getInstance()->executeS($sql);
    $found = false;
    if ($results !== false && count($results) > 0) {
        foreach ($results as $result) {
            if ($result['INDEX_NAME'] == 'ed_holiday') {
                $found = true;
            }
        }
    }
    if (!$found) {
        // Update the column types
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ed_holidays` ADD UNIQUE INDEX ed_holiday (`holiday_start`, `holiday_end`, `repeat`)');
    }

    $columns_to_add = [
        [
            'table' => 'ed_warehouse',
            'name' => 'id_manufacturer',
            'type' => 'int(11)',
            'extras' => 'NOT NULL AFTER `id_supplier`',
        ],
    ];
    foreach ($columns_to_add as $column) {
        $sql = 'SELECT COLUMN_NAME as name FROM INFORMATION_SCHEMA.`COLUMNS` WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\' AND TABLE_NAME = \'' . _DB_PREFIX_ . bqSQL($column['table']) . '\' AND COLUMN_NAME = "' . bqSQL($column['name']) . '"';
        $exists = Db::getInstance()->executeS($sql);
        if ($exists === false || count($exists) == 0) {
            $sql = 'ALTER TABLE ' . _DB_PREFIX_ . bqSQL($column['table']) . ' ADD COLUMN `' . bqSQL($column['name']) . '` ' . bqSQL($column['type']) . ($column['extras'] != '' ? ' ' . bqSQL($column['extras']) : '');
            if (Db::getInstance()->execute($sql) === false) {
                return false;
            }
            $sql = [];
            $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ed_warehouse` DROP INDEX `ed_war`';
            $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ed_warehouse` ADD CONSTRAINT ed_war UNIQUE (id_warehouse,id_supplier,id_manufacturer,id_shop)';
        }
    }

    return true;
}
