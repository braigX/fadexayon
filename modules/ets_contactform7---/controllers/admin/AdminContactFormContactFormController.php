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
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/ctf_paggination_class.php');
class AdminContactFormContactFormController extends ModuleAdminController
{
   public function __construct()
   {
       parent::__construct();
       $this->bootstrap = true;
   }
   public function initContent()
   {
        $this->context->controller->addJqueryUI('ui.sortable');
        parent::initContent();
        if(($action = Tools::getValue('action')) && $action=='updateContactFormOrdering' && ($formcontact= Tools::getValue('formcontact')) && Ets_contactform7::validateArray($formcontact))
        {
            $page = (int)Tools::getValue('page',1);
            if(Ets_contact_class::updateContactFormOrdering($formcontact,$page))
            {
                die(
                    json_encode(
                        array(
                            'page'=>$page,
                        )
                    )
                );
            }
        }
   }
   public function renderList()
   {
        $filter='';
        $url_extra='';
        $values_submit= array();
        $use_cache = true;
        if(($id_contact = Tools::getValue('id_contact'))!='')
        {
	        $use_cache = false;
            if(Validate::isCleanHtml($id_contact))
            {
                $filter .=' AND c.id_contact="'.(int)$id_contact.'"';
                $url_extra .='&id_contact='.(int)$id_contact;
                $values_submit['id_contact']=(int)$id_contact;
            }
        }
        if(($contact_title = Tools::getValue('contact_title'))!='')
        {
	        $use_cache = false;
            if(Validate::isCleanHtml($contact_title))
            {
                $filter .=' AND cl.title like "%'.pSQL($contact_title).'%"';
                $url_extra .='&contact_title='.$contact_title;
                $values_submit['contact_title']=$contact_title;
            }
        }
        if(($hook = Tools::getValue('hook'))!='')
        {
	        $use_cache = false;
            if(Validate::isCleanHtml($hook))
            {
                $filter .=' AND c.hook like "%'.pSQL($hook).'%"';
                $url_extra .='&hook='.$hook;
                $values_submit['hook'] = $hook;
            }
        }
        if(($messageFilter_dateadd_from = Tools::getValue('messageFilter_dateadd_from')) && Validate::isDate($messageFilter_dateadd_from))
        {
	        $use_cache = false;
            if(Validate::isCleanHtml($messageFilter_dateadd_from))
            {
                $filter .=' AND c.date_add >="'.pSQL($messageFilter_dateadd_from).'"';
                $url_extra .='&messageFilter_dateadd_from='.$messageFilter_dateadd_from;
                $values_submit['messageFilter_dateadd_from']= $messageFilter_dateadd_from;
            }
        }
        if($messageFilter_dateadd_to = Tools::getValue('messageFilter_dateadd_to'))
        {
	        $use_cache = false;
            if(Validate::isDate($messageFilter_dateadd_to))
            {
                $filter .= ' AND c.date_add <= "'.pSQL($messageFilter_dateadd_to).'"';
                $url_extra .='&messageFilter_dateadd_to='.$messageFilter_dateadd_to;
                $values_submit['messageFilter_dateadd_to']= $messageFilter_dateadd_to;
            }
            
        }
        if(($save_message = Tools::getValue('save_message')) || $save_message!='')
        {
	        $use_cache = false;
            if(Validate::isCleanHtml($save_message)) {
                $filter .= ' AND c.save_message = "' . (int)$save_message . '"';
                $url_extra .= '&save_message=' . $save_message;
                $values_submit['save_message'] = $save_message;
            }
        }
        if(($active_contact = Tools::getValue('active_contact')) || $active_contact!='')
        {
	        $use_cache = false;
            if(Validate::isCleanHtml($active_contact))
            {
                $filter .= ' AND c.active = "'.(int)$active_contact.'"';
                $url_extra .='&active_contact='.(int)$active_contact;
                $values_submit['active_contact']= (int)$active_contact;
            }
            
        }
        $post_sort = Tools::getValue('sort','position'); 
        if($post_sort=='id_contact' || !Validate::isCleanHtml($post_sort))
            $sort='c.id_contact';
        else
            $sort= $post_sort;
        $sort_type= Tools::strtolower(Tools::getValue('sort_type','asc'));
        if($sort_type!='asc' && $sort_type!='desc')
            $sort_type='asc';
		$page = (int)Tools::getValue('page',1);
		$okimport = (int)Tools::getValue('okimport');
		if (Tools::getIsset('okimport') || $sort != 'position' || $sort_type != 'asc' || $page != 1)
			$use_cache = false;
		$cache_id = $this->module->_getCacheId();
	   if (!$use_cache || !$this->module->isCached('list-contact.tpl', $cache_id)) {
		   $total= Ets_contact_class::getContacts(false,$filter,0,0,true);
		   $limit=20;
		   $start= ($page-1)*$limit;
		   $pagination = new Ctf_paggination_class();
		   $pagination->url = $this->context->link->getAdminLink('AdminContactFormContactForm',true).$url_extra.'&page=_page_';
		   $pagination->limit=$limit;
		   $pagination->page= $page;
		   $pagination->total=$total;
		   $contacts= Ets_contact_class::getContacts(false,$filter,$start,$limit,false,$sort,$sort_type);
		   if($contacts)
		   {
			   foreach($contacts as &$contact)
			   {
				   $contact['hooks']= explode(',',$contact['hook']);
				   if($contact['enable_form_page'])
					   $contact['link']= Ets_contactform7::getLinkContactForm($contact['id_contact']);
			   }
		   }
		   $hooks=array(
			   'nav_top'=>$this->l('Header - top navigation'),
			   'header'=>$this->l('Header - main header'),
			   'displayTop'=> $this->l('Top'),
			   'home'=> $this->l('Home'),
			   'left_column'=> $this->l('Left column'),
			   'footer'=> $this->l('Footer page'),
			   'right_column' => $this->l('Right column'),
			   'product_thumbs'=> $this->l('Product page - below product image'),
			   'product_right' => $this->l('Product page - right column'),
			   'product_left' => $this->l('Product page - left column'),
			   'checkout_page'=> $this->l('Checkout page'),
			   'register_page' => $this->l('Register page'),
			   'login_page'=> $this->l('Login page'),
		   );
		   $this->context->smarty->assign(
			   array(
				   'contacts'=>$contacts,
				   'url_module'=> $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->module->name.'&tab_module='.$this->module->tab.'&module_name='.$this->module->name,
				   'pagination_text' => $pagination->render(),
				   'filter'=>$filter,
				   'filter_params'=> $url_extra,
				   'is_ps15' => version_compare(_PS_VERSION_, '1.6', '<')? true: false,
				   'okimport'=> $okimport,
				   'hooks'=>$hooks,
				   'sort'=> $post_sort,
				   'sort_type' => $sort_type,
				   '_PS_JS_DIR_' => _PS_JS_DIR_,
				   'ETS_CTF7_ENABLE_TMCE' => Configuration::get('ETS_CTF7_ENABLE_TMCE'),
				   'show_shorcode_hook' =>Configuration::get('ETS_CTF7_ENABLE_HOOK_SHORTCODE'),
				   'values_submit' => $values_submit
			   )
		   );

	   }
       return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'list-contact.tpl', !$use_cache ? null : $cache_id);
   }
}