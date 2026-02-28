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

class EDIntlDateFormatter implements dateFormatter
{
    private static $locale;
    private static $special_encoding;
    private $formatter;

    public function __construct($locale = null, $date_type = null, $time_type = null, $timezone = null, $pattern = null)
    {
        if (is_null($locale)) {
            if (!isset(self::$locale)) {
                $this->getCurrentLocale();
            }
            $locale = self::$locale;
        }
        if (is_null($date_type) || $date_type == 'LONG') {
            $date_type = IntlDateFormatter::FULL;
        } elseif ($date_type == 'SHORT') {
            $date_type = IntlDateFormatter::SHORT;
        }
        if (is_null($time_type)) {
            $time_type = IntlDateFormatter::NONE;
        }

        //        $dateString = '2024-03-23';
        //        $date = DateTime::createFromFormat('Y-m-d', $dateString);
        // Tools::dieObject(array($locale, $date_type, $time_type, $timezone, null, $pattern));
        //        echo 'Formatter creation parameters. Locale > ' . $locale . ', Date type > ' . $date_type . ', Time Type > ' . $time_type . ', Timezone > ' . (is_null($timezone) ? 'null' : $timezone) . ', Pattern ' . $pattern . PHP_EOL;
        $this->formatter = new IntlDateFormatter($locale, $date_type, $time_type, $timezone, null, $pattern);
        if (intl_get_error_code() != 0) {
            $this->formatter = new IntlDateFormatter(str_replace('-', '_', $locale), $date_type, $time_type, $timezone, null, $pattern);
            // Try the locale by replacing the minus (-) for an undescore (_)
            // $this->formatter = datefmt_create(str_replace('-', '_', $locale), $date_type, $time_type, $timezone, null, $pattern);
        }
        //        echo 'Formatter creation parameters. Locale > ' . str_replace('-', '_', $locale) . ', Date type > ' . $date_type . ', Time Type > ' . $time_type . ', Timezone > ' . (is_null($timezone) ? 'null' : $timezone) . ', Pattern ' . $pattern . PHP_EOL;
    }

    public function getFormatter()
    {
        // Tools::dieObject($this->formatter->format('09/02/2023'));
        return $this->formatter;
    }

    public function format($date)
    {
        $date = $this->formatter->format(new DateTime($date));
        if (function_exists('mb_convert_encoding')) {
            $date = mb_convert_encoding($date, 'UTF-8');
        }
        if (!isset(self::$special_encoding)) {
            $this->setSpecialEncoding();
        }
        if ($this->needsSpecialEncoding($date)) {
            $date = $this->applySpecialEncoding($date);
        }

        if (Context::getContext()->language->iso_code == 'pl') {
            $date = $this->fixPolishDateFormat($date);
        }

        // Return & Remove possible double-spaces on the custom date format
        return str_replace('  ', ' ', $date);
    }

    private function getCurrentLocale()
    {
        if (!isset(self::$locale)) {
            $context = Context::getContext();
            self::$locale =
                isset($context->language->locale) ? $context->language->locale : $context->language->language_code;
        }

        return self::$locale;
    }

    private function setSpecialEncoding()
    {
        $special_encoding = Configuration::get('ED_SPECIAL_ENCODING');
        if (!EDTools::validEncoding($special_encoding)) {
            $special_encoding = '';
        }
        self::$special_encoding = $special_encoding;
    }

    private function needsSpecialEncoding($date)
    {
        if (self::$special_encoding === '') {
            return false;
        }
        $enc = mb_detect_encoding($date, self::$special_encoding . ', UTF-8', true);

        return $enc !== self::$special_encoding;
    }

    private function applySpecialEncoding($date)
    {
        return mb_convert_encoding($date, 'UTF-8', self::$special_encoding);
    }

    private function fixPolishDateFormat($date)
    {
        // Create 2 arrays, one for the search and another for the replacement for the weekdays in Polish one in the present tense and the other for the future one
        $search = [
            'Poniedziałek',
            'Wtrorek',
            'Środa',
            'Czwartek',
            'Piątek',
            'Sobota',
            'Niedziela',
            'poniedziałek',
            'wtrorek',
            'środa',
            'czwartek',
            'piątek',
            'sobota',
            'niedziela',
        ];
        $replace = [
            'Poniedziałek',
            'Wtorek',
            'Środę',
            'Czwartek',
            'Piątek',
            'Sobotę',
            'Niedzielę',
            'poniedziałek',
            'wtorek',
            'środę',
            'czwartek',
            'piątek',
            'sobotę',
            'niedzielę',
        ];

        return str_replace($search, $replace, $date);
    }
}
