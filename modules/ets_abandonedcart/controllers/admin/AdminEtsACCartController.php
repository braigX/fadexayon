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

class AdminEtsACCartController extends AdminEtsACFormController
{
    /**
     * @var Ets_abandonedcart
     */
    public $module;

    public function __construct()
    {
        $this->table = 'cart';
        $this->className = 'Cart';
        $this->list_id = $this->table;
        $this->show_form_cancel_button = false;
        $this->_redirect = false;
        $this->list_no_link = true;

        $this->addRowAction('sendmail');
        $this->addRowAction('viewcart');
        if ((int)Configuration::get('ETS_ABANCART_WRITE_NOTE_MANUAL') > 0) {
            $this->addRowAction('writenote');
        }
        $this->addRowAction('reminderlog');
        if (Module::isEnabled('ets_trackingcustomer')) {
            $this->addRowAction('session');
        }
        $this->allow_export = false;
        $this->_orderBy = 'a.id_cart';
        $this->_orderWay = 'DESC';

        parent::__construct();

        $this->tpl_folder = 'common/';

        $this->_select = '
            CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`, \'' . pSQL(Tools::nl2br("\r\n"), true) . '\', c.`email`) `customer_name`
            , c.id_customer
            , c.email
            , (SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_abancart_tracking` t WHERE t.id_cart=a.id_cart) as `reminders`
            , a.id_cart `total`
            , o.id_order
            , IF (IFNULL(o.id_order, \'' . pSQL($this->l('Non ordered', 'AdminEtsACCartController')) . '\') = \'' . pSQL($this->l('Non ordered', 'AdminEtsACCartController')) . '\', IF(TIME_TO_SEC(TIMEDIFF(\'' . pSQL(date('Y-m-d H:i:00', time())) . '\', a.`date_add`)) > 86400, \'' . pSQL($this->l('Abandoned cart', 'AdminEtsACCartController')) . '\', \'' . pSQL($this->l('Non ordered', 'AdminEtsACCartController')) . '\'), o.id_order) AS status
            , IF(o.id_order, 1, 0) badge_success
            , IF(o.id_order, 0, 1) badge_danger
            , IF(co.id_guest, 1, 0) id_guest
            , a.date_add
            , a.id_cart `sending_time`
            , 0 `sendmail_state`
		    , a.id_cart `next_mail_time`
        ';
        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.id_customer = a.id_customer)
            LEFT JOIN `' . _DB_PREFIX_ . 'currency` cu ON (cu.id_currency = a.id_currency)
            LEFT JOIN `' . _DB_PREFIX_ . 'carrier` ca ON (ca.id_carrier = a.id_carrier)
            LEFT JOIN `' . _DB_PREFIX_ . 'address` ad ON (ad.id_address = a.' . pSQL(Configuration::get('PS_TAX_ADDRESS_TYPE')) . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (o.id_cart = a.id_cart)
            LEFT JOIN (
                SELECT `id_guest`
                FROM `' . _DB_PREFIX_ . 'connections`
                WHERE
                    TIME_TO_SEC(TIMEDIFF(\'' . pSQL(date('Y-m-d H:i:00', time())) . '\', `date_add`)) < 1800
                LIMIT 1
           ) AS co ON co.`id_guest` = a.`id_guest`  
       ';
        if ((int)Configuration::get('ETS_ABANCART_WRITE_NOTE_MANUAL') > 0) {
            $this->_select .= ', nm.`content` `note`';
            $this->_join .= '
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_note_manual` nm ON (nm.id_cart = a.id_cart)
            ';
        }


        if (($action = Tools::getValue('action')) && Validate::isCleanHtml($action) && trim($action) === 'filterOnlyAbandonedCarts') {
            $this->_having = 'status = \'' . pSQL($this->l('Abandoned cart', 'AdminEtsACCartController')) . '\'';
        } else {
            $this->_use_found_rows = false;
        }

        $this->_where = '
            AND (ad.id_address is NOT NULL OR a.' . pSQL(Configuration::get('PS_TAX_ADDRESS_TYPE')) . ' = 0) 
            AND o.id_order is NULL AND a.id_shop = ' . (int)$this->context->shop->id . ' 
            AND a.id_cart IN (SELECT cp.id_cart FROM `' . _DB_PREFIX_ . 'cart_product` cp WHERE cp.id_cart=a.id_cart)
        ';

        $this->fields_list = array(
            'id_cart' => array(
                'title' => $this->l('ID', 'AdminEtsACCartController'),
                'type' => 'int',
                'class' => 'fixed-width-xs center',
                'filter_key' => 'a!id_cart',
            ),
            'customer_name' => array(
                'title' => $this->l('Customer', 'AdminEtsACCartController'),
                'havingFilter' => true,
                'callback' => 'displayCustomerName'
            ),
        );

        if ((int)Configuration::get('ETS_ABANCART_WRITE_NOTE_MANUAL') > 0) {
            $this->fields_list['note'] = array(
                'title' => $this->l('Note', 'AdminEtsACCartController'),
                'orderby' => false,
                'search' => false,
                'class' => 'ets_abancart_note_manual',
                'align' => 'ets_abancart_note_manual text-left',
            );
        }

        $this->fields_list = array_merge($this->fields_list, [
            'total' => array(
                'title' => $this->l('Cart total', 'AdminEtsACCartController'),
                'callback' => 'getOrderTotalUsingTaxCalculationMethod',
                'orderby' => false,
                'search' => false,
                'align' => 'text-right',
                'badge_success' => true,
            ),
            'reminders' => [
                'title' => $this->l('Reminders', 'AdminEtsACCartController'),
                'class' => 'fixed-width-xs center',
                'filterHaving' => 'reminders',
                'havingFilter' => true,
                'callback' => 'displayReminders'
            ],
            'date_add' => array(
                'title' => $this->l('Date', 'AdminEtsACCartController'),
                'align' => 'text-left',
                'type' => 'datetime',
                'class' => 'fixed-width-lg ets_abancart_dateadd',
                'filter_key' => 'a!date_add',
            ),
            'next_mail_time' => array(
                'title' => $this->l('Next time to send email', 'AdminEtsACCartController'),
                'type' => 'datetime',
                'align' => 'center',
                'class' => 'fixed-width-lg',
                'havingFilter' => true,
                'callback' => 'displayNextMailTime',
            ),
            'sending_time' => array(
                'title' => $this->l('Last email sent at', 'AdminEtsACCartController'),
                'type' => 'datetime',
                'class' => 'fixed-width-lg ets_abancart_send_date',
                'havingFilter' => true,
                'callback' => 'displaySendingTime',
            ),
        ]);

        $this->bulk_actions = array(
            'sendmail_divider' => array(
                'text' => 'divider',
            )
        );
    }

    public function displayNextMailTime($id_cart, $tr)
    {
        if ((int)$id_cart > 0 && (!isset($tr['id_order'])) || (int)$tr['id_order'] <= 0) {
            $next_mail_time = EtsAbancartReminder::getNextMailTime((int)$id_cart);

            return $next_mail_time ?: null;
        }
        return null;
    }

    public function displayReminders($reminders, $tr)
    {
        if (!empty($tr['id_cart'])) {
            if (!$reminders)
                return EtsAbancartTools::displayText($reminders, 'span');
            $attrs = [
                'href' => self::$currentIndex . '&id_cart=' . (int)$tr['id_cart'] . '&reminderlog' . '&token=' . $this->token,
                'title' => $reminders,
                'class' => 'ets_abancart_reminder_log',
            ];
            return EtsAbancartTools::displayText(EtsAbancartTools::displayText($reminders, 'span', ['class' => 'badge badge-info']), 'a', $attrs);
        }

        return $reminders;
    }

    public function displaySendingTime($id_cart)
    {
        $res = EtsAbancartTracking::getSendingTime($id_cart);
        if (!$res)
            return null;
        $sending_time = $res['sending_time'] !== '0000-00-00 00:00:00' ? $res['sending_time'] : null;
        if ($sending_time == null)
            return $sending_time;
        $time_elapsed_string = $this->timeElapsedString($sending_time);
        $sendmail_state = (int)$res['sendmail_state'];
        $attrs = array(
            'class' => 'badge badge-' . ($sendmail_state == 1 ? 'success' : ($sendmail_state == 2 ? 'danger' : '')),
            'title' => $sending_time . ' - ' . ($sendmail_state == 1 ? $this->l('Email sent successfully', 'AdminEtsACCartController') : ($sendmail_state == 2 ? $this->l('Email was failed to send', 'AdminEtsACCartController') : $this->l('Mail is in queue', 'AdminEtsACCartController')))
        );
        return EtsAbancartTools::displayText($time_elapsed_string, 'span', $attrs);
    }

    public function displayCustomerName($customer_name, $tr)
    {
        if (!isset($tr['id_customer']) || !(int)$tr['id_customer'])
            return $customer_name;
        if ($this->module->ver_min_1760) {
            try {
                $href = $this->context->link->getAdminLink('AdminCustomers', true, array('route' => 'admin_customers_view', 'customerId' => (int)$tr['id_customer']), array('viewcustomer' => '', 'id_customer' => (int)$tr['id_customer']));
            } catch (Exception $ex) {
                $href = $this->context->link->getAdminLink('AdminCustomers', true, array(), array('viewcustomer' => '', 'id_customer' => (int)$tr['id_customer']));
            }
        } else {
            $href = $this->context->link->getAdminLink('AdminCustomers', true) . '?viewcustomer&id_customer=' . (int)$tr['id_customer'];
        }
        $attrs = [
            'href' => $href,
            'target' => '_bank',
            'class' => 'ets_ab_customer_link',
        ];
        return $this->displayOnline($tr['id_guest']) . ' ' . EtsAbancartTools::displayText($customer_name, 'a', $attrs);
    }

    public function displayOnline($value)
    {
        if ((int)Configuration::get('PS_GUEST_CHECKOUT_ENABLED') < 1) {
            return '';
        }
        $label = $value ? $this->l('Yes', 'AdminEtsACCartController') : $this->l('No', 'AdminEtsACCartController');
        $attrs = array(
            'class' => 'badge badge-' . ($value ? 'success' : 'danger') . ' value_' . strtolower($label),
        );
        return EtsAbancartTools::displayText($label, 'span', $attrs);
    }

    public function initToolbarTitle()
    {
        if (!$this->display || $this->display == 'view') {
            $this->toolbar_title = array($this->l('Abandoned carts', 'AdminEtsACCartController', null, null, false));
            if (is_array($this->meta_title)) {
                $this->meta_title = array($this->l('Abandoned carts', 'AdminEtsACCartController', null, null, false));
            }
            if ($filter = $this->addFiltersToBreadcrumbs()) {
                $this->toolbar_title[] = $filter;
            }
        } else {
            parent::initToolbarTitle();
        }
    }

    public function setMedia($isNewTheme = false)
    {
        Media::addJsDef([
            'SELECT_ITEM_EMPTY_MESSAGE' => $this->l('Select a shopping cart to send your email reminder!', 'AdminEtsACCartController')
        ]);
        parent::setMedia($isNewTheme);

        $this->addJS(array(
            _PS_JS_DIR_ . 'admin/tinymce.inc.js',
            _PS_JS_DIR_ . 'tiny_mce/tiny_mce.js',
            _PS_JS_DIR_ . 'jquery/plugins/autocomplete/jquery.autocomplete.js',
        ));
    }

    protected function loadObject($opt = false)
    {
        if (empty($this->className)) {
            return true;
        }
        if ($opt)
            $id = $opt;
        else
            $id = (int)Tools::getValue($this->identifier);
        $this->object = new $this->className($id);

        return $this->object;
    }

    private function getFields($lang = false)
    {
        return array(
            //hidden.
            'id_cart' => array(
                'name' => 'id_cart',
                'label' => $this->l('Id', 'AdminEtsACCartController'),
                'type' => 'hidden',
                'default_value' => $this->id_object,
            ),
            //discount.
            'discount_option' => array(
                'name' => 'discount_option',
                'label' => $this->l('Discount options', 'AdminEtsACCartController'),
                'type' => 'radios',
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' => 'no',
                            'name' => $this->l('No discount', 'AdminEtsACCartController')
                        ),
                        array(
                            'id_option' => 'fixed',
                            'name' => $this->l('Fixed discount code', 'AdminEtsACCartController'),
                            'cart_rule_link' => $this->context->link->getAdminLink('AdminCartRules')
                        ),
                        array(
                            'id_option' => 'auto',
                            'name' => $this->l('Generate discount code automatically', 'AdminEtsACCartController')
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name',
                ),
                'default_value' => 'auto',
                'form_group_class' => 'abancart form_discount discount_option is_parent1',
            ),
            'quantity' => array(
                'name' => 'quantity',
                'label' => $this->l('Total available', 'AdminEtsACCartController'),
                'hint' => $this->l('The cart rule will be applied to the first', 'AdminEtsACCartController'),
                'type' => 'text',
                'col' => '2',
                'validate' => 'isUnsignedInt',
                'form_group_class' => 'abancart form_discount discount_option auto ets_ac_discount_qty',
                'default_value' => 1,
            ),
            'quantity_per_user' => array(
                'name' => 'quantity_per_user',
                'label' => $this->l('Total available for each user', 'AdminEtsACCartController'),
                'hint' => $this->l('A customer will only be able to use the cart rule', 'AdminEtsACCartController'),
                'type' => 'text',
                'col' => '2',
                'validate' => 'isUnsignedInt',
                'form_group_class' => 'abancart form_discount discount_option auto ets_ac_discount_qty',
                'default_value' => 1,
            ),
            'discount_code' => array(
                'name' => 'discount_code',
                'label' => $this->l('Discount code', 'AdminEtsACCartController'),
                'type' => 'text',
                'col' => '2',
                'required' => true,
                'validate' => 'isCleanHtml',
                'form_group_class' => 'abancart form_discount discount_option fixed isCleanHtml required',
            ),
            'free_shipping' => array(
                'name' => 'free_shipping',
                'label' => $this->l('Free shipping', 'AdminEtsACCartController'),
                'type' => 'switch',
                'default_value' => 0,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'AdminEtsACCartController')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No', 'AdminEtsACCartController')
                    ),
                ),
                'form_group_class' => 'abancart form_discount discount_option auto is_parent2',
            ),
            'apply_discount' => array(
                'name' => 'apply_discount',
                'label' => $this->l('Apply a discount', 'AdminEtsACCartController'),
                'type' => 'radios',
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' => 'percent',
                            'name' => $this->l('Percentage (%)', 'AdminEtsACCartController')
                        ),
                        array(
                            'id_option' => 'amount',
                            'name' => $this->l('Amount', 'AdminEtsACCartController')
                        ),
                        array(
                            'id_option' => 'off',
                            'name' => $this->l('None', 'AdminEtsACCartController')
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name',
                ),
                'default_value' => 'off',
                'form_group_class' => 'abancart form_discount discount_option auto apply_discount is_parent2',
            ),
            'reduction_amount' => array(
                'name' => 'reduction_amount',
                'label' => $this->l('Amount', 'AdminEtsACCartController'),
                'type' => 'text',
                'col' => '6',
                'default_value' => '0',
                'currencies' => Currency::getCurrencies(),
                'tax' => array(
                    array(
                        'id_option' => 0,
                        'name' => $this->l('Tax excluded', 'AdminEtsACCartController')
                    ),
                    array(
                        'id_option' => 1,
                        'name' => $this->l('Tax included', 'AdminEtsACCartController')
                    ),
                ),
                'required' => true,
                'validate' => 'isUnsignedFloat',
                'form_group_class' => 'abancart form_discount discount_option auto apply_discount amount isUnsignedFloat required',
            ),
            'discount_name' => array(
                'name' => 'discount_name',
                'label' => $this->l('Discount name', 'AdminEtsACCartController'),
                'type' => 'text',
                'lang' => true,
                'required' => true,
                'col' => 6,
                'validate' => 'isCleanHtml',
                'form_group_class' => 'abancart form_discount discount_option auto isCleanHtml required'
            ),
            'discount_prefix' => array(
                'name' => 'discount_prefix',
                'label' => $this->l('Discount prefix', 'AdminEtsACCartController'),
                'type' => 'text',
                'default_value' => 'AC_',
                'required' => true,
                'col' => 2,
                'validate' => 'isCleanHtml',
                'form_group_class' => 'abancart form_discount discount_option auto isCleanHtml required'
            ),
            'reduction_percent' => array(
                'name' => 'reduction_percent',
                'label' => $this->l('Discount percentage', 'AdminEtsACCartController'),
                'type' => 'text',
                'suffix' => '%',
                'col' => '2',
                'required' => true,
                'validate' => 'isPercentage',
                'desc' => $this->l('Does not apply to the shipping costs', 'AdminEtsACCartController'),
                'form_group_class' => 'abancart form_discount discount_option auto apply_discount percent isPercentage required',
            ),
            'apply_discount_to' => array(
                'name' => 'apply_discount_to',
                'label' => $this->l('Apply a discount to', 'AdminEtsACCartController'),
                'type' => 'radios',
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' => 'order',
                            'name' => $this->l('Order (without shipping)', 'AdminEtsACCartController'),
                        ),
                        array(
                            'id_option' => 'specific',
                            'name' => $this->l('Specific product', 'AdminEtsACCartController')
                        ),
                        array(
                            'id_option' => 'cheapest',
                            'name' => $this->l('Cheapest product', 'AdminEtsACCartController')
                        ),
                        array(
                            'id_option' => 'selection',
                            'name' => $this->l('Selected product(s)', 'AdminEtsACCartController')
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name',
                ),
                'default_value' => 'order',
                'form_group_class' => 'abancart form_discount discount_option auto apply_discount percent amount ets_ac_apply_discount',
            ),
            'reduction_product' => array(
                'name' => 'reduction_product',
                'label' => $this->l('Product', 'AdminEtsACCartController'),
                'type' => 'text',
                'col' => '2',
                'specific_product' => true,
                'form_group_class' => 'abancart form_discount discount_option auto apply_discount percent amount ets_ac_specific_product_group',
            ),
            'selected_product' => array(
                'name' => 'selected_product',
                'label' => $this->l('Search product', 'AdminEtsACCartController'),
                'type' => 'text',
                'col' => '2',
                'search_product' => true,
                'form_group_class' => 'abancart form_discount discount_option auto apply_discount percent ets_ac_selected_product_group',
            ),
            'reduction_exclude_special' => array(
                'name' => 'reduction_exclude_special',
                'label' => $this->l('Exclude discounted products', 'AdminEtsACCartController'),
                'type' => 'switch',
                'default_value' => 1,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'AdminEtsACCartController')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No', 'AdminEtsACCartController')
                    ),
                ),
                'form_group_class' => 'abancart form_discount discount_option auto apply_discount percent',
            ),
            'free_gift' => array(
                'name' => 'free_gift',
                'label' => $this->l('Send a free gift', 'AdminEtsACCartController'),
                'type' => 'switch',
                'default_value' => 0,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'AdminEtsACCartController')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No', 'AdminEtsACCartController')
                    ),
                ),
                'form_group_class' => 'abancart form_discount discount_option auto',
            ),
            'product_gift' => array(
                'name' => 'product_gift',
                'label' => $this->l('Search a product', 'AdminEtsACCartController'),
                'type' => 'text',
                'suffix' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'ets_abandonedcart/views/templates/hook/icon_search.tpl'),
                'col' => '2',
                'form_group_class' => 'abancart form_discount discount_option auto apply_discount percent amount off ets_ac_gift_product_filter_group',
            ),

            'id_currency' => array(
                'name' => 'id_currency',
                'label' => $this->l('Id currency', 'AdminEtsACCartController'),
                'type' => 'select',
                'options' => array(
                    'query' => Currency::getCurrencies(),
                    'id' => 'id_currency',
                    'name' => 'name',
                ),
                'default_value' => $this->context->currency->id,
                'form_group_class' => 'abancart form_discount'
            ),
            'reduction_tax' => array(
                'name' => 'reduction_tax',
                'label' => $this->l(''),
                'type' => 'select',
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' => 0,
                            'name' => $this->l('Tax excluded', 'AdminEtsACCartController')
                        ),
                        array(
                            'id_option' => 1,
                            'name' => $this->l('Tax included', 'AdminEtsACCartController')
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name',
                ),
                'default_value' => '0',
                'form_group_class' => 'abancart form_discount'
            ),
            'apply_discount_in' => array(
                'name' => 'apply_discount_in',
                'label' => $this->l('Discount availability', 'AdminEtsACCartController'),
                'type' => 'text',
                'required' => 'true',
                'suffix' => $this->l('days', 'AdminEtsACCartController'),
                'validate' => 'isUnsignedFloat',
                'col' => '2',
                'default_value' => 7,
                'form_group_class' => 'abancart form_discount discount_option auto apply_discount is_parent2 isUnsignedFloat required',
                'desc' => $this->l('Please enter the number of days available for the discount code. You can enter decimal values with up to 2 digits after the decimal point (.). Example: 1.50, 2.0', 'AdminEtsACCartController')
            ),
            'highlight_discount' => array(
                'name' => 'highlight_discount',
                'label' => $this->l('Highlight?', 'AdminEtsACCartController'),
                'hint' => $this->l('If the voucher is not yet in the cart, it will be displayed in the cart summary.', 'AdminEtsACCartController'),
                'type' => 'switch',
                'default_value' => 0,
                'values' => array(
                    array(
                        'id' => 'enable_highlight_discount_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'AdminEtsACCartController')
                    ),
                    array(
                        'id' => 'enable_highlight_discount_off',
                        'value' => 0,
                        'label' => $this->l('No', 'AdminEtsACCartController')
                    ),
                ),
                'form_group_class' => 'abancart form_discount discount_option fixed auto ets_ac_discount_qty',
            ),
            'allow_multi_discount' => array(
                'name' => 'allow_multi_discount',
                'label' => $this->l('Can use with other voucher in the same shopping cart?', 'AdminEtsACCartController'),
                'type' => 'switch',
                'default_value' => 0,
                'values' => array(
                    array(
                        'id' => 'enable_multi_discount_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'AdminEtsACCartController')
                    ),
                    array(
                        'id' => 'enable_multi_discount_off',
                        'value' => 0,
                        'label' => $this->l('No', 'AdminEtsACCartController')
                    ),
                ),
                'form_group_class' => 'abancart form_discount discount_option fixed auto ets_ac_discount_qty',
            ),
            //end discount
            //template.
            'hidden_reminder_id' => [
                'name' => 'hidden_reminder_id',
                'type' => 'hidden',
                'label' => $this->l('Reminder', 'AdminEtsACCartController'),
                'default_value' => 0,
            ],
            'id_ets_abancart_email_template' => array(
                'name' => 'id_ets_abancart_email_template',
                'label' => $this->l('Email templates', 'AdminEtsACCartController'),
                'type' => 'hidden',
            ),
            'title' => array(
                'name' => 'title',
                'label' => $this->l('Subject', 'AdminEtsACCartController'),
                'type' => 'text',
                'lang' => $lang,
                'required' => true,
                'validate' => 'isMailSubject',
                'default' => [
                    'origin' => 'You left something in your cart!',
                    'trans' => $this->l('You left something in your cart!', 'AdminEtsACCartController'),
                ],
                'form_group_class' => 'abancart form_message isMailSubject required'
            ),
            'content' => array(
                'name' => 'content',
                'label' => $this->l('Email content', 'AdminEtsACCartController'),
                'type' => 'textarea',
                'autoload_rte' => true,
                'lang' => $lang,
                'required' => true,
                'desc_type' => 'cart',
                'validate' => 'isCleanHtml',
                'form_group_class' => 'abancart content form_message isCleanHtml required'
            ),
            //end template.
            'enabled' => array(
                'type' => 'hidden',
                'name' => 'enabled',
                'label' => $this->l('Send email now?', 'AdminEtsACCartController'),
                'default_value' => 1,
                'form_group_class' => 'abancart form_confirm_information form_abandoned_cart'
            )
        );
    }

    public function initProcess()
    {
        parent::initProcess();

        if (($action = Tools::getValue('action')) && Validate::isCleanHtml($action) && trim($action) === 'sendMail' || (bool)Tools::isSubmit('sendmail')) {
            if ($this->access('edit')) {
                $this->fields_form = array(
                    'legend' => array(
                        'title' => $this->l('Abandoned cart', 'AdminEtsACCartController'),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save', 'AdminEtsACCartController'),
                    ),
                    'input' => $this->getFields(Tools::getValue('ids_cart', [])),
                );
                $this->action = $this->display = 'sendmail';
            } else {
                $this->errors[] = $this->l('You do not have permission to send this email.', 'AdminEtsACCartController');
            }
        }
    }

    public function ajaxProcessSendBulkReminder()
    {
        $ids_cart = Tools::getValue('ids_cart', []);
        if (!is_array($ids_cart))
            $ids_cart = explode(',', $ids_cart);
        if (empty($ids_cart)) {
            die(json_encode([
                'errors' => $this->l('Shopping cart is empty', 'AdminEtsACCartController')
            ]));
        }
        $this->ajaxProcessFormSendMail($ids_cart);
    }

    private function sendMail($id_cart, &$errors = [])
    {
        $this->loadObject($id_cart);
        $context = Context::getContext();
        $keeps = [
            'currency' => $context->currency,
            'shop' => $context->shop,
            'cart' => $context->cart,
            'customer' => $context->customer,
        ];
        $cart = new Cart((int)$id_cart);
        $context->cart = $cart;
        $context->shop = new Shop($cart->id_shop);
        if (!Validate::isLoadedObject($cart)) {
            $errors[] = $this->l('Cart does not exist!', 'AdminEtsACCartController');
        }
        if (count($errors) < 1) {

            $object = new EtsAbancartReminder();
            $this->validateRules('EtsAbancartReminder');
            $this->copyFromPost($object, 'ets_abancart_reminder');

            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            $language = new Language((int)$cart->id_lang);
            if (!$language->id) {
                $language = new Language($id_lang_default);
            }
            if ($customer = new Customer($cart->id_customer)) {
                $this->context->customer = $customer;
            }

            $template = 'abandoned_cart';
            if (!$customer->id) {
                $errors[] = $this->l('Customer does not exist!', 'AdminEtsACCartController');
            } elseif (!@glob(($destTempMail = $this->module->getLocalPath() . 'mails/' . $language->iso_code . '/' . $template . '*[.txt|.html]'))) {
                if ($this->module->_installMail($language) && !glob($destTempMail))
                    $errors[] = sprintf($this->l('Error - The following email template is missing: %s', 'AdminEtsACCartController'), $template);
            } elseif (!Validate::isEmail($customer->email)) {
                $errors[] = $this->l('Error: invalid email address', 'AdminEtsACCartController');
            }

            if (!count($errors)) {
                $cart_rule = new CartRule();
                if (($discount_option = Tools::getValue('discount_option')) && trim($discount_option) === 'auto') {
                    $cart_rule = $this->module->addCartRule($object, $customer->id);
                    if (!is_object($cart_rule) || !$cart_rule instanceof CartRule) {
                        $errors[] = implode($this->module->displayText('', 'br'), $cart_rule);
                    }
                } elseif ($discount_option != 'no' && ($discount_code = Tools::getValue('discount_code')) && Validate::isCleanHtml($discount_code)) {
                    $id_cart_rule = (int)CartRule::getIdByCode($discount_code);
                    if ($id_cart_rule <= 0) {
                        $errors[] = $this->l('Discount code does not exist', 'AdminEtsACCartController');
                    } else
                        $cart_rule = new CartRule($id_cart_rule);
                }
                $contentHtml = is_array($object->content) ? $object->content[$language->id] : $object->content;
                if ((int)Tools::getValue('preview')) {
                    $this->toJson([
                        'preview' => $this->module->doShortCode($contentHtml, 'cart', $cart_rule, $context)
                    ]);
                }
                if ($object->enabled) {
                    $url_params = array(
                        'id_cart' => $cart->id,
                        'mtime' => microtime()
                    );
                    if (!count($errors) && !EtsAbancartMail::Send(
                            $language->id,
                            $template,
                            is_array($object->title) ? $object->title[$language->id] : $object->title,
                            array(
                                '{tracking}' => $context->link->getModuleLink($this->module->name, 'image', ['rewrite' => EtsAbancartTools::getInstance()->encrypt(json_encode($url_params))], Configuration::get('PS_SSL_ENABLED_EVERYWHERE')),
                                '{content}' => $this->module->doShortCode($contentHtml, 'cart', $cart_rule, $context, null, $url_params),
                                '{url_params}' => json_encode($url_params),
                            ),
                            Tools::strtolower($customer->email),
                            $customer->firstname . ' ' . $customer->lastname,
                            null, null, null, null, $this->module->getLocalPath() . 'mails/')
                    ) {
                        $errors[] = $this->l('Sending email failed.', 'AdminEtsACCartController');
                    }
                } else {
                    $this->toJson(array(
                        'msg' => $this->l('No email to send', 'AdminEtsACCartController'),
                        'errors' => false
                    ));
                }

                $tracking = new EtsAbancartTracking();
                $tracking->id_cart = (int)$context->cart->id;
                $tracking->id_customer = $context->customer->id;
                $tracking->email = $context->customer->email;
                $tracking->id_shop = $context->shop->id;
                $tracking->display_times = date('Y-m-d H:i:s');
                $tracking->total_execute_times = 1;
                $tracking->id_ets_abancart_reminder = -1;
                $tracking->ip_address = ($ip_address = Tools::getRemoteAddr()) && $ip_address == '::1' ? '127.0.0.1' : $ip_address;
                $tracking->delivered = count($errors) > 0 ? 0 : 1;
                if ($tracking->save(true) && $cart_rule->id > 0 && trim($discount_option) === 'auto') {
                    EtsAbancartTracking::trackingDiscount($tracking->id, $cart_rule->id, (int)Tools::getValue('allow_multi_discount', 0) ? 1 : 0);
                }
            }
        }

        foreach ($keeps as $key => $keep) {
            $context->{$key} = $keep;
        }

        return !count($errors);
    }

    public function ajaxProcessSendMail()
    {
        $ids_cart = Tools::getValue('ids_cart', []);
        if (!is_array($ids_cart))
            $ids_cart = explode(',', $ids_cart);

        if (empty($ids_cart) && ($cartId = (int)Tools::getValue($this->identifier)))
            $ids_cart[] = $cartId;

        $count = 0;
        $errors = [];
        if ($ids_cart) {
            foreach ($ids_cart as $id_cart) {
                $errors = [];
                if ($this->sendMail($id_cart, $errors))
                    $count++;
            }
        }
        $hasError = count($errors) > 0;
        $this->toJson([
            'errors' => $hasError ? $this->module->displayError($errors) : false,
            'list' => $this->renderList(),
            'msg' => !$hasError ? sprintf($this->l('Sent %d email(s) successfully', 'AdminEtsACCartController'), $count) : false,
        ]);
    }

    public function renderList()
    {
        $html = EtsAbancartTools::displayText(EtsAbancartTools::displayText('<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 710v794q0 66-47 113t-113 47h-1472q-66 0-113-47t-47-113v-794q44 49 101 87 362 246 497 345 57 42 92.5 65.5t94.5 48 110 24.5h2q51 0 110-24.5t94.5-48 92.5-65.5q170-123 498-345 57-39 100-87zm0-294q0 79-49 151t-122 123q-376 261-468 325-10 7-42.5 30.5t-54 38-52 32.5-57.5 27-50 9h-2q-23 0-50-9t-57.5-27-52-32.5-54-38-42.5-30.5q-91-64-262-182.5t-205-142.5q-62-42-117-115.5t-55-136.5q0-78 41.5-130t118.5-52h1472q65 0 112.5 47t47.5 113z"/></svg>', 'span') . ' ' . $this->l('Send bulk reminder', 'AdminEtsACCartController'), 'a', ['class' => 'sendBulkReminder btn btn-default', 'href' => self::$currentIndex . '&action=sendBulkReminder&token=' . $this->token]);
        return $html . parent::renderList();
    }

    public function ajaxProcessFormSendMail($ids_cart = false)
    {
        if ($this->access('edit')) {
            $this->tpl_form_vars = array(
                'email_templates' => EtsAbancartEmailTemplate::getTemplates(null, 'email', null, $this->context),
                'menus' => EtsAbancartReminderForm::getInstance()->getReminderSteps(),
                'lead_forms' => EtsAbancartForm::getAllForms(false, true),
                'maxSizeUpload' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
                'baseUri' => __PS_BASE_URI__,
                'field_types' => EtsAbancartField::getInstance()->getFieldType(),
                'module_dir' => _PS_MODULE_DIR_ . $this->module->name,
                'is17Ac' => $this->module->is17,
                'image_url' => $this->context->shop->getBaseURL(true) . 'img/' . $this->module->name . '/img/',
                'short_codes' => EtsAbancartDefines::getInstance()->getFields('short_codes'),
                'short_code_urls' => EtsAbancartDefines::getInstance()->getFields('short_code_urls'),
            );

            $this->toJson(array(
                'html' => $this->renderForm($ids_cart)
            ));
        }
    }

    public function ajaxProcessSelectTemplate()
    {
        if ($this->access('edit')) {
            $object = new EtsAbancartEmailTemplate((int)Tools::getValue('id_ets_abancart_email_template'));
            $languages = Language::getLanguages(false);
            $mailContent = array();
            $idLangDefault = Configuration::get('PS_LANG_DEFAULT');
            $mailDirDefault = _ETS_AC_MAIL_UPLOAD_DIR_ . '/' . ($object->folder_name ?: $object->id) . '/' . $object->temp_path[$idLangDefault];

            foreach ($languages as $lang) {
                $mailDir = _ETS_AC_MAIL_UPLOAD_DIR_ . '/' . ($object->folder_name ?: $object->id) . '/' . $object->temp_path[$lang['id_lang']];
                if (file_exists($mailDir)) {
                    $mailContent[$lang['id_lang']] = EtsAbancartEmailTemplate::getBodyMailTemplate($mailDir, $this);
                } elseif (file_exists($mailDirDefault)) {
                    $mailContent[$lang['id_lang']] = EtsAbancartEmailTemplate::getBodyMailTemplate($mailDirDefault, $this);
                } else {
                    $mailContent[$lang['id_lang']] = '';
                }
            }
            $this->toJson(array(
                'html' => $object->id > 0 ? $mailContent : '',
            ));
        }
    }

    public function ajaxProcessReminderLog()
    {
        if ($this->access('edit')) {

            $this->loadObject();

            $id_cart = (int)Tools::getValue($this->identifier);
            $id_customer = (int)Tools::getValue('id_customer');
            $tpl_vars = [];
            $idCurrency = 0;
            if ($id_cart > 0) {
                $cart = new Cart($id_cart);
                $idLang = $cart->id_lang;
                $idCurrency = $cart->id_currency;
            } else {
                $customer = new Customer($id_customer);
                $idLang = $customer->id_lang;
            }
            if ($logs = EtsAbancartTracking::getLogs($id_cart, $id_customer, $idLang)) {
                $LOGs = array();
                foreach ($logs as &$log) {
                    $LOGs[] = EtsAbancartReminderForm::getInstance()->propertiesTracking($log['id_ets_abancart_reminder'], $log['reminder_name'], $log['id_cart_rule'], $idLang, $idCurrency, $log['template_name'], $log['display_times'], $log['total_execute_times']);
                }
                $tpl_vars['LOGs'] = $LOGs;
            }
            $recoverCart = (int)Tools::getValue('recover_cart');
            if ($recoverCart <= 0)
                $tpl_vars['next_mails_time'] = EtsAbancartReminder::getNextMailTime($id_cart, false);
            else
                $tpl_vars['recover_cart'] = $recoverCart;
            if ($tpl_vars)
                $this->context->smarty->assign($tpl_vars);

            $this->toJson(array(
                'html' => $this->createTemplate('logs.tpl')->fetch(),
            ));
        }
    }

    public function renderForm($ids_cart = false)
    {
        $this->loadObject();
        if ($ids_cart) {
            $fields = $this->getFields(true);
            $fields['ids_cart'] = [
                'name' => 'ids_cart',
                'label' => $this->l('Cart ID', 'AdminEtsACCartController'),
                'type' => 'hidden',
                'default_value' => $ids_cart,
            ];
            $this->fields_form = array(
                'legend' => array(
                    'title' => $this->l('Abandoned cart', 'AdminEtsACCartController'),
                ),
                'submit' => array(
                    'title' => $this->l('Save', 'AdminEtsACCartController'),
                ),
                'input' => $fields,
            );
        } else {
            $id_cart = (int)Tools::getValue('id_cart');
            $cart = new Cart($id_cart);
            $lang = new Language($cart->id_lang);
            if ($lang->iso_code) {
                $this->fields_form['input']['title']['default_value'] = $this->module->getTextLang($this->fields_form['input']['title']['default']['origin'], $lang, 'AdminEtsACCartController');
                $this->tpl_form_vars['id_lang_default'] = $lang->id;
            }
            $this->tpl_form_vars['PS_LANG_DEFAULT'] = Configuration::get('PS_LANG_DEFAULT');
        }
        $this->fields_form['buttons'] = array(
            'back' => array(
                'href' => self::$currentIndex . '&token=' . $this->token,
                'title' => $this->l('Back to list', 'AdminEtsACCartController'),
                'icon' => 'process-icon-back',
                'class' => 'ets_abancart_process_back',
            ),
        );

        self::$currentIndex .= (Tools::isSubmit('add' . $this->list_id) ? '&add' . $this->list_id : '') . (Tools::isSubmit('update' . $this->list_id) ? '&update' . $this->list_id : '');

        return parent::renderForm();
    }

    public function ajaxProcessWriteNote()
    {
        $cart_id = Tools::getValue('cart_id');
        if (!$cart_id || !Validate::isUnsignedInt($cart_id)) {
            die(json_encode([
                'errors' => $this->l('Your cart does not exist.', 'AdminEtsACCartController'),
            ]));
        }
        $note = Tools::getValue('note');
        if (!$note || trim($note) == '') {
            $this->errors[] = $this->l('The \'note\' field cannot be left empty.', 'AdminEtsACCartController');
        } elseif (!Validate::isMessage($note)) {
            $this->errors[] = $this->l('The \'note\' field is invalid.', 'AdminEtsACCartController');
        } elseif (!EtsAbancartNoteManual::writeNote($cart_id, $note)) {
            $this->errors[] = $this->l('Failed to save note, please check again.', 'AdminEtsACCartController');
        }
        $hasError = count($this->errors) > 0;
        die(json_encode([
            'errors' => $hasError ? implode(PHP_EOL, $this->errors) : false,
            'cart_id' => $cart_id,
            'note' => $note,
            'msg' => $this->l('Note saved successfully.', 'AdminEtsACCartController'),
        ]));
    }

    public function ajaxProcessRenderFormNote()
    {
        $id_cart = Tools::getValue('id_cart');
        if (!$id_cart || !Validate::isUnsignedInt($id_cart)) {
            die(json_encode([
                'errors' => $this->l('Your cart does not exist.', 'AdminEtsACCartController'),
            ]));
        }
        $cart = new Cart($id_cart);
        $this->context->smarty->assign([
            'currentIndex' => self::$currentIndex . '&' . $this->identifier . '=' . $id_cart . '&token=' . $this->token,
            'cart_id' => $cart->id,
            'cart_date_add' => $cart->date_add,
            'cart_total' => self::getOrderTotalUsingTaxCalculationMethod($id_cart),
            'cart_note' => EtsAbancartNoteManual::getNote($id_cart),
        ]);
        die(json_encode([
            'html' => $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/etsac_cart/form-note.tpl'),
        ]));
    }

    public function displayWriteNoteLink($token, $id)
    {
        if (!isset(self::$cache_lang['write_note'])) {
            self::$cache_lang['write_note'] = $this->l('Write note', 'AdminEtsACCartController');
        }
        $attrs = array(
            'href' => self::$currentIndex . '&' . $this->identifier . '=' . $id . '&token=' . ($token != null ? $token : $this->token),
            'title' => self::$cache_lang['write_note'],
            'token' => $token,
            'class' => 'ets_abancart_write_note',
        );
        return EtsAbancartTools::displayText(EtsAbancartTools::displayText('', 'i', ['class' => 'icon-pencil']) . ' ' . self::$cache_lang['write_note'], 'a', $attrs);
    }

    public function displayViewCartLink($token, $id)
    {
        if (!isset(self::$cache_lang['view'])) {
            self::$cache_lang['view'] = $this->l('View cart', 'AdminEtsACCartController');
        }
        $attrs = array(
            'href' => $this->context->link->getAdminLink('AdminCarts') . '&id_cart=' . $id . '&viewcart',
            'title' => self::$cache_lang['view'],
            'token' => $token,
            'target' => '_blank'
        );
        return EtsAbancartTools::displayText(EtsAbancartTools::displayText('', 'i', ['class' => 'icon-eye']) . ' ' . self::$cache_lang['view'], 'a', $attrs);
    }

    public function displaySendMailLink($token, $id)
    {
        $cart = new Cart($id);
        if (!isset(self::$cache_lang['sendmail'])) {
            self::$cache_lang['sendmail'] = $this->l('Send reminder', 'AdminEtsACCartController');
        }
        $attrs = array(
            'href' => $cart->id_customer ? self::$currentIndex . '&' . $this->identifier . '=' . $id . '&sendmail&token=' . ($token != null ? $token : $this->token) : 'javascript:void(0)',
            'title' => self::$cache_lang['sendmail'],
            'class' => 'ets_abancart_sendmail btn btn-default' . (!$cart->id_customer ? ' disabled' : ''),
        );
        if (!$cart->id_customer)
            $attrs['disabled'] = 'disabled';

        return EtsAbancartTools::displayText(EtsAbancartTools::displayText('<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 710v794q0 66-47 113t-113 47h-1472q-66 0-113-47t-47-113v-794q44 49 101 87 362 246 497 345 57 42 92.5 65.5t94.5 48 110 24.5h2q51 0 110-24.5t94.5-48 92.5-65.5q170-123 498-345 57-39 100-87zm0-294q0 79-49 151t-122 123q-376 261-468 325-10 7-42.5 30.5t-54 38-52 32.5-57.5 27-50 9h-2q-23 0-50-9t-57.5-27-52-32.5-54-38-42.5-30.5q-91-64-262-182.5t-205-142.5q-62-42-117-115.5t-55-136.5q0-78 41.5-130t118.5-52h1472q65 0 112.5 47t47.5 113z"/></svg>', 'span') . ' ' . self::$cache_lang['sendmail'], 'a', $attrs);
    }

    public function displayReminderLogLink($token, $id)
    {
        if (!isset(self::$cache_lang['reminder_log'])) {
            self::$cache_lang['reminder_log'] = $this->l('View reminder log', 'AdminEtsACCartController');
        }
        $attrs = array(
            'href' => self::$currentIndex . '&' . $this->identifier . '=' . $id . '&reminderlog&token=' . ($token != null ? $token : $this->token),
            'title' => self::$cache_lang['reminder_log'],
            'class' => 'ets_abancart_reminder_log',
        );

        return EtsAbancartTools::displayText(EtsAbancartTools::displayText('', 'i', ['class' => 'icon-file']) . ' ' . self::$cache_lang['reminder_log'], 'a', $attrs);
    }

    public function displaySessionLink($token, $id)
    {
        if (!isset(self::$cache_lang['view_session'])) {
            self::$cache_lang['view_session'] = $this->l('View session', 'AdminEtsACCartController');
        }
        $attrs = array(
            'href' => $this->context->link->getAdminLink('AdminTrackingCustomerSession') . '&' . $this->identifier . '=' . $id . '&current_tab=customer_session',
            'title' => self::$cache_lang['view_session'],
            'class' => 'ets_view_session',
            'token' => $token,
            'target' => '_blank',
        );

        return EtsAbancartTools::displayText(EtsAbancartTools::displayText('', 'i', ['class' => 'icon-search-plus']) . ' ' . self::$cache_lang['view_session'], 'a', $attrs);
    }
}