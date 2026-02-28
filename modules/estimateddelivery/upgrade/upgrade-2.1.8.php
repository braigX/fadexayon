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

function upgrade_module_2_1_8()
{
    $sql = [];

    // Add a column for the products release date.
    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ed_prod_oos` ADD `release_date` TEXT';

    foreach ($sql as $query) {
        Db::getInstance()->execute($query);
    }

    // All done if we get here the upgrade is successfull
    return true;
}
