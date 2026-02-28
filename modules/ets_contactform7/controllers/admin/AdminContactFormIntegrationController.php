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

/**
 * Class AdminContactFormIntegrationController
 * @property \Ets_contactform7 $module
 */
class AdminContactFormIntegrationController extends ModuleAdminController
{
    public $_html;
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
        $fields_form = Ets_ctf_defines::getInstance()->getFieldConfig('config_fields');
        $inputs = $fields_form['form']['input'];
        $languages= Language::getLanguages(false);
        if(Tools::isSubmit('cft_import_contact_submit'))
        {
            $this->module->processImport();
            $errors = $this->module->_errors;
            if($errors)
            {
                $this->_html .=$this->module->displayError($errors);
            }
        } 
        else
        {
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
                            $val_lang_default = trim(Tools::getValue($key.'_'.$id_lang_default));
                            if(isset($input['required']) && $input['required'] && !$val_lang_default)
                                $errors[] = sprintf($this->l('%s is required'),$input['label']);
                            elseif(isset($input['validate']) && method_exists('Validate',$input['validate']))
                            {
                                $validate = $input['validate'];
                                if(!Validate::$validate($val_lang_default,true))
                                    $errors[] = sprintf($this->l('%s is invalid'),$input['label']);
                                else{
                                    if($languages)
                                    {
                                        foreach($languages as $lang)
                                        {
                                            $val_lang = trim(Tools::getValue($key.'_'.$lang['id_lang']));
                                            if( $val_lang && !Validate::$validate($val_lang,true))
                                                $errors[] = sprintf($this->l('%s [%s] is invalid'),$input['label'],$lang['iso_code']);
                                        }
                                    }
                                }
                                unset($validate);
                            } 
                        }
                        elseif(isset($input['required']) && $input['required'] && $this->fieldRequired($input))
                                $errors[] = sprintf($this->l('%s is required'),$input['label']);
                        elseif(isset($input['validate']) && method_exists('Validate',$input['validate']))
                        {
                            $validate = $input['validate'];
                            if(!Validate::$validate(trim($key_value)))
                                $errors[] = sprintf($this->l('%s is invalid'),$input['label']);
                            unset($validate);
                        }elseif ($key == 'ETS_CTF7_IP_BLACK_LIST' && ($ip_blacklist = trim($key_value)) != '' && !preg_match('/^(([0-9A-Fa-f\.\*:])+(\n|(\r\n))*)+$/', $ip_blacklist)) {
                            $errors[] = sprintf($this->l('%s is invalid'),$input['label']);
                        } elseif($key == 'ETS_CTF7_EMAIL_BLACK_LIST' && ($email_blacklist = trim($key_value)) != '' && !preg_match('/^(([a-z0-9\*@\-\._])+(\n|(\r\n))*)+$/i', $email_blacklist)) {
                            $errors[] = sprintf($this->l('%s is invalid'),$input['label']);
                        }

                    }
                }
                if($errors)
                    $this->_html .= $this->module->displayError($errors);
                else
                {
                	$this->module->clearCacheWhenSaveConfig();
                    if($inputs)
                    {
                        foreach($inputs as $input)
                        {
                            $key=$input['name'];
                            if(isset($input['lang']) && $input['lang'])
                            {
                                $vals = array();
                                $val_lang_default = Tools::getValue($key.'_'.$id_lang_default);
                                foreach($languages as $language)
                                {
                                    $val_lang = Tools::getValue($key.'_'.$language['id_lang']); 
                                    $vals[$language['id_lang']]= $val_lang && Validate::isCleanHtml($val_lang) ? $val_lang :(Validate::isCleanHtml($val_lang_default) ? $val_lang_default:'');
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
                    $current_tab = Tools::getValue('current_tab');
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormIntegration').'&conf=4&current_tab='.(Validate::isCleanHtml($current_tab) ? $current_tab:''));
                }
            }
        }
        if (!$this->module->isCached('form.tpl', $this->module->_getCacheId('form_configs'))) {
	        $this->context->smarty->assign(
		        array(
			        'form_config'=>$this->module->renderFormConfig(),
		        )
	        );
        }
        $this->_html .=$this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'form.tpl', $this->module->_getCacheId('form_configs'));
        return $this->_html;
   }

    public function fieldRequired($input)
    {
        $ETS_CTF7_ENABLE_RECAPTCHA = Tools::getValue('ETS_CTF7_ENABLE_RECAPTCHA');
        $ETS_CTF7_RECAPTCHA_TYPE = Tools::getValue('ETS_CTF7_RECAPTCHA_TYPE');
        $val = Tools::getValue($input['name']);
        switch ($input['name']) {
            case 'ETS_CFT7_SITE_KEY' :
                if ( $ETS_CTF7_ENABLE_RECAPTCHA && $ETS_CTF7_RECAPTCHA_TYPE == 'v2' && !$val)
                    return true;
                break;
            case 'ETS_CFT7_SECRET_KEY' :
                if ($ETS_CTF7_ENABLE_RECAPTCHA && $ETS_CTF7_RECAPTCHA_TYPE == 'v2' && !$val)
                    return true;
                break;
            case 'ETS_CTF7_SITE_KEY_V3' :
                if ( $ETS_CTF7_ENABLE_RECAPTCHA && $ETS_CTF7_RECAPTCHA_TYPE == 'v3' && !$val)
                    return true;
                break;
            case 'ETS_CTF7_SECRET_KEY_V3' :
                if ($ETS_CTF7_ENABLE_RECAPTCHA && $ETS_CTF7_RECAPTCHA_TYPE == 'v3' && !$val)
                    return true;
                break;
            default :
                
                if (!$val)
                    return true;
                break;
        }
        return false;
    }
}