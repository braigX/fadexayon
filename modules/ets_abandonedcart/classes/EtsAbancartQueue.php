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


class EtsAbancartQueue extends ObjectModel
{
    public $id_shop;
    public $id_lang;
    public $id_cart;
    public $id_customer;
    public $id_ets_abancart_reminder;
    public $id_ets_abancart_campaign;
    public $customer_name;
    public $email;
    public $subject;
    public $content;
    public $sent;
    public $sending_time;
    public $send_count;
    public $date_add;

    public static $definition = array(
        'table' => 'ets_abancart_email_queue',
        'primary' => 'id_ets_abancart_email_queue',
        'multilang' => true,
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_ets_abancart_reminder' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_ets_abancart_campaign' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'customer_name' => array('type' => self::TYPE_STRING, 'validate' => 'isCustomerName'),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail'),
            'subject' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'content' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'sent' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'sending_time' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'send_count' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    public static function clearAllQueue()
    {
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_abancart_email_queue`');
    }

    public static function deleteQueue($id)
    {
        if (!$id || !Validate::isUnsignedInt($id)) {
            return false;
        }
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_abancart_email_queue` WHERE `id_ets_abancart_email_queue`=' . (int)$id);
    }

    public static function getMailLogs($id)
    {
        if (!$id || !Validate::isUnsignedInt($id)) {
            return false;
        }
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_abancart_mail_log` WHERE `id_ets_abancart_email_queue`=' . (int)$id);
    }
}