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
class AdminContactFormHelpController extends ModuleAdminController
{
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
   	    if (!$this->module->isCached('help.tpl', $this->module->_getCacheId())) {
	        $this->context->smarty->assign(
		        array(
			        'link_doc' => $this->module->getPathUri().'help/index.html',
			        'link_basic' =>(Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').Context::getContext()->shop->domain.Context::getContext()->shop->getBaseURI(),
		        )
	        );
        }
        return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'help.tpl', $this->module->_getCacheId());
   }
}