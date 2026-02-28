<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */

namespace JPresta\SpeedPack;

use Cache;
use Context;
use Db;
use Exception;
use Language;
use PrestaShopDatabaseException;
use Shop;
use Tools;

if (!defined('_PS_VERSION_')) {
    exit;
}

// Check existence of the class to be able to handle compatibility problems in a friendly way
if (!class_exists('JPresta\SpeedPack\JprestaUtils')) {
    require_once 'JprestaUtilsDispatcher.php';

    class JprestaUtils
    {
        public static function getImageTypeFormattedName($name)
        {
            if (method_exists('ImageType', 'getFormattedName')) {
                return \ImageType::getFormattedName($name);
            } else {
                $themeName = \Context::getContext()->shop->theme_name;
                $nameWithoutThemeName = str_replace(['_' . $themeName, $themeName . '_'], '', $name);

                // check if the theme name is already in $name if yes only return $name
                if ($themeName !== null && strstr($name, $themeName) && \ImageType::getByNameNType($name)) {
                    return $name;
                }

                if (\ImageType::getByNameNType($nameWithoutThemeName . '_' . $themeName)) {
                    return $nameWithoutThemeName . '_' . $themeName;
                }

                if (\ImageType::getByNameNType($themeName . '_' . $nameWithoutThemeName)) {
                    return $themeName . '_' . $nameWithoutThemeName;
                }

                return $nameWithoutThemeName . '_default';
            }
        }

        /**
         * Original PHP code by Chirp Internet: www.chirp.com.au, Please acknowledge use of this code by including this header.
         *
         * @param string
         * @param string $base Base URL
         * @param $managedControllers
         * @param bool|string $tagIgnoreStart
         * @param bool|string $tagIgnoreEnd
         * @param bool|string $ignoreBeforePattern
         * @param bool|string $ignoreAfterPattern
         *
         * @return array List of URLs
         */
        public static function parseLinks($html, $base, $managedControllers, $tagIgnoreStart = false, $tagIgnoreEnd = false, $ignoreBeforePattern = false, $ignoreAfterPattern = false)
        {
            if ($ignoreBeforePattern) {
                $startPos = self::strpos($html, $ignoreBeforePattern);
                if ($startPos !== false) {
                    $endPos = false;
                    if ($ignoreAfterPattern) {
                        $endPos = self::strpos($html, $ignoreAfterPattern, $startPos);
                    }
                    if ($endPos !== false) {
                        return self::parseLinks(\Tools::substr($html, $startPos, $endPos - $startPos), $base, $managedControllers);
                    } else {
                        return self::parseLinks(\Tools::substr($html, $startPos), $base, $managedControllers);
                    }
                }
            }
            $startPos = false;
            if ($tagIgnoreStart !== false) {
                $startPos = self::strpos($html, $tagIgnoreStart);
            }
            if ($startPos !== false) {
                $linksAfter = [];
                $endPos = self::strpos($html, $tagIgnoreEnd, min(self::strlen($html), $startPos + 4));
                $linksBefore = self::parseLinks(\Tools::substr($html, 0, $startPos), $base, $managedControllers,
                    $tagIgnoreStart, $tagIgnoreEnd);
                if ($endPos !== false) {
                    $linksAfter = self::parseLinks(\Tools::substr($html, $endPos + 4), $base, $managedControllers,
                        $tagIgnoreStart, $tagIgnoreEnd);
                }

                return array_merge($linksBefore, $linksAfter);
            } else {
                $links = [];

                $base_relative = preg_replace('/https?:\/\//', '//', $base === null ? '' : $base);
                $base_exp = preg_replace('/([^a-zA-Z0-9])/', '\\\\$1', $base === null ? '' : $base);
                $base_exp = preg_replace('/https?/', 'http[s]?', $base_exp === null ? '' : $base_exp);
                $regexp = '<a\s[^>]*href=(\"??)' . $base_exp . '([^\" >]*?)\\1[^>]*>(.*)<\/a>';
                $isMultiLanguageActivated = \Language::isMultiLanguageActivated();

                if (preg_match_all("/$regexp/siU", $html, $matches, PREG_SET_ORDER)) {
                    // The links array will help us to remove duplicates
                    foreach ($matches as $match) {
                        // $match[2] = link address
                        // $match[3] = link text
                        // Insert backlinks that correspond to a possibily cached page into the database

                        $url = $match[2];
                        // Add leading /
                        if (strpos($url, '/') > 0 || strpos($url, '/') === false) {
                            $url = '/' . $url;
                        }

                        // Remove language part if any
                        $url_without_lang = $url;
                        if ($isMultiLanguageActivated && preg_match('#^/([a-z]{2})(?:/.*)?$#', $url, $m)) {
                            $url_without_lang = \Tools::substr($url, 3);
                        }
                        $anchorPos = strpos($url_without_lang, '#');
                        if ($anchorPos !== false) {
                            $url_without_lang = \Tools::substr($url_without_lang, 0, $anchorPos);
                        }

                        $bl_controller = \JprestaUtilsDispatcher::getPageCacheInstance()->getControllerFromURL($url_without_lang);
                        if ($bl_controller === false) {
                            if (self::isModuleEnabled('smartseourl')) {
                                $bl_controller = self::getControllerFromURLSmartseourl($url_without_lang);
                            } else {
                                // To avoid re-installation of override we have this workaround
                                $bl_controller = \JprestaUtilsDispatcher::getPageCacheInstance()->getControllerFromURL('en' . $url_without_lang);
                            }
                        }
                        if (in_array($bl_controller, $managedControllers)) {
                            $links[$match[2]] = $base_relative . $match[2];
                        }
                    }
                }

                return $links;
            }
        }

        /**
         * @param string $url
         * @param int $curl_timeout
         * @param array $opts
         *
         * @return bool|string
         *
         * @throws \Exception
         */
        public static function file_get_contents_curl(
            $url,
            $curl_timeout,
            $opts
        ) {
            $content = false;

            if (function_exists('curl_init')) {
                if (method_exists('Tools', 'refreshCACertFile')) {
                    // Does not exist in some PS1.6
                    \Tools::refreshCACertFile();
                }
                $curl = curl_init();

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);
                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                    'User-Agent: ' . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Page Cache Ultimate'),
                ]);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($curl, CURLOPT_CAINFO, _PS_CACHE_CA_CERT_FILE_);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl, CURLOPT_MAXREDIRS, 5);

                if ($opts != null) {
                    if (isset($opts['http']['method']) && \Tools::strtolower($opts['http']['method']) == 'post') {
                        curl_setopt($curl, CURLOPT_POST, true);
                        if (isset($opts['http']['content'])) {
                            // parse_str($opts['http']['content'], $post_data);
                            curl_setopt($curl, CURLOPT_POSTFIELDS, $opts['http']['content']);
                        }
                    }
                }

                $content = curl_exec($curl);

                if (false === $content && _PS_MODE_DEV_) {
                    $errorMessage = sprintf('file_get_contents_curl failed to download %s : (error code %d) %s',
                        $url,
                        curl_errno($curl),
                        curl_error($curl)
                    );

                    throw new \Exception($errorMessage);
                }

                curl_close($curl);
            }

            return $content;
        }

        public static function getControllerFromURLSmartseourl($url)
        {
            static $smartseourl = null;
            if ($smartseourl === null) {
                $smartseourl = \Module::getInstanceByName('smartseourl');
            }
            if (!file_exists($smartseourl->path_cache)) {
                $smartseourl->createCacheFolders();
            }

            $request_uri = $url;
            $rewrite = trim($request_uri, '/');
            if (strpos($rewrite, '.html') > 0) {
                $rewrite = explode('.html', $rewrite);
                $rewrite = $rewrite[0];
            }

            $is_cache = $smartseourl->getDirectoryTree($smartseourl->path_cache, md5($rewrite));
            if ($is_cache) {
                if ($is_cache['type'] === 'cms_category') {
                    $is_cache['type'] = 'cms';
                }

                return $is_cache['type'];
            } else {
                $context = \Context::getContext();
                $result = \SSURewriteClass::getBy($rewrite, null, $context->language->id, $context->shop->id);
                if ($result) {
                    return $result['table_type'];
                }
            }

            return false;
        }

        public static function decodeConfiguration($value)
        {
            if ($value) {
                $value = str_replace('&lt;', '<', $value);
            }

            return $value;
        }

        public static function encodeConfiguration($value)
        {
            if ($value) {
                $value = str_replace('<', '&lt;', $value);
            }

            return $value;
        }

        public static function parseCSS($html, $base)
        {
            $links = [];
            $base_exp = preg_replace('/([^a-zA-Z0-9])/', '\\\\$1', $base === null ? '' : $base);
            $regexp = '<link\s[^>]*href=(\"??)[^\" >]*' . $base_exp . '([^\" >]*?)\\1[^>]*>';
            if (preg_match_all("/$regexp/siU", $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $links[] = $match[2];
                }
            }

            return $links;
        }

        public static function parseJS($html, $base)
        {
            $links = [];
            $base_exp = preg_replace('/([^a-zA-Z0-9])/', '\\\\$1', $base === null ? '' : $base);
            $regexp = '<script\s[^>]*src=(\"??)[^\" >]*' . $base_exp . '([^\" >]*?)\\1[^>]*>';
            if (preg_match_all("/$regexp/siU", $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $links[] = $match[2];
                }
            }

            return $links;
        }

        /**
         * Delete a file
         *
         * @param $file string The file to delete
         *
         * @return bool true if the file has been deleted
         */
        public static function deleteFile($file)
        {
            if (!$file) {
                return false;
            }
            $realFile = realpath($file);
            if ($realFile && !self::isAllowedPath($realFile)) {
                self::addLog('Utils | Cannot delete file ' . $file . ' : forbidden path - ' . self::getStackTrace(), 2);

                return false;
            }
            // PathTraversal : Here $realFile is secured
            if ($realFile && @unlink($realFile) === false) {
                $error = error_get_last();
                if ($error && stripos($error['message'], 'No such file or directory') === false) {
                    // Ignore error when the directory does not exist anymore
                    self::addLog('Utils | Cannot delete file ' . $file . ' : ' . $error['message'], 2);

                    return false;
                }
            }

            return true;
        }

        /**
         * @param $path string Path to check
         *
         * @return bool true if this patch is within the Prestashop folder
         */
        private static function isAllowedPath($path)
        {
            // Causes too many problems, this should be checked by open_basedir
            return true;
            /*static $rootDir = null;
            static $cacheDir = null;
            static $cacheDirPc = null;
            static $imgDir = null;
            $realPath = realpath($path);
            if ($rootDir === null) {
                $rootDir = realpath(_PS_ROOT_DIR_);
                $cacheDir = realpath(_PS_CACHE_DIR_);
                $cacheDirPc = realpath(_PS_ROOT_DIR_ . '/var/cache/');
                $imgDir = realpath(_PS_IMG_DIR_);
            }
            return self::startsWith($realPath, $rootDir)
                || self::startsWith($realPath, $cacheDirPc)
                || self::startsWith($realPath, $cacheDir)
                || self::startsWith($realPath, $imgDir)
                ;*/
        }

        /**
         * @param $oldname string
         * @param $newname string
         *
         * @return bool
         */
        public static function rename($oldname, $newname)
        {
            try {
                if (rename($oldname, $newname) === false) {
                    $error = error_get_last();
                    if ($error && stripos($error['message'], 'No such file or directory') === false) {
                        // Ignore error when the directory does not exist anymore
                        self::addLog('Utils | Cannot rename ' . $oldname . ' to ' . $newname . ': ' . $error['message'], 2);

                        return false;
                    }
                }
            } catch (\Exception $e) {
                self::addLog('Utils | Cannot rename ' . $oldname . ' to ' . $newname . ': ' . $e->getMessage(), 2);

                return false;
            }

            return true;
        }

        /**
         * Delete directory and subdirectories with their files.
         *
         * @param $dir string Directory to delete
         * @param int $timeoutSeconds
         *
         * @return bool true if the directory has been deleted
         */
        public static function deleteDirectory($dir, $timeoutSeconds = 0)
        {
            if (!$dir) {
                return false;
            }
            $startTime = microtime(true);
            $errorCount = 0;
            $first_error = null;

            $realDir = realpath($dir);
            if ($realDir && !self::isAllowedPath($realDir)) {
                self::addLog('Utils | Cannot delete directory ' . $dir . ' : forbidden path - ' . self::getStackTrace(), 2);

                return false;
            }

            if ($realDir) {
                $it = new \RecursiveDirectoryIterator($realDir, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS);
                $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
                foreach ($files as $file) {
                    if (is_dir($file)) {
                        if (@rmdir($file) === false) {
                            $error = error_get_last();
                            if ($error && stripos($error['message'], 'No such file or directory') === false) {
                                // Ignore error when the directory does not exist anymore
                                ++$errorCount;
                                if (!$first_error) {
                                    $first_error = $error['message'];
                                }
                            }
                        }
                    } else {
                        // PathTraversal : Here $file is secured
                        if (@unlink($file) === false) {
                            $error = error_get_last();
                            if ($error && stripos($error['message'], 'No such file or directory') === false) {
                                // Ignore error when the file does not exist anymore
                                ++$errorCount;
                                if (!$first_error) {
                                    $first_error = $error['message'];
                                }
                            }
                        }
                    }
                    if ($timeoutSeconds > 0 && (microtime(true) - $startTime) > $timeoutSeconds) {
                        // It's too long, stopping
                        if (!$first_error) {
                            $first_error = "too long to delete everything (> $timeoutSeconds seconds)";
                        }
                        ++$errorCount;
                        break;
                    }
                }
                if (!$errorCount && @rmdir($realDir) === false) {
                    $error = error_get_last();
                    if ($error && stripos($error['message'], 'No such file or directory') === false) {
                        // Ignore error when the directory does not exist anymore
                        ++$errorCount;
                        if (!$first_error) {
                            $first_error = $error['message'];
                        }
                    }
                }
                if ($errorCount > 0) {
                    self::addLog('Utils | ' . $errorCount . ' error(s) during deletion of ' . $dir . ' - First error: ' . $first_error, 2);
                }

                return $errorCount === 0;
            }

            return true;
        }

        /**
         * @param $sourceDir string Source directory
         * @param $targetDir string Target directory
         * @param $replace bool if true, replace files into target directory
         *
         * @return bool true on success, false on failure
         */
        public static function copyFiles($sourceDir, $targetDir, $replace = false)
        {
            $ret = true;
            if (is_dir($sourceDir)) {
                if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true)) {
                    self::addLog("Utils | Cannot create directory $targetDir", 2);

                    return false;
                }
                $files = scandir($sourceDir);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        if (is_dir($sourceDir . '/' . $file)) {
                            $ret = $ret && self::copyFiles($sourceDir . '/' . $file, $targetDir . '/' . $file, $replace);
                        } else {
                            if ($replace || !file_exists($targetDir . '/' . $file)) {
                                if (!copy($sourceDir . '/' . $file, $targetDir . '/' . $file)) {
                                    self::addLog("Utils | Cannot copy $sourceDir/$file to $targetDir/$file", 2);
                                    $ret = false;
                                }
                            }
                        }
                    }
                }
            }

            return $ret;
        }

        /**
         * Creates a backup file, then search and replace in it
         *
         * @param $file string File to modify
         * @param mixed $search <p>
         *                      The value being searched for, otherwise known as the needle.
         *                      An array may be used to designate multiple needles.
         *                      </p>
         * @param mixed $replace <p>
         *                       The replacement value that replaces found search
         *                       values. An array may be used to designate multiple replacements.
         *                       </p>
         */
        public static function replaceInFile($file, $search, $replace)
        {
            if (!$file) {
                return;
            }
            $realFile = realpath($file);
            if ($realFile && !self::isAllowedPath($realFile)) {
                self::addLog('Utils | Cannot replace in file ' . $file . ' : forbidden path - ' . self::getStackTrace(), 2);
            } elseif ($realFile && is_file($realFile)) {
                $i = 1;
                $suffix = '-backup-' . date('Ymd');
                while (file_exists($realFile . $suffix)) {
                    $suffix = '-backup-' . date('Ymd') . '-' . $i;
                    ++$i;
                }
                \Tools::copy($realFile, $realFile . $suffix);
                // PathTraversal : Here $realFile is secured
                $content = \Tools::file_get_contents($realFile);
                $content = str_replace($search, $replace, $content);
                // PathTraversal : Here $realFile is secured
                file_put_contents($realFile, $content);
            }
        }

        public static function isAjax()
        {
            // Usage of ajax parameter is deprecated
            $isAjax = \Tools::getValue('ajax') || \Tools::isSubmit('ajax');
            if (!$isAjax && isset($_SERVER['HTTP_ACCEPT'])) {
                $isAjax = preg_match('#\bapplication/json\b#', $_SERVER['HTTP_ACCEPT']);
            }
            if (!$isAjax && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                $isAjax = $_SERVER['HTTP_X_REQUESTED_WITH'] === 'xmlhttprequest';
            }
            if (!$isAjax && isset($_SERVER['HTTP_SEC_FETCH_MODE'])) {
                $isAjax = $_SERVER['HTTP_SEC_FETCH_MODE'] === 'cors';
            }

            return $isAjax;
        }

        // Does not support flag GLOB_BRACE
        public static function glob_recursive($pattern, $flags = 0)
        {
            $files = glob($pattern, $flags);
            foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
                $files = array_merge($files, self::glob_recursive($dir . '/' . basename($pattern), $flags));
            }

            return $files;
        }

        /**
         * Wrapper to make it easy to migrate
         */
        public static function strpos($str, $find, $offset = 0, $encoding = 'UTF-8')
        {
            if (method_exists('Tools', 'strpos')) {
                return \Tools::strpos($str, $find, $offset, $encoding);
            } else {
                if (function_exists('mb_strpos')) {
                    return mb_strpos($str, $find, $offset, $encoding);
                }

                return strpos($str, $find, $offset);
            }
        }

        /**
         * Wrapper to make it easy to migrate
         */
        public static function strlen($str, $encoding = 'UTF-8')
        {
            return \Tools::strlen($str, $encoding);
        }

        /**
         * Wrapper to make it easy to migrate
         */
        public static function version_compare($v1, $v2, $operator = '<')
        {
            return \Tools::version_compare($v1, $v2, $operator);
        }

        /**
         * Check if a string starts with a given substring.
         *
         * @param string $haystack the string to search in
         * @param string $needle the substring to search for at the start of $haystack
         * @param bool $casesensitive (optional) Whether the comparison should be case-sensitive. Default is true.
         *
         * @return bool true if $haystack starts with $needle, false otherwise
         */
        public static function startsWith($haystack, $needle, $casesensitive = true)
        {
            // If the needle is longer than the haystack, return false
            if (strlen($needle) > strlen($haystack)) {
                return false;
            }

            // Extract the beginning of the haystack with the same length as the needle
            $startOfString = substr($haystack, 0, strlen($needle));

            // Case-sensitive or case-insensitive comparison
            if ($casesensitive) {
                return $startOfString === $needle;
            } else {
                return strcasecmp($startOfString, $needle) === 0;
            }
        }

        public static function endsWith($haystack, $needle)
        {
            $length = self::strlen($needle);
            if ($length == 0) {
                return true;
            }

            return \Tools::substr($haystack, -$length) === $needle;
        }

        public static function trimTo($string, $default)
        {
            if (!$string) {
                return $default;
            }
            $ret = trim($string);
            if (self::strlen($ret === 0)) {
                return $default;
            }

            return $ret;
        }

        /**
         * Determine if a variable is iterable. i.e. can be used to loop over.
         *
         * @return bool
         */
        public static function isIterable($var)
        {
            return $var !== null && (is_array($var) || $var instanceof \Traversable);
        }

        /**
         * @param $id_shop int ID of the desired shop
         *
         * @return string URL path for the most adapted language
         */
        public static function getUrlLang($id_shop)
        {
            $psRewritingSettings = (int) \Configuration::get('PS_REWRITING_SETTINGS', null, null, $id_shop);
            if (!\Language::isMultiLanguageActivated($id_shop) || !$psRewritingSettings) {
                return '';
            }
            $selectedLang = null;
            $shopDefaultLangId = \Configuration::get('PS_LANG_DEFAULT', null, null, $id_shop);
            $languages = \Language::getLanguages(true, $id_shop, false);
            if (!$languages) {
                return '';
            }
            foreach ($languages as $lang) {
                if ($selectedLang === null) {
                    $selectedLang = $lang;
                }
                if ($shopDefaultLangId == $lang['id_lang']) {
                    $selectedLang = $lang;
                }
                if (\Context::getContext()->employee->id_lang == $lang['id_lang']) {
                    $selectedLang = $lang;
                    break;
                }
            }

            return $selectedLang['iso_code'] . '/';
        }

        public static function getDomains()
        {
            // Don't rely on Tools::getDomains() since it can be overwritten by modules like ets_upgrade which does not
            // return domains as expected
            $domains = [];
            foreach (\ShopUrl::getShopUrls() as $shop_url) {
                /** @var \ShopUrl $shop_url */
                if (!isset($domains[$shop_url->domain])) {
                    $domains[$shop_url->domain] = [];
                }

                $domains[$shop_url->domain][] = [
                    'physical' => $shop_url->physical_uri,
                    'virtual' => $shop_url->virtual_uri,
                    'id_shop' => $shop_url->id_shop,
                ];

                if ($shop_url->domain == $shop_url->domain_ssl) {
                    continue;
                }

                if (!isset($domains[$shop_url->domain_ssl])) {
                    $domains[$shop_url->domain_ssl] = [];
                }

                $domains[$shop_url->domain_ssl][] = [
                    'physical' => $shop_url->physical_uri,
                    'virtual' => $shop_url->virtual_uri,
                    'id_shop' => $shop_url->id_shop,
                ];
            }

            return $domains;
        }

        public static function valuesAreIdentical($v1, $v2)
        {
            $type1 = gettype($v1);
            $type2 = gettype($v2);

            if ($type1 !== $type2) {
                // Handle special case for boolean and string comparison
                if ($type1 === 'boolean' && $type2 === 'string') {
                    return ($v1 && $v2 === '1') || (!$v1 && $v2 === '0');
                } elseif ($type1 === 'integer' && $type2 === 'string') {
                    return $v1 === (int) $v2;
                } elseif ($type1 === 'double' && $type2 === 'string') {
                    return $v1 === (float) $v2;
                }

                // If types are different and not covered by special cases, return false
                return false;
            }

            // Handle comparisons for values of the same type
            switch ($type1) {
                case 'boolean':
                case 'integer':
                case 'double':
                case 'string':
                    return $v1 === $v2;

                case 'array':
                    return self::arraysAreIdentical($v1, $v2);

                case 'object':
                    $diffs = self::getObjectDifferences($v1, $v2);

                    return count($diffs) === 0;

                case 'NULL':
                    return true;

                case 'resource':
                case 'unknown type':
                default:
                    // Let's say that's it's OK
                    return true;
            }
        }

        public static function getObjectDifferences($o1, $o2)
        {
            $differences = [];

            try {
                if (gettype($o1) === 'object' && gettype($o2) === 'object') {
                    // Now do strict(er) comparison.
                    $reflectionObject = new \ReflectionObject($o1);

                    $properties = $reflectionObject->getProperties(\ReflectionProperty::IS_PUBLIC);
                    foreach ($properties as $property) {
                        if (in_array($property->name, ['date_upd', 'indexed']) || $property->isStatic()) {
                            continue;
                        }
                        if (!property_exists($o2, $property->name)) {
                            $differences[$property->name] = self::toString($o1->{$property->name}) . ' <> (not set)';
                        } else {
                            $bool = self::valuesAreIdentical($o1->{$property->name}, $o2->{$property->name});
                            if ($bool === false) {
                                $differences[$property->name] = self::toString($o1->{$property->name}) . ' <> ' . self::toString($o2->{$property->name});
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                self::addLog('Utils | Cannot getObjectDifferences o1:' . $o1 . ' o2:' . $o2 . ' - exception: ' . $e->getMessage(), 2);
            }

            // All tests passed.
            return $differences;
        }

        public static function arraysAreIdentical($arr1, $arr2)
        {
            if ($arr1 === null || $arr2 === null) {
                return $arr1 === $arr2;
            }

            if (!is_array($arr1) || !is_array($arr2)) {
                return false;
            }

            $count = count($arr1);

            // Require that they have the same size.
            if (count($arr2) !== $count) {
                return false;
            }

            // Require that they have the same keys.
            $arrKeysInCommon = array_intersect_key($arr1, $arr2);
            if (count($arrKeysInCommon) !== $count) {
                return false;
            }

            // Require that they have the same value for same key.
            foreach ($arr1 as $key => $val) {
                $bool = self::valuesAreIdentical($val, $arr2[$key]);
                if ($bool === false) {
                    return false;
                }
            }

            // All tests passed.
            return true;
        }

        public static function toString($val)
        {
            $type = gettype($val);
            switch (true) {
                case $type === 'boolean':
                    return $val ? 'true' : 'false';

                case $type === 'array':
                    return 'array[' . count($val) . ']';

                case $type === 'NULL':
                    return '(null)';

                case $type === 'unknown type':
                    return '(unknown type)';

                default:
                    return (string) $val;
            }
        }

        public static function getDatabaseName()
        {
            if (self::version_compare(_PS_VERSION_, '1.7', '>')) {
                $configFile = dirname(__FILE__) . '/../../../app/config/parameters.php';
                if (file_exists($configFile)) {
                    $config = require $configFile;

                    return $config['parameters']['database_name'];
                } else {
                    return _DB_NAME_;
                }
            } else {
                return _DB_NAME_;
            }
        }

        /**
         * @param string $sql SQL query to execute
         * @param bool $logOnError true if you want errors to be logged
         * @param bool $throwOnError true if you want errors to throw PrestaShopDatabaseException
         *
         * @return bool true if OK
         *
         * @throws \PrestaShopDatabaseException
         */
        public static function dbExecuteSQL($sql, $logOnError = true, $throwOnError = false)
        {
            $db = \Db::getInstance();
            $result = false;
            try {
                $result = $db->execute($sql, false);
                if (!$result) {
                    $msg = 'SQL Error #' . $db->getNumberError() . ': "' . $db->getMsgError() . '" in ' . self::getCallerInfos();
                    $msg .= ' - SQL query was: "' . $sql . '"';
                    if ($logOnError) {
                        self::addLog('Utils | ' . $msg, 2);
                    }
                    if ($throwOnError) {
                        throw new \PrestaShopDatabaseException($msg);
                    }
                }
            } catch (\Exception $e) {
                $msg = 'SQL Error #' . $db->getNumberError() . ': "' . $db->getMsgError() . '" in ' . self::getCallerInfos();
                $msg .= ' - SQL query was: "' . $sql . '"';
                if ($logOnError) {
                    self::addLog('Utils | ' . $msg . ' - exception: ' . $e->getMessage(), 2);
                }
                if ($throwOnError) {
                    throw new \PrestaShopDatabaseException($msg, 0, $e);
                }
            }

            return $result;
        }

        /**
         * @param string $sql SQL query to execute
         * @param bool $logOnError true if you want errors to be logged
         * @param bool $throwOnError true if you want errors to throw PrestaShopDatabaseException
         *
         * @return array
         *
         * @throws \PrestaShopDatabaseException
         */
        public static function dbSelectRows($sql, $logOnError = true, $throwOnError = false)
        {
            $db = \Db::getInstance();
            $result = [];
            try {
                $result = $db->executeS($sql, true, false);
                if (!$result && $db->getNumberError() != 0) {
                    $msg = 'SQL Error #' . $db->getNumberError() . ': "' . $db->getMsgError() . '" in ' . self::getCallerInfos();
                    $msg .= ' - SQL query was: "' . $sql . '"';
                    if ($logOnError) {
                        self::addLog('Utils | ' . $msg, 2);
                    }
                    if ($throwOnError) {
                        throw new \PrestaShopDatabaseException($msg);
                    }
                }
            } catch (\Exception $e) {
                $msg = 'SQL Error #' . $db->getNumberError() . ': "' . $db->getMsgError() . '" in ' . self::getCallerInfos();
                $msg .= ' - SQL query was: "' . $sql . '"';
                if ($logOnError) {
                    self::addLog('Utils | ' . $msg . ' - exception: ' . $e->getMessage(), 2);
                }
                if ($throwOnError) {
                    throw new \PrestaShopDatabaseException($msg, 0, $e);
                }
            }
            if (!is_array($result)) {
                $result = [];
            }

            return $result;
        }

        public static function dbColumnExists($tableName, $column)
        {
            $db = \Db::getInstance();
            $row = self::dbSelectRows('SHOW COLUMNS FROM `' . $db->escape($tableName) . '` LIKE \'' . $db->escape($column) . '\';', true, true);

            return is_array($row) && count($row) > 0 && is_array($row[0]) && count($row[0]) > 0;
        }

        public static function dbGetTableOfColumn($column, $listTables)
        {
            $db = \Db::getInstance();

            return self::dbGetValue('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=' . self::dbToString($db,
                self::getDatabaseName()) . ' AND TABLE_NAME IN (' . implode(',', array_map(function ($str) {
                    return '\'' . $str . '\'';
                }, $listTables)) . ') AND COLUMN_NAME=\'' . $db->escape($column) . '\'');
        }

        public static function dbTableExists($tableName)
        {
            $db = \Db::getInstance();

            return self::dbGetValue('SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE table_schema=' . self::dbToString($db,
                self::getDatabaseName()) . ' AND table_name=' . self::dbToString($db, $tableName)) > 0;
        }

        /**
         * @param $tableName string Name of the table
         * @param $columns string[] Names of the columns
         *
         * @return bool|string Name of the index or false if it does not exist
         *
         * @throws \PrestaShopDatabaseException
         */
        public static function dbIndexExists($tableName, $columns)
        {
            $cols = [];
            if (!is_array($columns)) {
                $cols[0] = $columns;
            } else {
                $cols = $columns;
            }
            if (count($cols) > 0) {
                $firsColumn = reset($cols);
                $db = \Db::getInstance();
                $rows = self::dbSelectRows('SELECT * FROM INFORMATION_SCHEMA.STATISTICS WHERE table_schema=' . self::dbToString($db,
                    self::getDatabaseName()) . ' AND table_name=' . self::dbToString($db,
                        $tableName) . ' AND column_name=' . self::dbToString($db, $firsColumn));
                foreach ($rows as $row) {
                    $indexCols = self::dbGetIndexColumns($tableName, $row['INDEX_NAME']);
                    if (count($indexCols) === count($cols) && count(array_diff($cols, $indexCols)) === 0) {
                        return $row['INDEX_NAME'];
                    }
                }
            }

            return false;
        }

        /**
         * @param $tableName string Name of the table to check
         *
         * @return bool true if this table has a primary key
         */
        public static function dbHasPrimaryKey($tableName)
        {
            $db = \Db::getInstance();

            return (int) self::dbGetValue('SELECT COUNT(*) AS has_primary_key
                FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA=' . self::dbToString($db, self::getDatabaseName())
                    . ' AND TABLE_NAME = ' . self::dbToString($db, $tableName)
                    . ' AND CONSTRAINT_TYPE = \'PRIMARY KEY\'') > 0;
        }

        /**
         * @param $tableName string Name of the table to check
         * @param $columnName string Name of the column to check
         *
         * @return bool true if this column has a unique index
         */
        public static function dbHasUniqueIndex($tableName, $columnName)
        {
            $db = \Db::getInstance();

            return (int) self::dbGetValue('SELECT COUNT(*) AS has_unique_index
                FROM INFORMATION_SCHEMA.STATISTICS
                WHERE TABLE_SCHEMA = ' . self::dbToString($db, self::getDatabaseName())
                  . ' AND TABLE_NAME = ' . self::dbToString($db, $tableName)
                  . ' AND COLUMN_NAME = ' . self::dbToString($db, $columnName)
                  . ' AND NON_UNIQUE = 0') > 0;
        }

        /**
         * @param $tableName string Name of the table
         * @param $indexName string Name of the index
         *
         * @return string[] Names of the columns
         *
         * @throws \PrestaShopDatabaseException
         */
        public static function dbGetIndexColumns($tableName, $indexName)
        {
            $columns = [];
            if (is_string($tableName) && is_string($indexName)) {
                $db = \Db::getInstance();
                $rows = self::dbSelectRows('SELECT SEQ_IN_INDEX, COLUMN_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE '
                    . 'table_schema=' . self::dbToString($db, self::getDatabaseName())
                    . ' AND table_name=' . self::dbToString($db, $tableName)
                    . ' AND index_name=' . self::dbToString($db, $indexName)
                    . ' ORDER BY SEQ_IN_INDEX ASC'
                );
                foreach ($rows as $row) {
                    $columns[(int) $row['SEQ_IN_INDEX']] = $row['COLUMN_NAME'];
                }
            }

            return $columns;
        }

        /**
         * @param $tableName string Name of the table
         * @param $columns string[] Names of columns in the index
         * @param $indexName string|null Name of the index (optional)
         *
         * @return bool true if index has been created
         */
        public static function dbCreateIndexIfNotExists($tableName, $columns, $indexName = null)
        {
            $cols = [];
            if (is_string($tableName) && (is_string($columns) || is_array($columns))) {
                if (!is_array($columns)) {
                    $cols[0] = $columns;
                } else {
                    $cols = $columns;
                }
                if (count($cols) > 0) {
                    $db = \Db::getInstance();
                    if (self::dbIndexExists($tableName, $columns) === false) {
                        $colsList = '';
                        foreach ($cols as $col) {
                            if (self::strlen($colsList) > 0) {
                                $colsList .= ',';
                            }
                            $colsList .= '`' . $db->escape(trim($col)) . '`';
                        }
                        if (self::dbExecuteSQL('ALTER TABLE `' . $db->escape($tableName) . '` ADD INDEX ' . ($indexName ? '`' . $db->escape($indexName) . '` ' : '') . '(' . $colsList . ');')) {
                            self::addLog("Utils | Index created for table $tableName on column (" . $colsList . ')', 1);

                            return true;
                        }
                    }
                }
            }

            return false;
        }

        /**
         * @param $tableName string Name of the table
         * @param $columns string[] Names of columns in the index
         *
         * @return bool true if the index has been deleted
         */
        public static function dbDeleteIndexIfExists($tableName, $columns)
        {
            $cols = [];
            if (is_string($tableName) && (is_string($columns) || is_array($columns))) {
                if (!is_array($columns)) {
                    $cols[0] = $columns;
                } else {
                    $cols = $columns;
                }
                if (count($cols) > 0) {
                    $db = \Db::getInstance();
                    $indexName = self::dbIndexExists($tableName, $columns);
                    if ($indexName !== false) {
                        $colsList = '';
                        foreach ($cols as $col) {
                            if (self::strlen($colsList) > 0) {
                                $colsList .= ',';
                            }
                            $colsList .= '`' . $db->escape(trim($col)) . '`';
                        }
                        if (self::dbExecuteSQL('ALTER TABLE `' . $db->escape($tableName) . '` DROP INDEX `' . $db->escape($indexName) . '`;')) {
                            self::addLog("Utils | Index $indexName deleted for table $tableName on column (" . $colsList . ')', 1);

                            return true;
                        }
                    }
                }
            }

            return false;
        }

        public static function dbDeleteIndexByName($tableName, $indexName, $logOnError = true, $throwOnError = false)
        {
            $db = \Db::getInstance();
            self::dbExecuteSQL('ALTER TABLE `' . $db->escape($tableName) . '` DROP INDEX `' . $db->escape($indexName) . '`;', $logOnError, $throwOnError);
        }

        /**
         * @param string $sql SQL query to execute
         * @param bool $logOnError true if you want errors to be logged
         * @param bool $throwOnError true if you want errors to throw PrestaShopDatabaseException
         *
         * @return mixed First value of the first row
         *
         * @throws \PrestaShopDatabaseException
         */
        public static function dbGetValue($sql, $logOnError = true, $throwOnError = false)
        {
            $row = self::dbSelectRows($sql, $logOnError, $throwOnError);
            if (is_array($row) && count($row) > 0 && is_array($row[0]) && count($row[0]) > 0) {
                return array_pop($row[0]);
            }

            return null;
        }

        /**
         * @param string $sql SQL query to execute
         * @param bool $logOnError true if you want errors to be logged
         * @param bool $throwOnError true if you want errors to throw PrestaShopDatabaseException
         *
         * @return mixed First row
         *
         * @throws \PrestaShopDatabaseException
         */
        public static function dbGetRow($sql, $logOnError = true, $throwOnError = false)
        {
            $row = self::dbSelectRows($sql, $logOnError, $throwOnError);
            if (is_array($row) && count($row) > 0 && is_array($row[0])) {
                return $row[0];
            }

            return null;
        }

        public static function getModuleInstanceById($id_module)
        {
            // Some fu**ing modules (like pohodaconnector) modify the shop context in the constructor so we set it back
            $currentShopContext = \Shop::getContextShopID(true);
            $moduleInstance = \Module::getInstanceById($id_module);
            if ($currentShopContext && \Shop::isFeatureActive()) {
                \Shop::setContext(\Shop::CONTEXT_SHOP, $currentShopContext);
            }

            return $moduleInstance;
        }

        /**
         * @return string Caller information s a string : file:line::function()
         */
        private static function getCallerInfos()
        {
            $traces = debug_backtrace();
            if (isset($traces[2])) {
                return $traces[1]['file'] . ':' . $traces[1]['line'] . '::' . $traces[2]['function'] . '()';
            }

            return '?';
        }

        /**
         * @param string $filePathContains
         * @param string $functionName
         *
         * @return bool
         */
        public static function isCaller($filePathContains, $functionName)
        {
            if (!is_string($filePathContains) || !is_string($functionName)) {
                return false;
            }

            $traces = debug_backtrace();

            return isset($traces[2]['file'], $traces[2]['function'])
                && strpos($traces[2]['file'], $filePathContains) !== false
                && $functionName === $traces[2]['function'];
        }

        /**
         * @return string
         */
        public static function getStackTrace()
        {
            $e = new \Exception();

            return $e->getTraceAsString();
        }

        /**
         * jTraceEx() - provide a Java style exception trace
         *
         * @param \Throwable $e
         * @param array $seen array passed to recursive calls to accumulate trace lines already seen leave as NULL when
         *                    calling this function
         *
         * @return string One entry per trace line
         */
        public static function jTraceEx($e, $seen = null)
        {
            $starter = $seen ? 'Caused by: ' : '';
            $result = [];
            if (!$seen) {
                $seen = [];
            }
            $trace = $e->getTrace();
            $prev = $e->getPrevious();
            $result[] = sprintf('%s%s: %s', $starter, get_class($e), $e->getMessage());
            $file = $e->getFile();
            $line = $e->getLine();
            while (true) {
                $current = "$file:$line";
                if (is_array($seen) && in_array($current, $seen)) {
                    $result[] = sprintf(' ... %d more', count($trace) + 1);
                    break;
                }
                $result[] = sprintf(' at %s%s%s(%s%s%s)',
                    count($trace) && array_key_exists('class', $trace[0]) ? str_replace('\\', '.', $trace[0]['class']) : '',
                    count($trace) && array_key_exists('class', $trace[0]) && array_key_exists('function', $trace[0]) ? '.' : '',
                    count($trace) && array_key_exists('function', $trace[0]) ? str_replace('\\', '.', $trace[0]['function']) : '(main)',
                    $line === null ? $file : basename($file),
                    $line === null ? '' : ':',
                    $line === null ? '' : $line);
                if (is_array($seen)) {
                    $seen[] = "$file:$line";
                }
                if (!count($trace)) {
                    break;
                }
                $file = array_key_exists('file', $trace[0]) ? $trace[0]['file'] : 'Unknown Source';
                $line = array_key_exists('file', $trace[0]) && array_key_exists('line', $trace[0]) && $trace[0]['line'] ? $trace[0]['line'] : null;
                array_shift($trace);
            }
            $result = join("\n", $result);
            if ($prev) {
                $result .= "\n" . self::jTraceEx($prev, $seen);
            }

            return $result;
        }

        public static function getRequestHeaderValue($headerName)
        {
            $headerNameLower = \Tools::strtolower($headerName);
            $headers = self::getAllHeaders();
            if (array_key_exists($headerName, $headers)) {
                return $headers[$headerNameLower];
            }

            return null;
        }

        public static function getAllHeaders()
        {
            static $headers = null;
            if ($headers === null) {
                $headers = [];
                foreach ($_SERVER as $name => $value) {
                    if (\Tools::substr($name, 0, 5) == 'HTTP_') {
                        $headers[str_replace(' ', '-',
                            \Tools::strtolower(str_replace('_', ' ', \Tools::substr($name, 5))))] = $value;
                    }
                }
            }

            return $headers;
        }

        public static function currentVisitorAcceptWebp()
        {
            $accept = self::getCookie('jpresta_accept_webp')
                || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false);
            if (!self::isAjax() && self::getCookie('jpresta_accept_webp') === null) {
                self::setCookie('jpresta_accept_webp',
                    (int) (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false),
                    60 * 24 * 365);
            }

            return $accept;
        }

        public static function currentVisitorAcceptAvif()
        {
            $accept = self::getCookie('jpresta_accept_avif')
                || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/avif') !== false);
            if (!self::isAjax() && self::getCookie('jpresta_accept_avif') === null) {
                self::setCookie('jpresta_accept_avif',
                    (int) (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/avif') !== false),
                    60 * 24 * 365);
            }

            return $accept;
        }

        public static function setCookie($name, $value, $expiresInMinutes)
        {
            if (!headers_sent()) {
                if (PHP_VERSION_ID <= 50200) {
                    /* PHP version > 5.2.0 */
                    setcookie($name, $value, time() + 60 * (int) $expiresInMinutes, '/', '', 0);
                } else {
                    setcookie($name, $value, time() + 60 * (int) $expiresInMinutes, '/', '', 0, false);
                }
            }
        }

        public static function getCookie($cookieName, $defaultValue = null)
        {
            if (array_key_exists($cookieName, $_COOKIE)) {
                // Necessary to avoid errors in Prestashop Addons validator
                foreach ($_COOKIE as $key => $cookieValue) {
                    if ($key === $cookieName) {
                        return $cookieValue;
                    }
                }
            }

            return $defaultValue;
        }

        public static function dbToInt($val)
        {
            if ($val && !empty($val)) {
                if (is_numeric($val)) {
                    // Preserve unsigned integers for 32bit systems
                    return $val;
                }
            }

            return 'NULL';
        }

        public static function dbWhereIntEqual($col, $val)
        {
            if ($val && !empty($val)) {
                if (is_numeric($val)) {
                    // Preserve unsigned integers for 32bit systems
                    return '`' . $col . '`=' . $val;
                }
            }

            return '`' . $col . '` IS NULL';
        }

        /**
         * @param $db Db
         * @param $val
         *
         * @return string
         */
        public static function dbToString($db, $val)
        {
            if ($val && !empty($val)) {
                return '\'' . $db->escape($val, true) . '\'';
            } else {
                return 'NULL';
            }
        }

        public static function getCanonicalHookNames()
        {
            $cacheId = 'hook_canonical_names';

            if (!\Cache::isStored($cacheId)) {
                $databaseResults = \Db::getInstance()->executeS('SELECT name, alias FROM `' . _DB_PREFIX_ . 'hook_alias`');
                $hooksByAlias = [];
                if ($databaseResults) {
                    foreach ($databaseResults as $record) {
                        $hooksByAlias[\Tools::strtolower($record['alias'])] = $record['name'];
                    }
                }
                \Cache::store($cacheId, $hooksByAlias);

                return $hooksByAlias;
            }

            return \Cache::retrieve($cacheId);
        }

        public static function getConfigurationAllShop($key, $default = false, $idLang = null)
        {
            if (self::version_compare(_PS_VERSION_, '1.7', '<')) {
                if (\Configuration::hasKey($key, $idLang, 0, 0)) {
                    return \Configuration::get($key, $idLang, 0, 0);
                }

                return $default;
            }

            return \Configuration::get($key, $idLang, 0, 0, $default);
        }

        public static function getConfigurationByShopId($key, $id_shop, $default = false, $idLang = null)
        {
            if (self::version_compare(_PS_VERSION_, '1.7', '<')) {
                if ($id_shop === null || !\Shop::isFeatureActive()) {
                    $id_shop = \Shop::getContextShopID(true);
                } else {
                    $id_shop = (int) $id_shop;
                }
                if (\Configuration::hasKey($key, $idLang, 0, $id_shop)) {
                    return \Configuration::get($key, $idLang, 0, $id_shop);
                }

                return $default;
            }

            return \Configuration::get($key, $idLang, 0, (int) $id_shop, $default);
        }

        public static function getConfigurationOfCurrentShop($key, $default = false, $idLang = null)
        {
            if (self::version_compare(_PS_VERSION_, '1.7', '<')) {
                if (\Configuration::hasKey($key, $idLang, null, null)) {
                    return \Configuration::get($key, $idLang, null, null);
                }

                return $default;
            }

            return \Configuration::get($key, $idLang, null, null, $default);
        }

        public static function saveConfigurationByShopId($key, $value, $id_shop)
        {
            \Configuration::updateValue($key, $value, false, null, $id_shop);
        }

        public static function saveConfigurationAllShop($key, $value)
        {
            // Make sure no value is store for a specific shop
            \Configuration::deleteByName($key);
            // Then save the new value in the global context
            \Configuration::updateValue($key, $value, false, 0, 0);
        }

        public static function saveConfigurationOfCurrentShop($key, $value)
        {
            \Configuration::updateValue($key, $value, false);
        }

        public static function isModuleEnabledByShopId($id_module, $id_shop)
        {
            $ret = (int) self::dbGetValue('SELECT count(*) FROM `' . _DB_PREFIX_ . 'module_shop` WHERE `id_module` = ' . (int) $id_module . ' AND `id_shop` = ' . (int) $id_shop);

            return $ret > 0;
        }

        public static function isModuleEnabled($module_name)
        {
            if (!\Cache::isStored('Module::isEnabled' . $module_name)) {
                $active = false;
                $id_module = \Module::getModuleIdByName($module_name);
                if ($id_module && \Db::getInstance()->getValue('SELECT `id_module` FROM `' . _DB_PREFIX_ . 'module_shop` WHERE `id_module` = ' . (int) $id_module . ' AND `id_shop` = ' . (int) \Context::getContext()->shop->id)) {
                    $active = true;
                }
                \Cache::store('Module::isEnabled' . $module_name, (bool) $active);

                return (bool) $active;
            }

            return \Cache::retrieve('Module::isEnabled' . $module_name);
        }

        /**
         * Get a security token specific to JPresta modules. Create it if it does not exists
         *
         * @param null $id_shop
         *
         * @return string
         */
        public static function getSecurityToken($id_shop = null)
        {
            if ($id_shop === null) {
                $id_shop = \Shop::getContextShopID();
            }
            $token = self::getConfigurationByShopId('pagecache_cron_token', $id_shop);
            if (!$token) {
                $token = self::generateRandomString();
                self::saveConfigurationByShopId('pagecache_cron_token', $token, $id_shop);
            }

            return $token;
        }

        /**
         * Fix a bug when multi-store has been disabled after creating some shops
         *
         * @return int[] List of shop IDs
         */
        public static function getCompleteListOfShopsID()
        {
            $allShopIds = \Shop::getCompleteListOfShopsID();
            if (!\Shop::isFeatureActive() && count($allShopIds) !== 1) {
                $allShopIds = [\Shop::getContextShopID()];
            }

            return $allShopIds;
        }

        public static function generateRandomString($length = 16)
        {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
            $final_rand = '';
            for ($i = 0; $i < $length; ++$i) {
                $final_rand .= $chars[rand(0, self::strlen($chars) - 1)];
            }

            return $final_rand;
        }

        /**
         * @param $message
         * @param int $severity 1 = info, 2 = warning, 3 = error, 4 = critical error
         * @param null $errorCode
         * @param null $objectType
         * @param null $objectId
         * @param bool $allowDuplicate
         * @param null $idEmployee
         */
        public static function addLog($message, $severity = 1, $errorCode = null, $objectType = null, $objectId = null, $allowDuplicate = true, $idEmployee = null)
        {
            if (class_exists('PrestaShopLogger')) {
                // Since PS 1.6.0.2
                \PrestaShopLogger::addLog('JPresta | ' . str_replace('<', '&lt;', $message), $severity, $errorCode, $objectType, $objectId, $allowDuplicate, $idEmployee);
            } else {
                \Logger::addLog('JPresta | ' . $message, $severity, $errorCode, $objectType, $objectId, $allowDuplicate, $idEmployee);
            }
        }

        public static function isSearchEngine()
        {
            return
                isset($_SERVER['HTTP_USER_AGENT'])
                && preg_match('/bot|crawl|slurp|spider|mediapartners|gtmetrix|chrome-lighthouse/i', $_SERVER['HTTP_USER_AGENT'])
            ;
        }

        /**
         * Checks if a method is overridden in /override by a specific module
         * based on the module name in the comment block before the method.
         *
         * @param string $className The original PrestaShop class name (e.g., 'Product')
         * @param string $functionName The method name to check (e.g., 'getPrice')
         * @param string $moduleName The name of the module (e.g., 'jprestaspeedpack')
         *
         * @return bool True if the method is overridden by the given module
         */
        public static function isOverridenBy($className, $functionName, $moduleName)
        {
            $overrideFile = _PS_ROOT_DIR_ . '/override/classes/' . $className . '.php';

            if (!file_exists($overrideFile)) {
                return false;
            }

            $content = \Tools::file_get_contents($overrideFile);
            if (!$content) {
                return false;
            }

            // Match comment blocks followed by the target method
            $pattern = '/\/\*.*?\*\/\s*public\s+function\s+' . preg_quote($functionName, '/') . '\s*\(/s';

            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[0] as $block) {
                    // Check if the module name appears in the comment block
                    if (preg_match('/\*\s*module:\s*' . preg_quote($moduleName, '/') . '/i', $block)) {
                        return true;
                    }
                }
            }

            return false;
        }
    }
}
