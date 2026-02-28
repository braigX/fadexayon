<?php
/**
 * SEO FAQ Blocks on Any Page (Products, Categories, etc.) module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_1_17()
{
    $res = true;

    $databases = [
        'op_faq_lists',
        'op_faq_lists_cache',
    ];

    foreach ($databases as $db) {
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . bqSQL($db) . '` ' .
            'ADD `all_brands` tinyint(1) unsigned NOT NULL DEFAULT \'0\' AFTER `category_ids`, ' .
            'ADD `brand_ids` text AFTER `all_brands`, ' .
            'ADD `select_products_by_brand` tinyint(1) unsigned NOT NULL DEFAULT \'0\' AFTER `category_ids_p`, ' .
            'ADD `brand_ids_p` text AFTER `select_products_by_brand`, ' .
            'ADD `select_products_by_tag` tinyint(1) unsigned NOT NULL DEFAULT \'0\' AFTER `brand_ids_p`, ' .
            'ADD `tag_ids_p` text AFTER `select_products_by_tag`, ' .
            'ADD `select_products_by_feature` tinyint(1) unsigned NOT NULL DEFAULT \'0\' AFTER `tag_ids_p`, ' .
            'ADD `feature_ids_p` text AFTER `select_products_by_feature`';

        $res &= Db::getInstance()->execute($sql);
    }

    return $res;
}
