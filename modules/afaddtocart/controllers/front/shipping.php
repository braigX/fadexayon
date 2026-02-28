<?php
/**
 *  2023 ALGO-FACTORY.COM
 *
 *  NOTICE OF LICENSE
 *
 *  @author        Algo Factory <contact@algo-factory.com>
 *  @copyright     Copyright (c) 2020 Algo Factory
 *  @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 *
 *  @version       1.0.0
 *
 *  @website       www.algo-factory.com
 *
 *  You can not resell or redistribute this software.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AfaddtocartShippingModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        // 1. Define log file path (same folder as this controller)
        $logFile = __DIR__ . '/shipping.log';
        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] initContent started\n", FILE_APPEND);

        // 2. Grab context, currency, cart
        $context = Context::getContext();
        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Context loaded\n", FILE_APPEND);

        $currency = $context->currency;
        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Currency: " . $currency->iso_code . "\n", FILE_APPEND);

        $cart = $context->cart;
        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Cart ID: " . $cart->id . "\n", FILE_APPEND);

        // 3. Compute totals
        $totalCartAmountWithTaxes = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Total PRODUCTS (with tax): " . $totalCartAmountWithTaxes . "\n", FILE_APPEND);

        $freeShippingThreshold = (float) Configuration::get('PS_SHIPPING_FREE_PRICE');
        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Free-shipping threshold: " . $freeShippingThreshold . "\n", FILE_APPEND);

        // 4. Calculate remaining
        $remainingShipping = $freeShippingThreshold - $totalCartAmountWithTaxes;
        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Remaining amount for free shipping (raw): " . $remainingShipping . "\n", FILE_APPEND);

        // 5. Format remaining or set to false
        $remainingShippingText = false;
        if ($remainingShipping > 0) {
            $remainingShippingText = Tools::displayPrice(abs($remainingShipping), $currency);
            file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Remaining formatted: " . $remainingShippingText . "\n", FILE_APPEND);
        } else {
            file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Threshold reached, no remaining amount\n", FILE_APPEND);
        }

        // 6. Return JSON
        ob_end_clean();
        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Sending JSON response and exiting\n", FILE_APPEND);

        header('Content-Type: application/json');
        exit(json_encode($remainingShippingText));
    }
}
