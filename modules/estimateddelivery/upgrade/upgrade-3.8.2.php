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

function upgrade_module_3_8_2($module)
{
    $new_settings = [
        [
            'ED_DISABLE_PRODUCT_CARRIERS' => 0,
            'SET_CAN_OOS_IF_ORIGINAL_IS_POSITIVE' => 0,
        ],
    ];
    foreach ($new_settings as $key => $default_value) {
        if (!Configuration::hasKey($key)) {
            Configuration::updateValue($key, $default_value);
        }
    }
    Configuration::deleteByName('ed_disable_combi_check');
    // Invert the USE TOT setting to have better coherence in the advanced settings
    Configuration::updateValue($module->prefix . 'USE_TOT', (int) !Configuration::get($module->prefix . 'USE_TOT'));

    return true;
}
