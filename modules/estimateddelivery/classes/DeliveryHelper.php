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

class DeliveryHelper
{
    protected $holidays;
    protected static $force_date = false;
    private static $disabled_products = [];

    public function __construct() // was $dps, $carriers
    {
        $this->inital_date = date('Y-m-d');
        $this->current_time = date('H:i:s');
        $this->holidays = $this->getHolidays();
    }

    /**
     * Check if the current product is disabled
     *
     * @param $id_product the product ID to check
     *
     * @return bool return true if the product has been disabled, false otherwhise
     */
    public static function isDisabledProduct($id_product)
    {
        if ((int) $id_product > 0) {
            if (!isset(self::$disabled_products[$id_product][0])) {
                self::$disabled_products[$id_product][0] = self::getProductDisabledStatus($id_product);
            }

            return (bool) self::$disabled_products[$id_product][0];
        }
    }

    public static function isDisabledCombination($id_product, $id_product_attribute)
    {
        if ((int) $id_product_attribute > 0) {
            if (!isset(self::$disabled_products[$id_product][$id_product_attribute])) {
                self::$disabled_products[$id_product][$id_product_attribute] = self::getCombinationDisabledStatus($id_product, $id_product_attribute);
            }

            return self::$disabled_products[$id_product][$id_product_attribute];
        }

        return false;
    }

    private static function getProductDisabledStatus($id_product)
    {
        return Db::getInstance()->getValue('SELECT disabled FROM ' . _DB_PREFIX_ . 'ed_prod WHERE id_product = ' . (int) $id_product . ' ' . Shop::addSqlRestriction());
    }

    private static function getCombinationDisabledStatus($id_product, $id_product_attribute)
    {
        return (bool) Db::getInstance()->getValue('SELECT disabled FROM ' . _DB_PREFIX_ . 'ed_prod_combi WHERE id_product = ' . (int) $id_product . ' AND id_product_attribute = ' . (int) $id_product_attribute . ' ' . Shop::addSqlRestriction());
    }

