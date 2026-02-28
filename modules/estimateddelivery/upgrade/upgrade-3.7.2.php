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

function upgrade_module_3_7_2($module)
{
    $summary_options = Configuration::get('ED_ORDER_SUMMARY');
    if (in_array($summary_options, [1, 3])) {
        Configuration::updateValue('ED_ORDER_SUMMARY', 2);
    }
    if (in_array($summary_options, [2, 3])) {
        Configuration::updateValue('ED_ORDER_SUMMARY_PRODUCT', 1);
    }
    if (!$module->isRegisteredInHook('displayShoppingCart')) {
        $module->registerHook('displayShoppingCart');
    }

    return true;
}
