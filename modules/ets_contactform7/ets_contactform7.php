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
require_once(dirname(__FILE__).'/classes/form-tag.php');
require_once(dirname(__FILE__).'/classes/function.php');
require_once(dirname(__FILE__).'/classes/Contact.php');
require_once(dirname(__FILE__).'/classes/contact-form.php');
require_once(dirname(__FILE__).'/classes/form-tags-manager.php');
require_once(dirname(__FILE__).'/classes/submission.php');
require_once(dirname(__FILE__).'/classes/mail.php');
require_once(dirname(__FILE__).'/classes/pipe.php');
require_once(dirname(__FILE__).'/classes/integration.php');
require_once(dirname(__FILE__).'/classes/recaptcha.php');
require_once(dirname(__FILE__).'/classes/validation.php');
require_once(dirname(__FILE__).'/classes/ets_ctf_link_class.php');
require_once(dirname(__FILE__).'/classes/ctf_browser.php');
require_once(dirname(__FILE__).'/classes/ets_ctf_defines.php');
require_once(dirname(__FILE__).'/classes/Contact.php');
require_once(dirname(__FILE__).'/classes/ContactMessage.php');
if (!defined('_ETS_CTF7_CACHE_DIR_'))
    define('_ETS_CTF7_CACHE_DIR_', _PS_CACHE_DIR_ . 'ets_contactform7/');
if (!defined('_PS_ETS_CTF7_UPLOAD_DIR_')) {
    define('_PS_ETS_CTF7_UPLOAD_DIR_', _PS_DOWNLOAD_DIR_.'ets_contactform7/');
}
if (!defined('_PS_ETS_CTF7_UPLOAD_')) {
    define('_PS_ETS_CTF7_UPLOAD_', __PS_BASE_URI__.'download/ets_contactform7/');
}
class Ets_contactform7 extends Module
{
    public $_html;
    public $_errors=array();
    public $_ps17;
    public $_path_module;

    public $rm_hook_shortcode = false;

