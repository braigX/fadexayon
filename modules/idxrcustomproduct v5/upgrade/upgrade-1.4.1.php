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

function upgrade_module_1_4_1($module)
{
    require_once(_PS_MODULE_DIR_ . 'idxrcustomproduct/sql/upgrade-1.4.1.php');
    return true;
}
