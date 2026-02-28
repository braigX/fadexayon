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

class Ets_contact_reply_class extends ObjectModel
{
    public $id_contact_message;
    public $id_employee;
    public $content;
    public $reply_to;
    public $attachment;
    public $attachment_name;
    public $subject;
    public $date_add;
    public $date_upd;
    public static $definition = array(
        'table' => 'ets_ctf_message_reply',
        'primary' => 'id_ets_ctf_message_reply',
        'fields' => array(
            'id_contact_message'=>     array('type'=> self::TYPE_INT),
            'id_employee'=>    array('type'=> self::TYPE_INT),
            'content'=>     array('type'=> self::TYPE_HTML),
            'attachment'=>  array('type'=> self::TYPE_STRING),
            'attachment_name'=>    array('type'=> self::TYPE_STRING),
            'reply_to' => array('type'=> self::TYPE_HTML),
            'subject' => array('type'=> self::TYPE_HTML),
            'date_add' => array('type'=>self::TYPE_DATE),
            'date_upd' => array('type'=>self::TYPE_DATE),
        ),
    );
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
    }
    public static function getCountRepliesByIdMessage($id_message)
    {
        return (int)Db::getInstance()->getValue('SELECT COUNT(distinct id_ets_ctf_message_reply) FROM `'._DB_PREFIX_.'ets_ctf_message_reply` WHERE id_contact_message="'.(int)$id_message.'"');
    }
    public function downloadFile()
    {
        if($this->attachment && file_exists(_PS_ETS_CTF7_UPLOAD_DIR_ .$this->attachment))
        {
                $ext = Tools::strtolower(Tools::substr(strrchr($this->attachment_name, '.'), 1));
                switch ($ext) {
                    case "pdf":
                        $ctype = "application/pdf";
                        break;
                    case "exe":
                        $ctype = "application/octet-stream";
                        break;
                    case "zip":
                        $ctype = "application/zip";
                        break;
                    case "doc":
                    case "docx":
                        $ctype = "application/msword";
                        break;
                    case "xls":
                        $ctype = "application/vnd.ms-excel";
                        break;
                    case "ppt":
                        $ctype = "application/vnd.ms-powerpoint";
                        break;
                    case "gif":
                        $ctype = "image/gif";
                        break;
                    case "png":
                        $ctype = "image/png";
                        break;
                    case "jpeg":
                    case "jpg":
                        $ctype = "image/jpg";
                        break;
                    default:
                        $ctype = "application/force-download";
                }
                header("Pragma: public"); // required
                header("Expires: 0");
                header("X-Robots-Tag: noindex, nofollow", true);
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false); // required for certain browsers
                header("Content-Type: $ctype");
                header("Content-Disposition: attachment; filename=\"" . $this->attachment_name . (trim(Tools::strtolower(Tools::substr(strrchr($this->attachment_name, '.'), 1))) == '' ? '.' . $ext : '') . "\";");
                header("Content-Transfer-Encoding: Binary");
                $file_url = _PS_ETS_CTF7_UPLOAD_DIR_ . $this->attachment;
                if ($fsize = @filesize($file_url)) {
                    header("Content-Length: " . $fsize);
                }
                ob_clean();
                flush();
                readfile($file_url);
                exit();
        }
        return false;
    }
}