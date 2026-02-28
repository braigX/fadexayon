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
function upgrade_module_3_9_3()
{
    $lda = [];
    if (method_exists('Configuration', 'getConfigInMultipleLangs')) {
        $lda = Configuration::getConfigInMultipleLangs('ED_ORDER_LONG_MSG');
    } else {
        foreach (Language::getLanguages(false) as $lang) {
            $lda[$lang['id_lang']] = Configuration::get('ED_ORDER_LONG_MSG_' . $lang['id_lang']);
        }
    }
    Configuration::deleteByName('ED_ORDER_LONG_MSG');
    if (!empty(array_filter($lda))) {
        Configuration::updateValue('ed_order_long_msg', $lda);
    }

    return true;
}
