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

class WkSampleProductSampleSpecificPriceModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $idProduct = Tools::getValue('id_product');
        $idAttr = 0;
        $objSampleMap = new WkSampleProductMap();
        $objModule = Module::getInstanceByName('wksampleproduct');
        if ($sample = $objSampleMap->getSampleProduct($idProduct)) {
            if (Tools::getValue('ajax') && (Tools::getValue('action') == 'getSampleInfo')) {
                $idAttr = Tools::getValue('attribute_id');
                /** @var WkSampleProduct $objModule */
                $cartQty = $objModule->getProductQuantityInCart($idProduct, $idAttr);
                $objSampleCart = new WkSampleCart();
                /** @var WkSampleCart $objSampleCart */
                $sampleCart = $objSampleCart->getSampleCartProduct($this->context->cart->id, $idProduct, $idAttr);
                unset($this->context->cookie->sampleProductId);
                unset($this->context->cookie->sampleProductIdAttr);
                $productStock = StockAvailable::getQuantityAvailableByProduct($idProduct, $idAttr);
                $sample['max_cart_qty'] = (($sample['max_cart_qty'] > 0) && ($sample['max_cart_qty'] < $productStock)) ?
                $sample['max_cart_qty'] :
                $productStock;
                $lowStockQty = (int) Configuration::get('PS_LAST_QTIES');
                $sampleQtyWarning = (($sample['max_cart_qty'] - $cartQty) < $lowStockQty);
                if ($sampleCart) {
                    if (($sample['max_cart_qty'] > 0) && ($cartQty >= $sample['max_cart_qty'])) {
                        exit(json_encode([
                            'status' => 'ko',
                            'addedStandard' => false,
                            'showStockWarning' => $sampleQtyWarning,
                            'availableQuantity' => 0,
                            'maxSampleQty' => $sample['max_cart_qty'],
                            'sampleInCart' => 1,
                        ]));
                    } else {
                        exit(json_encode([
                            'status' => 'ok',
                            'addedStandard' => false,
                            'showStockWarning' => $sampleQtyWarning,
                            'maxSampleQty' => $sample['max_cart_qty'],
                            'availableQuantity' => $sample['max_cart_qty'] - $cartQty,
                            'sampleInCart' => 1,
                        ]));
                    }
                } else {
                    exit(json_encode([
                        'status' => 'ok',
                        'addedStandard' => ($cartQty > 0) ? true : false,
                        'showStockWarning' => $sampleQtyWarning,
                        'maxSampleQty' => $sample['max_cart_qty'],
                        'availableQuantity' => $sample['max_cart_qty'],
                        'sampleInCart' => 0,
                    ]));
                }
            } else {
                // Getting Data

                if (Configuration::get('WK_SAMPLE_LOGGED_ONLY') == 1) {
                    if (!$this->context->customer->isLogged()) {
                        exit(json_encode([
                            'status' => 'ko',
                            'msg' => [
                                $this->module->l('Please log in to purchase a sample product.', 'samplespecificprice'),
                            ],
                        ]));
                    }
                }
                $defaultAttr = Product::getDefaultAttribute($idProduct);
                if ($defaultAttr && ($groups = Tools::getValue('group'))) {
                    $idAttr = (int) Product::getIdProductAttributeByIdAttributes($idProduct, $groups);
                }
                if ($this->context->cart->id) {
                    $sampleCartObj = new WkSampleCart();
                    $sampleCartObj->checkStandardProductInCart($idProduct, $idAttr);
                    $sampleCartObj->checkMaximumInCart($idProduct, $idAttr);
                }
                // $this->context->cookie->sampleProductId = $idProduct;
                // $this->context->cookie->sampleProductIdAttr = $idAttr;
                $this->context->cookie->__set('sampleProductId', $idProduct);
                $this->context->cookie->__set('sampleProductIdAttr', $idAttr);
                exit(json_encode([
                    'status' => 'ok',
                    'msg' => $this->module->l('success'),
                ]));
            }
        } else {
            exit(json_encode([
                'status' => 'ko',
                'msg' => [
                    $this->module->l('The sample is not available anymore.', 'samplespecificprice'),
                ],
            ]));
        }
    }
}
