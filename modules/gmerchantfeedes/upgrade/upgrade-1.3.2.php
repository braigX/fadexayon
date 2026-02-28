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

function upgrade_module_1_3_2($module)
{
    $module_key = 'gmerchantfeedes';

    if (Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . pSQL($module_key) . '_product_rewrites` LIKE \'addition_code\'') == false) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . pSQL($module_key) . '_product_rewrites` ADD `addition_code` TEXT DEFAULT NULL');
    }

    return $module;
}
