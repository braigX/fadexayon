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
 * Registered Trademark & Property of Smart-Modules.prpo
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class DeliveryCarrier
{
    public $id_carrier;
    public $id_reference;
    public $is_default;
    public $name;
    public $original_name;
    public $position;
    public $picking_limits;
    public $picking_days;
    public $shippingdays;
    public $shipping_method;
    public $min;
    public $max;
    public $max_width = 0;
    public $max_height = 0;
    public $max_depth = 0;
    public $active;
    public $range_behavior;
    public $delay;
    public $is_free;
    public $ignore_picking;
    public $shipping_handling;
    public $id_zone;
    protected static $disabled_carriers = [];
    protected static $allowed_groups = [];
    /**
     * @var mixed
     */

    /**
     * @var true
     */
    public function __construct($carrier, $is_default = false)
    {
        if (!in_array($carrier['id_carrier'], self::$disabled_carriers)) {
            if (Configuration::get('ED_ALLOW_EMPTY_CARRIER_GROUPS') || $this->isCarrierAllowed($carrier)) {
                if ($carrier['min'] == '') {
                    $this->min = 1;
                    unset($carrier['min']);
                }
                if ($carrier['max'] == '') {
                    $this->max = 1;
                    unset($carrier['max']);
                }
                $this->is_default = (bool) $is_default;
                $this->setCarrierName($carrier);
                $this->setCarrierPicking($carrier);
                $this->setIgnorePicking($carrier);
                if ($carrier['shippingdays'] == '') {
                    $this->shippingdays = '1111100';
                    unset($carrier['shippingdays']);
                }
                $carrier['active'] = $carrier['ed_active'];
                /*if (!Configuration::get('picking_adv')) {
                    $this->picking_days = Configuration::get('ed_picking_days');
                    unset($carrier['picking_days']);
                    $this->picking_limit = json_decode(Configuration::get('ed_picking_limit')), true);
                    unset($carrier['picking_limit']);
                }*/
                foreach ($carrier as $key => $value) {
                    if (property_exists($this, $key)) {
                        $this->{$key} = $value;
                    }
                }
                if (isset($this->id_zone)) {
                    $this->setMinMaxByZone($this->id_reference, $this->id_zone);
                }
            } else {
                $ed = Module::getInstanceByName('estimateddelivery');
                if (EstimatedDelivery::$debug_mode) {
                    $ed->debugVar($carrier['id_reference'] . ' ' . $carrier['name'], 'Carrer not allowed for current group');
                }
                self::$disabled_carriers[] = $carrier['id_carrier'];
            }
        }
    }

    private function isCarrierAllowed($carrier)
    {
        if (!isset(self::$allowed_groups[$carrier['id_carrier']])) {
            $this->getAllowedGroups();
        }
        $context = Context::getContext();
        if ($context->customer == null) {
            return true;
        }
        $customer_gr = Customer::getGroupsStatic($context->customer->id);
        $len = count($customer_gr);
        if (empty(self::$allowed_groups[$carrier['id_carrier']])) {
            return false; // No carriers allowed?
        }
        for ($i = 0; $i < $len; ++$i) {
            if (in_array($customer_gr[$i], self::$allowed_groups[$carrier['id_carrier']])) {
                return true;
            }
        }
        self::$disabled_carriers[] = $carrier['id_carrier'];

        return false;
    }

    /**
     * Gets the allowed shoups and stores in a static variable
     * If the function gets called multiple times, tries to get only the missing carriers
     *
     * @return false|void
     */
    private function getAllowedGroups()
    {
        $sql = 'SELECT id_carrier, id_group
            FROM ' . _DB_PREFIX_ . 'carrier_group';
        if (!empty(self::$allowed_groups)) {
            $sql .= ' WHERE id_carrier NOT IN (' . implode(',', array_keys(self::$allowed_groups)) . ')';
        }
        $results = Db::getInstance()->executeS($sql);
        if ($results === false) {
            return false;
        } else {
            foreach ($results as $r) {
                self::$allowed_groups[$r['id_carrier']][] = $r['id_group'];
            }
        }
    }

    /**
     * @param $carriers The carriers list
     * @param $controller The current controller
     * @param $order The order object, otherwise false
     *
     * @return The carriers list after disabling the not allowed ones
     */
    public static function cleanDisabledCarriers($carriers, $controller, $order)
    {
        $is_order = $order !== false || self::isOrder($controller);
        $selective = Configuration::get('ED_ORDER_FORCE') == 2;
        if (($is_order && !$selective) || $controller == 'EDOrderUpdate' || $controller == 'AdminEstimatedDelivery') {
            return $carriers;
        }
        foreach ($carriers as $key => $value) {
            if ($carriers[$key]->active == 0) {
                if ($is_order && $selective && Configuration::get('ED_ORDER_FORCE_CARRIER_' . $carriers[$key]->id_reference) == 'on') {
                    continue;
                }
                unset($carriers[$key]);
            }
        }

        return $carriers;
    }

    private static function isOrder($controller)
    {
        if ((int) Configuration::get('ED_ORDER_FORCE') >= 1) {
            $order_controllers = ['order', 'order-opc', 'onepagecheckout', 'supercheckout', 'AdminOrders', 'EDOrderUpdate'];

            return in_array($controller, $order_controllers);
        }

        return false;
    }

    private function setCarrierName(&$carrier)
    {
        if ($carrier['ed_alias'] != '' && Configuration::get('ed_carrier_adv')) {
            $this->name = $carrier['ed_alias'];
            $this->original_name = $carrier['name'];
            unset($carrier['name']);
            unset($carrier['ed_alias']);
        } else {
            $this->name = $carrier['name'];
        }
    }

    private function setCarrierPicking(&$carrier)
    {
        if (Configuration::get('ed_picking_adv')) {
            $this->picking_days = $carrier['picking_days'];
            $this->picking_limits = json_decode($carrier['picking_limit'], true);
        } else {
            $this->picking_days = Configuration::get('ed_picking_days');
            $this->picking_limits = json_decode(Configuration::get('ed_picking_limit'), true);
        }
        unset($carrier['picking_days'], $carrier['picking_limit']);
    }

    private function setIgnorePicking(&$carrier)
    {
        if (!Configuration::get('ed_carrier_adv')) {
            $this->ignore_picking = false;
            unset($carrier['ignore_picking']);
        }
    }

    public function setPickingLimit($limit)
    {
        $this->picking_limits = $limit;
    }

    public function setMinMaxByZone($id_reference, $id_zone)
    {
        if ((int) Configuration::get('ed_carrier_zone_adv') == 1) {
            $result = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'ed_delivery_zones` WHERE id_reference = ' . (int) $id_reference . ' AND id_zone = ' . (int) $id_zone);
            if ($result) {
                if ($result[0]['delivery_min'] != '' && $result[0]['delivery_max'] != '') {
                    $this->min = $result[0]['delivery_min'];
                    $this->max = $result[0]['delivery_max'];
                } else {
                    $global_value = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'ed_delivery_zones` WHERE id_reference = ' . (int) $id_reference . ' AND id_zone = 0');
                    if ($global_value) {
                        if ($global_value[0]['delivery_min'] != '' && $global_value[0]['delivery_max'] != '') {
                            $this->min = $global_value[0]['delivery_min'];
                            $this->max = $global_value[0]['delivery_max'];
                        }
                    }
                }
            }
        }
    }
}
