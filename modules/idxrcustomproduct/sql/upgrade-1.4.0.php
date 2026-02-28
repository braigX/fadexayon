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

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'idxrcustomproduct_configurationimage` (
 id_configurationimage int(11) NOT NULL AUTO_INCREMENT,
 id_configuration int(11) NOT NULL,
 conf_index int(11) NOT NULL DEFAULT 1,
 attached_values text,
 PRIMARY KEY (id_configurationimage)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$exist_q = "SELECT *
            FROM INFORMATION_SCHEMA.COLUMNS
           WHERE table_schema = '"._DB_NAME_."' 
            AND table_name = '"._DB_PREFIX_."idxrcustomproduct_components'
             AND column_name = 'optional'";
$exist = Db::getInstance()->getRow($exist_q);
if (!$exist) {
    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'idxrcustomproduct_components` ADD `optional` tinyint(1) unsigned AFTER `type`;';
}

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
