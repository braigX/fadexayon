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

function upgrade_module_3_0_9()
{
    $context = Context::getContext();
    // Update the module, join and rename tables to save up resources
    $sql = 'SELECT COLUMN_NAME as name FROM INFORMATION_SCHEMA.`COLUMNS` WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\' AND TABLE_NAME = \'' . _DB_PREFIX_ . 'ed_carriers\' AND COLUMN_NAME = "ignore_picking"';
    $exist = Db::getInstance()->executeS($sql);
    if ($exist === false || count($exist) == 0) {
        // Create the ignore_picking column
        $sql = [];
        $sql[] = 'ALTER TABLE ' . _DB_PREFIX_ . 'ed_carriers ADD COLUMN `ignore_picking` tinyint(1) DEFAULT 0 AFTER `picking_limit`';
        foreach ($sql as $query) {
            if (Db::getInstance()->execute(pSQL($query)) === false) {
                return false;
            }
        }
    }

    // Review old columns and remove them if still exist
    $sql = 'SELECT COLUMN_NAME as name FROM INFORMATION_SCHEMA.`COLUMNS` WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\' AND TABLE_NAME = \'' . _DB_PREFIX_ . 'carrier\'';
    $columns = Db::getInstance()->executeS($sql);
    if ($columns === false) {
        $context->controller->errors[] = Db::getInstance()->getMsgError();
    } elseif (count($columns) > 0) {
        if (function_exists('array_column')) {
            $columns = array_column($columns, 'name');
        } else {
            $columns = getColumnsFromArray($columns, 'name');
        }
        $col_list = ['shippingdays', 'min', 'max', 'picking_days', 'picking_limit', 'ed_active', 'ed_alias'];
        $max = count($col_list);
        for ($i = 0; $i < $max; ++$i) {
            if (!in_array($col_list[$i], $columns)) {
                unset($col_list[$i]);
            }
        }
        if (count($col_list) > 0) {
            $sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'carrier ';
            foreach ($col_list as $c) {
                $sql .= 'DROP `' . $c . '`,';
            }
            $sql = Tools::substr($sql, 0, -1);
            if (Db::getInstance()->execute($sql) === false) {
                echo 'ERROR: ' . Db::getInstance()->getMsgError();
            }
        }
    }

    return true;
}

function getColumnsFromArray($array, $columnName)
{
    $ret = [];
    foreach ($array as $i) {
        if (isset($i[$columnName])) {
            $ret[] = $i[$columnName];
        }
    }

    return $ret;
}
