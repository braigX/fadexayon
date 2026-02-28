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


use Configuration;
use Exception;
use ImageType;
use NewsletterPro;
use NewsletterProSubscriptionHook;
use NewsletterProTools;
use PQNP\Upgrade\Upgrade500;

class Config
{
    const NAME = 'NEWSLETTER_PRO';
    const DECODE_ERROR_LIMIT = 20;
    const EVENT_CONFIG_SET = 'EVENT_CONFIG_SET';

    protected static $initialized = false;

    protected static $configuration;

    protected static $ps_configuration_keys_cache;

    public static function defaultConfig($key = null)
    {
        $data = [
            'NEWSLETTER_TEMPLATE' => 'sample.html',
            'PRODUCT_TEMPLATE' => 'sample.html',
            'IMAGE_TYPE' => (
                method_exists('ImageType', 'getFormattedName')
                ? ImageType::getFormattedName('home')
                : (Version::isLower('1.5.1.0')
                    ? 'home'
                    : 'home'.'_default')
            ),
            'SLEEP' => '3',
            'FWD_FEATURE_ACTIVE' => '1',
            'CURRENCY' => (int) Configuration::get('PS_CURRENCY_DEFAULT'),
            'LANG' => (int) Configuration::get('PS_LANG_DEFAULT'),
            'CATEGORIES_DEPTH' => Version::isLower('1.5.0.5') ? 1 : 2,
            'DISPLAY_PRODUCT_IMAGE' => '1',
            'VIEW_ACTIVE_ONLY' => '1',
            'SUBSCRIBE_BY_CATEGORY' => '1',
            'SEND_NEWSLETTER_ON_SUBSCRIBE' => '0',
            'SMTP_ACTIVE' => '0',
            'SMTP' => '0',
            'DEBUG_MODE' => '0',
            'GOOGLE_ANALYTICS_ID' => '',
            'TOKEN' => NewsletterProTools::module()->token,
            'GOOGLE_ANALYTICS_ACTIVE' => '0',
            'CAMPAIGN_ACTIVE' => '0',
            'CAMPAIGN' => NewsletterProTools::module()->default_campaign_params,
            'PRODUCT_LINK_REWRITE' => (int) Configuration::get('PS_REWRITING_SETTINGS'),
            'ONLY_ACTIVE_PRODUCTS' => '1',
            'GOOGLE_UNIVERSAL_ANALYTICS_ACTIVE' => '0',
            'CONVERT_CSS_TO_INLINE_STYLE' => '1',
            // subscription feature options
            'SUBSCRIPTION_SECURE_SUBSCRIBE' => '1',
            'SUBSCRIPTION_ACTIVE' => '0',
            'LEFT_MENU_ACTIVE' => '1',
            'CROSS_TYPE_CLASS' => 'np-icon-cross_5',
            // send limit settings
            'SEND_METHOD' => NewsletterPro::SEND_METHOD_DEFAULT, // 0 -  Default, 1 - Anti flood. The values needs to be changed from the file init.js SEND_METHOD_DEFAULT and SEND_METHOD_ANTIFLOOD
            'SEND_ANTIFLOOD_ACTIVE' => '1',
            'SEND_ANTIFLOOD_EMAILS' => '100', // emails
            'SEND_ANTIFLOOD_SLEEP' => '10', // seconds

            'SEND_THROTTLER_ACTIVE' => '0',
            'SEND_THROTTLER_LIMIT' => '100',
            'SEND_THROTTLER_TYPE' => NewsletterPro::SEND_THROTTLER_TYPE_EMAILS, // 0 - Emails, 1 - MB. The values needs to be changed from the file init.js SEND_THROTTLER_TYPE_EMAILS and SEND_THROTTLER_TYPE_MB

            'PAGE_HEADER_TOOLBAR' => [
                'CSV' => 1,
                'MANAGE_IMAGES' => 1,
                'SELECT_PRODUCTS' => 1,
                'CREATE_TEMPLATE' => 1,
                'SEND_NEWSLETTERS' => 1,
                'TASK' => 1,
                'HISTORY' => 1,
                'STATISTICS' => 1,
                'CAMPAIGN' => 0,
                'SMTP' => 1,
                'MAILCHIMP' => 0,
                'FORWARD' => 0,
                'FRONT_SUBSCRIPTION' => 1,
                'SETTINGS' => 0,
                'TUTORIALS' => 1,
            ],
            'SHOW_CLEAR_CACHE' => 0,
            'SEND_EMBEDED_IMAGES' => 0,
            'SHOW_CUSTOM_COLUMNS' => [],
            'CHIMP_SYNC_UNSUBSCRIBED' => 1,
            'SEND_LIMIT_END_SCRIPT' => 100,
            'LAST_DATE_NEWSLETTER_BLOCK_SYNC' => '0000-00-00 00:00:00',
            'CUSTOMER_SUBSCRIBE_BY_LOI' => '1',
            'CUSTOMER_ACCOUNT_SUBSCRIBE_BY_LOI' => false,
            'DISPLYA_MY_ACCOUNT_NP_SETTINGS' => '1',
            'DEV_MODE' => '1',
            'LOAD_MINIFIED' => true,
            'SUBSCRIPTION_HOOK_POPUP_TYPE' => array_fill_keys(NewsletterProSubscriptionHook::getHooksUpper(), 0),
            'SUBSCRIPTION_CONTROLLER_ENABLED' => false,
            'SUBSCRIPTION_CONTROLLER_TEMPLATE_ID' => 0,
            'EMAIL_MIME_TEXT' => true,
            'TASK_MEMORY_CHECK_ENABLED' => true,
            'CAMPAIGN' => [
                'UTM_SOURCE' => 'Newsletter',
                'UTM_MEDIUM' => 'email',
                'UTM_CAMPAIGN' => '{newsletter_title}',
                'UTM_CONTENT' => '{product_name}',
            ],
            'CHIMP' => [
                'INSTALLED' => false,
                'API_KYE' => '',
                'ID_LIST' => '',
                'ID_GROUPING' => '',
                'CUSTOMERS_GROUP_IDS' => [],
                'FIELDS' => [],
                'CUSTOMERS_CHECKBOX' => 0,
                'VISITORS_CHECKBOX' => 0,
                'ADDED_CHECKBOX' => 0,
                'ORDERS_CHECKBOX' => 0,
            ],
            'CSV' => [
                /*
                * if the opton is false, you can import emails that already exists into ps_customer into the table ps_newsletter_pro_email, when you are importing a csv file
                */
                'IMPORT_STRICT' => true,
            ],
            'ENABLE_NEWSLETTER_SUBSCRIPTION_PAGE' => false,
        ];

        if (is_null($key)) {
            return $data;
        }

        if (!array_key_exists($key, $data)) {
            throw new Exception(sprintf('Invalid get default configuration key [%s].', $key));
        }

        return $data[$key];
    }

