<?php
/**
 * 2007-2023 ETS-Soft
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
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2023 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
    exit;

class Ode_export extends ObjectModel
{
    public $id_ets_export_order_rule;
    public $exported_fields;
    public $date_type;
    public $from_date;
    public $to_date;
    public $day_before;
    public $order_status;
    public $customer_group;
    public $specific_customer;
    public $specific_order;
    public $id_country;
    public $id_state;
    public $order_carrier;
    public $payment_method;
    public $manufacturer;
    public $supplier;
    public $category;
    public $specific_product;
    public $spent_from;
    public $spent_to;
    public $order_total;
    public $file_format;
    public $file_name_prefix;
    public $file_name_incl_name_rule;
    public $sort_by;
    public $convert_in_currency;
    public $receivers_mail;
    public $position;
    public $name;
    public $title_mail;
    public $description_mail;
    public $send_file_via_email;
    public $send_file_schedule;
    public $send_file_time;
    public $send_file_time_hours;
    public $send_file_time_weeks;
    public $send_file_time_months;
    public $send_file_date;
    public $send_file_filter;
    public $export_to_server1;
    public $directory_path1;
    public $server1_schedule;
    public $server1_time;
    public $server1_filter;
    public $server1_time_hours;
    public $server1_time_weeks;
    public $server1_time_months;
    public $server1_date;
    public $export_to_server2;
    public $global_ftp;
    public $host;
    public $username;
    public $password;
    public $port;
    public $directory_path2;
    public $server2_schedule;
    public $server2_time;
    public $server2_filter;
    public $server2_time_hours;
    public $server2_time_weeks;
    public $server2_time_months;
    public $server2_date;
    public $delete_exported_files;
    public static $definition = array(
        'table' => 'ets_export_order_rule',
        'primary' => 'id_ets_export_order_rule',
        'multilang' => true,
        'fields' => array(
            'exported_fields' => array('type' => self::TYPE_STRING),
            'date_type' => array('type' => self::TYPE_STRING),
            'from_date' => array('type' => self::TYPE_STRING),
            'to_date' => array('type' => self::TYPE_STRING),
            'day_before' => array('type' => self::TYPE_STRING),
            'order_status' => array('type' => self::TYPE_STRING),
            'send_file_via_email' => array('type' => self::TYPE_STRING),
            'customer_group' => array('type' => self::TYPE_STRING),
            'specific_customer' => array('type' => self::TYPE_STRING),
            'specific_order' => array('type' => self::TYPE_STRING),
            'id_country' => array('type' => self::TYPE_STRING),
            'id_state' => array('type' => self::TYPE_INT),
            'order_carrier' => array('type' => self::TYPE_STRING),
            'payment_method' => array('type' => self::TYPE_STRING),
            'manufacturer' => array('type' => self::TYPE_STRING),
            'supplier' => array('type' => self::TYPE_STRING),
            'category' => array('type' => self::TYPE_STRING),
            'specific_product' => array('type' => self::TYPE_STRING),
            'spent_from' => array('type' => self::TYPE_STRING),
            'spent_to' => array('type' => self::TYPE_STRING),
            'file_format' => array('type' => self::TYPE_STRING),
            'file_name_prefix' => array('type' => self::TYPE_STRING),
            'file_name_incl_name_rule' => array('type'=>self::TYPE_INT),
            'sort_by' => array('type' => self::TYPE_STRING),
            'convert_in_currency' => array('type'=>self::TYPE_INT),
            'receivers_mail' => array('type' => self::TYPE_STRING),
            'position' => array('type' => self::TYPE_INT),
            'title_mail' => array('type' => self::TYPE_STRING,'lang'=>true),
            'description_mail' => array('type' => self::TYPE_STRING,'lang'=>true),

            'send_file_schedule' => array('type' => self::TYPE_STRING),
            'send_file_time_hours' => array('type' => self::TYPE_STRING),
            'send_file_time_weeks' => array('type' => self::TYPE_STRING),
            'send_file_time_months' => array('type' => self::TYPE_STRING),
            'send_file_date' => array('type' => self::TYPE_DATE),

            'export_to_server1' => array('type' => self::TYPE_BOOL),
            'directory_path1' => array('type' => self::TYPE_STRING),
            'server1_schedule' => array('type' => self::TYPE_STRING),
            'server1_time_hours' => array('type' => self::TYPE_STRING),
            'server1_time_weeks' => array('type' => self::TYPE_STRING),
            'server1_time_months' => array('type' => self::TYPE_STRING),
            'server1_date' => array('type' => self::TYPE_DATE),

            'export_to_server2' => array('type' => self::TYPE_BOOL),
            'global_ftp' => array('type' => self::TYPE_BOOL),
            'host' => array('type' => self::TYPE_STRING),
            'username' => array('type' => self::TYPE_STRING),
            'port' => array('type' => self::TYPE_STRING),
            'password' => array('type' => self::TYPE_STRING),
            'directory_path2' => array('type' => self::TYPE_STRING),
            'server2_schedule' => array('type' => self::TYPE_STRING),
            'server2_time_hours' => array('type' => self::TYPE_STRING),
            'server2_time_weeks' => array('type' => self::TYPE_STRING),
            'server2_time_months' => array('type' => self::TYPE_STRING),
            'server2_date' => array('type' => self::TYPE_DATE),

            'delete_exported_files' => array('type' => self::TYPE_STRING),
            'send_file_filter' => array('type' => self::TYPE_STRING),
            'server1_filter' => array('type' => self::TYPE_STRING),
            'server2_filter' => array('type' => self::TYPE_STRING),
            //Lang fields
            'name' => array('type' => self::TYPE_STRING, 'lang' => true),
        )
    );
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
        if($this->exported_fields)
        {
            $fields = explode(',',$this->exported_fields);
            $orderMangage = Module::getInstanceByName('ets_ordermanager');
            foreach($fields as $key=> $field)
                if(!$orderMangage->getField($field))
                    unset($fields[$key]);
            $this->exported_fields = implode(',',$fields);
        }
    }
    public static function getExports($context)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        return Db::getInstance()->executeS('
            SELECT oe.id_ets_export_order_rule as `id`, oel.name
            FROM `' . _DB_PREFIX_ . 'ets_export_order_rule` oe
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_export_order_rule_lang` oel ON (oe.id_ets_export_order_rule = oel.id_ets_export_order_rule AND oel.id_lang = ' . (int)$context->language->id . ')
            INNER JOIN `' . _DB_PREFIX_ . 'ets_export_order_rule_shop` oes ON (oes.id_ets_export_order_rule = oe.id_ets_export_order_rule)
            WHERE oes.id_shop = '.(int)$context->shop->id.'
        ');
    }

    public function add($auto_date = true, $null_values = false)
    {
        $res = parent::add($auto_date, $null_values);
        if ($this->id) {
            $res &= Db::getInstance()->execute('
                INSERT INTO `' . _DB_PREFIX_ . 'ets_export_order_rule_shop` (`id_ets_export_order_rule`, `id_shop`)
                VALUES(' . (int)$this->id . ', ' . (int)Context::getContext()->shop->id . ')'
            );
        }
        return $res;
    }

    public function delete()
    {
        if (($res = parent::delete())) {
            $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_export_order_rule_shop` WHERE `id_ets_export_order_rule` = '.(int)$this->id);
        }
        return $res;
    }
    public static function getOrderFieldsValues($id_order)
    {
        $order = new Order($id_order);
        $fields = array();
        $fields['id_order'] = $order->id;
        $fields['reference'] = $order->reference;
        $fields['id_customer_order'] = $order->id_customer;
        $fields['id_carrier'] = $order->id_carrier;
        $fields['payment'] = $order->payment;
        $cart = new Cart($order->id_cart);
        if (!Tools::isSubmit('edit_customer')) {
            if ($cart->id)
                isset($cart->id);
            else {
                $cart->id_customer = $order->id_customer;
                $cart->id_address_delivery = $order->id_address_delivery;
                $cart->id_address_invoice = $order->id_address_invoice;
                $cart->id_currency = $order->id_currency;
            }
            $cart->add();
            $cart_products = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'cart_product` WHERE id_cart=' . (int)$order->id_cart);
            if ($cart_products) {
                foreach ($cart_products as $products) {
                    $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'cart_product` SET id_cart="' . (int)$cart->id . '"';
                    foreach ($products as $field => $value) {
                        if ($field != 'id_cart')
                            $sql .= ',' . pSQL($field) . '="' . pSQL($value) . '"';
                    }
                    Db::getInstance()->execute($sql);
                }
            }
        }
        $fields['id_cart'] = $cart->id;
        if ($order->id_customer) {
            $customer = new Customer($order->id_customer);
            $fields['customer'] = $customer->firstname . ' ' . $customer->lastname;
        }
        return $fields;
    }
    public static function updatePosition($payments)
    {
        if($payments)
        {
            foreach ($payments as $key => $id) {
                Db::getInstance()->execute('update `' . _DB_PREFIX_ . 'ets_export_order_rule` set position="' . (int)$key . '" WHERE id_ets_export_order_rule=' . (int)$id);
            }
        }
        return true;
    }
    public static function buildFields($fields,$list_fields)
    {
        if ($fields != '*') {
            $fields = explode(',', $fields);
            if ($fields && $list_fields) {
                foreach ($fields as &$field) {
                    if ($field == 'address') {
                        $field = 'IF(a.address1 is NOT NULL OR a.address1 != "", CONCAT(a.address1, ", ", a.city), CONCAT(a.address2, ", ", a.city)) as `address`';
                    } elseif ($field == 'a.phone' || $field == 'phone') {
                        $field = 'IF(a.phone is NOT NULL OR a.phone != "", a.phone, a.phone_mobile) as `phone`';
                    } elseif ($field == 'op.amount') {
                        $field = 'op.amount, op.id_currency as `currency_id`';
                    } elseif ($field == 'customer_msg') {
                        $field = '(SELECT GROUP_CONCAT(cm.message SEPARATOR ". ") FROM `' . _DB_PREFIX_ . 'customer_message` cm WHERE (cm.id_customer_thread = ct.id_customer_thread AND (cm.id_employee = 0 OR cm.id_employee is NULL))) as `customer_msg`';
                    } elseif ($field == 'employee_msg') {
                        $field = '(SELECT GROUP_CONCAT(em.message SEPARATOR ". ") FROM `' . _DB_PREFIX_ . 'customer_message` em WHERE (em.id_customer_thread = ct.id_customer_thread AND (em.id_employee != 0 AND em.id_employee is NOT NULL))) as `employee_msg`';
                    } elseif ($field == 'new_client') {
                        $field = 'IF((SELECT so.id_order FROM `' . _DB_PREFIX_ . 'orders` so WHERE so.id_customer = o.id_customer AND so.id_order < o.id_order LIMIT 1) > 0, "No", "Yes") as new_client';
                    } elseif ($field == 'shipping_address')
                        $field = 'o.id_address_delivery as shipping_address';
                    elseif ($field == 'invoice_address')
                        $field = 'o.id_address_invoice as invoice_address';
                    elseif ($field == 'od.reduction_amount_tax_incl')
                        $field = 'IF(od.reduction_amount_tax_incl!=0,od.reduction_amount_tax_incl,od.reduction_percent*od.product_price/100) as reduction_amount_tax_incl';
                    elseif ($field == 'od.tax_rate')
                        $field = 'od.unit_price_tax_incl-od.unit_price_tax_excl as tax_rate';
                    elseif ($field == 'discount_value')
                        $field = ' o.total_discounts_tax_incl as discount_value';
                    elseif ($field == 'discount_percent')
                        $field = ' o.total_discounts_tax_incl/(o.total_paid_tax_incl+o.total_discounts_tax_incl) as discount_percent';
                    else {
                        if ($array_field = Module::getInstanceByName('ets_ordermanager')->getField($field)) {
                            $field = $field . ' as ' . $array_field['key'];
                        }
                    }

                }
                if (!in_array('o.id_order', $fields))
                    $fields[] = 'o.id_order';
                if (!in_array('o.id_currency', $fields))
                    $fields[] = 'o.id_currency';
                return implode(',', $fields);
            }
        }
        return $fields;
    }
    public static function getOrderExport($fields = '*', $filter = '', $orderBy = '', $id_currency = 0)
    {
        $exported_fields = $fields;
        if ($fields != '*') {
            $list_fields = Module::getInstanceByName('ets_ordermanager')->getListFields($fields);
            Module::getInstanceByName('ets_ordermanager')->list_fields = $list_fields;
        }
        else
            $list_fields = false;
        $fields2 = $fields;
        $fields2 = str_replace('o.product_quantity_in_stock', 'od.product_quantity_in_stock', $fields2);
        $fields = str_replace(
            array('o.product_quantity_in_stock'),
            array('od.product_quantity_in_stock'),
            $fields
        );
        if($fields != '*')
            $fields = self::buildFields($fields,$list_fields);
        $sql = 'SELECT ' . (string)$fields . ' FROM `' . _DB_PREFIX_ . 'orders` o '
            . (strpos($fields, 'od.', 0) !== false || (strpos($filter, 'od.', 0) !== false || strpos($filter, 'mn.', 0) !== false || strpos($filter, 'supp.', 0) !== false || strpos($filter, 'cp.', 0) !== false) ? ' LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` od ON (o.id_order = od.id_order)' : '')
            . (strpos($fields, 'oc.', 0) !== false ? ' LEFT JOIN `' . _DB_PREFIX_ . 'order_carrier` oc ON (o.id_order = oc.id_order)' : '')
            . (strpos($fields, 'op.', 0) !== false ? ' LEFT JOIN `' . _DB_PREFIX_ . 'order_payment` op ON (o.reference = op.order_reference)' : '') . '
        LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (osl.id_order_state = o.current_state AND osl.id_lang="' . (int)Context::getContext()->language->id . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (o.id_customer = c.id_customer)'
            . (strpos($fields, 'cm.', 0) !== false || strpos($fields, 'em.', 0) !== false ? 'LEFT JOIN `' . _DB_PREFIX_ . 'customer_thread` ct ON (o.id_order = ct.id_order)' : '') . '
        LEFT JOIN `' . _DB_PREFIX_ . 'customer_group` cg ON (o.id_customer = cg.id_customer)
        LEFT JOIN `' . _DB_PREFIX_ . 'address` a ON (o.id_address_delivery = a.id_address)
        LEFT JOIN `' . _DB_PREFIX_ . 'country` country ON (country.id_country = a.id_country)
        LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (cl.id_country = country.id_country AND cl.id_lang="' . (int)Context::getContext()->language->id . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'state` s ON (a.id_state = s.id_state)
        LEFT JOIN `' . _DB_PREFIX_ . 'lang` lg ON (o.id_lang = lg.id_lang)
        LEFT JOIN `' . _DB_PREFIX_ . 'address` ainvoice ON (o.id_address_invoice= ainvoice.id_address)
        LEFT JOIN `' . _DB_PREFIX_ . 'country` invoice_country ON (invoice_country.id_country = ainvoice.id_country)
        LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` clinvoice ON (clinvoice.id_country = invoice_country.id_country AND clinvoice.id_lang="' . (int)Context::getContext()->language->id . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'state` sinvoice ON (ainvoice.id_state = sinvoice.id_state)
        LEFT JOIN `' . _DB_PREFIX_ . 'carrier` ca ON (ca.id_carrier = o.id_carrier) 
        LEFT JOIN `' . _DB_PREFIX_ . 'shop` sh ON (sh.id_shop = o.id_shop) '
            . (strpos($filter, 'mn.', 0) !== false ? ' 
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product=od.product_id)
            LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` mn ON (mn.id_manufacturer = p.id_manufacturer)' : '')
            . (strpos($filter, 'supp.', 0) !== false ? '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_supplier` ps ON (ps.id_product=od.product_id)
            LEFT JOIN `' . _DB_PREFIX_ . 'supplier` supp ON (supp.id_supplier = ps.id_supplier)' : '')
            . (strpos($filter, 'cp.', 0) !== false ? ' 
            LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.id_product = od.product_id) 
            LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (cp.id_category = cs.id_category AND cs.id_shop = ' . (int)Context::getContext()->shop->id . ')' : '') . '
        LEFT JOIN `' . _DB_PREFIX_ . 'currency` cu ON (cu.id_currency = o.id_currency) '
            . (Module::isInstalled('ets_payment_with_fee') && Module::isEnabled('ets_payment_with_fee') && Tools::strpos($fields, 'pam.fee') !== false ? ' LEFT JOIN `' . _DB_PREFIX_ . 'ets_paymentmethod_order` pam ON (pam.id_order=o.id_order) ' : '')
            . ($filter ? (string)$filter : '') . ' GROUP BY '
            . (string)$fields2
            . ($orderBy ? ' ORDER BY ' . pSQL($orderBy) : '');
        $orders = Db::getInstance()->executeS($sql);
        if ($orders && Tools::strpos($exported_fields, 'shipping_address') !== false || Tools::strpos($exported_fields, 'invoice_address') !== false) {
            foreach ($orders as &$order) {
                $class_order = new Order($order['id_order']);
                if (Tools::strpos($exported_fields, 'shipping_address') !== false) {
                    $address_shipping = new Address($class_order->id_address_delivery);
                    $order['shipping_address'] = AddressFormat::generateAddressSmarty(array('address' => $address_shipping, 'newLine' => ' - '), Context::getContext()->smarty);
                }
                if (Tools::strpos($exported_fields, 'invoice_address') !== false) {
                    $address_shipping = new Address($class_order->id_address_invoice);
                    $order['invoice_address'] = AddressFormat::generateAddressSmarty(array('address' => $address_shipping, 'newLine' => ' - '), Context::getContext()->smarty);
                }
            }
        }
        if ($orders) {

            foreach ($orders as &$order) {
                if ($id_currency) {
                    $currency = new Currency($id_currency, Context::getContext()->language->id);
                    $order['iso_code'] = $currency->iso_code;
                } else
                    $currency = new Currency($order['id_currency'], Context::getContext()->language->id);
                $decimal = isset($currency->precision) ? $currency->precision : $currency->decimals;
                if (isset($order['currency_name']))
                    $order['currency_name'] = $currency->name;
                if (isset($order['total_paid_tax_incl']))
                    $order['total_paid_tax_incl'] = $id_currency ? Tools::ps_round(Tools::convertPrice(Tools::convertPrice($order['total_paid_tax_incl'], $order['id_currency'], false), $id_currency), $decimal) : Tools::ps_round($order['total_paid_tax_incl'], $decimal);
                if (isset($order['total_paid_tax_excl']))
                    $order['total_paid_tax_excl'] = $id_currency ? Tools::ps_round(Tools::convertPrice(Tools::convertPrice($order['total_paid_tax_excl'], $order['id_currency'], false), $id_currency), $decimal) : Tools::ps_round($order['total_paid_tax_excl'], $decimal);
                if (isset($order['total_price_tax_incl']))
                    $order['total_price_tax_incl'] = $id_currency ? Tools::ps_round(Tools::convertPrice(Tools::convertPrice($order['total_price_tax_incl'], $order['id_currency'], false), $id_currency), $decimal) : Tools::ps_round($order['total_price_tax_incl'], $decimal);
                if (isset($order['total_price_tax_excl']))
                    $order['total_price_tax_excl'] = $id_currency ? Tools::ps_round(Tools::convertPrice(Tools::convertPrice($order['total_price_tax_excl'], $order['id_currency'], false), $id_currency), $decimal) : Tools::ps_round($order['total_price_tax_excl'], $decimal);
                if (isset($order['product_price']))
                    $order['product_price'] = $id_currency ? Tools::ps_round(Tools::convertPrice(Tools::convertPrice($order['product_price'], $order['id_currency'], false), $id_currency), $decimal) : Tools::ps_round($order['product_price'], $decimal);
                if (isset($order['original_product_price']))
                    $order['original_product_price'] = $id_currency ? Tools::ps_round(Tools::convertPrice(Tools::convertPrice($order['original_product_price'], $order['id_currency'], false), $id_currency), $decimal) : Tools::ps_round($order['original_product_price'], $decimal);
                if (isset($order['total_shipping_tax_incl']))
                    $order['total_shipping_tax_incl'] = $id_currency ? Tools::ps_round(Tools::convertPrice(Tools::convertPrice($order['total_shipping_tax_incl'], $order['id_currency'], false), $id_currency), $decimal) : Tools::ps_round($order['total_shipping_tax_incl'], $decimal);
                if (isset($order['total_shipping_tax_excl']))
                    $order['total_shipping_tax_excl'] = $id_currency ? Tools::ps_round(Tools::convertPrice(Tools::convertPrice($order['total_shipping_tax_excl'], $order['id_currency'], false), $id_currency), $decimal) : Tools::ps_round($order['total_shipping_tax_excl'], $decimal);
                if (isset($order['discount_value']))
                    $order['discount_value'] = $id_currency ? Tools::ps_round(Tools::convertPrice(Tools::convertPrice($order['discount_value'], $order['id_currency'], false), $id_currency), $decimal) : Tools::ps_round($order['discount_value'], $decimal);
                if (isset($order['reduction_amount_tax_incl']))
                    $order['reduction_amount_tax_incl'] = $id_currency ? Tools::ps_round(Tools::convertPrice(Tools::convertPrice($order['reduction_amount_tax_incl'], $order['id_currency'], false), $id_currency), $decimal) : Tools::ps_round($order['reduction_amount_tax_incl'], $decimal);
                if (isset($order['discount_percent']))
                    $order['discount_percent'] = Tools::ps_round($order['discount_percent'] * 100, 2) . '%';
                if (isset($order['date_add']))
                    $order['date_add'] = Tools::displayDate($order['date_add']);
                if (isset($order['date_upd']))
                    $order['date_upd'] = Tools::displayDate($order['date_upd']);
                if (isset($order['invoice_number'])) {
                    if ($order['invoice_number']) {
                        $id_order_invoice = Db::getInstance()->getValue('SELECT id_order_invoice FROM `' . _DB_PREFIX_ . 'order_invoice` WHERE number="' . pSQL($order['invoice_number']) . '"');
                        $order_invoice = new OrderInvoice($id_order_invoice);
                        $order['invoice_number'] = $order_invoice->getInvoiceNumberFormatted(Context::getContext()->language->id);
                    } else
                        $order['invoice_number'] = '';
                }
                //order1.
                if (isset($order['amount']) && $order['amount']) {
                    $order['amount'] = Tools::displayPrice($id_currency ? Tools::ps_round(Tools::convertPrice(Tools::convertPrice($order['amount'], $order['id_currency'], false), $id_currency), $decimal) : $order['amount'], $currency);
                }
                if ($id_currency)
                    $order['id_currency'] = $id_currency;
                unset($order['currency_id']);
            }
        }
        return $orders;
    }
    public static function getMaxPosition()
    {
        return Db::getInstance()->getValue('select MAX(position) FROM ' . _DB_PREFIX_ . 'ets_export_order_rule');
    }
    public static function autoExport($id = 0, $log = false)
    {
        if (Configuration::getGlobalValue('ETS_ODE_USE_CRONJOB')) {
            $current_date = date('Y-m-d H:i:s');
            $common_sql = 'SELECT id_ets_export_order_rule FROM `' . _DB_PREFIX_ . 'ets_export_order_rule` WHERE 1 ' . ($id ? ' AND id_ets_export_order_rule=' . (int)$id : '');
            $sql = $common_sql . ' AND (
                
                    ("' . pSQL($current_date) . '" > send_file_date AND ((send_file_schedule = "daily" AND hour("' . pSQL($current_date) . '") = send_file_time_hours AND day("' . pSQL($current_date) . '") != day(send_file_date))
                        OR (send_file_schedule = "weekly" AND hour("' . pSQL($current_date) . '") = send_file_time_hours AND dayofweek("' . pSQL($current_date) . '") - 1 = send_file_time_weeks AND dayofyear("' . pSQL($current_date) . '") != dayofyear(send_file_date) )
                        OR (send_file_schedule = "hourly" AND hour("' . pSQL($current_date) . '") != hour(send_file_date))
                        OR (send_file_schedule= "5_minutes" AND (send_file_date="0000-00-00 00:00:00" || send_file_date <= "' . pSQL(date('Y-m-d H:i:s', strtotime("-5 minutes"))) . '" ))
                        OR (send_file_schedule= "30_minutes" AND (send_file_date="0000-00-00 00:00:00" || send_file_date <="' . pSQL(date('Y-m-d H:i:s', strtotime("-30 minutes"))) . '" ))
                        OR (send_file_schedule = "monthly" AND month(send_file_date)!= month("' . pSQL($current_date) . '") AND hour("' . pSQL($current_date) . '") = send_file_time_hours AND IF(send_file_time_months = "first", day("' . pSQL($current_date) . '") = 1, IF(send_file_time_months = "middle", day("' . pSQL($current_date) . '") = 15, day("' . pSQL($current_date) . '") = day(last_day("' . pSQL($current_date) . '")))))))

                   OR ("' . pSQL($current_date) . '" > server1_date AND ((server1_schedule = "daily" AND hour("' . pSQL($current_date) . '") = server1_time_hours AND day("' . pSQL($current_date) . '") != day(server1_date))
                       OR (server1_schedule = "weekly" AND hour("' . pSQL($current_date) . '") = server1_time_hours AND dayofweek("' . pSQL($current_date) . '") - 1 = server1_time_weeks AND dayofyear("' . pSQL($current_date) . '") != dayofyear(server1_date))
                       OR (server1_schedule = "hourly" AND hour("' . pSQL($current_date) . '") != hour(server1_date))
                       OR (send_file_schedule= "5_minutes" AND (server1_date="0000-00-00 00:00:00" || server1_date <= "' . pSQL(date('Y-m-d H:i:s', strtotime("-5 minutes"))) . '" ))
                       OR (send_file_schedule= "30_minutes" AND (server1_date="0000-00-00 00:00:00" || server1_date <="' . pSQL(date('Y-m-d H:i:s', strtotime("-30 minutes"))) . '" ))
                       OR (server1_schedule = "monthly" AND month(server1_date)!= month("' . pSQL($current_date) . '") AND hour("' . pSQL($current_date) . '") = send_file_time_hours AND IF(server1_time_months = "first", day("' . pSQL($current_date) . '") = 1, IF(server1_time_months = "middle", day("' . pSQL($current_date) . '") = 15, day("' . pSQL($current_date) . '") = day(last_day("' . pSQL($current_date) . '")))))))
                   OR ("' . pSQL($current_date) . '" > server2_date AND ((server2_schedule = "daily" AND hour("' . pSQL($current_date) . '") = server2_time_hours AND day("' . pSQL($current_date) . '") != day(server2_date))
                       OR (server2_schedule = "weekly" AND hour("' . pSQL($current_date) . '") = server2_time_hours AND dayofweek("' . pSQL($current_date) . '") - 1 = server2_time_weeks AND dayofyear("' . pSQL($current_date) . '") != dayofyear(server2_date))
                       OR (server2_schedule = "hourly" AND hour("' . pSQL($current_date) . '") != hour(server2_date))
                       OR (send_file_schedule= "5_minutes" AND (server2_date="0000-00-00 00:00:00" || server2_date <= "' . pSQL(date('Y-m-d H:i:s', strtotime("-5 minutes"))) . '" ))
                       OR (send_file_schedule= "30_minutes" AND (server2_date="0000-00-00 00:00:00" || server2_date <="' . pSQL(date('Y-m-d H:i:s', strtotime("-30 minutes"))) . '" ))
                       OR (server2_schedule = "monthly" AND month(server2_date)!= month("' . pSQL($current_date) . '") AND hour("' . pSQL($current_date) . '") = send_file_time_hours AND IF(server2_time_months = "first", day("' . pSQL($current_date) . '") = 1, IF(server2_time_months = "middle", day("' . pSQL($current_date) . '") = 15, day("' . pSQL($current_date) . '") = day(last_day("' . pSQL($current_date) . '")))))))
                )
            ';
            $orders_exporter = Db::getInstance()->executeS($sql);
            $cron_job = false;
            if ($orders_exporter) {
                foreach ($orders_exporter as $order_exporter) {
                    if (Module::getInstanceByName('ets_ordermanager')->actionExportOrSendOrder(array(
                        'id' => $order_exporter['id_ets_export_order_rule'],
                        'schedule' => 'auto',
                        'log' => $log
                    )))
                        $cron_job = true;
                }
            }
            //delete exported file.
            $order_exported = Db::getInstance()->executeS($common_sql);
            if ($order_exported) {
                foreach ($order_exported as $order) {
                    Module::getInstanceByName('ets_ordermanager')->deleteExportedFiles($order['id_ets_export_order_rule']);
                }
            }
            return $cron_job;
        }
    }
    public static function getRuleExports()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'ets_export_order_rule` ox 
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_export_order_rule_lang` oxl ON (ox.id_ets_export_order_rule = oxl.id_ets_export_order_rule AND oxl.id_lang = ' . (int)Context::getContext()->language->id . ')
            INNER JOIN `' . _DB_PREFIX_ . 'ets_export_order_rule_shop` oxs ON (ox.id_ets_export_order_rule = oxs.id_ets_export_order_rule)
            WHERE oxs.id_shop = ' . (int)Context::getContext()->shop->id . '  
            ORDER BY position asc';
        return Db::getInstance()->executeS($sql);
    }
}