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
if (!class_exists('PageCacheCache')) {
    abstract class PageCacheCache
    {
        /**
         * @param $moduleInstance Jprestaspeedpack
         *
         * @return void
         */
        public function checkInstall($moduleInstance)
        {
            // Do nothing by default
        }

        /**
         * @param $moduleInstance Jprestaspeedpack
         *
         * @return void
         */
        public function install($moduleInstance)
        {
            // Do nothing by default
        }

        /**
         * @param $moduleInstance Jprestaspeedpack
         *
         * @return void
         */
        public function uninstall($moduleInstance)
        {
            // Do nothing by default
        }

        /**
         * @param $url string URL cached
         * @param $contextKey string Context key
         * @param $ttl int Time to live
         *
         * @return mixed
         */
        abstract public function get($url, $contextKey, $ttl = 0);

        /**
         * @param $url string URL cached
         * @param $contextKey string Context key
         * @param $value
         * @param $ttl int Time to live
         */
        abstract public function set($url, $contextKey, $value, $ttl = 0);

        /**
         * @param $url string URL cached
         * @param $contextKey string Context key
         *
         * @return bool true if OK, false if the key has not been completly deleted
         */
        abstract public function delete($url, $contextKey);

        /**
         * @param int $timeoutSeconds Maximum number of second to spend in flush
         *
         * @return bool true if OK, false if the cache has not been completly deleted
         */
        abstract public function flush($timeoutSeconds = 0);

        /**
         * Delete useless datas
         *
         * @param int $timeoutSeconds Maximum number of second to spend in purge
         *
         * @return void
         */
        abstract public function purge($timeoutSeconds = 0);

        /**
         * @return bool true if the jpresta_cache_context cookie should be set
         */
        public function needsContextCookie()
        {
            return false;
        }

        /**
         * @param $contextKey string
         *
         * @return bool
         */
        public static function isValidContextKey($contextKey)
        {
            return preg_match('/^[0-9a-fA-F\-]+$/', $contextKey) === 1;
        }
    }
}
