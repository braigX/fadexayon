<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

// Check existence of the class to be able to handle compatibility problems in a friendly way
if (!class_exists('JprestaCacheKey')) {
    if (!defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
        // PHP < 7.2 Define it as 0 so it does nothing
        define('JSON_INVALID_UTF8_SUBSTITUTE', 0);
    }

    class JprestaCacheKey
    {
        /**
         * This public only to be able to encode in JSON, you must use functions
         *
         * @var int CRC32 as an unsigned integer
         */
        public $key_int;

        /**
         * This public only to be able to encode in JSON, you must use functions
         *
         * @var string CRC32 as a string compatible with filename
         */
        public $key_string;

        /**
         * This public only to be able to encode in JSON, you must use functions
         *
         * @var array All informations used to compute the cache key
         */
        public $infos = [];

        /**
         * @var array Informations relative to the cache but ignored in key computation
         */
        public $infos_ignored = [];

        /**
         * @param $key
         * @param $value
         * @param bool $ignoreInKey If true it will be ignored in key computation
         */
        public function add($key, $value, $ignoreInKey = false)
        {
            $this->key_int = null;
            $this->key_string = null;
            if ($ignoreInKey) {
                $this->infos_ignored[$key] = $value;
            } else {
                $this->infos[$key] = $value;
            }
        }

        /**
         * @param $key string Key used with @add function
         * @param null $default
         *
         * @return mixed|null
         */
        public function get($key, $default = null)
        {
            if ($key && array_key_exists($key, $this->infos)) {
                return $this->infos[$key];
            } elseif ($key && array_key_exists($key, $this->infos_ignored)) {
                return $this->infos_ignored[$key];
            }

            return $default;
        }

        /**
         * @param $key string Key used with @add function
         */
        public function remove($key)
        {
            if ($key && array_key_exists($key, $this->infos)) {
                unset($this->infos[$key]);
                $this->key_int = null;
                $this->key_string = null;
            }
        }

        /**
         * @return $this
         */
        public function compute()
        {
            if ($this->key_int === null || $this->key_string == null) {
                // Make sure information are in the same order
                ksort($this->infos);
                // Create a unique string
                $str = json_encode($this->infos, JSON_INVALID_UTF8_SUBSTITUTE);
                // Compute CRC32 to be used by caching systems (as string)
                $this->key_string = hash('crc32b', $str);
                // Compute CRC32 to be stored in database (as integer)
                $this->key_int = (int) hexdec($this->key_string);
            }

            return $this;
        }

        public function toInt()
        {
            $this->compute();

            return $this->key_int;
        }

        public function toString()
        {
            $this->compute();

            return $this->key_string;
        }

        /**
         * @param $keyInt double
         *
         * @return string
         */
        public static function intToString($keyInt)
        {
            return sprintf('%08x', (float) $keyInt);
        }

        public function getInfos()
        {
            return $this->infos;
        }
    }
}
