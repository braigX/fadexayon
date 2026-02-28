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
 * Class AdminEtsSeoSearchConsoleController
 *
 * @property Ets_Seo $module
 */
class AdminEtsSeoSearchConsoleController extends ModuleAdminController
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
                'title' => $this->module->l('Settings', 'AdminEtsSeoSearchConsoleController'),
                'icon' => '',
                'description' => $this->module->l('To allow ETS SEO to fetch your Google Search Console information, please enter your Google Authorization Code. Clicking the button below will open a new window.', 'AdminEtsSeoSearchConsoleController'),
                'fields' => array_merge(
                    [
                        'ETS_SEO_GET_GOOGLE_AUTH_CODE' => [
                            'title' => $this->module->l('Get Google Authorization Code', 'AdminEtsSeoSearchConsoleController'),
                            'type' => 'button',
                            'no_multishop_checkbox' => true,
                        ],
                    ],
                    $seoDef->fields_config()['search_console']
                ),
                'submit' => [
                    'title' => $this->module->l('Authenticate', 'AdminEtsSeoSearchConsoleController'),
                ],
            ],
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->module->l('You must enable module SEO Audit to configure its features', 'AdminEtsSeoSearchConsoleController');
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
