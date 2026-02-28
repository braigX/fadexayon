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

function upgrade_module_2_0_0($module)
{
    $sql = '';
    $sql[] = 'ALTER TABLE ' . _DB_PREFIX_ . 'carrier ADD `picking_days` TEXT NOT NULL, ADD `picking_limit` TEXT NOT NULL, ADD `ed_active` TEXT NOT NULL, ADD `ed_alias` TEXT NOT NULL';

    foreach ($sql as $query) {
        if (!DB::getInstance()->execute(pSQL($query))) {
            return false;
        }
    }
    // All done if we get here the upgrade is successfull
    $module->registerHook('actionAdminControllerSetMedia');

    return true;
}
