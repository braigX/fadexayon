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

class Ets_contact_class extends ObjectModel
{
    public $title;
    public $short_code;
    public $email_to;
    public $bcc;
    public $bcc2;
    public $email_from;
    public $subject;
    public $additional_headers;
    public $message_body;
    public $exclude_lines;
    public $use_html_content;
    public $file_attachments;
    public $hook;
    public $template_mail;
    public $use_email2;
    public $email_to2;
    public $email_from2;
    public $subject2;
    public $additional_headers2;
    public $message_body2;
    public $exclude_lines2;
    public $use_html_content2;
    public $file_attachments2;
    public $message_mail_sent_ok;
    public $message_mail_sent_ng;
    public $message_validation_error;
    public $message_spam;
    public $message_accept_terms;
    public $message_invalid_required;
    public $message_invalid_too_long;
    public $message_invalid_too_short;
    public $message_invalid_date;
    public $message_date_too_early;
    public $message_date_too_late;
    public $message_upload_failed;
    public $message_upload_file_type_invalid;
    public $message_upload_file_too_large;
    public $message_quiz_answer_not_correct;
    public $message_invalid_email;
    public $message_invalid_url;
    public $message_invalid_tel;
    public $additional_settings;
    public $save_message;
    public $star_message;
    public $save_attachments;
    public $open_form_by_button;
    public $button_label;
    public $id_employee;
    public $date_add;
    public $date_upd;
    public $message_upload_failed_php_error;
    public $message_invalid_number;
    public $message_number_too_small;
    public $message_number_too_large;
    public $message_captcha_not_match;
    public $message_ip_black_list;
    public $title_alias;
    public $meta_title;
    public $meta_keyword;
    public $meta_description;
    public $enable_form_page;
    public $position;
    public $active;

    public $thank_you_active;
    public $thank_you_page;
    public $thank_you_message;
    public $thank_you_url;
    public $thank_you_alias;
    public $thank_you_page_title;

    public $message_email_black_list;

