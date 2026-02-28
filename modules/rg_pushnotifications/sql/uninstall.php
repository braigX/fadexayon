<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

$sql = [];
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'rg_pushnotifications_campaign`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'rg_pushnotifications_notification`';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
