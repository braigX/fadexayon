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

function upgrade_module_1_1_0($module)
{
    if (Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'gmerchantfeedes` LIKE \'google_product_category_rewrite\'') == false) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'gmerchantfeedes` ADD `google_product_category_rewrite` TINYINT(1) DEFAULT 0 AFTER `instance_of_tax`');
    }

    if (Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'gmerchantfeedes` LIKE \'shipping_weight_format\'') == false) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'gmerchantfeedes` ADD `shipping_weight_format` TINYINT(1) AFTER `instance_of_tax`');
    }

    return $module;
}
