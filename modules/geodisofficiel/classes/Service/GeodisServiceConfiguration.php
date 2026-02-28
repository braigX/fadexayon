<?php
/**
 * 2024 Novatis Agency - www.novatis-paris.fr.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@novatis-paris.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    NOVATIS <info@novatis-paris.fr>
 *  @copyright 2024 NOVATIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceTranslation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisFiscalCode.php';

class GeodisServiceConfiguration
{
    protected static $instance = null;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new GeodisServiceConfiguration();
        }

        return self::$instance;
    }

    public function get($key, $idLang = null, $idShopGroup = null, $idShop = null)
    {
        $storedKey = Tools::strtoupper(GEODIS_MODULE_NAME.'_'.$key);
        $value = Configuration::get(
            $storedKey,
            $idLang,
            $idShopGroup,
            $idShop,
            $this->getConfiguration($key)['default']
        );

        if ($this->getConfiguration($key)['serialized']) {
            if (!$value) {
                $value = array();
            } else {
                $value = json_decode($value, true);
            }
        }

        return $value;
    }

    public function set($key, $value, $html = false, $idShopGroup = null, $idShop = null)
    {
        $storedKey = Tools::strtoupper(GEODIS_MODULE_NAME.'_'.$key);

        if ($this->getConfiguration($key)['serialized']) {
            if (!$value) {
                $value = array();
            }
            $value = json_encode($value);
        }

        Configuration::updateValue($storedKey, $value, $html, $idShopGroup, $idShop);
    }

    protected function getConfiguration($key)
    {
        foreach ($this->getConfigurations() as $configuration) {
            if ($key == $configuration['name']) {
                return $configuration;
            }
        }

        throw new Exception('No configuration "'.$key.'"');
    }

    public function getPostValue($key, $default = null)
    {
        $configuration = $this->getConfiguration($key);

        if (is_null($default)) {
            $default = $configuration['default'];
        }

        if ($configuration['type'] == 'swap') {
            return Tools::getValue($key.'_selected', $default);
        }
        return Tools::getValue($key, $default);
    }

    public function getConfigurations()
    {
        return array(
            $this->getSwitch(
                'active',
                true,
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.active.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.active.desc'),
                true
            ),
            $this->getOrderStateSelect(
                'partial_shipping_state',
                null,
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.partialShippingState.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.partialShippingState.desc'),
                true,
                true
            ),
            $this->getOrderStateSelect(
                'complete_shipping_state',
                null,
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.completeShippingState.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.completeShippingState.desc'),
                true,
                true
            ),
            $this->getOrderStateMultiSelect(
                'ignore_order_states',
                json_encode($this->getDefaultIdOrderStateIgnored()),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.ignoreOrderStates.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.ignoreOrderStates.desc'),
                true
            ),
            $this->getOrderStateMultiSelect(
                'available_order_states',
                null,
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.availableOrderStates.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.availableOrderStates.desc'),
                true
            ),
            $this->getText(
                'departure_date_delay',
                30,
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.departureDateDelay.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.departureDateDelay.desc'),
                true,
                'isInt'
            ),
            $this->getText(
                'api_login',
                '',
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.api.login.label'),
                null,
                false,
                null
            ),
            $this->getText(
                'api_secret_key',
                '',
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.api.secret.label'),
                null,
                false,
                null
            ),
            $this->getSwitch(
                'use_white_label',
                false,
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.useWhiteLabel.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.useWhiteLabel.desc'),
                false
            ),
            $this->getSwitch(
                'map_enabled',
                false,
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.mapEnabled.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.mapEnabled.desc'),
                true
            ),
            $this->getSwitch(
                'load_google_map_js',
                true,
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.loadGoogleMapJs.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.loadGoogleMapJs.desc'),
                true
            ),
            $this->getText(
                'google_map_api_key',
                '',
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.googleMapApiKey.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.googleMapApiKey.desc'),
                true,
                null
            ),
            $this->getText(
                'google_map_client',
                '',
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.googleMapClient.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.googleMapClient.desc'),
                true,
                null
            ),
            $this->getText(
                'google_api_distance_last_error_call',
                false,
                '',
                '',
                false,
                null
            ),
            $this->getText(
                'date_customer_synchronization',
                false,
                '',
                '',
                false,
                null
            ),
            $this->getText(
                'customer_synchronization_crontask',
                false,
                '',
                '',
                false,
                null
            ),
            $this->getText(
                'shipment_synchronization_crontask',
                false,
                '',
                '',
                false,
                null
            ),
            $this->getText(
                'log_purge_crontask',
                false,
                '',
                '',
                false,
                null
            ),
            $this->getText(
                'front_columns_customisation',
                'layouts/layout-left-column.tpl',
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.layout.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.layout.desc'),
                true,
                null
            ),
            $this->getFiscalCodeSelect(
                'default_fiscal_code',
                'CRD',
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.fiscalCode.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.fiscalCode.desc'),
                true
            ),
            $this->getSwitch(
                'thermal_printing_activated',
                false,
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.thermalPrinting.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.thermalPrinting.desc'),
                true
            ),
            $this->getText(
                'thermal_printing_port',
                '3000',
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.thermalPrinting.port.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.thermalPrinting.port.desc'),
                true,
                null
            ),
            $this->getText(
                'purge_delay',
                '30',
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.purgeDelay.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.purgeDelay.desc'),
                true,
                null
            ),
            $this->getText(
                'carrier_logo_width',
                40,
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.carrierLogoWidth.label'),
                GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.carrierLogoWidth.desc'),
                true,
                'isInt'
            ),
        );
    }

    protected function getText(
        $name,
        $default,
        $label,
        $description,
        $generalConfiguration,
        $validator
    ) {
        return array(
            'name' => $name,
            'default' => $default,
            'type' => 'text',
            'serialized' => false,
            'label' => $label,
            'desc' => $description,
            'validator' => $validator,
            'general_configuration' => $generalConfiguration,
        );
    }

    protected function getFiscalCodeSelect(
        $name,
        $default,
        $label,
        $description,
        $generalConfiguration
    ) {
        $fiscalCodes = GeodisFiscalCode::getCollection();
        $fiscalCodes->where('label', '=', $default);
        $fiscalCode = $fiscalCodes->getFirst();
        if ($fiscalCode) {
            $default = $fiscalCode->id;
        }

        $return = array(
            'name' => $name,
            'default' => $default,
            'type' => 'select',
            'serialized' => false,
            'general_configuration' => $generalConfiguration,

            'options' => array(
                'query' => array(),
                'id' => 'value',
                'name' => 'name',
            ),
            'label' => $label,
            'desc' => $description,
        );

        $return['options']['query'][] = array(
            'value' => 0,
            'name' => '',
        );

        $fiscalCodes = GeodisFiscalCode::getCollection();
        foreach ($fiscalCodes as $fiscalCode) {
            $return['options']['query'][] = array(
                'value' => $fiscalCode->id,
                'name' => $fiscalCode->label,
            );
        }
        return $return;
    }

    protected function getOrderStateSelect(
        $name,
        $default,
        $label,
        $description,
        $generalConfiguration,
        $allowNone = true
    ) {
        $orderStates = OrderState::getOrderStates(Context::getContext()->language->id);
        $return = array(
            'name' => $name,
            'default' => $default,
            'type' => 'select',
            'serialized' => false,
            'general_configuration' => $generalConfiguration,

            'options' => array(
                'query' => array(),
                'id' => 'value',
                'name' => 'name',
            ),
            'label' => $label,
            'desc' => $description,
        );

        if ($allowNone) {
            $return['options']['query'][] = array(
                'value' => 0,
                'name' => GeodisServiceTranslation::get('Admin.ConfigurationBack.OrderStateSelect.none'),
            );
        }

        foreach ($orderStates as $orderState) {
            $return['options']['query'][] = array(
                'value' => $orderState['id_order_state'],
                'name' => $orderState['name'],
            );
        }

        return $return;
    }

    protected function getOrderStateMultiSelect($name, $default, $label, $description, $generalConfiguration)
    {
        $orderStates = OrderState::getOrderStates(Context::getContext()->language->id);

        if (is_null($default)) {
            $default = array();

            foreach ($orderStates as $orderState) {
                $default[] = $orderState['id_order_state'];
            }

            $default = json_encode($default, true);
        }

        $return = array(
            'name' => $name,
            'default' => $default,
            'type' => 'swap',
            'size' => count($orderStates),
            'serialized' => true,
            'general_configuration' => $generalConfiguration,
            'options' => array(
                'query' => array(),
                'id' => 'value',
                'name' => 'name',
            ),
            'label' => $label,
            'desc' => $description,
        );

        foreach ($orderStates as $orderState) {
            $return['options']['query'][] = array(
                'value' => $orderState['id_order_state'],
                'name' => $orderState['name'],
            );
        }

        return $return;
    }

    protected function getSwitch($name, $default, $label, $description, $generalConfiguration)
    {
        return array(
            'name' => $name,
            'default' => $default,
            'type' => 'switch',
            'is_bool' => true,
            'serialized' => false,
            'general_configuration' => $generalConfiguration,
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => GeodisServiceTranslation::get('*.*.switch.enabled'),
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => GeodisServiceTranslation::get('*.*.switch.disabled'),
                ),
            ),
            'label' => $label,
            'desc' => $description,
        );
    }

    protected function getDefaultIdOrderStateIgnored()
    {
        $collection = new PrestaShopCollection('OrderState');
        $collection->where('shipped', '=', 1);

        $ids = array();
        foreach ($collection as $orderState) {
            $ids[] = $orderState->id;
        }

        return $ids;
    }
}
