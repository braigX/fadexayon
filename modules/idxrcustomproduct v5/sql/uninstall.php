<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2016 Innova Deluxe SL

 * @license   INNOVADELUXE
 */

$sql = array();
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxrcustomproduct_components';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxrcustomproduct_notes';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_fav';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxrcustomproduct_customer_extra';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxrcustomproduct_files';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxrcustomproduct_clones';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxrcustomproduct_configurationimage';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxrcustomproduct_component_attribute';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
