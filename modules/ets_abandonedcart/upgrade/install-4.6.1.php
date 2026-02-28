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

function upgrade_module_4_6_1()
{
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_unsubscribers` DROP PRIMARY KEY;');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_unsubscribers` ADD `id_ets_abancart_unsubscribers` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id_ets_abancart_unsubscribers`);');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_unsubscribers` ADD `email` VARCHAR(191) NOT NULL AFTER `id_customer`;');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_unsubscribers` CHANGE `id_customer` `id_customer` INT(11) UNSIGNED NULL DEFAULT NULL;');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_unsubscribers` ADD UNIQUE `id_customer_email` (`id_customer`, `email`); ');
    return true;
}