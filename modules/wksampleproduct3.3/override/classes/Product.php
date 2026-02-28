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

class Product extends ProductCore
{
    public static function priceCalculation(
        $id_shop,
        $id_product,
        $id_product_attribute,
        $id_country,
        $id_state,
        $zipcode,
        $id_currency,
        $id_group,
        $quantity,
        $use_tax,
        $decimals,
        $only_reduc,
        $use_reduc,
        $with_ecotax,
        &$specific_price,
        $use_group_reduction,
        $id_customer = 0,
        $use_customer_price = true,
        $id_cart = 0,
        $real_quantity = 0,
        $id_customization = 0
    ) {
        // Admin order carrier list issue: Solution only works on version above 1.7.7.1
        if (version_compare(_PS_VERSION_, '1.7.7.1', '>=')
            && ((int) $id_cart > 0)
            && Module::isEnabled('wksampleproduct')
        ) {
            Module::getInstanceByName('wksampleproduct');
            $objSampleCart = new WkSampleCart();
            $isSampleProduct = $objSampleCart->getSampleCartProduct($id_cart, $id_product, $id_product_attribute);
            if ($isSampleProduct) {
                $orderId = Order::getIdByCartId((int) $id_cart);
                if ($orderId) {
                    // getPriceFromOrder is available since PS V1.7.7.1
                    return Product::getPriceFromOrder(
                        $orderId,
                        $id_product,
                        (int) $id_product_attribute,
                        $use_tax,
                        true,
                        $with_ecotax
                    );
                }
            }
        }

        return parent::priceCalculation(
            $id_shop,
            $id_product,
            $id_product_attribute,
            $id_country,
            $id_state,
            $zipcode,
            $id_currency,
            $id_group,
            $quantity,
            $use_tax,
            $decimals,
            $only_reduc,
            $use_reduc,
            $with_ecotax,
            $specific_price,
            $use_group_reduction,
            $id_customer,
            $use_customer_price,
            $id_cart,
            $real_quantity,
            $id_customization
        );
    }
}
