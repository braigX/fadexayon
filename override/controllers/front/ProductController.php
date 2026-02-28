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
class ProductController extends ProductControllerCore
{
    /**
     * @var string[] Adds excluded `$_GET` keys for redirection
     */
    /*
    * module: ets_seo
    * date: 2026-01-29 18:07:28
    * version: 3.1.3
    */
    protected $redirectionExtraExcludedKeys = ['category', 'rewrite', 'id_product_attribute'];
    /*
    * module: ets_seo
    * date: 2026-01-29 18:07:28
    * version: 3.1.3
    */
    public function getTemplateVarPage():array
    {
        $page = parent::getTemplateVarPage();
        if (isset($page['meta']['title'])) {
            
            $moduleSeo = Module::getInstanceByName('ets_seo');
            $pageMeta = $moduleSeo->getSeoMetaDataArray('product', Tools::getValue('id_product'));
            $page['meta']['title'] = $pageMeta['title'];
        }
        return $page;
    }
    /*
    * module: ets_seo
    * date: 2026-01-29 18:07:28
    * version: 3.1.3
    */
    public function getPriceProduct($getDiscount = false)
    {
        $id_customer = ($this->context->customer->id) ? (int) ($this->context->customer->id) : 0;
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int) $id_customer);
        }
        if (!$id_group) {
            $id_group = (int) Group::getCurrent()->id;
        }
        $group = new Group($id_group);
        if ($group->price_display_method) {
            $tax = false;
        } else {
            $tax = true;
        }
        if ($getDiscount) {
            return Tools::displayPriceSmarty(['price'=>$this->product->getPrice($tax),'currency'=> $this->context->currency->id], $this->context->smarty);
        }
        return Tools::displayPriceSmarty(['price'=>$this->product->getPriceWithoutReduct(!$tax), 'currency'=> $this->context->currency->id], $this->context->smarty);
    }
    /*
    * module: ets_seo
    * date: 2026-01-29 18:07:28
    * version: 3.1.3
    */
    public function getBrandName()
    {
        if($this->product->id_manufacturer)
            $manuf = new Manufacturer($this->product->id_manufacturer, $this->context->language->id);
        if ( isset($manuf) && $manuf->id) {
            return $manuf->name;
        }
        return '';
    }
}
