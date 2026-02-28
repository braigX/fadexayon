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

function upgrade_module_3_6_0($module)
{
    $context = Context::getContext();
    $sql = [];
    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_warehouse` (
    `id_warehouse` int(11) NOT NULL,
    `id_supplier` int(11) NOT NULL,
    `id_shop` int(11) NOT NULL,
    `picking_days` int(4),
    CONSTRAINT ed_war UNIQUE (id_warehouse,id_shop)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    foreach ($sql as $query) {
        if (Db::getInstance()->execute($query) === false) {
            $context->controller->errors[] = Db::getInstance()->getMsgError();
            echo Db::getInstance()->getMsgError();

            return false;
        }
    }

    return true;
}
