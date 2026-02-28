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

function upgrade_module_1_1_4($module)
{
    if (Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'gmerchantfeedes` LIKE \'id_suffix\'') == false) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'gmerchantfeedes` ADD `id_suffix` VARCHAR( 255 ) NOT NULL AFTER `title_suffix`');
    }

    if (Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'gmerchantfeedes` LIKE \'price_change\'') == false) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'gmerchantfeedes` ADD `price_change` DECIMAL(10, 2) NOT NULL AFTER `title_suffix`');
    }

    if (Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'gmerchantfeedes` LIKE \'price_change_type\'') == false) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'gmerchantfeedes` ADD `price_change_type` VARCHAR( 255 ) NOT NULL AFTER `title_suffix`');
    }

    return $module;
}
