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

function upgrade_module_4_6_5()
{
    if (Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_reminder` ADD `priority` INT(11) UNSIGNED NOT NULL DEFAULT \'0\' AFTER `deleted`')) {
        $cps = Db::getInstance()->executeS('SELECT `id_ets_abancart_campaign` FROM `' . _DB_PREFIX_ . 'ets_abancart_campaign`');
        foreach ($cps as $cp) {
            if (!empty($cp['id_ets_abancart_campaign']) && (bool)Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_abancart_reminder` SET `priority`=0 WHERE `id_ets_abancart_campaign`=' . (int)$cp['id_ets_abancart_campaign'])) {
                Db::getInstance()->execute('SET @pos := 0', false);
                Db::getInstance()->execute('
                    UPDATE `' . _DB_PREFIX_ . 'ets_abancart_reminder`
                    SET `priority`=(SELECT @pos := @pos + 1)
                    WHERE  `deleted`=0 AND `id_ets_abancart_campaign`=' . (int)$cp['id_ets_abancart_campaign'] . '
                    ORDER BY `id_ets_abancart_reminder`
                ');
            }
        }
    }

    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_index` ADD `last_campaign_run` DATETIME NULL DEFAULT NULL AFTER `cart_date_add`');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_index` ADD INDEX (`last_campaign_run`)');

    // Update index cart
    if (Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_index` ADD `priority` INT(11) NOT NULL DEFAULT \'0\' AFTER `last_campaign_run`')) {
        Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'ets_abancart_index` i INNER JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder` r ON (r.id_ets_abancart_reminder=i.id_ets_abancart_reminder)
            SET i.`priority`=r.`priority`
        ');
        $res = Db::getInstance()->executeS('SELECT `id_ets_abancart_campaign` FROM `' . _DB_PREFIX_ . 'ets_abancart_index` GROUP BY `id_ets_abancart_campaign` HAVING COUNT(`id_ets_abancart_reminder`) > 1');
        foreach ($res as $id) {
            $priority_min = (int)Db::getInstance()->getValue('SELECT MIN(`priority`) FROM `' . _DB_PREFIX_ . 'ets_abancart_index` WHERE `id_ets_abancart_campaign`=' . (int)$id['id_ets_abancart_campaign']);
            if ($priority_min > 0) {
                Db::getInstance()->execute('
                    DELETE FROM `' . _DB_PREFIX_ . 'ets_abancart_index`
                    WHERE `priority` > ' . (int)$priority_min . ' AND `id_ets_abancart_campaign`=' . (int)$id['id_ets_abancart_campaign'] . '
                ');
            }
        }
    }

    // Update tracking:
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_tracking` ADD `last_campaign_run` DATETIME NULL DEFAULT NULL AFTER `customer_last_visit`');
    $alter_exec = Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_tracking` ADD `id_ets_abancart_campaign` INT UNSIGNED NOT NULL AFTER `id_ets_abancart_reminder`');
    $alter_exec &= Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_tracking` ADD `priority` INT(11) UNSIGNED NOT NULL DEFAULT \'0\' AFTER `last_campaign_run`');
    if ($alter_exec) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_tracking` ADD INDEX (`id_ets_abancart_campaign`)');
        Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'ets_abancart_tracking` t 
            INNER JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder` r ON (r.`id_ets_abancart_reminder`=t.`id_ets_abancart_reminder`)
            SET t.`id_ets_abancart_campaign`=r.`id_ets_abancart_campaign`, t.`priority`=r.`priority`
        ');
    }

    return true;
}