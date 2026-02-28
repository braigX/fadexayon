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
 * @param $object Ets_abandonedcart
 * @return bool
 */
function upgrade_module_4_2_8($object)
{
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_unsubscribers` ADD `date_add` DATETIME NULL DEFAULT NULL AFTER `id_customer`;');
    $id_parent = Tab::getIdFromClassName(Ets_abandonedcart::$slugTab . 'MailConfigs');
    if ($id_parent !== false) {
        $object->_addTab(['id_parent' => $id_parent, 'origin' => 'Unsubscribed list', 'class' => 'UnSubscribed']);
    }

    $object->buildEmailTemplate(_PS_IMG_DIR_ . $object->name . '/mails/');

    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_tracking` ADD `customer_last_visit` DATETIME NULL DEFAULT NULL AFTER `date_upd`;');

    $object->unregisterHook('actionCustomerAccountAdd');

    return true;
}