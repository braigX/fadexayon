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
 * Class AdminEtsSeoSocialPinterestController
 *
 * @property Ets_Seo $module
 */
class AdminEtsSeoSocialPinterestController extends ModuleAdminController
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
            'pinterest_setting' => [
                'title' => $this->module->l('Pinterest settings', 'AdminEtsSeoSocialPinterestController'),
                'icon' => '',
                'info' => $this->module->l('Claim your website with Pinterest to get access to website analytics and let people know where they can find more of your content. To claim your site with Pinterest, add your meta tag into this tab.', 'AdminEtsSeoSocialPinterestController'),
                'description' => $this->module->l('Pinterest uses Open Graph metadata just like Facebook, so be sure to keep the Open Graph checkbox on the Facebook tab checked if you want to optimize your site for Pinterest. If you have already confirmed your website with Pinterest, you can skip the step below.', 'AdminEtsSeoSocialPinterestController'),
                'fields' => $seoDef->fields_config()['pinterest_setting'],
                'submit' => [
                    'title' => $this->module->l('Save', 'AdminEtsSeoSocialPinterestController'),
                ],
            ],
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->module->l('You must enable module SEO Audit to configure its features', 'AdminEtsSeoSocialPinterestController');
        }
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $this->module->_clearCache('*');
        }
    }
}
