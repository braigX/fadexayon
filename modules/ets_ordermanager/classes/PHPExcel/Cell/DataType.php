<?php
/**
 * 2007-2023 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2023 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

class PHPExcel_Cell_DataType
{
    /* Data types */
    const TYPE_STRING2  = 'str';
    const TYPE_STRING   = 's';
    const TYPE_FORMULA  = 'f';
    const TYPE_NUMERIC  = 'n';
    const TYPE_BOOL     = 'b';
    const TYPE_NULL     = 'null';
    const TYPE_INLINE   = 'inlineStr';
    const TYPE_ERROR    = 'e';

    /**
     * List of error codes
     *
     * @var array
     */
    private static $errorCodes = array(
        '#NULL!'  => 0,
        '#DIV/0!' => 1,
        '#VALUE!' => 2,
        '#REF!'   => 3,
        '#NAME?'  => 4,
        '#NUM!'   => 5,
        '#N/A'    => 6
    );

    /**
     * Get list of error codes
     *
     * @return array
     */
    public static function getErrorCodes()
    {
        return self::$errorCodes;
    }

    /**
     * DataType for value
     *
     * @deprecated  Replaced by PHPExcel_Cell_IValueBinder infrastructure, will be removed in version 1.8.0
     * @param       mixed  $pValue
     * @return      string
     */
    public static function dataTypeForValue($pValue = null)
    {
        return PHPExcel_Cell_DefaultValueBinder::dataTypeForValue($pValue);
    }

    /**
     * Check a string that it satisfies Excel requirements
     *
     * @param  mixed  Value to sanitize to an Excel string
     * @return mixed  Sanitized value
     */
    public static function checkString($pValue = null)
    {
        if ($pValue instanceof PHPExcel_RichText) {
            // TODO: Sanitize Rich-Text string (max. character count is 32,767)
            return $pValue;
        }

        // string must never be longer than 32,767 characters, truncate if necessary
        $pValue = PHPExcel_Shared_String::Substring($pValue, 0, 32767);

        // we require that newline is represented as "\n" in core, not as "\r\n" or "\r"
        $pValue = str_replace(array("\r\n", "\r"), "\n", $pValue);

        return $pValue;
    }

    /**
     * Check a value that it is a valid error code
     *
     * @param  mixed   Value to sanitize to an Excel error code
     * @return string  Sanitized value
     */
    public static function checkErrorCode($pValue = null)
    {
        $pValue = (string) $pValue;

        if (!array_key_exists($pValue, self::$errorCodes)) {
            $pValue = '#NULL!';
        }

        return $pValue;
    }
}
