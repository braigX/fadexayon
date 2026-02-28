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

$exist = Db::getInstance()->executeS("SHOW COLUMNS FROM `"._DB_PREFIX_."idxrcustomproduct_configurations` LIKE 'products'");
if ($exist[0]['Type'] == "varchar(255)") {
    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'idxrcustomproduct_configurations` MODIFY COLUMN products text, MODIFY COLUMN categories text;';
}

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
