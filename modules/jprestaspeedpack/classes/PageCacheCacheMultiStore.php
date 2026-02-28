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
if (!class_exists('PageCacheCacheMultiStore')) {
    class PageCacheCacheMultiStore extends PageCacheCache
    {
        /**
         * @var PageCacheCache[]
         */
        private $caches = [];

        /**
         * @param $cache PageCacheCache Cache to add
         *
         * @return void
         */
        public function addCache($cache)
        {
            $this->caches[] = $cache;
        }

        public function get($url, $contextKey, $ttl = 0)
        {
            // Should not be called
            foreach ($this->caches as $cache) {
                $value = $cache->get($url, $contextKey, $ttl);
                if ($value !== false) {
                    return $value;
                }
            }

            return false;
        }

        public function set($url, $contextKey, $value, $ttl = -1)
        {
            // Should not be called
            foreach ($this->caches as $cache) {
                $cache->set($url, $contextKey, $value, $ttl);
            }
        }

        public function delete($url, $contextKey)
        {
            $ret = true;
            foreach ($this->caches as $cache) {
                $ret = $ret && $cache->delete($url, $contextKey);
            }

            return $ret;
        }

        public function flush($timeoutSeconds = 0)
        {
            $ret = true;
            foreach ($this->caches as $cache) {
                $ret = $ret && $cache->flush($timeoutSeconds);
            }

            return $ret;
        }

        public function purge($timeoutSeconds = 0)
        {
            $startTime = microtime(true);
            foreach ($this->caches as $cache) {
                $cache->purge($timeoutSeconds - (microtime(true) - $startTime));
            }
        }

        public function needsContextCookie()
        {
            foreach ($this->caches as $cache) {
                if ($cache->needsContextCookie()) {
                    return true;
                }
            }

            return false;
        }
    }
}
