<?php
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015-2018
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

// Estimated Delviery Review Installation Procedure
$specific = 'review-install';
$output = '';
$tables = [
    'edholidays' => $this->l('Holidays table', $specific),
    'ed_cat_oos' => $this->l('Out of Stock days by Category table', $specific),
    'ed_prod_oos' => $this->l('Out of Stock days by Product table', $specific),
    'ed_prod_combi_oos' => $this->l('OOS Product combinations table', $specific),
    'ed_shop' => $this->l('Carrier additional parametters', $specific),
    'ed_prod_oos' => $this->l('Product OOS additional parametters', $specific),
    'ed' => $this->l('Estimated Delivery Order History Table', $specific),
    'ed_brand_picking' => $this->l('Additional Picking Days by Brand / Manufacturer', $specific),
    'ed_supplier_picking' => $this->l('Additional Picking Days by Supplier', $specific),
    'ed_supplier_oos' => $this->l('OOS additional days by supplier', $specific),
];
$columnsCheck = [
    // 'carrier' => array('shippingdays', 'min', 'max', 'picking_days', 'picking_limit', 'ed_active', 'ed_alias'),
    'ed_prod_oos' => ['release_date', 'ppicking_days'],
    'ed_cat_oos' => ['ppicking_days'],
    'ed' => ['id_carrier'],
];

// Search for older versions and remove the params for the carrier database
$search = ['shippingdays', 'min', 'max', 'picking_days', 'picking_limit', 'ed_active', 'ed_alias'];
$drop = [];
$sql = 'SELECT COLUMN_NAME as column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\' AND TABLE_NAME = \'' . _DB_PREFIX_ . bqSQL('carrier') . '\'';
$results = Db::getInstance()->executeS(pSQL($sql));
foreach ($results as $result) {
    if (in_array($result['column_name'], $search)) {
        $drop[] = $results['column_name'];
    }
}
if (count($drop) > 0) {
    $sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'carrier DROP COLUMN ' . implode(', DROP COLUMN ', $drop);
    Db::getInstance()->execute(pSQL($sql));
}

include_once _PS_MODULE_DIR_ . $this->name . '/sql/review-install.php';
foreach ($tables as $table => $msg) {
    $sql = 'SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\' AND TABLE_NAME = \'' . _DB_PREFIX_ . bqSQL($table) . '\'';
    $results = DB::getInstance()->executeS($sql);
    if ($results === false || empty($results)) {
        if ($this->reviewInstall($table) === true) {
            $output .= $this->displayConfirmation($msg . ' ' . $this->l('reviewed'), $specific);
            // $this->_confirmations[] = $msg.' '.$this->l('reviewed', $specific);
        }
    } else {
        $output .= $this->displayConfirmation($msg . ' ' . $this->l('OK'), $specific);
        // $this->_confirmations[] = $msg.' '.$this->l('OK', $specific);
    }
    // echo '<!-- ';
    // echo "\n<br>".print_r($table, true);
    // echo "\n<br>".print_r($columnsCheck, true);
    if (array_key_exists($table, $columnsCheck)) {
        // echo "\n<br>".'yes';
        $sql = 'SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\' AND TABLE_NAME = \'' . _DB_PREFIX_ . bqSQL($table) . '\'';
        $results = DB::getInstance()->executeS($sql);
        $columns = $columnsCheck[$table];
        // echo "\n<br>".print_r(array($columns, $results), true).' -->';
        $reviewed = false;
        if ($results !== 'false') {
            foreach ($results as $result) {
                foreach ($columns as $key => $column) {
                    if ($column == 'ppicking_days') {
                        $column = Tools::substr($column, 1);
                    }
                    if ($result['COLUMN_NAME'] == $column) {
                        unset($columns[$key]);
                    }
                }
            }
            // Add the missing columns
            if (!empty($columns) && count($columns) > 0) {
                if ($this->reviewInstall($table, $columns) === true) {
                    $output .= $this->displayConfirmation($msg . ' ' . $this->l('reviewed'), $specific);
                    // $this->_confirmations[] = $msg.' '.$this->l('reviewed', $specific);
                    $reviewed = true;
                }
            }
        }
        if ($reviewed === false) {
            // $this->_confirmations[] = $msg.' '.$this->l('Ok', $specific);
            $output .= $this->displayConfirmation($msg . ' ' . $this->l('OK'), $specific);
        }
    }
}
