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

$sql = array();
$module_key = 'gmerchantfeedes';

if (!defined('_PS_VERSION_')) {
    exit;
}

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_. pSQL($module_key) . '` (
    `id_' . pSQL($module_key) . '` int(11) NOT NULL AUTO_INCREMENT,
    `only_active` TINYINT NOT NULL DEFAULT \'1\',
    `taxonomy_ref` TINYINT NOT NULL DEFAULT \'0\',
    `only_available` TINYINT NOT NULL DEFAULT \'0\',
    `description_crop` TINYINT NOT NULL DEFAULT \'1\',
    `modify_uppercase_title` TINYINT NOT NULL DEFAULT \'1\',
    `modify_uppercase_description` TINYINT NOT NULL DEFAULT \'1\',
    `suffix_feature_title_set` TINYINT NOT NULL DEFAULT \'1\',
    `suffix_attribute_title_set` TINYINT NOT NULL DEFAULT \'1\',
    `parts_payment_enabled` TINYINT NOT NULL DEFAULT \'1\',
    `rounding_price` TINYINT NOT NULL DEFAULT \'0\',
    `product_title_in_product_type` TINYINT NOT NULL DEFAULT \'0\',
    `identifier_exists_mpn` TINYINT NOT NULL DEFAULT \'0\',
    `visible_product_hide` TINYINT NOT NULL DEFAULT \'0\',
    `mpn_force_on` TINYINT NOT NULL DEFAULT \'0\',
    `filtered_by_associated_type` TINYINT NOT NULL DEFAULT \'0\',
    `exclude_empty_description` TINYINT NOT NULL DEFAULT \'0\',
    `export_only_new_products` TINYINT NOT NULL DEFAULT \'0\',
    `filtered_by_only_with_image` TINYINT NOT NULL DEFAULT \'0\',
    `export_attributes` TINYINT NOT NULL DEFAULT \'1\',
    `export_attributes_only_first` TINYINT NOT NULL DEFAULT \'1\',
    `export_attributes_as_product` TINYINT NOT NULL DEFAULT \'0\',
    `export_attribute_url` TINYINT NOT NULL DEFAULT \'1\',
    `export_attribute_prices` TINYINT NOT NULL DEFAULT \'1\',
    `export_attribute_images` TINYINT NOT NULL DEFAULT \'1\',
    `export_feature` TINYINT NOT NULL DEFAULT \'1\',
    `use_additional_shipping_cost` TINYINT NOT NULL DEFAULT \'0\',
    `export_width` TINYINT NOT NULL DEFAULT \'0\',
    `export_height` TINYINT NOT NULL DEFAULT \'0\',
    `export_depth` TINYINT NOT NULL DEFAULT \'0\',
    `export_product_quantity` TINYINT NOT NULL DEFAULT \'1\',
    `param_order_out_of_stock_sys` TINYINT NOT NULL DEFAULT \'0\',
    `additional_image` TINYINT NOT NULL DEFAULT \'1\',
    `type_description` TINYINT NOT NULL DEFAULT \'1\',
    `unique_product` TINYINT NOT NULL DEFAULT \'1\',
    `identifier_exists` TINYINT NOT NULL DEFAULT \'1\',
    `export_non_available` TINYINT NOT NULL DEFAULT \'1\',   
    `only_once_show_the_price` TINYINT NOT NULL DEFAULT \'1\',
    `show_sale_price` TINYINT NOT NULL DEFAULT \'1\',
    `disable_tag_identifier_exists` TINYINT NOT NULL DEFAULT \'0\',
    `export_sale` TINYINT NOT NULL DEFAULT \'1\',
    `local_product_inventory_feed` TINYINT NOT NULL DEFAULT \'0\',
    `max_parts_payment` INT(11) DEFAULT NULL,
    `type_image` int(11) DEFAULT NULL,
    `select_lang` int(11) DEFAULT NULL,
    `id_currency` int(11) DEFAULT NULL,
    `id_country` int(11) DEFAULT NULL,
    `get_features_gender` int(11) DEFAULT NULL,
    `get_features_age_group` int(11) DEFAULT NULL,
    `instance_of_tax` int(11) DEFAULT 3,
    `google_product_category_rewrite` TINYINT(1) DEFAULT 0, 
    `taxonomy_language` TINYINT(1) DEFAULT 0,
    `shipping_weight_format` TINYINT(1),
    `min_price_filter` int(11) DEFAULT NULL,
    `max_price_filter` int(11) DEFAULT NULL,
    `from_product_id` int(11) DEFAULT NULL,
    `to_product_id` int(11) DEFAULT NULL,
    `exclude_discount_price_more` decimal(10, 3) NOT NULL DEFAULT \'0\',
    `interest_rates` VARCHAR(256) NOT NULL,
    `name` VARCHAR( 255 ) NOT NULL,
    `url_suffix` VARCHAR( 255 ) NOT NULL,
    `title_suffix` VARCHAR( 255 ) NOT NULL,
    `id_suffix` VARCHAR( 255 ) NOT NULL,
    `description_suffix` VARCHAR( 255 ) NOT NULL,
    `additional_each_product` TEXT DEFAULT NULL,
    `brand_type` VARCHAR(255) NOT NULL,
    `rule_out_of_stock` tinyint(3) DEFAULT 0,
    `mpn_type` VARCHAR( 255 ) NOT NULL,
    `gtin_type` VARCHAR( 255 ) NOT NULL,
    `id_carrier` VARCHAR(256) NOT NULL,
    `id_reference` VARCHAR(256) NOT NULL,
    `export_width_inp` VARCHAR(256) NOT NULL,
    `export_height_inp` VARCHAR(256) NOT NULL,
    `export_depth_inp` VARCHAR(256) NOT NULL,
    `store_code_inventory_feed` VARCHAR(155) NOT NULL,
    `get_attribute_color` TEXT DEFAULT NULL,
    `get_attribute_material` TEXT DEFAULT NULL,
    `get_attribute_size` TEXT DEFAULT NULL,
    `get_attribute_pattern` TEXT DEFAULT NULL,
    `category_filter` TEXT DEFAULT NULL,
    `manufacturers_filter` TEXT DEFAULT NULL,
    `manufacturers_exclude_filter` TEXT DEFAULT NULL,
    `with_suppliers` TEXT DEFAULT NULL,
    `exclude_suppliers` TEXT DEFAULT NULL,
    `exclude_ids` TEXT DEFAULT NULL,
    `price_change` decimal(10, 2) NOT NULL DEFAULT \'0\',
    `price_change_type` varchar(50) NOT NULL,
    `filter_qty_from` int(10) NOT NULL DEFAULT \'0\',
    `date_update` DATETIME NULL,
    PRIMARY KEY  (`id_' . pSQL($module_key) . '`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_. pSQL($module_key) . '_taxonomy` (
    `id_category` int( 11 ) NOT NULL,
    `id_taxonomy` int( 11 ) NOT NULL,
    `id_lang` int( 11 ) NOT NULL,
    `name_taxonomy` TEXT DEFAULT NULL,
    INDEX (`id_category`, `id_lang`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_. pSQL($module_key) . '_custom_features` (
    `id_' . pSQL($module_key) . '` int(11) NOT NULL,
    `id_feature` int(11) NOT NULL,
    `unit` varchar(255) NOT NULL,
    INDEX  (`id_' . pSQL($module_key) . '`, `id_feature`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_. pSQL($module_key) . '_custom_rows` (
    `id_' . pSQL($module_key) . '` int(11) NOT NULL,
    `id_param` varchar(100) NOT NULL,
    `unit` varchar(255) NOT NULL,
    INDEX  (`id_' . pSQL($module_key) . '`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_. pSQL($module_key) . '_custom_attributes` (
    `id_' . pSQL($module_key) . '` int(11) NOT NULL,
    `id_attribute` int(11) NOT NULL,
    `unit` varchar(255) NOT NULL,
    INDEX  (`id_' . pSQL($module_key) . '`, `id_attribute`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_. pSQL($module_key) . '_product_rewrites` (
    `id_product` int(11) unsigned NOT NULL,
    `title` varchar(255) NOT NULL,
    `short_description` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `addition_code` text DEFAULT NULL,
    `id_lang` int(10) unsigned NOT NULL,
    INDEX  (`id_product`, `id_lang`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