    private function getHolidays()
    {
        // Get a -1 month date to start checking the holidays.
        $todayDate = date('m-d', strtotime('-1 month'));
        $results = Db::getInstance()->executeS('
            SELECT * FROM ' . _DB_PREFIX_ . 'ed_holidays h
            LEFT JOIN ' . _DB_PREFIX_ . 'ed_holidays_shop hs ON (h.id_holidays = hs.id_holidays AND hs.id_shop = ' . (int) Context::getContext()->shop->id . ')
            WHERE active = 1
            ORDER BY holiday_start ASC');
        // $results = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ed_holidays WHERE active = 1 ORDER BY holiday_start ASC');
        if (empty($results) || $results === false) {
            return false;
        } else {
            $today = strtotime('today');
            $currentYear = date('Y');
            $nextYear = date('Y', strtotime('+1 year'));
            $holidays = [];
            foreach ($results as $result) {
                if ($result['repeat']) {
                    $holidays[] = ['holiday_start' => $this->addYearToDate($result['holiday_start'], $currentYear), 'holiday_end' => $this->addYearToDate($result['holiday_end'], $currentYear)];
                    $holidays[] = ['holiday_start' => $this->addYearToDate($result['holiday_start'], $nextYear), 'holiday_end' => $this->addYearToDate($result['holiday_end'], $nextYear)];
                } else {
                    if (strtotime($result['holiday_start']) < $today && strtotime($result['holiday_end']) < $today) {
                        // Skip the holiday as it is in the past
                        continue;
                    }
                    $holidays[] = ['holiday_start' => $result['holiday_start'], 'holiday_end' => $result['holiday_end']];
                }
            }
            // Sort the arrays
            usort($holidays, function ($a, $b) {
                return strtotime($a['holiday_start']) - strtotime($b['holiday_start']);
            });

            return $holidays;
        }
    }

    /**
     * Used to build the dates for the holidays
     * Gets a date without year and adds the year
     *
     * @param $date
     * @param $year
     *
     * @return string The date with the year needed
     */
    private function addYearToDate($date, $year)
    {
        return $year . '-' . date('m-d', strtotime($date));
    }

    public function checkHolidays($date, $inverse = false)
    {
        $init_date = $date;
        // Make it like standard timestamp to calculate the next non holidays day
        $datecmp = date('Y-m-d', strtotime($date));
        $datecmp .= ' 00:00:00';
        if (is_array($this->holidays) && count($this->holidays) > 0) {
            foreach ($this->holidays as $holiday) {
                $hs = $holiday['holiday_start'] . ' 00:00:00';
                $he = $holiday['holiday_end'] . ' 00:00:00';
                if ($datecmp >= $hs && $datecmp <= $he) {
                    if (!$inverse) {
                        $date = date('Y-m-d', strtotime($he . ' + 1 days'));
                    } else {
                        $date = date('Y-m-d', strtotime($hs . ' - 1 days'));
                    }
                }
            }
        }
        if ($init_date == $date) {
            return $date;
        } else {
            return $this->checkHolidays($date, $inverse);
        }
    }

    public function addDaysIteration($date, $pattern, $days, $inverse = false)
    {
        $add_days = $inverse ? '-1 day' : '+1 day';

        if ($pattern == '0000000') {
            $pattern = '1111100';
        }

        // Check if the current day it's a holiday and get the next working day
        $date = $this->checkHolidays($date, $inverse);
        // If days is 0 and the pattern allows for the current day, return the date
        if ($days == 0 && $pattern[date('N', strtotime($date)) - 1] == '1') {
            return $date;
        }

        // Loop to find the next working day according to the pattern
        do {
            $date = date('Y-m-d', strtotime($date . $add_days));
            $date = $this->checkHolidays($date, $inverse);
        } while ($pattern[date('N', strtotime($date)) - 1] == '0' || --$days > 0);

        return $date;
    }

    public function checkNext($case, $date, $pattern, $picking_limits = '', $return = 'date', $forced_date = false, $inverse = false)
    {
        $add_days = $inverse ? -1 : 1;
        $add = 0;
        if ($pattern != '0000000' && $pattern != '') {
            if ($case == 'picking') {
                if ($picking_limits == '') {
                    $picking_limits = json_decode(Configuration::get('ed_picking_limit'), true);
                }
                if (empty($picking_limits)) {
                    $picking_limits = array_fill(0, 7, '23:59');
                }
                // Picking must be next day
                $date = $this->checkHolidays($date, $inverse);
                if (date('Y-m-d H:i:s', strtotime($date)) != date('Y-m-d H:i:s') && $forced_date === false) {
                    // If it's not today set  the current time to 00:00
                    $current_time = '00:00';
                } else {
                    $current_time = date('H:i', strtotime($date));
                }
                // Get the picking limit for the picking day based on weekdays
                $picking_limit = $picking_limits[date('N', strtotime($date)) - 1];
                if (strtotime($current_time) > strtotime($picking_limit)) {
                    $date = date('Y-m-d', strtotime($date . ' ' . $add_days . ' days')) . ' 00:00:00';
                    // Check if next day is a holiday
                    $date = $this->checkHolidays($date, $inverse);
                }
            }
            $weekday = date('N', strtotime($date));
            if ($pattern[$weekday - 1] != 1) {
                $nextday = ($weekday - 1) % 7;
                while ($pattern[$nextday] != 1) {
                    ++$add;
                    $date = date('Y-m-d', strtotime($date . ' ' . $add_days . ' days')) . ' 00:00:00';
                    $tmp_date = $date;
                    $date = $this->checkHolidays($date, $inverse);
                    if ($tmp_date == $date) {
                        $nextday = ($nextday + $add_days) % 7;
                    } else {
                        $nextday = (date('N', strtotime($date)) - 1) % 7;
                    }
                }
            }
            if ($return == 'date') {
                return $date;
            } else {
                return $add;
            }
        } else {
            return false;
        }
    }

    public function calculatePickingFromOrder($order, $df)
    {
        $return = [];

        if (!Configuration::get('ed_picking_adv')) {
            $return['picking_limit'] = json_decode(Configuration::get('ed_picking_limit'), true);
            $return['picking_days'] = Configuration::get('ed_picking_days');
        } else {
            $sql = 'SELECT picking_days, picking_limit, position FROM ' . _DB_PREFIX_ . 'orders LEFT JOIN ' . _DB_PREFIX_ . 'carrier AS c USING (id_carrier) LEFT JOIN ' . _DB_PREFIX_ . 'ed_carriers AS ed USING (id_reference) WHERE id_order = ' . (int) $order->id . ' ' . Shop::addSqlRestriction(false, 'ed');
            $row = Db::getInstance()->getRow(pSQL($sql));
            if ($row === false) {
                return false;
            }
            $return['picking_limit'] = json_decode($row['picking_limit'], true);
            $return['picking_days'] = $row['picking_days'];
        }
        if (Tools::strlen($return['picking_days']) != 7) {
            $return['picking_limit'] = '1111100';
        }
        $return['picking_day'] = $this->checkNext('picking', date('Y-m-d H:i:s'), $return['picking_days'], $return['picking_limit']);
        $return['picking_limit'] = $return['picking_limit'][date('N', strtotime($return['picking_day'])) - 1];
        $return['picking_day'] = EDTools::setDateFormatForED($return['picking_day'], $df);

        return $return;
    }

    public static function validateDate($date, $format = 'Y-m-d')
    {
        if (is_string($date) && $date != '' && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}/', $date)) {
            $d = DateTime::createFromFormat($format, $date);

            // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits
            // changing the comparison from == to === fixes the issue.
            return $d && $d->format($format) === $date;
        }

        return false;
    }

