<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innovadeluxe SL
 * @copyright 2017 Innovadeluxe SL

 * @license   INNOVADELUXE
 */

class IdxCustomizedProduct
{

    /** @var int $id_product id of the new created product */
    public $id_product;

    /** @var string $icp_code code of the customization */
    public $icp_code;

    /** @var int $id_product id of the parent product */
    public $id_product_parent;

    /** @var Context $context prestashop context */
    public function __construct($id_product = 0)
    {
        $this->context = Context::getContext();
        $this->id_product = (int) $id_product;
        $this->icp_code = $this->getIcpCode();
        if ($this->icp_code) {
            $this->id_product_parent = self::getParentProduct($this->id_product);
        } else {
            $this->id_product_parent = false;
        }
    }

    public function getIcpCode()
    {
        $description_q = 'Select description from ' . _DB_PREFIX_ . 'product_lang where id_product = ' . (int) $this->id_product;
        $description = Db::getInstance()->getValue($description_q);
        $start = strpos($description, 'icp_code');
        if ($start) {
            $start += 9; // jump icp_code(
            $end = strpos($description, ')', $start);
            $icp_code = substr($description, $start, $end - $start);
            return $icp_code;
        } else {
            return false;
        }
    }

    public function setIcpCode($icp_code)
    {
        $this->icp_code = $icp_code;
    }

    public function isCustomized()
    {
        return (bool) $this->icp_code;
    }

    public static function getParentProduct($id_product)
    {
        return Db::getInstance()->getValue('Select id_producto from ' . _DB_PREFIX_ . 'idxrcustomproduct_clones where id_clon = ' . (int) $id_product);
    }

