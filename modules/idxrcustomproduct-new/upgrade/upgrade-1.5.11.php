<?php
/**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innovadeluxe SL
* @copyright 2022 Innovadeluxe SL

* @license   INNOVADELUXE
*/

function upgrade_module_1_5_11($module)
{
    Configuration::updateValue(Tools::strtoupper($module->name) . '_DISCOUNTLINE', true);
    return true;
}
