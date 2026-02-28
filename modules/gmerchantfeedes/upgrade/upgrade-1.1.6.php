<?php
/**
 * 2020 ExtraSolutions
 *
 * NOTICE OF LICENSE
 *
 * @author    ExtraSolutions
 * @copyright 2020 ExtraSolutions
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_6($module)
{
    if (Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'gmerchantfeedes` LIKE \'param_order_out_of_stock_sys\'') == false) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'gmerchantfeedes` ADD `param_order_out_of_stock_sys` TINYINT NOT NULL DEFAULT \'0\' AFTER `title_suffix`');
    }

    return $module;
}
