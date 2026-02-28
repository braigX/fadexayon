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

function upgrade_module_3_5_9()
{
    $context = Context::getContext();
    $sql = [];
    $sql[] = 'UPDATE ' . _DB_PREFIX_ . 'ed_delivery_zones SET delivery_min = NULL WHERE delivery_min = 0';
    $sql[] = 'UPDATE ' . _DB_PREFIX_ . 'ed_delivery_zones SET delivery_max = NULL WHERE delivery_max = 0';

    foreach ($sql as $query) {
        if (Db::getInstance()->execute($query) === false) {
            $context->controller->errors[] = Db::getInstance()->getMsgError();

            return false;
        }
    }

    return true;
}
