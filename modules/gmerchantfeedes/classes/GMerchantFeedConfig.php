<?php
/**
 * 2019 ExtraSolutions
 *
 * NOTICE OF LICENSE
 *
 * @author    ExtraSolutions
 * @copyright 2019 ExtraSolutions
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class GMerchantFeedConfig extends ObjectModel
{
    public $id_gmerchantfeedes;
    public $name;
    public $only_active;
    public $taxonomy_ref;
    public $only_available;
    public $description_crop;
    public $modify_uppercase_title;
    public $modify_uppercase_description;
    public $suffix_feature_title_set = 0;
    public $suffix_attribute_title_set = 0;
    public $parts_payment_enabled;
    public $product_title_in_product_type;
    public $rounding_price;
    public $identifier_exists_mpn;
    public $visible_product_hide;
    public $mpn_force_on;
    public $filtered_by_associated_type;
    public $export_only_new_products;
    public $exclude_empty_description;
    public $filtered_by_only_with_image;
    public $max_parts_payment;
    public $interest_rates;
    public $url_suffix;
    public $title_suffix;
    public $id_suffix;
    public $description_suffix;
    public $additional_each_product;
    public $type_image;
    public $brand_type;
    public $rule_out_of_stock;
    public $mpn_type;
    public $gtin_type;
    public $additional_image;
    public $type_description;
    public $select_lang;
    public $id_currency;
    public $id_country;
    public $id_carrier;
    public $id_reference;
    public $export_attributes;
    public $export_attributes_only_first;
    public $export_attributes_as_product;
    public $export_attribute_url;
    public $export_attribute_prices;
    public $export_attribute_images;
    public $export_feature;
    public $export_sale;
    public $only_once_show_the_price;
    public $show_sale_price;
    public $disable_tag_identifier_exists;
    public $use_additional_shipping_cost;
    public $export_product_quantity;
    public $param_order_out_of_stock_sys;
    public $get_features_gender;
    public $get_features_age_group;
    public $instance_of_tax;
    public $google_product_category_rewrite;
    public $taxonomy_language = 0;
    public $shipping_weight_format = 3;
    public $get_attribute_color;
    public $get_attribute_material;
    public $get_attribute_size;
    public $get_attribute_pattern;
    public $unique_product;
    public $identifier_exists;
    public $export_non_available;
    public $category_filter;
    public $manufacturers_filter;
    public $manufacturers_exclude_filter;
    public $with_suppliers;
    public $exclude_suppliers;
    public $min_price_filter;
    public $max_price_filter;
    public $exclude_ids;
    public $date_update;
    public $from_product_id;
    public $to_product_id;
    public $exclude_discount_price_more;
    public $export_width;
    public $export_width_inp;
    public $export_height;
    public $export_height_inp;
    public $export_depth;
    public $export_depth_inp;
    public $select_manufacturers;
    public $filter_qty_from;
    public $local_product_inventory_feed;
    public $store_code_inventory_feed;

    public $price_change = '0.00';
    public $price_change_type;

    public static $moduleName = 'gmerchantfeedes';
    public static $definition = array(
        'table' => 'gmerchantfeedes',
        'primary' => 'id_gmerchantfeedes',
        'multilang' => false,
        'fields' => array(
            'id_gmerchantfeedes' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'only_active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'taxonomy_ref' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'only_available' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'description_crop' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'modify_uppercase_title' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'modify_uppercase_description' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'suffix_feature_title_set' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'suffix_attribute_title_set' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'parts_payment_enabled' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'product_title_in_product_type' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'rounding_price' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'identifier_exists_mpn' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'visible_product_hide' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'mpn_force_on' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'filtered_by_associated_type' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'export_only_new_products' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'exclude_empty_description' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'filtered_by_only_with_image' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'export_attributes' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'export_attributes_only_first' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'export_attributes_as_product' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'export_attribute_url' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'export_attribute_prices' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'export_attribute_images' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'export_feature' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'use_additional_shipping_cost' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'export_product_quantity' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'param_order_out_of_stock_sys' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'additional_image' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'unique_product' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'identifier_exists' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'export_non_available' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'only_once_show_the_price' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'show_sale_price' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'disable_tag_identifier_exists' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'export_width' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'export_height' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'export_depth' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'type_description' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'max_parts_payment' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'type_image' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'select_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_country' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'get_features_gender' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'get_features_age_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'instance_of_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'google_product_category_rewrite' => array('type' => self::TYPE_BOOL, 'validate' => 'isUnsignedId'),
            'taxonomy_language' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'shipping_weight_format' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'min_price_filter' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'max_price_filter' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'export_sale' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'from_product_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'to_product_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'exclude_discount_price_more' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'interest_rates' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'url_suffix' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'title_suffix' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'id_suffix' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'description_suffix' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'additional_each_product' => array('type' => self::TYPE_HTML, 'validate' => 'isString'),
            'brand_type' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'rule_out_of_stock' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'mpn_type' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'gtin_type' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'id_carrier' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'id_reference' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'get_attribute_color' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'get_attribute_material' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'get_attribute_size' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'get_attribute_pattern' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'category_filter' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'manufacturers_filter' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'manufacturers_exclude_filter' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'with_suppliers' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'exclude_suppliers' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'exclude_ids' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'export_width_inp' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'export_height_inp' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'export_depth_inp' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'price_change' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'price_change_type' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'filter_qty_from' => array('type' => self::TYPE_STRING, 'validate' => 'isInt'),
            'date_update' => array('type' => self::TYPE_DATE),
            'local_product_inventory_feed' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'store_code_inventory_feed' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
        )
    );

    public $arrayFields = array(
        'get_attribute_size', 'get_attribute_color',
        'get_attribute_pattern', 'get_attribute_material',
        'id_carrier', 'id_reference', 'mpn_type', 'gtin_type',
        'manufacturers_filter', 'exclude_suppliers', 'with_suppliers', 'manufacturers_exclude_filter'
    );

    public $foreignTableArrayField = array(
        'category_filter', 'custom_attribute', 'features_custom_mod', 'custom_product_row'
    );

    /**
     * @param false $forForm
     * @return array
     * @throws PrestaShopException
     */
    public function getFields($forForm = false)
    {
        $fieldValues = parent::getFields();

        if ($forForm) {
            foreach ($this->arrayFields as $arrayField) {
                if (!in_array($arrayField, $this->foreignTableArrayField)) {
                    $fieldValues[$arrayField . '[]'] = (!empty($this->{$arrayField})) ? json_decode($this->{$arrayField}) : '';
                }
            }

            foreach ($this->foreignTableArrayField as $arrayField) {
                $fieldValues = array_merge($fieldValues, $this->getForeignField($arrayField));
            }
        }

        return $fieldValues;
    }

    public function jsonDecodeField($data)
    {
        if (!$data) {
            return [];
        }

        try {
            return json_decode($data);
        } catch (Exception $exception) {
        }

        return [];
    }

    /**
     * @param string $field
     * @return array|array[]
     */
    private function getForeignField($field = '')
    {
        switch ($field) {
            case 'category_filter':
                return array($field => $this->jsonDecodeField($this->{$field}));
            case 'custom_attribute':
                return array($field => $this->getCustomAttrById($this->id_gmerchantfeedes));
            case 'features_custom_mod':
                return array($field => $this->getCustomFeatureById($this->id_gmerchantfeedes));
            case 'custom_product_row':
                return array($field => $this->getCustomParamsById($this->id_gmerchantfeedes));
        }

        return array();
    }

    public static function getFeedsForForms()
    {
        $link = Context::getContext()->link;
        $lang = Context::getContext()->language->id;

        $sql = 'SELECT `id_gmerchantfeedes`, `id_currency`, `id_country`, `name`, `select_lang`, `date_update`, `local_product_inventory_feed` 
                FROM ' . _DB_PREFIX_ . 'gmerchantfeedes';
        $feeds = DB::getInstance()->executeS($sql);
        $moduleName = self::$moduleName;
        $feeds = array_map(function ($feed) use ($link, $lang, $moduleName) {
            $selectLang = (new Language($feed['select_lang']));
            $feed['locale'] = (!isset($selectLang->locale) || empty($selectLang->locale)) ? $selectLang->language_code : $selectLang->locale;
            $feed['cron'] = $link->getModuleLink(
                $moduleName,
                'generation',
                array(
                    'key' => (int)$feed['id_gmerchantfeedes'],
                    'token' => md5(_COOKIE_KEY_ . $feed['id_gmerchantfeedes'])
                )
            );
            $feed['cron_rebuild'] = $link->getModuleLink(
                $moduleName,
                'generation',
                array(
                    'key' => (int)$feed['id_gmerchantfeedes'],
                    'token' => md5(_COOKIE_KEY_ . $feed['id_gmerchantfeedes']),
                    'only_rebuild' => 1
                )
            );
            $feed['cron_download'] = $link->getModuleLink(
                $moduleName,
                'generation',
                array(
                    'key' => (int)$feed['id_gmerchantfeedes'],
                    'token' => md5(_COOKIE_KEY_ . $feed['id_gmerchantfeedes']),
                    'only_download' => 1
                )
            );

            if ($feed['local_product_inventory_feed']) {
                $feed['cron_inventory_download'] = $link->getModuleLink(
                    $moduleName,
                    'generation',
                    array(
                        'key' => (int)$feed['id_gmerchantfeedes'],
                        'token' => md5(_COOKIE_KEY_ . $feed['id_gmerchantfeedes']),
                        'inventory' => 1
                    )
                );
            }

            $feed['currency'] = Currency::getCurrency((int)$feed['id_currency']);
            $feed['country'] = Country::getNameById((int)$lang, (int)$feed['id_country']);

            return $feed;
        }, $feeds);

        return $feeds;
    }

    public function save($null_values = false, $auto_date = true)
    {
        $save = parent::save($null_values, $auto_date);

        if ($save) {
            $this->updateCustomAttr();
            $this->updateCustomFeatures();
            $this->updateCustomParams();
        }

        return $save;
    }

    public function delete()
    {
        $id_gshopping = (isset($this->id_gmerchantfeedes) && !empty($this->id_gmerchantfeedes))
            ? (int)$this->id_gmerchantfeedes : (int)$this->id;

        $rem = parent::delete();

        Db::getInstance()->delete('gmerchantfeedes_custom_features', 'id_gmerchantfeedes=' . (int)$id_gshopping);
        Db::getInstance()->delete('gmerchantfeedes_custom_attributes', 'id_gmerchantfeedes=' . (int)$id_gshopping);

        return $rem;
    }

    private function updateCustomFeatures()
    {
        $id_gshopping = (isset($this->id_gmerchantfeedes) && !empty($this->id_gmerchantfeedes))
            ? (int)$this->id_gmerchantfeedes : (int)$this->id;

        $features_custom_modification = array();
        Db::getInstance()->delete('gmerchantfeedes_custom_features', 'id_gmerchantfeedes=' . (int)$id_gshopping);
        if (Tools::getValue('feature_custom_inheritage')) {
            $feature_custom_inheritage = Tools::getValue('feature_custom_inheritage');
            $feature_custom_inheritage_param = Tools::getValue('feature_custom_inheritage_param');
            if (is_array($feature_custom_inheritage) && count($feature_custom_inheritage)) {
                foreach ($feature_custom_inheritage as $f_pos => $feature_s) {
                    if (Validate::isInt($feature_s) && $feature_s > 0) {
                        $features_custom_modification[] = array(
                            'id_feature' => (int)$feature_s,
                            'unit' => (isset($feature_custom_inheritage_param[$f_pos]) && !empty($feature_custom_inheritage_param[$f_pos])) ? urldecode($feature_custom_inheritage_param[$f_pos]) : ''
                        );
                    }
                }
            }

            if (is_array($features_custom_modification) && count($features_custom_modification)) {
                Db::getInstance()->insert('gmerchantfeedes_custom_features', array_map(function ($data) use ($id_gshopping) {
                    return array(
                        'id_gmerchantfeedes' => (int)$id_gshopping,
                        'id_feature' => (int)$data['id_feature'],
                        'unit' => pSQL($data['unit'])
                    );
                }, $features_custom_modification));
            }
        }
    }

    private function updateCustomParams()
    {
        $id_gshopping = (isset($this->id_gmerchantfeedes) && !empty($this->id_gmerchantfeedes))
            ? (int)$this->id_gmerchantfeedes : (int)$this->id;
        $features_custom_modification = array();
        Db::getInstance()->delete('gmerchantfeedes_custom_rows', 'id_gmerchantfeedes=' . (int)$id_gshopping);
        if (Tools::getValue('custom_product_row')) {
            $custom_product_row = Tools::getValue('custom_product_row');
            $custom_product_row_param = Tools::getValue('custom_product_row_param');

            if (is_array($custom_product_row) && count($custom_product_row)) {
                foreach ($custom_product_row as $f_pos => $feature_s) {
                    if (!empty($feature_s)) {
                        $features_custom_modification[] = array(
                            'id_param' => pSQL($feature_s),
                            'unit' => (isset($custom_product_row_param[$f_pos]) && !empty($custom_product_row_param[$f_pos])) ? urldecode($custom_product_row_param[$f_pos]) : ''
                        );
                    }
                }
            }

            if (is_array($features_custom_modification) && count($features_custom_modification)) {
                Db::getInstance()->insert('gmerchantfeedes_custom_rows', array_map(function ($data) use ($id_gshopping) {
                    return array(
                        'id_gmerchantfeedes' => (int)$id_gshopping,
                        'id_param' => pSQL($data['id_param']),
                        'unit' => pSQL($data['unit'])
                    );
                }, $features_custom_modification));
            }
        }
    }

    private function updateCustomAttr()
    {
        $id_gshopping = (isset($this->id_gmerchantfeedes) && !empty($this->id_gmerchantfeedes))
            ? (int)$this->id_gmerchantfeedes : (int)$this->id;

        DB::getInstance()->delete('gmerchantfeedes_custom_attributes', 'id_gmerchantfeedes=' . (int)$id_gshopping);
        $customAttrKey = Tools::getValue('custom_attr_key');
        $customAttrId = Tools::getValue('custom_attr_id');

        $prepareAttrForInsert = array();
        if (is_array($customAttrKey) && count($customAttrKey)) {
            foreach ($customAttrKey as $linePos => $attr) {
                $attr = trim($attr);
                $attr = str_replace(' ', '_', $attr);
                $prepareAttrForInsert[] = array(
                    'id_gmerchantfeedes' => (int)$id_gshopping,
                    'id_attribute' => (int)$customAttrId[$linePos],
                    'unit' => pSQL($attr)
                );
            }

            DB::getInstance()->insert('gmerchantfeedes_custom_attributes', $prepareAttrForInsert);
        }
    }

    public static function getCustomAttrById($id_gmerchantfeed, $id_lang = null)
    {
        if (is_null($id_lang)) {
            $id_lang = Context::getContext()->language->id;
        }

        $rows = Db::getInstance()->executeS('SELECT `id_attribute`, `unit` 
            FROM `' . _DB_PREFIX_ . 'gmerchantfeedes_custom_attributes`
                WHERE `id_gmerchantfeedes` = ' . (int)$id_gmerchantfeed);
        return ($rows && is_array($rows) && count($rows)) ? array_map(function ($data) use ($id_lang) {
            return array_merge($data, array('name' => (new AttributeGroup($data['id_attribute'], $id_lang))->name));
        }, $rows) : array();
    }

    public static function getCustomFeatureById($id_gmerchantfeed, $id_lang = null)
    {
        if (is_null($id_lang)) {
            $id_lang = Context::getContext()->language->id;
        }

        $rows = Db::getInstance()->executeS('SELECT `id_feature`, `unit` 
            FROM `' . _DB_PREFIX_ . 'gmerchantfeedes_custom_features`
                WHERE `id_gmerchantfeedes` = ' . (int)$id_gmerchantfeed);

        return ($rows && is_array($rows) && count($rows)) ? array_map(function ($data) use ($id_lang) {
            return array_merge($data, array('name' => (new Feature($data['id_feature'], $id_lang))->name));
        }, $rows) : array();
    }

    public static function getCustomParamsById($id_gmerchantfeed)
    {
        $rows = Db::getInstance()->executeS('SELECT `id_param`, `unit` 
            FROM `' . _DB_PREFIX_ . 'gmerchantfeedes_custom_rows`
                WHERE `id_gmerchantfeedes` = ' . (int)$id_gmerchantfeed);

        return ($rows && is_array($rows) && count($rows)) ? $rows : array();
    }


    public static function getProductAttributesIdsOverride($id_product, $onlyMain = false)
    {
        return Db::getInstance()->executeS('
		SELECT pa.id_product_attribute
		FROM `' . _DB_PREFIX_ . 'product_attribute` pa
		WHERE pa.`id_product` = ' . (int)$id_product .
            ($onlyMain ? ' AND pa.default_on = 1' : ''));
    }
}
