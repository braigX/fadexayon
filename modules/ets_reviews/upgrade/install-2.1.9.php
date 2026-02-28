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
 * @return bool
 */
function upgrade_module_2_1_9()
{
    $sqls = [];
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_publish_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_origin_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_criterion` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_criterion_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_criterion_product` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_criterion_category` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_grade` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_usefulness` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_customer` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_image` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_video` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_cart_rule` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_order` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';

    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_comment` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_comment_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_comment_origin_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_comment_usefulness` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';

    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_reply_comment` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_reply_comment_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_reply_comment_origin_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_reply_comment_usefulness` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';


    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_activity` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_email_queue` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_tracking` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_discount` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';

    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_email_template` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_email_template_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_email_template_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_email_template_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';

    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_staff` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_staff_activity` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $sqls[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_mail_log` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';

    $sqls[] = '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_rv_unsubscribe` (
            `id_customer` int(11) unsigned NOT NULL,
            `active` tinyint(1) unsigned NOT NULL DEFAULT 1,
            `date_add` DATETIME NULL DEFAULT NULL,
            PRIMARY KEY (`id_customer`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
    ';

    if ($sqls) {
        foreach ($sqls as $sql) {
            Db::getInstance()->execute($sql);
        }
    }


    return true;
}