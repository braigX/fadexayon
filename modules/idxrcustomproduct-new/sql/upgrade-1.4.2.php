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

$exist_q = "SELECT *
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE table_schema = '"._DB_NAME_."' 
            AND table_name = '"._DB_PREFIX_."idxrcustomproduct_configurations'
            AND column_name = 'productbase_component'";
$exist = Db::getInstance()->getRow($exist_q);
if (!$exist) {
    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'idxrcustomproduct_configurations` ADD productbase_component tinyint(1) NOT NULL DEFAULT 0;';
}

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
