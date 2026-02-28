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

function upgrade_module_1_5_12($module)
{
    require_once(_PS_MODULE_DIR_ . 'idxrcustomproduct/sql/upgrade-1.5.12.php');
    return true;
}
