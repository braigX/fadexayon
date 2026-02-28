<?php
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @version 3.5.4
 *
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                    V 3.5.4                      *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_5_4($module)
{
    // Add the configuration for the delayed delivery warning
    Configuration::updateValue('enable_delayed_delivery', 0);
    Configuration::updateValue('enable_cc_email', 0);
    Configuration::updateValue('ed_dd_admin_hours', 0);
    Configuration::updateValue('dd_customer_hours', 0);
    Configuration::updateValue('ed_cron_secret_key', Tools::getAdminTokenLite('AdminModules'));
    Configuration::updateValue('ed_dd_admin_email', Configuration::get('PS_SHOP_EMAIL'));

    // addtional option for the customization days
    Configuration::updateValue('enable_custom_days', 1);
    Configuration::updateValue('custom_module_for_custom_days', 0);

    // Register new hook.
    if (!$module->isRegisteredInHook('actionOrderStatusPostUpdate')) {
        $module->registerHook('actionOrderStatusPostUpdate');
    }
    $sql = [];

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_order_individual` (`id_order` int(11) NOT NULL, `id_order_detail` int(11) NOT NULL, `delivery_min` TEXT NOT NULL, `delivery_max` TEXT NOT NULL, CONSTRAINT ed_individual UNIQUE (id_order, id_order_detail)) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    foreach ($sql as $query) {
        if (DB::getInstance()->execute(pSQL($query)) === false) {
            Tools::DisplayError($module->l('Could not create the database. Error upgrading'));

            return false;
        }
    }

    $add_columns = [
        [
            'table' => 'ed_orders',
            'column_name' => 'shipped',
            'parameters' => 'TINYINT(1) DEFAULT 0',
        ],
        [
            'table' => 'ed_orders',
            'column_name' => 'admin_notified',
            'parameters' => 'TINYINT(1) DEFAULT 0',
        ],
        [
            'table' => 'ed_orders',
            'column_name' => 'client_notified',
            'parameters' => 'TINYINT(1) DEFAULT 0',
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
