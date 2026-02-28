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


require_once(dirname(__FILE__) . '/AdminEtsACFormController.php');

class AdminEtsACUnsubscribedController extends AdminEtsACFormController
{
    static $status_mail = [];

    public function __construct()
    {
        $this->table = 'ets_abancart_unsubscribers';
        $this->identifier = 'id_ets_abancart_unsubscribers';
        $this->list_id = $this->table;
        $this->className = 'EtsAbancartUnsubscribers';
        $this->lang = false;
        $this->_orderWay = 'DESC';
        $this->list_no_link = true;
        $this->bootstrap = true;
        $this->allow_export = false;

        $this->addRowAction('delete');

        parent::__construct();

        $this->tpl_folder = 'common/';
        $this->override_folder = 'common/';

        $this->_select = '
            CONCAT(c.`firstname`, " ", c.`lastname`) `customer_name`
            , IFNULL(c.`email`, a.`email`) `email`
        ';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.id_customer = a.id_customer'.(Shop::getContext() !== Shop::CONTEXT_ALL? ' AND c.id_shop = ' . (int)$this->context->shop->id:'').')';
        $this->fields_list = array(
            'id_ets_abancart_unsubscribers' => array(
                'title' => $this->l('ID', 'AdminEtsACUnSubscribedController'),
                'type' => 'int',
                'filter_key' => 'a!id_ets_abancart_unsubscribers',
                'class' => 'fixed-width-xs center'
            ),
            'customer_name' => array(
                'title' => $this->l('Customer', 'AdminEtsACUnSubscribedController'),
                'type' => 'text',
                'filter_key' => 'customer_name',
                'callback' => 'displayCustomerName',
                'havingFilter' => true,
            ),
            'email' => array(
                'title' => $this->l('Email', 'AdminEtsACUnSubscribedController'),
                'type' => 'text',
                'havingFilter' => true,
            ),
            'date_add' => array(
                'title' => $this->l('Date added', 'AdminEtsACUnSubscribedController'),
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
                'class' => 'fixed-width-lg'
            ),
        );

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Unsubscribed', 'AdminEtsACUnsubscribedController'),
            ),
            'submit' => array(
                'title' => $this->l('Save', 'AdminEtsACUnsubscribedController'),
            ),
            'input' => [
                'search_customer' => array(
                    'name' => 'search_customer',
                    'label' => $this->l('Search customer', 'AdminEtsACUnsubscribedController'),
                    'type' => 'text',
                ),
                'id_customer' => array(
                    'name' => 'id_customer',
                    'label' => $this->l('Customer Id', 'AdminEtsACUnsubscribedController'),
                    'type' => 'hidden',
                    'default_value' => 0
                ),
            ],
        );
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(array(
            _PS_JS_DIR_ . 'jquery/plugins/autocomplete/jquery.autocomplete.js'
        ));
    }

    protected function getWhereClause()
    {
        if ($this->_filter)
            $this->_filter = preg_replace('/\s+AND\s+([a-z0-9A-Z]+)\.`(customer_name)`\s+LIKE\s+\'%(.+?)%\'/', ' AND ($1.`$2` LIKE \'%$3%\' OR $1.`id_customer`=\'$3\') ', $this->_filter);

        return parent::getWhereClause();
    }

    public function displayCustomerName($customer_name, $tr)
    {
        if (!isset($tr['id_customer']) || (int)$tr['id_customer'] < 1 || trim($customer_name) == '')
            return null;
        $attrs = [
            'href' => $this->context->link->getAdminLink('AdminCustomers', true, $this->module->ver_min_1760 ? ['route' => 'admin_customers_view', 'customerId' => (int)$tr['id_customer']] : [], ['viewcustomer' => '', 'id_customer' => (int)$tr['id_customer']]),
            'target' => '_bank',
            'class' => 'ets_ab_customer_link',
        ];
        return EtsAbancartTools::displayText($customer_name, 'a', $attrs);
    }

    public function ajaxProcessSearchCustomer()
    {
        EtsAbancartUnsubscribers::ajaxSearchCustomer(Tools::getValue('q'));
    }

    public function processSave()
    {
        $id = (int)Tools::getValue('id_customer');
        $objectId = (int)Tools::getValue('id_ets_abancart_unsubscribers');
        $customer = new Customer($id);
        if (!$id)
            $this->errors[] = $this->l('Customer is required.', 'AdminEtsACUnsubscribedController');
        elseif (!$customer->id)
            $this->errors[] = $this->l('Customer does not exist.', 'AdminEtsACUnsubscribedController');
        elseif (EtsAbancartUnsubscribers::isUnsubscribe($customer->email))
            $this->errors[] = $this->l('Customer has been subscribed.', 'AdminEtsACUnsubscribedController');
        elseif (!EtsAbancartUnsubscribers::setCustomerUnsubscribe($customer->id, $customer->email)) {
            $this->errors[] = $this->l('An error occurred while creating an object.', 'AdminEtsACUnsubscribedController');
        }
        $this->object = new EtsAbancartUnsubscribers($objectId);
        if (count($this->errors) === 0 && $this->_redirect) {
            if (empty($this->redirect_after) && $this->redirect_after !== false) {
                $this->redirect_after = self::$currentIndex . '&conf=3&token=' . $this->token;
            }
        }
        $this->errors = array_unique($this->errors);
        if (!empty($this->errors)) {
            $this->display = 'edit';

            return false;
        }
        return $this->object;
    }

    public function getFieldsValue($obj)
    {
        $id = (int)Tools::getValue('id_customer');
        $customer = new Customer($id);
        $this->fields_value['id_customer'] = $customer->id;
        $this->fields_value['email'] = $customer->email;
        $this->fields_value['customer_name'] = $customer->firstname . ' ' . $customer->lastname;
        return parent::getFieldsValue($obj);
    }
}