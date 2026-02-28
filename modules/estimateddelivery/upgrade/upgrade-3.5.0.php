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

function upgrade_module_3_5_0($module)
{
    $module->manageTabs();

    // Remove the old ajax file
    if (file_exists(dirname(__FILE__) . '/estimateddelivery_ajax.php')) {
        unlink(dirname(__FILE__) . '/estimateddelivery_ajax.php');
    }
    // Add the default value of an option to allow showing ED in the modal box after ajax cart successes
    Configuration::updateValue('ed_cart_modal', 0);

    return true;
}
