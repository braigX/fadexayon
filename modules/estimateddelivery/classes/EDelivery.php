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

class EDelivery
{
    public $id_product;
    public $id_product_attribute;
    public $name;
    public $picking_date;
    public $shipping_day;
    public $delivery_min;
    public $delivery_max;
    public $delivery_cmp_min;
    // Days difference between today and the date min
    public $delivery_cmp_days_min;
    public $delivery_cmp_max;
    // Days difference between today and the date max
    public $delivery_cmp_days_max;
    public $initial_date;
    // public $initial_weekday;
    public $formatted_date;
    /* To allow precise sorting */
    public $picking_limit;
    public $picking_limits;
    public $position;
    public $id_range_weight;
    public $id_range_price;
    public $price;
    public $price_cmp = 0;
    public $is_free;
    public $delay;
    public $days_to_add;
    /*public $has_available_date;
    public $has_release_date;*/
    public $tot;
    public $compute_available_oos = false;

    public $rest;
    public $time_limit;
    public $rest_with_format;
    public $picking_weekday;

    public function __construct($dp)
    {
        $this->id_product = $dp->id_product;
        $this->id_product_attribute = $dp->id_product_attribute;
        $this->dp = $dp;
        $this->compute_available_oos = Configuration::get('ED_APPLY_OOS_TO_AVAIL');
        $this->tot = EstimatedDelivery::getTot();
    }

    public function calculatePicking($dh, $date, $forced_date = false)
    {
        $picking_days = $this->dc->picking_days;
        // Save the vars to use later sor sorting
        $this->picking_limits = $this->dc->picking_limits;
        $this->position = $this->dc->position;

        return $dh->checkNext('picking', $date, $picking_days, $this->picking_limits, 'date', $forced_date);
    }

    public function calculateShipping($dh, $dc, $dp)
    {
        $types = ['min', 'max'];
        foreach ($types as $type) {
            if (isset($this->delivery_cmp_min)) {
                $sd = $this->delivery_cmp_min;
            } else {
                $sd = $this->shipping_day;
            }
            $days = $this->dc->{$type};
            if ($type == 'max') {
                $days -= $this->dc->min;
            }
            if ($type == 'min') {
                // Increase the delivery days if the product or combination are Out Of Stock
                // Only for the minimum release
                if (!$dp->is_release && (($dp->isOOS && !$dp->is_available) || ($dp->is_available && $this->compute_available_oos && Configuration::get('ed_available_date_msg_' . $this->context->language->id) == ''))) {
                    $sd = $dh->addDaysIteration($sd, $dc->shippingdays, $dp->oos_add_days);
                }
                // TODO ADD Customization DAYS
                if ($dp->customizable && (int) $dp->add_custom_days > 0) {
                    $sd = $dh->addDaysIteration($sd, $dc->shippingdays, $dp->add_custom_days);
                }
                // Update the initial shipping date
                $this->shipping_day = $sd;
            }
            //            echo '<br>Start Shipping:<br>';
            //            echo 'Mode '.$type.' Date: '.$sd.'<br/>';
            // Calculate the Shipping Date
            $this->{'delivery_' . $type} = $dh->addDaysIteration($sd, $dc->shippingdays, (int) $days);
            //            echo 'After Iteration<br>Mode '.$type.' Date: '.$sd.'<br/><br><br>';
            // Save the date in timestamp format to sort the carriers
            $this->{'delivery_cmp_' . $type} = $this->{'delivery_' . $type};

            // Prepare the date for delivery MAX
            if ($type == 'min') {
                $this->delivery_max = $this->delivery_min;
            }
            // Add the days diff between today and the delivery cmp date
            $this->{'delivery_cmp_days_' . $type} = EDTools::getDaysDiff($this->{'delivery_cmp_' . $type});
        }
    }

    public function setInitialDate($date)
    {
        $this->initial_date = $date;
        // $this->initial_weekday = date('w', strtotime($date));
        $weekday = (date('N', strtotime($date)) - 1);
        $this->picking_limit = is_array($this->picking_limits) && isset($this->picking_limits[$weekday]) ? $this->picking_limits[$weekday] : '23:59';
    }

