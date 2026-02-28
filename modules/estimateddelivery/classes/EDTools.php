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

class EDTools
{
    // df >> date format
    // protected static $locale;
    protected static $base_df;
    protected static $special_df;
    protected static $weekday_df;
    protected static $email_df;
    protected static $product_listing_df;

    public static function validEncoding($text)
    {
        if (empty($text)) {
            return false;
        }
        $valid_encodings = ['UCS-4', ' UCS-4BE', ' UCS-4LE', ' UCS-2', ' UCS-2BE', ' UCS-2LE', ' UTF-32', ' UTF-32BE', ' UTF-32LE', ' UTF-16', ' UTF-16BE', ' UTF-16LE', ' UTF-7', ' UTF7-IMAP', ' UTF-8', ' ASCII', ' EUC-JP', ' SJIS', ' eucJP-win', ' SJIS-win', ' ISO-2022-JP', ' ISO-2022-JP-MS', ' CP932', ' CP51932', ' SJIS-mac', 'MacJapanese', ' SJIS-Mobile#DOCOMO', 'SJIS-DOCOMO', ' SJIS-Mobile#KDDI', 'SJIS-KDDI', ' SJIS-Mobile#SOFTBANK', 'SJIS-SOFTBANK', ' UTF-8-Mobile#DOCOMO', 'UTF-8-DOCOMO', ' UTF-8-Mobile#KDDI-A', ' UTF-8-Mobile#KDDI-B', 'UTF-8-KDDI', ' UTF-8-Mobile#SOFTBANK', 'UTF-8-SOFTBANK', ' ISO-2022-JP-MOBILE#KDDI', 'ISO-2022-JP-KDDI', ' JIS', ' JIS-ms', ' CP50220', ' CP50220raw', ' CP50221', ' CP50222', ' ISO-8859-1', ' ISO-8859-2', ' ISO-8859-3', ' ISO-8859-4', ' ISO-8859-5', ' ISO-8859-6', ' ISO-8859-7', ' ISO-8859-8', ' ISO-8859-9', ' ISO-8859-10', ' ISO-8859-13', ' ISO-8859-14', ' ISO-8859-15', ' ISO-8859-16', ' byte2be', ' byte2le', ' byte4be', ' byte4le', ' BASE64', ' HTML-ENTITIES', 'HTML', ' 7bit', ' 8bit', ' EUC-CN', ' CP936', ' GB18030', ' HZ', ' EUC-TW', ' CP950', ' BIG-5', ' EUC-KR', ' UHC', 'CP949', ' ISO-2022-KR', ' Windows-1251', 'CP1251', ' Windows-1252', 'CP1252', ' CP866', 'IBM866', ' KOI8-R', ' KOI8-U', ' ArmSCII-8', 'ArmSCII8'];

        return in_array($text, $valid_encodings);
    }

    /**
     * Creates a date format for regular dates or Intl ones to later use with only the format method
     *
     * @param $type String The target property
     * @param $format Array With the data about the format
     *
     * @return EDIntlDateFormat|RegularDateFormat|false
     */
    public static function setDateFormat($type, $format, $locale = null, $timezone = false)
    {
        if (property_exists('EDTools', $type)) {
            if (!isset(self::${$type})) {
                self::${$type} = self::createDateFormat($format, $locale, $timezone);
            }

            return self::${$type};
        }

        return false;
    }

    public static function createDateFormat($format, $locale = null, $timezone = null)
    {
        if (isset($format['intl'])) {
            return new IntlDateFormat($format, $locale, $timezone);
        } else {
            return new RegularDateFormat(isset($format['pattern']) ? $format['pattern'] : 'Y-m-d');
        }
    }

    /**
     * Recovers a date formatted if it has already been set
     *
     * @param $df stored date formatter
     */
    public static function getDateFormat($df)
    {
        if (!is_string($df)) {
            return $df;
        }
        if (strpos($df, '_df') !== false
        && isset(self::${$df})) {
            return self::${$df};
        }
    }