    public function getConfiguration()
    {
        if ($this->id_product_parent) {
            $module = Module::getInstanceByName('idxrcustomproduct');
            $conf_id = $module->getConfigurationByProduct($this->id_product_parent);
            if ($conf_id) {
                return new IdxConfiguration($conf_id, true);
            }
        }
        return false;
    }
    
/*Modifi with team wassim novatis*/
    public function createInPs($product_id, $snaps, $attribute_id, $customization, $extra = false, $quantity = false, $product_weight = 0, $product_volume = 0, $product_width = 0 , $product_height = 0 , $product_depth = 0, $prix_de_decouper=0, $price_from_cube=0 )
    {
        $languages = Language::getLanguages(false);
        $add_price = 0;
        $add_discount = 0;
        $add_weight = 0;
        $add_ref = '';
        $add_desc = '';
        $add_shortdesc = array();
        $icp_code = '';
        $icp_sep = '';
        $tax_change = false;
        foreach ($languages as $lang) {
            $add_shortdesc[$lang['id_lang']] = '';
        }
        $module = Module::getInstanceByName('idxrcustomproduct');
        $id_configuration = $module->getConfigurationByProduct($product_id);
        $configuration = new IdxConfiguration($id_configuration, true);
        $this->checkConstraints($customization, $id_configuration);

        $id_product_old = (int) $product_id;
        $product = new Product($id_product_old);

        $product_source = new Product($id_product_old);
        
        //If customer is without tax the calculation tax rate must be 1
        $without_taxes = false;
        if (isset($this->context->customer)) {
            if (method_exists($this->context->cart, 'getTaxAddressId')) {
                $addressId = $this->context->cart->getTaxAddressId();
            } else {
                $addressId = $this->context->cart->id_address_delivery;
            }

            if ($addressId) {
                $customer_product_tax = Tax::getProductTaxRate($product_id, $addressId);
                if ($customer_product_tax == 0) {
                    $without_taxes = true;
                }
            }
        }
        
        if ($without_taxes) {
            $tax = 1;
        } else {
            $product_tax_rule_group_id = Product::getIdTaxRulesGroupByIdProduct($id_product_old);
            $default_rate = TaxRulesGroup::getAssociatedTaxRatesByIdCountry(Configuration::get('PS_COUNTRY_DEFAULT'));
            $tax_rate = $default_rate[$product_tax_rule_group_id];
            $tax = (($tax_rate) / 100) + 1;
        }
        
        $product_max_stock = 10000;
        if ($configuration->add_base) {
            $product_max_stock = Product::getQuantity((int) $id_product_old);
        }
        $out_of_stock = StockAvailable::outOfStock((int) $id_product_old, Context::getContext()->shop->id);
        if ($configuration->productbase_component && !$attribute_id) {
            $product_comp_id = IdxComponent::getComponentIdByProduct($product_id);
            $base_product_components = IdxComponent::getChildrenComponent($product_comp_id, $this->context->language->id);
            foreach ($base_product_components as $base_product_component) {
                foreach ($customization as $selected_comp) {
                    if ($selected_comp['id_component'] == $base_product_component['id_component']) {
                        $option_selected = $selected_comp['id_option'];
                        $options = json_decode($base_product_component['json_values']);
                        foreach ($options->options as $option) {
                            if ($option->id !== $option_selected) {
                                continue;
                            }
                            if (isset($option->att_product) && $option->att_product != "none") {
                                $product_ids = explode('_', $option->att_product);
                                $attribute_id = $product_ids[1];
                            }
                        }
                    }
                }
            }
        }
        $impact_options = json_decode($configuration->impact_options);
        foreach ($customization as &$option) {
            if (strpos($option['id_component'], 'f') > 0 || $this->isUniqueProductComp($option['id_component'])) {
                $id_data = explode('f', $option['id_component']);
                $option['id_component'] = $id_data[0];
                $option['att_product'] = IdxComponent::getProductByCustomization($id_data[0], $customization);
                $option['from_product'] = true;
            }
            $icp_code .= $icp_sep . (isset($option['qty']) ? $option['qty'] . 'x' : '') . $option['id_component'] . '-' . $option['id_option'];
            $icp_sep = ',';
            $options = $module->getComponentOptions($option['id_component']);
            if (
                is_array($options)
                && isset($options['type'], $options['lang'][$this->context->language->id])
                && $options['type'] != 'textarea'
                && $options['type'] != 'text'
                && $options['type'] != 'file'
                && isset($options['lang'][$this->context->language->id]->options)
                && is_iterable($options['lang'][$this->context->language->id]->options)
            ) {
                foreach ($options['lang'][$this->context->language->id]->options as $item) {
                    if ($item->id == $option['id_option']) {
                        if (isset($item->tax_change) && $item->tax_change) {
                            $tax_change = $item->tax_change;
                        }
                        if (isset($option['from_product'])) {
                            $item->att_product = $option['att_product'];
                            $module->generateProductImpact($item, false, $tax, $option['id_component'], true);
                        } else {
                            $module->generateImpact($item, false, $tax, $option['id_component'], $product_id, $attribute_id, true);
                        }
                        $this->setAdditionalImpact($option['id_component'], $item, $impact_options, $customization);
                        if (isset($item->out_of_stock)) {
                            $out_of_stock = $item->out_of_stock;
                        }
                        if ($item->max_qty < $product_max_stock) {
                            $product_max_stock = $item->max_qty;
                        }
                        
                        $add_price += $item->price_impact * (isset($option['qty']) ? $option['qty'] : 1);
                        if (isset($item->price_impact_wodiscount) && $item->price_impact_wodiscount && $item->price_impact_wodiscount > $item->price_impact) {
                            $add_discount += ($item->price_impact_wodiscount - $item->price_impact)*(isset($option['qty']) ? $option['qty'] : 1); 
                        }
                        $add_weight += $item->weight_impact * (isset($option['qty']) ? $option['qty'] : 1);
                        $add_ref .= $item->reference;
                        $add_desc .= '<p>' . $module->getComponentName($option['id_component']) . ': ' . (isset($option['qty']) ? $option['qty'] . 'x' : '') . $item->name
                                . (($item->reference != '' && $item->reference != $item->name) ? ' - ' . $item->reference . '</p>' : '</p>');
                        $limit = (int) Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
                        foreach ($languages as $lang) {
                            $add_shortdesc[$lang['id_lang']] .= '<p>' . $module->getComponentName($option['id_component'], true, $lang['id_lang']) . ': ' . (isset($option['qty']) ? $option['qty'] . 'x' : '') . $item->name . '</p>';
                            if ($limit && Tools::strlen($add_shortdesc[$lang['id_lang']]) > $limit) {
                                $add_shortdesc[$lang['id_lang']] = Tools::substr($add_shortdesc[$lang['id_lang']], 0, ($limit - 5)) . '</p>';
                            }
                        }
                        $option['option'] = $item;
                        break;
                    }
                }
            }
        }
        $add_price_wt = $add_price / $tax;
        $prix_de_decoupe = $prix_de_decouper/$tax;
        $base_price = 0;
        if ($configuration->add_base) {
            $base_price = $product_source->price;
        }
        /*Add with team wassim novatis*/
        // Calculer la surface en utilisant la volume et Epaisseur
        $price_from_cube = (float) $price_from_cube;

        if ($price_from_cube > 0) {
            $actual_price = Tools::ps_round($price_from_cube, 2);
        }else if (is_numeric($price_from_cube) && (float) $price_from_cube > 0) {
            $actual_price = Tools::ps_round((float) $price_from_cube, 2);
        }else if ($product_depth > 0 && $product_volume > 0) {
            $depth_in_meters = $product_depth / 1000;
            $surface = $product_volume / $depth_in_meters;
            $actual_price = Tools::ps_round(($surface * $base_price) + $prix_de_decoupe + $add_price_wt, 2);
        } else {
            $actual_price = Tools::ps_round((0 * $base_price) + $prix_de_decoupe + $add_price_wt, 2);
        }
        //$actual_price = Tools::ps_round($base_price + $add_price_wt, 6);
         /* End */

        $add_desc .= '<p>icp_code(' . $icp_code . ')</p>';
        $exist = $module->productByIcpcode($product_id, $icp_code);
        $extra_ids = [];

foreach ((array)$extra as $opt_extra) {
    // Ensure cart exists
    if (!$this->context->cart->id) {
        if (Context::getContext()->cookie->id_guest) {
            $guest = new Guest(Context::getContext()->cookie->id_guest);
            $this->context->cart->mobile_theme = $guest->mobile_theme;
        }
        $this->context->cart->add();
        if ($this->context->cart->id) {
            $this->context->cookie->id_cart = (int)$this->context->cart->id;
        }
    }

    // --- robust parsing ---
    $parts = explode('_', (string)$opt_extra, 2);  // limit=2
    if (count($parts) !== 2) {
        continue;
    }
    list($compId, $rawVal) = $parts;

    // Decode HTML entities then trim
    $rawVal = html_entity_decode($rawVal, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $rawVal = trim($rawVal);

    // If it’s a JSON literal or a quoted JSON string, decode; otherwise keep as-is
    // e.g. "\"1200\"" => "1200", "false" => "false", "0" => "0"
    $decoded = json_decode($rawVal, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $val = $decoded;
    } else {
        // Strip wrapping quotes if they slipped through
        if ((strlen($rawVal) >= 2) &&
            (($rawVal[0] === '"' && substr($rawVal, -1) === '"') ||
             ($rawVal[0] === "'" && substr($rawVal, -1) === "'"))) {
            $val = substr($rawVal, 1, -1);
        } else {
            $val = $rawVal;
        }
    }

    // Skip explicit false/empty flags (treat boolean false and string "false" the same)
    if ($val === false || (is_string($val) && strcasecmp($val, 'false') === 0) || $val === '' || $val === null) {
        continue;
    }

    // Normalize numeric strings (comma to dot)
    if (is_string($val) && preg_match('/^\d+(?:[.,]\d+)?$/', $val)) {
        $val = str_replace(',', '.', $val);
    }

    $data = [
        'id_component' => (int)$compId,
        'id_option'    => 0,
        'id_product'   => (int)$exist,
        'extra'        => pSQL((string)$val),
        'id_cart'      => (int)$this->context->cart->id,
    ];

    // Attach to existing customized product if found
    $id_extra = Db::getInstance()->getValue(
        'SELECT id_extra FROM ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_extra
         WHERE id_component = ' . (int)$compId . '
           AND id_cart = ' . (int)$this->context->cart->id . '
           AND id_product = ' . (int)$exist
    );

    if ($id_extra) {
        $extra_ids[] = (int)$id_extra;
        $current_extra = Db::getInstance()->getValue(
            'SELECT extra FROM ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_extra
             WHERE id_extra = ' . (int)$id_extra
        );
        if ($current_extra) {
            $data['extra'] = $current_extra . '§ ' . $data['extra'];
        }
        Db::getInstance()->update('idxrcustomproduct_customer_extra', $data, 'id_extra = ' . (int)$id_extra);
    } else {
        Db::getInstance()->insert('idxrcustomproduct_customer_extra', $data);
        $extra_ids[] = (int)Db::getInstance()->Insert_ID();
    }
}


        // $extra_ids = array();
        // foreach ($extra as $opt_extra) {
        //     if (!$this->context->cart->id) {
        //         if (Context::getContext()->cookie->id_guest) {
        //             $guest = new Guest(Context::getContext()->cookie->id_guest);
        //             $this->context->cart->mobile_theme = $guest->mobile_theme;
        //         }
        //         $this->context->cart->add();
        //         if ($this->context->cart->id) {
        //             $this->context->cookie->id_cart = (int) $this->context->cart->id;
        //         }
        //     }
        //     $extra = explode('_', $opt_extra);
        //     if (isset($extra[1])) {
        //         $extra[1] = json_decode($extra[1]);
        //     }
        //     if (!isset($extra[1]) || $extra[1] == "false") {
        //         continue;
        //     } else {
        //         $extra_text = $extra[1];
        //         // if ($quantity > 1) {
        //         //     $extra_text = $quantity.'x '.$extra[1];
        //         // }
        //         $data = array(
        //             'id_component' => (int) $extra[0],
        //             'extra' => pSQL($extra_text),
        //             'id_cart' => (int) $this->context->cart->id,
        //         );

        //         $exist_q = 'Select id_extra from ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_extra where id_component = ' . (int) $extra[0] . ' and id_cart = ' . (int) $this->context->cart->id . ' and id_product = ' . (int) $exist;
        //         $id_extra = Db::getInstance()->getValue($exist_q);
        //         if ($id_extra) {
        //             $extra_ids[] = $id_extra;
        //             $current_extra = Db::getInstance()->getValue('Select extra from ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_extra where id_extra = ' . (int) $id_extra);
        //             if ($current_extra) {
        //                 $data['extra'] = $current_extra . '§ ' . $data['extra'];
        //             }
        //             Db::getInstance()->update('idxrcustomproduct_customer_extra', $data, 'id_extra = ' . (int) $id_extra);
        //         } else {
        //             Db::getInstance()->insert('idxrcustomproduct_customer_extra', $data);
        //             $extra_ids[] = Db::getInstance()->Insert_ID();
        //         }
        //     }
        // }
        $reference = $product->reference;
        if ($add_ref != '') {
            $reference .= '_' . $add_ref;
        } else {
            $reference .= '_CUSTOM';
        }

        $limit = (int) Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
        if ($limit <= 0) {
            $limit = 800;
        }
        foreach ($languages as $lang) {
            $product->description[$lang['id_lang']] = $add_desc;
            $product->description_short[$lang['id_lang']] = (Tools::strlen($add_shortdesc[$lang['id_lang']]) >= $limit) ? Tools::substr($add_shortdesc[$lang['id_lang']], 0, $limit) : $add_shortdesc[$lang['id_lang']];
        }

        if ($exist) {
            echo $exist;
            flush();
            $customized_product = new Product($exist);
            $customized_product->reference = $reference;
            $customized_product->description = $product->description;
            $customized_product->description_short = $product->description_short;
            if ($extra_ids) {
                $extra_sql = 'Update ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_extra set id_product = ' . (int) $exist . ' where id_extra in (' . pSQL(implode(',', $extra_ids)) . ')';
                Db::getInstance()->execute($extra_sql);
            }
            $this->checkIntegrity($exist);
            $actual_stock = Product::getQuantity((int) $exist);
            $qty_diff = $product_max_stock - $actual_stock;
            $module->setQty($qty_diff, $exist);
            $old_price = Product::getPriceStatic($exist, false);
            if ($customized_product->out_of_stock != $out_of_stock) {
                StockAvailable::setProductOutOfStock($exist, $out_of_stock);
            }
            if ($add_weight > 0) {
                $old_weight = Db::getInstance()->getValue('select weight from ' . _DB_PREFIX_ . 'product where id_product = ' . (int) $product_id);
                $customized_product->weight = ($old_weight + $add_weight);
            }
            if ($old_price !== $actual_price) {
                $customized_product->price = $actual_price;
            }
            $customized_product->update();
            $categories = array();
            $categories[0] = (int) Configuration::get(Tools::strtoupper($module->name . '_CATEGORY'));
            if (Configuration::get('IDXRCUSTOMPRODUCT_CLONECAT')) {
                $categories[1] = (int) $module->getCustomCategory($id_product_old);
            }
            $customized_product->updateCategories($categories);
            $this->cloneShippingConf($id_product_old, $exist);
            $this->cloneSpecifPrices($id_product_old, $exist);
            if ($add_discount) {
                $amount_wot = $add_discount/$tax;
                $this->addDiscount($exist, $amount_wot, $add_discount);
            }
            return $exist;
        }
        //Select auto_increment value block the value at create
        $new_id = Db::getInstance()->getValue('select id_product+1 from '._DB_PREFIX_.'product order by id_product desc');
        echo $new_id;
        flush();
        $product->price = $actual_price;
        unset($product->id);
        unset($product->id_product);
        $product->weight += $add_weight;
        $product->indexed = 0;
        $product->active = 1;
        $product->out_of_stock = $out_of_stock;
        $product->id_category_default = (int) Configuration::get(Tools::strtoupper($module->name . '_CATEGORY'));
        $max_reference = (class_exists('Reference') && Reference::MAX_LENGTH) ? Reference::MAX_LENGTH : 32;
        $product->reference = Tools::substr($reference, 0, $max_reference);

        $product->depends_on_stock = false;
        $product->visibility = 'none';

        if ($tax_change) {
            $id_tax_rules_group = Db::getInstance()->getValue('select id_tax_rules_group from ' . _DB_PREFIX_ . 'tax_rule where id_tax = ' . (int) $tax_change . ' and id_country = ' . (int) Configuration::get('PS_COUNTRY_DEFAULT'));
            $product->id_tax_rules_group = $id_tax_rules_group;
        }

        $attribute_ref = '';
        if ((int) $attribute_id > 0) {
            $attribute_ref = $this->getAttributeReference($id_product_old, (int) $attribute_id);
        }

        // Set the default category for the product
        // $products_with_combination_naming = [16648, 179594];
        $products_with_combination_naming = json_decode(Configuration::get('idxr_skipped_product_ids'), true);
        if (!is_array($products_with_combination_naming)) {
            $products_with_combination_naming = [];
        }
        if (in_array((int)$product_id, $products_with_combination_naming) && $attribute_id) {
            $attribute_texts = [];

            $attribute_infos = Db::getInstance()->executeS('
                SELECT agl.name AS group_name, al.name AS attribute_name 
                FROM '._DB_PREFIX_.'product_attribute_combination pac
                INNER JOIN '._DB_PREFIX_.'attribute a ON a.id_attribute = pac.id_attribute
                INNER JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute = a.id_attribute AND al.id_lang = '.(int)$lang['id_lang'].')
                INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = '.(int)$lang['id_lang'].')
                WHERE pac.id_product_attribute = '.(int)$attribute_id
            );

            foreach ($attribute_infos as $attr) {
                $attribute_texts[] = $attr['group_name'].' : '.$attr['attribute_name'];
            }

            if (!empty($attribute_texts)) {
                $product->name[$lang['id_lang']] .= ' (' . implode(' - ', $attribute_texts) . ')' . ' - ' .  $new_id;
            }
        }else{
            if (Configuration::get('IDXRCUSTOMPRODUCT_ADDIDNAME')) {
                foreach ($languages as $lang) {
                    $product->name[$lang['id_lang']] .= ' - ' .  $new_id;
                    $product->link_rewrite[$lang['id_lang']] .= $new_id;
                }
            }
        }
        if ($attribute_ref) {
            foreach ($languages as $lang) {
                if (strpos($product->name[$lang['id_lang']], '(' . $attribute_ref . ')') === false) {
                    $product->name[$lang['id_lang']] .= ' (' . $attribute_ref . ')';
                }
            }
        }
        /*Add with team wassim novatis*/
        if ($product_weight > 0) {
            $product->weight = $product_weight;
        }
        if ($product_volume > 0) {
            $product->volume = $product_volume;
        }
        if ($product_width > 0) {
            $product->width = $product_width;
        }
        if ($product_depth > 0) {
            $product->depth = $product_depth;
        }
        if ($product_height > 0) {
            $product->height = $product_height;
        }
        /*End*/

