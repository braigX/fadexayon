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
 * Class AdminEtsSeoSocialAccountController
 *
 * @property Ets_Seo $module;
 */
class AdminEtsSeoSocialAccountController extends ModuleAdminController
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
            'rss_setting' => [
                'title' => $this->module->l('Organization social profiles', 'AdminEtsSeoSocialAccountController'),
                'icon' => '',
                'fields' => $seoDef->fields_config()['social_account'],
                'submit' => [
                    'title' => $this->module->l('Save', 'AdminEtsSeoSocialAccountController'),
                ],
            ],
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->module->l('You must enable module SEO Audit to configure its features', 'AdminEtsSeoSocialAccountController');
        }
    }

    public function postProcess()
    {
        $seoDef = Ets_Seo_Define::getInstance();
        $configs = $seoDef->fields_config()['social_account'];
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            foreach ($configs as $key => $config) {
                if (($url = Tools::getValue($key)) && 'ETS_SEO_URL_TWITTER' != $key) {
                    if (!Validate::isAbsoluteUrl($url)) {
                        $this->errors[] = $this->module->l('The', 'AdminEtsSeoSocialAccountController') . ' ' . $config['title'] . ' ' . $this->module->l('must start with http:// or https://', 'AdminEtsSeoSocialAccountController');
                    }
                }
            }
            $this->module->_clearCache('*');
        }

        return parent::postProcess();
    }
}
