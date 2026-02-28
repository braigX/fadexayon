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

function upgrade_module_2_5_1()
{
    $tmp_sql = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\' AND TABLE_NAME = \'' . _DB_PREFIX_ . 'ed\' AND COLUMN_NAME = \'id_carrier\'';

    $res = DB::getInstance()->executeS($tmp_sql);
    if (empty($res) || count($res) == 0) {
        $sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'ed ADD `id_carrier` int(11) NOT NULL';
        if (!DB::getInstance()->execute($sql)) {
            return false;
        }
    }

    return true;
}
