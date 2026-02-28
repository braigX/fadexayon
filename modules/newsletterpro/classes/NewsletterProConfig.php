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

class NewsletterProConfig
{
    public static $table_name = 'newsletter_pro_config';

    public static $errors = [];

    public static $module;

    public static function get($name)
    {
        $sql = 'SELECT `value` FROM `'._DB_PREFIX_.'newsletter_pro_config` WHERE `name` = "'.pSQL($name).'"';

        return Db::getInstance()->getValue($sql);
    }

    public static function save($name, $value)
    {
        $sql = 'SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_config` WHERE `name` = "'.pSQL($name).'"';
        if (Db::getInstance()->getValue($sql)) {
            return Db::getInstance()->update('newsletter_pro_config', [
                'value' => pSQL($value),
            ], '`name` = "'.pSQL($name).'"');
        } else {
            return Db::getInstance()->insert('newsletter_pro_config', [
                'name' => pSQL($name),
                'value' => pSQL($value),
            ]);
        }
    }

    public static function test($name)
    {
        $sql = 'SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_config` WHERE `name` = "'.$name.'"';

        return Db::getInstance()->getValue($sql);
    }

    public static function saveArray($name, $array)
    {
        $value = serialize($array);

        return self::save($name, $value);
    }

    public static function getArray($name)
    {
        $get = self::get($name);
        if ($get) {
            return NewsletterProTools::unSerialize($get);
        }

        return false;
    }

    public static function delete($name)
    {
        return Db::getInstance()->delete('newsletter_pro_config', '`name` = "'.pSQL($name).'"');
    }

    public static function install()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$table_name.'` (
				`id_newsletter_pro_config` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(50) NOT NULL,
				`value` TEXT NULL,
				PRIMARY KEY (`id_newsletter_pro_config`),
				UNIQUE INDEX `name` (`name`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET='._PQNP_MYSQL_CHARSET_.';';

        if (!Db::getInstance()->execute($sql)) {
            self::addError(sprintf(NewsletterPro::getInstance()->l('Cannot create the table "%s".'), _DB_PREFIX_.'newsletter_pro_config'));

            return false;
        } else {
            if (!self::test('CHIMP_SYNC')) {
                self::saveArray('CHIMP_SYNC', []);
            }

            if (!self::test('LAST_DATE_CHIMP_SYNC')) {
                self::save('LAST_DATE_CHIMP_SYNC', '0000-00-00 00:00:00');
            }

            if (!self::test('CHIMP_LAST_DATE_SYNC_ORDERS')) {
                self::save('CHIMP_LAST_DATE_SYNC_ORDERS', '0000-00-00 00:00:00');
            }
        }

        return true;
    }

    public static function uninstall()
    {
        $sql = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.self::$table_name.'`;';
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        return true;
    }

    public static function addError($error)
    {
        self::$errors[] = $error;
    }

    public static function getErrors()
    {
        return self::$errors;
    }

    public static function dbSerialize($value)
    {
        return serialize($value);
    }

    public static function unSerialize($serialized)
    {
        if (is_string($serialized) && preg_match('/a:[0-9]+:\{.*\}/', $serialized)) {
            return @unserialize($serialized);
        }

        return [];
    }
}
