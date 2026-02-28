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

if (!defined('_PS_VERSION_') || !defined('_ETS_ODE_MODULE_'))
    exit;

class Ode_defines
{
    public static $instance;
    public $module;
    public $configTabs;
    public $configs;
    public $order_fields;
    public $other_fields;
    public $customer_fields;
    public $shipping_fields;
    public $invoice_fields;        
    public $carrier_fields;
    public $product_fields;
    public $list_fields;
    public $payment_fields;
    public $rule_fields;
    public $settingTabs;
    public $schedules;
    public $context;
    public $smarty;

    public function __construct($module = null)
    {
        if (!$module || !is_object($module)) {
            $module = Module::getInstanceByName(_ETS_ODE_MODULE_);
        }
        $this->module = $module;
        $this->context = Context::getContext();
        if (is_object($this->context->smarty)) {
            $this->smarty = $this->context->smarty;
        }
    }

    public static function getInstance($module = null)
    {
        if (!(isset(self::$instance)) || !self::$instance) {
            self::$instance = new Ode_defines($module);
        }
        return self::$instance;
    }

    public function getFields($fields,$id_ets_export_order_rule=0)
    {
        if ($fields == 'tabs') {
            if (!(isset($this->configTabs)) || !$this->configTabs) {
                $this->configTabs = array(
                    'information' => $this->l('Information'),
                    'exported_fields' => $this->l('Export fields'),
                    'filtering_options' => $this->l('Filter'),
                    'settings' => $this->l('Automation'),
                );
            }
            return $this->configTabs;
        }
        //field $settingTabs
        elseif ($fields == 'tab_setting') {
            if (!(isset($this->settingTabs)) || !$this->settingTabs) {
                $this->settingTabs = array(
                    'cronjob' => $this->l('Settings'),
                    'general' => $this->l('Global FTP'),
                    'manager_form' => $this->l('Permissions'),
                    'delivery_label' => $this->l('Shipping labels'),
                );
            }
            return $this->settingTabs;
        }
        elseif($fields=='short_code')
        {
            return '{logo},{shop_phone},{shop_fax},{shop_address},{shop_email},{shop_site_name},{shop_name},{shop_city},{shop_postcode},{shop_country},{shop_state},{customer_lastname},{customer_firstname},{customer_address},{customer_phone},{customer_company},{customer_country},{customer_state},{customer_city},{customer_postcode},{id_order},{reference_order},{barcode_order},{total_weight},{product_list},{product_list_total_order},{subtotal},{shipping},{taxes},{total},{discount},{payment_method},{shipping_method}';
        }
        //field $configs;
        elseif ($fields == 'config') {
            if (!(isset($this->configs)) || !$this->configs) {
                $short_codes = explode(',',$this->getFields('short_code'));
                $short_codes_text = '';
                if($short_codes)
                {
                    foreach($short_codes as $short_code){
                        $short_codes_text .= Module::getInstanceByName('ets_ordermanager')->displayText($short_code,'strong',array('class'=>'shortcode')).',';
                    }

                }
                $this->configs = array(
                    'ETS_ODE_HOST' => array(
                        'label' => $this->l('Host'),
                        'type' => 'text',
                        'tab' => 'general',
                    ),
                    'ETS_ODE_USERNAME' => array(
                        'label' => $this->l('Username'),
                        'type' => 'text',
                        'tab' => 'general',
                        'autocomplete' => false,
                    ),
                    'ETS_ODE_PASSWORD' => array(
                        'name' => 'password',
                        'label' => $this->l('Password'),
                        'type' => 'text',
                        'col' => 3,
                        'tab' => 'general',
                        'autocomplete'=>false,
                    ),
                    'ETS_ODE_PORT' => array(
                        'label' => $this->l('Port'),
                        'type' => 'text',
                        'tab' => 'general',
                        'col' => 1,
                        'default'=>21,
                    ),
                    'ETS_ODM_ENABLE_INSTANT_FILTER' => array(
                        'label' => $this->l('Enable instant search on order listing page'),
                        'global' => true,
                        'type' => 'switch',
                        'tab' => 'cronjob',
                        'default' =>1,
                        'desc' => $this->l('Display search result immediately as you typing in order filter'),
                    ),
                    'ETS_ODM_KEEP_SEARCH_FILTER' => array(
                        'label' => $this->l('Keep search on page reload'),
                        'global' => true,
                        'type' => 'switch',
                        'tab' => 'cronjob',
                        'default' =>0,
                        'form_group_class' => 'keep_search',
                    ),
                    'ETS_ODE_BEHAVIOR_DELETE_ORDER' => array(
                        'label' => $this->l('Behavior when deleting order from the order list'),
                        'type'=>'radio',
                        'tab' =>'cronjob',
                        'default' =>'permanently',
                        'values' => array(
                            array(
                                'id' => 'ETS_ODE_BEHAVIOR_DELETE_ORDER_permanently',
                                'label' => $this->l('Delete permanently (Can NOT restore the order)'),
                                'value' => 'permanently',
                            ),
                            array(
                                'id' => 'ETS_ODE_BEHAVIOR_DELETE_ORDER_move_to_trash',
                                'label' => $this->l('Move to Trash (Can restore the order from Trash)'),
                                'value' => 'move_to_trash',
                            ),
                        ),
                    ),
                    
                    'ETS_ODE_USE_CRONJOB' => array(
                        'label' => $this->l('Enable cronjob'),
                        'global' => true,
                        'type' => 'switch',
                        'tab' => 'cronjob',
                        'desc' => $this->l('Setup a cronjob to execute automation tasks (send order export file via email, FTP or save to a web directory). For more information about Cronjob, see').' <a href="https://www.digitalocean.com/community/tutorials/how-to-schedule-routine-tasks-with-cron-and-anacron-on-a-vps" target="_blank">'.$this->l('here').'</a>'
                    ),
                    'ETS_ODE_CRONJOB_TOKEN' => array(
                        'label' => $this->l('Cronjob secure token'),
                        'type' => 'text',
                        'global' => true,
                        'required' => true,
                        'default' => Tools::strtolower(Tools::passwdGen(10)),
                        'tab' => 'cronjob'
                    ),
                    'ETS_ODE_SAVE_CRONJOB_TOKEN' => array(
                        'label' => $this->l('Save Cronjob log'),
                        'type' => 'switch',
                        'default' =>0,
                        'tab' => 'cronjob',
                        'desc' => $this->l('Recommended for debug purpose only'),
                    ),
                    'ETS_ODE_DELIVERY_LABEL_SIZE_FORMAT'=> array(
                        'label' => $this->l('Size format'),
                        'type' => 'select',
                        'default' =>'A4',
                        'tab' => 'delivery_label',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 'A3',
                                    'name' => $this->l('A3'),
                                ),
                                array(
                                    'id' => 'A4',
                                    'name' => $this->l('A4'),
                                ),
                                array(
                                    'id' => 'A5',
                                    'name' => $this->l('A5'),
                                ),
                                array(
                                    'id' => 'A6',
                                    'name' => $this->l('A6'),
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    'ETS_ODE_DELIVERY_LABEL_USE_HEADER' => array(
                        'label' => $this->l('Use header'),
                        'type' => 'switch',
                        'default' =>1,
                        'tab' => 'delivery_label',
                    ),
                    'ETS_ODE_DELIVERY_LABEL_HEIGHT_HEADER' => array(
                        'label' => $this->l('Height of header'),
                        'type' => 'text',
                        'default' =>version_compare(_PS_VERSION_, '1.7', '>=')?20:30,
                        'tab' => 'delivery_label',
                        'col'=>1,
                        'validate' =>'isUnsignedFloat',
                        'suffix'=>'mm',
                        
                    ),
                    'ETS_ODE_DELIVERY_LABEL_HEADER' => array(
                        'label' => $this->l('Header content'),
                        'type' => 'textarea',
                        'tab' => 'delivery_label',
                        'autoload_rte'=>true,
                        'lang' => true,
                        'desc'=> $this->l('Custom variables: ').trim($short_codes_text,', '),
                    ),
                    'ETS_ODE_DELIVERY_LABEL_CONTENT' => array(
                        'label' => $this->l('Body content'),
                        'type' => 'textarea',
                        'tab' => 'delivery_label',
                        'autoload_rte'=>true,
                        'lang' => true,
                        'desc'=> $this->l('Custom variables: ').trim($short_codes_text,', '),
                    ),
                    'ETS_ODE_DELIVERY_LABEL_USE_FOOTER' => array(
                        'label' => $this->l('Use footer'),
                        'type' => 'switch',
                        'default' =>1,
                        'tab' => 'delivery_label',
                    ),
                    'ETS_ODE_DELIVERY_LABEL_HEIGHT_FOOTER' => array(
                        'label' => $this->l('Height of footer'),
                        'type' => 'text',
                        'default' =>15,
                        'tab' => 'delivery_label',
                        'suffix'=>'mm',
                        'validate' =>'isUnsignedFloat',
                        'col'=>1,
                    ),
                    'ETS_ODE_DELIVERY_LABEL_FOOTER' => array(
                        'label' => $this->l('Footer content'),
                        'type' => 'textarea',
                        'tab' => 'delivery_label',
                        'autoload_rte'=>true,
                        'lang' => true,
                        'desc'=> $this->l('Custom variables :').trim($short_codes_text,', '),
                    ),
                );
            }
            return $this->configs;
        }
        //field $order;
        elseif ($fields == 'order') {
            if (!(isset($this->order_fields)) || !$this->order_fields) {
                $this->order_fields = array(
                    array(
                        'name' => $this->l('Order ID'),
                        'id' => 'o.id_order',
                        'key' => 'id_order',
                        'val' => 'o.id+order',
                    ),
                    array(
                        'name' => $this->l('Order reference'),
                        'id' => 'o.reference',
                        'key' => 'reference',
                        'val' => 'o.reference',
                    ),
                    array(
                        'name' => $this->l('Total payment tax included'),
                        'id' => 'o.total_paid_tax_incl',
                        'key' => 'total_paid_tax_incl',
                        'val' => 'o.total+paid+tax+incl',
                    ),
                    array(
                        'name' => $this->l('Total payment tax excluded'),
                        'id' => 'o.total_paid_tax_excl',
                        'key' => 'total_paid_tax_excl',
                        'val' => 'o.total+paid+tax+excl',
                    ),
                    array(
                        'name' => $this->l('Invoice number'),
                        'id' => 'o.invoice_number',
                        'key' => 'invoice_number',
                        'val' => 'o.invoice+number',
                    ),
                    array(
                        'name' => $this->l('Order added date'),
                        'id' => 'o.date_add',
                        'val' => 'o.date+add',
                        'key' => 'date_add',
                    ),
                    array(
                        'name' => $this->l('Order updated date'),
                        'id' => 'o.date_upd',
                        'val' => 'o.date+upd',
                        'key' => 'date_upd',
                    ),
                    array(
                        'name' => $this->l('Order status'),
                        'id' => 'osl.name',
                        'val' => 'osl.name',
                        'key' => 'status_name',
                    ),
                    array(
                        'name' => $this->l('Customer messages'),
                        'id' => 'customer_msg',
                        'val' => 'customer+msg',
                        'key' => 'customer_msg',
                    ),
                    array(
                        'name' => $this->l('Admin messages'),
                        'id' => 'employee_msg',
                        'val' => 'employee+msg',
                        'key' => 'employee_msg',
                    ),
                    array(
                        'name' => $this->l('Order discount value (Tax incl)'),
                        'id' => 'discount_value',
                        'val' => 'discount+value',
                        'key' => 'discount_value',
                    ),
                    array(
                        'name' => $this->l('Order discount percentage'),
                        'id' => 'discount_percent',
                        'val' => 'discount+percent',
                        'key' => 'discount_percent',
                    ),
                );
            }
            return $this->order_fields;
        }
        //field $customer_fields;
        elseif ($fields == 'customer') {
            if (!(isset($this->customer_fields)) || !$this->customer_fields) {
                $this->customer_fields = array(
                    array(
                        'name' => $this->l('Customer ID'),
                        'id' => 'o.id_customer',
                        'key' => 'id_customer',
                        'val' => 'o.id+customer',
                    ),
                    array(
                        'name' => $this->l('First name'),
                        'id' => 'c.firstname',
                        'key' => 'customer_firstname',
                        'val' => 'c.firstname',
                    ),
                    array(
                        'name' => $this->l('Last name'),
                        'id' => 'c.lastname',
                        'key' => 'customer_lastname',
                        'val' => 'c.lastname',
                    ),
                    array(
                        'name' => $this->l('Email'),
                        'id' => 'c.email',
                        'key' => 'customer_email',
                        'val' => 'c.email',
                    ),
                    array(
                        'name' => $this->l('New client'),
                        'id' => 'new_client',
                        'key' => 'new_client',
                        'val' => 'new_client',
                    ),                    
                );
            }
            return $this->customer_fields;
        }
        elseif($fields=='shipping')
        {
            if(!isset($this->shipping_fields) || !$this->shipping_fields )
            {
                $this->shipping_fields = array(
                    array(
                        'name' => $this->l('Shipping address (full)'),
                        'id' => 'shipping_address',
                        'key' => 'shipping_address',
                        'val' => 'shipping+address',
                    ),
                    array(
                        'name' => $this->l('Address alias'),
                        'id' => 'a.alias',
                        'key' => 'shipping_alias',
                        'val' => 'a.alias',
                    ),
                    array(
                        'name' => $this->l('First name'),
                        'id' => 'a.firstname',
                        'key' => 'shipping_firstname',
                        'val' => 'a.firstname',
                    ),
                    array(
                        'name' => $this->l('Last name'),
                        'id' => 'a.lastname',
                        'key' => 'shipping_lastname',
                        'val' => 'a.lastname',
                    ),
                    array(
                        'name' => $this->l('Company'),
                        'id' => 'a.company',
                        'key' => 'shipping_company',
                        'val' => 'a.company',
                    ),
                    array(
                        'name' => $this->l('VAT number'),
                        'id' => 'a.vat_number',
                        'key' => 'shipping_vat_number',
                        'val' => 'a.vat+number',
                    ),
                    array(
                        'name' => $this->l('Address'),
                        'id' => 'a.address1',
                        'key' => 'shipping_address1',
                        'val' => 'a.address1',
                    ),
                    array(
                        'name' => $this->l('Address (2)'),
                        'id' => 'a.address2',
                        'key' => 'shipping_address2',
                        'val' => 'a.address2',
                    ),
                    array(
                        'name' => $this->l('City'),
                        'id' => 'a.city',
                        'key' => 'shipping_city',
                        'val' => 'a.city',
                    ),
                    array(
                        'name' => $this->l('Company'),
                        'id' => 'a.company',
                        'key' => 'company',
                        'val' => 'a.company',
                    ),
                    array(
                        'name' => $this->l('Zip/postal code'),
                        'id' => 'a.postcode',
                        'key' => 'shipping_postcode',
                        'val' => 'a.postcode',
                    ),
                    array(
                        'name' => $this->l('Country'),
                        'id' => 'cl.name',
                        'key' => 'shipping_name_country',
                        'val' => 'cl.name',
                    ),
                    array(
                        'name' => $this->l('State'),
                        'id' => 's.name',
                        'key' => 'shippingname_state',
                        'val' => 's.name',
                    ),
                    array(
                        'name' => $this->l('Home phone'),
                        'id' => 'a.phone',
                        'key' => 'phone',
                        'val' => 'a.phone',
                    ),
                );         
            } 
            return $this->shipping_fields;        
        } 
        elseif($fields=='invoice')
        {
            if(!isset($this->invoice_fields) || !$this->invoice_fields )
            {
                $this->invoice_fields = array(
                    array(
                        'name' => $this->l('Invoice address (full)'),
                        'id' => 'invoice_address',
                        'key' => 'invoice_address',
                        'val' => 'invoice+address',
                    ),
                    array(
                        'name' => $this->l('Address alias'),
                        'id' => 'ainvoice.alias',
                        'key' => 'invoicealias',
                        'val' => 'ainvoice.alias',
                    ),
                    array(
                        'name' => $this->l('First name'),
                        'id' => 'ainvoice.firstname',
                        'key' => 'invoicefirstname',
                        'val' => 'ainvoice.firstname',
                    ),
                    array(
                        'name' => $this->l('Last name'),
                        'id' => 'ainvoice.lastname',
                        'key' => 'invoicelastname',
                        'val' => 'ainvoice.lastname',
                    ),
                    array(
                        'name' => $this->l('Company'),
                        'id' => 'ainvoice.company',
                        'key' => 'invoicecompany',
                        'val' => 'ainvoice.company',
                    ),
                    array(
                        'name' => $this->l('VAT number'),
                        'id' => 'ainvoice.vat_number',
                        'key' => 'invoicevat_number',
                        'val' => 'ainvoice.vat+number',
                    ),
                    array(
                        'name' => $this->l('Address'),
                        'id' => 'ainvoice.address1',
                        'key' => 'invoiceaddress1',
                        'val' => 'ainvoice.address1',
                    ),
                    array(
                        'name' => $this->l('Address (2)'),
                        'id' => 'ainvoice.address2',
                        'key' => 'invoiceaddress2',
                        'val' => 'ainvoice.address2',
                    ),
                    array(
                        'name' => $this->l('City'),
                        'id' => 'ainvoice.city',
                        'key' => 'invoicecity',
                        'val' => 'ainvoice.city',
                    ),
                    array(
                        'name' => $this->l('Zip/postal code'),
                        'id' => 'ainvoice.postcode',
                        'key' => 'invoicepostcode',
                        'val' => 'ainvoice.postcode',
                    ),
                    array(
                        'name' => $this->l('Country'),
                        'id' => 'clinvoice.name',
                        'key' => 'invoicename_country',
                        'val' => 'clinvoice.name',
                    ),
                    array(
                        'name' => $this->l('State'),
                        'id' => 'sinvoice.name',
                        'key' => 'invoicename_state',
                        'val' => 'ainvoice.name',
                    ),
                    array(
                        'name' => $this->l('Home phone'),
                        'id' => 'ainvoice.phone',
                        'key' => 'invoicephone',
                        'val' => 'ainvoice.phone',
                    ),
                );         
            } 
            return $this->invoice_fields;        
        }               
        //field $carrier_fields;
        elseif ($fields == 'carrier') {
            if (!(isset($this->carrier_fields)) || !$this->carrier_fields) {
                $this->carrier_fields = array(
                    array(
                        'name' => $this->l('Carrier ID'),
                        'id' => 'o.id_carrier',
                        'key' => 'id_carrier',
                        'val' => 'o.id+carrier',
                    ),
                    array(
                        'name' => $this->l('Carrier name'),
                        'id' => 'ca.name',
                        'key' => 'name_carrier',
                        'val' => 'ca.name',
                    ),
                    array(
                        'name' => $this->l('Shipping tax incl'),
                        'id' => 'o.total_shipping_tax_incl',
                        'key' => 'total_shipping_tax_incl',
                        'val' => 'o.total+shipping+tax+incl',
                    ),
                    array(
                        'name' => $this->l('Shipping tax excl'),
                        'id' => 'o.total_shipping_tax_excl',
                        'key' => 'total_shipping_tax_excl',
                        'val' => 'o.total+shipping+tax+excl',
                    ),
                    array(
                        'name' => $this->l('Delivery number'),
                        'id' => 'o.delivery_number',
                        'key' => 'delivery_number',
                        'val' => 'o.delivery+number',
                    ),
                    array(
                        'name' => $this->l('Delivery date'),
                        'id' => 'o.delivery_date',
                        'key' => 'delivery_date',
                        'val' => 'o.delivery+date',
                    ),
                    array(
                        'name' => $this->l('Tracking number'),
                        'id' => 'oc.tracking_number',
                        'key' => 'tracking_number',
                        'val' => 'oc.tracking+number',
                    )
                );
            }
            return $this->carrier_fields;
        }
        //field $payment_fields;
        elseif ($fields == 'payment') {
            if (!(isset($this->payment_fields)) || !$this->payment_fields) {
                $this->payment_fields = array(
                    array(
                        'name' => $this->l('Payment method'),
                        'id' => 'op.payment_method',
                        'key' => 'payment_method',
                        'val' => 'op.payment+method',
                    ),
                    array(
                        'name' => $this->l('Payment date'),
                        'id' => 'op.date_add',
                        'key' => 'od_date_add',
                        'val' => 'op.date+add',
                    ),
                    array(
                        'name' => $this->l('Transaction ID'),
                        'id' => 'op.transaction_id',
                        'key' => 'transaction_id',
                        'val' => 'op.transaction+id',
                    ),
                    array(
                        'name' => $this->l('Payment amount'),
                        'id' => 'op.amount',
                        'key' => 'amount',
                        'val' => 'op.amount',
                    ),
                );
                if(Module::isInstalled('ets_payment_with_fee') && Module::isEnabled('ets_payment_with_fee'))
                    $this->payment_fields[]= array(
                        'name' => Configuration::get('ETS_PMF_TEXT_PAYMENT_FEE',Context::getContext()->language->id) ? : $this->l('Payment fee'),
                        'id' => 'pam.fee',
                        'key' => 'fee',
                        'val' => 'pam.fee',
                    );
            }
            return $this->payment_fields;
        }
        //field $product_fields;
        elseif ($fields == 'product') {
            if (!(isset($this->product_fields)) || !$this->product_fields) {
                $this->product_fields = array(
                    array(
                        'name' => $this->l('Product ID'),
                        'id' => 'od.product_id',
                        'key' => 'product_id',
                        'val' => 'od.product+id',
                    ),
                    array(
                        'name' => $this->l('Product name'),
                        'id' => 'od.product_name',
                        'key' => 'product_name',
                        'val' => 'od.product+name',
                    ),
                    array(
                        'name' => $this->l('Product quantity'),
                        'id' => 'od.product_quantity',
                        'key' => 'product_quantity',
                        'val' => 'od.product+quantity',
                    ),
                    array(
                        'name' => $this->l('Product quantity in stock'),
                        'id' => 'o.product_quantity_in_stock',
                        'key' => 'product_quantity_in_stock',
                        'val' => 'o.product+quantity+in+stock',
                    ),
                    array(
                        'name' => $this->l('Product EAN13'),
                        'id' => 'od.product_ean13',
                        'key' => 'product_ean13',
                        'val' => 'od.product+ean13',
                    ),
                    array(
                        'name' => $this->l('Product UPC'),
                        'id' => 'od.product_upc',
                        'key' => 'product_upc',
                        'val' => 'od.product+upc',
                    ),
                    array(
                        'name' => $this->l('Product reference'),
                        'id' => 'od.product_reference',
                        'key' => 'product_reference',
                        'val' => 'od.product+reference',
                    ),
                    array(
                        'name' => $this->l('Product price'),
                        'id' => 'od.product_price',
                        'key' => 'product_price',
                        'val' => 'od.product+price',
                    ),
                    array(
                        'name' => $this->l('Product wholesale price'),
                        'id' => 'od.original_wholesale_price',
                        'key' => 'original_wholesale_price',
                        'val' => 'od.original+wholesale+price',
                    ),
                    array(
                        'name' => $this->l('Product discount'),
                        'id' => 'od.reduction_amount_tax_incl',
                        'key' => 'reduction_amount_tax_incl',
                        'val' => 'od.reduction+amount+tax+incl',
                    ),
                    array(
                        'name' => $this->l('Original price'),
                        'id' => 'od.original_product_price',
                        'key' => 'original_product_price',
                        'val' => 'od.original+product+price',
                    ),
                    array(
                        'name' => $this->l('Product supplier reference'),
                        'id' => 'od.product_supplier_reference',
                        'key' => 'product_supplier_reference',
                        'val' => 'od.product+supplier+reference',
                    ),
                    array(
                        'name' => $this->l('Product weight'),
                        'id' => 'od.product_weight',
                        'key' => 'product_weight',
                        'val' => 'od.product+weight',
                    ),
                    array(
                        'name' => $this->l('Tax rate'),
                        'id' => 'od.tax_rate',
                        'key' => 'tax_rate',
                        'val' => 'od.tax+rate',
                    ),
                    array(
                        'name' => $this->l('Total products price tax included'),
                        'id' => 'od.total_price_tax_incl',
                        'key' => 'total_price_tax_incl',
                        'val' => 'od.total+price+tax+incl',
                    ),
                    array(
                        'name' => $this->l('Total products price tax excluded'),
                        'id' => 'od.total_price_tax_excl',
                        'key' => 'total_price_tax_excl',
                        'val' => 'od.total+price+tax+excl',
                    )
                );
            }
            return $this->product_fields;
        }
        //field $other_fields;
        elseif ($fields == 'other') {
            if (!(isset($this->other_fields)) || !$this->other_fields) {
                $this->other_fields = array(
                    array(
                        'name' => $this->l('Shop ID'),
                        'id' => 'o.id_shop',
                        'key' => 'id_shop',
                        'val' => 'o.id+shop',
                    ),
                    array(
                        'name' => $this->l('Shop name'),
                        'id' => 'sh.name',
                        'key' => 'shop_name',
                        'val' => 'sh.name',
                    ),
                    array(
                        'name' => $this->l('Language ID'),
                        'id' => 'o.id_lang',
                        'key' => 'id_lang',
                        'val' => 'o.id+lang',
                    ),
                    array(
                        'name' => $this->l('Language'),
                        'id' => 'lg.name',
                        'key' => 'language_name',
                        'val' => 'lg.name'
                    ),
                    array(
                        'name' => $this->l('Language code'),
                        'id' => 'lg.language_code',
                        'key' => 'language_code',
                        'val' => 'lg.language+code'
                    ),
                    array(
                        'name' => $this->l('Cart ID'),
                        'id' => 'o.id_cart',
                        'key' => 'id_cart',
                        'val' => 'o.id+cart',
                    ),
                    array(
                        'name' => $this->l('Currency ID'),
                        'id' => 'o.id_currency',
                        'key' => 'id_currency',
                        'val' => 'o.id+currency',
                    ),
                    array(
                        'name' => 'Currency',
                        'id' => 'cu.name',
                        'key' => 'currency_name',
                        'val' => 'cu.name',
                    ),
                    array(
                        'name' => $this->l('Currency code'),
                        'id' => 'cu.iso_code',
                        'key' => 'iso_code',
                        'val' => 'cu.iso+code'
                    )
                    
                );
            }
            return $this->other_fields;
        }
        //field $this->schedules
        elseif ($fields == 'schedule') {
            if (!(isset($this->schedules)) || !$this->schedules) {
                $this->schedules = array(
                    array(
                        'id_option' => '5_minutes',
                        'name' => $this->l('Every 5 minutes')
                    ),
                    array(
                        'id_option' => '30_minutes',
                        'name' => $this->l('Every 30 minutes')
                    ),
                    array(
                        'id_option' => 'hourly',
                        'name' => $this->l('Hourly')
                    ),
                    array(
                        'id_option' => 'daily',
                        'name' => $this->l('Daily')
                    ),
                    array(
                        'id_option' => 'weekly',
                        'name' => $this->l('Weekly')
                    ),
                    array(
                        'id_option' => 'monthly',
                        'name' => $this->l('Monthly')
                    ),
                );
            }
            return $this->schedules;
        }
        //field $rule_fields
        elseif ($fields == 'rule') {
            if (!(isset($this->rule_fields)) || !$this->rule_fields) {
                $order_status = $this->getOrderStatus();
                $order_exporter = new Ode_export($id_ets_export_order_rule);
                $selected_categories = !$order_exporter->id ? $this->getCategories() : explode(',', $order_exporter->category);
                $selected_countries = $this->getCountries();
                $this->rule_fields = array(
                    array(
                        'name' => 'id_ets_export_order_rule',
                        'type' => 'hidden'
                    ),
                    array(
                        'name' => 'name',
                        'type' => 'text',
                        'label' => $this->l('Rule name'),
                        'required' => true,
                        'lang' => true,
                        'tab' => 'information',
                    ),
                    array(
                        'name' => 'file_format',
                        'type' => 'select',
                        'label' => $this->l('File format'),
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 'csv',
                                    'name' => $this->l('csv'),
                                ),
                                array(
                                    'id' => 'xls',
                                    'name' => $this->l('xls'),
                                ),
                                array(
                                    'id' => 'xlsx',
                                    'name' => $this->l('xlsx'),
                                ),
                                array(
                                    'id' => 'xml',
                                    'name' => $this->l('xml'),
                                )
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'information',
                    ),
                    array(
                        'name' => 'file_name_prefix',
                        'type' => 'text',
                        'label' => $this->l('File name prefix'),
                        'tab' => 'information',
                        'class' => 'col-lg-4',
                        'autocomplete'=>false,
                    ),
                    array(
                        'name' => 'file_name_incl_name_rule',
                        'label' => $this->l('File name including rule name'),
                        'type' => 'switch',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'tab' => 'information',
                    ),
                    array(
                        'name' => 'sort_by',
                        'type' => 'select',
                        'label' => $this->l('Sort by'),
                        'options' => array(
                            'query' => array(
                                array(
                                    'name' => $this->l('Newest order'),
                                    'id' => 'o.id_order DESC',
                                ),
                                array(
                                    'name' => $this->l('Newest customer'),
                                    'id' => 'o.id_customer DESC',
                                ),
                                array(
                                    'name' => $this->l('Order total asc'),
                                    'id' => 'o.total_paid_tax_incl ASC',
                                ),
                                array(
                                    'name' => $this->l('Order total desc'),
                                    'id' => 'o.total_paid_tax_incl DESC',
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'information',
                    ),
                    array(
                        'name' => 'convert_in_currency',
                        'type' => 'select',
                        'label' => $this->l('Convert currency of all items in the order into one currency'),
                        'options' => array(
                            'query' => array_merge(
                                array(
                                    array(
                                        'id_currency' => '',
                                        'name' => $this->l('No conversion'),
                                    )
                                ),
                                Currency::getCurrenciesByIdShop(Context::getContext()->shop->id)
                            ),
                            'id' => 'id_currency',
                            'name' => 'name',
                        ),
                        'tab' => 'information',
                    ),
                    array(
                        'name' => 'send_file_via_email',
                        'label' => $this->l('Send  export file via email'),
                        'type' => 'switch',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'tab' => 'settings',
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'title_mail',
                        'label' => $this->l('Email title'),
                        'default' => $this->l('Orders exported on [date]'),
                        'default_lang' => 'Orders exported on [date]',
                        'desc' => $this->backOfficeSmarty(array('fields' => 'tag_mail')),
                        'form_group_class' => 'send_mail',
                        'required' => true,
                        'lang'=>true,
                        'tab' => 'settings'
                    ),
                    array(
                        'type' => 'textarea',
                        'name' => 'description_mail',
                        'label' => $this->l('Email content'),
                        'default_lang' => 'Here are orders exported on [date]',
                        'default' => $this->l('Here are orders exported on [date]'),
                        'desc' => $this->backOfficeSmarty(array('fields' => 'tag_mail')),
                        'autoload_rte' => true,
                        'form_group_class' => 'send_mail',
                        'required' => true,
                        'tab' => 'settings',
                        'lang'=>true,
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'receivers_mail',
                        'label' => $this->l('Receiver emails'),
                        'desc' => $this->l('Email addresses to receive export files, separated by a comma (if multiple)'),
                        'form_group_class' => 'send_mail',
                        'tab' => 'settings',
                        'required' => true,
                        'default' => Configuration::get('PS_SHOP_EMAIL'),
                    ),
                    array(
                        'name' => 'send_file_schedule',
                        'label' => $this->l('Frequency'),
                        'type' => 'select',
                        'options' => array(
                            'query' => $this->getFields('schedule'),
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                        'default' => 'daily',
                        'form_group_class' => 'send_mail schedule',
                        'tab' => 'settings',
                    ),
                    array(
                        'name' => 'send_file_time',
                        'label' => $this->l('Send on (at)'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'default' => 23,
                        'form_group_class' => 'send_mail schedule',
                        'tab' => 'settings',
                    ),
                    array(
                        'name' => 'send_file_filter',
                        'label' => $this->l('Orders to send'),
                        'type' => 'select',
                        'form_group_class' => 'send_mail',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 'all',
                                    'name' => $this->l('All orders matched filter (default)')
                                ),
                                array(
                                    'id_option' => 'new',
                                    'name' => $this->l('New orders matched filter only (orders have not been sent)')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                        'tab' => 'settings',
                        'default' => 'all',
                    ),
                    array(
                        'name' => 'export_to_server1',
                        'label' => $this->l('Save order export file to a directory on this server'),
                        'type' => 'switch',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'tab' => 'settings',
                    ),
                    array(
                        'name' => 'directory_path1',
                        'label' => $this->l('Directory path'),
                        'type' => 'text',
                        'desc' => $this->l('Only enter sub directory of root: ')._PS_ROOT_DIR_.'. '.$this->l('Leave blank will export file to the root directory (not recommended).'),
                        'form_group_class' => 'server1',
                        'tab' => 'settings',
                    ),
                    array(
                        'name' => 'server1_schedule',
                        'label' => $this->l('Frequency'),
                        'type' => 'select',
                        'options' => array(
                            'query' => $this->getFields('schedule'),
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                        'default' => 'daily',
                        'form_group_class' => 'server1 schedule',
                        'tab' => 'settings',
                    ),
                    array(
                        'name' => 'server1_time',
                        'label' => $this->l('Send on (at)'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'default' => 23,
                        'form_group_class' => 'server1 schedule',
                        'tab' => 'settings',
                    ),
                    array(
                        'name' => 'server1_filter',
                        'label' => $this->l('Orders to send'),
                        'type' => 'select',
                        'form_group_class' => 'server1',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 'all',
                                    'name' => $this->l('All orders matched filter (default)')
                                ),
                                array(
                                    'id_option' => 'new',
                                    'name' => $this->l('New orders matched filter only (orders have not been sent)')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                        'tab' => 'settings',
                        'default' => 'all',
                    ),
                    array(
                        'name' => 'delete_exported_files',
                        'label' => $this->l('Automatically delete exported files'),
                        'type' => 'select',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 'never',
                                    'name' => $this->l('Never')
                                ),
                                array(
                                    'id_option' => '1_week_old',
                                    'name' => $this->l('1 week old')
                                ),
                                array(
                                    'id_option' => '1_month',
                                    'name' => $this->l('1 month old')
                                ),
                                array(
                                    'id_option' => '3_month',
                                    'name' => $this->l('3 months old')
                                ),
                                array(
                                    'id_option' => '6_month',
                                    'name' => $this->l('6 months old')
                                ),
                                array(
                                    'id_option' => '1_year',
                                    'name' => $this->l('1 year old')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                        'default' => 'never',
                        'form_group_class' => 'server1',
                        'tab' => 'settings'
                    ),
                    array(
                        'name' => 'export_to_server2',
                        'label' => $this->l('Upload order export file to another server using FTP'),
                        'type' => 'switch',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'tab' => 'settings',
                    ),
                    array(
                        'name' => 'global_ftp',
                        'label' => $this->l('Use global FTP'),
                        'type' => 'switch',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'default' => 1,
                        'form_group_class' => 'server2',
                        'tab' => 'settings',
                    ),
                    array(
                        'name' => 'host',
                        'label' => $this->l('Host'),
                        'type' => 'text',
                        'col' => 3,
                        'required' => true,
                        'form_group_class' => 'server2 ftp',
                        'tab' => 'settings',
                    ),
                    array(
                        'name' => 'username',
                        'label' => $this->l('Username'),
                        'type' => 'text',
                        'col' => 3,
                        'required' => true,
                        'form_group_class' => 'server2 ftp',
                        'tab' => 'settings',
                    ),
                    array(
                        'name' => 'password',
                        'label' => $this->l('Password'),
                        'type' => 'password',
                        'col' => 3,

                        'required' => true,
                        'form_group_class' => 'server2 ftp',
                        'tab' => 'settings',
                    ),
                    array(
                        'name' => 'port',
                        'label' => $this->l('Port'),
                        'type' => 'text',
                        'col' => 3,
                        'form_group_class' => 'server2 ftp',
                        'tab' => 'settings',
                        'default'=>21,
                    ),
                    array(
                        'name' => 'directory_path2',
                        'label' => $this->l('Remote directory path'),
                        'type' => 'text',
                        'desc' => $this->l('Leave blank to upload file to root directory of the FTP account. Only enter sub directory if you want to specify a sub directory to upload order export file to.'),
                        'form_group_class' => 'server2',
                        'tab' => 'settings',
                    ),
                    array(
                        'name' => 'server2_schedule',
                        'label' => $this->l('Frequency'),
                        'type' => 'select',
                        'options' => array(
                            'query' => $this->getFields('schedule'),
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                        'default' => 'daily',
                        'form_group_class' => 'server2 schedule',
                        'tab' => 'settings',
                    ),
                    array(
                        'name' => 'server2_time',
                        'label' => $this->l('Send on (at)'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'default' => 23,
                        'form_group_class' => 'server2 schedule',
                        'tab' => 'settings',
                    ),
                    array(
                        'name' => 'server2_filter',
                        'label' => $this->l('Orders to send'),
                        'type' => 'select',
                        'form_group_class' => 'server2',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 'all',
                                    'name' => $this->l('All orders matched filter (default)')
                                ),
                                array(
                                    'id_option' => 'new',
                                    'name' => $this->l('New orders matched filter only (orders have not been sent)')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                        'tab' => 'settings',
                        'default' => 'all',
                    ),
                    array(
                        'name' => 'exported_fields',
                        'label' => $this->l('Order'),
                        'type' => 'checkbox',
                        'class' => 'exported_fields order_fields',
                        
                        'values' => array(
                            'query' => $this->getFields('order'),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'exported_fields',
                    ),
                    array(
                        'name' => 'exported_fields',
                        'type' => 'hidden',
                    ),
                    array(
                        'name' => 'exported_fields',
                        'label' => $this->l('Customer information'),
                        'type' => 'checkbox',
                        'class' => 'exported_fields customer_fields',
                        'values' => array(
                            'query' => $this->getFields('customer'),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'exported_fields',
                    ),
                    array(
                        'name' => 'exported_fields',
                        'label' => $this->l('Shipping address'),
                        'type' => 'checkbox',
                        'class' => 'exported_fields shipping_fields',
                        'values' => array(
                            'query' => $this->getFields('shipping'),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'exported_fields',
                    ),
                    array(
                        'name' => 'exported_fields',
                        'label' => $this->l('Invoice address'),
                        'type' => 'checkbox',
                        'class' => 'exported_fields invoice_fields',
                        'values' => array(
                            'query' => $this->getFields('invoice'),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'exported_fields',
                    ),
                    array(
                        'name' => 'exported_fields',
                        'label' => $this->l('Product'),
                        'type' => 'checkbox',
                        'class' => 'exported_fields product_fields',
                        'values' => array(
                            'query' => $this->getFields('product'),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'exported_fields',
                    ),
                    array(
                        'name' => 'exported_fields',
                        'label' => $this->l('Carrier'),
                        'type' => 'checkbox',
                        'class' => 'exported_fields carrier_fields',
                        'values' => array(
                            'query' => $this->getFields('carrier'),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'exported_fields',
                    ),
                    array(
                        'name' => 'exported_fields',
                        'label' => $this->l('Payment'),
                        'type' => 'checkbox',
                        'class' => 'exported_fields payment_fields',
                        'values' => array(
                            'query' => $this->getFields('payment'),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'exported_fields',
                    ),
                    array(
                        'name' => 'exported_fields',
                        'label' => $this->l('Other'),
                        'type' => 'checkbox',
                        'class' => 'exported_fields other_fields',
                        'values' => array(
                            'query' => $this->getFields('other'),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'exported_fields',
                    ),
                    array(
                        'name' => 'date_type',
                        'type' => 'radio',
                        'label' => $this->l('Date of order'),
                        'values' => array(
                            array(
                                'label' => $this->l('Any date'),
                                'id' => 'date_type_any_date',
                                'value' => 'any_date'
                            ),
                            array(
                                'label' => $this->l('Today'),
                                'id' => 'date_type_today',
                                'value' => 'today'
                            ),
                            array(
                                'label' => $this->l('Yesterday'),
                                'id' => 'date_type_yesterday',
                                'value' => 'yesterday'
                            ),
                            array(
                                'label' => $this->l('This month').' ('.date('m/Y').')',
                                'id' => 'date_type_this_month',
                                'value' => 'this_month'
                            ),
                            array(
                                'label' => $this->l('Month -1').' ('.date("m/Y", strtotime("-1 month")).')',
                                'id' => 'date_type_month_1',
                                'value' => 'month_1'
                            ),
                            array(
                                'label' => $this->l('This year').' ('.date('Y').')',
                                'id' => 'date_type_this_year',
                                'value' => 'this_year'
                            ),
                            array(
                                'label' => $this->l('Year -1').' ('.date("Y", strtotime("-1 Year")).')',
                                'id' => 'date_type_year_1',
                                'value' => 'year_1'
                            ),
                            array(
                                'label' => $this->l('From-To'),
                                'id' => 'date_type_from_to',
                                'value' => 'from_to'
                            ),
                            array(
                                'label' => $this->l('The day before'),
                                'id' => 'the_day_before',
                                'value' => 'day_before'
                            ),
                        ),
                        'default' => 'any_date',
                        'tab' => 'filtering_options',
                    ),
                    array(
                        'type' => 'date',
                        'name' => 'from_date',
                        'label' => $this->l('From'),
                        'tab' => 'filtering_options',
                        'form_group_class' => 'date_from_to',
                    ),
                    array(
                        'type' => 'date',
                        'name' => 'to_date',
                        'label' => $this->l('To'),
                        'tab' => 'filtering_options',
                        'form_group_class' => 'date_from_to',
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'day_before',
                        'label' => $this->l('The day before'),
                        'suffix' => $this->l('day(s)'),
                        'required' => true,
                        'col' => 4,
                        'tab' => 'filtering_options',
                        'form_group_class' => 'the_day_before',
                        'desc' => $this->l('For example, enter "1" if you want to export all orders of the day before. Set the Automation at 8 am, then every morning you will receive an order list of the day before')
                    ),
                    array(
                        'name' => 'order_status',
                        'type' => 'select',
                        'label' => $this->l('Order status'),
                        'multiple' => true,
                        'class' => 'order_status',
                        'options' => array(
                            'query' => $order_status,
                            'id' => 'id_order_state',
                            'name' => 'name',
                        ),
                        'default' => 'all',
                        'tab' => 'filtering_options',
                    ),
                    array(
                        'name' => 'specific_order',
                        'type' => 'text',
                        'label' => $this->l('Specific order'),
                        'desc' => $this->l('Enter order ID or order ID range. Example: 10; 08 - 16; etc.'),
                        'tab' => 'filtering_options',
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'specific_customer',
                        'tab' => 'filtering_options',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Specific customer'),
                        'name' => 'specific_customer_select',
                        'tab' => 'filtering_options',
                        'class' => 'col-lg-6',
                        'placeholder' => $this->l('Search for specific customer by id, name, email'),
                        'suffix' => Module::getInstanceByName('ets_ordermanager')->displayText('','i',array('class'=>'icon icon-search')),
                        'form_group_class' => 'customer_list_id',
                        'col'=> '9'
                    ),
                    array(
                        'label' => $this->l('Customer groups'),
                        'type' => 'select',
                        'name' => 'customer_group',
                        'multiple' => true,
                        'form_group_class' => 'customer_form',
                        'options' => array(
                            'query' => $this->getGroupCustomers(),
                            'id' => 'id_group',
                            'name' => 'name'
                        ),
                        'default' => 'all',
                        'tab' => 'filtering_options'
                    ),
                    array(
                        'label' => $this->l('Countries'),
                        'type' => 'select',
                        'name' => 'id_country',
                        'multiple' => true,
                        'form_group_class' => 'customer_form',
                        'options' => array(
                            'query' => $selected_countries,
                            'id' => 'id_country',
                            'name' => 'name'
                        ),
                        'default' => 'all',
                        'tab' => 'filtering_options'
                    ),
                    array(
                        'label' => $this->l('Carriers'),
                        'type' => 'select',
                        'name' => 'order_carrier',
                        'multiple' => true,
                        'options' => array(
                            'query' => $this->getCarriers(),
                            'id' => 'id_reference',
                            'name' => 'name'
                        ),
                        'default' => 'all',
                        'tab' => 'filtering_options'
                    ),
                    array(
                        'label' => $this->l('Payment methods'),
                        'type' => 'select',
                        'multiple' => true,
                        'name' => 'payment_method',
                        'class' => 'payment_method',
                        'options' => array(
                            'query' => $this->getPaymentMethods(),
                            'id' => 'module',
                            'name' => 'payment'
                        ),
                        'default' => 'all',
                        'tab' => 'filtering_options',
                    ),
                    array(
                        'label' => $this->l('Manufacturers'),
                        'type' => 'select',
                        'multiple' => true,
                        'name' => 'manufacturer',
                        'class' => 'manufacturer',
                        'options' => array(
                            'query' => $this->getManufacturers(),
                            'id' => 'id_manufacturer',
                            'name' => 'name'
                        ),
                        'default' => 'all',
                        'tab' => 'filtering_options',
                    ),
                    array(
                        'label' => $this->l('Suppliers'),
                        'type' => 'select',
                        'multiple' => true,
                        'name' => 'supplier',
                        'class' => 'supplier',
                        'options' => array(
                            'query' => $this->getSuppliers(),
                            'id' => 'id_supplier',
                            'name' => 'name'
                        ),
                        'default' => 'all',
                        'tab' => 'filtering_options',
                    ),
                    //end.
                    array(
                        'label' => $this->l('Categories'),
                        'name' => 'category',
                        'type' => 'categories',
                        'tree' => array(
                            'id' => 'categories-tree',
                            'selected_categories' => $selected_categories,
                            'root_category' => $this->context->shop->getCategory(),
                            'use_checkbox' => true,
                            'use_search' => true,
                        ),
                        'class' => 'category',
                        'tab' => 'filtering_options',
                    ),
                    array(
                        'label' => $this->l('Include specific products'),
                        'type' => 'text',
                        'name' => 'specific_product',
                        'col' => '6',
                        'class' => 'specific_product hide',
                        'tab' => 'filtering_options',
                    ),
                    array(
                        'label' => $this->l('Order total from'),
                        'type' => 'text',
                        'name' => 'spent_from',
                        'tab' => 'filtering_options',
                        'class' => 'col-lg-4',
                        'suffix' => $this->context->currency->iso_code,
                    ),
                    array(
                        'label' => $this->l('To'),
                        'type' => 'text',
                        'name' => 'spent_to',
                        'tab' => 'filtering_options',
                        'class' => 'col-lg-4',
                        'suffix' => $this->context->currency->iso_code,
                    ),
                    array(
                        'label' =>'',
                        'type' => 'hidden',
                        'name' => 'current_tab',
                    )
                );
            }
            if(!$this->checkGLOALFTP())
            {
                foreach($this->rule_fields as $key=> $rule_field)
                {
                    if($rule_field['name']=='global_ftp')
                        unset($this->rule_fields[$key]);
                }
            }
            return $this->rule_fields;
        }
    }
    public function getEmployees($activeOnly)
    {
        return  Db::getInstance()->executeS('
			SELECT e.`id_employee`, p.id_profile, CONCAT(`firstname`," ", `lastname`) as `employee_name`, `email`, pl.`name` as `profile_name`
			FROM `' . _DB_PREFIX_ . 'employee` e '.Shop::addSqlAssociation('employee','e').'
			LEFT JOIN `' . _DB_PREFIX_ . 'profile` p ON (e.id_profile = p.id_profile)
			LEFT JOIN `' . _DB_PREFIX_ . 'profile_lang` pl ON (p.`id_profile` = pl.`id_profile`)
			WHERE pl.`id_lang` = ' . (int)$this->context->language->id. ($activeOnly ? ' `active` = 1 ' : '') . '
			ORDER BY `id_employee` ASC
		');
    }

    public function getGroupCustomers()
    {
        $groups = Db::getInstance()->executeS('
            SELECT g.id_group, gl.name 
            FROM `' . _DB_PREFIX_ . 'group` g, `' . _DB_PREFIX_ . 'group_lang` gl 
            WHERE g.id_group=gl.id_group and gl.id_lang = ' . (int)$this->context->language->id
        );
        return array_merge(array(array('id_group' => 'all', 'name' => $this->l('All'))), $groups);
    }

    public function getCarriers()
    {
        $carriers = array();
        if (($results = Db::getInstance()->executeS('SELECT id_reference, name FROM `' . _DB_PREFIX_ . 'carrier` group by id_reference'))) {
            $carriers[] = array(
                'id_reference' => 'all',
                'name' => $this->l('All')
            );
            foreach ($results as $carrier) {
                if ($carrier['name'] == '0') {
                    $carrier['name'] = $this->l('Demo shop');
                }
                $carriers[] = $carrier;
            }
        }
        return $carriers;
    }

    public function getPaymentMethods()
    {
        $payments = Db::getInstance()->executeS('
            SELECT payment, module 
            FROM `' . _DB_PREFIX_ . 'orders` group by module
        ');
        return array_merge(array(array('payment' => $this->l('All'), 'module' => 'all')), $payments);
    }

    public function getCategories()
    {

        $cats = array();
        if ($results = Category::getCategories($this->context->language->id)) {
            foreach ($results as $cat) {
                foreach ($cat as $id_category => $sub_cat) {
                    $cats[] = (int)$id_category;
                    unset($sub_cat);
                }
            }
        }
        return $cats;
    }

    public function getManufacturers($active = false)
    {
        $manus = Db::getInstance()->executeS('
            SELECT id_manufacturer, name 
            FROM `' . _DB_PREFIX_ . 'manufacturer`
            WHERE 1 ' . ($active ? 'active = ' . (int)$active : '') . '
            GROUP BY id_manufacturer
        ');
        return array_merge(array(array('id_manufacturer' => 'all', 'name' => $this->l('All'))), $manus);
    }

    public function getSuppliers($active = false)
    {
        $suppliers = Db::getInstance()->executeS('
            SELECT id_supplier, name 
            FROM `' . _DB_PREFIX_ . 'supplier`
            WHERE 1 ' . ($active ? 'active = ' . (int)$active : '') . '
            GROUP BY id_supplier
        ');
        return array_merge(array(array('id_supplier' => 'all', 'name' => $this->l('All'))), $suppliers);
    }

    public function getCountries()
    {
        $selected_countries = Country::getCountries($this->context->language->id);
        if ($selected_countries) {
            $countries = array();
            $countries[] = array(
                'id_country' => 'all',
                'name' => $this->l('All'),
            );
            foreach ($selected_countries as $id => $selected_country) {
                $countries[] = array(
                    'id_country' => $id,
                    'name' => isset($selected_country['name']) ? $selected_country['name'] : $this->l('Unknown'),
                );
            }
            if ($countries) {
                $selected_countries = $countries;
            }
        }
        return $selected_countries;
    }

    public function getOrderStatus()
    {
        $order_status = Db::getInstance()->executeS('
            SELECT os.id_order_state, osl.name
            FROM `' . _DB_PREFIX_ . 'order_state` os 
            LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.id_order_state=osl.id_order_state) 
            WHERE osl.id_lang=' . (int)$this->context->language->id
        );
        return array_merge(array(array('id_order_state' => 'all', 'name' => $this->l('All'))), $order_status);
    }

    public function backOfficeSmarty($args = array())
    {
        $this->smarty->assign($args);
        return $this->display('defines.tpl');
    }

    public function display($template)
    {
        if (!$this->module)
            return;
        return $this->module->display($this->module->getLocalPath(), $template);
    }

    public function l($string)
    {
        return Translate::getModuleTranslation(_ETS_ODE_MODULE_, $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public function checkGLOALFTP()
    {
        return Configuration::get('ETS_ODE_HOST') && Configuration::get('ETS_ODE_USERNAME') && Configuration::get('ETS_ODE_PASSWORD');
    }
}