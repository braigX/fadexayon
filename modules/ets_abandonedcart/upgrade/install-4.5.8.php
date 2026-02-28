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


/* @var  $object Ets_abandonedcart */
function upgrade_module_4_5_8($object)
{
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_tracking` ADD `id_lang` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `id_ets_abancart_reminder`');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_tracking` ADD INDEX(`id_lang`)');
    Db::getInstance()->execute('
        UPDATE `' . _DB_PREFIX_ . 'ets_abancart_tracking` t
        INNER JOIN `' . _DB_PREFIX_ . 'cart` c ON t.id_cart = c.id_cart
        SET t.id_lang = c.id_lang
    ');
    Db::getInstance()->execute('
        UPDATE `' . _DB_PREFIX_ . 'ets_abancart_tracking` t
        INNER JOIN `' . _DB_PREFIX_ . 'customer` c ON t.id_customer = c.id_customer
        SET t.id_lang = c.id_lang;
    ');
    Db::getInstance()->execute('
        UPDATE `' . _DB_PREFIX_ . 'ets_abancart_tracking` t
        SET t.id_lang = ' . (int)Configuration::get('PS_LANG_DEFAULT') . '
        WHERE t.id_cart is NULL AND t.id_customer is NULL
    ');
    return true;
}