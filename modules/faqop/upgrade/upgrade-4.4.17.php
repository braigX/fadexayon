<?php
/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 *    @author    Opossum Dev
 *    @copyright Opossum Dev
 *    @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_4_17($module)
{
    $res = true;

    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'op_faq_lists` ' .
        'ADD `is_only_default_category` tinyint(1) unsigned NOT NULL DEFAULT \'0\' AFTER `category_ids_p`, ' .
        'ADD `admin_name` varchar(255) AFTER `position`';

    $res &= Db::getInstance()->execute($sql);

    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'op_faq_lists_cache` ' .
        'ADD `is_only_default_category` tinyint(1) unsigned NOT NULL DEFAULT \'0\' AFTER `category_ids_p`';

    $res &= Db::getInstance()->execute($sql);

    $res &= $module->cache_helper->recacheAllLists();

    return $res;
}
