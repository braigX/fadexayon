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

function upgrade_module_3_1_1($module)
{
    $context = Context::getContext();
    // Review and fix the definitive state and the delivery date for the orders.
    // $sql = 'SELECT id_order FROM '._DB_PREFIX_.'ed_orders WHERE is_definitive = 0';
    /*$sql = 'UPDATE '._DB_PREFIX_.'ed_orders SET is_definitive = 0';
    Db::getInstance()->execute($sql);*/
    Configuration::deleteByName('ED_LIST_CART');
    Configuration::updateValue('ED_LIST_NEW-PRODUCTS', 1);
    // Register new hook for email template vars.
    if (!$module->isRegisteredInHook('actionGetExtraMailTemplateVars')) {
        $module->registerHook('actionGetExtraMailTemplateVars');
    }
    // Review past orders to make sure the Delviery Date is correct, this may take a while
    $sql = 'SELECT id_order FROM ' . _DB_PREFIX_ . 'ed_orders WHERE is_definitive = 0';
    $results = Db::getInstance()->executeS($sql);
    $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');

    $sql = 'SELECT COLUMN_NAME as name FROM INFORMATION_SCHEMA.`COLUMNS` WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\' AND TABLE_NAME = \'' . _DB_PREFIX_ . 'ed_carriers\' AND COLUMN_NAME = "ignore_picking"';
    $exist = Db::getInstance()->executeS($sql);
    if ($exist === false || count($exist) == 0) {
        // Create the ignore_picking column
        $sql = [];
        $sql[] = 'ALTER TABLE ' . _DB_PREFIX_ . 'ed_carriers ADD COLUMN `ignore_picking` tinyint(1) DEFAULT 0 AFTER `picking_limit`';
        foreach ($sql as $query) {
            if (Db::getInstance()->execute(pSQL($query)) === false) {
                return false;
            }
        }
    }
    // $states = $this->getLogableStates();
    $toDef = [];
    if ($results === false) {
        return true;
    } elseif (count($results) > 0) {
        // Review all non definitive orders
        foreach ($results as $result) {
            $order = new Order((int) $result['id_order']);
            $delivery_date = $order->getHistory($id_lang, false, false, OrderState::FLAG_PAID);
            if (count($delivery_date) > 0) {
                if ($order->current_state != $delivery_date[0]['id_order_state']) {
                    $params = [
                        'cart' => [
                            'id_address_delivery' => $order->id_address_delivery,
                        ],
                        'order' => $order,
                    ];
                    $module->updateEstimatedDeliveryForOrder($order, $params, false, $delivery_date[0]['date_add']);
                } else {
                    $toDef[] = (int) $result['id_order'];
                }
            }
        }
        if (count($toDef) > 0) {
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'ed_orders SET is_definitive = 1 WHERE id_order IN (' . implode(',', $toDef) . ')';
            if (Db::getInstance()->execute($sql) === false) {
                $context->controller->errors[] = Db::getInstance()->getMsgError() . '<br>' . $sql;
            }
        }
    }

    return true;
}
