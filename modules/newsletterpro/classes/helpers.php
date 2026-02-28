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

if (!defined('_PS_VERSION_')) {
	exit;
}

use PQNP\Config;

if (!function_exists('pqnp_log')) {
    function pqnp_log()
    {
        return NewsletterProLog::newInstance();
    }
}

if (!function_exists('pqnp_config')) {
    function pqnp_config($name = null, $value = null, $write = false)
    {
        return Config::config($name, $value, $write);
    }
}

if (!function_exists('pqnp_config_safe')) {
    function pqnp_config_safe($name = null, $value = null, $write = false)
    {
        return Config::configSafe($name, $value, $write);
    }
}

if (!function_exists('pqnp_config_get')) {
    function pqnp_config_get($name = null, $defaultValue = null, $safe = true)
    {
        return Config::get($name, $defaultValue, $safe);
    }
}

if (!function_exists('pqnp_dconfig')) {
    function pqnp_dconfig($name = null, $value = null, $write = false)
    {
        return Config::delete($name);
    }
}

if (!function_exists('pqnp_ini_config')) {
    function pqnp_ini_config($name)
    {
        return NewsletterPro::getInstance()->ini_config[$name];
    }
}

if (!function_exists('pqnp_demo_mode')) {
    function pqnp_demo_mode($name)
    {
        return NewsletterPro::getInstance()->demo_mode[$name];
    }
}

if (!function_exists('pqnp_addcslashes')) {
    function pqnp_addcslashes($str)
    {
        return NewsletterProTools::addCShashes($str);
    }
}

if (!function_exists('pqnp_template_path')) {
    function pqnp_template_path($path)
    {
        return NewsletterProTools::getTemplatePath($path);
    }
}

if (!function_exists('pqnp_module')) {
    function pqnp_module()
    {
        return NewsletterPro::getInstance();
    }
}

if (!function_exists('vdp')) {
    function vdp($data)
    {
        var_dump($data);
    }
}

if (!function_exists('npp')) {
    function npp($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

if (!function_exists('vdd')) {
    function vdd($data)
    {
        vdp($data);
        exit;
    }
}

if (!function_exists('npd')) {
    function npd($data)
    {
        echo '<pre>';
        print_r($data);
        echo "\n\nEND\n";
        echo '</pre>';
        exit;
    }
}

if (!function_exists('pqp')) {
    function pqp()
    {
        header('Content-Type: text/html');

        $args = func_get_args();
        $argc = count($args);
        $types = ['string', 'integer', 'double', 'boolean', 'NULL'];
        echo '<pre>';
        echo "\n";

        foreach ($args as $key => $value) {
            $type = gettype($value);
            if (in_array($type, $types)) {
                if ($key + 1 < $argc && in_array(gettype($args[$key + 1]), $types)) {
                    echo $value.', ';
                } else {
                    echo $value;
                }
            } else {
                echo "\n";
                print_r($value);
            }
        }

        echo "\n";
        echo '</pre>';
    }
}

if (!function_exists('pqd')) {
    function pqd()
    {
        header('Content-Type: text/html');

        $args = func_get_args();
        $argc = count($args);
        $types = ['string', 'integer', 'double', 'boolean', 'NULL'];
        echo '<pre>';
        echo "\n";

        foreach ($args as $key => $value) {
            $type = gettype($value);
            if (in_array($type, $types)) {
                if ($key + 1 < $argc && in_array(gettype($args[$key + 1]), $types)) {
                    echo $value.', ';
                } else {
                    echo $value;
                }
            } else {
                echo "\n";
                print_r($value);
            }
        }

        echo "\n";
        echo "\n";
        echo 'END';
        echo "\n";
        echo '</pre>';
        exit;
    }
}

if (!function_exists('dd')) {
    function dd()
    {
        $args = func_get_args();
        call_user_func_array('pqd', $args);
    }
}

if (!function_exists('vd')) {
    function vd()
    {
        $args = func_get_args();
        $argsDump = [];
        foreach ($args as $ar) {
            $argsDump[] = var_dump($ar, true);
        }
        call_user_func_array('pqd', $argsDump);
    }
}
