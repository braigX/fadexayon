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

function upgrade_module_1_3_6($module)
{
    $module_key = 'gmerchantfeedes';

    if (Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . pSQL($module_key) . '` LIKE \'exclude_discount_price_more\'') == false) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . pSQL($module_key) . '` ADD `exclude_discount_price_more` decimal(10, 3) NOT NULL DEFAULT 0');
    }

    return $module;
}
