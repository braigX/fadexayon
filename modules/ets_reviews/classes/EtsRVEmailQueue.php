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

class EtsRVEmailQueue extends ObjectModel
{
    public $id_lang;
    public $id_shop;
    public $id_customer;
    public $employee = 0;
    public $template;
    public $to_email;
    public $to_name;
    public $template_vars;
    public $subject;
    public $content;
    public $sent;
    public $sending_time;
    public $send_count;
    public $schedule_time;
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ets_rv_email_queue',
        'primary' => 'id_ets_rv_email_queue',
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'employee' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'template' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'size' => 255),
            'to_email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 255),
            'to_name' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'size' => 255),
            'template_vars' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 65535),
            'subject' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500),
            'content' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 65535),
            'sent' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'sending_time' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'send_count' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'schedule_time' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public static function getNbSentMailQueue($id_ets_rv_email_queue)
    {
        if (!$id_ets_rv_email_queue || !Validate::isUnsignedInt($id_ets_rv_email_queue))
            return false;

        return Db::getInstance()->getValue('SELECT `send_count` FROM `' . _DB_PREFIX_ . 'ets_rv_email_queue` WHERE `id_ets_rv_email_queue`=' . (int)$id_ets_rv_email_queue);
    }

    public static function clear($id_ets_rv_email_queue)
    {
        if (!$id_ets_rv_email_queue || !Validate::isUnsignedInt($id_ets_rv_email_queue))
            return false;

        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_email_queue` WHERE `id_ets_rv_email_queue`=' . (int)$id_ets_rv_email_queue);
    }
}