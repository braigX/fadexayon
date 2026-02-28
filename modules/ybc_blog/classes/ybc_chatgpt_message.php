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
class Ybc_chatgpt_message extends ObjectModel
{
    public static $instance;
    public $is_chatgpt;
    public $message;
    public $field;
    public $date_add;
    public static $definition = array(
        'table' => 'ybc_blog_chatgpt_message',
        'primary' => 'id_ybc_blog_chatgpt_message',
        'fields' => array(
            'is_chatgpt' => array('type' => self::TYPE_INT),
            'message' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'field' => array('type' => self::TYPE_STRING,'validate' =>'isCleanHtml'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );
    public static function getMessages($lastID = false)
    {
        $messages = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ybc_blog_chatgpt_message` '.($lastID ? ' WHERE id_ybc_blog_chatgpt_message <'.(int)$lastID:'').' ORDER BY id_ybc_blog_chatgpt_message DESC LIMIT 0,10');
        if($messages)
        {
            foreach($messages as &$message)
            {
                $message['content'] = Ybc_chatgpt::getInstance()->displayMessage($message['id_ybc_blog_chatgpt_message']);
            }
        }
        return $messages;
    }
}