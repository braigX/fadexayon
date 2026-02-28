<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class WkSampleDb
{
    public function createTable()
    {
        if ($sql = $this->getModuleSql()) {
            foreach ($sql as $query) {
                if ($query) {
                    if (!Db::getInstance()->execute(trim($query))) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function getModuleSql()
    {
        return [
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_sample_product` (
                `id_sample_product` int(10) unsigned NOT NULL auto_increment,
                `id_product` int(10) unsigned NOT NULL,
                `id_product_attribute` int(10) unsigned NOT NULL,
                `max_cart_qty` int(10) unsigned NOT NULL,
                `price_type` int(10) unsigned NOT NULL,
                `price_tax` int(10) unsigned NOT NULL,
                `amount` decimal(17,2) unsigned NOT NULL,
                `price` decimal(17,2) NOT NULL,
                `weight` decimal(17,2) NOT NULL,
                `sample_file` varchar(512) NOT NULL,
                `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
            PRIMARY KEY  (`id_sample_product`)
            ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_sample_product_lang` (
                `id_sample_product` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `button_label` varchar(32) NOT NULL,
                `description` TEXT,
            PRIMARY KEY  (`id_sample_product`, `id_lang`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_sample_product_shop` (
                `id_sample_product` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                `id_product` int(10) unsigned NOT NULL,
                `id_product_attribute` int(10) unsigned NOT NULL,
                `max_cart_qty` int(10) unsigned NOT NULL,
                `price_type` int(10) unsigned NOT NULL,
                `price_tax` int(10) unsigned NOT NULL,
                `amount` decimal(17,2) unsigned NOT NULL,
                `price` decimal(17,2) NOT NULL,
                `weight` decimal(17,2) NOT NULL,
                `sample_file` varchar(512) NOT NULL,
                `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
            PRIMARY KEY  (`id_sample_product`, `id_shop`)
            ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_sample_cart` (
                `id_sample_cart` int(10) unsigned NOT NULL auto_increment,
                `id_cart` int(10) unsigned NOT NULL,
                `id_product_attribute` int(10) unsigned NOT NULL,
                `id_order` int(10) unsigned NOT NULL,
                `id_product` int(10) unsigned NOT NULL,
                `id_specific_price` int(10) unsigned NOT NULL,
                `sample` tinyint(1) unsigned NOT NULL DEFAULT '1',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
            PRIMARY KEY  (`id_sample_cart`)
            ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_sample_cart_shop` (
                `id_sample_cart` int(10) unsigned NOT NULL,
                `id_product_attribute` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                `id_cart` int(10) unsigned NOT NULL,
                `id_order` int(10) unsigned NOT NULL,
                `id_product` int(10) unsigned NOT NULL,
                `id_specific_price` int(10) unsigned NOT NULL,
                `sample` tinyint(1) unsigned NOT NULL DEFAULT '1',
            PRIMARY KEY  (`id_sample_cart`, `id_shop`)
            ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_sample_carrier` (
                `id_wk_sample_carrier` int(10) unsigned NOT NULL auto_increment,
                `id_product` int(10) unsigned NOT NULL,
                `id_carrier_reference` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY  (`id_wk_sample_carrier`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
        ];
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `' . _DB_PREFIX_ . 'wk_sample_product`,
            `' . _DB_PREFIX_ . 'wk_sample_product_lang`,
            `' . _DB_PREFIX_ . 'wk_sample_product_shop`,
            `' . _DB_PREFIX_ . 'wk_sample_cart`,
            `' . _DB_PREFIX_ . 'wk_sample_cart_shop`,
            `' . _DB_PREFIX_ . 'wk_sample_carrier`
        ');
    }

    public static function isDisable($id)
    {
        $sql = 'SELECT `id_module` FROM `' . _DB_PREFIX_ . 'module_shop`
                WHERE `id_module` = ' . (int) $id;

        return Db::getInstance()->getValue($sql);
    }
}
