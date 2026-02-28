<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2019 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PSWPCache
{
    protected static $cache_enabled = true;

    public static function set($cache_id, $content)
    {
        if (!self::$cache_enabled) {
            return false;
        }

        $filename = md5($cache_id);

        $cache_dir = _PS_MODULE_DIR_ . 'prestawp/cache/';
        if (is_writable($cache_dir)) {
            file_put_contents($cache_dir . $filename, $content);

            Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'prestawp_cache`
                 (`cache_id`, `filename`, `datetime`)
                 VALUES
                 ("' . pSQL($cache_id) . '", "' . pSQL($filename) . '", NOW())
                 ON DUPLICATE KEY UPDATE
                  `filename` = "' . pSQL($filename) . '",
                  `datetime` = NOW()'
            );
        }
    }

    public static function get($cache_id, $lifetime)
    {
        if (!self::$cache_enabled) {
            return false;
        }

        $filename = Db::getInstance()->getValue(
            'SELECT `filename`
             FROM `' . _DB_PREFIX_ . 'prestawp_cache`
             WHERE `cache_id` = "' . pSQL($cache_id) . '"
              AND `datetime` >= (NOW() - INTERVAL ' . (int) $lifetime . ' MINUTE)'
        );

        $cache_dir = _PS_MODULE_DIR_ . 'prestawp/cache/';
        if ($filename && file_exists($cache_dir . $filename)) {
            $content = Tools::file_get_contents($cache_dir . $filename);

            return $content;
        }

        return false;
    }

    /**
     * @param $cache_id string
     * @param $lifetime int minutes
     *
     * @return string|bool
     */
    public static function isStored($cache_id, $lifetime)
    {
        if (!self::$cache_enabled) {
            return false;
        }

        $filename = Db::getInstance()->getValue(
            'SELECT `filename`
             FROM `' . _DB_PREFIX_ . 'prestawp_cache`
             WHERE `cache_id` = "' . pSQL($cache_id) . '"
              AND `datetime` >= (NOW() - INTERVAL ' . (int) $lifetime . ' MINUTE)'
        );

        $cache_dir = _PS_MODULE_DIR_ . 'prestawp/cache/';
        if ($filename && file_exists($cache_dir . $filename)) {
            return true;
        }
    }
}
