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

function upgrade_module_1_6_3($module)
{
    require_once(_PS_MODULE_DIR_ . 'idxrcustomproduct/sql/upgrade-1.6.3.php');
    return true;
}
