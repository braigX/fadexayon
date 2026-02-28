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

class EtsRVMailLog extends ObjectModel
{
    const SEND_MAIL_DELIVERED = 1;
    const SEND_MAIL_FAILED = 0;
    const SEND_MAIL_TIMEOUT = 2;

    public $id_ets_rv_email_queue;
    public $id_lang;
    public $id_shop;
    public $id_customer;
    public $employee;
    public $template;
    public $to_mail;
    public $to_name;
    public $template_vars;
    public $subject;
    public $content;
    public $sent_time;
    public $status;

    public static $definition = [
        'table' => 'ets_rv_mail_log',
        'primary' => 'id_ets_rv_mail_log',
        'fields' => [
            'id_ets_rv_email_queue' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'employee' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'template' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'to_mail' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 255),
            'to_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'template_vars' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 65000),
            'subject' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500),
            'content' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 65000),
            'sent_time' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        ]
    ];

    public static function writeLog($queue, $status)
    {
        if ((int)Configuration::getGlobalValue('ETS_RV_CRONJOB_MAIL_LOG') < 1)
            return true;
        if (empty($queue) || !in_array($status, [EtsRVMailLog::SEND_MAIL_DELIVERED, EtsRVMailLog::SEND_MAIL_FAILED, EtsRVMailLog::SEND_MAIL_TIMEOUT]))
            return false;
        if ($queue instanceof EtsRVEmailQueue) {
            $queue = (array)$queue;
        }
        $query = '
            INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_mail_log`(`id_ets_rv_email_queue`
                , `id_lang`
                , `id_shop`
                , `id_customer`
                , `employee`
                , `template`
                , `to_email`
                , `to_name`
                , `template_vars`
                , `subject`
                , `content`
                , `sent_time`
                , `status`
            ) VALUES (' . (int)(isset($queue['id']) ? $queue['id'] : $queue['id_ets_rv_email_queue']) . '
                , ' . (int)$queue['id_lang'] . '
                , ' . (int)$queue['id_shop'] . '
                , ' . (int)$queue['id_customer'] . '
                , ' . (int)$queue['employee'] . '
                , "' . pSQL($queue['template']) . '"
                , "' . pSQL($queue['to_email']) . '"
                , "' . pSQL($queue['to_name']) . '"
                , "' . pSQL((is_array($queue['template_vars']) ? json_encode($queue['template_vars']) : $queue['template_vars']), true) . '"
                , "' . pSQL($queue['subject']) . '"
                , "' . pSQL($queue['content']) . '"
                , "' . pSQL(date('Y-m-d H:i:s')) . '"
                , ' . (int)$status . '
            ) ON DUPLICATE KEY UPDATE `sent_time`="' . pSQL(date('Y-m-d H:i:s')) . '", `status` = ' . (int)$status . '
        ';

        return Db::getInstance()->execute($query);
    }

    public static function getLog($id_ets_rv_email_queue)
    {
        if (!$id_ets_rv_email_queue || !Validate::isUnsignedInt($id_ets_rv_email_queue))
            return false;
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_rv_mail_log` WHERE `id_ets_rv_mail_log`=' . (int)$id_ets_rv_email_queue);
    }

    public static function cleanLog()
    {
        return Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'ets_rv_mail_log`');
    }
}