<?php
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015-2018
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

abstract class dateFormat
{
    private static $today_date;
    private static $tomorrow_date;
    protected $formatter;

    abstract public function getDateFormatter();

    public function format($date)
    {
        if (Estimateddelivery::getTot() && $tot = $this->getTot($date)) {
            return $tot;
        }
        if (!isset($this->formatter)) {
            $this->getDateFormatter();
        }

        //        echo 'Formatting date '.$date.'<br>';
        return $this->formatter->format($date);
    }

    public function getFormatter()
    {
        return $this->formatter;
    }

    public function setFormatter($formatter)
    {
        $this->formatter = $formatter;
    }

    private function getTot($date)
    {
        //        $days_diff = EDTools::getDaysDiff($date);
        //        switch($days_diff) {
        //            case 0:
        //                if (!isset(self::$today_date)) {
        //                    self::$today_date = date('Y-m-d');
        //                }
        //        }
        $tmpdate = date('Y-m-d', strtotime($date));
        if (!isset(self::$today_date) || !isset(self::$tomorrow_date)) {
            self::$today_date = date('Y-m-d');
            self::$tomorrow_date = date('Y-m-d', strtotime('+1 day'));
        }
        if ($tmpdate == self::$today_date) {
            return EstimatedDelivery::getToday();
        }
        if ($tmpdate == self::$tomorrow_date) {
            return EstimatedDelivery::getTomorrow();
        }

        return false;
    }
}
