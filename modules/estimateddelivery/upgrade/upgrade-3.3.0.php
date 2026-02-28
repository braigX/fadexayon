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

function upgrade_module_3_3_0($module)
{
    // Add the new hook to display the delivery messate on each product at the summary page
    $module->registerHook('displayCartSummaryProductDelivery');
    // Update email will no longer be used as a module configuration.
    // Now the module asks if you want to send an email after the delivery date is updated
    Configuration::deleteByName('ED_UPDATE_EMAIL');

    // Add a new column for order messages
    return true;
}
