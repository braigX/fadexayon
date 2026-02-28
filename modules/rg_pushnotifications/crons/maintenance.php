<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

require_once dirname(__FILE__) . '/../../../config/config.inc.php';
require_once dirname(__FILE__) . '/../rg_pushnotifications.php';

$module = Module::getInstanceByName('rg_pushnotifications');

if (Db::getInstance()->getValue('SELECT `id_module` FROM `' . _DB_PREFIX_ . 'module_shop` WHERE `id_module` = ' . (int) $module->id) &&
    Tools::getValue('token') == $module->secure_key
) {
    RgPuNoNotification::cleanNotifications();
    RgPuNoSubscriber::cleanUnsubscribed();

    die(1);
}

die(0);
