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

class IntlDateFormat extends dateFormat
{
    private $locale;
    private $date_type;
    private $time_type;
    private $pattern;
    private $timezone;

    public function __construct($format = null, $locale = null, $timezone = null)
    {
        $this->locale = $locale;
        $this->date_type = isset($format['date_type']) ? $format['date_type'] : null;
        $this->time_type = isset($format['time_type']) ? $format['time_type'] : null;
        $this->pattern = isset($format['pattern']) ? $format['pattern'] : null;
        $this->timezone = empty($timezone) ? null : $timezone;
    }

    public function getDateFormatter()
    {
        if (!isset($this->formatter)) {
            $this->setFormatter(new EDIntlDateFormatter(
                $this->locale,
                $this->date_type,
                $this->time_type,
                $this->timezone,
                $this->pattern
            ));
        }
    }
}
