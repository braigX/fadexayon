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

class WkSampleCart extends ObjectModel
{
    public $id_sample_cart;
    public $id_cart;
    public $id_order;
    public $id_product;
    public $id_product_attribute;
    public $id_specific_price;
    public $sample;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'wk_sample_cart',
        'primary' => 'id_sample_cart',
        'fields' => [
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'id_order' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'id_specific_price' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'sample' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'shop' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false],
        ],
    ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        Shop::addTableAssociation('wk_sample_cart', ['type' => 'shop', 'primary' => 'id_sample_cart']);
    }

    public function deleteSampleCart($idCart, $idProduct, $idAttr)
    {
        $sampleCarts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT wsa.`id_sample_cart` FROM `' . _DB_PREFIX_ . 'wk_sample_cart` wsa'
            . WkSampleCart::addSqlAssociationCustom('wk_sample_cart', 'wsa') . ' WHERE wsa.`id_cart` = '
            . (int) $idCart . ' AND wsa.`id_product` = ' . (int) $idProduct . ' AND wsa.`id_product_attribute`=' . (int) $idAttr
            . ' GROUP BY wsa.`id_sample_cart`'
        );
        $success = true;
        if (!empty($sampleCarts)) {
            foreach ($sampleCarts as $sampleCart) {
                $objSampleCart = new WkSampleCart((int) $sampleCart['id_sample_cart']);
                $success &= $objSampleCart->delete();
            }
        }

        return $success;
    }

    /**
     * Delete Speciic Price from PrestaShop
     *
     * @param int $idCart
     * @param int $idProduct
     *
     * @return bool
     */
    public function deleteSampleSpecificPrice($idCart, $idProduct, $idAttr)
    {
        $sampleCarts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT wsa.`id_specific_price` FROM `' . _DB_PREFIX_ . 'wk_sample_cart` wsa'
            . WkSampleCart::addSqlAssociationCustom('wk_sample_cart', 'wsa') . ' WHERE wsa.`id_cart` = '
            . (int) $idCart . ' AND wsa.`id_product` = ' . (int) $idProduct . ' AND wsa.`id_product_attribute`=' . (int) $idAttr
            . ' GROUP BY wsa.`id_sample_cart`'
        );
        $success = true;
        if (!empty($sampleCarts)) {
            foreach ($sampleCarts as $sampleCart) {
                $specificPrice = new SpecificPrice((int) $sampleCart['id_specific_price']);
                if (Validate::isLoadedObject($specificPrice)) {
                    $success &= $specificPrice->delete();
                }
            }
        }

        return $success;
    }

    public function getSampleOrderProduct($idOrder, $idProduct, $idAttr)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_sample_cart` wsa'
            . WkSampleCart::addSqlAssociationCustom('wk_sample_cart', 'wsa') . ' WHERE wsa.`id_order` = '
            . (int) $idOrder . ' AND wsa.`id_product` = ' . (int) $idProduct . ' AND wsa.`id_product_attribute`=' . (int) $idAttr
            . ' GROUP BY wsa.`id_sample_cart`'
        );
    }

    public function getSampleCartProduct($idCart, $idProduct, $idAttr = false)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_sample_cart` wsa'
        . WkSampleCart::addSqlAssociationCustom('wk_sample_cart', 'wsa') . ' WHERE wsa.`id_cart` = '
        . (int) $idCart . ' AND wsa.`id_product` = ' . (int) $idProduct;
        if ($idAttr) {
            $sql .= ' AND wsa.`id_product_attribute`=' . (int) $idAttr;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public function getOtherProductInCart($idCart, $idProduct, $idAttr)
    {
        $cart = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_sample_cart` wsa'
            . WkSampleCart::addSqlAssociationCustom('wk_sample_cart', 'wsa') . ' WHERE wsa.`id_cart` = '
            . (int) $idCart . ' AND (wsa.`id_product` != ' . (int) $idProduct . ' OR wsa.`id_product_attribute`!=' . (int) $idAttr
            . ') GROUP BY wsa.`id_sample_cart`'
        );

        if ($cart) {
            return $cart;
        }

        return false;
    }

    public function updateCartOrder($idCart, $idProduct, $idOrder, $idAttr)
    {
        $sampleCarts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT wsa.`id_sample_cart` FROM `' . _DB_PREFIX_ . 'wk_sample_cart` wsa'
            . WkSampleCart::addSqlAssociationCustom('wk_sample_cart', 'wsa') . ' WHERE wsa.`id_cart` = '
            . (int) $idCart . ' AND wsa.`id_product` = ' . (int) $idProduct . ' AND wsa.`id_product_attribute`=' . (int) $idAttr
            . ' GROUP BY wsa.`id_sample_cart`'
        );
        $success = true;
        if (!empty($sampleCarts)) {
            foreach ($sampleCarts as $sampleCart) {
                $sampleCartObj = new WkSampleCart($sampleCart['id_sample_cart']);
                $sampleCartObj->id_order = (int) $idOrder;
                $success &= $sampleCartObj->save();
            }
        }

        return $success;
    }

    /**
     * Get Sample Cart
     *
     * @param int $idCart
     *
     * @return array
     */
    public function getSampleCart($idCart)
    {
        $cart = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_sample_cart` wsa'
            . WkSampleCart::addSqlAssociationCustom('wk_sample_cart', 'wsa') . '
            WHERE wsa.`id_cart` = ' . (int) $idCart . ' GROUP BY wsa.`id_sample_cart`'
        );

        if ($cart) {
            return $cart;
        }

        return false;
    }

    public function checkProductQtyInCart($cart, $idProduct, $updateQty = 0, $idAttr = 0)
    {
        $objSampleProductMap = new WkSampleProductMap();
        if ($sample = $objSampleProductMap->getSampleProduct($idProduct)) {
            $sampleCart = $this->getSampleCart($cart->id);
            if (!empty($sampleCart)) {
                foreach ($sampleCart as $prod) {
                    if (((int) $prod['id_product'] == (int) $idProduct)
                        && ((int) $prod['id_product_attribute'] == (int) $idAttr)
                    ) {
                        $prodQtyCart = $cart->getProductQuantity($idProduct, $idAttr);
                        if (($sample['max_cart_qty'] != 0)
                            && (($prodQtyCart['quantity'] + $updateQty) > $sample['max_cart_qty'])
                        ) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

public function validateSampleCart($ipa, $idProduct)
{
    $context = Context::getContext();
    $module = new WkSampleProduct();
    
    if ($idProduct) {
        $objSampleProductMap = new WkSampleProductMap();
        $sample = $objSampleProductMap->getSampleProduct($idProduct);
        
        if (!$sample) {
            exit(json_encode([
                'status' => 'ko',
                'msg' => $module->l('This product is not the sample product.', 'samplespecificprice'),
            ]));
        }

        // Check if this standard product already in the cart
        $this->checkStandardProductInCart($idProduct, $ipa);

        // Check maximum product in cart restriction
        $this->checkMaximumInCart($idProduct, $ipa);

        // Check maximum quantity in cart restriction
        if ($this->checkProductQtyInCart($context->cart, $idProduct, 0, $ipa)) {
            exit(json_encode([
                'status' => 'ko',
                'msg' => $module->l('Maximum quantity for this product to buy exceeded.', 'samplespecificprice'),
            ]));
        }

        $product = new Product($idProduct);

        // Price type 1 = standard price, no specific price needed
        if ($sample['price_type'] == 1) {
            $this->createSampleCart($idProduct, $ipa, $context->cart->id);
            return;
        }

        // Get tax configuration from shop
        $taxConfig = new TaxConfiguration();
        $shopUsesTaxIncluded = $taxConfig->includeTaxes();
        
        // Get base product price
        $productPriceWithTax = Product::getPriceStatic($idProduct, true, $ipa, 6, null, false, false);
        $productPriceWithoutTax = Product::getPriceStatic($idProduct, false, $ipa, 6, null, false, false);

        // Calculate final sample price
        $finalPrice = 0;
        
        if ($sample['price_type'] == 2) {
            // Fixed amount deduction
            $deductAmount = (float) $sample['amount'];
            
            if ($sample['price_tax'] == 1) {
                // Deduction amount is WITH tax
                if ($shopUsesTaxIncluded) {
                    $finalPrice = $productPriceWithTax - $deductAmount;
                } else {
                    // Shop uses tax excluded, convert deduction to tax excluded
                    $deductAmountNoTax = $this->removeTaxes($deductAmount, $product->id_tax_rules_group);
                    $finalPrice = $productPriceWithoutTax - $deductAmountNoTax;
                }
            } else {
                // Deduction amount is WITHOUT tax
                if ($shopUsesTaxIncluded) {
                    // Shop uses tax included, convert deduction to tax included
                    $deductAmountWithTax = $this->addTaxToAmount($deductAmount, $product->id_tax_rules_group);
                    $finalPrice = $productPriceWithTax - $deductAmountWithTax;
                } else {
                    $finalPrice = $productPriceWithoutTax - $deductAmount;
                }
            }
            
        } elseif ($sample['price_type'] == 3) {
            // Percentage deduction
            $percentAmount = (float) $sample['amount'];
            
            if ($shopUsesTaxIncluded) {
                $deductAmount = ($productPriceWithTax * $percentAmount) / 100;
                $finalPrice = $productPriceWithTax - $deductAmount;
            } else {
                $deductAmount = ($productPriceWithoutTax * $percentAmount) / 100;
                $finalPrice = $productPriceWithoutTax - $deductAmount;
            }
            
        } elseif ($sample['price_type'] == 4) {
            // *** CUSTOM PRICE - YOUR CASE ***
            $customPrice = (float) $sample['price'];
            
            if ($sample['price_tax'] == 1) {
                // Custom price is stored WITH tax (your case)
                if ($shopUsesTaxIncluded) {
                    // Shop displays WITH tax - use price as-is
                    $finalPrice = $customPrice;
                } else {
                    // Shop displays WITHOUT tax - remove tax from custom price
                    $finalPrice = $this->removeTaxes($customPrice, $product->id_tax_rules_group);
                }
            } else {
                // Custom price is stored WITHOUT tax
                if ($shopUsesTaxIncluded) {
                    // Shop displays WITH tax - add tax to custom price
                    $finalPrice = $this->addTaxToAmount($customPrice, $product->id_tax_rules_group);
                } else {
                    // Shop displays WITHOUT tax - use price as-is
                    $finalPrice = $customPrice;
                }
            }
            
        } elseif ($sample['price_type'] == 5) {
            // Free sample
            $finalPrice = 0;
        }

        // Ensure price is not negative
        if ($finalPrice < 0) {
            $finalPrice = 0;
        }

        // Round to 6 decimals to match PrestaShop precision
        $finalPrice = round($finalPrice, 6);

        // Create specific price
        $specificPrice = new SpecificPrice();
        $specificPrice->id_shop = (int) $context->shop->id;
        $specificPrice->id_shop_group = (int) Shop::getContextShopGroupID();
        $specificPrice->id_product = (int) $idProduct;
        $specificPrice->id_cart = (int) $context->cart->id;
        $specificPrice->id_currency = 0; // 0 = all currencies
        $specificPrice->id_country = 0;
        $specificPrice->id_group = 0;
        $specificPrice->reduction_type = 'amount';
        $specificPrice->price = $finalPrice;
        $specificPrice->from_quantity = 1;
        $specificPrice->id_customer = (int) $context->customer->id;
        $specificPrice->reduction = 0;
        $specificPrice->from = '0000-00-00 00:00:00';
        $specificPrice->to = '0000-00-00 00:00:00';
        $specificPrice->id_product_attribute = (int) $ipa;
        
        if ($specificPrice->save()) {
            $this->createSampleCart($idProduct, $ipa, $context->cart->id, $specificPrice->id);
            return;
        } else {
            exit(json_encode([
                'status' => 'ko',
                'msg' => $module->l('Failed to create sample price.', 'samplespecificprice'),
            ]));
        }
    }
}

    public function getTaxIncludedReduction(
        $idTaxRulesGroup,
        $samplePrice,
        $addTax = false
    ) {
        $context = Context::getContext();
        $address = $this->getAddressFromContext($context);
        if ($idTaxRulesGroup) {
            // tax included amount deduction
            $taxRule = new TaxRulesTaxManager($address, $idTaxRulesGroup);
            $taxCalculator = $taxRule->getTaxCalculator();
            $taxRate = $taxCalculator->getTotalRate();
            if ($addTax) {
                $samplePrice = (float) $samplePrice + (((float) $samplePrice * (float) $taxRate) / 100);
            } else {
                $samplePrice = ((float) $samplePrice * 100) / ((float) $taxRate + 100);
            }
        }

        return (float) $samplePrice;
    }

    public function addTaxToAmount($amount, $idTaxRulesGroup, $context = false)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $address = $this->getAddressFromContext($context);
        $taxRule = new TaxRulesTaxManager($address, $idTaxRulesGroup);
        $taxCalculator = $taxRule->getTaxCalculator();

        return $taxCalculator->addTaxes($amount);
    }

    public function removeTaxes($amount, $id_tax_rules_group, $context = false)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $address = $this->getAddressFromContext($context);
        $taxRule = new TaxRulesTaxManager($address, $id_tax_rules_group);
        $taxCalculator = $taxRule->getTaxCalculator();
        $taxRate = $taxCalculator->getTotalRate();

        return ($amount * 100) / (100 + (float) $taxRate);
    }

    public function getAddressFromContext($context)
    {
        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {
            $cartIdAddress = $context->cart->id_address_delivery;
        } else {
            $cartIdAddress = $context->cart->id_address_invoice;
        }
        if ($cartIdAddress) {
            return Address::initialize($cartIdAddress, true);
        } elseif ($context->customer->isLogged()) {
            $addresses = $context->customer->getAddresses($context->language->id);
            if (!empty($addresses)) {
                $id_address = $addresses['0']['id_address'];
            } else {
                $id_address = 0;
            }
        } else {
            $id_address = 0;
        }

        return Address::initialize($id_address, true);
    }

    public function checkMaximumInCart($idProduct, $idAttr)
    {
        $module = Module::getInstanceByName('wksampleproduct');
        $context = Context::getContext();
        $maxInCart = Configuration::get('WK_MAX_SAMPLE_IN_CART');
        $sampleProduct = $this->getOtherProductInCart($context->cart->id, $idProduct, $idAttr);
        if ($maxInCart) { // If not max set, then unlimited, no check needed
            if ($sampleProduct && (count($sampleProduct) >= $maxInCart)) {
                exit(json_encode([
                    'status' => 'ko',
                    'msg' => [
                        sprintf(
                            $module->l('Only %d sample product(s) allowed to buy in single cart', 'samplespecficprice'),
                            $maxInCart
                        ),
                    ],
                ]));
            }
        }
    }

    public function checkStandardProductInCart($idProduct, $idAttr)
    {
        $context = Context::getContext();
        $idCart = $context->cart->id;
        $module = Module::getInstanceByName('wksampleproduct');
        $products = $context->cart->getProducts();
        $stdErrMsg =
        $module->l('This standard product already in cart. Please checkout your cart first.', 'samplespecificprice');
        if ($products && !$this->getSampleCartProduct($idCart, $idProduct, $idAttr)) {
            foreach ($products as $product) {
                if (($product['id_product'] == $idProduct) && ($product['id_product_attribute'] == $idAttr)) {
                    exit(json_encode([
                        'status' => 'ko',
                        'msg' => [$stdErrMsg],
                    ]));
                }
            }
        }
    }

    public function createSampleCart($idProduct, $idAttr, $idCart, $idSpecificPrice = false)
    {
        if (!$this->getSampleCartProduct($idCart, $idProduct, $idAttr)) {
            $sampleCart = new WkSampleCart();
            $sampleCart->id_cart = (int) $idCart;
            $sampleCart->id_product = (int) $idProduct;
            $sampleCart->id_product_attribute = (int) $idAttr;
            if ($idSpecificPrice) {
                $sampleCart->id_specific_price = (int) $idSpecificPrice;
            } else {
                $sampleCart->id_specific_price = (int) 0;
            }
            $sampleCart->sample = (int) 1;
            $sampleCart->save();
        }
    }

    public static function addSqlAssociationCustom(
        $table,
        $alias,
        $identifier = 'id_sample_cart'
    ) {
        $table_alias = $table . '_shop';
        if (strpos($table, '.') !== false) {
            list($table_alias, $table) = explode('.', $table);
        }

        $asso_table = Shop::getAssoTable($table);
        if ($asso_table === false || $asso_table['type'] != 'shop') {
            return;
        }
        $sql = ' INNER JOIN ' . _DB_PREFIX_ . $table . '_shop ' . $table_alias . '
        ON (' . $table_alias . '.' . $identifier . ' = ' . $alias . '.' . $identifier;
        if ((int) Shop::getContextShopID()) {
            $sql .= ' AND ' . $table_alias . '.id_shop = ' . (int) Shop::getContextShopID();
        } elseif (Shop::checkIdShopDefault($table)) {
            $sql .= ' AND ' . $table_alias . '.id_shop = ' . $alias . '.id_shop_default';
        } else {
            $sql .= ' AND ' . $table_alias . '.id_shop IN (' . implode(', ', Shop::getContextListShopID()) . ')';
        }
        $sql .= ')';

        return $sql;
    }
}
