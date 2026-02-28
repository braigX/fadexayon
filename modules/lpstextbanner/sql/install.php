<?php
/**
 * Loulou66
 * LpsTextBanner module for Prestashop
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php*
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Loulou66.fr <contact@loulou66.fr>
 *  @copyright loulou66.fr
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = [];
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lpstextbanner_config` (
    `id_lpstextbanner_config` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `display_banner` tinyint(1) unsigned NOT NULL DEFAULT 1,
    `fixed_banner` tinyint(1) unsigned NOT NULL DEFAULT 1,
    `banner_background_color` varchar(50) NOT NULL,
    `banner_text_color` varchar(50) NOT NULL,
    `transition_effect` varchar(100) NOT NULL,
    `directionH` varchar(100) NOT NULL,
    `directionV` varchar(100) NOT NULL,
    `speedScroll` INT(10) UNSIGNED NOT NULL,
    `displayTime` INT(10) UNSIGNED NOT NULL,
    `id_shop` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id_lpstextbanner_config`)
   )  ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lpstextbanner` (
    `id_lpstextbanner` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `display_link` tinyint(1) unsigned NOT NULL DEFAULT 0,
    `target` tinyint(1) NOT NULL,
    `active` tinyint(1) NOT NULL,
    `position` int(10) unsigned NOT NULL DEFAULT "0",
    `id_shop` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id_lpstextbanner`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lpstextbanner_lang` (
    `id_lpstextbanner` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_lang` int(10) unsigned NOT NULL,
    `message`  text NOT NULL,
    `link` varchar(500) NOT NULL,
    PRIMARY KEY (`id_lpstextbanner`, `id_lang`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
return true;
