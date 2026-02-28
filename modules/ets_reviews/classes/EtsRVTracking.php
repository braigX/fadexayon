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

class EtsRVTracking extends ObjectModel
{
    public $guid;
    public $id_customer;
    public $employee;
    public $id_shop;
    public $product_id;
    public $email;
    public $subject;
    public $is_read;
    public $delivered;
    public $date_add;
    public $date_upd;
    public $product_comment_id;
    public $ip_address;
    public $id_order;
    public $queue_id;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ets_rv_tracking',
        'primary' => 'id_ets_rv_tracking',
        'fields' => array(
            'ip_address' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 64),
            'guid' => array('type' => self::TYPE_STRING, 'validate' => 'isSha1', 'size' => 128),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'employee' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_comment_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'queue_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 255),
            'subject' => array('type' => self::TYPE_STRING, 'validate' => 'isMailSubject', 'size' => 500),
            'is_read' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'delivered' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public static function trackingDiscount($tracking_id, $cart_rule_id)
    {
        if (!$tracking_id || !Validate::isUnsignedInt($tracking_id) || !$cart_rule_id || !Validate::isUnsignedInt($cart_rule_id))
            return true;

        return Db::getInstance()->insert('ets_rv_discount', ['id_ets_rv_tracking' => $tracking_id, 'id_cart_rule' => $cart_rule_id, 'use_same_cart' => (int)Configuration::get('ETS_RV_USE_OTHER_VOUCHER_SAME_CART') ? 1 : 0], true, true, Db::INSERT_IGNORE);
    }

    public static function getTrackingByOrderId($id_order)
    {
        return (int)Db::getInstance()->getValue('SELECT `id_order` FROM `' . _DB_PREFIX_ . 'ets_rv_tracking` WHERE `id_order`=' . (int)$id_order);
    }

    public static function setDelivered($queue_id)
    {
        if (!$queue_id || !Validate::isUnsignedInt($queue_id))
            return false;

        return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_rv_tracking` SET `delivered` = 1, date_upd=\'' . pSQL(date('Y-m-d H:i:s')) . '\' WHERE `queue_id` = ' . (int)$queue_id);
    }

    public static function clearQueueByIdOrder($id_order)
    {
        return Db::getInstance()->execute('
            DELETE qu
            FROM `' . _DB_PREFIX_ . 'ets_rv_email_queue` qu
            INNER JOIN `' . _DB_PREFIX_ . 'ets_rv_tracking` t ON t.`queue_id`=qu.`id_ets_rv_email_queue`
            WHERE t.`id_order`=' . (int)$id_order . '
        ');
    }

    public static function makeIsRead($guid)
    {
        return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_rv_tracking` SET `is_read` = 1, date_upd=\'' . pSQL(date('Y-m-d H:i:s')) . '\' WHERE `guid` = \'' . pSQL($guid) . '\'');
    }
}