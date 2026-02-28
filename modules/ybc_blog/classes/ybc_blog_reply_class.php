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

if (!defined('_PS_VERSION_'))
    exit;
class Ybc_blog_reply_class extends ObjectModel
{
    public $id_comment;
    public $id_user;
    public $name;
    public $email;
    public $reply;
    public $id_employee;
    public $approved;
    public $datetime_added;
    public $datetime_updated;
    public static $definition = array(
        'table' => 'ybc_blog_reply',
        'primary' => 'id_reply',
        'multilang' => false,
        'fields' => array(
            'id_comment' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'id_user' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 5000),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 5000),
            'reply' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 99000),
            'id_employee' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'approved' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'datetime_added' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'datetime_updated' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_item, $id_lang, $id_shop);
    }
    public static function getTotalRepliesByIDComment($id_comment,$approved = false)
    {
        return (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'ybc_blog_reply` WHERE id_comment='.(int)$id_comment.($approved!==false ?' AND approved='.(int)$approved:'') );
    }
}