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

function upgrade_module_3_0_0($module)
{
    if (!$module->isRegisteredInHook('displayOrderConfirmation')) {
        $module->registerHook('displayOrderConfirmation');
    }
    if (!$module->isRegisteredInHook('actionOrderStatusPostUpdate')) {
        $module->registerHook('actionOrderStatusPostUpdate');
    }

    // Define columns to add with parameters
    $add_columns = [
        [
            'table' => 'ed',  // Check old table name first
            'column_name' => 'is_definitive',
            'parameters' => 'TINYINT(1) NOT NULL DEFAULT 0',
        ],
        [
            'table' => 'ed_prod_combi_oos',
            'column_name' => 'delay',
            'parameters' => 'int(4) DEFAULT 0',
        ],
        [
            'table' => 'ed_prod_combi_oos',
            'column_name' => 'picking_days',
            'parameters' => 'int(4) DEFAULT 0',
        ],
        [
            'table' => 'ed_prod_combi_oos',
            'column_name' => 'release_date',
            'parameters' => 'TEXT',
        ],
    ];

    // Add columns if they do not exist, and check if the table itself exists
    foreach ($add_columns as $col) {
        // Check if the table exists (using old table name 'ed' for 'ed_orders')
        $table_exists = Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . bqSQL($col['table']) . '"');
        if ($table_exists === false || count($table_exists) === 0) {
            continue; // Skip this table if it does not exist
        }

        // Check if the column exists in the table
        $column_exists = Db::getInstance()->executeS('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                                                      WHERE TABLE_NAME = "' . _DB_PREFIX_ . bqSQL($col['table']) . '" 
                                                      AND COLUMN_NAME = "' . bqSQL($col['column_name']) . '" 
                                                      AND TABLE_SCHEMA = "' . _DB_NAME_ . '"');

        // Add the column if it does not exist
        if (empty($column_exists)) {
            $alter_query = 'ALTER TABLE ' . _DB_PREFIX_ . bqSQL($col['table']) . ' 
                            ADD COLUMN `' . bqSQL($col['column_name']) . '` ' . $col['parameters'];
            if (Db::getInstance()->execute($alter_query) === false) {
                return false; // Stop the upgrade if the query fails
            }
        }
    }

    // Run the additional SQL update only if the table `ed` (or renamed `ed_orders`) exists
    $ed_table_exists = Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . 'ed"');
    if ($ed_table_exists !== false && count($ed_table_exists) > 0) {
        $sql_update = 'UPDATE `' . _DB_PREFIX_ . 'ed` SET `is_definitive` = 1';
        if (Db::getInstance()->execute($sql_update) === false) {
            return false; // Stop the upgrade if the update query fails
        }
    }

    return true;
}
