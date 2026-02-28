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

// Remove the old ed-order-states.tpl as it has been relocated
function upgrade_module_3_8_9()
{
    $files = ['ed-order-states.tpl'];
    foreach ($files as $file) {
        $filename = dirname(__FILE__) . '/../views/templates/admin/' . $file;
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
    $picking_order_states = json_decode(Configuration::get('order_state'), true);
    if (!empty($picking_order_states)) {
        Configuration::updateValue('ed_picking_order_state', json_encode(array_map('intval', $picking_order_states)));
    }

    return true;
}
