<?php
/**
* 2007-2026 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innovadeluxe SL
* @copyright 2026 Innovadeluxe SL
*
* @license   INNOVADELUXE
*/

function upgrade_module_1_7_6($module)
{
    require_once(_PS_MODULE_DIR_ . 'idxrcustomproduct/sql/upgrade-1.7.6.php');
    return true;
}

