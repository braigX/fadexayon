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

function upgrade_module_3_3_2()
{
    // Update the picking limits of the carriers
    $sql = 'SELECT id_reference, id_shop, picking_limit FROM ' . _DB_PREFIX_ . 'ed_carriers';
    $results = Db::getInstance()->executeS(pSQL($sql));
    if ($results === false) {
        return false;
    } else {
        if (count($results) > 0) {
            foreach ($results as $result) {
                if (Tools::strlen($result['picking_limit']) <= 5) {
                    if (Db::getInstance()->update('ed_carriers', ['picking_limit' => json_encode(array_fill(0, 7, pSQL($result['picking_limit'])))], 'id_reference = ' . (int) $result['id_reference'] . ' AND id_shop = ' . (int) $result['id_shop']) === false) {
                        return false;
                    }
                }
            }
        }
    }

    // Update the picking limits of the carrier global value for each shop
    if (Shop::isFeatureActive()) {
        $shops = Shop::getShops(false, null, true);
    } else {
        $context = Context::getContext();
        $shops = [$context->shop->id];
    }
    foreach ($shops as $shop) {
        $picking_limit = Configuration::get('ed_picking_limit', null, null, $shop);
        if (Tools::strlen($picking_limit) <= 5) {
            Configuration::updateValue('ed_picking_limit', json_encode(array_fill(0, 7, $picking_limit)), null, $shop);
        }
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            if (Configuration::get('ED_LOCATION', null, null, $shop) == 5) {
                Configuration::updateValue('ED_LOCATION', -5, null, $shop);
            }
        }
    }
    $picking_limit = Configuration::get('ed_picking_limit');
    if (Tools::strlen($picking_limit) <= 5) {
        Configuration::updateValue('ed_picking_limit', json_encode(array_fill(0, 7, $picking_limit)));
    }
    if (version_compare(_PS_VERSION_, '1.7', '<')) {
        if (Configuration::get('ED_LOCATION') == 5) {
            Configuration::updateValue('ED_LOCATION', -5);
        }
    }

    return true;
}
