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
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class CartController extends CartControllerCore
{
    protected function processChangeProductInCart(): void
    {
        $context = Context::getContext();
        if (Module::isEnabled('wksampleproduct')) {
            include_once _PS_MODULE_DIR_ . 'wksampleproduct/classes/WkSampleCart.php';
            if (!Tools::getValue('sample_product')) {
                if (Tools::getIsset('add') && Tools::getValue('add')) {
                    $idProduct = $this->id_product;
                    $product = new Product($idProduct);
                    $idAttr = $this->id_product_attribute;
                    $hasAttr = $product->hasAttributes();
                    if ($hasAttr > 0 && $idAttr == 0) {
                        $idAttr = Product::getDefaultAttribute($idProduct);
                    }
                    $objWkSampleCart = new WkSampleCart();
                    $cartProducts = $context->cart->getProducts();

                    if (!empty($cartProducts)) {
                        $isSampleExist = $objWkSampleCart->getSampleCartProduct(
                            $context->cart->id,
                            $idProduct,
                            $idAttr
                        );
                        if (is_array($isSampleExist) && count($isSampleExist) > 0) {
                            $this->errors[] = $this->trans(
                                'Product sample already present in the cart.',
                                [],
                                'Shop.Notifications.Error'
                            );
                            return;
                        }
                    }
                }
            }
        }
        parent::processChangeProductInCart();
    }

    protected function areProductsAvailable(): bool|string
    {
        $products = $this->context->cart->getProducts();

        foreach ($products as $product) {
            $currentProduct = new Product();
            $currentProduct->hydrate($product);

            if ($currentProduct->hasAttributes() && $product['id_product_attribute'] === '0') {
                return $this->trans(
                    'The item %product% in your cart is now a product with attributes. Please delete it and choose one of its combinations to proceed with your order.',
                    ['%product%' => $product['name']],
                    'Shop.Notifications.Error'
                );
            }
        }

        if (Module::isEnabled('wksampleproduct')) {
            include_once _PS_MODULE_DIR_ . 'wksampleproduct/classes/WkSampleCart.php';
            include_once _PS_MODULE_DIR_ . 'wksampleproduct/classes/WkSampleProductMap.php';

            $objSampleCart = new WkSampleCart();
            $objSampleProductMap = new WkSampleProductMap();
            foreach ($products as $productList) {
                if ($objSampleCart->checkProductQtyInCart(
                    $this->context->cart,
                    $productList['id_product'],
                    $productList['cart_quantity'],
                    $productList['id_product_attribute']
                )) {
                    $sample = $objSampleProductMap->getSampleProduct($productList['id_product']);

                    return $this->errors[] = $this->trans(
                        sprintf(
                            'Only %d quantity allowed to buy in single cart.',
                            $sample['max_cart_qty']
                        ),
                        [],
                        'Shop.Notifications.Error'
                    );
                }
            }
        }

        $product = $this->context->cart->checkQuantities(true);

        if (true === $product || !is_array($product)) {
            return true;
        }

        if ($product['active']) {
            return $this->trans(
                'You can only buy %quantity% "%product%". Please adjust the quantity in your cart to continue.',
                [
                    '%product%' => $product['name'],
                    '%quantity%' => $product['quantity_available'],
                ],
                'Shop.Notifications.Error'
            );
        }

        return $this->trans(
            'This product (%product%) is no longer available.',
            ['%product%' => $product['name']],
            'Shop.Notifications.Error'
        );
    }
}