    public function __construct()
	{
		$this->name = 'ets_contactform7';
		$this->tab = 'front_office_features';
		$this->version = '2.2.6';
		$this->author = 'PrestaHero';
		$this->need_instance = 0;
		$this->bootstrap = true;
        $this->module_key = '0cc001cd32aeba4622dec23df91ab813';
		$this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $this->_ps17=true; 
		parent::__construct();
        $this->context = Context::getContext();
        $this->url_module = $this->_path;
        $this->displayName = $this->l('Contact Form 7');
		$this->description = $this->l('The most famous contact form plugin that will help you create any kinds of contact form using contact form editor');
$this->refs = 'https://prestahero.com/';
        $this->_path_module = $this->_path;
    }
    public function getContent()
    {
        $action = Tools::getValue('action');
        if($action=='getCountMessageContactForm7')
        {
            die(
                json_encode(
                    array(
                        'count' => Ets_contact_message_class::getCountMessageNoReaed(),
                    )
                )
            );
        }
        if(version_compare(_PS_VERSION_, '1.6', '<'))
            $this->context->controller->addJqueryUI('ui.widget');
        else
            $this->context->controller->addJqueryPlugin('widget');
        $this->context->controller->addJqueryPlugin('tagify');
        
        if(Tools::isSubmit('exportContactForm'))
            $this->generateArchive();
        if(Tools::isSubmit('getFormElementAjax'))
        {
            $short_code = Tools::getValue('short_code');
            die(json_encode(
                array(
                    'form_html'=>$this->replace_all_form_tags(Validate::isCleanHtml($short_code,true) ? $short_code :''),
                )
            ));
        }
        if(Tools::isSubmit('contactform7default'))
        {
            $id_contact = (int)Tools::getValue('id_contact');
            if((int)Tools::getValue('contactform7default')==1 && $id_contact)
            {
                Configuration::updateValue('ETS_CONTACTFORM7_DEFAULT',$id_contact); 
            }elseif((int)Tools::getValue('contactform7default')==0 && Configuration::get('ETS_CONTACTFORM7_DEFAULT')==$id_contact)
            {
                Configuration::updateValue('ETS_CONTACTFORM7_DEFAULT',0); 
            }
        }
        elseif(Tools::isSubmit('save_message_update') && ($id_contact =(int)Tools::getValue('id_contact')) && ($contact = new Ets_contact_class($id_contact)) && Validate::isLoadedObject($contact) )
        {
        	$this->clearCacheWhenUpdateOrCreateContactForm($id_contact);
            $save_message_update = (int)Tools::getValue('save_message_update');
            $contact->save_message = $save_message_update;
            $contact->update();
        }
        elseif(Tools::isSubmit('submitSaveContact') || Tools::isSubmit('submitSaveAndStayContact'))
        {
            $this->_html .= $this->saveContactForm();
        }
        elseif(Tools::isSubmit('duplicatecontact') && ($id_contact=(int)Tools::getValue('id_contact')) )
        {
	        $this->clearCacheWhenUpdateOrCreateContactForm($id_contact);
            $contact= new Ets_contact_class($id_contact);
            unset($contact->id);
            $languages= Language::getLanguages(false);
            $identity = Tools::passwdGen(2, 'NUMERIC');
            foreach($languages as $language)
            {
                $contact->title[$language['id_lang']] = $contact->title[$language['id_lang']].' ['.$this->l('duplicated').']';
                $contact->title_alias[$language['id_lang']] = $contact->title_alias[$language['id_lang']] . '-' . $identity;
            }
            $contact->position = Ets_contact_class::getTotalContact();
            $contact->add();
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormContactForm', true).'&conf=19');
        }
        elseif(Tools::isSubmit('deletecontact') && ($id_contact= (int)Tools::getValue('id_contact')))
        {
	        $this->clearCacheWhenUpdateOrCreateContactForm($id_contact);
            $contact= new Ets_contact_class($id_contact);
            $contact->delete();
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&conf=1');
        }
        elseif(Tools::isSubmit('preview') && ($id_contact= (int)Tools::getValue('id_contact')))
        {
            $contact= new Ets_contact_class($id_contact,$this->context->language->id);
            die(json_encode(
                array(
                    'form_html'=>$this->replace_all_form_tags($contact->short_code),
                    'contact'=>$contact,
                )
            ));
        }
        elseif(Tools::isSubmit('active_update') && ($id_contact= (int)Tools::getValue('id_contact')) && ($contact = new Ets_contact_class($id_contact)) && Validate::isLoadedObject($contact) )
        {
	        $this->_clearCache('contact-form.tpl', $this->_getCacheId($id_contact));
	        $this->_clearCache('list-contact.tpl');
            $active_update = (int)Tools::getValue('active_update');
            $contact->active = $active_update;
            $contact->update();
        }
        if(Tools::isSubmit('editContact') || Tools::isSubmit('addContact'))
        {
            $this->context->smarty->assign(
                array(
                    'link'=> $this->context->link,
                    'link_basic' => $this->getBaseLink(),
                    'link_doc' => $this->_path.'help/index.html',
                    'ETS_CTF7_ENABLE_TMCE' => Configuration::get('ETS_CTF7_ENABLE_TMCE'),
                    'show_shorcode_hook' =>Configuration::get('ETS_CTF7_ENABLE_HOOK_SHORTCODE')
                )
            );
            $this->_html .= $this->renderAddContactForm();
            $this->_html .= $this->display(__FILE__,'url.tpl');
            $this->_html .= $this->display(__FILE__,'textarea.tpl');
            $this->_html .= $this->display(__FILE__,'text.tpl');
            $this->_html .= $this->display(__FILE__,'telephone.tpl');
            $this->_html .= $this->display(__FILE__,'submit.tpl');
            $this->_html .= $this->display(__FILE__,'select.tpl');
            $this->_html .= $this->display(__FILE__,'radio.tpl');
            $this->_html .= $this->display(__FILE__,'quiz.tpl');
            $this->_html .= $this->display(__FILE__,'number.tpl');
            $this->_html .= $this->display(__FILE__,'hidden.tpl');
            $this->_html .= $this->display(__FILE__,'email.tpl');
            $this->_html .= $this->display(__FILE__,'password.tpl');
            $this->_html .= $this->display(__FILE__,'checkbox.tpl');
            $this->_html .= $this->display(__FILE__,'captcha.tpl');
            $this->_html .= $this->display(__FILE__,'recaptcha.tpl');
            $this->_html .= $this->display(__FILE__,'acceptance.tpl');
            $this->_html .= $this->display(__FILE__,'date.tpl');   
            $this->_html .= $this->display(__FILE__,'file.tpl');
        }
        else
        {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormContactForm',true));
        }
        $okimport = Tools::getValue('okimport');
        $this->context->smarty->assign(
            array(
                'html_content' => $this->_html,
                'okimport' => $okimport && Validate::isCleanHtml($okimport),
                '_PS_JS_DIR_' =>_PS_JS_DIR_,
                'ETS_CTF7_ENABLE_TMCE' => Configuration::get('ETS_CTF7_ENABLE_TMCE'),

            )
        );
        return $this->display(__FILE__,'admin.tpl');
    }
    public function install()
	{
        return parent::install()&& $this->_registerHook() && Ets_ctf_defines::installDb() && Ets_ctf_defines::createIndex() && $this->_installDbConfig() && $this->_installTabs()&& $this->createTemplateMail();
    }
    public function _registerHook()
    {
    	$hooks = Ets_ctf_defines::getListHooks();
        foreach($hooks as $hook)
        {
            $this->registerHook($hook);
        }
        return true;
    }
    public function uninstall()
	{
        return parent::uninstall() && $this->_unInstallDbConfig() &&Ets_ctf_defines::uninstallDb() && $this->_uninstallTabs();
    } 
    private function _installTabs()
    {
        $languages = Language::getLanguages(false);
        $tab = new Tab();
        $tab->class_name = 'AdminContactForm';
        $tab->module = $this->name;
        $tab->id_parent = 0;            
        foreach($languages as $lang){
            $tab->name[$lang['id_lang']] = $this->l('Contact');
        }
        $tab->save();
        if($tabId = $tab->id)
        {
            $subTabs = Ets_ctf_defines::getSubTabs();
            
            foreach($subTabs as $tabArg)
            {
                $tab = new Tab();
                $tab->class_name = $tabArg['class_name'];
                $tab->module = $this->name;
                $tab->id_parent = $tabId; 
                $tab->icon=$tabArg['icon'];           
                foreach($languages as $lang){
                        $tab->name[$lang['id_lang']] = $tabArg['tab_name'];
                }
                $tab->save();
            }                
        }            
        return true;
    }
    public function createTemplateMail(){
        $languages= Language::getLanguages(false);
        $this->enable(true);
        foreach($languages as $language)
        {
            if (!file_exists(dirname(__FILE__).'/mails/'.$language['iso_code'])) {
                mkdir(dirname(__FILE__).'/mails/'.$language['iso_code'], 0755, true);
                Tools::copy(dirname(__FILE__).'/mails/en/contact_form7.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/contact_form7.html');
                Tools::copy(dirname(__FILE__).'/mails/en/contact_form7.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/contact_form7.txt');
                Tools::copy(dirname(__FILE__).'/mails/en/ncontact_reply_form7.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/contact_reply_form7.html');
                Tools::copy(dirname(__FILE__).'/mails/en/contact_reply_form7.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/contact_reply_form7.txt');
                Tools::copy(dirname(__FILE__).'/mails/en/contact_form_7_plain.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/contact_form_7_plain.txt');
                Tools::copy(dirname(__FILE__).'/mails/en/contact_form_7_plain.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/contact_form_7_plain.html');
                Tools::copy(dirname(__FILE__).'/mails/en/contact_reply_form7_plain.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/contact_reply_form7_plain.txt');
                Tools::copy(dirname(__FILE__).'/mails/en/contact_reply_form7_plain.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/contact_reply_form7_plain.html');
                Tools::copy(dirname(__FILE__).'/mails/en/index.php',dirname(__FILE__).'/mails/'.$language['iso_code'].'/index.php');  
            }
        }
        return true;
    }
    private function _uninstallTabs()
    {
        $tabs = array('AdminContactFormContactForm','AdminContactFormMessage','AdminContactFormIntegration','AdminContactFormHelp','AdminContactFormImportExport','AdminContactFormEmail','AdminContactForm','AdminContactFormStatistics');
        if($tabs)
        foreach($tabs as $classname)
        {
            if($tabId = Tab::getIdFromClassName($classname))
            {
                $tab = new Tab($tabId);
                if($tab)
                    $tab->delete();
            }                
        }
        return true;
    }
    public function hookDisplayHeader() 
    {
    	$this->clearCacheWhenNeed();
        if(!(int)Configuration::get('ETS_CTF7_ENABLE_HOOK_SHORTCODE') && ($controller = Dispatcher::getInstance()->getController($this->context->shop->id)) && $controller !='contact'){
            $this->rm_hook_shortcode = true;
        }

        if($this->rm_hook_shortcode){
            return '';
        }
        $this->context->controller->addCSS($this->_path.'views/css/date.css','all');
        $this->context->controller->addCSS($this->_path.'views/css/style.css','all');

        $this->context->controller->addJqueryUI('datepicker');
        if (method_exists($this->context->controller, 'registerJavascript')) {
            $jquery_ui_timepicker_addon_js = 'js/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js';
            $this->context->controller->registerJavascript(sha1($jquery_ui_timepicker_addon_js), $jquery_ui_timepicker_addon_js, ['position' => 'bottom', 'priority' => 800]);
        } else {
            $this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');
        }
        $this->context->controller->registerStylesheet('jquery-ui-timepicker', '/js/jquery/plugins/timepicker/jquery-ui-timepicker-addon.css', ['media' => 'all', 'priority' => 90]);
        $this->context->controller->addJS($this->_path.'views/js/scripts.js');

        if(version_compare(_PS_VERSION_, '1.6', '<'))
            $this->context->controller->addCSS($this->_path.'views/css/style15.css','all');
        if(version_compare(_PS_VERSION_, '1.7', '<') && version_compare(_PS_VERSION_, '1.5', '>'))
                $this->context->controller->addCSS($this->_path.'views/css/style16.css','all');
        if(Configuration::get('ETS_CTF7_ENABLE_TMCE'))
        {
            $this->context->controller->addJS($this->_path.'views/js/tinymce/tinymce.min.js');            
        }

	    $recaptcha = WPCF7_RECAPTCHA::get_instance();
		if ($recaptcha->isEnableRecaptcha())
	        $this->context->controller->addJS($this->_path . 'views/js/recaptcha.js');
		if (!$this->isCached('header.tpl', $this->_getCacheId())) {
			if ($recaptcha->isEnableRecaptcha()) {
				$this->context->smarty->assign(
					array(
						'rc_enabled'=>true,
						'rc_v3'=>$recaptcha->getCaptchaType() == 'v3',
						'rc_key'=>$recaptcha->get_key(),
						'iso_code'=>$this->context->language->iso_code
					)
				);
			}
			$this->context->smarty->assign(
				array(
					'url_basic'=> $this->getBaseLink(),
					'link_contact_ets' => $this->context->link->getModuleLink('ets_contactform7','contact'),
				)
			);
		}
        return $this->display(__FILE__,'header.tpl', $this->_getCacheId()).$this->getContactFormByHook('header');
    }
    public function renderFormConfig()
    {
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id =0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'btnSubmit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminContactFormIntegration',false);
		$helper->token = Tools::getAdminTokenLite('AdminContactFormIntegration');
		$helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),        
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
            'image_baseurl' => $this->_path.'views/img/',
            'page'=>'integration',
            'name_controller'=>'integration',
            'link_basic'=> $this->getBaseLink(),
            'link_doc' => $this->_path.'help/index.html',
            'ps15'=> version_compare(_PS_VERSION_, '1.6', '<') ? true: false,
		);
        $helper->module = $this;                
		return $helper->generateForm(array(Ets_ctf_defines::getInstance()->getFieldConfig('config_fields')));
	}
    public function renderFormEmail()
    {
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'btnSubmit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminContactFormEmail',false);
		$helper->token = Tools::getAdminTokenLite('AdminContactFormEmail');
		$helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),        
			'fields_value' => $this->getEmailFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
            'image_baseurl' => $this->_path.'views/img/',
            'page'=>'email',
            'name_controller'=>'email',
            'link_basic'=> $this->getBaseLink(),
            'link_doc' => $this->_path.'help/index.html',
            'ps15'=> version_compare(_PS_VERSION_, '1.6', '<') ? true: false,            
		);
        $helper->module = $this;
		return $helper->generateForm(array(Ets_ctf_defines::getInstance()->getFieldConfig('email_fields',true)));
	}
    public function getEmailFieldsValues()
    {
        $fields_config= Ets_ctf_defines::getInstance()->getFieldConfig('email_fields',false);
        $inputs = $fields_config['form']['input'];
        $languages= Language::getLanguages(false);
        $fields=array();
        if($inputs)
        {
            foreach($inputs as $input)
            {
                $key= $input['name'];
                if(isset($input['lang']) && $input['lang'])
                {
                    foreach($languages as $language)
                    {
                        $fields[$key][$language['id_lang']] = Tools::getValue($key.'_'.$language['id_lang'],Configuration::get($key,$language['id_lang']));
                    }
                }
                else
                    $fields[$key] = Tools::getValue($key,Configuration::get($key));
            }
        }
        return $fields;
    }
	public function getConfigFieldsValues()
	{
	   $fields_config = Ets_ctf_defines::getInstance()->getFieldConfig('config_fields');
	   $inputs = $fields_config['form']['input'];
       $languages= Language::getLanguages(false);
       $fields=array();
       if($inputs)
       {
            foreach($inputs as $input)
            {
                $key= $input['name'];
                if(isset($input['lang']) && $input['lang'])
                {
                    foreach($languages as $language)
                    {
                        $fields[$key][$language['id_lang']] = Tools::getValue($key.'_'.$language['id_lang'],Configuration::get($key,$language['id_lang']));
                    }
                }
                else
                    $fields[$key] = Tools::getValue($key,Configuration::get($key));
            }
       }
       return $fields;
	}
    public function renderAddContactForm()
	{
        $id_contact = (int)Tools::getValue('id_contact');
        $contact_fields = Ets_ctf_defines::getInstance()->getFieldConfig('contact_fields',true,$id_contact);
        if (Tools::isSubmit('id_contact'))
		{
			$contact_fields['form']['input'][] = array('type' => 'hidden', 'name' => 'id_contact');
            $contact_fields['form']['legend']['new']= $this->context->link->getAdminLink('AdminModules').'&configure=ets_contactform7&tab_module=front_office_features&module_name=ets_contactform7&addContact=1';
		}
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitSaveContact';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.(Tools::isSubmit('editContact') && $id_contact ? '&editContact=1&id_contact='.(int)$id_contact: '&addContact=1');
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getAddContactFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'image_baseurl' => $this->_path.'views/img/',
            'page'=>'contact',
            'link_basic'=> $this->getBaseLink(),
            'link_doc' => $this->_path.'help/index.html',
            'name_controller'=>'edit_contact_form',
            'ps15'=> version_compare(_PS_VERSION_, '1.6', '<') ? true: false,
		);
		$helper->override_folder = '/';
        return $helper->generateForm(array($contact_fields));	
	}
    public function getAddContactFieldsValues()
    {
        $fields = array();
        $id_contact=(int)Tools::getValue('id_contact');
        $languages=Language::getLanguages(true);
        $contact_fields = Ets_ctf_defines::getInstance()->getFieldConfig('contact_fields',true,$id_contact);
        $inputs = $contact_fields['form']['input'];
		if($id_contact)
		{
		    $inputs[] = array('type' => 'hidden', 'name' => 'id_contact');
			$contact= new Ets_contact_class($id_contact);
            if($inputs)
            {
                $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
                foreach($inputs as $input)
                {
                    $key= $input['name'];
                    if(isset($input['lang']) && $input['lang'])
                    {                    
                        foreach($languages as $l)
                        {
                            $temp = $contact->$key;
                            $fields[$key][$l['id_lang']] = Tools::getValue($key.'_'.$l['id_lang'],isset($temp[$l['id_lang']]) ? $temp[$l['id_lang']] : $temp[$default_lang]);
                        }
                    }
                    elseif($input['name']=='id_contact')
                    {
                        $fields['id_contact']= $id_contact;
                        $fields['link_contact'] = $contact->enable_form_page? Ets_contactform7::getLinkContactForm($id_contact):'';
                    }
                    elseif($input['type']=='checkbox')
                    {
                        $values=Tools::getValue($key, explode(',',$contact->$key));
                        if($values)
                        {
                            foreach($values as $value)
                            {
                                $fields[$key.'_'.$value] =1 ;
                            }
                        }
                        
                    }
                    elseif($input['type']=='select' && isset($input['multiple'])&&$input['multiple'])
                    {
                        $fields[$key.'[]']= Tools::getValue($key, explode(',',$contact->$key));
                    }
                    elseif(!isset($input['tree'])&& $input['type']!='checkbox')
                        $fields[$key] = Tools::getValue($key,$contact->$key);
                }
            }
		}
		else
        {
            $contact= new Ets_contact_class();
            foreach($inputs as $input)
            {
                $key=$input['name'];
                if(isset($input['lang']) && $input['lang'])
                {                    
                    foreach($languages as $l)
                    {
                        $temp = $contact->$key;
                        $fields[$key][$l['id_lang']] = isset($input['default'])&&$input['default'] ? Tools::getValue($key.'_'.$l['id_lang'],$input['default']):Tools::getValue($key.'_'.$l['id_lang']);
                    }
                }
                elseif($input['name']=='id_contact')
                {
                    $fields['id_contact'] = 0;
                }
                elseif($input['type']=='checkbox')
                {
                    $values = isset($input['default'])&&$input['default'] ? Tools::getValue($key,explode(',',$input['default'])):Tools::getValue($key);
                    if($values)
                    {
                        foreach($values as $value)
                        {
                            $fields[$key.'_'.$value] =1 ;
                        }
                    }
                }
                elseif($input['type']=='select' && isset($input['multiple'])&&$input['multiple'])
                {
                    $fields[$key.'[]']= Tools::getValue($key);
                }
                elseif(!isset($input['tree'])&& $input['type']!='checkbox')
                    $fields[$key] = isset($input['default'])&&$input['default'] ? Tools::getValue($key,$input['default']):Tools::getValue($key);
                
            }
        }
		return $fields;
    }
    public function hookDisplayBackOfficeHeader()
    {
    	$this->clearCacheWhenNeed(true);
        $controller = Tools::getValue('controller');
        $configure = Tools::getValue('configure');
        if($configure==$this->name && $controller=='AdminModules')
        {
            $this->context->controller->addCSS((__PS_BASE_URI__).'modules/'.$this->name.'/views/css/contact_form7_admin.css','all');
            if(version_compare(_PS_VERSION_, '1.6', '<')){
                $this->context->controller->addCSS((__PS_BASE_URI__).'modules/'.$this->name.'/views/css/contact_form7_admin15.css','all');
                if (!$this->isCached('header_admin.tpl', $this->_getCacheId())) {
	                $this->context->smarty->assign(
		                array(
			                'is_15' =>true
		                )
	                );
                }
            }
        }
        $this->context->controller->addCSS((__PS_BASE_URI__).'modules/'.$this->name.'/views/css/contact_form7_admin_all.css','all');
        
        if($controller=='AdminContactFormStatistics' || $controller=='AdminContactFormEmail' || $controller =='AdminContactFormImportExport'|| $controller=='AdminContactFormContactForm'|| $controller=='AdminContactFormMessage'|| $controller=='AdminContactFormIntegration'|| $controller=='AdminContactFormHelp')
        {
            $this->context->controller->addCSS((__PS_BASE_URI__).'modules/'.$this->name.'/views/css/contact_form7_admin.css','all');
             if(version_compare(_PS_VERSION_, '1.7', '<') && version_compare(_PS_VERSION_, '1.5', '>'))
                $this->context->controller->addCSS((__PS_BASE_URI__).'modules/'.$this->name.'/views/css/contact_form7_admin16.css','all');
            if(version_compare(_PS_VERSION_, '1.6', '<'))
                $this->context->controller->addCSS((__PS_BASE_URI__).'modules/'.$this->name.'/views/css/contact_form7_admin15.css','all');
        }
        if($controller=='AdminContactFormStatistics')
        {
            $this->context->controller->addCSS((__PS_BASE_URI__).'modules/'.$this->name.'/views/css/nv.d3_rtl.css','all');
            $this->context->controller->addCSS((__PS_BASE_URI__).'modules/'.$this->name.'/views/css/nv.d3.css','all');
        }
        $this->context->controller->addJS((__PS_BASE_URI__).'modules/'.$this->name.'/views/js/admin_all.js','all');
        
        return $this->display(__FILE__,'header_admin.tpl', $this->_getCacheId());
    }
    public function getConfigContactFormByKey($key, $default, $type = "string", $id_lang = null) {
    	if (Tools::isSubmit('submitSaveContact') || Tools::isSubmit('submitSaveAndStayContact')) {
		    $key = $id_lang ? $key . '_' . $id_lang : $key;
    		$value = Tools::getValue($key);
	    } else {
		    return $default;
	    }
    	setType($value, $type);
    	return $value;
    }
    public function saveContactForm()
    {
        $errors = array();
        $id_contact= (int)Tools::getValue('id_contact');
        $thank_you_page = Tools::getValue('thank_you_page');;
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $is_active_thank_page = (int)Tools::getValue('thank_you_active');
        $contact_fields = Ets_ctf_defines::getInstance()->getFieldConfig('contact_fields',true,$id_contact);
        $configs = $contact_fields['form']['input'];      
        if($configs)
        {
            foreach($configs as $config)
            {
                $key= $config['name'];
                $key_value = Tools::getValue($key);
                if(isset($config['lang']) && $config['lang'])
                {
                    $key_lang_value_default = Tools::getValue($key.'_'.$id_lang_default);
                    if ($key == 'thank_you_url'){
                        if (trim($thank_you_page) == 'thank_page_url'){
                            if (! $key_lang_value_default){
                                $errors[] = $config['label'] . ' ' . $this->l('is required');
                            }elseif (!Validate::isAbsoluteUrl(trim($key_lang_value_default)) && $is_active_thank_page){
                                $errors[] = $config['label'] . ' ' . $this->l('is invalid');
                            }
                        }
                        continue;
                    }
                    if (trim($thank_you_page) == 'thank_page_default' && ($key == 'thank_you_message' || $key == 'thank_you_page_title')){
                        if (!trim($key_lang_value_default)){
                            $errors[] = $config['label'] .' '. $this->l('thank you page') .' '. $this->l('is required');
                            continue;
                        }
                        
                    }
                    if(isset($config['required']) && $config['required'] && $config['type']!='switch' && trim($key_lang_value_default)=='')
                    {
                        $errors[] = $config['label'].' '.$this->l('is required');
                    }
                    elseif(isset($config['validate']) && method_exists('Validate',$config['validate']))
                    {
                        $validate = $config['validate'];
                        
                        if(!Validate::$validate(trim($key_lang_value_default),true))
                            $errors[] = $config['label'].' '.$this->l('is invalid');
                        else{
                            if($languages)
                            {
                                foreach($languages as $lang)
                                {
                                    $key_lang_value = Tools::getValue($key.'_'.$lang['id_lang']);
                                    if($key_lang_value &&! Validate::$validate(trim($key_lang_value),true) )
                                        $errors[] = $config['label'].' '.$lang['iso_code'].' '.$this->l('is invalid');
                                }
                            }
                        }
                        unset($validate);
                    }                        
                }
                else
                {
                    if(isset($config['required']) && $config['required'] && isset($config['type']) && $config['type']=='file')
                    {
                        if($this->$key=='' && !isset($_FILES[$key]['size']))
                            $errors[] = $config['label'].' '.$this->l('is required');
                        elseif(isset($_FILES[$key]['size']))
                        {
                            $fileSize = round((int)$_FILES[$key]['size'] / (1024 * 1024));
                			if($fileSize > 100)
                                $errors[] = $config['label'].' '.$this->l('Upload file cannot be larger than 100MB');
                        }   
                    }
                    else
                    {
                        if(isset($config['required']) && $config['required'] && $config['type']!='switch' && trim($key_value) == '')
                        {
                            $errors[] = $config['label'].' '.$this->l('is required');
                        }
                        elseif(!is_array($key_value) && isset($config['validate']) && method_exists('Validate',$config['validate']))
                        {
                            $validate = $config['validate'];
                            if(!Validate::$validate(trim($key_value)))
                                $errors[] = $config['label'].' '.$this->l('is invalid');
                            unset($validate);
                        }
                    }                          
                }                    
            }
        }
        $enable_form_page = Tools::getValue('enable_form_page');
        if($enable_form_page && Validate::isCleanHtml($enable_form_page))
        {
            foreach ($languages as $language) {
                if (($title_alias = Tools::getValue('title_alias_' . $language['id_lang'])) && !Validate::isLinkRewrite($title_alias)) {
                    $errors[] = $this->l('Title alias') . ' (' . $language['iso_code'] . ') is invalid.';
                } elseif ($title_alias && ($alias = Ets_contact_class::getIdContactByAlias($title_alias, (int)$language['id_lang'])) && $alias != $id_contact) {
                    $errors[] = $this->l('Title alias') . ' (' . $language['iso_code'] . ') is exists.';
                }
            }
        }
        if (trim($thank_you_page) == 'thank_page_default') {
            foreach ($languages as $language) {
                $thank_you_alias = Tools::getValue('thank_you_alias_' . $language['id_lang']);
                if ($thank_you_alias && !Validate::isLinkRewrite($thank_you_alias)) {
                    $errors[] = $this->l('Thank page alias') . ' (' . $language['iso_code'] . ') is invalid.';
                }else if($thank_you_alias && ($alias = Ets_contact_class::getIdContactByAlias($thank_you_alias, (int)$language['id_lang'],true)) && $alias != $id_contact){
                    $errors[] = $this->l('Thank page alias') . ' (' . $language['iso_code'] . ') is invalid.';
                }
            }
        }

        if(!$errors)
        {

			$this->clearCacheWhenUpdateOrCreateContactForm($id_contact);
            if($id_contact)
            {
                $contact= new Ets_contact_class($id_contact);
            } 
            else
            {
                $contact= new Ets_contact_class();
                $contact->position = Ets_contact_class::getTotalContact();
            }
            $contact->id_employee= (int)$this->context->employee->id;
            $contact->id_employee = $this->context->employee->id;    
            if($configs)
            {
                foreach($configs as $config)
                {
                    $key=$config['name'];
                    
                    if (trim($thank_you_page) == 'thank_page_url' && ($key == 'thank_you_page_title' || $key == 'thank_you_message')){
                        continue;
                    }
                    if (trim($thank_you_page) == 'thank_page_default' && $key == 'thank_you_url'){
                        continue;
                    }
                    $key_value = Tools::getValue($key);
                    if($key_value && !Ets_contactform7::validateArray($key_value))
                        continue;
                    if(isset($config['lang']) && $config['lang'])
                    {
                        $valules = array();
                        $val_lang_default = Tools::getValue($key.'_'.$id_lang_default);
                        $short_code_default = Tools::getValue('short_code'.'_'.$id_lang_default);
                        foreach($languages as $lang)
                        {
                            $val_lang = Tools::getValue($key.'_'.$lang['id_lang']);
                            if ( $config['type']=='field_form' ){
                                $short_code = Tools::getValue('short_code'.'_'.$lang['id_lang']);
                                $shor_code = trim($short_code) && Validate::isCleanHtml($short_code,true) ? trim($short_code) : (Validate::isCleanHtml($short_code_default,true) ? trim($short_code_default):'');
                                $manager = WPCF7_FormTagsManager::get_instance();
                                $manager->set_instance();
                                if ( etscf7_autop_or_not() ) {
                                    $form = $manager->normalize( $shor_code );
                                    $form = etscf7_autop( $form );
                                }
                                $manager->scan( $form );
                                $all_tag = $manager->get_scanned_tags();
                                $arr_temp = array();
                                if ( $total_field = count($all_tag) ){
                                    for( $i = 0; $i<$total_field; $i++){
                                        $arr_temp[] = $all_tag[$i]['name'] ? '['.$all_tag[$i]['name'].']':'';
                                    }
                                }
                                $valules[$lang['id_lang']] = implode(',',$arr_temp);
                            } else
                                $valules[$lang['id_lang']] = trim($val_lang) && Validate::isCleanHtml($val_lang,true) ? trim($val_lang) : (Validate::isCleanHtml($val_lang_default,true) ? trim($val_lang_default) :'');
                        }
                        $contact->$key = $valules;
                    }
                    elseif($config['type']=='switch')
                    {                           
                        $contact->$key = (int)$key_value ? 1 : 0;                                                      
                    }
                    elseif($config['type']=='categories' && isset($config['tree']['use_checkbox']) && $config['tree']['use_checkbox'])
                        $contact->$key = implode(',',$key_value);  
                    elseif($config['type']=='checkbox')
                    {
                        $values=array();
                        foreach($config['values']['query'] as $value)
                        {
                            $val = Tools::getValue($key.'_'.$value['id']);
                            if($val && Ets_contactform7::validateArray($val))
                            {
                                $values[]= $val;
                            }
                        }
                        $contact->$key = implode(',',$values);
                    }
                    elseif($config['type']=='select' && isset($config['multiple'])&& $config['multiple'])
                    {
                        $contact->$key = implode(',',$key);
                    }                                                 
                    else
                        $contact->$key = trim($key_value);   
                    }
                    $valules = array();
                    $valules_title_thank = array();
                    $thank_you_alias_default = Tools::getValue('thank_you_alias_' . $id_lang_default);
                    $thank_you_page_title_default = Tools::getValue('thank_you_page_title_' . $id_lang_default);
                    $title_alias_default = Tools::getValue('title_alias_'.$id_lang_default);
                    $title_default = Tools::getValue('title_'.$id_lang_default);
                    foreach($languages as $lang)
                    {
                        $thank_you_alias = Tools::getValue('thank_you_alias_' . $lang['id_lang']);
                        $thank_you_page_title = Tools::getValue('thank_you_page_title_' . $lang['id_lang']);
                        $title_alias = Tools::getValue('title_alias_'.$lang['id_lang']);
                        $title = Tools::getValue('title_'.$lang['id_lang']);
                        if(!$title_alias && !$title_alias_default)
                        {
                            $valules[$lang['id_lang']] = trim($title) && Validate::isCleanHtml($title) ? Tools::link_rewrite($title)  : (Validate::isCleanHtml($title_default) ? Tools::link_rewrite(trim($title_default),true) :'');
                        }
                        else
                            $valules[$lang['id_lang']] = trim($title_alias) && Validate::isCleanHtml($title_alias) ? trim($title_alias) : (Validate::isCleanHtml(trim($title_alias_default)) ? trim($title_alias_default):'');

                        if (!$thank_you_alias && !$thank_you_alias_default) {
                            $valules_title_thank[$lang['id_lang']] = trim($thank_you_page_title) && Validate::isCleanHtml($thank_you_page_title) ? Tools::link_rewrite(trim($thank_you_page_title)) : (Validate::isCleanHtml($thank_you_page_title_default) ?  Tools::link_rewrite(trim($thank_you_page_title_default), true) :'');
                            $checkAliasExit = Ets_contact_class::checkAliasExit($valules_title_thank[$lang['id_lang']], (int)$lang['id_lang'],$contact->id ? $contact->id : false);
                            if ($checkAliasExit){
                                $valules_title_thank[$lang['id_lang']] = $valules_title_thank[$lang['id_lang']].'-'.($contact->id ? $contact->id : (int)Ets_contact_class::getMaxId()+1);
                            }
                        } else{

                            $valules_title_thank[$lang['id_lang']] = trim($thank_you_alias) && Validate::isCleanHtml($thank_you_alias) ? trim($thank_you_alias) : ( Validate::isCleanHtml($thank_you_alias_default) ? trim($thank_you_alias_default):'');
                            $checkAliasExit = Ets_contact_class::checkAliasExit($valules_title_thank[$lang['id_lang']], (int)$lang['id_lang'],$contact->id ? $contact->id : false);
                            if ( $checkAliasExit){
                                $valules_title_thank[$lang['id_lang']] = $valules_title_thank[$lang['id_lang']].'-'.($contact->id ? $contact->id : (int)Ets_contact_class::getMaxId()+1);
                            }
                        }
                    }
                    $contact->title_alias=$valules;
                } 
        } 
        if (!count($errors))
        {
            $current_tab = Tools::getValue('current_tab'); 
            if($contact->id && $contact->update())
            {
                if(Tools::isSubmit('submitSaveAndStayContact'))
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&conf=4&editContact=1&id_contact='.(int)$contact->id.'&current_tab='.(Validate::isCleanHtml($current_tab) ? $current_tab:''));
                else 
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormContactForm').'&conf=4');
            }                
            elseif(!$contact->id && $contact->add())
            {
                if(Tools::isSubmit('submitSaveAndStayContact'))
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&conf=3&editContact=1&id_contact='.(int)$contact->id.'&current_tab='.(Validate::isCleanHtml($current_tab) ? $current_tab:''));
                else 
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormContactForm').'&conf=3');
            }
            else
                $errors[] = $this->l('Unknown error happens');
        }
        if($errors)
            return $this->displayError($errors);       
    }
    public function hookDisplayContactForm7($params)
    {
        if ($this->rm_hook_shortcode){
            return '';
        }
        $id=isset($params['id'])?$params['id']:$params['id_contact'];
        if($id && Ets_contact_class::existContact($id))
        {
            $contact= new Ets_contact_class($id);
            if($contact->active && $contact->id)
            {
                $contact_form = $this->etscf7_contact_form($id);
                return $this->form_html( $contact_form,true );
            }
        }
        return '';
    }
    public function etscf7_contact_form( $id ) {
    	return WPCF7_ContactForm::get_instance( $id );
    }
    public function hookContactForm7LeftBlok()
    {
        $id_contact = (int)Tools::getValue('id_contact');
        $controller = Tools::getValue('controller');
        $ets_ctf_is_updating = $id_contact?1:0;
        $cache_id = $this->_getCacheId([$controller, $ets_ctf_is_updating]);
        if (!$this->isCached('block-left.tpl', $cache_id)) {
	        $this->context->smarty->assign(
		        array(
			        'controller'=> Validate::isControllerName($controller) ? $controller :'',
			        'link'=> $this->context->link,
			        'js_dir_path' => $this->_path.'views/js/',
			        'ets_ctf_default_lang'=>Configuration::get('PS_LANG_DEFAULT'),
			        'ets_ctf_is_updating' => $ets_ctf_is_updating,
			        'count_messages' => Ets_contact_message_class::getCountMessageNoReaed(),
			        'refsLink' => isset($this->refs) ? $this->refs.$this->context->language->iso_code : false,
		        )
	        );
        }
        return $this->display(__FILE__,'block-left.tpl', $cache_id);
    }
    public static function getBaseModLink()
    {
        $context = Context::getContext();
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$context->shop->domain.$context->shop->getBaseURI();
    }
    public function hookModuleRoutes($params) {
        $contactAlias =(Configuration::get('ETS_CFT7_CONTACT_ALIAS',$this->context->language->id) ? Configuration::get('ETS_CFT7_CONTACT_ALIAS',$this->context->language->id) : 'contact-form');
        if(!$contactAlias)
            return array();
        $routes = array(
            'etscontactform7contactform' => array(
                'controller' => 'contact',
                'rule' => $contactAlias,
                'keywords' => array(
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ets_contactform7',
                ),
            ),              
            'etscontactform7contactform_contact' => array(
                'controller' => 'contact',
                'rule' => $contactAlias.'/{id_contact}-{url_alias}'.(Configuration::get('ETS_CTF7_URL_SUBFIX') ?'.html':''),
                'keywords' => array(
                    'id_contact' =>    array('regexp' => '[0-9]+', 'param' => 'id_contact'),
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-]+','param' => 'url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ets_contactform7',
                ),
            ),
            'etscontactform7contactform_noid_contact' => array(
                'controller' => 'contact',
                'rule' => $contactAlias.'/{url_alias}'.(Configuration::get('ETS_CTF7_URL_SUBFIX') ?'.html':''),
                'keywords' => array(
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-]+','param' => 'url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ets_contactform7',
                ),
            ),
            'etscontactform7contactform_contact_thank' => array(
                'controller' => 'thank',
                'rule' => $contactAlias . '/thank/{url_alias}' . (Configuration::get('ETS_CTF7_URL_SUBFIX') ? '.html' : ''),
                'keywords' => array(
                    'url_alias' => array('regexp' => '[_a-zA-Z0-9-]+', 'param' => 'url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ets_contactform7',
                ),
            ),
        );
        return $routes;
    }
    public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 60)
    {
        if ($stream_context == null && preg_match('/^https?:\/\//', $url)) {
            $stream_context = stream_context_create(array(
                "http" => array(
                    "timeout" => $curl_timeout,
                    "max_redirects" => 101,
                    "header" => 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36'
                ),
                "ssl"=>array(
                    "allow_self_signed"=>true,
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            ));
        }
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => html_entity_decode($url),
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => $curl_timeout,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_FOLLOWLOCATION => true,
            ));
            $content = curl_exec($curl);
            curl_close($curl);
            return $content;
        } elseif (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url)) {
            return Tools::file_get_contents($url, $use_include_path, $stream_context);
        } else {
            return false;
        }
    }
    public function hookActionOutputHTMLBefore($params)
    {
        if (isset($params['html']) && $params['html'])
        {
            $params['html'] = $this->doShortcode($params['html']);
        }
    }
    public function hookDisplayHome()
    {
        return $this->getContactFormByHook('home');
    }
    public function hookDisplayNav2(){
        return $this->getContactFormByHook('nav_top');
    }
    public function hookDisplayProductAdditionalInfo()
    {
        return $this->getContactFormByHook('product_info');
    }
    public function hookDisplayFooterProduct()
    {
        return $this->getContactFormByHook('product_footer');
    }
    public function hookDisplayNav(){
        return $this->getContactFormByHook('nav_top');
    }
    public function hookDisplayTop()
    {
        return $this->getContactFormByHook('displayTop');
    }
    public function hookDisplayLeftColumn()
    {
        return $this->getContactFormByHook('left_column');
    }
    public function hookDisplayFooter()
    {
        return $this->getContactFormByHook('footer_page');
    }
    public function hookDisplayRightColumn()
    {
        return $this->getContactFormByHook('right_column');
    }
    public function hookDisplayAfterProductThumbs()
    {
        return $this->getContactFormByHook('product_thumbs');
    }
    public function hookDisplayRightColumnProduct()
    {
        return $this->getContactFormByHook('product_right');
    }
    public function hookDisplayLeftColumnProduct()
    {
        return $this->getContactFormByHook('product_left');
    }
    public function hookDisplayShoppingCartFooter(){
        return $this->getContactFormByHook('checkout_page');
    }
    public function hookDisplayCustomerAccountForm()
    {
        return $this->getContactFormByHook('register_page');
    }
    public function hookDisplayCustomerLoginFormAfter()
    {
        return $this->getContactFormByHook('login_page');
    }
    public function getContactFormByHook($hook)
    {
        if($contacts = Ets_contact_class::getContactsByHook($hook))
        {
            $form_html ='';
            foreach($contacts as $contact)
            {
                $form_html .= $this->hookDisplayContactForm7($contact);
            }
            return $form_html;
        }
        return '';
    }
    public function doShortcode($str)
    {
        return preg_replace_callback('~\[contact\-form\-7 id="(\d+)"\]~',array($this,'replace'), $str);//[social-locker ]
    }
    public function replaceDefaultContactForm($str)
    {
        return preg_replace('~<section class="contact-form">.*</section>~','abc', $str);
    }
    public function replace ($matches)
    {
        if(is_array($matches) && count($matches)==2)
        {
            if ($this->rm_hook_shortcode){
                return $matches[0];
            }

            $form= $this->hookDisplayContactForm7(array(
                'id' => (int)$matches[1]
            ));
            if($form)
                return $form;
            else
            	return $this->displayText($this->l('Contact form is not available'), 'p', 'alert alert-warning');
        }
    }
    public function setMetas($id_contact = false,$thank_page = false)
    {
        $meta = array();
        $module = Tools::getValue('module');
        if(trim($module)==$this->name)
        {
            $id_lang = $this->context->language->id;
            if(!$id_contact)
                $id_contact=(int)Tools::getValue('id_contact');
    
            if ( !$id_contact && ($url_alias = Tools::getValue('url_alias')) && Validate::isCleanHtml($url_alias)){
                $id_contact = Ets_contact_class::getIdByAlias($url_alias);
            }
            $contact= new Ets_contact_class($id_contact,$id_lang);
    
            $meta['meta_title'] = $thank_page ? $contact->thank_you_page_title : ($contact->meta_title ? $contact->meta_title :$contact->title );
            $meta['meta_description'] = $contact->meta_description;
            $meta['meta_keywords'] = $contact->meta_keyword;
    
            if (version_compare(_PS_VERSION_, '1.7.0', '>='))
            {
                $body_classes = array(
                    'lang-'.$this->context->language->iso_code => true,
                    'lang-rtl' => (bool) $this->context->language->is_rtl,
                    'country-'.$this->context->country->iso_code => true,                                   
                );
                $page = array(
                    'title' => '',
                    'canonical' => '',
                    'meta' => array(
                        'title' => $meta['meta_title'],
                        'description' => $meta['meta_description'],
                        'keywords' => $meta['meta_keywords'],
                        'robots' => 'index',
                    ),
                    'page_name' => 'ets_cft_page',
                    'body_classes' => $body_classes,
                    'admin_notifications' => array(),
                ); 
                $this->context->smarty->assign(array('page' => $page)); 
            }    
            else
            {
                $this->context->smarty->assign($meta);
            }
        }
        
    }

    /**
     * @param \WPCF7_ContactForm $contact_form
     * @param bool $displayHook
     * @return string
     */
    public function form_html($contact_form,$displayHook=false) {
    	$cache_id = $this->_getCacheId($contact_form->id);
    	if (!$this->isCached('contact-form.tpl', $cache_id)) {
		    $contact_form->unit_tag = WPCF7_ContactForm::get_unit_tag( $contact_form->id );
		    $this->context->smarty->assign(
			    array(
				    'contact_form'=>$contact_form,
				    'link'=> $this->context->link,
				    'open_form_by_button' => $contact_form->open_form_by_button && $displayHook,
				    'form_elements'=> $contact_form->form_elements(),
				    'displayHook'=>$displayHook,
			    )
		    );
	    }
        return $this->display(__FILE__,'contact-form.tpl', $cache_id);

	}
    public function etscf7_text_form_tag_handler($tag)
    {
        $validation_error = false;
    	$class = etscf7_form_controls_class( $tag->type, 'wpcf7-text' );
    	if ( in_array( $tag->basetype, array( 'email', 'url', 'tel' ) ) ) {
    		$class .= ' wpcf7-validates-as-' . $tag->basetype;
    	}
    	if ( $validation_error ) {
    		$class .= ' wpcf7-not-valid';
    	}
        $class .=' form-control';
    	$atts = array();
    	$atts['size'] = $tag->get_size_option( '40' );
    	$atts['maxlength'] = $tag->get_maxlength_option();
    	$atts['minlength'] = $tag->get_minlength_option();
    	if ( $atts['maxlength'] && $atts['minlength']
    	&& $atts['maxlength'] < $atts['minlength'] ) {
    		unset( $atts['maxlength'], $atts['minlength'] );
    	}
    	$atts['class'] = $tag->get_class_option( $class );
    	$atts['id'] = $tag->get_id_option();
    	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
    	$atts['autocomplete'] = $tag->get_option( 'autocomplete',
    		'[-0-9a-zA-Z]+', true );
    	if ( $tag->has_option( 'readonly' ) ) {
    		$atts['readonly'] = 'readonly';
    	}
    	if ( $tag->is_required() ) {
    		$atts['aria-required'] = 'true';
    	}
    	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
    	$value = (string) reset( $tag->values );
    	if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
    		$atts['placeholder'] = $value;
    		$value = '';
    	}
    	$value = $tag->get_default_option( $value );
    	$value = self::get_hangover( $tag->name, $value );
        if($tag->has_option('use_current_url'))
            $value =$this->getFileCacheByUrl();
        if($tag->has_option('read_only'))
            $atts['readonly']='true';
    	$atts['value'] = $value;
    	if ( etscf7_support_html5() ) {
    		$atts['type'] = $tag->basetype;
    	} else {
    		$atts['type'] = 'text';
    	}
    	$atts['name'] = $tag->name;

        $this->context->smarty->assign(
            array(
                'html_class' => ets_sanitize_html_class( $tag->name ),
                'atts'=>$atts,
                'validation_error'=>$validation_error,
            )
        );
        return $this->display(__FILE__,'form_text.tpl');
    }
    public function etscf7_textarea_form_tag_handler($tag)
    {
        $validation_error = false;
    	$class = etscf7_form_controls_class( $tag->type );
    	if ( $validation_error ) {
    		$class .= ' wpcf7-not-valid';
    	}
        $class .=' form-control' .($tag->has_option('rte') && Configuration::get('ETS_CTF7_ENABLE_TMCE') ? ' autoload_rte_ctf7':'');
    	$atts = array();
    	$atts['cols'] = $tag->get_cols_option( '40' );
    	$atts['rows'] = $tag->get_rows_option( '10' );
    	$atts['maxlength'] = $tag->get_maxlength_option();
    	$atts['minlength'] = $tag->get_minlength_option();
    	if ( $atts['maxlength'] && $atts['minlength'] && $atts['maxlength'] < $atts['minlength'] ) {
    		unset( $atts['maxlength'], $atts['minlength'] );
    	}
    	$atts['class'] = $tag->get_class_option( $class );
    	$atts['id'] = $tag->get_id_option();
    	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
    	$atts['autocomplete'] = $tag->get_option( 'autocomplete',
    		'[-0-9a-zA-Z]+', true );
    	if ( $tag->has_option( 'readonly' ) ) {
    		$atts['readonly'] = 'readonly';
    	}
    	if ( $tag->is_required() ) {
    		$atts['aria-required'] = 'true';
    	}
    	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
    	$value = empty( $tag->content )
    		? (string) reset( $tag->values )
    		: $tag->content;
    	if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
    		$atts['placeholder'] = $value;
    		$value = '';
    	}
    	$value = $tag->get_default_option( $value );
    	$value = self::get_hangover( $tag->name, $value );
    	$atts['name'] = $tag->name;
        $preview = (int)Tools::getValue('preview');
    	$this->context->smarty->assign(
            array(
                'html_class'=>ets_sanitize_html_class( $tag->name ),
                'atts'=>$atts,
                'value'=>esc_textarea($value),
                'preview' => $preview && Configuration::get('ETS_CTF7_ENABLE_TMCE'),
                'validation_error'=>$validation_error,
            )
        );
        return $this->display(__FILE__,'form_textarea.tpl');
    }

	/**
	 * @param WPCF7_FormTag $tag
	 * @return string
	 */
    public function etscf7_captcha_form_tag_handler($tag)
    {
        $validation_error = false;
    	$class = etscf7_form_controls_class( $tag->type, 'wpcf7-text' );
    	if ( in_array( $tag->basetype, array( 'email', 'url', 'tel' ) ) ) {
    		$class .= ' wpcf7-validates-as-' . $tag->basetype;
    	}
    	if ( $validation_error ) {
    		$class .= ' wpcf7-not-valid';
    	}
        $class .=' form-control';
    	$atts = array();
    	$atts['size'] = $tag->get_size_option( '40' );
    	$atts['maxlength'] = $tag->get_maxlength_option();
    	$atts['minlength'] = $tag->get_minlength_option();
    	if ( $atts['maxlength'] && $atts['minlength']
    	&& $atts['maxlength'] < $atts['minlength'] ) {
    		unset( $atts['maxlength'], $atts['minlength'] );
    	}
    	$atts['class'] = $tag->get_class_option( $class );
    	$atts['id'] = $tag->get_id_option();
    	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
    	$atts['autocomplete'] = $tag->get_option( 'autocomplete',
    		'[-0-9a-zA-Z]+', true );
    	if ( $tag->has_option( 'readonly' ) ) {
    		$atts['readonly'] = 'readonly';
    	}
    	if ( $tag->is_required() ) {
    		$atts['aria-required'] = 'true';
    	}
    	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
        $atts['type'] = 'captcha';
    	$atts['name'] = $tag->name;
        $rand = md5(rand());
        $theme = $tag->get_option( 'theme', '(basic|complex|colorful)', true );
        $this->context->smarty->assign(
            array(
                'link_captcha_image'=>Context::getContext()->link->getModuleLink('ets_contactform7', 'captcha', array('captcha_name' => $tag->name, 'rand' => $rand,'theme'=>$theme),true),
                'html_class'=> ets_sanitize_html_class( $tag->name ),
                'atts'=>$atts,
                'url_base'=> $this->getBaseLink(),
                'rand'=>$rand,
                'validation_error'=>$validation_error
            )
        );
    	return $this->display(__FILE__,'form_captcha.tpl');
    }

	/**
	 * @param WPCF7_FormTag $tag
	 * @return string
	 */
    public function etscf7_quiz_form_tag_handler($tag)
    {
        $validation_error = false;
    	$class = etscf7_form_controls_class( $tag->type );
    	if ( $validation_error ) {
    		$class .= ' wpcf7-not-valid';
    	}
        $class .=' form-control';
    	$atts = array();
    	$atts['size'] = $tag->get_size_option( '40' );
    	$atts['maxlength'] = $tag->get_maxlength_option();
    	$atts['minlength'] = $tag->get_minlength_option();
    	if ( $atts['maxlength'] && $atts['minlength'] && $atts['maxlength'] < $atts['minlength'] ) {
    		unset( $atts['maxlength'], $atts['minlength'] );
    	}
    	$atts['class'] = $tag->get_class_option( $class );
    	$atts['id'] = $tag->get_id_option();
    	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
    	$atts['autocomplete'] = 'off';
    	$atts['aria-required'] = 'true';
    	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
	    if (count($tag->values) && count($tag->labels)) {

		    $question = $tag->labels[0];
		    $answer = $tag->values[0];
	    } else {
    		// default quiz
    		$question = '1+1=?';
    		$answer = '2';
	    }
    	$answer = etscf7_canonicalize( $answer );
    	$atts['type'] = 'text';
    	$atts['name'] = $tag->name;
    	$this->context->smarty->assign(
            array(
                'html_class' => ets_sanitize_html_class( $tag->name ),
                'question' => $question,
                'atts' =>$atts,
                'tag_name' => $tag->name,
                'answer'=> ets_hash( $answer, 'etscf7_quiz' ),
                'validation_error'=>$validation_error,
            )
        );
        return $this->display(__FILE__,'form_quiz.tpl');
    }
    public function etscf7_number_form_tag_handler($tag)
    {
        $validation_error = false;
    	$class = etscf7_form_controls_class( $tag->type );
    	$class .= ' wpcf7-validates-as-number';
    	if ( $validation_error ) {
    		$class .= ' wpcf7-not-valid';
    	}
        $class .=' form-control';
    	$atts = array();
    	$atts['class'] = $tag->get_class_option( $class );
        $atts['maxlength'] = $tag->get_maxlength_option();
        $atts['minlength'] = $tag->get_minlength_option();
    	$atts['id'] = $tag->get_id_option();
    	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
    	$atts['min'] = $tag->get_option( 'min', 'signed_int', true );
    	$atts['max'] = $tag->get_option( 'max', 'signed_int', true );
    	$atts['step'] = $tag->get_option( 'step', 'int', true );
    	if ( $tag->has_option( 'readonly' ) ) {
    		$atts['readonly'] = 'readonly';
    	}
    	if ( $tag->is_required() ) {
    		$atts['aria-required'] = 'true';
    	}
    	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
    	$value = (string) reset( $tag->values );
    	if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
    		$atts['placeholder'] = $value;
    		$value = '';
    	}
    	$value = $tag->get_default_option( $value );
    	$value = self::get_hangover( $tag->name, $value );
    	$atts['value'] = $value;
    	if ( etscf7_support_html5() ) {
    		$atts['type'] = $tag->basetype;
    	} else {
    		$atts['type'] = 'text';
    	}
    	$atts['name'] = $tag->name;
        $this->context->smarty->assign(
            array(
                'html_class' => ets_sanitize_html_class( $tag->name ),
                'atts'=>$atts,
                'validation_error'=>$validation_error,
            )
        );
        return $this->display(__FILE__,'form_number.tpl');
    }
    public function etscf7_hidden_form_tag_handler($tag)
    {
        $attrs = array();
    	$class = etscf7_form_controls_class( $tag->type );
        $attrs['class'] = $tag->get_class_option( $class );
        $attrs['id'] = $tag->get_id_option();
        $option = $tag->get_option( 'option', '(id_product|product_name)', true );
        if(!$option)
            $value = (int)Tools::getValue('id_product');
        else
        {
            $id_product = (int)Tools::getValue('id_product');
            $product = new Product($id_product,false,$this->context->language->id);
            $value = $product->name;
        }
        $attrs['value'] = $value;
        $attrs['type'] = 'hidden';
        $attrs['name'] = $tag->name;

    	$this->context->smarty->assign(
            array(
                'hidden_attrs'=>$attrs,
            )
        );
        return $this->display(__FILE__,'form_hidden.tpl');
    }
    public function etscf7_file_form_tag_handler($tag)
    {
        $validation_error = false;
    	$class = etscf7_form_controls_class( $tag->type );
    	if ( $validation_error ) {
    		$class .= ' wpcf7-not-valid';
    	}
        $class .=' form-control';
    	$atts = array();
    	$atts['size'] = $tag->get_size_option( '40' );
    	$atts['class'] = $tag->get_class_option( $class );
    	$atts['id'] = $tag->get_id_option();
    	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
    	$atts['accept'] = etscf7_acceptable_filetypes(
    		$tag->get_option( 'filetypes' ), 'attr' );
    
    	if ( $tag->is_required() ) {
    		$atts['aria-required'] = 'true';
    	}
    	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
    	$atts['type'] = 'file';
    	$atts['name'] = $tag->name;
    	$this->context->smarty->assign(
            array(
                'html_class'=> ets_sanitize_html_class( $tag->name ),
                'atts'=>$atts,
                'validation_error'=>$validation_error,
                'type_file' => $tag->get_option( 'filetypes') ? implode(',',$tag->get_option( 'filetypes')):'',
                'limit_zie' => $tag->get_option('limit') ? implode(',',$tag->get_option('limit')):'',
            )
        );
    	return $this->display(__FILE__,'form_file.tpl');
    }

	/**
	 * @param WPCF7_FormTag $tag
	 * @return string
	 */
    public function etscf7_select_form_tag_handler($tag)
    {
        $validation_error = false;
    	$class = etscf7_form_controls_class( $tag->type );
    
    	if ( $validation_error ) {
    		$class .= ' wpcf7-not-valid';
    	}
        $class .=' form-control';
    	$atts = array();
    	$atts['class'] = $tag->get_class_option( $class );
    	$atts['id'] = $tag->get_id_option();
    	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
    	if ( $tag->is_required() ) {
    		$atts['aria-required'] = 'true';
    	}
    	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
    	$multiple = $tag->has_option( 'multiple' );
    	$include_blank = $tag->has_option( 'include_blank' );
    	$first_as_label = $tag->has_option( 'first_as_label' );
    	if ( $tag->has_option( 'size' ) ) {
    		$size = $tag->get_option( 'size', 'int', true );
    
    		if ( $size ) {
    			$atts['size'] = $size;
    		} elseif ( $multiple ) {
    			$atts['size'] = 4;
    		} else {
    			$atts['size'] = 1;
    		}
    	}
    	$values = $tag->values;
    	$labels = $tag->labels;
    	if ( $data = (array) $tag->get_data_option() ) {
    		$values = array_merge( $values, array_values( $data ) );
    		$labels = array_merge( $labels, array_values( $data ) );
    	}
    	$defaults = array();
    	$default_choice = $tag->get_default_option( null, 'multiple=1' );
    	foreach ( $default_choice as $value ) {
    		$key = array_search( $value, $values, true );
    
    		if ( false !== $key ) {
    			$defaults[] = (int) $key + 1;
    		}
    	}
    	if ( $matches = $tag->get_first_match_option( '/^default:([0-9_]+)$/' ) ) {
    		$defaults = array_merge( $defaults, explode( '_', $matches[1] ) );
    	}
    	$defaults = array_unique( $defaults );
    	$shifted = false;
        if(!$multiple)
        {
            if ( $include_blank || empty( $values ) ) {
        		array_unshift( $labels, '---' );
        		array_unshift( $values, '' );
        		$shifted = true;
        	} elseif ( $first_as_label ) {
        		$values[0] = '';
        	}
        }
    	$html = '';
    	$hangover = self::get_hangover( $tag->name );
    	foreach ( $values as $key => $value ) {
    		$selected = false;
    		if ( $hangover ) {
    			if ( $multiple ) {
    				$selected = in_array( $value, (array) $hangover, true );
    			} else {
    				$selected = ( $hangover === $value );
    			}
    		} else {
    			if ( ! $shifted && in_array( (int) $key + 1, (array) $defaults ) ) {
    				$selected = true;
    			} elseif ( $shifted && in_array( (int) $key, (array) $defaults ) ) {
    				$selected = true;
    			}
    		}
    		$item_atts = array(
    			'value' => $value,
    			'selected' => $selected ? 'selected' : '',
    		);
    		$label = isset( $labels[$key] ) ? $labels[$key] : $value;
            $this->context->smarty->assign(
                array(
                    'item_atts'=>$item_atts,
                    'label'=>$label,
                )
            );
    		$html .= $this->display(__FILE__,'option.tpl');
    	}
    	if ( $multiple ) {
    		$atts['multiple'] = 'multiple';
    	}
    	$atts['name'] = $tag->name . ( $multiple ? '[]' : '' );
        $this->context->smarty->assign(
            array(
                'html_class'=>ets_sanitize_html_class($tag->name),
                'atts'=>$atts,
                'html'=>$html,
                'validation_error'=>$validation_error,
            )
        );
        return $this->display(__FILE__,'form_select.tpl');
    }
    public function etscf7_submit_form_tag_handler($tag)
    {
        $class = etscf7_form_controls_class( $tag->type );
        
        $atts = array();
    	$atts['class'] = $tag->get_class_option( $class );
    	$atts['id'] = $tag->get_id_option();
    	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
    	$value = isset( $tag->values[0] ) ? $tag->values[0] : '';
    	if ( empty( $value ) ) {
    		$value ='Send';
    	}
    	$atts['type'] = 'submit';
    	$atts['value'] = $value;
    	$this->context->smarty->assign(
            array(
                'atts'=>$atts
            )
        );
        return $this->display(__FILE__,'form_submit.tpl');
    }

	/**
	 * @param WPCF7_FormTag $tag
	 * @return string
	 */
    public function etscf7_recaptcha_form_tag_handler($tag)
    {
        $atts = array();
    	$recaptcha = WPCF7_RECAPTCHA::get_instance();
    	if (!$recaptcha->isEnableRecaptcha())
    		return '';
    	$atts['data-key'] = $recaptcha->get_sitekey();
    	$atts['data-type'] = $tag->get_option( 'type', '(audio|image)', true );
    	$atts['data-size'] = $tag->get_option(
    		'size', '(compact|normal|invisible)', true );
    	$atts['data-theme'] = $tag->get_option( 'theme', '(dark|light)', true );
    	$atts['data-badge'] = $tag->get_option(
    		'badge', '(bottomright|bottomleft|inline)', true );
    	$atts['data-tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
    	$atts['data-callback'] = $tag->get_option( 'callback', '', true );
    	$atts['data-expired-callback'] =
    		$tag->get_option( 'expired_callback', '', true );
    	$atts['class'] = $tag->get_class_option(etscf7_form_controls_class( $tag->type, 'ets-ct7-recaptcha' ));
    	$atts['id'] = $tag->get_id_option(true);
        $getFormElementAjax = (int)Tools::getValue('getFormElementAjax');
        $preview = (int)Tools::getValue('preview');
        $this->context->smarty->assign(
            array(
                'atts'=>$atts,
                'preview' => $preview || $getFormElementAjax,
                'html'=>etscf7_recaptcha_noscript(array( 'sitekey' => $atts['data-key'])),
                'v3' => $recaptcha->getCaptchaType() != 'v2',
            )
        );
        return $this->display(__FILE__,'form_recaptcha.tpl');
    }
    public function etscf7_date_form_tag_handler($tag)
    {
        $validation_error = false;
    	$class = etscf7_form_controls_class( $tag->type );
    	$class .= ' wpcf7-validates-as-date';
    	if ( $validation_error ) {
    		$class .= ' wpcf7-not-valid';
    	}
        if ($tag->has_option('time')) {
            $class .= ' datetimepicker';
        } else {
            $class .= ' datepicker';
        }
        $class .=' form-control ';
    	$atts = array();
    	$atts['class'] = $tag->get_class_option( $class );
    	$atts['id'] = $tag->get_id_option();
    	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
    	$atts['min'] = $tag->get_date_option( 'min' );
    	$atts['max'] = $tag->get_date_option( 'max' );
    	$atts['step'] = $tag->get_option( 'step', 'int', true );
    	if ( $tag->has_option( 'readonly' ) ) {
    		$atts['readonly'] = 'readonly';
    	}



    	if ( $tag->is_required() ) {
    		$atts['aria-required'] = 'true';
    	}
    	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
    	$value = (string) reset( $tag->values );
    	if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
    		$atts['placeholder'] = $value;
    		$value = '';
    	}
    	$value = $tag->get_default_option( $value );
    	$value =  self::get_hangover( $tag->name, $value );
    	$atts['value'] = $value;
    	if ( etscf7_support_html5() ) {
    		$atts['type'] = 'text';
    	} else {
    		$atts['type'] = 'text';
    	}
    	$atts['name'] = $tag->name;
    	$this->context->smarty->assign(
            array(
                'html_class' => ets_sanitize_html_class( $tag->name ),
                'atts'=>$atts,
                'validation_error'=>$validation_error,
            )
        );
    	return $this->display(__FILE__,'form_date.tpl');
    }
    public function etscf7_count_form_tag_handler($tag)
    {
        $targets = etscf7_scan_form_tags( array( 'name' => $tag->name ) );
    	$maxlength = $minlength = null;
    	while ( $targets ) {
    		$target = array_shift( $targets );
    
    		if ( 'count' != $target->type ) {
    			$maxlength = $target->get_maxlength_option();
    			$minlength = $target->get_minlength_option();
    			break;
    		}
    	}
    	if ( $maxlength && $minlength && $maxlength < $minlength ) {
    		$maxlength = $minlength = null;
    	}
    	if ( $tag->has_option( 'down' ) ) {
    		$value = (int) $maxlength;
    		$class = 'wpcf7-character-count down';
    	} else {
    		$value = '0';
    		$class = 'wpcf7-character-count up';
    	}

    	$atts = array();
    	$atts['id'] = $tag->get_id_option();
    	$atts['class'] = $tag->get_class_option( $class );
    	$atts['data-target-name'] = $tag->name;
    	$atts['data-starting-value'] = $value;
    	$atts['data-current-value'] = $value;
    	$atts['data-maximum-value'] = $maxlength;
    	$atts['data-minimum-value'] = $minlength;
    	$this->context->smarty->assign(
            array(
                'atts'=>$atts,
                'value'=>$value,
            )
        );
        return $this->display(__FILE__,'form_count.tpl');
    }


	/**
	 * @param WPCF7_FormTag $tag
	 * @return string
	 */
    public function etscf7_checkbox_form_tag_handler($tag)
    {
        $validation_error = false;
    	$class = etscf7_form_controls_class( $tag->type );
    	if ( $validation_error ) {
    		$class .= ' wpcf7-not-valid';
    	}
    	$label_first = $tag->has_option( 'label_first' );
        $each_a_line = $tag->has_option('each_a_line');
    	$use_label_element = $tag->has_option( 'use_label_element' );
    	$exclusive = $tag->has_option( 'exclusive' );
    	$multiple = false;
    	if ( 'checkbox' == $tag->basetype ) {
    		$multiple = ! $exclusive;
    	} else { // radio
    		$exclusive = false;
    	}
    	if ( $exclusive ) {
    		$class .= ' wpcf7-exclusive-checkbox';
    	}
    	$atts = array();
    	$atts['class'] = $tag->get_class_option( $class );
    	$atts['id'] = $tag->get_id_option();
    	$tabindex = $tag->get_option( 'tabindex', 'signed_int', true );
    	if ( false !== $tabindex ) {
    		$tabindex = (int) $tabindex;
    	}
    	$html = '';
    	$count = 0;
    	$values = (array) $tag->values;
    	$labels = (array) $tag->labels;
    	$defaults = array();
    	$default_choice = $tag->get_default_option( null, 'multiple=1' );
    	foreach ( $default_choice as $value ) {
    		$key = array_search( $value, $values, true );
    
    		if ( false !== $key ) {
    			$defaults[] = (int) $key + 1;
    		}
    	}
    	if ( $matches = $tag->get_first_match_option( '/^default:([0-9_]+)$/' ) ) {
    		$defaults = array_merge( $defaults, explode( '_', $matches[1] ) );
    	}
    	$defaults = array_unique( $defaults );
    	$hangover = self::get_hangover( $tag->name, $multiple ? array() : '' );
    	foreach ( $values as $key => $value ) {
    		$class = 'wpcf7-list-item';
    		$checked = false;
    
    		if ( $hangover ) {
    			if ( $multiple ) {
    				$checked = in_array( $value, (array) $hangover, true );
    			} else {
    				$checked = ( $hangover === $value );
    			}
    		} else {
    			$checked = in_array( $key + 1, (array) $defaults );
    		}
    
    		if ( isset( $labels[$key] ) ) {
    			$label = $labels[$key];
    		} else {
    			$label = $value;
    		}
    		$item_atts = array(
    			'type' => $tag->basetype,
    			'name' => $tag->name . ( $multiple ? '[]' : '' ),
    			'value' => $value,
    			'checked' => $checked ? 'checked' : '',
    			'tabindex' => false !== $tabindex ? $tabindex : '',
                'id'=> $tag->name.'_'.$value,
    		);
    		if ( false !== $tabindex && 0 < $tabindex ) {
    			$tabindex += 1;
    		}
    		$count += 1;
    		if ( 1 == $count ) {
    			$class .= ' first';
    		}
            $this->context->smarty->assign(
                array(
                    'class'=>$class,
                    'label'=>$label,
                    'label_first'=>$label_first,
                    'label_for'=> $tag->name.'_'.$value,
                    'use_label_element'=>$use_label_element,
                    'item_atts'=>$item_atts,
                    'values'=>$values,
                    'count'=>$count,
                )
            );
    		$html .= $this->display(__FILE__,'item_checkbox.tpl');
    	}
        $this->context->smarty->assign(
            array(
                'html_class'=> ets_sanitize_html_class($tag->name),
                'atts'=>$atts,  
                'html'=>$html,
                'validation_error'=>$validation_error,
                'each_a_line' => $each_a_line
            )
        );
        return $this->display(__FILE__,'form_checkbox.tpl');
    }
    public function etscf7_acceptance_form_tag_handler($tag)
    {
        $validation_error = false;
    	$class = etscf7_form_controls_class( $tag->type );
    	if ( $validation_error ) {
    		$class .= ' wpcf7-not-valid';
    	}
    	if ( $tag->has_option( 'invert' ) ) {
    		$class .= ' invert';
    	}
    	if ( $tag->has_option( 'optional' ) ) {
    		$class .= ' optional';
    	}
    	$atts = array(
    		'class' => trim( $class ),
    	);
    	$item_atts = array();
    	$item_atts['type'] = 'checkbox';
    	$item_atts['name'] = $tag->name;
    	$item_atts['value'] = '1';
    	$item_atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
    	$item_atts['aria-invalid'] = $validation_error ? 'true' : 'false';
    	if ( $tag->has_option( 'default:on' ) ) {
    		$item_atts['checked'] = 'checked';
    	}
    	$item_atts['class'] = $tag->get_class_option();
    	$item_atts['id'] = $tag->get_id_option();
    	$content = empty( $tag->content )
    		? (string) reset( $tag->values )
    		: $tag->content;
    	$content = trim( $content );
    	$this->context->smarty->assign(
            array(
                'html_class'=>ets_sanitize_html_class( $tag->name ),
                'atts'=>$atts,
                'item_atts' =>$item_atts,
                'content'=>$content,
                'validation_error'=>$validation_error,
            )
        );
        return $this->display(__FILE__,'form_acceptance.tpl');
    }
    public function replace_all_form_tags($form) {
		$manager = WPCF7_FormTagsManager::get_instance();
        $manager->set_instance();    
		if ( etscf7_autop_or_not() ) {
			$form = $manager->normalize( $form );
			$form = etscf7_autop( $form );
		}
		$form = $manager->replace_all( $form );
		$this->scanned_form_tags = $manager->get_scanned_tags();
		return $form;
	}
    static public function getEmailToString($string)
    {
        $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
        preg_match_all($pattern, $string, $matches);
        return isset($matches[0][0])?$matches[0][0]:'';
    }
    public function getBaseLink()
    {
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$this->context->shop->domain.$this->context->shop->getBaseURI();
    }
    static public function getLinkContactForm($id_contact_form,$id_lang=0,$controller = 'contact')
    {
        $context = Context::getContext();
        $id_lang = $id_lang ? $id_lang : $context->language->id;
        $contact_form= new Ets_contact_class($id_contact_form,$id_lang);
        $blogLink = new Ets_ctf_link_class();

        if(Configuration::get('PS_REWRITING_SETTINGS') && $contact_form->id && $contact_form->title_alias)
        {            
            $url = $blogLink->getBaseLinkFriendly(null, null).$blogLink->getLangLinkFriendly($id_lang, null, null);
            if ($controller == 'contact' && $contact_form->id && $contact_form->title_alias){
                $url .= (($subAlias = Configuration::get('ETS_CFT7_CONTACT_ALIAS', Context::getContext()->language->id)) ? $subAlias : 'contact-form') . '/' . (Configuration::get('ETS_CTF7_URL_NO_ID') ? '' : (int)$contact_form->id . '-') . $contact_form->title_alias . (Configuration::get('ETS_CTF7_URL_SUBFIX') ? '.html' : '');
            }elseif ($controller == 'thank'){
                $url .= (($subAlias = Configuration::get('ETS_CFT7_CONTACT_ALIAS', Context::getContext()->language->id)) ? $subAlias : 'contact-form') . '/'. (Configuration::get('ETS_CTF7_URL_SUBFIX') ? '.html' : '');
            }

            return $url;                       
        }
        return $context->link->getModuleLink('ets_contactform7','contact',array('id_contact'=>$id_contact_form));
    }
    public function _installDbConfig()
    {
        $fields_config = Ets_ctf_defines::getInstance()->getFieldConfig('config_fields',false);
        $inputs = $fields_config['form']['input'];
        $languages = Language::getLanguages(false);
        if($inputs)
        {
            foreach($inputs as $input)
            {
                if(isset($input['default']))
                {
                    $key=$input['name'];
                    if(isset($input['lang']) && $input['lang'])
                    {
                        $vals = array();
                        foreach($languages as $language)
                        {
                            $vals[$language['id_lang']]= $input['default'];
                        }
                        Configuration::updateValue($key,$vals,true);
                    }
                    else
                    {
                        Configuration::updateValue($key,$input['default']);
                    }
                }
            }
        }
        $fields_config = Ets_ctf_defines::getInstance()->getFieldConfig('email_fields');
        $inputs = $fields_config['form']['input'];
        $languages = Language::getLanguages(false);
        if($inputs)
        {
            foreach($inputs as $input)
            {
                if(isset($input['default']))
                {
                    $key=$input['name'];
                    if(isset($input['lang']) && $input['lang'])
                    {
                        $vals = array();
                        foreach($languages as $language)
                        {
                            $vals[$language['id_lang']]= $input['default'];
                        }
                        Configuration::updateValue($key,$vals,true);
                    }
                    else
                    {
                        Configuration::updateValue($key,$input['default']);
                    }
                }
            }
        }
        return true;
    }
    public function _unInstallDbConfig()
    {
        $fields_config = Ets_ctf_defines::getInstance()->getFieldConfig('config_fields',false);
        $inputs = $fields_config['form']['input'];
        if($inputs)
        {
            foreach($inputs as $input)
            {
                $key=$input['name'];
                Configuration::deleteByName($key);
            }
        }
        $fields_config= Ets_ctf_defines::getInstance()->getFieldConfig('email_fields',false);
        $inputs = $fields_config['form']['input'];
        if($inputs)
        {
            foreach($inputs as $input)
            {
                $key=$input['name'];
                Configuration::deleteByName($key);
            }
        }
        foreach (glob(_PS_ETS_CTF7_UPLOAD_DIR_ . '*.*') as $filename) {
            if(basename($filename) != 'index.php' && file_exists($filename))
                @unlink($filename);
        }
        return true;
    }
    public function displayReplyMessage($reply)
    {
        $this->context->smarty->assign(
            array(
                'reply'=>$reply,
                'link' => $this->context->link,
                'countReply' => Ets_contact_reply_class::getCountRepliesByIdMessage($reply->id_contact_message),
            )
        );
        return $this->display(__FILE__,'reply.tpl');
    }
    private function generateArchive()
    {
        $zip = new ZipArchive();
        $cacheDir = _ETS_CTF7_CACHE_DIR_;
        if(!is_dir(_ETS_CTF7_CACHE_DIR_))
            mkdir(_ETS_CTF7_CACHE_DIR_,'0755');
        $zip_file_name = 'contactform7_'.date('dmYHis').'.zip';
        if ($zip->open($cacheDir.$zip_file_name, ZipArchive::OVERWRITE | ZipArchive::CREATE) === true) {
            if (!$zip->addFromString('Data-Info.xml', $this->renderDataInfo())) {
                $this->_errors[] = $this->l('Cannot create Contact-Info.xml');
            }
            if (!$zip->addFromString('Contact-Info.xml', $this->renderContactFormData())) {
                $this->_errors[] = $this->l('Cannot create Contact-Info.xml');
            }
            $zip->close();
            if (!is_file($cacheDir.$zip_file_name)) {
                $this->_errors[] = $this->l(sprintf('Could not create %1s', $cacheDir.$zip_file_name));
            }
            if (!$this->_errors) {
                if (ob_get_length() > 0) {
                    ob_end_clean();
                }
                ob_start();
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: public');
                header('Content-Description: File Transfer');
                header('Content-type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.$zip_file_name.'"');
                header('Content-Transfer-Encoding: binary');
                ob_end_flush();
                readfile($cacheDir.$zip_file_name);
                if($cacheDir && file_exists($cacheDir.$zip_file_name))
                    @unlink($cacheDir.$zip_file_name);
                exit;
            }
        }
        {
            echo $this->l('An error occurred during the archive generation');
            die;
        }
    }
    private function renderDataInfo()
    {
        $xml_output = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml_output .= '<entity_profile>'."\n";
        $xml_output .='<version>'.$this->version.'</version>';
        $xml_output .= '</entity_profile>'."\n";
		return $xml_output;
    }
    private function renderContactFormData()
    {
        $xml_output = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml_output .= '<entity_profile>'."\n";
        $contacts = Ets_contact_class::getListContacts();
        if($contacts)
        {
            foreach($contacts as $contact)
            {
                $xml_output .='<contactfrom id="'.(int)$contact['id_contact'].'">';
                    foreach($contact as $key=>$value)
                    {
                        if($key!='id_contact')
                        {
                            $xml_output.='<'.$key.'><![CDATA['.$value.']]></'.$key.'>'."\n";  
                        }
                    }
                $contactLanguages = Ets_contact_class::getContactLanguage($contact['id_contact']);
                if($contactLanguages)
                {
                    foreach($contactLanguages as $datalanguage)
                    {
                        $xml_output .= '<datalanguage iso_code="'.$datalanguage['iso_code'].'"'.($datalanguage['id_lang']==Configuration::get('PS_LANG_DEFAULT') ? ' default="1"':'').' >'."\n";
                        foreach($datalanguage as $key=>$value)
                            if($key!='id_contact' && $key!='id_lang'&& $key!='iso_code')
                                $xml_output.='<'.$key.'><![CDATA['.$value.']]></'.$key.'>'."\n";   
                        $xml_output .='</datalanguage>'."\n";
                    }
                }
                $xml_output .='</contactfrom>';
            }
        }
        $xml_output .= '</entity_profile>'."\n";
		return $xml_output;
    }
    public function processImport($zipfile = false)
    {
        if(!is_dir(_ETS_CTF7_CACHE_DIR_))
        {
            mkdir(_ETS_CTF7_CACHE_DIR_,'0755');
        }
        if(!$zipfile)
        {
            $savePath = _ETS_CTF7_CACHE_DIR_;
            if(@file_exists($savePath.'contactform.data.zip'))
                @unlink($savePath.'contactform.data.zip');
            $uploader = new Uploader('contactformdata');
            $uploader->setCheckFileSize(false);
            $uploader->setAcceptTypes(array('zip'));        
            $uploader->setSavePath($savePath);
            $file = $uploader->process('contactform.data.zip'); 
            if ($file[0]['error'] === 0) {
                if (!Tools::ZipTest($savePath.'contactform.data.zip')) 
                    $this->_errors[] = $this->l('Zip file seems to be broken');
            } else {
                $this->_errors[] = $file[0]['error'];
            }
            $extractUrl = $savePath.'contactform.data.zip';
        }
        else      
            $extractUrl = $zipfile;
        if(!@file_exists($extractUrl))
            $this->_errors[] = $this->l('Zip file doesn\'t exist'); 
        if(!$this->_errors)
        {
            $zip = new ZipArchive();
            if($zip->open($extractUrl) === true)
            {
                if ($zip->locateName('Contact-Info.xml') === false)
                {
                    $this->_errors[] = $this->l('Import file is invalid');
                    if($extractUrl && file_exists(_ETS_CTF7_CACHE_DIR_.'contactform.data.zip'))
                       @unlink(_ETS_CTF7_CACHE_DIR_.'contactform.data.zip');
                }
                $zip->close();
            }
            else
                $this->_errors[] = $this->l('Cannot open zip file. It might be broken or damaged');
        }
        if(!$this->_errors)
        {            
            if(!Tools::ZipExtract($extractUrl, _ETS_CTF7_CACHE_DIR_))
                $this->_errors[] = $this->l('Cannot extract zip data');
            if(!@file_exists(_ETS_CTF7_CACHE_DIR_.'Contact-Info.xml'))
                $this->_errors[] = $this->l('Import file is invalid');            
        }      
        if(!$this->_errors)
        {
            if(@file_exists(_ETS_CTF7_CACHE_DIR_.'Contact-Info.xml'))
            {
                $this->importXmlTbl(@simplexml_load_file(_ETS_CTF7_CACHE_DIR_.'Contact-Info.xml'));
                @unlink(_ETS_CTF7_CACHE_DIR_.'Contact-Info.xml');
                if(file_exists(_ETS_CTF7_CACHE_DIR_.'Data-Info.xml'))
                    @unlink(_ETS_CTF7_CACHE_DIR_.'Data-Info.xml');
            } 
            if($extractUrl && file_exists(_ETS_CTF7_CACHE_DIR_.'contactform.data.zip'))
            {
                @unlink(_ETS_CTF7_CACHE_DIR_.'contactform.data.zip');
            }               
        }
        if(!$this->_errors)
        {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormContactForm').'&okimport=1');
        }
                  
    }
    public function importXmlTbl($xml)
    {
        $languages = Language::getLanguages(false);
        if($xml && isset($xml->contactfrom))
        {
            $importdeletebefore = (int)Tools::getValue('importdeletebefore');
            if($importdeletebefore)
            {
                Ets_contact_class::deleteAllContact();
            }
            foreach($xml->contactfrom as $dataContact)
            {
                $id_contact = (int)$dataContact['id'];
                $importoverride = (int)Tools::getValue('importoverride');
                if($importoverride && Ets_contact_class::existContact($id_contact))
                    $contact = new Ets_contact_class($id_contact);
                else
                {
                    $contact= new Ets_contact_class();
                    $contact->position = Ets_contact_class::getTotalContact();
                }
                $contact_fields = Ets_ctf_defines::getInstance()->getFieldConfig('contact_fields');
                $configs = $contact_fields['form']['input'];
                if($configs)
                {
                    foreach($configs as $config)
                    {
                        $key=$config['name'];
                        if(!isset($config['lang']) || !$config['lang'] && $key!='postion')
                            $contact->$key = $dataContact->$key;
                        if($key=='id_employee')
                            $contact->id_employee = (int)$this->context->employee->id;
                    }
                }
                if(isset($dataContact->datalanguage) && $dataContact->datalanguage)
                {
                    $language_xml_default=null;
                    foreach($dataContact->datalanguage as $language_xml)
                    {
                        if(isset($language_xml['default']) && (int)$language_xml['default'])
                        {
                            $language_xml_default=$language_xml;
                            break;
                        }
                    }
                    $list_language_xml=array();
                    foreach($dataContact->datalanguage as $language_xml)
                    {
                        $iso_code = (string)$language_xml['iso_code'];
                        $id_lang = Language::getIdByIso($iso_code);
                        $list_language_xml[]=$id_lang;
                        if($id_lang)
                        {
                            foreach($configs as $config)
                            {
                                $key= $config['name'];
                                if(isset($config['lang']) && $config['lang'])
                                {
                                    $temp = $contact->$key;
                                    $temp[$id_lang] = (string)$language_xml->$key;
                                    if(!$temp[$id_lang])
                                    {
                                        if(isset($language_xml_default) && $language_xml_default && isset($language_xml_default->$key)&& $language_xml_default->$key)
                                        {
                                            $temp[$id_lang]=(string)$language_xml_default->$key;
                                        }
                                    }  
                                    $contact->$key =$temp;
                                }
                            }
                        }
                    }
                    foreach($languages as $language)
                    {
                        if(!in_array($language['id_lang'],$list_language_xml))
                        {
                            foreach($configs as $config)
                            {
                                $key= $config['name'];
                                if(isset($config['lang']) && $config['lang'])
                                {
                                    $temp = $contact->$key;
                                    if(isset($language_xml_default) && $language_xml_default && isset($language_xml_default->$key) && $language_xml_default->$key)
                                    {
                                        $temp[$language['id_lang']]=$language_xml_default->$key;
                                    }  
                                    $contact->$key =$temp;
                                }
                            }
                        }
                    }
                }
                $contact->save();
            }
        }
    }
    public function getFileCacheByUrl()
    {
        $url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
            $url ='https://'.$url;
        }
        else
            $url ='http://'.$url;
        if (strpos($url, '#') !== FALSE) {
            $url = Tools::substr($url, 0, strpos($url, '#'));
        }
        return $url;
    }
    public function hookDisplayBackOfficeFooter()
    {
	    if(version_compare(_PS_VERSION_, '1.6', '<'))
		    return '';
    	if (!$this->isCached('admin_footer.tpl', $this->_getCacheId())) {
		    $this->context->smarty->assign(
			    array(
				    'ctf7_link_ajax' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name,
			    )
		    );

	    }
        return $this->display(__FILE__,'admin_footer.tpl', $this->_getCacheId());
    }
    public function getDevice()
    {
      return ($userAgent = new Ctf_browser())? $userAgent->getBrowser().' '.$userAgent->getVersion().' '.$userAgent->getPlatform() : $this->l('Unknown');
    }

    public function redirect($url)
    {
        header("HTTP/1.1 301 Moved Permanently");
        call_user_func('header',"Location: $url");
        exit;
    }
    public static function validateArray($array,$validate='isCleanHtml')
    {
        if(!is_array($array))
        {
            if(method_exists('Validate',$validate))
                return Validate::$validate($array);
            else
                return true;
        }
        if(method_exists('Validate',$validate))
        {
            if($array && is_array($array))
            {
                $ok= true;
                foreach($array as $val)
                {
                    if(!is_array($val))
                    {
                        if($val && !Validate::$validate($val))
                        {
                            $ok= false;
                            break;
                        }
                    }
                    else
                        $ok = self::validateArray($val,$validate);
                }
                return $ok;
            }
        }
        return true;
    }
	public function displayText($content=null,$tag=null,$class=null,$id=null,$href=null,$blank=false,$src = null,$alt = null,$name = null,$value = null,$type = null,$data_id_product = null,$rel = null,$attr_datas=null)
	{
		return Ets_ctf_defines::displayText($content,$tag,$class,$id,$href,$blank,$src,$alt,$name,$value,$type,$data_id_product,$rel ,$attr_datas);
	}


    public function copy_directory($src, $dst,$typeImage = true)
    {
        if (is_dir($src)) {
            $dir = opendir($src);
            if (!file_exists($dst))
                @mkdir($dst);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src . '/' . $file)) {
                        $this->copy_directory($src . '/' . $file, $dst . '/' . $file);
                    } elseif (!file_exists($dst . '/' . $file)) {
                        $type = Tools::strtolower(Tools::substr(strrchr($file, '.'), 1));
                        if(!$typeImage || in_array($type,array('jpg', 'gif', 'jpeg', 'png')))
                        {
                            copy($src . '/' . $file, $dst . '/' . $file);
                        }
                    }
                }
            }
            closedir($dir);
        }
        return true;
    }
    public function rrmdir($dir)
    {
        $dir = rtrim($dir, '/');
        if ($dir && is_dir($dir)) {
            if ($objects = scandir($dir)) {
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (is_dir($dir . "/" . $object) && !is_link($dir . "/" . $object))
                            $this->rrmdir($dir . "/" . $object);
                        elseif(file_exists($dir . "/" . $object))
                            @unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
        return true;
    }
    public static function get_hangover( $name, $default = null ) {
        if ( ! etscf7_is_posted() ) {
            return $default;
        }
        $submission = WPCF7_Submission::get_instance();
        if ( ! $submission || $submission->is( 'mail_sent' ) ) {
            return $default;
        }
        $post_value = Tools::getValue($name);
        return Tools::isSubmit($name) && Validate::isCleanHtml($post_value) ? ets_unslash($post_value) : $default;
    }
    /**
     * @param \WPCF7_Validation $result
     * @param \WPCF7_FormTag $tag
     * @return mixed
     */
    public static function quiz_validation_filter( $result, $tag ) {
        $name = $tag->name;
        $post_value = Tools::getValue($name);
        $answer = Tools::isSubmit($name) && Validate::isCleanHtml($post_value) ? etscf7_canonicalize( $post_value ) : '';
        $answer = ets_unslash( $answer );

        $answer_hash = ets_hash( $answer, 'etscf7_quiz' );
        $_etscf7_quiz_answer = (string) Tools::getValue('_etscf7_quiz_answer_' . $name);
        $expected_hash = Tools::isSubmit('_etscf7_quiz_answer_' . $name) && Validate::isCleanHtml($_etscf7_quiz_answer)
            ? $_etscf7_quiz_answer
            : '';
        if ( $answer_hash != $expected_hash && $expected_hash ) {
            $result->invalidate( $tag, etscf7_get_message( 'quiz_answer_not_correct' ) );
        }
        return $result;
    }
    /**
     * @param \WPCF7_Validation $result
     * @param \WPCF7_FormTag $tag
     * @return mixed
     */
    public static function file_validation_filter( $result, $tag ) {
        $name = $tag->name;

        $file = isset( $_FILES[$name] ) ? $_FILES[$name] : null;

        if ( $file['error'] && UPLOAD_ERR_NO_FILE != $file['error'] ) {
            $result->invalidate( $tag, etscf7_get_message( 'upload_failed_php_error' ));
            return $result;
        }
        if ( empty( $file['tmp_name'] ) && $tag->is_required() ) {
            $result->invalidate( $tag, etscf7_get_message( 'invalid_required' ) );
            return $result;
        }

        if ( ! is_uploaded_file( $file['tmp_name'] ) ) {
            return $result;
        }
        /* File type validation */

        $file_type_pattern = etscf7_acceptable_filetypes(
            $tag->get_option( 'filetypes' ), 'regex' );

        $file_type_pattern = '/\.(' . $file_type_pattern . ')$/i';

        if ( ! preg_match( $file_type_pattern, $file['name'] ) ) {
            $result->invalidate( $tag,
                etscf7_get_message( 'upload_file_type_invalid' ) );
            return $result;
        }

        /* File size validation */

        $allowed_size = 1048576; // default size 1 MB

        if ( $file_size_a = $tag->get_option( 'limit' ) ) {
            $limit_pattern = '/^([1-9][0-9]*)([kKmM]?[bB])?$/';

            foreach ( $file_size_a as $file_size ) {
                if ( preg_match( $limit_pattern, $file_size, $matches ) ) {
                    $allowed_size = (int) $matches[1];

                    if ( ! empty( $matches[2] ) ) {
                        $kbmb = Tools::strtolower( $matches[2] );

                        if ( 'kb' == $kbmb ) {
                            $allowed_size *= 1024;
                        } elseif ( 'mb' == $kbmb ) {
                            $allowed_size *= 1024 * 1024;
                        }
                    }

                    break;
                }
            }
        }
        if ( $file['size'] > $allowed_size ) {
            $result->invalidate( $tag, etscf7_get_message( 'upload_file_too_large' ) );
            return $result;
        }

        etscf7_init_uploads(); // Confirm upload dir
        $uploads_dir = etscf7_upload_tmp_dir();
        $filename = $file['name'];
        $filename = etscf7_canonicalize( $filename, 'as-is' );
        $filename = etscf7_antiscript_file_name( $filename );
        $filename = etscf7_generateRandomString(7).'-'.ets_unique_filename( $uploads_dir, str_replace(' ','-',$filename));
        $new_file = ets_path_join( $uploads_dir, $filename );
        $attachment= Tools::fileAttachment($name);
        if ( false === move_uploaded_file( $file['tmp_name'], $new_file ) ) {
            $result->invalidate( $tag, etscf7_get_message( 'upload_failed' ) );
            return $result;
        }
        chmod( $new_file, 0644 );
        if ( $submission = WPCF7_Submission::get_instance() ) {
            $submission->add_uploaded_file( $name, $new_file,$attachment );
        }
        return $result;
    }
    /**
     * @param \WPCF7_Validation $result
     * @param \WPCF7_FormTag $tag
     * @return mixed
     */
    public static function textarea_validation_filter( $result, $tag ) {
        $name = $tag->name;
        $post_value = (string) Tools::getValue($name);
        $value = Tools::isSubmit($name) ? $post_value : '';
        if ( $tag->is_required() && '' == $value ) {
            $result->invalidate( $tag, etscf7_get_message( 'invalid_required' ) );
        }
        if ( '' !== $value ) {
            $maxlength = $tag->get_maxlength_option();
            $minlength = $tag->get_minlength_option();

            if ( $maxlength && $minlength && $maxlength < $minlength ) {
                $maxlength = $minlength = null;
            }

            $code_units = etscf7_count_code_units( Tools::stripslashes( $value ) );

            if ( false !== $code_units ) {
                if ( $maxlength && $maxlength < $code_units ) {
                    $result->invalidate( $tag, etscf7_get_message( 'invalid_too_long' ) );
                } elseif ( $minlength && $code_units < $minlength ) {
                    $result->invalidate( $tag, etscf7_get_message( 'invalid_too_short' ) );
                }
                elseif(!Validate::isCleanHtml($value))
                {
                    $result->invalidate($tag,etscf7_get_message('invailid_no_valid'));
                }
            }
            elseif(!Validate::isCleanHtml($value))
            {
                $result->invalidate($tag,etscf7_get_message('invailid_no_valid'));
            }
        }
        return $result;
    }

    /**
     * @param \WPCF7_Validation $result
     * @param \WPCF7_FormTag $tag
     * @return mixed
     */
    public static function text_validation_filter( $result, $tag ) {
        $name = $tag->name;
        $post_value = Tools::getValue($name);
        $value = $post_value && !is_array($post_value)
            ? trim( ets_unslash( strtr( (string) $post_value, "\n", " " ) ) )
            : $post_value;

        if ( 'text' == $tag->basetype ) {
            if ( $tag->is_required() && '' == $value ) {
                $result->invalidate( $tag, etscf7_get_message( 'invalid_required' ) );
            }
        }
        if ( 'email' == $tag->basetype ) {
            if ( $tag->is_required() && '' == $value ) {
                $result->invalidate( $tag, etscf7_get_message( 'invalid_required' ) );
            } elseif ( '' != $value && ! etscf7_is_email( $value ) ) {
                $result->invalidate( $tag, etscf7_get_message( 'invalid_email' ) );
            }elseif ('' != $value && etscf7_is_blacklist_email($value)){
                $result->invalidate($tag, etscf7_get_message('email_black_list'));
            }
        }

        if ( 'url' == $tag->basetype ) {
            if ( $tag->is_required() && '' == $value ) {
                $result->invalidate( $tag, etscf7_get_message( 'invalid_required' ) );
            } elseif ( '' != $value && ! etscf7_is_url( $value ) ) {
                $result->invalidate( $tag, etscf7_get_message( 'invalid_url' ) );
            }
        }

        if ( 'tel' == $tag->basetype ) {
            if ( $tag->is_required() && '' == $value ) {
                $result->invalidate( $tag, etscf7_get_message( 'invalid_required' ) );
            } elseif ( '' != $value && ! etscf7_is_tel( $value ) ) {
                $result->invalidate( $tag, etscf7_get_message( 'invalid_tel' ) );
            }
        }
        if ( '' !== $value && !is_array($value) ) {
            $maxlength = $tag->get_maxlength_option();
            $minlength = $tag->get_minlength_option();

            if ( $maxlength && $minlength && $maxlength < $minlength ) {
                $maxlength = $minlength = null;
            }
            $code_units = etscf7_count_code_units( Tools::stripslashes( $value ) );
            if ( false !== $code_units ) {
                if ( $maxlength && $maxlength < $code_units ) {
                    $result->invalidate( $tag, etscf7_get_message( 'invalid_too_long' ) );
                } elseif ( $minlength && $code_units < $minlength ) {
                    $result->invalidate( $tag, etscf7_get_message( 'invalid_too_short' ) );
                }
                elseif(!Validate::isCleanHtml($value))
                {
                    $result->invalidate($tag,etscf7_get_message('invailid_no_valid'));
                }
            }
            elseif(!Validate::isCleanHtml($value))
            {
                $result->invalidate($tag,etscf7_get_message('invailid_no_valid'));
            }
        }
        return $result;
    }
    /**
     * @param \WPCF7_Validation $result
     * @param \WPCF7_FormTag $tag
     * @return mixed
     */
    public static function checkbox_validation_filter( $result, $tag ) {
        $name = $tag->name;
        $is_required = $tag->is_required() || 'radio' == $tag->type;
        $post_value = (array) Tools::getValue($name);
        $value = Tools::isSubmit($name) && self::validateArray($post_value) ? $post_value : array();
        if ( $is_required && empty( $value ) ) {
            $result->invalidate( $tag, etscf7_get_message( 'invalid_required' ) );
        }
        return $result;
    }
    /**
     * @param \WPCF7_Validation $result
     * @param \WPCF7_FormTag $tag
     * @return mixed
     */
    public static function date_validation_filter( $result, $tag ) {
        $name = $tag->name;

        $min = $tag->get_date_option( 'min' );
        $max = $tag->get_date_option( 'max' );
        $post_value = (string) Tools::getValue($name);
        $value = Tools::isSubmit($name) && Validate::isCleanHtml($post_value)
            ? trim( strtr( $post_value, "\n", " " ) )
            : '';
        if ( $tag->is_required() && '' == $value ) {
            $result->invalidate( $tag, etscf7_get_message( 'invalid_required' ) );
        } elseif ( '' != $value && ! Validate::isDate( $value ) ) {
            $result->invalidate( $tag, etscf7_get_message( 'invalid_date' ) );
        } elseif ( '' != $value && ! empty( $min ) && $value < $min ) {
            $result->invalidate( $tag, etscf7_get_message( 'date_too_early' ) );
        } elseif ( '' != $value && ! empty( $max ) && $max < $value ) {
            $result->invalidate( $tag, etscf7_get_message( 'date_too_late' ) );
        }
        return $result;
    }
    /**
     * @param \WPCF7_Validation $result
     * @param \WPCF7_FormTag $tag
     * @return mixed
     */
    public static function number_validation_filter( $result, $tag ) {
        $name = $tag->name;
        $post_value = (string) Tools::getValue($name);
        $value = Tools::isSubmit($name) && Validate::isCleanHtml($post_value)
            ? trim( strtr( $post_value, "\n", " " ) )
            : '';

        $min = $tag->get_option( 'min', 'signed_int', true );
        $max = $tag->get_option( 'max', 'signed_int', true );

        if ( $tag->is_required() && '' == $value ) {
            $result->invalidate( $tag, etscf7_get_message( 'invalid_required' ) );
        } elseif ( '' != $value && ! etscf7_is_number( $value ) ) {
            $result->invalidate( $tag, etscf7_get_message( 'invalid_number' ) );
        } elseif ( '' != $value && '' != $min && (float) $value < (float) $min ) {
            $result->invalidate( $tag, etscf7_get_message( 'number_too_small' ) );
        } elseif ( '' != $value && '' != $max && (float) $max < (float) $value ) {
            $result->invalidate( $tag, etscf7_get_message( 'number_too_large' ) );
        }

        return $result;
    }
    /**
     * @param \WPCF7_Validation $result
     * @param \WPCF7_FormTag $tag
     * @return mixed
     */
    public static function captcha_validation_filter( $result, $tag ) {
        $name = $tag->name;
        $prefix = isset( Context::getContext()->cookie->$name) ? (string) Context::getContext()->cookie->$name : '';
        $post_value = (string)Tools::getValue($name);
        $response = Tools::isSubmit($name) && Validate::isCleanHtml($post_value) ? $post_value : '';
        if ( 0 == Tools::strlen( $prefix ) || trim($prefix)!=trim($response) ) {
            $result->invalidate( $tag, etscf7_get_message( 'captcha_not_match' ) );
        }
        Context::getContext()->cookie->$name='';
        Context::getContext()->cookie->write();
        return $result;
    }
    /**
     * @param \WPCF7_Validation $result
     * @param \WPCF7_FormTag $tag
     * @return mixed
     */
    public static function select_validation_filter( $result, $tag ) {
        $name = $tag->name;
        $values=Tools::getValue($name);
        if (is_array($values) && Ets_contactform7::validateArray($values)) {
            foreach ( $values as $key => $value ) {
                if ( '' === $value ) {
                    unset( $values[$key] );
                }
            }
        }
        $empty = empty($values);

        if ( $tag->is_required() && $empty ) {
            $result->invalidate( $tag, etscf7_get_message( 'invalid_required' ) );
        }

        return $result;
    }
    /**
     * @param \WPCF7_Validation $result
     * @param \WPCF7_FormTag $tag
     * @return mixed
     */
    public static function acceptance_validation_filter( $result, $tag ) {
        if ( $tag->has_option( 'optional' ) ) {
            return $result;
        }
        $name = $tag->name;
        $value = (Tools::getValue($name) ? 1 : 0 );
        $invert = $tag->has_option( 'invert' );
        if ( $invert && $value || ! $invert && ! $value ) {
            $result->invalidate( $tag, etscf7_get_message( 'accept_terms' ) );
        }
        return $result;
    }
    public static function recaptcha_response() {
        if ( Tools::isSubmit('g-recaptcha-response') && ($response = Tools::getValue('g-recaptcha-response')) && Validate::isCleanHtml($response)) {
            return $response;
        }
        return false;
    }

    /**
     * @param \WPCF7_MailTaggedText $mailText
     * @param $matches
     * @param bool $html
     * @return array|bool|false|mixed|string|string[]|null
     */
    public static function email_replace_tags_callback($mailText,$matches, $html = false)
    {
        if ( $matches[1] == '[' && $matches[4] == ']' ) {
            return Tools::substr( $matches[0], 1, -1 );
        }
        $tag = $matches[0];
        $tagname = $matches[2];
        $values = $matches[3];
        $mail_tag = new WPCF7_MailTag( $tag, $tagname, $values );

        $field_name = $mail_tag->field_name();
        $submission = WPCF7_Submission::get_instance();
        $submitted = $submission ? $submission->get_posted_data( $field_name ) : null;
        if ( null !== $submitted ) {

            if ( $mail_tag->get_option( 'do_not_heat' ) ) {
                $field_value = Tools::getValue($field_name);
                $submitted = Tools::isSubmit($field_name) && Validate::isCleanHtml($field_value) ? $field_value : '';
            }
            $replaced = $submitted;
            if ( $format = $mail_tag->get_option( 'format' ) ) {
                $replaced = $mailText->format( $replaced, $format );
            }
            $replaced = etscf7_flat_join( $replaced );
            if ( $html ) {
                $replaced = ets_esc_html( $replaced );
            }
            if ( $form_tag = $mail_tag->corresponding_form_tag() ) {
                $type = $form_tag->type;
                if($type=='acceptance'|| $type=='acceptance*')
                    $replaced =etscf7_acceptance_mail_tag($replaced,$submitted,$html,$mail_tag);
            }
            $replaced = ets_unslash( trim( $replaced ) );
            $mailText->replaced_tags[$tag] = $replaced;
            return $replaced;
        }
        $special = null;
        if ( null !== $special ) {
            $mailText->replaced_tags[$tag] = $special;
            return $special;
        }
        return $tag;
    }
    public function displayPaggination($limit,$name)
    {
        $this->context->smarty->assign(
            array(
                'limit' => $limit,
                'pageName' => $name,
            )
        );
        return $this->display(__FILE__,'limit.tpl');
    }
	public function clearCacheWhenNeed($forBackEnd = false) {
		if (Configuration::get('ETS_CTF7_NEED_CLEAR_CACHE') && !$forBackEnd) {
			$this->_clearCache('*');
			Configuration::updateValue('ETS_CTF7_NEED_CLEAR_CACHE', 0);
		}
		if (Configuration::get('ETS_CTF7_NEED_CLEAR_CACHE_BE') && $forBackEnd) {
			$this->_clearCache('block-left.tpl');
			$this->_clearCache('message.tpl');
			$this->_clearCache('list-message.tpl');
			$this->_clearCache('row-message.tpl');
			$this->_clearCache('statistics.tpl');
			$this->_clearCache('list-contact.tpl');
			Configuration::updateValue('ETS_CTF7_NEED_CLEAR_CACHE_BE', 0);
		}
	}

	public function clearCacheWhenSaveConfig() {
		$this->_clearCache('*');
		Configuration::updateValue('ETS_CTF7_NEED_CLEAR_CACHE', 1);
	}

	public function clearCacheForBackEnd() {
		Configuration::updateValue('ETS_CTF7_NEED_CLEAR_CACHE_BE', 1);
	}

	public function _getCacheId($params = null)
	{
		$cacheId = $this->getCacheId($this->name);
		$cacheId = str_replace($this->name, '', $cacheId);
		$suffix ='';
		if($params)
		{
			if(is_array($params))
				$suffix .= '|'.implode('|',$params);
			else
				$suffix .= '|'.$params;
		}
		return $this->name . $suffix . $cacheId;
	}

	public function clearCacheWhenUpdateOrCreateContactForm($id_contact = 0) {
		Configuration::updateValue('ETS_CTF7_NEED_CLEAR_CACHE', 1);
		$this->_clearCache('header.tpl', $this->_getCacheId());
		$this->_clearCache('list-contact.tpl');
		$this->_clearCache('contact-form.tpl', $id_contact ? $this->_getCacheId($id_contact) : null);
		$this->_clearCache('list-message.tpl');
		$this->_clearCache('row-message.tpl');
		$this->_clearCache('message.tpl');
	}

	public function _clearCache($template,$cache_id = null, $compile_id = null)
	{
		if ($cache_id === null) {
			$cache_id = $this->name;
		}
		if($template=='*')
		{
			Tools::clearCache(Context::getContext()->smarty, null, $cache_id, $compile_id);
		}
		else
		{
			Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath($template), $cache_id, $compile_id);
		}
	}
}    