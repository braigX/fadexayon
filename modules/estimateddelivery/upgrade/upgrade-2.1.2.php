<?php
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_1_2($object)
{
    /* Update procedure for version 2.1.2 adding new features exclusively for out of stock products */
    if (!$object->isRegisteredInHook('displayAdminProductsExtra')) {
        $object->registerHook('displayAdminProductsExtra');
    }

    if (!$object->isRegisteredInHook('actionProductUpdate')) {
        $object->registerHook('actionProductUpdate');
    }
    $sql = [];
    // Create the category table in database
    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_cat_oos` (
    `id_cat_oos` int(11) NOT NULL AUTO_INCREMENT,
    `id_category` int(11) NOT NULL UNIQUE,
    `id_shop` int(11) NOT NULL,
    `delay` int(4),
    PRIMARY KEY  (`id_cat_oos`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    // Create the product table in database
    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_prod_oos` (
    `id_prod_oos` int(11) NOT NULL AUTO_INCREMENT,
    `id_product` int(11) NOT NULL UNIQUE,
    `id_shop` int(11) NOT NULL,
    `delay` int(4),
    PRIMARY KEY  (`id_prod_oos`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    foreach ($sql as $query) {
        if (Db::getInstance()->execute($query) == false) {
            return false;
        }
    }

    // All done if we get here the upgrade is successfull
    return true;
}
