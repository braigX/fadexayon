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

function upgrade_module_3_1_6($module)
{
    // Add hooks for better email variables!
    if (!$module->isRegisteredInHook('sendMailAlterTemplateVars')) {
        $module->registerHook('sendMailAlterTemplateVars');
    }
    if (!$module->isRegisteredInHook('actionGetExtraMailTemplateVars')) {
        $module->registerHook('actionGetExtraMailTemplateVars');
    }

    return true;
}
