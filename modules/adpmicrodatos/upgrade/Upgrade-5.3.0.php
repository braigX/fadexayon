<?php
/**
 * 2007-2023 PrestaShop.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Ádalop <contact@prestashop.com>
 *  @copyright 2023 Ádalop
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_3_0($object)
{
    $iso_country_default = Context::getContext()->country->iso_code;

    $paises = Country::getCountries(Context::getContext()->language->id, true);

    $refund_policy = [];

    foreach ($paises as $key => $value) {
        $active = 0;
        $return_policy_categories = '';
        $merchant_return_days = '';
        $return_method = '';
        $return_fees = '';

        if (strtolower($iso_country_default) == strtolower($value['iso_code'])) {
            $active = Configuration::get('ADP_ACTIVE_MICRODATA_REFUND_POLICY');
            $return_policy_categories = Configuration::get('ADP_RETURN_POLICY_CATEGORIES');
            $merchant_return_days = Configuration::get('ADP_MERCHANT_RETURN_DAYS');
            $return_method = Configuration::get('ADP_RETURN_METHOD');
            $return_fees = Configuration::get('ADP_RETURN_FEES');
        }

        $refund_policy[strtolower($value['iso_code'])] = [
            'active' => $active,
            'applicable_country' => strtolower($value['iso_code']),
            'name_country' => $value['country'],
            'return_policy_categories' => $return_policy_categories,
            'merchant_return_days' => $merchant_return_days,
            'return_method' => $return_method,
            'return_fees' => $return_fees,
        ];
    }

    Configuration::updateValue('ADP_RETURN_POLICY_INFORMATION', json_encode($refund_policy));
    Configuration::updateValue('ADP_ID_FEATURE_3D_MODEL', '');

    Configuration::deleteByName('ADP_ACTIVE_MICRODATA_REFUND_POLICY');
    Configuration::deleteByName('ADP_RETURN_POLICY_CATEGORIES');
    Configuration::deleteByName('ADP_MERCHANT_RETURN_DAYS');
    Configuration::deleteByName('ADP_RETURN_METHOD');
    Configuration::deleteByName('ADP_RETURN_FEES');

    return true;
}
