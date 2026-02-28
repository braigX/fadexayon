<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
function upgrade_module_2_4_7($module)
{
    $tbl_manu_url = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_manufacturer_url` (
            `id_manufacturer` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `link_rewrite` VARCHAR(191) NOT NULL,
            PRIMARY KEY (`id_manufacturer`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

    $tbl_supplier_url = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_supplier_url` (
            `id_supplier` int(11) unsigned NOT NULL,
            `link_rewrite` VARCHAR(191) NOT NULL,
            PRIMARY KEY (`id_supplier`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

    Db::getInstance()->execute($tbl_manu_url);
    Db::getInstance()->execute($tbl_supplier_url);
    EtsSeoManufacturer::updateLinkRewriteManufacturer();
    EtsSeoSupplier::updateLinkRewriteSupplier();

    return true;
}
