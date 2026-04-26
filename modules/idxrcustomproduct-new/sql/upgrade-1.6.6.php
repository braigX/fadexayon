<?php
/**
 * 2007-2023 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2023 Innova Deluxe SL

 * @license   INNOVADELUXE
 */

$sql = array();

$exist = Db::getInstance()->executeS("SHOW COLUMNS FROM `"._DB_PREFIX_."idxrcustomproduct_components_opt_impact` LIKE 'attach_product_type'");

if (!$exist) {
    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'idxrcustomproduct_components_opt_impact` ADD  attach_product_type varchar(255) DEFAULT NULL;';
}

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
