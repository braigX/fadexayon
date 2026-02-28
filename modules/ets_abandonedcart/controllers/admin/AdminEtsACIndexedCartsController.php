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


require_once(dirname(__FILE__) . '/AdminEtsACFormController.php');

class AdminEtsACIndexedCartsController extends AdminEtsACFormController
{
    public function __construct()
    {
        $this->table = 'ets_abancart_index';
        $this->list_id = $this->table;
        $this->lang = false;
        $this->_orderBy = 'cart_date_add';
        $this->_orderWay = 'DESC';
        $this->list_no_link = true;
        $this->allow_export = false;

        parent::__construct();

        $this->_select = '
            CONCAT(a.`firstname`, \' \', a.`lastname`, \'' . pSQL(Tools::nl2br("\r\n"), true) . '\', a.`email`) as `customer_name`
            , c.`id_currency`
            , IF(a.`total_cart` > 0, 1, 0) `badge_success`
            , rl.`title` as `reminder_name`
        ';

        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'cart` c ON (a.id_cart = c.id_cart)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder_lang` rl ON (rl.id_ets_abancart_reminder = a.id_ets_abancart_reminder AND rl.id_lang=a.id_cart_lang)
        ';

        $this->_where = 'AND a.id_shop = ' . (int)$this->context->shop->id;

        $this->fields_list = array(
            'id_cart' => array(
                'title' => $this->l('Cart ID', 'AdminEtsACIndexedCartsController'),
                'type' => 'int',
                'filter_key' => 'a!id_cart',
                'class' => 'fixed-width-xs center',
            ),
            'reminder_name' => array(
                'title' => $this->l('Reminder', 'AdminEtsACIndexedCartsController'),
                'type' => 'text',
                'filter_key' => 'rl!title',
            ),
            'customer_name' => array(
                'title' => $this->l('Customer', 'AdminEtsACIndexedCartsController'),
                'type' => 'text',
                'havingFilter' => true,
                'callback' => 'displayCustomerName',
            ),
            'total_cart' => array(
                'title' => $this->l('Total cart', 'AdminEtsACIndexedCartsController'),
                'type' => 'decimal',
                'filter_key' => 'a!total_cart',
                'align' => 'text-center',
                'callback' => 'displayTotalCart',
                'badge_success' => true,
                'class' => '',
            ),
            'cart_date_add' => array(
                'title' => $this->l('Added date', 'AdminEtsACIndexedCartsController'),
                'type' => 'datetime',
                'align' => 'center',
                'filter_key' => 'a!cart_date_add',
                'class' => 'fixed-width-lg',
            ),
        );
    }

    public function displayCustomerName($customer_name, $tr)
    {
        if (!isset($tr['id_customer']) || !(int)$tr['id_customer'])
            return $customer_name;
        $attrs = [
            'href' => $this->context->link->getAdminLink('AdminCustomers', true, $this->module->ver_min_1760 ? ['route' => 'admin_customers_view', 'customerId' => (int)$tr['id_customer']] : [], ['viewcustomer' => '', 'id_customer' => (int)$tr['id_customer']]),
            'target' => '_bank',
            'class' => 'ets_ab_customer_link',
        ];
        return EtsAbancartTools::displayText($customer_name, 'a', $attrs);
    }

    public function displayTotalCart($total_cart, $tr)
    {
        $defaultCurrency = Currency::getDefaultCurrency();
        $total_cart = Tools::convertPrice($total_cart, $defaultCurrency);
        return Tools::displayPrice($total_cart, $defaultCurrency);
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }
}