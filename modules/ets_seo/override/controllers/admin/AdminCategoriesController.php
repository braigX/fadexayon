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
class AdminCategoriesController extends AdminCategoriesControllerCore
{
    public function __construct()
    {
        parent::__construct();
        $this->_select .= ', esc.key_phrase,
            esc.seo_score,
            esc.readability_score';

        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_category` esc ON (a.`id_category` = esc.`id_category` 
                                                AND esc.`id_shop` = ' . (int) $this->context->shop->id . ' 
                                                AND esc.`id_lang` = ' . (int) $this->context->language->id . ')';

        $ets_seo = Module::getInstanceByName('ets_seo');

        $this->fields_list = array_merge($this->fields_list, $ets_seo->get_fields_list_page('esc'));
    }

    public function processFilter()
    {
        parent::processFilter();
        $ets_seo = Module::getInstanceByName('ets_seo');
        $ets_seo->actionAdminCmsChangeFilter($this->_filter, 'esc');
    }
}
