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
class AdminContactFormEmailController extends ModuleAdminController
{
    public $_html;
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
        
   }
   public function renderList()
   {
        $errors= array();
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $fields_form = Ets_ctf_defines::getInstance()->getFieldConfig('email_fields');
        $inputs = $fields_form['form']['input'];
        $languages= Language::getLanguages(false);
        if(Tools::isSubmit('btnSubmit'))
        {
            if($inputs)
            {
                foreach($inputs as $input)
                {
                    $key = $input['name'];
                    $key_value = Tools::getValue($key);
                    if(isset($input['lang']) && $input['lang'])
                    {
                        $key_lang_default = Tools::getValue($key.'_'.$id_lang_default);
                        if(isset($input['required']) && $input['required'] && ! $key_lang_default)
                            $errors[] = sprintf($this->l('%s is required'),$input['label']);
                        elseif(isset($input['validate']) && method_exists('Validate',$input['validate']))
                        {
                            $validate = $input['validate'];
                            if(!Validate::$validate(trim($key_lang_default)))
                                $errors[] = sprintf($this->l('%s is invalid'),$input['label']);
                            else{
                                if($languages)
                                {
                                    foreach($languages as $lang)
                                    {
                                        $key_lang = trim(Tools::getValue($key.'_'.$lang['id_lang']));
                                        if( $key_lang && !Validate::$validate($key_lang))
                                            $errors[] = sprintf($this->l('%s [%s] is invalid'),$input['label'],$lang['iso_code']);
                                    }
                                }
                            }
                            unset($validate);
                        } 
                    }
                    elseif(isset($input['required']) && $input['required'] && ! $key_value)
                            $errors[] = sprintf($this->l('%s is required'),$input['label']);
                    elseif(isset($input['validate']) && method_exists('Validate',$input['validate']))
                    {
                        $validate = $input['validate'];
                        if(!Validate::$validate(trim($key_value)))
                            $errors[] = sprintf($this->l('%s is invalid'),$input['label']);;
                        unset($validate);
                    }
                    
                }
            }
            if($errors)
                $this->_html .= $this->module->displayError($errors);
            else
            {
            	$this->module->_clearCache('form.tpl', $this->module->_getCacheId('form_email'));
                if($inputs)
                {
                    foreach($inputs as $input)
                    {
                        $key=$input['name'];
                        if(isset($input['lang']) && $input['lang'])
                        {
                            $vals = array();
                            $key_lang_default = Tools::getValue($key.'_'.$id_lang_default);
                            foreach($languages as $language)
                            {
                                $key_lang = Tools::getValue($key.'_'.$language['id_lang']);
                                $vals[$language['id_lang']]= $key_lang && Validate::isCleanHtml($key_lang) ? $key_lang : (Validate::isCleanHtml($key_lang_default) ? $key_lang_default:'');
                            }
                            Configuration::updateValue($key,$vals,true);
                        }
                        else
                        {
                            $key_value = Tools::getValue($key);
                            if(Validate::isCleanHtml($key_value))
                                Configuration::updateValue($key,$key_value);
                        }
                    }
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormEmail').'&conf=4');
            }
        }
        if (!$this->module->isCached('form.tpl', $this->module->_getCacheId('form_email'))) {
	        $this->context->smarty->assign(
		        array(
			        'form_config'=>$this->module->renderFormEmail(),
		        )
	        );
        }
        $this->_html .=$this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'form.tpl', $this->module->_getCacheId());
        return $this->_html;
   }
}