    public static function setDateFormatForED($date, $df)
    {
        if (is_string($df)) {
            $df = self::getDateFormat($df);
        }
        if ($df === false) {
            return $date;
        }

        return $df->format($date);
    }

    public static function getDaysDiff($date)
    {
        //        $date = new DateTime($date);
        //        $today = new DateTime();
        // Set time to 00:00 to get the correct number of day diff
        //        $date->setTime(0, 0);
        //        $today->setTime(0, 0);
        // Return the number of days
        //        return $today->diff($date)->format('%a');
        $date = date('Y-m-d', strtotime($date)) . ' 00:00:00';
        $today = date('Y-m-d') . ' 00:00:00';

        // -1 to remove the additional day generated for display purposes
        return DeliveryHelper::getDateDiff($date, $today, '%a') - 1;
    }

    public static function getImageFormattedName($name)
    {
        if (method_exists('ImageType', 'getFormatedName')) {
            // Old method call, can't remove it
            return ImageType::getFormatedName($name);
        } else {
            return ImageType::getFormattedName($name);
        }
    }

    public static function formatDateForOrderList($date, $module, $palete_name, $id_order = 0)
    {
        $context = Context::getContext();
        $new_date = DateTimeImmutable::createFromFormat($context->language->date_format_lite, $date);
        if ($new_date !== false) {
            $today = new DateTime();
            $interval = $today->diff($new_date);
            $days = $interval->format('%r%a');
        } else {
            if ($date == $module->l('Undefined')) {
                $days = null;
            } else {
                return $date;
            }
        }
        $params = self::setDateColor($days, $module, $palete_name);

        if ($date == $module->l('Undefined') && $id_order > 0) {
            $order = new Order((int) $id_order);

            // Undefined Date with link to date establishment
            return SmartForm::genDesc($date, [['a', 'href="https://' . $context->shop->domain_ssl . $context->link->getAdminLink('AdminOrders', true, ['id_order' => $order->id, 'vieworder' => 1]) . '#estimateddeliveryTabContent"'], ['span', $params]]);
        } else {
            // Print the date and format it accordingly to the preferred format in the current language displayed
            return SmartForm::genDesc($new_date->format($context->language->date_format_lite), ['span', $params]);
        }
    }

    private static function setDateColor($days, $module, $palete_name)
    {
        $data_title = 'data-original-title';
        // Picking Palete
        switch (true) {
            case $days === null:
                $params = 'ed-orange" ' . $data_title . '="' . $module->l('No delivery date configured or an undefined delivery. Set up the Estimated Date inside the order details') . '"';
                break;
            case $days > 4:
                if ($palete_name == 'delivery') {
                    $params = 'ed-dark-green" ' . $data_title . '="' . sprintf($module->l('There are still some days until the %s date'), $palete_name) . '"';
                    break;
                }
                // no break
            case $days > 2:
                $params = 'ed-green" ' . $data_title . '="' . sprintf($module->l('There are still some days until the %s date'), $palete_name) . '"';
                break;
            case $days == 2:
                $params = 'ed-light-green" ' . $data_title . '="' . sprintf($module->l('2 days until the %s date'), $palete_name) . '"';
                break;
            case $days == 1:
                $params = 'ed-yellow" ' . $data_title . '="' . sprintf($module->l('1 day until the %s date'), $palete_name) . '"';
                break;
            case $days == 0:
                if ($palete_name == 'picking') {
                    $msg = $module->l('Today this order must be sent');
                } else {
                    $msg = $module->l('Order should be arriving by today! If you still haven\'t sent it send a message to your customer to avoid complains');
                }
                $params = 'ed-orange" ' . $data_title . '="' . $msg . '"';
                break;
            case $days < 0:
                $params = 'ed-red" ' . $data_title . '="' . sprintf($module->l('%s date already passed'), $palete_name) . '"';
                break;
        }
        $params = 'class="picking_date ' . $params . ' data-toggle="pstooltip" data-placement="top"';
        // Delivery Palete

        return $params;
    }

