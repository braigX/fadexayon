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

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'idxrcustomproduct_runtime_customisations` (
 `id_runtime_customisation` int(11) NOT NULL AUTO_INCREMENT,
 `id_customer` int(11) NOT NULL DEFAULT 0,
 `id_guest` int(11) NOT NULL DEFAULT 0,
 `id_cart` int(11) NOT NULL DEFAULT 0,
 `id_product` int(11) NOT NULL,
 `id_customized_product` int(11) NOT NULL DEFAULT 0,
 `id_product_attribute` int(11) NOT NULL DEFAULT 0,
 `customization` longtext,
 `extra_info` longtext,
 `snapshot_json` longtext,
 `thumbnail_svg` longtext,
 `id_snap` int(11) NOT NULL DEFAULT 0,
 `source` varchar(32) NOT NULL DEFAULT "cart",
 `date_add` datetime NOT NULL,
 `date_upd` datetime NOT NULL,
 PRIMARY KEY (`id_runtime_customisation`),
 KEY `idxr_runtime_cart` (`id_cart`, `date_add`),
 KEY `idxr_runtime_customer` (`id_customer`, `date_add`),
 KEY `idxr_runtime_guest` (`id_guest`, `date_add`),
 KEY `idxr_runtime_product` (`id_product`, `id_product_attribute`),
 KEY `idxr_runtime_customized_product` (`id_customized_product`),
 KEY `idxr_runtime_snap` (`id_snap`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
