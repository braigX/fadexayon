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
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/ContactReply.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/ctf_paggination_class.php');
class AdminContactFormMessageController extends ModuleAdminController
{
	/** @var Ets_contactform7 */
	public $module;
   public function __construct()
   {
       parent::__construct();
       $this->bootstrap = true;
   }
   public function initContent()
   {
        parent::initContent();
        if(Tools::isSubmit('downloadFile') && ($id_message = (int)Tools::getValue('id_message')) && ($messageObj = new Ets_contact_message_class($id_message)) && Validate::isLoadedObject($messageObj))
        {
            $index = (int)Tools::getValue('index');
            if(!$messageObj->downloadFile($index))
                die($this->l('File Not Found'));
        }
       if(Tools::isSubmit('downloadFileReply') && ($id_reply = (int)Tools::getValue('id_reply')) && ($replyObj = new Ets_contact_reply_class($id_reply)) && Validate::isLoadedObject($replyObj))
       {
           if(!$replyObj->downloadFile())
               die($this->l('File Not Found'));
       }
        if(Tools::isSubmit('submitSpecialActionMessage') && ($id_message=(int)Tools::getValue('id_contact_message')) && ($messageObj = new Ets_contact_message_class($id_message)) && Validate::isLoadedObject($messageObj))
        {
            $messages = array();
            $submitSpecialActionMessage = (int)Tools::getValue('submitSpecialActionMessage');
            $messageObj->special = $submitSpecialActionMessage;
            if($messageObj->update())
            {
                $messages[$id_message] = $this->displayRowMesage($id_message);
                die(
                    json_encode(
                        array(
                            'ok'=>true,
                            'messages'=>$messages,
                        )
                    )
                );
            }
        }
        if(Tools::isSubmit('submitBulkActionMessage'))
        {
            $bulk_action_message = Tools::getValue('bulk_action_message');
            if($bulk_action_message && ($message_readed = Tools::getValue('message_readed')) && Ets_contactform7::validateArray($message_readed))
            {
                if($bulk_action_message=='mark_as_read')
                {
                    $messages = array();
                    foreach(array_keys($message_readed) as $id_message)
                    {
                        if(($messageObj = new Ets_contact_message_class($id_message)) && Validate::isLoadedObject($messageObj))
                        {
                            $messageObj->readed = 1;
                            $messageObj->update();
                            $messages[$id_message] = $this->displayRowMesage($id_message);
                        }
                    }
                    die(
                        json_encode(
                            array(
                                'ok'=>true,
                                'messages'=>$messages,
                                'count_messages' => Ets_contact_message_class::getCountMessageNoReaed(),
                            )
                        )
                    );
                    
                }
                elseif($bulk_action_message=='mark_as_unread')
                {
                    $messages = array();
                    foreach(array_keys($message_readed) as $id_message)
                    {
                        if(($messageObj = new Ets_contact_message_class($id_message)) && Validate::isLoadedObject($messageObj))
                        {
                            $messageObj->readed=0;
                            if($messageObj->update())
                            {
                                $messages[$id_message] = $this->displayRowMesage($id_message);
                            }
                        }
                    }
                    die(
                        json_encode(
                            array(
                                'ok'=>true,
                                'messages'=>$messages,
                                'count_messages' => Ets_contact_message_class::getCountMessageNoReaed(),
                            )
                        )
                    );
                    
                }
                elseif($bulk_action_message=='delete_selected')
                {
                    foreach(array_keys($message_readed) as $id_message)
                    {
                        if(($messageObj = new Ets_contact_message_class($id_message)) && Validate::isLoadedObject($messageObj))
                        {
                            $messageObj->delete();
                        }
                    }
                    die(
                        json_encode(
                            array(
                                'ok'=>true,
                                'url_reload'=>$this->context->link->getAdminLink('AdminContactFormMessage',true).'&conf=1'
                            )
                        )
                    );
                }
            }
        }
        if(Tools::isSubmit('submitReplyMessage') && ($id_message=(int)Tools::getValue('id_message')))
        {
            $reply= new Ets_contact_reply_class();
            $errors = array();
            $reply_to = Tools::getValue('reply_to'); 
            $reply_subject = Tools::getValue('reply_subject');
            $message_reply = Tools::getValue('message_reply');
            $reply_to_reply = Tools::getValue('reply_to_reply');
            $from_reply = Tools::getValue('from_reply');
            if(!$reply_to|| !$reply_subject || !$message_reply)
            {
                $errors[]= $this->l('All fields are required');
            }
            elseif(!Ets_contactform7::getEmailToString($reply_to))
            {
                $errors[] = $this->l('Email is not validate');
            }
            if($reply_subject && !Validate::isCleanHtml($reply_subject))
                $errors[] = $this->l('Subject is not valid');
            if($message_reply && !Validate::isCleanHtml($message_reply))
                $errors[] = $this->l('Message is not valid');
            if($reply_to_reply && !Validate::isCleanHtml($reply_to_reply))
                $errors[] = $this->l('To is not valid');
            if($from_reply && !Validate::isCleanHtml($from_reply))
                $errors[] = $this->l('From is not valid');
            $attachment = '';
            $attachment_name='';
            $attachments = array();
            if(isset($_FILES['attachment']['tmp_name']) && isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'])
            {
                $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
                $_FILES['attachment']['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['attachment']['name']);
                $attachment_name = $_FILES['attachment']['name'];
                $attachment = md5($attachment_name).time();
                if(!Validate::isFileName($_FILES['attachment']['name']))
                    $errors[] = $this->l('Attachemnt is not valid');
                elseif($_FILES['attachment']['size'] > $max_file_size)
                    $errors[] = sprintf($this->l('Attachment file is too large. Limit: %s'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                else
                {
                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES['attachment']['name'], '.'), 1));
                    if (
                        in_array($type, array('zip','jpg', 'gif', 'jpeg', 'png','pdf','doc','docx','txt'))
                    )
                    {
                        if(!is_dir(_PS_ETS_CTF7_UPLOAD_DIR_))
                            @mkdir(_PS_ETS_CTF7_UPLOAD_DIR_,'0755');
                        if (!move_uploaded_file($_FILES['attachment']['tmp_name'], _PS_ETS_CTF7_UPLOAD_DIR_.$attachment))
                            $errors[] = $this->l('Cannot upload the file in');
                        else
                        {
                            $attachment_file = Tools::fileAttachment('attachment',false);
                            $attachment_file['content'] = Tools::file_get_contents(_PS_ETS_CTF7_UPLOAD_DIR_.$attachment);
                            $attachments[] = $attachment_file;
                        }
                    }
                    else
                        $errors[] = $this->l('Attachment is invalid in');
                }
            }
            if($errors)
            {
                die(
                    json_encode(
                        array(
                            'error'=> $this->module->displayError($errors),
                        )
                    )
                );
            }
            else
            {
	            $cache_id = $this->module->_getCacheId($id_message);
	            $this->module->_clearCache('message.tpl', $cache_id);
	            $this->module->_clearCache('list-message.tpl');
	            $this->module->_clearCache('row-message.tpl');
                $id_shop= Context::getContext()->shop->id;
                $link_basic =(Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').Context::getContext()->shop->domain.Context::getContext()->shop->getBaseURI();
                $attr_datas = array(
                    array(
                        'name' => 'width',
                        'value' => '200px',
                    )
                );
                if (Configuration::get('PS_LOGO_MAIL') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL', null, null,$id_shop))) {
                    $logo = Configuration::get('PS_LOGO_MAIL', null, null,$id_shop);
                    $shop_logo = $this->module->displayText('','img','','','','',$link_basic.'/img/'.$logo,Configuration::get('PS_SHOP_NAME'),null,null,null,null,null,$attr_datas);
                } else {
                    if (file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null,$id_shop))) {
                        $logo = Configuration::get('PS_LOGO', null, null,$id_shop);
                        $shop_logo = $this->module->displayText('','img','','','','',$link_basic.'/img/'.$logo,Configuration::get('PS_SHOP_NAME'),null,null,null,null,null,$attr_datas);
                    } else {
                        $shop_logo='';
                    }
                }
                $shop_url=Context::getContext()->link->getPageLink('index', true,Context::getContext()->language->id,null,false,$id_shop);
                $template_email = Configuration::get('ETS_CTF_TEMPLATE_3',Context::getContext()->language->id);
                $template_vars=array(
                    '{message_content}' => Configuration::get('ETS_CTF7_ENABLE_TEAMPLATE')? str_replace(array('{message_content}','{shop_name}','{shop_url}','{shop_logo}','%7Bshop_url%7D','%7Bshop_logo%7D'),array($message_reply,Configuration::get('PS_SHOP_NAME'),$shop_url,$shop_logo,$shop_url,$shop_logo),$template_email): $message_reply,
                );
                $toEmail = Ets_contactform7::getEmailToString($reply_to);
                $toName = str_replace(array('<','>',$toEmail),'',$reply_to);
        		$fromEmail = Ets_contactform7::getEmailToString($from_reply);
                $fromName = str_replace(array('<','>',$fromEmail),'',$from_reply);
                $replyTo = Ets_contactform7::getEmailToString($reply_to_reply);
                $replyToName = trim(str_replace(array('<','>',$replyTo),'',$reply_to_reply));
                if(Mail::Send(
        			Context::getContext()->language->id,
        			Configuration::get('ETS_CTF7_ENABLE_TEAMPLATE') ? 'contact_reply_form7' : 'contact_reply_form7_plain',
        			$reply_subject,
        			$template_vars,
        			$toEmail,
        			$toName ? $toName: null,
                    $fromEmail ? $fromEmail :null,
        			$fromName ? $fromName : null,
                    $attachments,
                    null,
        			dirname(__FILE__).'/../../mails/',
        			null,
        			Context::getContext()->shop->id,
                    null,
                    $replyTo? $replyTo :null,
                    $replyToName ? $replyToName :null
                ))
                {
                     $reply->id_contact_message=$id_message;
                     $reply->content = $message_reply;
                     $reply->id_employee= $this->context->employee->id;
                     $reply->reply_to = $replyTo;
                     $reply->subject = $reply_subject;
                     $reply->attachment = $attachment;
                     $reply->attachment_name  = $attachment_name;
                     $reply->add();
                     die(
                        json_encode(
                            array(
                                'success' => $this->l('Your message has been successfully sent'),
                                'message_reply' => $message_reply,
                                'id_message'=>$id_message,
                                'reply'=>$this->module->displayReplyMessage($reply),
                            ) 
                        )
                     );
                }
                else
                {
                    $errors[] = $this->l('An error occurred while sending the message');
                    die(
                        json_encode(
                            array(
                                'error'=> $this->module->displayError($errors),
                            )  
                        )
                    );
                }
                
            }
        }
        if(Tools::isSubmit('deleteMessage')&& ($id_message =(int)Tools::getValue('id_message')) && ($messageObj = new Ets_contact_message_class($id_message)) && Validate::isLoadedObject($messageObj))
        {
            if($messageObj->delete())
            {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormMessage',true).'&conf=1');
            }
        }
        if(Tools::isSubmit('ajax_ets') && Tools::isSubmit('viewMessage')&& ($id_message=(int)Tools::getValue('id_message')) && ($messageObj = new Ets_contact_message_class($id_message)) && Validate::isLoadedObject($messageObj))
        {
            $message= Ets_contact_message_class::getMessageById($id_message);
            if($message['reply_to'] && Ets_contactform7::getEmailToString($message['reply_to']))
            {
                $message['reply_to_check']= true;
            }
            else
                $message['reply_to_check']=false;
            if($message)
            {
            	if (!$this->module->isCached('message.tpl', $this->module->_getCacheId($id_message))) {
		            $replies = Ets_contact_message_class::getRepliesByIdMessage($id_message);
		            $this->context->smarty->assign(
			            array(
				            'message'=>$message,
				            'replies'=>$replies,
			            )
		            );
	            }
                $message_html= $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'message.tpl', $this->module->_getCacheId($id_message));
                $messages = array();
                $message_readed = (int)Tools::getValue('message_readed');
                if(!$message_readed)
                {
                    $messageObj->readed=1;
                    if($messageObj->update()) {
                    	$this->module->clearCacheForBackEnd();
	                    $messages[$id_message] = $this->displayRowMesage($id_message);
                    }
                }
                die(json_encode(
                    array(
                        'message_html'=>$message_html,
                        'messages'=>$messages,
                        'count_messages' => Ets_contact_message_class::getCountMessageNoReaed(),
                    )
                ));
            }
        }
   }
   public function renderList()
   {
        if(Tools::isSubmit('viewMessage') && $id_message=(int)Tools::getValue('id_message'))
        {
        	$cache_id = $this->module->_getCacheId($id_message);
        	if (!$this->module->isCached('message.tpl', $cache_id)) {
		        $message= Ets_contact_message_class::getMessageById($id_message);
		        if($message['reply_to'] && Ets_contactform7::getEmailToString($message['reply_to']))
		        {
			        $message['reply_to_check']= True;
		        }
		        else
			        $message['reply_to_check']=false;
		        if($message)
		        {
			        $replies = Ets_contact_message_class::getRepliesByIdMessage($id_message);
			        $this->context->smarty->assign(
				        array(
					        'message'=>$message,
					        'replies'=>$replies,
					        'base_url' => $this->module->getBaseLink(),
				        )
			        );
		        }
	        }
	        return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'message.tpl', $cache_id);
        }
        else
        {
        	$use_cache = true;
            $filter='';
            $url_extra='';
            $values_submit= array();
            if(($id_contact = Tools::getValue('id_contact'))!=0)
            {
	            $use_cache = false;
                if(Validate::isCleanHtml($id_contact))
                {
                    $filter .=' AND m.id_contact="'.(int)$id_contact.'"';
                    $url_extra .='&id_contact='.(int)$id_contact;
                    $values_submit['id_contact']= $id_contact;
                }
                
            }
            if(($id_contact_message = Tools::getValue('id_contact_message'))!='')
            {
	            $use_cache = false;
                if(Validate::isCleanHtml($id_contact_message))
                {
                    $filter .=' AND m.id_contact_message="'.(int)$id_contact_message.'"';
                    $url_extra .='&id_contact_message='.(int)$id_contact_message;
                    $values_submit['id_contact_message']=(int)$id_contact_message;
                }
            }
            if(($subject = Tools::getValue('subject'))!='')
            {
	            $use_cache = false;
                if(Validate::isCleanHtml($subject))
                {
                    $filter .=' AND m.subject like "%'.pSQL($subject).'%"';
                    $url_extra .='&subject='.$subject;
                    $values_submit['subject'] = $subject;
                }
                
            }
            if(($sender = Tools::getValue('sender'))!='')
            {
	            $use_cache = false;
                if(Validate::isCleanHtml($sender))
                {
                    $filter .=' AND m.sender like "%'.pSQL($sender).'%"';
                    $url_extra .='&sender='.$sender;
                    $values_submit['sender'] = $sender;
                }
            }
	        $messageFilter_dateadd_from = Tools::getValue('messageFilter_dateadd_from');
            if($messageFilter_dateadd_from!='')
            {
	            $use_cache = false;
                if(Validate::isDate($messageFilter_dateadd_from))
                {
                    $filter .=' AND m.date_add >="'.pSQL($messageFilter_dateadd_from).'"';
                    $url_extra .='&messageFilter_dateadd_from='.$messageFilter_dateadd_from;
                    $values_submit['messageFilter_dateadd_from']=$messageFilter_dateadd_from;
                }

            }
            if(($messageFilter_dateadd_to = Tools::getValue('messageFilter_dateadd_to'))!='')
            {
            	if ($messageFilter_dateadd_from && $messageFilter_dateadd_from == $messageFilter_dateadd_to) {
		            $messageFilter_dateadd_to = date('Y-m-d', strtotime("+1 day", strtotime($messageFilter_dateadd_from)));
	            }
	            $use_cache = false;
                if(Validate::isDate($messageFilter_dateadd_to))
                {
                    $filter .= ' AND m.date_add <= "'.pSQL($messageFilter_dateadd_to).'"';
                    $url_extra .='&messageFilter_dateadd_to='.$messageFilter_dateadd_to;
                    $values_submit['messageFilter_dateadd_to']= $messageFilter_dateadd_to;
                }
            }
            if(($messageFilter_replied = Tools::getValue('messageFilter_replied'))!='')
            {
	            $use_cache = false;
                if(Validate::isCleanHtml($messageFilter_replied))
                {
                    if($messageFilter_replied==0)
                        $filter .=' AND m.id_contact_message NOT IN (SELECT id_contact_message FROM '._DB_PREFIX_.'ets_ctf_message_reply)';
                    else
                        $filter .=' AND m.id_contact_message IN (SELECT id_contact_message FROM '._DB_PREFIX_.'ets_ctf_message_reply)';
                    $url_extra .='&messageFilter_replied='.$messageFilter_replied;
                    $values_submit['messageFilter_replied']=$messageFilter_replied;
                }
                
            }
            if(($messageFilter_message = Tools::getValue('messageFilter_message'))!='' )
            {
	            $use_cache = false;
                if(Validate::isCleanHtml($messageFilter_message))
                {
                    $filter .=' AND m.body like "%'.pSQL($messageFilter_message).'%"';
                    $url_extra .='&messageFilter_message='.$messageFilter_message;
                    $values_submit['messageFilter_message']=$messageFilter_message;
                }
                
            }
            $url_extra_no_order=$url_extra;
            $OrderWay = Tools::strtoupper(Tools::getValue('OrderWay','DESC'));
            if($OrderWay!='ASC' && $OrderWay!='DESC')
                $OrderWay = 'DESC';
            $postOrderBy = Tools::getValue('OrderBy','m.id_contact_message');
            if ($postOrderBy != 'm.id_contact_message' || $OrderWay != 'DESC')
	            $use_cache = false;
            if($postOrderBy!='' && $postOrderBy!='m.id_contact_message')
            {
               $orderBy= $postOrderBy.' '.$OrderWay.',m.id_contact_message DESC';
               $url_extra .= '&OrderBy='.$postOrderBy.'&OrderWay='.$OrderWay;
            }
            else
            {
                $orderBy = $postOrderBy.' '.$OrderWay;
            }
	        if(Tools::isSubmit('submitExportButtonMessage'))
	        {
		        ob_get_clean();
		        ob_start();
		        $messages = Ets_contact_message_class::getMessages($filter);
		        $csv ="Subject\tFrom\tContact Form\tMessage\tDate"."\r\n";
		        foreach($messages as $row) {
			        $message=array();
			        $message[]=$row['subject'];
			        $message[]=$row['sender'];
			        $message[]=$row['title'];
			        $message[]=str_replace("\n",'',strip_tags($row['body']));
			        $message[]=$row['date_add'];
			        $csv .= join("\t", $message)."\r\n";
		        }
		        $csv = chr(255).chr(254).mb_convert_encoding($csv, "UTF-16LE", "UTF-8");
		        header("Content-type: application/x-msdownload");
		        header("Content-disposition: csv; filename=" . date("Y-m-d") .
			        "_message_list.csv; size=".Tools::strlen($csv));
		        echo $csv;
		        exit();
	        }
	        $limit = (int)Tools::getValue('paginator_message_select_limit',20);
	        $page = (int)Tools::getValue('page',1);
	        if ($limit != 20 || $page != 1)
	        	$use_cache = false;
	        $cache_id = $this->module->_getCacheId();
	        if (!$use_cache || !$this->module->isCached('list-message.tpl', $cache_id)) {
		        $totalMessage = Ets_contact_message_class::getMessages($filter,0,0,true,$orderBy);
		        $start= ($page-1)*$limit;
		        $paggination = new Ctf_paggination_class();
		        $paggination->limit =  $limit;
		        $paggination->url = $this->context->link->getAdminLink('AdminContactFormMessage',true).$url_extra.'&page=_page_'.($paggination->limit!=20 ? '&paginator_message_select_limit='.$paggination->limit:'');
		        $paggination->name = 'message';
		        $paggination->page = $page;
		        $paggination->total = $totalMessage;
		        $messages= Ets_contact_message_class::getMessages($filter,$start,$limit,false,$orderBy);
		        if($messages)
		        {
			        foreach($messages as &$message)
			        {
				        $message['replies'] = Ets_contact_message_class::getRepliesByIdMessage($message['id_contact_message']);
				        $message['row_message'] = $this->displayRowMesage($message);
			        }

		        }
		        $contacts= Ets_contact_class::getContacts();
		        $this->context->smarty->assign(
			        array(
				        'messages'=>$messages,
				        'contacts'=>$contacts,
				        'totalMessage' => $totalMessage,
				        'url_module'=> $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->module->name.'&tab_module='.$this->module->tab.'&module_name='.$this->module->name,
				        'link'=>$this->context->link,
				        'base_url' => $this->module->getBaseLink(),
				        'filter'=>$filter,
				        'is_ps15' => version_compare(_PS_VERSION_, '1.6', '<')? true: false,
				        'pagination_text' => $paggination->render(),
				        'values_submit'=>$values_submit,
				        'url_full' => $this->context->link->getAdminLink('AdminContactFormMessage',true).$url_extra_no_order.'&page='.$page.($paggination->limit!=20 ? '&paginator_message_select_limit='.$paggination->limit:''),
				        'orderBy'=>$postOrderBy,
				        'orderWay' => $OrderWay,
			        )
		        );
	        }
           return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'list-message.tpl', !$use_cache ? null : $cache_id);
        }
        return '';
   }
   public function displayRowMesage($message)
   {
   	    $cache_params = is_array($message) ? $message['id_contact_message'] : $message;
   	    if (!$this->module->isCached('row-message.tpl', $this->module->_getCacheId($cache_params))) {
	        if(!is_array($message))
		        $message = Ets_contact_message_class::getMessageById($message);
	        $this->context->smarty->assign(
		        array(
			        'message'=>$message,
		        )
	        );
        }
        return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'row-message.tpl', $this->module->_getCacheId($cache_params));
   }
}