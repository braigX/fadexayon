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

function upgrade_module_4_3_17($module)
{
    return $module->cache_helper->recacheAllLists();
}