        $product->add();

        $newProductId = $product->id;

        if ($snaps != 0 && $newProductId) {
            $this->updateSnapsWithProductId($snaps, $newProductId);
        }
        
        $module->checkMpcartordersplit($id_product_old, $product->id);
        $product->setCarriers($product_source->getCarriers());
        $this->setProductoOriginal($id_product_old, $product->id, $icp_code);
        $this->cloneShippingConf($id_product_old, $product);
        $this->cloneSpecifPrices($id_product_old, $product->id);
        $categories = array();
        $categories[0] = (int) Configuration::get(Tools::strtoupper($module->name . '_CATEGORY'));
        if (Configuration::get('IDXRCUSTOMPRODUCT_CLONECAT')) {
            $categories[1] = (int) $module->getCustomCategory($id_product_old);
        }
        $product->updateCategories($categories);
        if ($extra_ids) {
            $extra_sql = 'Update ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_extra set id_product = ' . (int) $product->id . ' where id_extra in (' . pSQL(implode(',', $extra_ids)) . ')';
            Db::getInstance()->execute($extra_sql);
        }
        $module->setQty($product_max_stock, $product->id);
        Image::duplicateProductImages($id_product_old, $product->id, 0);
        if ((int) $attribute_id > 0) {
            $this->cloneSelectedAttribute($id_product_old, (int) $attribute_id, (int) $product->id);
        }
        
