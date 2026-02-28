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

function upgrade_module_1_5_0($module)
{
    require_once(_PS_MODULE_DIR_ . 'idxrcustomproduct/sql/upgrade-1.5.0.php');
    return true;
}