    public static function psConfigutationKeys()
    {
        if (!isset(self::$ps_configuration_keys_cache)) {
            self::$ps_configuration_keys_cache = array_keys(self::psDefault());
        }

        return self::$ps_configuration_keys_cache;
    }

    public static function psDefault()
    {
        return [
            'PS_SHOP_EMAIL' => Configuration::get('PS_SHOP_EMAIL'),
            'PS_MAIL_METHOD' => Configuration::get('PS_MAIL_METHOD'),
            'PS_CURRENCY_DEFAULT' => Configuration::get('PS_CURRENCY_DEFAULT'),
            'PS_LANG_DEFAULT' => Configuration::get('PS_LANG_DEFAULT'),
            'PS_SHOP_DEFAULT' => Configuration::get('PS_SHOP_DEFAULT'),
            'PS_MULTISHOP_FEATURE_ACTIVE' => Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE'),
            'PS_LOGO_MAIL' => Configuration::get('PS_LOGO_MAIL'),
            'PS_SHOP_NAME' => Configuration::get('PS_SHOP_NAME'),
            'PS_LOCALE_COUNTRY' => Configuration::get('PS_LOCALE_COUNTRY'),
            'PS_REWRITING_SETTINGS' => Configuration::get('PS_REWRITING_SETTINGS'),
        ];
    }

    public static function install()
    {
        return Configuration::updateValue(Config::NAME, json_encode(Config::defaultConfig()), false, 0, 0);
    }

