<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber` (
    `id_subscriber` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `id_customer` int(10) unsigned DEFAULT NULL,
    `id_guest` int(10) unsigned DEFAULT NULL,
    `device` varchar(64) NOT NULL,
    `platform` varchar(64) NOT NULL,
    `session_count` int(10) unsigned DEFAULT 0,
    `last_active` datetime NULL,
    `id_player` varchar(64) NOT NULL,
    `unsubscribed` tinyint(1) unsigned DEFAULT 0,
    `from_app` tinyint(1) unsigned DEFAULT 0,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_subscriber`),
    INDEX(`id_customer`),
    INDEX(`id_guest`),
    INDEX(`id_player`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'rg_pushnotifications_campaign` (
    `id_campaign` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(64) NOT NULL,
    `total_notifications` int(10) unsigned DEFAULT NULL,
    `total_delivered` int(10) unsigned DEFAULT NULL,
    `total_unreachable` int(10) unsigned DEFAULT NULL,
    `total_clicked` int(10) unsigned DEFAULT NULL,
    `finished` tinyint(1) unsigned DEFAULT 0,
    `delivery` enum("immediately","intelligent","optimized") NOT NULL,
    `date_start` datetime NOT NULL,
    `date_end` datetime NOT NULL,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_campaign`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'rg_pushnotifications_notification` (
    `id_notification` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `id_campaign` int(10) unsigned DEFAULT NULL,
    `id_onesignal` varchar(64) NOT NULL,
    `id_subscriber` int(10) unsigned NOT NULL,
    `id_cart` int(10) unsigned DEFAULT NULL,
    `title` varchar(64) NOT NULL,
    `notification_type` enum("event","reminder","message") NOT NULL,
    `status` enum("delivered","queued","scheduled","canceled","norecipients") NOT NULL,
    `clicked` tinyint(1) unsigned DEFAULT 0,
    `date_start` datetime NOT NULL,
    `date_end` datetime NOT NULL,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_notification`),
    INDEX(`id_onesignal`),
    INDEX(`id_subscriber`),
    INDEX(`id_cart`),
    INDEX(`id_campaign`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
