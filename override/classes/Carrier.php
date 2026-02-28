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
class Carrier extends CarrierCore
{
    /*
    * module: wksampleproduct
    * date: 2026-01-29 11:42:05
    * version: 5.3.3
    */
    public static function getAvailableCarrierList(
        Product $product,
        $id_warehouse,
        $id_address_delivery = null,
        $id_shop = null,
        $cart = null,
        &$error = []
    ) {
        $parentCarriers = parent::getAvailableCarrierList($product, $id_warehouse, $id_address_delivery, $id_shop, $cart, $error);
        if (Module::isEnabled('wksampleproduct')) {
            if (null === $id_shop) {
                $id_shop = Context::getContext()->shop->id;
            }
            if (null === $cart) {
                $cart = Context::getContext()->cart;
            }
            $objModule = Module::getInstanceByName('wksampleproduct');
            $objSampleCart = new WkSampleCart();
            $sampleCart = $objSampleCart->getSampleCartProduct(
                $cart->id,
                $product->id
            );
            if ($sampleCart) {
                $objSampleProductMap = new WkSampleProductMap();
                $sample = $objSampleProductMap->getSampleProduct($product->id);
                if ($sample && $sample['active']) {
                    $product->weight = (float) $sample['weight'];
                    $cache_id = 'Carrier::getAvailableCarrierList_' . (int) $product->id . '_' . (int) $id_shop;
                    if (!Cache::isStored($cache_id)) {
                        if (null === $cart) {
                            $cart = Context::getContext()->cart;
                        }
                        $productCarriers = $objModule->getSampleProductCarriers($cart->id, $product->id, $id_shop);
                        $productCarriers = array_column($productCarriers, 'id_carrier');
                        foreach ($parentCarriers as $key => $carrier) {
                            if (!in_array($carrier, $productCarriers)) {
                                unset($parentCarriers[$key]);
                            }
                        }
                        Cache::store($cache_id, $parentCarriers);
                    }
                }
            }
        }
        return $parentCarriers;
    }
}
