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

$exist = Db::getInstance()->executeS("SHOW COLUMNS FROM `"._DB_PREFIX_."idxrcustomproduct_components` LIKE 'show_price'");
if (!$exist) {
    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'idxrcustomproduct_components` ADD show_price tinyint(1) NOT NULL DEFAULT 0;';
}

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
