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

use Tools;

class Path
{
    public static function join()
    {
        $args = func_get_args();
        $count = count($args);
        $output = [];

        foreach ($args as $key => $value) {
            if ($key < $count - 1) {
                $len = Tools::strlen($value);
                $output[$key] = DIRECTORY_SEPARATOR !== Tools::substr($value, $len - 1, $len) ? $value.DIRECTORY_SEPARATOR : $value;
            } else {
                $output[$key] = $value;
            }
        }

        return join('', $output);
    }
}
