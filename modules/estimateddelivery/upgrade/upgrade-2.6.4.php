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

function upgrade_module_2_6_4()
{
    // Add the new configuration values for the module
    Configuration::updateValue('ED_ORDER_LONG', 0);
    Configuration::updateValue('ED_ORDER_LONG_MSG', '');
    Configuration::updateValue('ED_ORDER_LONG_NO_OOS', 0);
    // All done if we get here the upgrade is successfull
    DB::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ed` CHANGE `id_order` `id_order` INT(11) NOT NULL');

    return true;
}
