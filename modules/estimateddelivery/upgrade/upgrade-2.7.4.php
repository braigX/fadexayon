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

function upgrade_module_2_7_4()
{
    $sql = [];
    $sql[] = 'ALTER TABLE ' . _DB_PREFIX_ . 'ed_cat_oos ADD COLUMN `excluded` int(2) NOT NULL DEFAULT 0 AFTER `picking_days`';
    foreach ($sql as $query) {
        if (Db::getInstance()->execute(pSQL($query)) === false) {
            return false;
        }
    }

    return true;
}
