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


class Event
{
    protected static $events = [];

    public static function listen($event_name, callable $callback)
    {
        if (!isset(self::$events[$event_name])) {
            self::$events[$event_name] = [];
        }

        self::$events[$event_name][] = $callback;
    }

    public static function detach($event_name)
    {
        if (array_key_exists($event_name, self::$events)) {
            unset(self::$events[$event_name]);
        }
    }

    public static function notify($event_name, $data)
    {
        if (array_key_exists($event_name, self::$events)) {
            foreach (self::$events[$event_name] as $callback) {
                call_user_func_array($callback, [$data]);
            }
        }
    }
}
