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

class Ets_Contactform7ThankModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::initContent()
     */

    public function initContent()
    {
        parent::initContent();
        $id_contact = (int)Tools::getValue('id_contact');
        if (!$id_contact && ($alias = Tools::getValue('url_alias')) && Validate::isCleanHtml($alias)) {
            $id_contact = Ets_contact_class::getIdContactByAlias($alias,null,true);
        }

        if ( Configuration::get('PS_REWRITING_SETTINGS') && $id_contact && (Tools::strpos($_SERVER['REQUEST_URI'],'url_alias')!==false || Tools::strpos($_SERVER['REQUEST_URI'],'id_contact')!==false)){
            $url = $this->module->getLinkContactForm($id_contact,$this->context->language->id);
            Tools::redirect($url);
        }

        $this->module->setMetas($id_contact,true);
        Ets_contact_class::saveLog($id_contact);
        $action = Tools::getValue('action');
        if ($action == 'addLoger') {
            die(json_encode(array(
                                      'sus' => true
                                  )));
        }
        $contact = new Ets_contact_class($id_contact, $this->context->language->id);
        if ($contact->id && $contact->active && Ets_contact_class::existContact($id_contact)) {
            $contact_form = $this->module->etscf7_contact_form($contact->id);
            $base_url = Ets_contactform7::getLinkContactForm($id_contact,(int)Context::getContext()->language->id,'thank');
            $base_url .='thank/'.$contact->thank_you_alias;
            $this->context->smarty->assign(array(
                                               'contact' => $contact,
                                               'link_contact' => $base_url,
                                               'thank_you_page_title' => $contact_form->thank_you_page_title,
                                               'thank_you_message' => $contact_form->thank_you_message
                                           ));
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('thank-page16.tpl');
            } else {
                $this->setTemplate('module:ets_contactform7/views/templates/front/thank-page.tpl');
            }
        } elseif (version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->setTemplate('not-found16.tpl');
        } else {
            $this->setTemplate('module:ets_contactform7/views/templates/front/not-found.tpl');
        }
    }


    public function getBreadcrumbLinks()
    {
        if (version_compare(_PS_VERSION_, '1.7', '<'))
            return;
        $breadcrumb = parent::getBreadcrumbLinks();
        $id_contact = (int)Tools::getValue('id_contact');
        if (!$id_contact && ($alias = Tools::getValue('url_alias')) && Validate::isCleanHtml($alias)) {
            $id_contact = Ets_contact_class::getIdContactByAlias($alias,null, true);
        }
        $contact = new Ets_contact_class($id_contact, $this->context->language->id);
        $base_url = Ets_contactform7::getLinkContactForm($id_contact,(int)Context::getContext()->language->id,'thank');
        $base_url .='thank/'.$contact->thank_you_alias;
        $breadcrumb['links'][] = array(
            'title' => $contact->thank_you_page_title,
            'url' => $base_url,
        );
        return $breadcrumb;
    }
}