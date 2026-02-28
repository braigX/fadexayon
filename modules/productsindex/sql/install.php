<?php
/**
 * 2007 - 2017 ZSolutions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Eugene Zubkov <magrabota@gmail.com>
 *  @copyright 2018 ZLabSolutions
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Property of ZLabSolutions https://www.facebook.com/ZLabSolutions/
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = [];

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'zlcpi_settings`';
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'zlcpi_settings` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`option` varchar(50) NOT NULL,
		`value` varchar(150) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=' . _MYSQL_ENGINE_ . '  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'zlcpi_log`';
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'zlcpi_log` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`date` varchar(50) NOT NULL,
	`log` varchar(100) NOT NULL,
	`timestamp` int(11) NOT NULL,
	PRIMARY KEY (`id`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

// $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'zlcpi_log`';
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'zlcpi_manufacturer_product` (
	`id_manufacturer` int(10) unsigned NOT NULL,
	`id_product` int(10) unsigned NOT NULL,
	`position` int(10) unsigned NOT NULL DEFAULT \'0\',
	PRIMARY KEY (`id_manufacturer`,`id_product`),
	KEY `id_product` (`id_product`),
	KEY `id_manufacturer` (`id_manufacturer`,`position`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

$handle = fopen(_PS_MODULE_DIR_ . 'productsindex/sql/settings.sql', 'r');
if ($handle) {
    while (($sql_line = fgets($handle)) !== false) {
        $sql_line = str_replace('ps_zlc', _DB_PREFIX_ . 'zlc', $sql_line);
        if (Db::getInstance()->execute($sql_line) == false) {
            return false;
        }
    }
    fclose($handle);
}
