<?php
/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
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

function upgrade_module_3_3_17()
{
    $res = true;

    $databases = [
        'op_faq_lists',
        'op_faq_lists_cache',
    ];

    foreach ($databases as $db) {
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . bqSQL($db) . '` ' .
            'ADD `not_all_customer_groups` tinyint(1) unsigned NOT NULL DEFAULT \'0\' AFTER `currencies`, ' .
            'ADD `customer_groups` text AFTER `not_all_customer_groups`';

        $res &= Db::getInstance()->execute($sql);
    }

    return $res;
}
