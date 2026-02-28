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

// Helper function to check if a column exists
function columnExists($table, $column)
{
    return !empty(Db::getInstance()->executeS('SHOW COLUMNS FROM ' . _DB_PREFIX_ . bqSQL($table) . ' LIKE \'' . bqSQL($column) . '\''));
}

function upgrade_module_3_0_8()
{
    $db = Db::getInstance();
    $sql = [];

    // List of tables and their alterations
    $tables = [
        'ed_brand_picking' => [
            'drop_columns' => ['id_brand_picking'],
            'add_columns' => ['delay' => 'int(4) DEFAULT 0 AFTER `picking_days`'],
            'rename_to' => 'ed_manufacturer',
        ],
        'ed_supplier_picking' => [
            'drop_columns' => ['id_supplier_picking'],
            'add_columns' => ['delay' => 'int(4) DEFAULT 0 AFTER `picking_days`'],
            'rename_to' => 'ed_supplier',
        ],
        'ed_cat_oos' => [
            'drop_columns' => ['id_cat_oos'],
            'rename_to' => 'ed_cat',
        ],
        'ed_prod_oos' => [
            'drop_columns' => ['id_prod_oos'],
            'rename_to' => 'ed_prod',
        ],
        'ed_prod_combi_oos' => [
            'drop_columns' => ['id_product_attribute_oos'],
            'rename_to' => 'ed_prod_combi',
        ],
        'edholidays' => [
            'add_columns' => ['repeat' => 'TINYINT(1) DEFAULT 0 AFTER `holiday_end`'],
            'rename_to' => 'ed_holidays',
        ],
        'ed' => [
            'rename_to' => 'ed_orders',
        ],
        'ed_shop' => [
            'rename_to' => 'ed_carriers',
        ],
    ];

    // Iterate through tables to alter and rename them if they exist
    foreach ($tables as $table => $actions) {
        $table_exists = $db->executeS('SHOW TABLES LIKE \'' . _DB_PREFIX_ . bqSQL($table) . '\'');

        if ($table_exists !== false && count($table_exists) > 0) {
            // Drop columns if they exist
            if (!empty($actions['drop_columns'])) {
                foreach ($actions['drop_columns'] as $column) {
                    if (columnExists($table, $column)) {
                        $sql[] = 'ALTER TABLE ' . _DB_PREFIX_ . bqSQL($table) . ' DROP COLUMN `' . bqSQL($column) . '`';
                    }
                }
            }
            // Add columns if they do not exist
            if (!empty($actions['add_columns'])) {
                foreach ($actions['add_columns'] as $column => $definition) {
                    if (!columnExists($table, $column)) {
                        $sql[] = 'ALTER TABLE ' . _DB_PREFIX_ . bqSQL($table) . ' ADD COLUMN `' . bqSQL($column) . '` ' . $definition;
                    }
                }
            }
            // Rename the table
            if (!empty($actions['rename_to'])) {
                $sql[] = 'RENAME TABLE ' . _DB_PREFIX_ . bqSQL($table) . ' TO ' . _DB_PREFIX_ . bqSQL($actions['rename_to']);
            }
        }
    }

    // Execute SQL queries
    foreach ($sql as $query) {
        if ($db->execute($query) === false) {
            return false;
        }
    }

    // Handle migration from `ed_supplier_oos` to `ed_supplier`
    $table = 'ed_supplier_oos';
    $table_exists = $db->executeS('SHOW TABLES LIKE \'' . _DB_PREFIX_ . bqSQL($table) . '\'');

    if ($table_exists !== false && count($table_exists) > 0) {
        $results = $db->executeS('SELECT * FROM ' . _DB_PREFIX_ . bqSQL($table));
        if ($results === false) {
            return false;
        }

        if (count($results) > 0) {
            // Empty `ed_supplier` if `ed_supplier_oos` has data to migrate
            Db::getInstance()->execute('TRUNCATE TABLE ' . _DB_PREFIX_ . 'ed_supplier');

            $erronupdate = false;
            foreach ($results as $result) {
                // Use ON DUPLICATE KEY UPDATE to handle any unique constraints
                $query = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_supplier (id_supplier, id_shop, delay)
                      VALUES (' . (int) $result['id_supplier'] . ', ' . (int) $result['id_shop'] . ', ' . (int) $result['delay'] . ')
                      ON DUPLICATE KEY UPDATE delay = VALUES(delay)';

                if ($db->execute($query) === false) {
                    $erronupdate = true;
                }
            }

            if ($erronupdate === false) {
                $db->execute('DROP TABLE ' . _DB_PREFIX_ . bqSQL($table));
            }
        }
    }

    return true;
}