    public $field_form;
    public static $definition = array(
        'table' => 'ets_ctf_contact',
        'primary' => 'id_contact',
        'multilang' => true,
        'fields' => array(
            'field_form' =>  array('type' => self::TYPE_HTML,'lang'=>true),
            'email_to'=>     array('type'=> self::TYPE_HTML),
            'bcc'=>     array('type'=> self::TYPE_HTML),
            'active' => array('type'=>self::TYPE_INT),
            'email_from'=>     array('type'=> self::TYPE_HTML),
            'additional_headers'=>     array('type'=> self::TYPE_HTML),
            'exclude_lines' => array('type'=>self::TYPE_INT),
            'use_html_content'=> array('type'=>self::TYPE_INT),
            'save_message' => array('type'=>self::TYPE_INT),
            'star_message'=>array('type'=>self::TYPE_INT),
            'save_attachments' => array('type'=>self::TYPE_INT),
            'open_form_by_button' => array('type'=>self::TYPE_INT),
            'hook'=>array('type'=>self::TYPE_HTML),
            'button_label' => array('type'=>self::TYPE_HTML,'lang'=>true),
            'id_employee' => array('type'=>self::TYPE_INT),
            'title' =>    array('type' => self::TYPE_HTML,'lang'=>true),
            'short_code' =>  array('type' => self::TYPE_HTML,'lang'=>true),
            'subject' =>    array('type' => self::TYPE_HTML,'lang'=>true),
            'message_body'=> array('type'=> self::TYPE_HTML,'lang'=>true),
            'file_attachments'=> array('type'=> self::TYPE_HTML),
            'use_email2' => array('type'=>self::TYPE_INT),
            'email_to2' => array('type'=>self::TYPE_HTML),
            'bcc2'=>     array('type'=> self::TYPE_HTML),
            'email_from2' => array('type'=>self::TYPE_HTML),
            'subject2'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'additional_headers2'=>array('type'=> self::TYPE_HTML),
            'message_body2' => array('type'=> self::TYPE_HTML,'lang'=>true),
            'exclude_lines2' => array('type'=>self::TYPE_INT),
            'use_html_content2'=> array('type'=>self::TYPE_INT),
            'file_attachments2'=> array('type'=> self::TYPE_HTML),
            'message_mail_sent_ok'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_mail_sent_ng'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_validation_error'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_spam'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_accept_terms'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_invalid_required'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_invalid_too_long'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_invalid_too_short'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_date_too_early'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_date_too_late'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_invalid_date'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_upload_failed'=> array('type'=>self::TYPE_HTML,'lang'=>true), 
            'message_upload_file_type_invalid'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_upload_file_too_large'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_quiz_answer_not_correct'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_invalid_email'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_invalid_url'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_invalid_tel'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'additional_settings'=> array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_upload_failed_php_error'=>array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_invalid_number'=>array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_number_too_small'=>array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_number_too_large'=>array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_captcha_not_match'=>array('type'=>self::TYPE_HTML,'lang'=>true),
            'message_ip_black_list'=>array('type'=>self::TYPE_HTML,'lang'=>true),
            'template_mail'=>array('type'=>self::TYPE_HTML,'lang'=>true),
            'title_alias'=>array('type'=>self::TYPE_HTML,'lang'=>true),
            'meta_title'=>array('type'=>self::TYPE_HTML,'lang'=>true),
            'meta_keyword'=>array('type'=>self::TYPE_HTML,'lang'=>true),
            'meta_description'=>array('type'=>self::TYPE_HTML,'lang'=>true),
            'enable_form_page' => array('type'=>self::TYPE_INT),
            'position' => array('type'=>self::TYPE_INT),
            /*2.0.5*/
            'thank_you_active' => array('type' => self::TYPE_INT),
            'thank_you_page' => array('type' => self::TYPE_HTML),
            'thank_you_message' => array('type' => self::TYPE_HTML, 'lang' => true),
            'thank_you_url' => array('type' => self::TYPE_HTML, 'lang' => true),
            'thank_you_alias' => array('type' => self::TYPE_HTML, 'lang' => true),
            'thank_you_page_title' => array('type' => self::TYPE_HTML, 'lang' => true),
            'message_email_black_list' => array('type' => self::TYPE_HTML, 'lang' => true),
            /*end 2.0.5*/
            'date_add' => array('type'=>self::TYPE_DATE),
            'date_upd' => array('type'=>self::TYPE_DATE),
        ),
    );
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
    }
    public static function getIdContactByAlias($alias, $id_lang = null,$thank_page = false)
    {
        $context = Context::getContext();
        if (!$id_lang) {
            $id_lang = $context->language->id;
        }
        return (int)Db::getInstance()->getValue("
            SELECT c.id_contact FROM `" . _DB_PREFIX_ . "ets_ctf_contact` c
            LEFT JOIN `" . _DB_PREFIX_ . "ets_ctf_contact_lang` cl ON (c.id_contact = cl.id_contact AND cl.id_lang = " . (int)$id_lang . ")
            INNER JOIN `" . _DB_PREFIX_ . "ets_ctf_contact_shop` cs ON (c.id_contact = cs.id_contact AND cs.id_shop = " . (int)$context->shop->id . ")
            WHERE ".($thank_page ? 'cl.thank_you_alias':'cl.title_alias')."  LIKE '%" . pSQL($alias) . "%'
            GROUP BY c.id_contact
        ");
    }

    public function add($autodate = true, $null_values = false)
   	{
		$context = Context::getContext();
		$id_shop = $context->shop->id;
		$res = parent::add($autodate, $null_values);
		$res &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'ets_ctf_contact_shop` (`id_shop`, `id_contact`)
			VALUES('.(int)$id_shop.', '.(int)$this->id.')'
		);
		return $res;
   	}
   	public static function getTotalContact()
    {
        return (int)Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'ets_ctf_contact_shop` where id_shop='.(int)Context::getContext()->shop->id);
    }
    public static function getContactsByHook($hook_name)
    {
        return Db::getInstance()->executeS('
            SELECT c.id_contact FROM `'._DB_PREFIX_.'ets_ctf_contact` c
            INNER JOIN `'._DB_PREFIX_.'ets_ctf_contact_shop` cs ON (c.id_contact= cs.id_contact)
            LEFT JOIN `'._DB_PREFIX_.'ets_ctf_contact_lang` cl on (c.id_contact= cl.id_contact AND cl.id_lang="'.(int)Context::getContext()->language->id.'")
            WHERE c.active=1 AND c.hook like "%'.pSQL($hook_name).'%" AND cs.id_shop="'.(int)Context::getContext()->shop->id.'";
        ');
    }
    public static function getContacts($active=false,$filters='',$start=0,$limit=0,$count=false,$order_by='position',$order_way='asc')
    {
        $sql= 'SELECT '.($count ? 'COUNT(*)':'c.*,cl.*,cs.*,e.firstname,e.lastname').' FROM `'._DB_PREFIX_.'ets_ctf_contact` c
        INNER JOIN `'._DB_PREFIX_.'ets_ctf_contact_shop` cs ON (c.id_contact =cs.id_contact)
        LEFT JOIN `'._DB_PREFIX_.'ets_ctf_contact_lang` cl on (c.id_contact=cl.id_contact AND cl.id_lang="'.(int)Context::getContext()->language->id.'")
        LEFT JOIN `'._DB_PREFIX_.'employee` e on(c.id_employee= e.id_employee)
        WHERE cs.id_shop ="'.(int)Context::getContext()->shop->id.'"'.(string)$filters.($active? ' AND c.active=1':'').' GROUP BY c.id_contact ORDER BY '.pSQL($order_by).' '.pSQL($order_way).' '.($limit? 'LIMIT '.(int)$start.','.(int)$limit:'');
        if($count)
            return Db::getInstance()->getValue($sql);
        else
        {
            $contacts = Db::getInstance()->executeS($sql);
            if($contacts)
            {
                foreach($contacts as &$contact)
                {
                    $contact['count_views'] = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'ets_ctf_log` WHERE id_contact='.(int)$contact['id_contact']);
                    $contact['count_message'] = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'ets_ctf_contact_message` WHERE id_contact='.(int)$contact['id_contact']);
                }
            }
            return $contacts;
        }
    }
    static public function getFieldShortCode($id_contact_form,$id_lang=0){
        $context = Context::getContext();
        $id_lang = $id_lang ? $id_lang : $context->language->id;
        $sql = 'SELECT field_form 
                FROM `'._DB_PREFIX_.'ets_ctf_contact` ct 
                LEFT JOIN `'._DB_PREFIX_.'ets_ctf_contact_lang` ctl ON ( ct.id_contact = ctl.id_contact AND ctl.id_lang = '.(int)$id_lang.')
                lEFT JOIN `'._DB_PREFIX_.'ets_ctf_contact_shop` cts ON (ct.id_contact = cts.id_contact AND cts.id_shop = '.(int)$context->shop->id.')
                WHERE ct.id_contact ='.(int)$id_contact_form.'  ';
        return DB::getInstance()->getValue($sql);
    }
    public static function getListContacts()
    {
        $sql= 'SELECT c.* FROM `'._DB_PREFIX_.'ets_ctf_contact` c
        INNER JOIN `'._DB_PREFIX_.'ets_ctf_contact_shop` cs ON (c.id_contact =cs.id_contact AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")';
        return Db::getInstance()->executeS($sql);
    }
    public static function getContactLanguage($id_contact)
    {
        return Db::getInstance()->executeS('SELECT cl.*,l.iso_code FROM `'._DB_PREFIX_.'ets_ctf_contact_lang` cl,'._DB_PREFIX_.'lang l WHERE cl.id_lang=l.id_lang AND cl.id_contact='.(int)$id_contact);
    }
    public static function deleteAllContact()
    {
        Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."ets_ctf_contact` WHERE id_contact IN (SELECT id_contact FROM `"._DB_PREFIX_."ets_ctf_contact_shop` WHERE id_shop=".(int)Context::getContext()->shop->id.")");
        Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."ets_ctf_contact_lang` WHERE id_contact IN (SELECT id_contact FROM `"._DB_PREFIX_."ets_ctf_contact_shop` WHERE id_shop=".(int)Context::getContext()->shop->id.")");
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_ctf_contact_shop` WHERE id_shop="'.(int)Context::getContext()->shop->id.'"');
        return true;
    }
    public static function existContact($id_contact)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ets_ctf_contact` c, `'._DB_PREFIX_.'ets_ctf_contact_shop` cs WHERE c.id_contact="'.(int)$id_contact.'" AND c.id_contact=cs.id_contact AND cs.id_shop="'.(int)Context::getContext()->shop->id.'"');
    }
    public static function getIdByAlias($title_alias){
        if ( ! $title_alias ){
            return false;
        }
        $sql = 'SELECT ct.`id_contact` 
                FROM `'._DB_PREFIX_.'ets_ctf_contact` ct 
                LEFT JOIN `'._DB_PREFIX_.'ets_ctf_contact_lang` ctl ON (ct.`id_contact` = ctl.`id_contact`) 
                LEFT JOIN `'._DB_PREFIX_.'ets_ctf_contact_shop` cts On (cts.`id_contact` = ct.`id_contact`) 
                WHERE 1 AND ctl.`id_lang`='.(int)Context::getContext()->language->id.' 
                        AND cts.`id_shop`= '.(int)Context::getContext()->shop->id.' 
                         AND ctl.`title_alias` = \''.pSQL($title_alias).'\' ';
        return (int)DB::getInstance()->getValue($sql);
    }
    public static function checkAliasExit($alias,$id_lang,$id_contact= false){
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        return Db::getInstance()->getValue("
            SELECT c.id_contact FROM `" . _DB_PREFIX_ . "ets_ctf_contact` c
            LEFT JOIN `" . _DB_PREFIX_ . "ets_ctf_contact_lang` cl ON (c.id_contact = cl.id_contact AND cl.id_lang = " . (int)$id_lang . ")
            INNER JOIN `" . _DB_PREFIX_ . "ets_ctf_contact_shop` cs ON (c.id_contact = cs.id_contact AND cs.id_shop = " . (int)Context::getContext()->shop->id . ")
            WHERE cl.`thank_you_alias` LIKE '%" . pSQL($alias) . "%' ".($id_contact ? " AND c.`id_contact` != ".(int)$id_contact." ":"")."
            GROUP BY c.id_contact
        ");
    }
    public static function getMaxId()
    {
        $req = 'SELECT max(`id_contact`) as maxid
			FROM `'._DB_PREFIX_.'ets_ctf_contact` tbl';
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);
        return isset($row['maxid']) ? (int)$row['maxid'] : 0;
    }
    public function delete()
    {
        if(parent::delete())
        {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_ctf_contact_shop` WHERE id_contact='.(int)$this->id);
            $contacts = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ets_ctf_contact` c ,'._DB_PREFIX_.'ets_ctf_contact_shop cs  WHERE c.id_contact=cs.id_contact AND  id_shop='.(int)Context::getContext()->shop->id.' order by c.position asc');
            $messages= Db::getInstance()->getValue('SELECT attachments FROM `'._DB_PREFIX_.'ets_ctf_contact_message` WHERE id_contact="'.(int)$this->id.'"');
            if($messages)
            {
                foreach($messages as $message)
                {
                    if($message['attachments'])
                    {
                        foreach(explode(',',$message['attachments']) as $attachment)
                        {
                            if(file_exists(_PS_ETS_CTF7_UPLOAD_DIR_.$attachment))
                                @unlink(_PS_ETS_CTF7_UPLOAD_DIR_.$attachment);
                        }
                    }
                }
            }
            if($contacts)
            foreach($contacts as $key=> $contact)
            {
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_ctf_contact` set position="'.(int)$key.'" WHERE id_contact='.(int)$contact['id_contact']);
            }
            return true;
        }
        return false;
    }
    public static function saveLog($id_contact)
    {
        $ip = Tools::getRemoteAddr();
        /** @var Ets_contactform7 $module */
        $module = Module::getInstanceByName('ets_contactform7');
        $browser = $module->getDevice();
        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_ctf_log` WHERE ip="'.pSQL($ip).'" AND DAY(datetime_added) ="'.pSQL(date('d')).'" AND MONTH(datetime_added) ="'.pSQL(date('m')).'" AND YEAR(datetime_added) ="'.pSQL(date('Y')).'" AND id_contact='.(int)$id_contact))
        {
	        $module->clearCacheForBackEnd();
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_ctf_log`(ip,id_contact,browser,id_customer,datetime_added) VALUES ("'.pSQL($ip).'","'.(int)$id_contact.'","'.pSQL($browser).'","'.(int)Context::getContext()->customer->id.'","'.pSQL(date('Y-m-d h:i:s')).'")');
        }
    }
    public static function updateContactFormOrdering($formcontact,$page)
    {
	    /** @var Ets_contactform7 $module */
	    $module = Module::getInstanceByName('ets_contactform7');
	    $module->clearCacheWhenUpdateOrCreateContactForm();
        foreach($formcontact as $key=> $form)
        {
            $position=$key + ($page-1)*20;
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_ctf_contact` SET position="'.(int)$position.'" WHERE id_contact='.(int)$form);
        }
        return true;
    }
    public static function deleteAllLog()
    {
	    /** @var Ets_contactform7 $module */
	    $module = Module::getInstanceByName('ets_contactform7');
	    $module->_clearCache('list-contact.tpl');
	    $module->_clearCache('statistics.tpl');
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_ctf_log` WHERE id_contact IN (SELECT id_contact FROM `'._DB_PREFIX_.'ets_ctf_contact_shop` WHERE id_shop='.(int)Context::getContext()->shop->id.')');
    }
    public static function getStartYear($id_contact=0)
    {
        return Db::getInstance()->getValue('SELECT MIN(YEAR(date_add)) FROM `'._DB_PREFIX_.'ets_ctf_contact` WHERE 1 '.((int)$id_contact? ' AND id_contact='.(int)$id_contact :''));
    }
    public static function getCountLog(){
        $sql = "SELECT COUNT(distinct l.ip,l.id_contact,l.datetime_added) FROM `"._DB_PREFIX_."ets_ctf_log` l 
        INNER JOIN `"._DB_PREFIX_."ets_ctf_contact` c ON (l.id_contact=c.id_contact)";
        return Db::getInstance()->getValue($sql);
    }
    public static function getLogs($start,$limit)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."ets_ctf_log` l 
        INNER JOIN `"._DB_PREFIX_."ets_ctf_contact` c ON (l.id_contact=c.id_contact)
        LEFT JOIN `"._DB_PREFIX_."ets_ctf_contact_lang` cl ON (c.id_contact=cl.id_contact AND cl.id_lang='".(int)Context::getContext()->language->id."')
        LEFT JOIN `"._DB_PREFIX_."customer` cu ON (l.id_customer=cu.id_customer)
        GROUP BY l.ip,l.id_contact,l.datetime_added ORDER BY l.datetime_added DESC LIMIT ".(int)$start.", ".(int)$limit;
        return Db::getInstance()->executeS($sql);
    }
    public static function getCountMesssage($year='',$month='',$day='',$id_contact=0)
    {
        return Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'ets_ctf_contact_message` m, `'._DB_PREFIX_.'ets_ctf_contact_shop` cs WHERE m.id_contact=cs.id_contact AND cs.id_shop='.(int)Context::getContext()->shop->id.($id_contact ? ' AND cs.id_contact='.(int)$id_contact : '').($year ? ' AND YEAR(m.date_add) ="'.pSQL($year).'"':'').($month ? ' AND MONTH(m.date_add) ="'.pSQL($month).'"':'').($day ? ' AND DAY(m.date_add) ="'.pSQL($day).'"':''));
    }
    public static function getCountView($year='',$month='',$day='',$id_contact=0)
    {
        return Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'ets_ctf_log` l, `'._DB_PREFIX_.'ets_ctf_contact_shop` cs WHERE l.id_contact=cs.id_contact AND cs.id_shop='.(int)Context::getContext()->shop->id.($id_contact ? ' AND cs.id_contact='.(int)$id_contact : '').($year ? ' AND YEAR(l.datetime_added) ="'.pSQL($year).'"':'').($month ? ' AND MONTH(l.datetime_added) ="'.pSQL($month).'"':'').($day ? ' AND DAY(l.datetime_added) ="'.pSQL($day).'"':''));
    }
    public static function getCountReplies($year='',$month='',$day='',$id_contact=0)
    {
        $sql ='SELECT COUNT(*) FROM `'._DB_PREFIX_.'ets_ctf_message_reply` r
        INNER JOIN `'._DB_PREFIX_.'ets_ctf_contact_message` m ON (r.id_contact_message = m.id_contact_message)
        INNER JOIN `'._DB_PREFIX_.'ets_ctf_contact` c ON (c.id_contact=m.id_contact)
        INNER JOIN `'._DB_PREFIX_.'ets_ctf_contact_shop` cs ON (c.id_contact=cs.id_contact AND cs.id_shop='.(int)Context::getContext()->shop->id.')
        WHERE 1'.($id_contact ? ' AND cs.id_contact='.(int)$id_contact : '').($year ? ' AND YEAR(r.date_add) ="'.pSQL($year).'"':'').($month ? ' AND MONTH(r.date_add) ="'.pSQL($month).'"':'').($day ? ' AND DAY(r.date_add) ="'.pSQL($day).'"':'');
        return Db::getInstance()->getValue($sql);
    }
}