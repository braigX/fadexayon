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


function upgrade_module_4_4_2()
{
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_email_queue` ADD `id_ets_abancart_campaign` int(11) UNSIGNED NOT NULL AFTER `id_ets_abancart_reminder`;');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_email_queue` ADD INDEX `id_ets_abancart_campaign` (`id_ets_abancart_campaign`)');
    Db::getInstance()->execute('
        UPDATE `' . _DB_PREFIX_ . 'ets_abancart_email_queue`
        INNER JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder` ON `' . _DB_PREFIX_ . 'ets_abancart_reminder`.id_ets_abancart_reminder = `' . _DB_PREFIX_ . 'ets_abancart_email_queue`.`id_ets_abancart_reminder`
        SET `' . _DB_PREFIX_ . 'ets_abancart_email_queue`.`id_ets_abancart_campaign` = `' . _DB_PREFIX_ . 'ets_abancart_reminder`.`id_ets_abancart_campaign`
    ');

    return true;
}