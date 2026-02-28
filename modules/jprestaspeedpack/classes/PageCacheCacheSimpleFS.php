<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

// Check existence of the class to be able to handle compatibility problems in a friendly way
if (!class_exists('PageCacheCacheSimpleFS')) {
    class PageCacheCacheSimpleFS extends PageCacheCache
    {
        protected $extension;

        private $dir;

        private $log;

        public function __construct($dir, $log = false)
        {
            $this->dir = $dir;
            $this->log = $log;
            $this->extension = '.htm';
        }

        protected function storeContent($filepath, $content)
        {
            return file_put_contents($filepath, $content);
        }

        protected function readContent($filepath)
        {
            return Tools::file_get_contents($filepath);
        }

        public static function isCompatible()
        {
            // Always compatible
            return true;
        }

        /**
         * @param $url
         * @param $contextKey
         *
         * @return false|string
         */
        private function getFilePath($url, $contextKey)
        {
            if (!self::isValidContextKey($contextKey)) {
                JprestaUtils::addLog('PageCache | Invalid context key: ' . $contextKey, 2);

                return false;
            }
            $key = md5($url . $contextKey);
            $subdir = $this->dir;
            for ($i = 0; $i < min(3, JprestaUtils::strlen($key)); ++$i) {
                $subdir .= '/' . $key[$i];
            }
            $cache_file = $subdir . '/' . $key . $this->extension;

            return $cache_file;
        }

        public function get($url, $contextKey, $ttl = -1)
        {
            $cache_file = $this->getFilePath($url, $contextKey);
            if ($cache_file) {
                $filemtime = @filemtime($cache_file);
                if ($filemtime && ($ttl < 0 or (microtime(true) - $filemtime < $ttl))) {
                    return $this->readContent($cache_file);
                }
            }

            return false;
        }

        public function set($url, $contextKey, $value, $ttl = -1)
        {
            if (!self::isValidContextKey($contextKey)) {
                JprestaUtils::addLog('PageCache | Invalid context key: ' . $contextKey, 2);

                return;
            }

            $cache_file = $this->getFilePath($url, $contextKey);
            $cache_dir = dirname($cache_file);

            if (!file_exists($cache_dir)) {
                // Creates subdirectory with 777 to be sure it will work
                $grants = 0777;
                if (!@mkdir($cache_dir, $grants, true)) {
                    $mkdirErrorArray = error_get_last();
                    clearstatcache();
                    if (!file_exists($cache_dir)) {
                        if ($mkdirErrorArray !== null) {
                            JprestaUtils::addLog('PageCache | Cannot create directory ' . $cache_dir . " with grants $grants: " . $mkdirErrorArray['message'] . " (ttl=$ttl)", 3);
                        } else {
                            JprestaUtils::addLog('PageCache | Cannot create directory ' . $cache_dir . " with grants $grants (ttl=$ttl)", 3);
                        }
                    }
                }
            }

            $write_ok = $this->storeContent($cache_file, $value);
            if ($write_ok === false) {
                $mkdirErrorArray = error_get_last();
                if ($mkdirErrorArray !== null) {
                    JprestaUtils::addLog("PageCache | Cannot write file $cache_file: " . $mkdirErrorArray['message'], 3);
                } else {
                    JprestaUtils::addLog("PageCache | Cannot write file $cache_file", 3);
                }
            } else {
                if ($this->log) {
                    // Log debug
                    $exists = file_exists($cache_file) ? 'true' : 'false';
                    $date_infos = '';
                    if (file_exists($cache_file)) {
                        $now = date('d/m/Y H:i:s', microtime(true));
                        $last_date = date('d/m/Y H:i:s', filemtime($cache_file));
                        $date_infos = "now=$now file=$last_date";
                    }
                    JprestaUtils::addLog("PageCache | cached | cache_file=$cache_file exists=$exists $date_infos", 1, null,
                        null, null, true);
                }
            }
            if ($write_ok !== false) {
                @chmod($cache_file, 0666);
            }
        }

        public function delete($url, $contextKey)
        {
            $cache_file = $this->getFilePath($url, $contextKey);

            return JprestaUtils::deleteFile($cache_file);
        }

        public function flush($timeoutSeconds = 0)
        {
            if (!JprestaUtils::deleteDirectory($this->dir, $timeoutSeconds)) {
                $obsoleteDirName = realpath($this->dir) . '_' . time() . '_please_delete_me';

                return JprestaUtils::rename(realpath($this->dir), $obsoleteDirName);
            }

            return true;
        }

        public function purge($timeoutSeconds = 0)
        {
            $startTime = microtime(true);
            // Get all directories matching the pattern "*_please_delete_me"
            $directories = glob(realpath($this->dir) . '_*_please_delete_me', GLOB_ONLYDIR);

            // Loop through each directory and delete it
            foreach ($directories as $directory) {
                if ((microtime(true) - $startTime) >= $timeoutSeconds) {
                    break;
                }
                // Recursively delete the directory and its contents
                JprestaUtils::deleteDirectory($directory, $timeoutSeconds - (microtime(true) - $startTime));
            }
        }
    }
}
