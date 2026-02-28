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
            AND table_name = '"._DB_PREFIX_."idxrcustomproduct_components'
            AND column_name = 'parent'";
$exist = Db::getInstance()->getRow($exist_q);
if (!$exist) {
    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'idxrcustomproduct_components` ADD parent int(11);';
}

$sql[] = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'idxrcustomproduct_component_attribute (
    id_component_attribute int(11) NOT NULL AUTO_INCREMENT,
    id_component int(11),
    id_attribute_group int(11),
    PRIMARY KEY  (id_component_attribute)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
