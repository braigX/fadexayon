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
if (!class_exists('PageCacheCacheStatic')) {
    class PageCacheCacheStatic extends PageCacheCache
    {
        protected $extension;

        private $dir;

        private $log;

        public function __construct($dir, $log = false)
        {
            $this->dir = $dir;
            $this->log = $log;
            $this->extension = '.gz';
        }

        /**
         * @param $moduleInstance ModuleCore
         *
         * @return string
         */
        private function getOutputScript($moduleInstance)
        {
            return _PS_MODULE_DIR_ . $moduleInstance->name . DIRECTORY_SEPARATOR . 'static.config.php';
        }

        /**
         * @param $moduleInstance ModuleCore
         *
         * @return string
         */
        private function getTemplateScript($moduleInstance)
        {
            return _PS_MODULE_DIR_ . $moduleInstance->name . DIRECTORY_SEPARATOR . 'vendor/static.config.php.tpl';
        }

        public function checkInstall($moduleInstance)
        {
            parent::checkInstall($moduleInstance);
            if (!file_exists($this->getOutputScript($moduleInstance))) {
                $this->install($moduleInstance);
            }
        }

        public function install($moduleInstance)
        {
            parent::install($moduleInstance);
            // PathTraversal : Here it is secured because getTemplateScript() is secured
            $content = Tools::file_get_contents($this->getTemplateScript($moduleInstance));
            file_put_contents($this->getOutputScript($moduleInstance), str_replace(
                ['%PS_ROOT_DIR%', '%DEBUG_MODE%', '%ALWAYS_DISPLAY_INFOS%', '%MODULE_PATH%', '%CACHE_DIR%', '%PCU_HEADER%', '%IGNORED_PARAMS%', '%EXPIRES_MIN%', '%EXCLUDED_REFERERS%'],
                [
                    _PS_ROOT_DIR_,
                    Configuration::get('pagecache_debug') ? 'true' : 'false',
                    Configuration::get('pagecache_always_infosbox') ? 'true' : 'false',
                    '\'' . _PS_MODULE_DIR_ . $moduleInstance->name . '/\'',
                    '\'' . $this->dir . '/\'',
                    '\'' . $moduleInstance::HTTP_HEADER_CACHE_INFO . '\'',
                    '\'' . Configuration::get('pagecache_ignored_params') . '\'',
                    (int) Configuration::get('pagecache_static_expires'),
                    '\'' . JprestaUtils::getConfigurationAllShop('pagecache_ignore_referers') . '\'',
                ],
                $content));
        }

        public function uninstall($moduleInstance)
        {
            parent::uninstall($moduleInstance);
            // PathTraversal : Here it is secured because getOutputScript() is secured
            JprestaUtils::deleteFile($this->getOutputScript($moduleInstance));
        }

        /**
         * @param $contextKey string
         */
        public function displayCacheIfAvailable($contextKey)
        {
            $key = md5($_SERVER['REQUEST_URI']);
            $filepath = $this->getFilePathFromKeyAndContextKey($key, $contextKey);
            if ($filepath !== false && file_exists($filepath)) {
                // PathTraversal : Here $filepath is secured because getFilePathFromKeyAndContextKey() is secured
                echo Tools::file_get_contents($filepath);
                exit;
            }
        }

        private function storeContent($filepath, $content)
        {
            return file_put_contents($filepath, gzcompress($content, 7));
        }

        private function readContent($filepath)
        {
            // PathTraversal : Here $filepath is secured
            return gzuncompress(Tools::file_get_contents($filepath));
        }

        public static function isCompatible()
        {
            // Always compatible
            return true;
        }

        /**
         * @param $url string URL of the page
         * @param $contextKey string Context key of the cache
         *
         * @return string File path of the cache
         */
        private function getFilePath($url, $contextKey)
        {
            return $this->getFilePathFromKeyAndContextKey(md5($url), $contextKey);
        }

        /**
         * @param $key string Key of the cache
         * @param $contextKey string Context key of the cache
         *
         * @return string|bool File path of the cache or false if an error occured
         */
        private function getFilePathFromKeyAndContextKey($key, $contextKey)
        {
            if (!$contextKey) {
                // The context probably does not exist anymore
                return false;
            }
            if (!self::isValidContextKey($contextKey)) {
                JprestaUtils::addLog('PageCache | Invalid context key: ' . $contextKey, 2);

                return false;
            }
            $subdir = $this->dir . DIRECTORY_SEPARATOR . $contextKey;
            for ($i = 0; $i < min(3, JprestaUtils::strlen($key)); ++$i) {
                $subdir .= DIRECTORY_SEPARATOR . $key[$i];
            }

            return $subdir . DIRECTORY_SEPARATOR . $key . $this->extension;
        }

        public function get($url, $contextKey, $ttl = -1)
        {
            if (!$contextKey) {
                // The context probably does not exist anymore
                return false;
            }
            if (!self::isValidContextKey($contextKey)) {
                JprestaUtils::addLog('PageCache | Invalid context key: ' . $contextKey, 2);

                return false;
            }

            $cache_file = $this->getFilePath($url, $contextKey);
            $filemtime = @filemtime($cache_file);
            if ($filemtime && ($ttl < 0 or (microtime(true) - $filemtime < $ttl))) {
                return $this->readContent($cache_file);
            }

            return false;
        }

        public function set($url, $contextKey, $value, $ttl = -1)
        {
            if (!$contextKey) {
                // The context probably does not exist anymore
                return false;
            }
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
                            if ('mkdir(): File exists' != $mkdirErrorArray['message']) {
                                JprestaUtils::addLog('PageCache | Cannot create directory ' . $cache_dir . " with grants $grants: " . $mkdirErrorArray['message'] . " (ttl=$ttl)", 3);
                            }
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
                        $now = date('d/m/Y H:i:s');
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

        public function needsContextCookie()
        {
            return true;
        }
    }
}
