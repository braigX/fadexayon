<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Order extends OrderCore
{
    public function refreshShippingCost()
    {
        if (Module::isEnabled('wksampleproduct')) {
            if (empty($this->id)) {
                return false;
            }

            if (!Configuration::get('PS_ORDER_RECALCULATE_SHIPPING')) {
                return $this;
            }

            $fake_cart = new Cart((int) $this->id_cart);
            $new_cart = $fake_cart->duplicate();
            $new_cart = $new_cart['cart'];

            // assign order id_address_delivery to cart
            $new_cart->id_address_delivery = (int) $this->id_address_delivery;

            // assign id_carrier
            $new_cart->id_carrier = (int) $this->id_carrier;

            // remove all products : cart (maybe change in the meantime)
            foreach ($new_cart->getProducts() as $product) {
                $new_cart->deleteProduct((int) $product['id_product'], (int) $product['id_product_attribute']);
            }

            // add real order products
            $context = Context::getContext();
            $context->customer = new Customer($new_cart->id_customer);
            $context->cart = $new_cart;
            $addressObj = Address::initialize($fake_cart->id_address_delivery);
            $context->country = new Country($addressObj->id_country);
            Module::getInstanceByName('wksampleproduct');
            foreach ($this->getProducts() as $product) {
                $objSampleCart = new WkSampleCart();
                $isSampleProduct = $objSampleCart->getSampleCartProduct(
                    $fake_cart->id,
                    $product['product_id'],
                    $product['product_attribute_id']
                );
                if ($isSampleProduct) {
                    // $context->cookie->sampleProductId = $product['product_id'];
                    // $context->cookie->sampleProductIdAttr = $product['product_attribute_id'];
                    $context->cookie->__set('sampleProductId', $product['product_id']);
                    $context->cookie->__set('sampleProductIdAttr', $product['product_attribute_id']);
                }
                $new_cart->updateQty($product['product_quantity'], (int) $product['product_id']);
                unset($context->cookie->sampleProductId);
                unset($context->cookie->sampleProductIdAttr);
            }

            // get new shipping cost
            $base_total_shipping_tax_incl = (float) $new_cart->getPackageShippingCost(
                (int) $new_cart->id_carrier,
                true,
                null
            );
            $base_total_shipping_tax_excl = (float) $new_cart->getPackageShippingCost(
                (int) $new_cart->id_carrier,
                false,
                null
            );
            // calculate diff price, then apply new order totals
            $diff_shipping_tax_incl = $this->total_shipping_tax_incl - $base_total_shipping_tax_incl;
            $diff_shipping_tax_excl = $this->total_shipping_tax_excl - $base_total_shipping_tax_excl;

            $this->total_shipping_tax_excl = $this->total_shipping_tax_excl - $diff_shipping_tax_excl;
            $this->total_shipping_tax_incl = $this->total_shipping_tax_incl - $diff_shipping_tax_incl;
            $this->total_shipping = $this->total_shipping_tax_incl;
            $this->total_paid_tax_excl = $this->total_paid_tax_excl - $diff_shipping_tax_excl;
            $this->total_paid_tax_incl = $this->total_paid_tax_incl - $diff_shipping_tax_incl;
            $this->total_paid = $this->total_paid_tax_incl;
            $this->update();

            // save order_carrier prices, we'll save order right after this in update() method
            $order_carrier = new OrderCarrier((int) $this->getIdOrderCarrier());
            $order_carrier->shipping_cost_tax_excl = $this->total_shipping_tax_excl;
            $order_carrier->shipping_cost_tax_incl = $this->total_shipping_tax_incl;
            $order_carrier->update();

            // remove fake cart
            $new_cart->delete();

            return $this;
        } else {
            return parent::refreshShippingCost();
        }
    }
}
