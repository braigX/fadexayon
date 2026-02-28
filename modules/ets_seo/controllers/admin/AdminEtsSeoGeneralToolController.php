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
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class AdminEtsSeoGeneralToolController
 *
 * @property Ets_Seo $module
 */
class AdminEtsSeoGeneralToolController extends ModuleAdminController
{
    /**
     * __construct.
     *
     * @return void
     */
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $seoDef = Ets_Seo_Define::getInstance();
        $this->fields_options = [
            'featured' => [
                'title' => $this->module->l('Webmaster tools', 'AdminEtsSeoGeneralToolController'),
                'description' => $this->module->l('You can use the boxes below to verify with the different Webmaster Tools. This feature will add a verification meta tag on your home page.', 'AdminEtsSeoGeneralToolController')
                    . ' ' . $this->module->l('Follow the links to the different Webmaster Tools and look for instructions for the meta tag verification method to get the verification code. If your site is already verified, you can just forget about these', 'AdminEtsSeoGeneralToolController'),
                'fields' => $seoDef->fields_config()['general_tool'],
                'icon' => '',
                'submit' => [
                    'title' => $this->module->l('Save', 'AdminEtsSeoGeneralToolController'),
                ],
            ],
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->module->l('You must enable module SEO Audit to configure its features', 'AdminEtsSeoGeneralToolController');
        }
    }
    public function renderOptions()
    {
        if ($this->fields_options ) {
            $helper = new HelperOptions();
            $this->setHelperDisplay($helper);
            $helper->toolbar_scroll = true;
            $helper->toolbar_btn = [
                'save' => [
                    'href' => '#',
                    'desc' => $this->module->l('Save', 'AdminEtsSeoGeneralToolController'),
                ],
            ];
            $helper->id = $this->id;
            $helper->tpl_vars = $this->tpl_option_vars;
            return  $helper->generateOptions($this->fields_options);
        }
        return "";
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $this->module->_clearCache('*');
        }
    }
}
