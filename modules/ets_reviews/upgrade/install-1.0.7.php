<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

/**
 * @param $object Ets_reviews
 * @return bool
 */
function upgrade_module_1_0_7($object)
{
    $res = Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_email_queue` 
        DROP COLUMN `is_read`,
        DROP COLUMN `delivered`,
        DROP COLUMN `guid`,
        DROP COLUMN `to`
    ');

    $res &= Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_email_queue` 
        ADD COLUMN `id_shop` INT(11) UNSIGNED NOT NULL,
        ADD COLUMN `id_customer` INT(11) UNSIGNED NOT NULL,
        ADD COLUMN `employee` TINYINT(11) UNSIGNED NOT NULL,
        ADD COLUMN `date_add` datetime DEFAULT NULL
    ');

    $res &= Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_email_queue` 
        ADD INDEX `idx_id_shop` (`id_shop`),
        ADD INDEX `idx_id_customer` (`id_customer`),
        ADD INDEX `idx_employee` (`employee`)
    ');

    $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_rv_tracking`(
        `id_ets_rv_tracking` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `id_customer` INT(11) UNSIGNED NOT NULL,
        `employee` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
        `email` varchar(255) NOT NULL,
        `subject` varchar(500) NOT NULL,
        `guid` varchar(128) NOT NULL,
        `ip_address` varchar(64) NOT NULL,
        `is_read` tinyint(1) UNSIGNED DEFAULT 0,
        `delivered` tinyint(1) UNSIGNED DEFAULT 0,
        `id_shop` INT(11) UNSIGNED NOT NULL,
        `product_id` INT(11) UNSIGNED NOT NULL,
        `product_comment_id` INT(11) UNSIGNED NOT NULL,
        `queue_id` INT(11) UNSIGNED NOT NULL,
        `id_order` INT(11) UNSIGNED NOT NULL,
        `date_add` datetime DEFAULT NULL,
        `date_upd` datetime DEFAULT NULL,
        PRIMARY KEY (`id_ets_rv_tracking`),
        KEY `idx_id_shop` (`id_shop`),
        KEY `idx_product_id` (`product_id`),
        KEY `idx_id_order` (`id_order`),
        KEY `idx_id_customer` (`id_customer`),
        KEY `idx_employee` (`employee`),
        KEY `idx_guid` (`guid`),
        KEY `idx_product_comment_id` (`product_comment_id`),
        KEY `idx_queue_id` (`queue_id`)
    ) ENGINE = ' . _MYSQL_ENGINE_ . ' CHARSET = utf8mb4;');

    $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_rv_discount`(
        `id_ets_rv_tracking` INT(11) UNSIGNED NOT NULL,
        `id_cart_rule` INT(11) UNSIGNED NOT NULL,
        `use_same_cart` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (`id_ets_rv_tracking`, `id_cart_rule`),
        KEY `idx_use_same_cart` (`use_same_cart`)
    ) ENGINE = ' . _MYSQL_ENGINE_ . ' CHARSET = utf8mb4;');

    $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_rv_email_template`(
       `id_ets_rv_email_template` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
       `template` varchar(128) NOT NULL,
        `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
       PRIMARY KEY (`id_ets_rv_email_template`),
       UNIQUE KEY `uiq_template` (`template`)
    ) ENGINE = ' . _MYSQL_ENGINE_ . ' CHARSET = utf8mb4;');

    $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_rv_email_template_shop`(
        `id_ets_rv_email_template` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `id_shop` INT(11) UNSIGNED NOT NULL,
        `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
        PRIMARY KEY (`id_ets_rv_email_template`, `id_shop`)
    ) ENGINE = ' . _MYSQL_ENGINE_ . ' CHARSET = utf8mb4;');

    $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_rv_email_template_lang`(
        `id_ets_rv_email_template` INT(11) UNSIGNED NOT NULL,
        `id_lang` INT(11) UNSIGNED NOT NULL,
        `id_shop` INT(11) UNSIGNED NOT NULL,
        `subject` varchar(500) NOT NULL,
        PRIMARY KEY (`id_ets_rv_email_template`, `id_lang`, `id_shop`)
    ) ENGINE = ' . _MYSQL_ENGINE_ . ' CHARSET = utf8mb4;');

    $res &= EtsRVTools::getInstance()->upgradeActivities();

    $res &= Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_activity` CHANGE `content` `content` VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;');

    $res &= Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_criterion` 
        ADD COLUMN `deleted` tinyint(1) NOT NULL DEFAULT 0
    ');

    $res &= EtsRVTools::getInstance()->initEmailTemplate();

    return $res && $object->uninstallQuickTabs() && $object->installQuickTabs();
}