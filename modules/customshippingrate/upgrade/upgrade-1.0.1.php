<?php
/**
* 2018 Prestamatic
*
* NOTICE OF LICENSE
*
*  @author    Prestamatic
*  @copyright 2018 Prestamatic
*  @license   Licensed under the terms of the MIT license
*  @link      https://prestamatic.co.uk
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_1($module)
{
    $return = $module->registerHook('actionAdminControllerSetMedia');
    $return = $return && Db::getInstance()->execute(
        'ALTER TABLE `'._DB_PREFIX_.'customshippingrate` ADD `order_total` decimal(17,2) NOT NULL AFTER `total_weight`'
    );
    return $return;
}