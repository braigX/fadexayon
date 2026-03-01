<?php
/**
* 2007-2026 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innovadeluxe SL
* @copyright 2026 Innovadeluxe SL
*
* @license   INNOVADELUXE
*/

function upgrade_module_1_7_7($module)
{
    $table = _DB_PREFIX_ . 'idxrcustomproduct_saved_customisations';
    $tableExists = (bool) Db::getInstance()->getValue("SHOW TABLES LIKE '" . pSQL($table) . "'");
    if (!$tableExists) {
        return Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . $table . '` (
             `id_saved_customisation` int(11) NOT NULL AUTO_INCREMENT,
             `id_customer` int(11) NOT NULL,
             `id_product` int(11) NOT NULL,
             `id_product_attribute` int(11) NOT NULL DEFAULT 0,
             `customisation_name` varchar(100) NOT NULL,
             `customization` longtext,
             `extra_info` longtext,
             `snapshot_json` longtext,
             `preview_html` longtext,
             `thumbnail_svg` longtext,
             `date_add` datetime NOT NULL,
             `date_upd` datetime NOT NULL,
             PRIMARY KEY (`id_saved_customisation`),
             KEY `idxr_saved_customer_product` (`id_customer`, `id_product`),
             KEY `idxr_saved_customer_date` (`id_customer`, `date_add`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;'
        );
    }

    $col = Db::getInstance()->getRow("SHOW COLUMNS FROM `" . pSQL($table) . "` LIKE 'thumbnail_svg'");
    if (!$col) {
        return Db::getInstance()->execute(
            'ALTER TABLE `' . $table . '` ADD COLUMN `thumbnail_svg` longtext NULL AFTER `preview_html`'
        );
    }

    return true;
}

