<?php
/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2022 Innova Deluxe SL

 * @license   INNOVADELUXE
 */

$sql = array();

$exist = Db::getInstance()->executeS("SHOW COLUMNS FROM `"._DB_PREFIX_."idxrcustomproduct_components` LIKE 'multivalue'");
if (!$exist) {
    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'idxrcustomproduct_components` ADD multivalue varchar(255) NOT NULL DEFAULT "unique";';
}

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
