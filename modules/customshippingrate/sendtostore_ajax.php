<?php
/**
* 2018 Prestamatic
*
* NOTICE OF LICENSE
*
*  @author    Prestamatic
*  @copyright 2018 Prestamatic
*  @license   Licensed under the terms of the MIT license
*  @link      https://prestamatic.co.uk
*/
include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('../../modules/customshippingrate/customshippingrate.php');

$module = new CustomShippingRate();
$context = Context::getContext();

if (Tools::getValue('token') != sha1(_COOKIE_KEY_.'customshippingrate'.$context->customer->secure_key)) {
    die('1');
}

if (Module::isEnabled('customshippingrate') &&
    Tools::getValue('action') == 'submitQuoteRequest' &&
    Tools::getValue('token') == sha1(_COOKIE_KEY_.'customshippingrate'.$context->customer->secure_key)) {
    $id_contact = Tools::getValue('id_contact');
    if ($id_contact == 0) {
        $contact_name = '';
        $contact_email = Configuration::get('CUSTOMSHIP_EMAIL_TO');
    } else {
        $contact = new Contact($id_contact, (int)$context->cookie->id_lang);
        $contact_name = $contact->name;
        $contact_email =  $contact->email;
    }
    $email = Tools::getValue('from');
    $message = Tools::getValue('message');
    $customerName = $context->cookie->customer_firstname ? $context->cookie->customer_firstname
    .' '.$context->cookie->customer_lastname : $module->l('Customer', 'sendtostore_ajax');
    $customer = $context->customer;
    if (!$customer->id) {
        $customer->getByEmail($email);
    }

    $id_cart = (int)Tools::getValue('id_cart');
    $id_address_delivery = (int)Tools::getValue('id_address_delivery');
    $id_customer = (int)Tools::getValue('id_customer');
    $shipping_price = '-0.01';
    $carrier_name = '';
    $carrier_delay = '';
    $cart = new Cart($id_cart);
    $nbProducts = $cart->getNbProducts($id_cart);
    $totalWeight = $cart->getTotalWeight();
    $orderTotal = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
    $date_add = date('Y-m-d H:i:s');

    $sql = 'REPLACE INTO `'._DB_PREFIX_.'customshippingrate` (`id_cart`, `id_customer`,
    `id_address_delivery`, `carrier_name`, `carrier_delay`, `shipping_price`,
    `number_products`, `total_weight`, `order_total`, `date_add`)
    VALUES ('.(int)$id_cart.','.(int)$id_customer.','.(int)$id_address_delivery.',
    "'.pSQL($carrier_name).'","'.pSQL($carrier_delay).'","'.pSQL($shipping_price).'",
    '.pSQL($nbProducts).','.pSQL($totalWeight).',"'.$orderTotal.'","'.$date_add.'")';
    Db::getInstance()->execute($sql);
    
    $templateVars = array(
        '{customer}' => $customerName,
        '{message}' => Tools::nl2br(Tools::stripslashes($message)),
        '{email}' =>  $email,
    );

    /* Email sending */
    if (!Mail::Send(
        (int)$context->cookie->id_lang,
        'send_request_to_store',
        $module->l('Customer has requested a shipping quote', 'sendtostore_ajax'),
        $templateVars,
        $contact_email,
        $contact_name,
        null,
        null,
        null,
        null,
        dirname(__FILE__).'/mails/'
    )) {
        die('1');
    }

    $ct = new CustomerThread();
    if (isset($customer->id)) {
        $ct->id_customer = (int)$customer->id;
    }
    $ct->id_shop = (int)$context->shop->id;
    $ct->id_order = 0;
    if ($id_product = (int)Tools::getValue('id_product')) {
        $ct->id_product = $id_product;
    }
    $ct->id_contact = (int)$id_contact;
    $ct->id_lang = (int)$context->language->id;
    $ct->email = $email;
    $ct->status = 'open';
    $ct->token = Tools::passwdGen(12);
    $ct->add();
    if ($ct->id) {
        $cm = new CustomerMessage();
        $cm->id_customer_thread = $ct->id;
        $cm->message = $message;
        $cm->ip_address = (int)ip2long(Tools::getRemoteAddr());
        $cm->user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (!$cm->add()) {
            die('1');
        }
    } else {
        die('1');
    }
    die('0');
}
die('1');
