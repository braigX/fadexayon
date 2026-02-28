<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innovadeluxe SL
* @copyright 2016 Innovadeluxe SL

* @license   INNOVADELUXE
*/

function upgrade_module_1_4_4($module)
{
    require_once(_PS_MODULE_DIR_ . 'idxrcustomproduct/sql/upgrade-1.4.4.php');
    Configuration::updateValue(Tools::strtoupper($module->name) . '_SHOWFAV', true);
    return true;
}
