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

function upgrade_module_4_6_3()
{
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_discount` ADD `code` VARCHAR(254) NOT NULL AFTER `id_cart_rule`');
    Db::getInstance()->execute('
        UPDATE `' . _DB_PREFIX_ . 'ets_abancart_discount` a
        INNER JOIN `' . _DB_PREFIX_ . 'cart_rule` b ON a.id_cart_rule = b.id_cart_rule
        SET a.code = b.code;
    ');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_email_queue` ADD `time_run` INT(11) UNSIGNED NOT NULL DEFAULT \'0\' AFTER `id_ets_abancart_campaign`');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_email_queue` ADD `id_ets_abancart_tracking` INT(11) UNSIGNED NOT NULL AFTER `id_customer`');
    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_abancart_email_queue` qu
        INNER JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder` ar ON ar.id_ets_abancart_reminder = qu.id_ets_abancart_reminder
        SET `time_run`= (86400 * IFNULL(ar.day, 0) + 3600*IFNULL(ar.hour, 0))
    ');
    Db::getInstance()->execute('
        UPDATE `' . _DB_PREFIX_ . 'ets_abancart_email_queue` q
        JOIN `' . _DB_PREFIX_ . 'ets_abancart_tracking` t
        SET q.id_ets_abancart_tracking = t.id_ets_abancart_tracking
        WHERE q.email = t.email 
        AND q.id_ets_abancart_reminder = t.id_ets_abancart_reminder
        AND IF(t.id_cart is NOT NULL AND t.id_cart > 0, t.id_cart = q.id_cart, IF(t.id_customer is NOT NULL AND t.id_customer > 0, t.id_customer=q.id_customer, 0))
    ');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_email_queue` ADD INDEX (`id_ets_abancart_tracking`)');
    return true;
}