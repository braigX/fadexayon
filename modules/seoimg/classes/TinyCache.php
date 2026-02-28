<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
class TinyCache
{
    protected static $path;

    /** Set time to live in hour */
    const TIMEEXPIRE_LANG = 24;

    /**
     * Set cache path
     *
     * @param string $path
     *
     * @return void
     */
    public static function setPath($path)
    {
        self::$path = $path;
    }

    /**
     * Get cache path
     *
     * @return sting
     */
    public static function getPath()
    {
        return self::$path;
    }

    /**
     * Get TTL
     *
     * @param string $name
     * @param int $ttl
     * @param string $type
     *
     * @return sting
     */
    public static function getTTL($name, $ttl = 0, $type = '')
    {
        if ($ttl == 0) {
            $ttl = self::TIMEEXPIRE_LANG;
        }
        if (empty($type) || in_array($type, ['h', 'hour', 'hours'])) {
            $type = 'hours';
        } else {
            $type = 'minutes';
        }

        $d = strtotime('+ ' . (int) $ttl . ' ' . $type, @filemtime(self::$path . $name));

        return $name . ' ' . date('j/m/Y H:m:s', $d);
    }

    /**
     * Retrieve a data from cache
     *
     * @param string $name
     * @param int $ttl
     * @param string $type
     *
     * @return array
     */
    public static function getCache($name, $ttl = 0, $type = '')
    {
        if ($ttl == 0) {
            $ttl = self::TIMEEXPIRE_LANG;
        }
        if (empty($type) || in_array($type, ['h', 'hour', 'hours'])) {
            $type = 'hours';
        } else {
            $type = 'minutes';
        }

        if (Tools::file_exists_cache(self::$path . $name)) {
            @clearstatcache();
            $d = strtotime('+ ' . (int) $ttl . ' ' . $type, @filemtime(self::$path . $name));
            if (time() > $d) {
                self::clearCache($name);

                return false;
            }

            return self::uncompressObject(Tools::file_get_contents(self::$path . $name));
        }

        return false;
    }

    /**
     * Store a data in cache
     *
     * @param string $name
     * @param mixed $data
     *
     * @return bool
     */
    public static function setCache($name, $data)
    {
        return @file_put_contents(self::$path . $name, self::compressObject($data));
    }

    /**
     * Delete all cache
     */
    public static function clearAllCache()
    {
        $is_dot = ['.', '..'];
        if (is_dir(self::$path)) {
            if (version_compare((float) phpversion(), '5.3', '<')) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator(self::$path),
                    RecursiveIteratorIterator::SELF_FIRST
                );
            } else {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator(self::$path, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );
            }

            foreach ($iterator as $file) {
                if (version_compare((float) phpversion(), '5.2.17', '<=')) {
                    if (in_array($file->getBasename(), $is_dot)) {
                        continue;
                    }
                } elseif (version_compare((float) phpversion(), '5.3', '<')) {
                    if ($file->isDot()) {
                        continue;
                    }
                }
                if ($file->getBasename() !== 'index.php') {
                    unlink($file->getPathname());
                }
            }
            unset($iterator, $file);
        }
    }

    /**
     * Delete a data in cache
     *
     * @param string $name
     *
     * @return bool
     */
    public static function clearCache($name)
    {
        if (Tools::file_exists_cache(self::$path . $name)) {
            return @unlink(self::$path . $name);
        }
    }

    /**
     * Compress a data in cache
     *
     * @param mixed $string_array
     *
     * @return mixed
     */
    public static function compressObject($string_array)
    {
        $base_encode = 'base64_encode';

        return strtr($base_encode(addslashes(gzcompress(serialize($string_array), 9))), '+/=', '-_,');
    }

    /**
     * Uncompress a data in cache
     *
     * @param mixed $string_array
     *
     * @return mixed
     */
    public static function uncompressObject($string_array)
    {
        $base_decode = 'base64_decode';
        $strip_slashes = 'stripslashes';

        return unserialize(@gzuncompress($strip_slashes($base_decode(strtr($string_array, '-_,', '+/=')))));
    }
}