    public static function init()
    {
        // this will convert the serizlized configuration into the json_encode configuration and also will solve te serialized bug
        if (version_compare(NewsletterProTools::getDbVersion(), '5.0.0', '<')) {
            if (false == (bool) Configuration::get('NEWSLETTER_PRO_CONFIGURATION_CALL')) {
                Configuration::updateValue('NEWSLETTER_PRO_CONFIGURATION_CALL', true, false, 0, 0);
                (new Upgrade500())->call();
            }
        }

        $configuration_str = Configuration::get(Config::NAME);

        $configuration = json_decode($configuration_str, true);

        // this if for the installation proccess, will load the default configuration
        if (!Configuration::get(Config::NAME, null, null, null)) {
            $configuration = self::defaultConfig();
        } elseif (!is_array($configuration) || (is_array($configuration) && count($configuration) < self::DECODE_ERROR_LIMIT)) {
            throw new Exception('Unable the load the module configuration.');
        }

        $ps_configuration = self::psDefault();

        $configuration = array_merge($configuration, $ps_configuration);

        self::$configuration = $configuration;
        self::$initialized = true;
    }

    public static function get($path = null, $defaultValue = null, $safe = false)
    {
        if (!self::$initialized) {
            self::init();
        }

        $data = self::$configuration;

        if (!isset($path)) {
            return $data;
        }

        $parts = explode('.', $path);
        $part_data = null;

        if (count($parts) > 1) {
            foreach ($parts as $part) {
                if (!isset($part_data)) {
                    $part_data = $data;
                }
                if (!array_key_exists($part, $part_data)) {
                    if (true == $safe) {
                        return $defaultValue;
                    }
                    throw new Exception(sprintf('The module configuration [%s] does not exists into the database.', $path));
                }
                $part_data = $part_data[$part];
            }

            return $part_data;
        } else {
            if (!array_key_exists($path, $data)) {
                if (true == $safe) {
                    return $defaultValue;
                }
                throw new Exception(sprintf('The module configuration [%s] does not exists into the database.', $path));
            }

            return $data[$path];
        }
    }

    public static function write($path, $value, $safe = false)
    {
        return self::set($path, $value, true, $safe);
    }

    public static function set($path, $value, $write = false, $safe = false)
    {
        if (!self::$initialized) {
            self::init();
        }

        Event::notify(Config::EVENT_CONFIG_SET, [
            'path' => $path,
            'value' => $value,
        ]);

        $data = &self::$configuration;

        $parts = explode('.', $path);

        $part_data = null;
        $count_parts = count($parts);

        if ($count_parts > 0) {
            foreach ($parts as $index => $part) {
                if (!isset($part_data)) {
                    $part_data = &$data;
                }

                if (!$write && !array_key_exists($part, $part_data)) {
                    if ($safe) {
                        return false;
                    }
                    throw new Exception(sprintf('The module configuration [%s] does not exists into the database to be able to set it.', $path));
                }

                if ($index < $count_parts - 1) {
                    $part_data = &$part_data[$part];
                }
            }
            $part_data[$part] = $value;
        } else {
            if (!$write && !array_key_exists($path, $data)) {
                if ($safe) {
                    return false;
                }
                throw new Exception(sprintf('The module configuration [%s] does not exists into the database to be able to set it.', $path));
            }

            $data[$path] = $value;
        }

        $tmp = $data;
        foreach (self::psConfigutationKeys() as $key) {
            if (array_key_exists($key, $tmp)) {
                unset($tmp[$key]);
            }
        }

        if (count($tmp) < self::DECODE_ERROR_LIMIT) {
            throw new Exception(sprintf('Unable to write the configuration. Configuration data is less than %s.', self::DECODE_ERROR_LIMIT));
        }

        if (!Configuration::updateValue(Config::NAME, json_encode($tmp), false, 0, 0)) {
            if ($safe) {
                return false;
            }
            throw new Exception(sprintf('Unable to write the configuration [%s] into database.', $path));
        }

        return true;
    }

    public static function delete($name)
    {
        if (!self::$initialized) {
            self::init();
        }

        $data = &self::$configuration;

        if (array_key_exists($name, $data)) {
            unset($data[$name]);
        }

        if (false !== strpos($name, '.')) {
            throw new Exception(sprintf('The dot notation deletion is not supported for [%s].', $name));
        }

        if (!Configuration::updateValue(Config::NAME, json_encode($data), false, 0, 0)) {
            throw new Exception(sprintf('Unable to write the configuration [%s] into database.', $name));
        }

        return true;
    }

    public static function config($path = null, $value = null, $write = false)
    {
        if (isset($value)) {
            return self::set($path, $value, $write);
        }

        return self::get($path);
    }

    public static function configSafe($path = null, $value = null, $write = false)
    {
        if (isset($value)) {
            return self::set($path, $value, $write, true);
        }

        return self::get($path, null, true);
    }
}
