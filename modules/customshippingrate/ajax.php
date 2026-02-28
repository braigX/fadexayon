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

$context = Context::getContext();

if (!Tools::getValue('ajax') || Tools::getValue('token') != sha1(_COOKIE_KEY_.'customshippingrate')) {
    die;
}

if (Tools::getValue('id_cart') && Tools::getValue('value')
&& Tools::getValue('name') && Tools::getValue('delay')) {
    // Set custom shipping rate for this customer and cart
    $id_cart = Tools::getValue('id_cart');
    $id_address_delivery = (int)Tools::getValue('id_address_delivery');
    $id_customer2 = Tools::getValue('id_customer');
    $shipping_price = round((float)Tools::getValue('value'), 2);
    $carrier_name = Tools::getValue('name');
    $carrier_delay = Tools::getValue('delay');
    $cart = new Cart($id_cart);
    $nbProducts = $cart->getNbProducts($id_cart);
    $totalWeight = $cart->getTotalWeight();
    $orderTotal = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
    $date_add = date('Y-m-d H:i:s');
    $id_customer = $cart->id_customer;


    // Prepare data for logging
    $logData = [
        'id_cart' => $id_cart,
        'id_address_delivery' => $id_address_delivery,
        'id_customer2' => $id_customer2,
        'shipping_price' => $shipping_price,
        'carrier_name' => $carrier_name,
        'carrier_delay' => $carrier_delay,
        'nbProducts' => $nbProducts,
        'totalWeight' => $totalWeight,
        'orderTotal' => $orderTotal,
        'date_add' => $date_add,
        'id_customer' => $id_customer
    ];

    // Convert to JSON format for structured logging
    $logString = json_encode($logData, JSON_PRETTY_PRINT);

    // Write to log file
    file_put_contents(__DIR__ . '/logfiles.txt', $logString . "\n", FILE_APPEND);


    $sql = 'REPLACE INTO `'._DB_PREFIX_.'customshippingrate` (`id_cart`, `id_customer`,
    `id_address_delivery`, `carrier_name`, `carrier_delay`, `shipping_price`,
    `number_products`, `total_weight`, `order_total`, `date_add`)
    VALUES ('.(int)$id_cart.','.(int)$id_customer.','.(int)$id_address_delivery.',
    "'.pSQL($carrier_name).'","'.pSQL($carrier_delay).'",'.pSQL($shipping_price).',
    '.pSQL($nbProducts).','.pSQL($totalWeight).',"'.$orderTotal.'","'.$date_add.'")';
    return Db::getInstance()->execute($sql);
}

if (Tools::getValue('id_cart') && Tools::getValue('id_customer') && Tools::getValue('remove')) {
    // Remove custom shipping rate for this customer and cart
    $id_cart = Tools::getValue('id_cart');
    $id_customer = Tools::getValue('id_customer');
    $remove = Tools::getValue('remove');
    if ($remove == 1) {
        $sql = 'DELETE FROM `'._DB_PREFIX_.'customshippingrate` WHERE `id_cart` = '.(int)$id_cart.' 
        AND `id_customer` ='.(int)$id_customer;
        return Db::getInstance()->execute($sql);
    }
}
