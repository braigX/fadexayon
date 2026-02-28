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
 
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/form-tag.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/function.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/Contact.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/contact-form.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/form-tags-manager.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/submission.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/mail.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/pipe.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/integration.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/recaptcha.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/validation.php');
class Ets_contactform7ContactModuleFrontController extends ModuleFrontController
{
    /**
    * @see FrontController::initContent()
    */
    public function initContent()
    {
        
        parent::initContent();
        $this->module->setMetas();
        $this->context= Context::getContext();
        $id_contact= (int)Tools::getValue('id_contact');

        if ( !$id_contact && ($url_alias = Tools::getValue('url_alias')) && Validate::isCleanHtml($url_alias)){
            $id_contact = Ets_contact_class::getIdByAlias($url_alias);
            if ( $id_contact && Configuration::get('PS_REWRITING_SETTINGS') && ! Configuration::get('ETS_CTF7_URL_NO_ID')){
                $this->module->redirect($this->module->getLinkContactForm($id_contact));
            }
        }elseif($id_contact && Configuration::get('ETS_CTF7_URL_NO_ID') && Configuration::get('PS_REWRITING_SETTINGS'))
        {
            $this->module->redirect($this->module->getLinkContactForm($id_contact));
        }

        if ( Configuration::get('PS_REWRITING_SETTINGS') && $id_contact && (Tools::strpos($_SERVER['REQUEST_URI'],'url_alias')!==false || Tools::strpos($_SERVER['REQUEST_URI'],'id_contact')!==false)){
            $url = $this->module->getLinkContactForm($id_contact,$this->context->language->id);
            Tools::redirect($url);
        }
        Ets_contact_class::saveLog($id_contact);
        $action = Tools::getValue('action');
        if($action=='addLoger')
        {
            die(
                json_encode(
                    array(
                        'sus'=>true
                    )
                )
            );
        }
        $contact= new Ets_contact_class($id_contact,$this->context->language->id);
        if($contact->id && $contact->active && $contact->enable_form_page && Ets_contact_class::existContact($id_contact))
        {
            
            $contact_form = $this->module->etscf7_contact_form($contact->id);
            $this->context->smarty->assign(
                array(
                    'form_html'=>$this->module->form_html( $contact_form ),
                    'contact'=>$contact,
                    'link_contact'=> $this->module->getLinkContactForm($id_contact),
                )
            );
            if(version_compare(_PS_VERSION_, '1.7', '<'))
                $this->setTemplate('contactform16.tpl');
            else
                $this->setTemplate('module:ets_contactform7/views/templates/front/contactform.tpl');
        }
        elseif(version_compare(_PS_VERSION_, '1.7', '<'))
            $this->setTemplate('not-found16.tpl');
        else
            $this->setTemplate('module:ets_contactform7/views/templates/front/not-found.tpl');
    }
    public function getBreadcrumbLinks()
    {
        if(version_compare(_PS_VERSION_, '1.7', '<'))
            return '';
        $breadcrumb = parent::getBreadcrumbLinks();
        $id_contact = (int)Tools::getValue('id_contact');
        if (!$id_contact && ($alias = Tools::getValue('url_alias')) && Validate::isCleanHtml($alias) ) {
            $id_contact = Ets_contact_class::getIdContactByAlias($alias);
        }
        $contact= new Ets_contact_class($id_contact,$this->context->language->id);
        $breadcrumb['links'][] = array(
            'title' => $contact->title,
            'url' => $this->module->getLinkContactForm($id_contact),
         );

         return $breadcrumb;
    }
}