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

require_once(dirname(__FILE__) . '/AdminEtsRVBaseController.php');

class AdminEtsRVTrackingController extends AdminEtsRVBaseController
{
    static $execute_status = [];

    public function __construct()
    {
        $this->table = 'ets_rv_tracking';
        $this->list_id = $this->table;
        $this->className = 'EtsRvTracking';
        $this->list_simple_header = false;
        $this->show_toolbar = false;
        $this->list_no_link = true;
        $this->_orderWay = 'DESC';

        parent::__construct();

        self::$execute_status = [
            'delivered' => $this->l('Delivered', 'AdminEtsRVTrackingController'),
            'read' => $this->l('Read', 'AdminEtsRVTrackingController'),
        ];

        $this->_default_pagination = 20;
        $this->_select = '
            IF(a.is_read, \'read\', IF(a.delivered, \'delivered\', NULL)) as `execute_status`
            , cr.code
            , o.reference
            , o.id_order
            , IF(a.product_comment_id > 0, IFNULL(pc.grade, 0), NULL) as `rating`
            , CONCAT(IF(a.`employee`>0, e.`firstname`, c.`firstname`), \' \', IF(a.`employee`>0, e.`lastname`, c.`lastname`), \''.pSQL(Tools::nl2br("\n"), true).'\', IF(a.`employee`>0, e.`email`, c.`email`)) as `customer`
            , IF(a.`employee`>0, e.`email`, c.`email`) as to_email
            , IF(a.employee, e.id_employee, a.id_customer) as `customer_id`
        ';
        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.id_customer = a.id_customer)
            LEFT JOIN `' . _DB_PREFIX_ . 'employee` e ON (e.id_employee = a.employee)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_discount` d ON (d.id_ets_rv_tracking = a.id_ets_rv_tracking)
            LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule` cr ON (cr.id_cart_rule = d.id_cart_rule)
            LEFT JOIN `' . _DB_PREFIX_ . 'cart_cart_rule` ccr ON (ccr.id_cart_rule = d.id_cart_rule)
            LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (o.id_order = a.id_order)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment` pc ON (a.product_comment_id = pc.id_ets_rv_product_comment)
        ';
        $this->_defaultOrderWay = 'DESC';

        $this->_filterHaving = 'execute_status is NOT NULL';

        $this->fields_list = [
            'id_ets_rv_tracking' => [
                'title' => $this->l('ID', 'AdminEtsRVTrackingController'),
                'align' => 'ets-rv-tracking center',
                'class' => 'ets-rv-tracking fixed-width-xs',
                'filter_key' => 'a!id_ets_rv_tracking'
            ],
            'customer' => [
                'title' => $this->l('Customer', 'AdminEtsRVTrackingController'),
                'align' => 'ets-rv-customer left',
                'filter_key' => 'customer',
                'havingFilter' => true,
                'class' => 'ets-rv-customer fixed-width-lg',
                'callback' => 'displayCustomerLink',
                'ref' => 'customer_id',
            ],
            'subject' => [
                'title' => $this->l('Subject', 'AdminEtsRVTrackingController'),
                'align' => 'ets-rv-subject left',
                'class' => 'ets-rv-subject subject_status',
                'filter_key' => 'a!subject'
            ],
            'execute_status' => [
                'title' => $this->l('Execute status', 'AdminEtsRVTrackingController'),
                'type' => 'select',
                'list' => self::$execute_status,
                'class' => 'ets-rv-execute_status fixed-width-lg execute_status center',
                'filter_key' => 'execute_status',
                'havingFilter' => true,
                'callback' => 'displayExecuteStatus',
                'align' => 'ets-rv-execute_status',
            ],
            'date_upd' => [
                'title' => $this->l('Execution time', 'AdminEtsRVTrackingController'),
                'class' => 'ets-rv-date_upd excution_time',
                'type' => 'datetime',
                'filter_key' => 'a!date_upd',
                'align' => 'ets-rv-date_upd',
            ],
            'reference' => [
                'title' => $this->l('Order reference', 'AdminEtsRVTrackingController'),
                'align' => 'ets-rv-reference center',
                'class' => 'ets-rv-reference fixed-width-lg',
                'filter_key' => 'o!reference',
                'havingFilter' => true,
                'callback' => 'displayReference',
            ],
            'code' => [
                'title' => $this->l('Discount', 'AdminEtsRVTrackingController'),
                'align' => 'ets-rv-code center',
                'class' => 'ets-rv-code fixed-width-lg',
                'filter_key' => 'cr.code'
            ],
            'rating' => [
                'title' => $this->l('Rating', 'AdminEtsRVTrackingController'),
                'align' => 'ets-rv-rating center',
                'filter_key' => 'rating',
                'class' => 'ets-rv-rating fixed-width-lg',
                'havingFilter' => true,
                'callback' => 'displayRating'
            ],
        ];
    }

    public function displayReference($reference, $tr)
    {
        if (isset($tr['id_order']) && $tr['id_order'] > 0) {
            $attrs = [
                'href' => EtsRVLink::getAdminLink('AdminOrders', true, version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? ['route' => 'admin_orders_view', 'orderId' => (int)$tr['id_order']] : [], ['vieworder' => '', 'id_order' => (int)$tr['id_order']], $this->context),
                'target' => '_self',
                'title' => $reference,
            ];
            return EtsRVTools::displayText($reference, 'a', $attrs);
        }
        return $reference;
    }

    public function displaySendingTime($sending_time)
    {
        if ($sending_time !== '' && $sending_time !== '0000-00-00 00:00:00')
            return $sending_time;
        return null;
    }

    public function displayCustomerLink($to_name, $tr)
    {
        if (isset($tr['to_email']) && trim($tr['to_email']) !== '') {
            $cache_id = $tr['to_email'] . '|' . (int)$tr['employee'];
            if (isset(self::$st_customers[$cache_id]) && self::$st_customers[$cache_id])
                return self::$st_customers[$cache_id];
            $email = trim($tr['to_email']);
            if ((int)$tr['employee'] < 1) {
                $customer = (new Customer())->getByEmail($email);
                $href = $customer instanceof Customer && $customer->id > 0 ? EtsRVLink::getAdminLink('AdminCustomers', true, $this->module->ps1760 ? ['route' => 'admin_customers_view', 'customerId' => $customer->id] : [], ['viewcustomer' => '', 'id_customer' => $customer->id], $this->context) : '';
            } else {
                $employee = EtsRVModel::getEmployeeByEmail($email);
                $href = $employee instanceof Employee && $employee->id > 0 ? EtsRVLink::getAdminLink('AdminEmployees', true, ($this->module->ps1760 ? ['route' => 'admin_employees_edit', 'employeeId' => $employee->id] : []), ['viewemployee' => '', 'id_employee' => $employee->id], $this->context) : '';
            }
            $attrs = [
                'href' => $href,
                'target' => '_bank',
                'title' => strip_tags($to_name),
                'class' => 'ets_rv_customer_link',
            ];
            self::$st_customers[$cache_id] = EtsRVTools::displayText($to_name, 'a', $attrs);
            return self::$st_customers[$cache_id];
        }

        return $to_name;
    }

    public function displayExecuteStatus($status)
    {
        return $this->displayListItemValue($status, isset(self::$execute_status[$status]) ? self::$execute_status[$status] : '');
    }

    public function displayListItemValue($class, $status)
    {
        if ($status) {
            $attrs = [
                'class' => $class,
            ];
            return EtsRVTools::displayText($status, 'span', $attrs);
        }

        return null;
    }

    public function displayDelivered($delivered)
    {
        $attrs = [
            'class' => 'badge delivered_' . ($delivered ? 'yes' : 'no'),
        ];
        return EtsRVTools::displayText(($delivered ? $this->l('Yes', 'AdminEtsRVTrackingController') : $this->l('No', 'AdminEtsRVTrackingController')), 'span', $attrs);
    }

    public function displayRating($grade)
    {
        if ($grade <= 0)
            return null;
        $this->context->smarty->assign([
            'grade' => $grade ?: 0,
            'ETS_RV_DESIGN_COLOR1' => trim(Configuration::get('ETS_RV_DESIGN_COLOR1')),
        ]);

        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/rating.tpl');
    }
}