    public function setDeliveryCmpMin($date)
    {
        $this->delivery_cmp_min = $date;
    }

    public function setDeliveryCmpMax($date)
    {
        $this->delivery_cmp_max = $date;
    }

    public function setDeliveryCarrier($dc)
    {
        $this->dc = $dc;
        $this->delay = $dc->delay;
        $this->name = $dc->name;
        if ($dc->ignore_picking == 1) {
            $this->dp->ignorePickingDays();
        }
    }

    public function setDateFormatForED($df, $use_tot = true)
    {
        $types = ['min', 'max'];
        foreach ($types as $type) {
            //            $days_diff = EDTools::getDaysDiff($this->{'delivery_cmp_'.$type});
            // //            echo 'TOT: '.(int)$use_tot.' days_diff: '.$days_diff.'<br>';
            //            if ($use_tot && $days_diff < 2) {
            //                $this->tot = true;
            //            }
            //            Tools::dieObject([EstimatedDelivery::getTot(),$this->tot], false);

            $this->{'delivery_' . $type} = EDTools::setDateFormatForED($this->{'delivery_cmp_' . $type}, $df);
        }
    }

    public static function allVirtual($products)
    {
        foreach ($products as $p) {
            if ($p['is_virtual'] == 0) {
                return false;
            }
        }

        return true;
    }

    public function setDeliveryRestParameters()
    {
        $module = Module::getInstanceByName('estimateddelivery');
        $hours = 0;
        $minutes = 0;
        if (isset($this->dc)) {
            // Remove a day to set the current day
            $date = $this->getInitialDate($module);
            $this->days_to_add = DeliveryHelper::getDateDiff($date, $this->initial_date, '%a');
            $picking_limit = array_map('intval', explode(':', $this->picking_limit));
            if (!isset($picking_limit[1])) {
                $picking_limit[1] = 0;
            }
            $picking_date_with_limit = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($this->picking_date)) . ' ' . implode(':', $picking_limit) . ':00'));

            //            Tools::dieObject([$this->initial_date, $date, $this->days_to_add, $picking_limit], false);
            $diff_from_today = DeliveryHelper::getDateDiff($date, $picking_date_with_limit);
            $hours = ($diff_from_today->days * 24) + $diff_from_today->h;
            $minutes = $diff_from_today->i;
            if (EstimatedDelivery::$debug_mode) {
                $module->debugVar([$hours, $minutes], 'Rest');
                $module->debugVar(($hours > 0 ? $hours . ' ' . $module->l('hours', 'EDelivery') . ' ' . $module->l('and', 'EDelivery') . ' ' : '') . $minutes . ' ' . $module->l('minutes', 'EDelivery'), 'Rest with format');
                $module->debugVar(isset($picking_limit) ? date('H:i', strtotime(implode(':', $picking_limit))) : '00:00', 'Time Limit');
            }
            // Assign the variables to the delivery
            $this->rest = [$hours, $minutes];
            $this->time_limit = isset($picking_limit) ? date('H:i', strtotime(implode(':', $picking_limit))) : '00:00';
            $this->rest_with_format = ($hours > 0 ? $hours . ' ' . $module->l('hours', 'EDelivery') . ' ' . $module->l('and', 'EDelivery') . ' ' : '') . $minutes . ' ' . $module->l('minutes', 'EDelivery');
            //        $this->days_to_add = $days_to_add ?? 0;
            $this->picking_weekday = EDTools::setDateFormatForED($this->picking_date, 'weekday_df');
        }
    }

    private function getInitialDate($module)
    {
        if (!Context::getContext()->controller instanceof FrontController
            || !$module->getAdvMode()
            || !Configuration::get('ED_FORCE_DATE')
            || !Validate::isDate(Configuration::get('ED_FORCED_DATE'))
        ) {
            return date('Y-m-d H:i:s');
        }

        return Configuration::get('ED_FORCED_DATE');
    }

    public function __clone()
    {
        foreach ($this as $key => $val) {
            if (is_object($val) || is_array($val)) {
                $this->{$key} = unserialize(serialize($val)); // Strictly necessary to be able to clone the class
            }
        }
    }
}