    public static function setForceDate($date)
    {
        if (self::validateDate($date)) {
            self::$force_date = $date;
        }
    }

    public static function isFutureDate($date)
    {
        if (self::validateDate($date)) {
            if (self::$force_date !== false) {
                $d1 = new DateTime(self::$force_date);
            } else {
                $d1 = new DateTime('now');
            }
            $d2 = DateTime::createFromFormat('Y-m-d', $date);

            return $d1 <= $d2;
        }

        return false;
    }

    /* Removes OOS products if they aren't allowed by module configuration */
    public static function SetOrCleanOOS($dps)
    {
        $return = [];
        if (is_array($dps) && count($dps) > 0) {
            $cnt = count($dps);
            for ($i = 0; $i < $cnt; ++$i) {
                //                if ($_SERVER['REMOTE_ADDR'] == '93.176.132.123') {
                //                    Tools::dieObject($dps);
                //                    var_dump(array($dps[$i]->isOOS), $dps[$i]->canOOS);
                //                }
                if (!($dps[$i]->isOOS && !$dps[$i]->canOOS)) {
                    $return[] = $dps[$i];
                }
            }
        }

        return $return;
    }

    /** Get the longer delivery date possible
     * If the Product Details is enabled return all the deliveries grouped by carrier
     *
     * @param $deliveries All product available deliveries
     * @param $relandavail Array with the release or available dates, if set
     * @param $remove_oos Remove from the calculation the products out of stock
     *
     * @return The longest delivery date, optional can remove oos products from the calculation
     **/
    public static function filterDeliveries($deliveries, $remove_oos, $commonCarrierIds)
    {
        $individual_deliveries = Configuration::get('ED_DATES_BY_PRODUCT');
        $return = [];
        $total_deliveries = count($deliveries);
        for ($i = 0; $i < $total_deliveries; ++$i) {
            $product_deliveries = count($deliveries[$i]);
            for ($j = 0; $j < $product_deliveries; ++$j) {
                $item = $deliveries[$i][$j];
                if (is_object($item)) {
                    if (in_array($item->dc->id_carrier, $commonCarrierIds)) {
                        if ($remove_oos === false || ($remove_oos && $item->dp->isOOS === false)) {
                            if ($individual_deliveries) {
                                $return[$item->dc->id_carrier][] = $item;
                            } else {
                                if (!isset($return[$item->dc->id_carrier])) {
                                    $return[$item->dc->id_carrier] = $item;
                                } else {
                                    $return[$item->dc->id_carrier] = self::getMaxDelivery($return[$item->dc->id_carrier], $item);
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($individual_deliveries) {
            return self::sortDeliveriesByDate($return);
        }

        return $return;
    }

    /**
     * Gets the delivery list, sorts them in descending order and takes the longest delivery for each carrier
     * to build the output for the renderDelivery function
     *
     * @param $deliveries the deliveries array
     *
     * @return The deliveries ready to be printed
     */
    public static function groupIndividualDeliveriesForPacks($deliveries)
    {
        $deliveries = self::sortDeliveriesByDateDesc($deliveries);
        // Get the pack product ID (product page)
        // If id_product is not defined it means it's a product list
        if (Tools::getIsset('id_product')) {
            $id_product = (int) Tools::getValue('id_product');
        }

        $ret = [];
        // Transform the results to be able to output the Estimated Delivery on the Product Page
        foreach ($deliveries as $delivery_by_carrier) {
            $delivery = reset($delivery_by_carrier);
            if (isset($id_product)) {
                $delivery->id_product = $delivery->dp->id_product = $id_product;
                $delivery->dp->id_product_attribute = 0;
            }
            $ret[] = $delivery;
        }

        return $ret;
    }

    public static function getCommonCarriersFromDeliveries($deliveries, $clear = true)
    {
        if (Configuration::get('ED_DIS_COMMON_CARRIER_INTERSECTION')) {
            $clear = false;
        }
        $common = [];
        self::findCommonCarriersRecursive($deliveries, $common);

        if ($clear) {
            return self::filterCommonCarriers($common);
        } else {
            return empty($common) ? [] : call_user_func_array('array_merge', $common);
        }
    }

    private static function findCommonCarriersRecursive($deliveries, &$common)
    {
        foreach ($deliveries as $delivery) {
            if (is_object($delivery) && isset($delivery->dc->id_carrier)) {
                $common[] = self::getCarrierIds([$delivery]);
            } elseif (is_array($delivery)) {
                self::findCommonCarriersRecursive($delivery, $common);
            }
        }
    }

    private static function getCarrierIds($deliveries)
    {
        $carrierIds = [];
        foreach ($deliveries as $delivery) {
            if (isset($delivery->dc->id_carrier)) {
                $carrierIds[] = $delivery->dc->id_carrier;
            }
        }

        return $carrierIds;
    }

    private static function filterCommonCarriers($common)
    {
        if (empty($common)) {
            return [];
        }

        $base = array_pop($common);
        foreach ($common as $carriers) {
            $base = array_intersect($base, $carriers);
            if (empty($base)) {
                break; // No common carriers left, exit early.
            }
        }

        return $base;
    }

    public static function sortDeliveriesByDate($data)
    {
        foreach ($data as &$dates) {
            usort($dates, 'self::cmpByDate');
        }

        return $data;
    }

    /*
     * Used to sort the orders with individial delivery dates (by product)
     */
    public static function sortDeliveriesByDateArray($data)
    {
        usort($data, 'self::cmpByDateArray');

        return $data;
    }

    private static function sortDeliveriesByDateDesc($data)
    {
        foreach ($data as &$dates) {
            usort($dates, 'self::sortDateDesc');
        }

        return $data;
    }

    /*
     * Compare two deliveries, if id_product has been set then order by id_product too
     */
    private static function cmpByDateArray($a, $b)
    {
        if ($a['delivery_min'] < $b['delivery_min']) {
            return -1;
        } elseif ($a['delivery_min'] == $b['delivery_min']) {
            if (isset($a['id_product'])) {
                return $a['id_product'] < $b['id_product'] ? -1 : 1;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }

    private static function cmpByDate($a, $b)
    {
        return $a->delivery_cmp_min < $b->delivery_cmp_min ? -1 : 1;
    }

    private static function sortDateDesc($a, $b)
    {
        return $a->delivery_cmp_min < $b->delivery_cmp_min ? 1 : -1;
    }

    private static function getMaxDelivery($delivery, $tmp_delivery)
    {
        if (empty($delivery) || $delivery == '' || (!isset($delivery->delivery_cmp_min) && !$delivery->dp->is_undefined_delivery)) {
            return $tmp_delivery;
        }
        if ($delivery->dp->is_undefined_delivery) {
            return $delivery;
        }
        if ($tmp_delivery->dp->is_undefined_delivery) {
            return $tmp_delivery;
        }

        return $delivery->delivery_cmp_min >= $tmp_delivery->delivery_cmp_min ? $delivery : $tmp_delivery;
    }

    public static function filterEmpty($day)
    {
        // TODO review product combinations
        if (is_array($day) && count($day) > 0) {
            foreach ($day as $d) {
                if (!($d > 0)) {
                    return false;
                }
            }
        } else {
            if ($day == 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the date difference between two dates
     * If format is configured, return the specified format
     * For days add +1 day to make the date calculation work
     *
     * @param $date1
     * @param $date2
     * @param $format string optional
     *
     * @return string|DateTime
     */
    public static function getDateDiff($date1, $date2, $format = false)
    {
        if ($format == '%a' && date('Y-m-d', strtotime($date1)) == date('Y-m-d', strtotime($date2))) {
            return 0;
        }
        $date1 = new DateTime($date1);
        $diff = $date1->diff(new DateTime($date2));
        // echo "\n<br>".print_r($diff, true);
        // print_r($diff);
        if ($format) {
            return $diff->format($format) + ($format == '%a' ? 1 : 0);
        }

        return $diff;
    }

    public static function groupDeliveriesByCombi($deliveries)
    {
        $return = [];
        if (is_array($deliveries)) {
            foreach ($deliveries as $delivery) {
                $return[$delivery->id_product_attribute][] = $delivery;
            }
        } else {
            return $deliveries;
        }

        return $return;
    }

    public static function countOrderStates($id_order)
    {
        return Db::getInstance()->getValue('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'order_history WHERE id_order = ' . (int) $id_order);
    }

    public static function edIsDefinitive($id_order)
    {
        $sql = 'SELECT is_definitive FROM ' . _DB_PREFIX_ . 'ed_orders WHERE id_order = ' . (int) $id_order;

        return Db::getInstance()->getValue(pSQL($sql));
    }

    public function setPickingDays($days)
    {
        $this->picking_days = $days;
    }

    public function setPickingLimit($time)
    {
        $this->picking_limit = $time;
    }
}
