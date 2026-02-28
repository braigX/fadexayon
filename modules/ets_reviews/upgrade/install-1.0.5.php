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
function upgrade_module_1_0_5($object)
{
    $free_downloads_disabled = Module::isEnabled('ets_free_downloads') ? 1 : 0;
    Configuration::updateValue('ETS_RV_FREE_DOWNLOADS_ENABLED', $free_downloads_disabled);

    $write_review = [];
    if (Configuration::get('ETS_RV_ALLOW_GUESTS'))
        $write_review[] = 'guest';
    if (Configuration::get('ETS_RV_PURCHASED_PRODUCT'))
        $write_review[] = 'purchased';
    else
        $write_review[] = 'no_purchased';
    if (Configuration::updateValue('ETS_RV_WHO_POST_REVIEW', implode(',', $write_review))) {
        Configuration::deleteByName('ETS_RV_ALLOW_GUESTS');
        Configuration::deleteByName('ETS_RV_PURCHASED_PRODUCT');
    }
    $rating = [];
    if (Configuration::get('ETS_RV_ALLOW_GUESTS_RATE'))
        $rating[] = 'guest';
    if (Configuration::get('ETS_RV_PURCHASED_PRODUCT_RATE'))
        $rating[] = 'purchased';
    else
        $rating[] = ($free_downloads_disabled > 0 ? 'no_purchased_incl' : 'no_purchased');
    if (Configuration::updateValue('ETS_RV_WHO_POST_RATING', implode(',', $rating))) {
        Configuration::deleteByName('ETS_RV_PURCHASED_PRODUCT');
        Configuration::deleteByName('ETS_RV_PURCHASED_PRODUCT_RATE');
    }

    Configuration::updateValue('ETS_RV_RECORDED_ACTIVITIES', 'rev,que,lie,rpt');

    $execCmd = $object->_installConfigs(EtsRVDefines::getInstance()->getAutoConfigs());

    $execCmd &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_rv_email_queue`(
        `id_ets_rv_email_queue` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `guid` varchar(128) NOT NULL,
        `id_lang` INT(11) UNSIGNED NOT NULL,
        `template` varchar(255) NOT NULL,
        `to` varchar(64) DEFAULT NULL,
        `to_email` varchar(255) NOT NULL,
        `to_name` varchar(255) DEFAULT NULL,
        `template_vars` text NOT NULL,
        `subject` varchar(500) NOT NULL,
        `sent` tinyint(1) UNSIGNED DEFAULT 0,
        `sending_time` datetime DEFAULT NULL,
        `send_count` int(1) UNSIGNED NOT NULL DEFAULT 0,
        `schedule_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
        `is_read` tinyint(1) UNSIGNED DEFAULT 0,
        `delivered` tinyint(1) UNSIGNED DEFAULT 0,
        PRIMARY KEY (`id_ets_rv_email_queue`),
        KEY `idx_id_lang` (`id_lang`),
        KEY `idx_sent` (`sent`),
        KEY `idx_to_email` (`to_email`)
    ) ENGINE = ' . _MYSQL_ENGINE_ . ' CHARSET = utf8mb4;');

    $execCmd &= Db::getInstance()->execute('
        ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment` 
            ADD `id_country` INT(11) NOT NULL DEFAULT 0 AFTER `question`, 
            ADD `verified_purchase` varchar(6) NOT NULL AFTER `id_country`;
    ');
    if ($execCmd) {

        $verified_purchase = trim(Configuration::get('ETS_RV_VERIFIED_PURCHASE'));
        if ($verified_purchase !== 'no' && $verified_purchase !== 'yes')
            $verified_purchase = 'auto';

        $execCmd &= Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_rv_product_comment` SET `verified_purchase`=\'' . pSQL($verified_purchase) . '\' WHERE `question` = 0');
        $execCmd &= Configuration::deleteByName('ETS_RV_VERIFIED_PURCHASE');

        $execCmd &= Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'ets_rv_product_comment` pc
            INNER JOIN (
                SELECT `id_customer`, `id_country`
                FROM `' . _DB_PREFIX_ . 'address` WHERE `active` = 1 AND `deleted` < 1 AND `id_customer` > 0
                GROUP BY `id_customer`
            ) a ON (a.`id_customer` = pc.`id_customer`)
            SET pc.`id_country` = a.`id_country`
            WHERE pc.`id_customer` > 0 AND a.`id_customer` > 0 AND pc.`question` = 0
        ');
    }

    $execCmd &= Db::getInstance()->execute('
        ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_cart_rule` ADD `id_customer` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `id_product`;
    ');

    return $execCmd;
}