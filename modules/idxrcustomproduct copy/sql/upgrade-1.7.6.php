<?php
/**
 * 2007-2026 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2026 Innova Deluxe SL
 *
 * @license   INNOVADELUXE
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'idxrcustomproduct_saved_customisations` (
 `id_saved_customisation` int(11) NOT NULL AUTO_INCREMENT,
 `id_customer` int(11) NOT NULL,
 `id_product` int(11) NOT NULL,
 `id_product_attribute` int(11) NOT NULL DEFAULT 0,
 `customisation_name` varchar(100) NOT NULL,
 `customization` longtext,
 `extra_info` longtext,
 `snapshot_json` longtext,
 `preview_html` longtext,
 `date_add` datetime NOT NULL,
 `date_upd` datetime NOT NULL,
 PRIMARY KEY (`id_saved_customisation`),
 KEY `idxr_saved_customer_product` (`id_customer`, `id_product`),
 KEY `idxr_saved_customer_date` (`id_customer`, `date_add`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

