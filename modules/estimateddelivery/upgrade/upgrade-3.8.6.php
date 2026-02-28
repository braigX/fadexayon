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

function upgrade_module_3_8_6($module)
{
    $context = Context::getContext();
    $sql = [];

    // Update the column types
    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ed_holidays` MODIFY COLUMN `holiday_name` VARCHAR(255)';

    foreach ($sql as $query) {
        if (Db::getInstance()->execute($query) === false) {
            $context->controller->errors[] = Db::getInstance()->getMsgError();
            echo Db::getInstance()->getMsgError();

            return false;
        }
    }

    return true;
}
