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

function upgrade_module_1_2_1($module)
{
    $module_key = 'gmerchantfeedes';

    if (Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . pSQL($module_key) . '` LIKE \'filter_qty_from\'') == false) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . pSQL($module_key) . '` ADD `filter_qty_from` integer(10) NOT NULL DEFAULT \'0\'');
    }

    $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . pSQL($module_key) . '_product_rewrites` (
        `id_product` int(11) unsigned NOT NULL,
        `title` varchar(255) NOT NULL,
        `short_description` varchar(255) NOT NULL,
        `description` text NOT NULL,
        `id_lang` int(10) unsigned NOT NULL,
        INDEX  (`id_product`, `id_lang`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    if (Db::getInstance()->execute($query) == false) {
        return false;
    }

    return $module;
}
