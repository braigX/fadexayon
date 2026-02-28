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

if (!defined('_PS_VERSION_')) { exit; }

require_once dirname(__FILE__) . '/AdminEtsRVBaseController.php';


class AdminEtsRVDiscountsController extends AdminEtsRVBaseController
{
    public function __construct()
    {
        $this->table = 'cart_rule';
        $this->className = 'CartRule';
        $this->identifier = 'id_cart_rule';

        $this->allow_export = true;
        $this->_redirect = false;
        $this->list_no_link = true;
        $this->lang = true;

        parent::__construct();

        $this->addRowAction('edit');
        $this->bulk_actions = array();

        $this->_select = 'pcr.*
            , CONCAT(c.`firstname`, \' \',  c.`lastname`) customer_name
            , c.id_customer `customer_id`
            , IF(a.reduction_percent > 0, a.reduction_percent, a.reduction_amount) `discount`
        ';
        $this->_join = 'JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment_cart_rule` pcr ON (pcr.id_cart_rule = a.id_cart_rule)
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)
        ';

        $this->fields_list = array(
            'id_cart_rule' => array(
                'title' => $this->l('ID', 'AdminEtsRVDiscountsController'),
                'type' => 'text',
                'align' => 'ets-rv-id_cart_rule center',
                'class' => 'ets-rv-id_cart_rule fixed-width-xs'
            ),
            'code' => array(
                'title' => $this->l('Code', 'AdminEtsRVDiscountsController'),
                'type' => 'text',
                'align' => 'ets-rv-code',
                'class' => 'ets-rv-code',
            ),
            'quantity' => array(
                'title' => $this->l('Quantity', 'AdminEtsRVDiscountsController'),
                'type' => 'text',
                'align' => 'ets-rv-quantity center',
                'class' => 'ets-rv-quantity fixed-width-xs'
            ),
            'customer_name' => array(
                'title' => $this->l('Customer', 'AdminEtsRVDiscountsController'),
                'type' => 'text',
                'havingFilter' => true,
                'callback' => 'buildFieldCustomerLink',
                'ref' => 'customer_id',
                'align' => 'ets-rv-customer_name',
                'class' => 'ets-rv-customer_name'
            ),
            'discount' => array(
                'title' => $this->l('Discount', 'AdminEtsRVDiscountsController'),
                'type' => 'text',
                'align' => 'ets-rv-discount center',
                'class' => 'ets-rv-discount fixed-width-xs',
                'havingFilter' => true,
                'callback' => 'displayVoucherInfo',
            ),
            'date_to' => array(
                'title' => $this->l('Expiration date', 'AdminEtsRVDiscountsController'),
                'type' => 'datetime',
                'align' => 'ets-rv-date_to',
                'class' => 'ets-rv-date_to'
            ),
            'active' => array(
                'title' => $this->l('Status', 'AdminEtsRVDiscountsController'),
                'active' => 'status',
                'class' => 'ets-rv-active fixed-width-xs',
                'align' => 'ets-rv-active center',
                'type' => 'bool',
                'orderby' => false,
            ),
        );
    }

    public function initProcess()
    {
        if (Tools::isSubmit('update' . $this->table) && ($id = (int)Tools::getValue($this->identifier))) {
            Tools::redirectAdmin(EtsRVLink::getAdminLink('AdminCartRules', true, [], ['update' . $this->table => '', $this->identifier => $id], $this->context));
        }

        parent::initProcess();
    }

    public function initToolbarTitle()
    {
        parent::initToolbarTitle();

        unset($this->toolbar_btn['new']);
    }

    public function displayVoucherInfo($val, $tr)
    {
        if (!$val)
            return null;

        return isset($tr['reduction_percent']) && (float)$tr['reduction_percent'] > 0 ? $tr['reduction_percent'] . ' %' : (isset($tr['reduction_amount']) && (float)$tr['reduction_amount'] > 0 ? Tools::displayPrice(Tools::convertPriceFull($tr['reduction_amount'], Currency::getCurrencyInstance((int)$tr['reduction_currency']), Currency::getDefaultCurrency())) : '--');
    }
}