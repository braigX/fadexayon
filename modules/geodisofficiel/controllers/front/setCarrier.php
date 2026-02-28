<?php
/**
 * 2024 Novatis Agency - www.novatis-paris.fr.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@novatis-paris.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    NOVATIS <info@novatis-paris.fr>
 *  @copyright 2024 NOVATIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisOption.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCartCarrier.php';

class GeodisOfficielSetCarrierModuleFrontController extends ModuleFrontController
{
    protected $price;
    protected $result;

    public $ssl = true;

    public function __construct()
    {
        parent::__construct();
        $this->page_name = 'ajax';

        $this->result = array();
    }

    public function initContent()
    {
        if (!Tools::getIsset('idCarrier')) {
            $this->sendError('Missing parameter idCarrier.');
        }

        if (!Tools::getIsset('token')) {
            $this->sendError('Missing parameter token.');
        }

        if (Tools::getIsset('token') != Context::getContext()->cookie->geodisToken) {
            $this->sendError('Invalid token.');
        }

        $idCarrier = (int) Tools::getValue('idCarrier');

        $cart = Context::getContext()->cart;

        $carrier = new GeodisCarrier($idCarrier);

        // Get idPrestashopCarrier submited if exist else idPCarrier
        $idPCarrier = $cart->id_carrier;
        $taxesRate = 0.0;
        if (Tools::getIsset('idPrestaShopCarrier')) {
            $idPCarrier = (int) Tools::getValue('idPrestaShopCarrier');
            if (!empty($cart->id_address_delivery)) {
                $pCarrier = new Carrier($idPCarrier);
                $addressDelivery = new Address($cart->id_address_delivery);
                $taxesRate = $pCarrier->getTaxesRate($addressDelivery);
            }
        }

        $this->price = $carrier->getInitialPriceFromCart($cart, $idPCarrier);

        if ($idCarrier != $carrier->id) {
            $this->sendError('Carrier do not exists.');
        }

        if (!$carrier->active) {
            $this->sendError('Carrier is not active.');
        }

        $idOptionList = Tools::getValue('idOptionList', array());
        $carrierOptionsPriceImpact = 0;
        foreach ($idOptionList as $idOption) {
            $option = $carrier->getCarrierOption((int) $idOption);

            if (!$option) {
                $this->sendError('Option do not exists.');
            }

            $optionPriceImpact = $option->price_impact;
            if (($optionPriceImpact > 0) && ($taxesRate > 0)) {
                $optionPriceImpact = $optionPriceImpact + ($optionPriceImpact * ($taxesRate / 100));
            }

            $carrierOptionsPriceImpact += $optionPriceImpact;
        }
        if (($carrier->enable_free_shipping == 1) && ($cart->getOrderTotal(true, Cart::ONLY_PRODUCTS) >= $carrier->free_shipping_from)) {
            $carrierOptionsPriceImpact = 0;
        }
        $this->price += $carrierOptionsPriceImpact;


        $cartCarrier = GeodisCartCarrier::loadFromIdCart($cart->id);
        if (!$cartCarrier) {
            $cartCarrier = new GeodisCartCarrier();
            $cartCarrier->id_cart = $cart->id;
        }
        $cartCarrier->id_carrier = $carrier->id;
        $cartCarrier->id_option_list = implode(',', $idOptionList);
        $cartCarrier->code_withdrawal_point = Tools::getValue('codeWithdrawalPoint');
        $cartCarrier->code_withdrawal_agency = Tools::getValue('codeWithdrawalAgency');
        $cartCarrier->info = json_encode(Tools::getValue('info'));
        $cartCarrier->save();

        $this->sendSuccess();
    }

    protected function sendSuccess()
    {
        $this->result['status'] = 'success';
        if ($this->price <= 0) {
            $this->result['price'] = $this->l('Free');
        } else {
            $this->result['price'] = $this->translator->trans(
                '%price% tax incl.',
                array('%price%' => Tools::displayPrice($this->price)),
                'Shop.Theme.Checkout'
            );
        }

        $this->send();
    }

    protected function sendError($message)
    {
        $this->result['status'] = 'error';
        $this->result['message'] = $message;

        $this->send();
    }

    protected function send()
    {
        echo json_encode($this->result);
        exit;
    }
}
