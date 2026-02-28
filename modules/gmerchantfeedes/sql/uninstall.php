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

$sql = array();
$module_key = 'gmerchantfeedes';

$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_. pSQL($module_key) . '`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_. pSQL($module_key) . '_taxonomy`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_. pSQL($module_key) . '_custom_features`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_. pSQL($module_key) . '_custom_attributes`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_. pSQL($module_key) . '_product_rewrites`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_. pSQL($module_key) . '_custom_rows`';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
