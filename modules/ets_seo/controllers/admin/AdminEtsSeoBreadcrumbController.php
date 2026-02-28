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

class AdminEtsSeoBreadcrumbController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $seoDef = Ets_Seo_Define::getInstance();
        $this->fields_options = [
            'breadcrumb' => [
                'title' => $this->module->l('Breadcrumb snippet', 'AdminEtsSeoBreadcrumbController'),
                'fields' => array_merge($seoDef->fields_config()['breadcrumb_general'], $seoDef->fields_config()['breadcrumb_types']),
                'icon' => '',
                'submit' => [
                    'title' => $this->module->l('Save', 'AdminEtsSeoBreadcrumbController'),
                ],
                'description' => $this->module->l('Search engines use breadcrumb markup to categorize the information from the page in search results. A breadcrumb trail indicates the page position in the site hierarchy, allows users to navigate through your site easier.', 'AdminEtsSeoBreadcrumbController'),
            ],
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->module->l('You must enable module SEO Audit to configure its features', 'AdminEtsSeoBreadcrumbController');
        }
    }
}
