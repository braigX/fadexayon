<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'topbanner` (
    `id_banner` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`height` varchar(10) NOT NULL DEFAULT "300",
	`background` varchar(10) NOT NULL,
	`type` int(11) NOT NULL,
	`subtype` int(11) NOT NULL,
	`cartrule` int(11) NOT NULL,
	`timer` int(11) NOT NULL,
	`timer_left_text` varchar(255) NOT NULL,
	`timer_right_text` varchar(255) NOT NULL,
	`timer_background` varchar(10) NOT NULL,
	`timer_text_color` varchar(10) NOT NULL,
	`text` varchar(255) NOT NULL,
	`mobile_text` varchar(255) NOT NULL,
	`text_carrier_empty` varchar(255) NOT NULL,
	`text_carrier_between` varchar(255) NOT NULL,
	`text_carrier_full` varchar(255) NOT NULL,
	`text_size` int(4) NOT NULL,
	`mobile_text_size` int(4) NOT NULL,
	`with_mobile_text` int(1) NOT NULL DEFAULT "0",
	`cta` int(1) NOT NULL,
	`cta_text` varchar(255) NOT NULL,
	`cta_link` varchar(255) NOT NULL,
	`cta_text_color` varchar(10) NOT NULL,
	`cta_background` varchar(10) NOT NULL,
	`status` int(1) NOT NULL DEFAULT "0",
    PRIMARY KEY  (`id_banner`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'topbanner_lang` (
  `id_banner` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  UNIQUE KEY `id_banner` (`id_banner`,`id_lang`,`id_shop`,`name`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

// Check if 'height' column exists, add if missing
if (!Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'topbanner` LIKE "height"')) {
    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'topbanner` ADD `height` VARCHAR(10) NOT NULL DEFAULT "300"';
}

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
