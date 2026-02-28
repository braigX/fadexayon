<?php
/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innovadeluxe SL
* @copyright 2021 Innovadeluxe SL

* @license   INNOVADELUXE
*/

function upgrade_module_1_5_6($module)
{
    if (!HookCore::getIdByName('actionAdminProductsListingFieldsModifier')) {
        $hook_action = new Hook();
        $hook_action->name = 'actionAdminProductsListingFieldsModifier';
        $hook_action->title = 'actionAdminProductsListingFieldsModifier';
        $hook_action->description = 'actionAdminProductsListingFieldsModifier';
        $hook_action->add();
    }
    
    
    return $module->unregisterHook('actionAdminProductsListingResultsModifier') 
            && $module->registerHook('actionAdminProductsListingFieldsModifier');
}
