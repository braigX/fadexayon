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
 */
function upgrade_module_1_1_3($object)
{
    Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_rv_staff` (
          `id_employee` int(11) UNSIGNED NOT NULL,
          `id_last_activity` int(11) UNSIGNED NOT NULL DEFAULT 0,
          `display_name` varchar(255) COLLATE utf8mb4_bin NOT NULL,
          `avatar` varchar(500) COLLATE utf8mb4_bin NOT NULL,
          `enabled` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`id_employee`),
          KEY `id_last_activity` (`id_last_activity`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
    ');

    EtsRVStaff::initSupperAdmin();

    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_customer` ADD `display_name` VARCHAR(255) NULL AFTER `id_customer`;');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_customer` ADD `is_staff` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `is_block`;');

    $id_parent = Tab::getIdFromClassName(Ets_reviews::TAB_PREFIX);
    if ($id_parent !== false)
        $object->addQuickTab($id_parent, 'Staffs', 'Staffs');

    EtsRVProductCommentCustomer::updateManagitor();
    EtsRVTools::getInstance()->initEmailTemplate();

    Configuration::deleteByName('ETS_RV_EMAIL_TO_ADMIN_RECEIVE');
    Configuration::deleteByName('ETS_RV_MANAGITOR');

    Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_rv_mail_log` (
        `id_ets_rv_mail_log` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `id_ets_rv_email_queue` INT(11) UNSIGNED NOT NULL,
        `id_lang` INT(11) UNSIGNED NOT NULL,
        `id_shop` INT(11) UNSIGNED NOT NULL,
        `id_customer` INT(11) UNSIGNED NOT NULL,
        `employee` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
        `template` varchar(255) NOT NULL,
        `to_email` varchar(255) NOT NULL,
        `to_name` varchar(255) DEFAULT NULL,
        `template_vars` text NOT NULL,
        `subject` varchar(500) NOT NULL,
        `sent_time` datetime DEFAULT NULL,
        `status` tinyint(1) NOT NULL,
        PRIMARY KEY (`id_ets_rv_mail_log`),
        UNIQUE `idx_id_ets_rv_email_queue` (`id_ets_rv_email_queue`) USING BTREE,
        KEY `idx_id_lang` (`id_lang`),
        KEY `idx_id_shop` (`id_shop`),
        KEY `idx_id_customer` (`id_customer`),
        KEY `idx_employee` (`employee`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
    ');

    $id_parent = Tab::getIdFromClassName(Ets_reviews::TAB_PREFIX . 'Email');
    if ($id_parent !== false)
        $object->addQuickTab($id_parent, 'MailLog', 'Mail Log');

    return true;
}