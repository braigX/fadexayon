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

function upgrade_module_3_5_2()
{
    // Add the configration for the customization days
    Configuration::updateValue('ED_ADD_CUSTOM_DAYS_MODE', 0);
    $add_columns = [
        [
            'table' => 'ed_prod',
            'column_name' => 'customization_days',
            'parameters' => 'int(4) DEFAULT 0',
        ],
        [
            'table' => 'ed_prod_combi',
            'column_name' => 'customization_days',
            'parameters' => 'int(4) DEFAULT 0',
        ],
        [
            'table' => 'ed_supplier',
            'column_name' => 'customization_days',
            'parameters' => 'int(4) DEFAULT 0',
        ],
        [
            'table' => 'ed_manufacturer',
            'column_name' => 'customization_days',
            'parameters' => 'int(4) DEFAULT 0',
        ],
        [
            'table' => 'ed_cat',
            'column_name' => 'customization_days',
            'parameters' => 'int(4) DEFAULT 0',
        ],
    ];
    foreach ($add_columns as $col) {
        if (Db::getInstance()->getValue('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = \'' . _DB_PREFIX_ . bqSQL($col['table']) . '\' AND COLUMN_NAME = \'' . bqSQL($col['column_name']) . '\'') === false) {
            // If Column doesn't exist, add it
            if (Db::getInstance()->execute(pSQL('ALTER TABLE ' . _DB_PREFIX_ . bqSQL($col['table']) . ' ADD COLUMN `' . bqSQL($col['column_name']) . '` ' . pSQL($col['parameters']))) === false) {
                return false;
            }
        }
    }

    return true;
}