        return $product->id;
    }
/**
 * Update the snaps table with the new product ID
 */
private function updateSnapsWithProductId($snapsId, $productId) {
    $sql = 'UPDATE ' . _DB_PREFIX_ . 'idxrcustomproduct_snaps SET id_product = ' . (int)$productId . ' WHERE id_snap = ' . (int)$snapsId;
    if (!Db::getInstance()->execute($sql)) {
        // Optionally handle the error, such as logging it or sending a notification
        error_log('Failed to update snaps table: ' . Db::getInstance()->getMsgError());
    }
}

    private function getAttributeReference($productId, $attributeId)
    {
        return (string) Db::getInstance()->getValue(
            'SELECT reference FROM ' . _DB_PREFIX_ . 'product_attribute
             WHERE id_product = ' . (int) $productId . '
               AND id_product_attribute = ' . (int) $attributeId
        );
    }

    private function cloneSelectedAttribute($fromProductId, $fromAttributeId, $toProductId)
    {
        $source_attr = Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute
             WHERE id_product_attribute = ' . (int) $fromAttributeId . '
               AND id_product = ' . (int) $fromProductId
        );
        if (!$source_attr) {
            return 0;
        }

        unset($source_attr['id_product_attribute']);
        $source_attr['id_product'] = (int) $toProductId;
        $source_attr['default_on'] = 1;
        Db::getInstance()->insert('product_attribute', $source_attr);
        $new_attr_id = (int) Db::getInstance()->Insert_ID();
        if (!$new_attr_id) {
            return 0;
        }

        $shop_rows = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute_shop
             WHERE id_product_attribute = ' . (int) $fromAttributeId
        );
        foreach ($shop_rows as $row) {
            unset($row['id_product_attribute']);
            $row['id_product_attribute'] = (int) $new_attr_id;
            $row['id_product'] = (int) $toProductId;
            $row['default_on'] = 1;
            Db::getInstance()->insert('product_attribute_shop', $row);
        }

        $comb_rows = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute_combination
             WHERE id_product_attribute = ' . (int) $fromAttributeId
        );
        foreach ($comb_rows as $row) {
            $row['id_product_attribute'] = (int) $new_attr_id;
            Db::getInstance()->insert('product_attribute_combination', $row);
        }

        $image_map = $this->getImageIdMapByPosition($fromProductId, $toProductId);
        if ($image_map) {
            $img_rows = Db::getInstance()->executeS(
                'SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute_image
                 WHERE id_product_attribute = ' . (int) $fromAttributeId
            );
            foreach ($img_rows as $row) {
                if (!isset($image_map[$row['id_image']])) {
                    continue;
                }
                $row['id_product_attribute'] = (int) $new_attr_id;
                $row['id_image'] = (int) $image_map[$row['id_image']];
                Db::getInstance()->insert('product_attribute_image', $row);
            }
        }

        $stock_rows = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'stock_available
             WHERE id_product_attribute = ' . (int) $fromAttributeId . '
               AND id_product = ' . (int) $fromProductId
        );
        foreach ($stock_rows as $row) {
            unset($row['id_stock_available']);
            $row['id_product'] = (int) $toProductId;
            $row['id_product_attribute'] = (int) $new_attr_id;
            Db::getInstance()->insert('stock_available', $row);
        }

        if (method_exists('Product', 'setDefaultAttribute')) {
            Product::setDefaultAttribute((int) $toProductId, (int) $new_attr_id);
        } else {
            Db::getInstance()->update('product', array('cache_default_attribute' => (int) $new_attr_id), 'id_product = ' . (int) $toProductId);
            Db::getInstance()->update('product_shop', array('cache_default_attribute' => (int) $new_attr_id), 'id_product = ' . (int) $toProductId);
        }

        return $new_attr_id;
    }

    private function getImageIdMapByPosition($fromProductId, $toProductId)
    {
        $from_images = Db::getInstance()->executeS(
            'SELECT id_image, position FROM ' . _DB_PREFIX_ . 'image
             WHERE id_product = ' . (int) $fromProductId . '
             ORDER BY position ASC'
        );
        $to_images = Db::getInstance()->executeS(
            'SELECT id_image, position FROM ' . _DB_PREFIX_ . 'image
             WHERE id_product = ' . (int) $toProductId . '
             ORDER BY position ASC'
        );
        if (!$from_images || !$to_images) {
            return array();
        }

        $to_by_pos = array();
        foreach ($to_images as $img) {
            $to_by_pos[(int) $img['position']] = (int) $img['id_image'];
        }

        $map = array();
        foreach ($from_images as $img) {
            $pos = (int) $img['position'];
            if (!isset($to_by_pos[$pos])) {
                continue;
            }
            $map[(int) $img['id_image']] = (int) $to_by_pos[$pos];
        }

        return $map;
    }
    public function destroyInPs()
    {
        $product = new Product($this->id_product);
        if ($product) {
            return $product->delete();
        }
        return false;
    }

    public function productByIcpcode($id_producto, $icp_code)
    {
        $sql = 'SELECT id_clon FROM ' . _DB_PREFIX_ . $this->name . '_clones WHERE id_producto = ' . (int) $id_producto . ' AND icp_code LIKE "' . pSQL($icp_code) . '"';
        $id_product = Db::getInstance()->getValue($sql);
        return $id_product;
    }

    public function getAttachedProducts()
    {
        $icp = explode(',', $this->icp_code);
        $products = array();
        $product_components = array();
        foreach ($icp as $component) {
            $comp = explode('-', $component);
            $qty = 1;
            if (substr_count($comp[0], 'x')) {
                $qty_parts = explode('x', $comp[0]);
                $qty = $qty_parts[1];
                $comp[0] = $qty_parts[1];
            }
            $idxComponent = new IdxComponent($comp[0], true);
            if ($idxComponent->parent) {
                if (!isset($product_components[$idxComponent->parent])) {
                    $product_components[$idxComponent->parent] = array();
                    $product_components[$idxComponent->parent]['customization'] = array();
                }
                $product_components[$idxComponent->parent]['final_component'] = $idxComponent->id_component;
                $product_components[$idxComponent->parent]['customization'][] = array('id_component' => $idxComponent->id_component, 'id_option' => $comp[1]);
            }
            $options = $idxComponent->getComponentOptions();
            if (isset($options['lang'])) {
                $option = $options['lang'][Context::getContext()->language->id];
                if (isset($comp[1]) && isset($option->options[$comp[1]]) && $option->options[$comp[1]]->attach_product != "none") {
                    if ($option->options[$comp[1]]->attach_product == 'base') {//Must change for current base product
                        $option->options[$comp[1]]->attach_product = $this->getBaseProductId();
                    }
                    if (!$option->options[$comp[1]]->attach_product) {
                        continue;
                    }
                    $prod_atta = explode('_', $option->options[$comp[1]]->attach_product);
                    $product = array(
                        'id_product' => $prod_atta[0],
                        'id_product_attribute' => $prod_atta[1],
                        'quantity' => $option->options[$comp[1]]->attach_product_qty * $qty
                    );
                    $products[] = $product;
                }
            }
        }
        if ($product_components) {
            foreach ($product_components as $product_component) {
                $product_id = IdxComponent::getProductByCustomization($product_component['final_component'], $product_component['customization']);
                $prod_atta = explode('_', $product_id);
                $product = array(
                    'id_product' => $prod_atta[0],
                    'id_product_attribute' => $prod_atta[1],
                    'quantity' => 1
                );
                $products[] = $product;
            }
        }
        return $products;
    }

    public static function getMinPrice($id_product)
    {
        $id_shop = Context::getContext()->shop->id;
        $query = new DbQuery();
        $query->select('id_clon');
        $query->from('idxrcustomproduct_clones', 'a');
        $query->innerJoin('product_shop', 'p', 'a.id_clon = p.id_product');
        $query->where('a.id_producto =' . (int) $id_product);
        $query->where('p.id_shop =' . (int) $id_shop);
        $query->orderBy('p.price ASC');

        $id_product_clon = Db::getInstance()->getValue($query);
        if ($id_product_clon) {
            if (isset(Context::getContext()->customer->id_default_group)) {
                $displaytax = Group::getPriceDisplayMethod((int) Context::getContext()->customer->id_default_group);
            } else {
                $displaytax = Group::getDefaultPriceDisplayMethod();
            }
            if ($displaytax) {
                $price = Product::getPriceStatic((int) $id_product_clon, false);
            } else {
                $price = Product::getPriceStatic((int) $id_product_clon, true);
            }
            return $price;
        }
        return false;
    }

    public function checkConstraints(&$customization, $id_configuration)
    {
        $options_selected = array();
        foreach ($customization as $option) {
            $options_selected[] = $option['id_component'] . '_' . $option['id_option'];
        }
        $sql = 'Select constraints_options from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations where id_configuration = ' . (int) $id_configuration;
        $constrains = Db::getInstance()->getValue($sql);

        if ($constrains) {
            $rules = array();
            $contstraints_array = explode(',', $constrains);
            foreach ($contstraints_array as $cont) {
                if ($cont) {
                    $cont_val = explode('@', $cont);
                    $rules[$cont_val[0]][] = explode('+', $cont_val[1]);
                }
            }

            foreach ($customization as $key => $option) {
                if (isset($rules[$option['id_component']])) {
                    $delete = true;
                    foreach ($rules[$option['id_component']] as $rules) {
                        $sub_delete = false;
                        foreach ($rules as $rule) {
                            if (!in_array($rule, $options_selected)) {
                                $sub_delete = true;
                            }
                        }
                        if (!$sub_delete) {
                            $delete = false;
                        }
                    }
                    if ($delete) {
                        unset($customization[$key]);
                    }
                }
            }
        }
    }

    public function isUniqueProductComp($id_component)
    {
        $component = new IdxComponent($id_component, true);
        if ($component->parent) {
            $comp_amount = Db::getInstance()->getValue('select count(*) from ' . _DB_PREFIX_ . 'idxrcustomproduct_components where parent = ' . (int) $component->parent);
            return (bool) ($comp_amount == 1);
        }
        return false;
    }

    public function setProductoOriginal($id_producto, $id_clon, $icp_code)
    {
        return Db::getInstance()->insert('idxrcustomproduct_clones', array(
                    'id_producto' => (int) $id_producto,
                    'id_clon' => (int) $id_clon,
                    'icp_code' => pSQL($icp_code)
        ));
    }

    public function cloneShippingConf($from, $to)
    {
        $from_carriers_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT id_carrier_reference
            FROM `' . _DB_PREFIX_ . 'product_carrier` 
            WHERE `id_product` = ' . (int) $from . '
                AND `id_shop` = ' . (int) $this->context->shop->id);
        $from_carriers = array();
        foreach ($from_carriers_data as $carrier) {
            $from_carriers[] = $carrier['id_carrier_reference'];
        }

        if (!is_object($to)) {
            $to = new Product($to);
        }
        $to->setCarriers($from_carriers);
    }

    public function cloneSpecifPrices($from, $to)
    {
        SpecificPrice::deleteByProductId($to);
        Product::duplicateSpecificPrices($from, $to);
    }

    public function checkIntegrity($id_product)
    {
        $current_shop = $this->context->shop->id;
        $category_default = Db::getInstance()->getValue('Select id_category_default from ' . _DB_PREFIX_ . 'product_shop where id_product = ' . (int) $id_product . ' and id_shop = ' . (int) $current_shop);
        if (!$category_default) {
            $id_category_default = (int) Configuration::get('IDXRCUSTOMPRODUCT_CATEGORY');
            $correct_shop = Db::getInstance()->getValue('Select id_shop from ' . _DB_PREFIX_ . 'product_shop where id_product = ' . (int) $id_product . ' and id_category_default = ' . (int) $id_category_default);
            if ($correct_shop) {
                Db::getInstance()->delete('product_shop', 'id_product = ' . (int) $id_product . ' and id_shop = ' . (int) $current_shop);
                $row = Db::getInstance()->getRow('Select * from ' . _DB_PREFIX_ . 'product_shop where id_product = ' . (int) $id_product . ' and id_shop = ' . (int) $correct_shop);
                $row['id_shop'] = $current_shop;
                Db::getInstance()->insert('product_shop', $row);
            }
        }
    }

    public function getProductQtyInCart($id_product)
    {
        if (method_exists($this->context->cart, 'getProductQuantity')) {
            return $this->context->cart->getProductQuantity($id_product);
        }

        $cart_qts = $this->context->cart->containsProduct($id_product);
        if (!$cart_qts) {
            return 0;
        } else {
            return $cart_qts['quantity'];
        }
    }

    public function setAdditionalImpact($component_id, &$item, $impact_options, $customization)
    {
        if (!$impact_options) {
            return;
        }
        foreach ($impact_options as $rule) {
            if ($rule->option_impacted == $component_id . '_' . $item->id) {
                foreach ($customization as $value) {
                    if ($rule->option_trigger == $value['id_component'] . '_' . $value['id_option']) {
                        if ($rule->impact_percent) {
                            $item->price_impact *= 1 + ($rule->impact_percent / 100);
                        } else {
                            $item->price_impact += $rule->impact_fixed;
                        }
                    }
                }
            }
        }
    }
    
    public function getBaseProductId()
    {
        $product = new Product($this->id_product_parent);
        return $this->id_product_parent."_".$product->cache_default_attribute;
    }
    
    public static function isCustomizedById($id_product)
    {
        $prod_obj = new IdxCustomizedProduct($id_product);
        return $prod_obj->isCustomized();
    }
    
    public function addDiscount($id_product, $amount, $amount_wt)
    {
        $current_discount = SpecificPrice::getByProductId($id_product);
        
        if(!$current_discount) {
            $specificPrice = new SpecificPrice();
            $specificPrice->id_product = (int) $id_product;
            $specificPrice->id_product_attribute = 0;
            $specificPrice->id_shop = (int) $this->context->shop->id;
            $specificPrice->id_currency = 0;
            $specificPrice->id_customer = 0;
            $specificPrice->id_group = 0;
            $specificPrice->id_country = 0;
            $specificPrice->price = -1;
            $specificPrice->from_quantity = 1;
            $specificPrice->reduction = Tools::ps_round($amount, Context::getContext()->getComputingPrecision());
            $specificPrice->reduction_tax = false;
            $specificPrice->reduction_type = 'amount';
            $specificPrice->from = '0000-00-00 00:00:00';
            $specificPrice->to = '0000-00-00 00:00:00';
            $specificPrice->add();
            
            $product = new Product($id_product);
            $product->price = Tools::ps_round($product->price+$amount, Context::getContext()->getComputingPrecision());
            $product->update();
        } else {
            $applied = false;
            foreach ($current_discount as $discount) {
                if ($discount['reduction_type'] == "amount" && !$discount['id_customer'] && !$discount['id_group']) {
                    $specificPrice = new SpecificPrice($discount['id_specific_price']);
                    $total_reduction = $specificPrice->reduction + (($specificPrice->reduction_tax)?$amount_wt:$amount);
                    $specificPrice->reduction = Tools::ps_round($total_reduction, Context::getContext()->getComputingPrecision());
                    $specificPrice->update();
                    $applied = true;
                    break;
                }
            }
            if ($applied) {
                $product = new Product($id_product);
                $product->price = Tools::ps_round($product->price+$amount, Context::getContext()->getComputingPrecision());
                $product->update();
            }
        }
    }
    
    public static function incrementPriceProduct($amount)
    {
        $id_product = Product::getIdByReference('CUSTOMISATION_CHARGES');
        $module = Module::getInstanceByName('idxrcustomproduct');

        if ($id_product) {
            $product = new Product($id_product);
            $product->price = $amount;
            $product->update();
        } else {
            $product = new Product();
            $product->name = 'CUSTOMISATION_CHARGES';
            $product->reference = 'CUSTOMISATION_CHARGES';
            $product->id_category_default = (int) Configuration::get(Tools::strtoupper($module->name . '_CATEGORY'));
            $product->price = $amount;
            $product->add();
            $id_product = $product->id;
            $categories = array();
            $categories[0] = (int) Configuration::get(Tools::strtoupper($module->name . '_CATEGORY'));
            $product->updateCategories($categories);
        }
        
        $module->setQty(1000, $id_product);
        return $id_product;
    }
}
