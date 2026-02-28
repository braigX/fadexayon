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

function upgrade_module_2_7_2($module)
{
    if (!$module->isRegisteredInHook('actionFrontControllerSetMedia')) {
        $module->registerHook('actionFrontControllerSetMedia');
    }
    $sql = [];
    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_prod_combi_oos` (
    `id_product_attribute_oos` int(11) NOT NULL AUTO_INCREMENT,
    `id_product` int(11) NOT NULL,
    `id_product_attribute` int(11) NOT NULL,
    `id_shop` int(11) NOT NULL,
    `restock_date` TEXT,
    PRIMARY KEY  (`id_product_attribute_oos`),
    CONSTRAINT ed_prod UNIQUE (id_product_attribute, id_shop)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
    foreach ($sql as $query) {
        Db::getInstance()->execute(pSQL($query));
    }

    return true;
}
