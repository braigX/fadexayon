<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
class Cart extends CartCore
{
    
    
    
    
    
    /*
    * module: minimumcartfee
    * date: 2025-06-22 03:48:29
    * version: 1.0.0
    */
    public function getOrderTotal(
        $withTaxes = true,
        $type = Cart::BOTH,
        $products = null,
        $id_carrier = null,
        $use_cache = false,
        bool $keepOrderPrices = false,
        $fee_payment = false,
        $only_cart = false
    ) {
        $module = Module::getInstanceByName('minimumcartfee');
        try {
            $total = parent::getOrderTotal($withTaxes, $type, $products, $id_carrier, $use_cache, $keepOrderPrices);
            if ($only_cart || $type !== Cart::BOTH) {
                return $total;
            }
            if (!Validate::isLoadedObject($module)) {
                return $total;
            }
            $fee = (float) $module->calculateMinimumCartFee($this);
            if ($fee_payment) {
                return $fee; // Specific case if only asking for the fee
            }
            if ($fee > 0) {
                $finalTotal = $total + $fee;
                return $finalTotal;
            }
            return $total;
        } catch (Exception $e) {
            return parent::getOrderTotal($withTaxes, $type, $products, $id_carrier, $use_cache, $keepOrderPrices);
        }
    }
    /*
    * module: idxrcustomproduct
    * date: 2026-01-29 11:34:18
    * version: 1.7.5
    */
    public function duplicate()
    {
        $new_cart = parent::duplicate();
        if ((bool) Module::isEnabled('idxrcustomproduct')) {
            $module = Module::getInstanceByName('idxrcustomproduct');
            $module->duplicateCartInfo($this->id, $new_cart->id);
        }
        return $new_cart;
    }
    /*
    * module: wksampleproduct
    * date: 2026-01-29 11:42:05
    * version: 5.3.3
    */
    public function checkQuantities($returnProductOnFailure = false)
    {
        if (Module::isEnabled('wksampleproduct')) {
            if (Configuration::isCatalogMode() && !defined('_PS_ADMIN_DIR_')) {
                return false;
            }
            $delivery = $this->getDeliveryOption();
            foreach ($this->getProducts() as $product) {
                if (!$this->allow_seperated_package
                    && !$product['allow_oosp']
                    && StockAvailable::dependsOnStock($product['id_product'])
                    && $product['advanced_stock_management']
                    && (bool) Context::getContext()->customer->isLogged()
                    && !empty($delivery)
                ) {
                    $product['stock_quantity'] = StockManager::getStockByCarrier(
                        (int) $product['id_product'],
                        (int) $product['id_product_attribute'],
                        $delivery
                    );
                }
                if (!$product['active']
                    || !$product['available_for_order']
                    || (!$product['allow_oosp'] && $product['stock_quantity'] < $product['cart_quantity'])
                ) {
                    return $returnProductOnFailure ? $product : false;
                }
                if (!$product['allow_oosp']) {
                    $productQuantity = Product::getQuantity(
                        $product['id_product'],
                        $product['id_product_attribute'],
                        null,
                        $this,
                        $product['id_customization']
                    );
                    if ($productQuantity < 0) {
                        return $returnProductOnFailure ? $product : false;
                    }
                }
                require_once _PS_MODULE_DIR_ . 'wksampleproduct/classes/WkSampleProductMap.php';
                require_once _PS_MODULE_DIR_ . 'wksampleproduct/classes/WkSampleCart.php';
                $objSampleCart = new WkSampleCart();
                $sampleCart = $objSampleCart->getSampleCartProduct(
                    $this->id,
                    $product['id_product'],
                    $product['id_product_attribute']
                );
                if ($sampleCart) {
                    $objSampleProductMap = new WkSampleProductMap();
                    $sample = $objSampleProductMap->getSampleProduct($product['id_product']);
                    if ($sample && $sample['active']) {
                        if (($sample['max_cart_qty'] > 0) && ($product['cart_quantity'] > $sample['max_cart_qty'])) {
                            return $returnProductOnFailure ? $product : false;
                        }
                    } else {
                        return $returnProductOnFailure ? $product : false;
                    }
                }
            }
            return true;
        } else {
            return parent::checkQuantities($returnProductOnFailure);
        }
    }
    /*
    * module: wksampleproduct
    * date: 2026-01-29 11:42:05
    * version: 5.3.3
    */
    public function getProducts(
        $refresh = false,
        $id_product = false,
        $id_country = null,
        $fullInfos = true,
        bool $keepOrderPrices = false
    ) {
        $cartProducts = parent::getProducts($refresh, $id_product, $id_country, $fullInfos, $keepOrderPrices);
        if (Module::isEnabled('wksampleproduct') && !empty($cartProducts)) {
            $objModule = Module::getInstanceByName('wksampleproduct');
            $sampleInfos = $objModule->getSampleCartInformations(
                $this->id,
                array_unique(array_column($cartProducts, 'id_product'))
            );
            if (!empty($sampleInfos['samples'])) {
                foreach ($cartProducts as $key => $prod) {
                    if (in_array(
                        $prod['id_product'] . '_' . (int) $prod['id_product_attribute'],
                        $sampleInfos['samples']
                    )) {
                        $cartProducts[$key]['minimal_quantity'] = 1;
                        $sampleWeight = $this->getProductWeightFromSampleInfo($sampleInfos, $prod);
                        if ($sampleWeight > 0) {
                            $cartProducts[$key]['weight'] = (float) $sampleWeight;
                            if (isset($prod['weight_attribute']) && $prod['weight_attribute'] > 0) {
                                $cartProducts[$key]['weight_attribute'] = $sampleWeight;
                            }
                        }
                    }
                }
            }
        }
        return $cartProducts;
    }
    /*
    * module: wksampleproduct
    * date: 2026-01-29 11:42:05
    * version: 5.3.3
    */
    public function getTotalWeight($products = null)
    {
        if (Module::isEnabled('wksampleproduct')) {
            if (null !== $products) {
                $total_weight = 0;
                if (!empty($products)) {
                    $objModule = Module::getInstanceByName('wksampleproduct');
                    $sampleInfos = $objModule->getSampleCartInformations(
                        $this->id,
                        array_unique(array_column($products, 'id_product'))
                    );
                    foreach ($products as $product) {
                        $sampleWeight = 0;
                        if (in_array(
                            $product['id_product'] . '_' . (int) $product['id_product_attribute'],
                            $sampleInfos['samples']
                        )) {
                            if (isset($sampleInfos['weights']['prod_' . $product['id_product']])
                                && ($sampleInfos['weights']['prod_' . $product['id_product']] > 0)
                            ) {
                                $sampleWeight = (float) $sampleInfos['weights']['prod_' . $product['id_product']];
                            } elseif ($sampleInfos['weights']['global'] > 0) {
                                $sampleWeight = (float) $sampleInfos['weights']['global'];
                            }
                        }
                        if ($sampleWeight > 0) {
                            $total_weight += $sampleWeight * $product['cart_quantity'];
                        } elseif (!isset($product['weight_attribute']) || null === $product['weight_attribute']) {
                            $total_weight += $product['weight'] * $product['cart_quantity'];
                        } else {
                            $total_weight += $product['weight_attribute'] * $product['cart_quantity'];
                        }
                    }
                }
                return $total_weight;
            }
            if (!isset(self::$_totalWeight[$this->id])) {
                $this->updateProductWeight($this->id);
            }
            return self::$_totalWeight[(int) $this->id];
        }
        return parent::getTotalWeight($products);
    }
    /*
    * module: wksampleproduct
    * date: 2026-01-29 11:42:05
    * version: 5.3.3
    */
    protected function updateProductWeight($productId)
    {
        if (Module::isEnabled('wksampleproduct')) {
            $productId = (int) $productId;
            $objModule = Module::getInstanceByName('wksampleproduct');
            $sampleInfos = $objModule->getSampleCartInformations($productId);
            if (Combination::isFeatureActive()) {
                $attrProducts = Db::getInstance()->executeS('
                    SELECT cp.`id_product`, cp.`id_product_attribute`, (p.`weight` + pa.`weight`) as paweight, cp.`quantity`
                    FROM `' . _DB_PREFIX_ . 'cart_product` cp
                    LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (cp.`id_product` = p.`id_product`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
                    ON (cp.`id_product_attribute` = pa.`id_product_attribute`)
                    WHERE (cp.`id_product_attribute` IS NOT NULL AND cp.`id_product_attribute` != 0)
                    AND cp.`id_cart` = ' . $productId);
                $weight_product_with_attribute = 0;
                foreach ($attrProducts as $attrProduct) {
                    $sampleWeight = $this->getProductWeightFromSampleInfo($sampleInfos, $attrProduct);
                    if ($sampleWeight > 0) {
                        $weight_product_with_attribute += (float) $sampleWeight * (int) $attrProduct['quantity'];
                    } else {
                        $weight_product_with_attribute += (float) $attrProduct['paweight'] * (int) $attrProduct['quantity'];
                    }
                }
            } else {
                $weight_product_with_attribute = 0;
            }
            $weight_product_without_attribute = 0;
            $mainProducts = Db::getInstance()->executeS('
                SELECT cp.`id_product`, cp.`id_product_attribute`, p.`weight`, cp.`quantity`
                FROM `' . _DB_PREFIX_ . 'cart_product` cp
                LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (cp.`id_product` = p.`id_product`)
                WHERE (cp.`id_product_attribute` IS NULL OR cp.`id_product_attribute` = 0)
                AND cp.`id_cart` = ' . $productId);
            foreach ($mainProducts as $mainProduct) {
                $sampleWeight = $this->getProductWeightFromSampleInfo($sampleInfos, $mainProduct);
                if ($sampleWeight > 0) {
                    $weight_product_without_attribute += (float) $sampleWeight * (int) $mainProduct['quantity'];
                } else {
                    $weight_product_without_attribute += (float) $mainProduct['weight'] * (int) $mainProduct['quantity'];
                }
            }
            $weight_cart_customizations = Db::getInstance()->getValue('
                SELECT SUM(cd.`weight` * c.`quantity`) FROM `' . _DB_PREFIX_ . 'customization` c
                LEFT JOIN `' . _DB_PREFIX_ . 'customized_data` cd ON (c.`id_customization` = cd.`id_customization`)
                WHERE c.`in_cart` = 1 AND c.`id_cart` = ' . $productId);
            self::$_totalWeight[$productId] = round(
                (float) $weight_product_with_attribute +
                (float) $weight_product_without_attribute +
                (float) $weight_cart_customizations,
                6
            );
        } else {
            return parent::updateProductWeight($productId);
        }
    }
    /*
    * module: wksampleproduct
    * date: 2026-01-29 11:42:05
    * version: 5.3.3
    */
    private function getProductWeightFromSampleInfo($sampleInfos, $product)
    {
        $sampleWeight = 0;
        if (in_array(
            $product['id_product'] . '_' . (int) $product['id_product_attribute'],
            $sampleInfos['samples']
        )) {
            if (isset($sampleInfos['weights']['prod_' . $product['id_product']])
                && ($sampleInfos['weights']['prod_' . $product['id_product']] > 0)
            ) {
                $sampleWeight = (float) $sampleInfos['weights']['prod_' . $product['id_product']];
            } elseif ($sampleInfos['weights']['global'] > 0) {
                $sampleWeight = (float) $sampleInfos['weights']['global'];
            }
        }
        return $sampleWeight;
    }
}