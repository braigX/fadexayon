<?php
/**
* Since 2013 Ovidiu Cimpean.
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
*
* @version   Release: 4
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

$lang_default = (int) Configuration::get('PS_LANG_DEFAULT');
$shop_default = (int) Configuration::get('PS_SHOP_DEFAULT');

$install = new NewsletterProInstall();

$install->createTable(
    'newsletter_pro_attachment',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_attachment` (
    `id_newsletter_pro_attachment` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `template_name` VARCHAR(255) NOT NULL,
    `files` LONGTEXT NOT NULL,
    PRIMARY KEY (`id_newsletter_pro_attachment`),
    UNIQUE INDEX `template_name` (`template_name`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_customer_category',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_customer_category` (
    `id_newsletter_pro_customer_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `id_customer` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
    `categories` TEXT NULL,
    `date_add` datetime DEFAULT NULL,
    `date_upd` datetime DEFAULT NULL,
    PRIMARY KEY (`id_newsletter_pro_customer_category`),
    UNIQUE INDEX `id_customer` (`id_customer`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_customer_list_of_interests',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_customer_list_of_interests` (
    `id_newsletter_pro_customer_list_of_interests` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `id_customer` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
    `categories` TEXT NULL,
    `date_add` datetime DEFAULT NULL,
    `date_upd` text DEFAULT NULL,
    PRIMARY KEY (`id_newsletter_pro_customer_list_of_interests`),
    UNIQUE INDEX `id_customer` (`id_customer`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_email',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_email` (
    `id_newsletter_pro_email` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_shop` INT(10) UNSIGNED NOT NULL DEFAULT \''.$shop_default.'\',
    `id_shop_group` INT(10) UNSIGNED NOT NULL DEFAULT \''.$shop_default.'\',
    `id_lang` INT(10) UNSIGNED NULL DEFAULT \''.$lang_default.'\',
    `firstname` VARCHAR(32) NULL DEFAULT NULL,
    `lastname` VARCHAR(32) NULL DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL,
    `ip_registration_newsletter` VARCHAR(15) NULL DEFAULT NULL,
    `filter_name` VARCHAR(255) NULL DEFAULT NULL,
    `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `active` TINYINT(1) NOT NULL DEFAULT \'1\',
    PRIMARY KEY (`id_newsletter_pro_email`),
    INDEX `id_shop` (`id_shop`),
    INDEX `id_lang` (`id_lang`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_email_exclusion',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_email_exclusion` (
    `id_newsletter_pro_email_exclusion` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL DEFAULT \'0\',
    PRIMARY KEY (`id_newsletter_pro_email_exclusion`),
    UNIQUE INDEX `email` (`email`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_filters_selection',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_filters_selection` (
    `id_newsletter_pro_filters_selection` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `value` TEXT NULL,
    PRIMARY KEY (`id_newsletter_pro_filters_selection`),
    UNIQUE INDEX `name` (`name`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_forward',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_forward` (
    `id_newsletter_pro_forward` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `from` VARCHAR(128) NOT NULL,
    `to` VARCHAR(128) NOT NULL,
    `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_newsletter_pro_forward`),
    UNIQUE INDEX `to` (`to`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_fwd_unsibscribed',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_fwd_unsibscribed` (
    `id_newsletter_pro_fwd_unsibscribed` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `id_newsletter_pro_tpl_history` int(10) unsigned NOT NULL DEFAULT 0,
    `email` varchar(255) NOT NULL,
    `date_add` datetime DEFAULT NULL,
    `date_upd` datetime DEFAULT NULL,
    PRIMARY KEY (`id_newsletter_pro_fwd_unsibscribed`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_send',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_send` (
    `id_newsletter_pro_send` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_newsletter_pro_tpl_history` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
    `template` VARCHAR(50) NULL DEFAULT NULL,
    `active` INT(1) NOT NULL DEFAULT \'0\',
    `state` INT(10) NOT NULL DEFAULT \'0\',
    `emails_count` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
    `emails_success` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
    `emails_error` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
    `emails_completed` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
    `error_msg` LONGTEXT NULL,
    `date` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id_newsletter_pro_send`),
    INDEX `id_newsletter_pro_tpl_history` (`id_newsletter_pro_tpl_history`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_send_connection',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_send_connection` (
    `id_newsletter_pro_send_connection` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_newsletter_pro_smtp` INT(10) UNSIGNED NOT NULL,
    `state` INT(1) NOT NULL DEFAULT \'0\',
    `script_uid` VARCHAR(50) NULL DEFAULT NULL,
    PRIMARY KEY (`id_newsletter_pro_send_connection`, `id_newsletter_pro_smtp`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_send_step',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_send_step` (
    `id_newsletter_pro_send_step` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_newsletter_pro_send` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
    `id_newsletter_pro_send_connection` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
    `step` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
    `step_active` INT(1) NOT NULL DEFAULT \'0\',
    `emails_to_send` LONGTEXT NULL,
    `emails_sent` LONGTEXT NULL,
    `error_msg` LONGTEXT NULL,
    `date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id_newsletter_pro_send_step`),
    INDEX `id_step` (`step`),
    INDEX `id_task` (`id_newsletter_pro_send`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_smtp',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_smtp` (
    `id_newsletter_pro_smtp` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `method` INT(1) NOT NULL DEFAULT \'1\',
    `name` VARCHAR(64) NOT NULL,
    `from_name` VARCHAR(255) NULL DEFAULT NULL,
    `from_email` VARCHAR(255) NULL DEFAULT NULL,
    `reply_to` VARCHAR(255) NULL DEFAULT NULL,
    `domain` VARCHAR(255) NULL DEFAULT NULL,
    `server` VARCHAR(255) NULL DEFAULT NULL,
    `user` VARCHAR(255) NOT NULL,
    `passwd` VARCHAR(255) NULL DEFAULT NULL,
    `encryption` VARCHAR(255) NULL DEFAULT NULL,
    `port` VARCHAR(255) NULL DEFAULT NULL,
    `list_unsubscribe_active` INT(1) NULL DEFAULT \'0\',
    `list_unsubscribe_email` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id_newsletter_pro_smtp`),
    UNIQUE INDEX `name` (`name`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_statistics',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_statistics` (
    `id_product` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
    `clicks` INT(10) NOT NULL DEFAULT \'0\',
    UNIQUE INDEX `id_product` (`id_product`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_task',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_task` (
    `id_newsletter_pro_task` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_newsletter_pro_smtp` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
    `id_newsletter_pro_tpl_history` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
    `date_start` DATETIME NULL DEFAULT NULL,
    `date_modified` DATETIME NULL DEFAULT NULL,
    `active` INT(1) NOT NULL DEFAULT \'0\',
    `template` VARCHAR(128) NOT NULL,
    `send_method` ENUM(\'mail\',\'smtp\') NOT NULL DEFAULT \'mail\',
    `started` INT(1) NOT NULL DEFAULT 0,
    `status` INT(10) NOT NULL DEFAULT \'0\',
    `sleep` INT(10) NOT NULL DEFAULT \'3\',
    `pause` INT(10) NOT NULL DEFAULT \'0\',
    `emails_count` INT(10) NOT NULL DEFAULT \'0\',
    `emails_error` INT(10) NOT NULL DEFAULT \'0\',
    `emails_success` INT(10) NOT NULL DEFAULT \'0\',
    `emails_completed` INT(10) NOT NULL DEFAULT \'0\',
    `done` INT(10) NOT NULL DEFAULT \'0\',
    `error_msg` LONGTEXT NULL,
    PRIMARY KEY (`id_newsletter_pro_task`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_task_step',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_task_step` (
    `id_newsletter_pro_task_step` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_newsletter_pro_task` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
    `step` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
    `step_active` INT(1) NOT NULL DEFAULT \'0\',
    `emails_to_send` LONGTEXT NULL,
    `emails_sent` LONGTEXT NULL,
    `date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_newsletter_pro_task_step`),
    INDEX `id_step` (`step`),
    INDEX `id_task` (`id_newsletter_pro_task`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

// `template` LONGTEXT NULL,
$install->createTable(
    'newsletter_pro_tpl_history',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_tpl_history` (
    `id_newsletter_pro_tpl_history` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `token` VARCHAR(32) NOT NULL,
    `template_name` VARCHAR(255) NOT NULL,
    `active` INT(1) NULL DEFAULT \'1\',
    `clicks` INT(10) NOT NULL DEFAULT \'0\',
    `opened` INT(10) NOT NULL DEFAULT \'0\',
    `unsubscribed` INT(10) NOT NULL DEFAULT \'0\',
    `fwd_unsubscribed` INT(10) NOT NULL DEFAULT \'0\',
    PRIMARY KEY (`id_newsletter_pro_tpl_history`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_tpl_history_lang',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_tpl_history_lang` (
    `id_newsletter_pro_tpl_history` INT(10) UNSIGNED NOT NULL,
    `id_lang` INT(10) UNSIGNED NOT NULL,
    `template` longtext CHARACTER SET utf8mb4 DEFAULT NULL,
    PRIMARY KEY (`id_newsletter_pro_tpl_history`, `id_lang`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_unsibscribed',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_unsibscribed` (
    `id_newsletter_pro_unsibscribed` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_newsletter_pro_tpl_history` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
    `email` VARCHAR(255) NOT NULL,
    `date_add` datetime DEFAULT NULL,
    `date_upd` datetime DEFAULT NULL,
    PRIMARY KEY (`id_newsletter_pro_unsibscribed`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

// 3.1.1
$install->createTable(
    'newsletter_pro_list_of_interest',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_list_of_interest` (
    `id_newsletter_pro_list_of_interest` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `active` TINYINT(1) NOT NULL DEFAULT \'1\',
    `position` INT(11) NOT NULL DEFAULT \'0\',
    PRIMARY KEY (`id_newsletter_pro_list_of_interest`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_list_of_interest_lang',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_list_of_interest_lang` (
    `id_newsletter_pro_list_of_interest` INT(11) UNSIGNED NOT NULL,
    `id_lang` INT(11) UNSIGNED NOT NULL,
    `id_shop` INT(11) UNSIGNED NOT NULL,
    `name` VARCHAR(255) NULL DEFAULT NULL,
    PRIMARY KEY (`id_newsletter_pro_list_of_interest`, `id_lang`, `id_shop`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_list_of_interest_shop',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_list_of_interest_shop` (
    `id_newsletter_pro_list_of_interest` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
    `id_shop` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
    `active` TINYINT(1) NOT NULL DEFAULT \'1\',
    `position` INT(11) NOT NULL DEFAULT \'0\',
    PRIMARY KEY (`id_newsletter_pro_list_of_interest`, `id_shop`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_mailchimp_token',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_mailchimp_token` (
    `id_newsletter_pro_mailchimp_token` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `token` varchar(50) NOT NULL,
    `creation_date` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
    `modified_date` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
    `expiration_date` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
    PRIMARY KEY (`id_newsletter_pro_mailchimp_token`),
    UNIQUE KEY `token` (`token`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_subscribers',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_subscribers` (
    `id_newsletter_pro_subscribers` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_shop` INT(10) UNSIGNED NOT NULL DEFAULT \'1\',
    `id_shop_group` INT(10) UNSIGNED NOT NULL DEFAULT \'1\',
    `id_lang` INT(10) UNSIGNED NULL DEFAULT \'1\',
    `id_gender` INT(10) UNSIGNED NULL DEFAULT \'0\',
    `firstname` VARCHAR(32) NULL DEFAULT NULL,
    `lastname` VARCHAR(32) NULL DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL,
    `birthday` DATE NULL DEFAULT NULL,
    `ip_registration_newsletter` VARCHAR(15) NULL DEFAULT NULL,
    `list_of_interest` TEXT NULL,
    `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `active` TINYINT(1) NOT NULL DEFAULT \'1\',
    PRIMARY KEY (`id_newsletter_pro_subscribers`),
    INDEX `id_shop` (`id_shop`),
    INDEX `id_lang` (`id_lang`),
    INDEX `id_shop_group` (`id_shop_group`),
    INDEX `id_gender` (`id_gender`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_subscribers_custom_field',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_subscribers_custom_field` (
    `id_newsletter_pro_subscribers_custom_field` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `variable_name` VARCHAR(50) NOT NULL,
    `type` INT(10) NOT NULL,
    `required` INT(10) NOT NULL DEFAULT \'0\',
    PRIMARY KEY (`id_newsletter_pro_subscribers_custom_field`),
    UNIQUE INDEX `variable_name` (`variable_name`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_subscribers_custom_field_lang',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_subscribers_custom_field_lang` (
    `id_newsletter_pro_subscribers_custom_field` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_lang` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
    `value` LONGTEXT NOT NULL,
    PRIMARY KEY (`id_newsletter_pro_subscribers_custom_field`, `id_lang`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_subscribers_temp',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_subscribers_temp` (
    `id_newsletter_pro_subscribers_temp` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_newsletter_pro_subscription_tpl` INT(11) NOT NULL DEFAULT \'0\',
    `load_file` varchar(255) default null,
    `token` VARCHAR(32) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `data` LONGTEXT NOT NULL,
    `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_newsletter_pro_subscribers_temp`, `id_newsletter_pro_subscription_tpl`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_subscription_tpl',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_subscription_tpl` (
    `id_newsletter_pro_subscription_tpl` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `voucher` VARCHAR(255) NULL DEFAULT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT \'0\',
    `display_gender` TINYINT(1) NOT NULL DEFAULT \'0\',
    `display_firstname` TINYINT(1) NOT NULL DEFAULT \'0\',
    `display_lastname` TINYINT(1) NOT NULL DEFAULT \'0\',
    `display_language` TINYINT(1) NOT NULL DEFAULT \'0\',
    `display_birthday` TINYINT(1) NOT NULL DEFAULT \'0\',
    `display_list_of_interest` TINYINT(1) NOT NULL DEFAULT \'0\',
    `list_of_interest_type` TINYINT(1) NOT NULL DEFAULT \'0\',
    `display_subscribe_message` TINYINT(1) NOT NULL DEFAULT \'1\',
    `body_width` VARCHAR(255) NOT NULL DEFAULT \'40%\',
    `body_min_width` INT(11) NOT NULL DEFAULT \'0\',
    `body_max_width` INT(11) NOT NULL DEFAULT \'0\',
    `body_top` INT(11) NOT NULL DEFAULT \'100\',
    `show_on_pages` VARCHAR(255) NOT NULL DEFAULT \'0\',
    `cookie_lifetime` FLOAT NOT NULL DEFAULT \'366\',
    `start_timer` INT(11) NOT NULL DEFAULT \'0\',
    `when_to_show` INT(11) NOT NULL DEFAULT \'0\',
    `allow_multiple_time_subscription` INT(11) NOT NULL DEFAULT \'1\',
    `mandatory_fields` VARCHAR(255) NULL DEFAULT NULL,
    `date_add` DATETIME NULL DEFAULT NULL,
    `css_style` LONGTEXT NULL,
    `terms_and_conditions_url` TEXT NULL,
    `render_loader` int(11) DEFAULT 0,
    PRIMARY KEY (`id_newsletter_pro_subscription_tpl`),
    UNIQUE INDEX `name` (`name`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_subscription_tpl_lang',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_subscription_tpl_lang` (
    `id_newsletter_pro_subscription_tpl` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_lang` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
    `id_shop` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
    `content` LONGTEXT NULL,
    `subscribe_message` LONGTEXT NULL,
    `email_subscribe_voucher_message` LONGTEXT NULL,
    `email_subscribe_confirmation_message` LONGTEXT NULL,
    `email_unsubscribe_confirmation_message` longtext default null,
    PRIMARY KEY (`id_newsletter_pro_subscription_tpl`, `id_lang`, `id_shop`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_subscription_tpl_shop',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_subscription_tpl_shop` (
    `id_newsletter_pro_subscription_tpl` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
    `id_shop` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
    `active` TINYINT(1) NOT NULL DEFAULT \'1\',
    `css_style` LONGTEXT NULL,
    `show_on_pages` VARCHAR(255) NOT NULL DEFAULT \'0\',
    `cookie_lifetime` FLOAT NOT NULL DEFAULT \'366\',
    `start_timer` INT(11) NOT NULL DEFAULT \'0\',
    `when_to_show` INT(11) NOT NULL DEFAULT \'0\',
    `terms_and_conditions_url` TEXT NULL,
    PRIMARY KEY (`id_newsletter_pro_subscription_tpl`, `id_shop`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);

$install->createTable(
    'newsletter_pro_subscription_consent',
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_subscription_consent` (
    `id_newsletter_pro_subscription_consent` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `email` varchar(64) NOT NULL DEFAULT \'\',
    `subscribed` int(2) NOT NULL DEFAULT 0,
    `ip_address` varchar(128) NOT NULL,
    `url` text DEFAULT NULL,
    `http_referer` text DEFAULT NULL,
    `consent_date` datetime DEFAULT NULL,
    `date_add` datetime DEFAULT NULL,
    `date_upd` datetime DEFAULT NULL,
    PRIMARY KEY (`id_newsletter_pro_subscription_consent`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';'
);
