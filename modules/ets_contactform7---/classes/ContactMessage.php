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

class Ets_contact_message_class extends ObjectModel
{
    public $id_contact;
    public $id_customer;
    public $subject;
    public $sender;
    public $body;
    public $recipient;
    public $attachments;
    public $replied;
    public $reply_to;
    public $special;
    public $readed;
    public $date_add;
    public $date_upd;
    /** @var Ets_contactform7 */
    private $module;
    public static $definition = array(
        'table' => 'ets_ctf_contact_message',
        'primary' => 'id_contact_message',
        'fields' => array(
            'id_contact'=>     array('type'=> self::TYPE_INT),
            'id_customer' => array('type'=> self::TYPE_INT),
            'subject'=>     array('type'=> self::TYPE_HTML),
            'sender'=>     array('type'=> self::TYPE_HTML),
            'readed' =>array('type'=> self::TYPE_INT),
            'special' =>array('type'=> self::TYPE_INT),
            'body' => array('type'=>self::TYPE_HTML),
            'recipient' => array('type'=>self::TYPE_HTML),
            'attachments' => array('type'=>self::TYPE_HTML),
            'reply_to' => array('type'=>self::TYPE_HTML),
            'replied'=> array('type'=>self::TYPE_INT),
            'date_add' => array('type'=>self::TYPE_DATE),
            'date_upd' => array('type'=>self::TYPE_DATE),
        ),
    );
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		$this->module = new Ets_contactform7();
		parent::__construct($id_item, $id_lang, $id_shop);
    }
    public function add($autodate = true, $null_values = false)
   	{
		$context = Context::getContext();
		$id_shop = $context->shop->id;
		$res = parent::add($autodate, $null_values);
		if ($res) { // clear related caches
			$this->module->clearCacheForBackEnd();
		}
		$res &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'ets_ctf_contact_message_shop` (`id_shop`, `id_contact_message`)
			VALUES('.(int)$id_shop.', '.(int)$this->id.')'
		);
		return $res;
   	}
    public static function getMessages($filters='',$start=0,$limit=0,$count=false,$orderby='')
    {
        if($count)
        {
            return Db::getInstance()->getValue('
            SELECT COUNT(distinct m.id_contact_message) FROM `'._DB_PREFIX_.'ets_ctf_contact_message` m
            INNER JOIN `'._DB_PREFIX_.'ets_ctf_contact_message_shop` ms ON (m.id_contact_message=ms.id_contact_message)
            lEFT JOIN `'._DB_PREFIX_.'ets_ctf_contact_lang` cl on (m.id_contact=cl.id_contact AND cl.id_lang="'.(int)Context::getContext()->language->id.'")
            LEFT JOIN `'._DB_PREFIX_.'ets_ctf_message_reply` r ON (r.id_contact_message=m.id_contact_message)
            WHERE ms.id_shop="'.(int)Context::getContext()->shop->id.'"'.(string)$filters);
        }
        else
        {
        	$sql = '
            SELECT m.*,cl.title,IF(r.id_ets_ctf_message_reply IS NULL,0,1) AS replied FROM `'._DB_PREFIX_.'ets_ctf_contact_message` m
            INNER JOIN `'._DB_PREFIX_.'ets_ctf_contact_message_shop` ms ON (m.id_contact_message=ms.id_contact_message)
            lEFT JOIN `'._DB_PREFIX_.'ets_ctf_contact_lang` cl on (m.id_contact=cl.id_contact AND cl.id_lang="'.(int)Context::getContext()->language->id.'")
            LEFT JOIN `'._DB_PREFIX_.'ets_ctf_message_reply` r ON (r.id_contact_message=m.id_contact_message)
            WHERE ms.id_shop="'.(int)Context::getContext()->shop->id.'"'.(string)$filters.' GROUP BY m.id_contact_message '.($orderby ? 'ORDER BY '.pSQL($orderby):'').',replied'.($limit? ' LIMIT '.(int)$start.','.(int)$limit:'');
            $messages = Db::getInstance()->executeS($sql);
            if($messages)
            {
                foreach($messages as &$message)
                {
                    $message['attachments'] =$message['attachments']? explode(',',$message['attachments']):array();
                }
            }
            return $messages;
        }

    }
    public static function getCountMessageNoReaed()
    {
        return  Db::getInstance()->getValue('
            SELECT COUNT(distinct m.id_contact_message) FROM `'._DB_PREFIX_.'ets_ctf_contact_message` m
            INNER JOIN `'._DB_PREFIX_.'ets_ctf_contact_message_shop` ms ON (m.id_contact_message=ms.id_contact_message)
            lEFT JOIN `'._DB_PREFIX_.'ets_ctf_contact` c on (m.id_contact=c.id_contact)
            WHERE ms.id_shop="'.(int)Context::getContext()->shop->id.'" AND m.readed=0');
    }
    public function update($null_values = false)
    {
	    $this->module->_clearCache('row-message.tpl', $this->module->_getCacheId($this->id));
	    $this->module->_clearCache('message.tpl', $this->module->_getCacheId($this->id));
	    $this->module->clearCacheForBackEnd();
	    return parent::update($null_values); // TODO: Change the autogenerated stub
    }

	public function delete()
    {
        if(parent::delete())
        {
	        $this->module->_clearCache('row-message.tpl', $this->module->_getCacheId($this->id));
	        $this->module->_clearCache('message.tpl', $this->module->_getCacheId($this->id));
	        $this->module->clearCacheForBackEnd();
            $attachments= Db::getInstance()->getValue('SELECT attachments FROM `'._DB_PREFIX_.'ets_ctf_contact_message` WHERE id_contact_message="'.(int)$this->id.'"');
            if($attachments)
            {
                foreach(explode(',',$attachments) as $attachment)
                {
                    if(file_exists(_PS_ETS_CTF7_UPLOAD_DIR_.$attachment))
                    @unlink(_PS_ETS_CTF7_UPLOAD_DIR_.$attachment);
                }
            }
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_ctf_contact_message_shop` where id_contact_message='.(int)$this->id);
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_ctf_message_reply` WHERE id_contact_message='.(int)$this->id);
            return true;
        }
        return false;
    }
    public function downloadFile($index)
    {
        if($this->attachments)
        {
            $attachments = explode(',',trim($this->attachments));
            if(isset($attachments[$index]) && ($filename = $attachments[$index]) && file_exists(_PS_ETS_CTF7_UPLOAD_DIR_ . $filename))
            {
                $ext = Tools::strtolower(Tools::substr(strrchr($filename, '.'), 1));
                $atts = explode('-',$filename);
                if(count($atts)>1 && array_shift($atts))
                    $name_attachment = implode('-',$atts);
                else
                    $name_attachment = $filename;
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
                header("Content-Disposition: attachment; filename=\"" . $name_attachment . (trim(Tools::strtolower(Tools::substr(strrchr($name_attachment, '.'), 1))) == '' ? '.' . $ext : '') . "\";");
                header("Content-Transfer-Encoding: Binary");
                $file_url = _PS_ETS_CTF7_UPLOAD_DIR_ . $filename;
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
    public static function getMessageById($id_message)
    {
        $message= Db::getInstance()->getRow('
        SELECT m.*,cl.title,c.save_attachments , CONCAT(cu.firstname," ",cu.lastname) as customer_name FROM `'._DB_PREFIX_.'ets_ctf_contact_message` m
        INNER JOIN `'._DB_PREFIX_.'ets_ctf_contact_message_shop` ms ON (m.id_contact_message=ms.id_contact_message)
        LEFT JOIN `'._DB_PREFIX_.'ets_ctf_contact` c ON (c.id_contact=m.id_contact)
        lEFT JOIN `'._DB_PREFIX_.'ets_ctf_contact_lang` cl on (c.id_contact=cl.id_contact AND cl.id_lang="'.(int)Context::getContext()->language->id.'")
        LEFT JOIN `'._DB_PREFIX_.'customer` cu ON (m.id_customer=cu.id_customer)
        WHERE ms.id_shop="'.(int)Context::getContext()->shop->id.'" AND m.id_contact_message='.(int)$id_message);
        if(trim($message['attachments']))
        {
            $attachments = explode(',',trim($message['attachments']));
            $message['attachments'] = array();
            foreach($attachments as $index=> $attachment)
            {
                $atts = explode('-',$attachment);
                if(count($atts)>1 && array_shift($atts))
                    $file_name = implode('-',$atts);
                else
                    $file_name = $attachment;
                $message['attachments'][] = array(
                    'name' => $file_name,
                    'link_download' => Context::getContext()->link->getAdminLink('AdminContactFormMessage').'&downloadFile&id_message='.(int)$message['id_contact_message'].'&index='.$index,
                );
            }

        }
        else
            $message['attachments']='';
        $message['replies'] = self::getRepliesByIdMessage($message['id_contact_message']);
        $message['from_reply'] = Configuration::get('PS_SHOP_NAME').' <'.(Configuration::get('PS_MAIL_METHOD')==2? Configuration::get('PS_MAIL_USER'): Configuration::get('PS_SHOP_EMAIL')).'>';
        $message['reply'] =  Configuration::get('PS_SHOP_NAME').' <'.Configuration::get('PS_SHOP_EMAIL').'>';
        return $message;
    }
    public static function getRepliesByIdMessage($id_message)
    {
        $replies= Db::getInstance()->executeS('
           SELECT * FROM `'._DB_PREFIX_.'ets_ctf_message_reply` 
           WHERE id_contact_message="'.(int)$id_message.'"'
        );
        return $replies;
    }
}