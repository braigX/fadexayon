<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_6_0($module)
{
    if ($id_tab = (int) Tab::getIdFromClassName('AdminRgPushNotifications')) {
        $tab = new Tab($id_tab);
        $tab->delete();
    }

    if ($id_tab = (int) Tab::getIdFromClassName('AdminRgPushNotificationsCampaigns')) {
        $tab = new Tab($id_tab);
        $tab->delete();
    }

    if ($id_tab = (int) Tab::getIdFromClassName('AdminRgPushNotificationsNotifications')) {
        $tab = new Tab($id_tab);
        $tab->delete();
    }

    if ($id_tab = (int) Tab::getIdFromClassName('AdminRgPushNotificationsSubscribers')) {
        $tab = new Tab($id_tab);
        $tab->delete();
    }

    $languages = Language::getLanguages(false);
    $main_tab = new Tab();
    $main_tab->class_name = 'AdminRgPuNo';
    $main_tab->module = $module->name;

    if (version_compare(_PS_VERSION_, '1.7', '>=')) {
        $main_tab->id_parent = (int) Db::getInstance()->getValue('
            SELECT `id_tab` FROM `' . _DB_PREFIX_ . 'tab` WHERE `class_name` = "DEFAULT"
        ');
        $main_tab->icon = 'notifications';
    } else {
        $main_tab->id_parent = 0;
    }

    foreach ($languages as $lang) {
        switch (RgPuNoTools::findLang($lang['iso_code'])) {
            case 'es':
                $main_tab->name[$lang['id_lang']] = 'Notificaciones Push';

                break;
            case 'fr':
                $main_tab->name[$lang['id_lang']] = 'Notifications Push';

                break;
            default:
                $main_tab->name[$lang['id_lang']] = 'Push Notifications';

                break;
        }
    }

    $main_tab->add();

    $tab = new Tab();
    $tab->class_name = 'AdminRgPuNoSubscribers';
    $tab->id_parent = (int) $main_tab->id;
    $tab->module = $module->name;

    foreach ($languages as $lang) {
        switch (RgPuNoTools::findLang($lang['iso_code'])) {
            case 'es':
                $tab->name[$lang['id_lang']] = 'Subscriptores';

                break;
            case 'fr':
                $tab->name[$lang['id_lang']] = 'Abonnés';

                break;
            default:
                $tab->name[$lang['id_lang']] = 'Subscribers';

                break;
        }
    }

    $tab->add();

    $tab = new Tab();
    $tab->class_name = 'AdminRgPuNoNotifications';
    $tab->id_parent = (int) $main_tab->id;
    $tab->module = $module->name;

    foreach ($languages as $lang) {
        switch (RgPuNoTools::findLang($lang['iso_code'])) {
            case 'es':
                $tab->name[$lang['id_lang']] = 'Notificaciones';

                break;
            case 'fr':
                $tab->name[$lang['id_lang']] = 'Notifications';

                break;
            default:
                $tab->name[$lang['id_lang']] = 'Notifications';

                break;
        }
    }

    $tab->add();

    $tab = new Tab();
    $tab->class_name = 'AdminRgPuNoCampaigns';
    $tab->id_parent = (int) $main_tab->id;
    $tab->module = $module->name;

    foreach ($languages as $lang) {
        switch (RgPuNoTools::findLang($lang['iso_code'])) {
            case 'es':
                $tab->name[$lang['id_lang']] = 'Campañas';

                break;
            case 'fr':
                $tab->name[$lang['id_lang']] = 'Campagnes';

                break;
            default:
                $tab->name[$lang['id_lang']] = 'Campaigns';

                break;
        }
    }

    $tab->add();

    Tools::deleteDirectory($module->getLocalPath() . 'views/templates/admin/rg_push_notifications_campaigns', true);

    return Db::getInstance()->execute('
            ALTER TABLE `' . _DB_PREFIX_ . 'rg_pushnotifications_notification`
            ADD INDEX(`id_onesignal`),
            ADD INDEX(`id_subscriber`),
            ADD INDEX(`id_cart`),
            ADD INDEX(`id_campaign`);
        ') &&
        Db::getInstance()->execute('
            ALTER TABLE `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`
            ADD INDEX(`id_customer`),
            ADD INDEX(`id_guest`),
            ADD INDEX(`id_player`);
        ') &&
        Db::getInstance()->update(
            'configuration',
            ['name' => 'RGPUNO_CART_MIN_AMOUNT'],
            '`name` = "RGPN_CART_AMOUNT"'
        ) &&
        Db::getInstance()->update(
            'configuration',
            ['name' => 'RGPUNO_CLEAN_CLEAN'],
            '`name` = "RGPN_CLEAN"'
        ) &&
        Db::getInstance()->update(
            'configuration',
            ['name' => ['type' => 'sql', 'value' => 'REPLACE(`name`, "RGPN_", "RGPUNO_")']],
            '`name` LIKE "RGPN_%"'
        );
}