    /**
     * Adds the selected date to a backup array, just in case the cookie gets deleted before the order validation
     * Can update the previously saved date
     * Adding the timestamp when creating the date to allow date cleaning for abandoned carts
     *
     * @param $id_cart int the id of the cart
     * @param $date string the selected date
     */
    public static function addPendingCalendarDate($id_cart, $date)
    {
        $context = Context::getContext();
        if ($context->cart->id != $id_cart || (int) $id_cart == 0) {
            // Avoid adding dates for other carts or arbitrary dates
            return false;
        }
        $dates = EDTools::getPendingCalendarDates();
        $dates[$id_cart] = [$date, date('Y-m-d H:i:s')];
        Configuration::updateValue('ED_PENDING_CALENDAR_DATE', json_encode($dates));
    }

    /**
     * Gets the pending calendar dates from the Configuration,
     * if the id_cart is provided it will return the date for that cart
     * it will return false if the date is not stored
     *
     * @param $id_cart
     *
     * @return false|mixed
     */
    public static function getPendingCalendarDates($id_cart = false)
    {
        $dates = json_decode(Configuration::get('ED_PENDING_CALENDAR_DATE'), true);

        return $id_cart ? ($dates[$id_cart] ?? false) : $dates;
    }

    public static function fixDate($delivery_min, $delivey_cmp_min)
    {
        $lang = Context::getContext()->language;

        switch ($lang->iso_code) {
            case 'pl':
                $delivery_min = self::fixPolishDate($delivery_min, $delivey_cmp_min);
                break;
            case 'cz':
                $delivery_min = self::fixCzechDate($delivery_min, $delivey_cmp_min);
                break;
            case 'sk':
                $delivery_min = self::fixSlovakDate($delivery_min, $delivey_cmp_min);
                break;
        }

        return $delivery_min;
    }

    public static function fixPolishDate($delivery_min, $delivey_cmp_min)
    {
        // Polish uses "we" before some weekdays that start with a consonant cluster
        $dayOfWeek = date('N', strtotime($delivey_cmp_min));

        // Only Tuesday (Wtorek) and Wednesday (Środa) use "we" in Polish
        if ($dayOfWeek == 2 || $dayOfWeek == 3) {
            $delivery_min = str_replace('w w', 'we w', $delivery_min);
        }

        return $delivery_min;
    }

    public static function fixCzechDate($delivery_min, $delivey_cmp_min)
    {
        $dayOfWeek = date('N', strtotime($delivey_cmp_min));

        // Only Wednesday (Středa) and Thursday (Čtvrtek) use "ve" instead of "v" in Czech
        if ($dayOfWeek == 3 || $dayOfWeek == 4) {
            $delivery_min = str_replace('v', 've', $delivery_min);
        }

        return $delivery_min;
    }

    public static function fixSlovakDate($delivery_min, $delivey_cmp_min)
    {
        $dayOfWeek = date('N', strtotime($delivey_cmp_min));

        // Only Thursday (Štvrtok) uses "vo" instead of "v" in Slovak
        if ($dayOfWeek == 4) {
            $delivery_min = str_replace('v', 'vo', $delivery_min);
        }

        return $delivery_min;
    }

    public static function getControllerName()
    {
        $context = Context::getContext();
        if (Tools::getIsset('controller') && Tools::getValue('controller') !== '') {
            return Tools::getValue('controller');
        } elseif (isset($context->controller->php_self) && $context->controller->php_self != '') {
            return $context->controller->php_self;
        } elseif (isset($context->controller->controller_name) && $context->controller->controller_name != '') {
            return $context->controller->controller_name;
        } elseif (isset($context->controller->module->name) && $context->controller->module->name != '') {
            return $context->controller->module->name;
        } elseif (Tools::getIsset('ed_update_type')) {
            return 'EDOrderUpdate';
        } else {
            return '';
        }
    }
}
