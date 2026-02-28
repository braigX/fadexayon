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

function upgrade_module_3_3_4()
{
    // Add the default value for sorting EDs according to the options.
    Configuration::updateValue('ED_DEFAULT_CARRIER_FIRST', 0);
    Configuration::updateValue('ED_DISPLAY_PRIORITY', 1);
    // Add the default value for geolocation
    Configuration::updateValue('ED_DISABLE_GEOLOCATION', 0);

    Configuration::updateValue('ED_LOCATION_INS', 2);
    Configuration::updateValue('ED_LOCATION_SEL', '');
    Configuration::updateValue('ED_SHOW_INVOICE', 0);

    $add_columns = [
        [
            'table' => 'ed_orders',
            'column_name' => 'individual_dates',
            'parameters' => 'TINYINT(1) NOT NULL DEFAULT 0',
        ],
    ];
    foreach ($add_columns as $col) {
        if (Db::getInstance()->getValue('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = \'' . _DB_PREFIX_ . bqSQL($col['table']) . '\' AND COLUMN_NAME = \'' . bqSQL($col['column_name']) . '\'') === false) {
            // Colum doesn't exist, add it
            if (Db::getInstance()->execute(pSQL('ALTER TABLE ' . _DB_PREFIX_ . bqSQL($col['table']) . ' ADD COLUMN `' . bqSQL($col['column_name']) . '` ' . pSQL($col['parameters']))) === false) {
                return false;
            }
        }
    }

    return true;
}
