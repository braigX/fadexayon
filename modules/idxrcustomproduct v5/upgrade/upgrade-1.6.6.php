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

function upgrade_module_1_6_6($module)
{
    require_once(_PS_MODULE_DIR_ . 'idxrcustomproduct/sql/upgrade-1.6.6.php');
    return true;
}
