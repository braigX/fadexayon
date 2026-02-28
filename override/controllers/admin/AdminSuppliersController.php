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
class AdminSuppliersController extends AdminSuppliersControllerCore
{
    /*
    * module: ets_seo
    * date: 2026-01-29 18:07:28
    * version: 3.1.3
    */
    public function __construct()
    {
        parent::__construct();
        $this->_select .= ', ess.key_phrase,
            ess.seo_score,
            ess.readability_score';
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_supplier` ess ON (a.`id_supplier` = ess.`id_supplier` 
                                                AND ess.`id_shop` = ' . (int) $this->context->shop->id . ' 
                                                AND ess.`id_lang` = ' . (int) $this->context->language->id . ')';
        $ets_seo = Module::getInstanceByName('ets_seo');
        $this->fields_list = array_merge($this->fields_list, $ets_seo->get_fields_list_page('ess'));
    }
    /*
    * module: ets_seo
    * date: 2026-01-29 18:07:28
    * version: 3.1.3
    */
    public function processFilter()
    {
        parent::processFilter();
        $ets_seo = Module::getInstanceByName('ets_seo');
        $ets_seo->actionAdminSupplierChangeFilter($this->_filter, 'ess');
    }
}
