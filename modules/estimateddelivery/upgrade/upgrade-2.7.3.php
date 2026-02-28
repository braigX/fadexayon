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

function upgrade_module_2_7_3()
{
    $sql = [];
    $sql[] = 'ALTER TABLE ' . _DB_PREFIX_ . 'ed_supplier_picking DROP INDEX id_supplier';
    $sql[] = 'ALTER TABLE ' . _DB_PREFIX_ . 'ed_brand_picking DROP INDEX id_manufacturer';
    foreach ($sql as $query) {
        if (Db::getInstance()->execute(pSQL($query)) === false) {
            return false;
        }
    }

    return true;
}
