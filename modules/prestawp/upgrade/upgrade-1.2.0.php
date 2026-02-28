<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2019 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_0($module)
{
    try {
        $module->registerHook('moduleRoutes');

        // blocks table
        $db_result = Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestawp_block` (
            `id_prestawp_block` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_shop` INT(10) UNSIGNED NOT NULL,
            `hook` VARCHAR(255),
            `active` TINYINT(1) DEFAULT 1,
            `number_of_posts` INT(10) UNSIGNED,
            `grid_columns` INT(4) UNSIGNED,
            `show_featured_image` TINYINT(1) DEFAULT 1,
            `show_preview_no_img` TINYINT(1) DEFAULT 1,
            `masonry` TINYINT(1) DEFAULT 1,
            `title_color` VARCHAR(255),
            `title_bg_color` VARCHAR(255),
            `show_article_footer`  TINYINT(1) DEFAULT 1,
            `show_full_posts`  TINYINT(1) DEFAULT 0,
            `strip_tags`  TINYINT(1) DEFAULT 1,
            `ps_categories` TEXT,
            `wp_categories` TEXT,
            `wp_posts` TEXT,
            PRIMARY KEY (`id_prestawp_block`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8');
        if (!$db_result) {
            return false;
        }
        // blocks lang table
        $db_result = Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestawp_block_lang` (
            `id_prestawp_block` INT(10) UNSIGNED NOT NULL,
            `id_lang` INT(10) UNSIGNED NOT NULL,
            `title` VARCHAR(255),
            UNIQUE (`id_prestawp_block`, `id_lang`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8');
        if (!$db_result) {
            return false;
        }

        // cache table
        $db_result = Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestawp_cache` (
            `id_prestawp_cache` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `cache_id` VARCHAR(255),
            `filename` VARCHAR(255),
            `datetime` DATETIME,
            PRIMARY KEY (`id_prestawp_cache`),
            UNIQUE (`cache_id`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8');
        if (!$db_result) {
            return false;
        }
    } catch (Exception $e) {
        // ignore
    }

    return true; // Return true if success.
}
