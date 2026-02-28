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

class RegularDateFormatter implements dateFormatter
{
    private $pattern;

    public function __construct($pattern)
    {
        $this->pattern = empty($pattern) ? 'Y-m-d' : $pattern;
    }

    public function format($date)
    {
        return date($this->pattern, strtotime($date));
    }
}
