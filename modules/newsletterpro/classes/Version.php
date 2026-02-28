<?php
/**
* Since 2013 Ovidiu Cimpean.
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
*
* @version   Release: 4
*/

namespace PQNP;

if (!defined('_PS_VERSION_')) {
	exit;
}


class Version
{
    public static function isEqual($version)
    {
        return version_compare($version, _PS_VERSION_, '==');
    }

    public static function isLower($version)
    {
        return version_compare($version, _PS_VERSION_, '>');
    }

    public static function isHigher($version)
    {
        return !(self::isLower($version));
    }

    public static function compare($va, $vb, $sign)
    {
        return version_compare($va, $vb, $sign);
    }

    public static function isBetween($lower_version, $higher_version)
    {
        return !Version::isLower($lower_version) && Version::isLower($higher_version);
    }
}
