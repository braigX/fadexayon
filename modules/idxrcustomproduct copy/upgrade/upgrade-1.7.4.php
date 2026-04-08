<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innovadeluxe SL
* @copyright 2023 Innovadeluxe SL

* @license   INNOVADELUXE
*/

function upgrade_module_1_7_4($module)
{
    $module->registerHook('displayBackOfficeHeader');
    return true;
}
