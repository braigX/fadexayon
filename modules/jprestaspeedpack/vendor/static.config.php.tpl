<?php
function jprestaIsSearchEngine()
{
    return (
        isset($_SERVER['HTTP_USER_AGENT'])
        && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'])
    );
}


/**
 * @return bool true if the URL is excluded because of the referer
 */
function jprestaIsExcludedByReferer() {
    // Get the list of URLs to ignore as a comma-separated string
    $ignoreReferers = %EXCLUDED_REFERERS%;

    // Check if the list is empty
    if (empty($ignoreReferers)) {
        return false; // No URLs to ignore, caching is allowed
    }

    // Split the list into an array of URLs
    $ignoreReferersList = array_map('trim', explode(',', $ignoreReferers));

    // Get the current referer
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

    // Check if the referer matches any of the URLs in the list
    foreach ($ignoreReferersList as $ignoreUrl) {
        if (strpos($referer, $ignoreUrl) !== false) {
            return true; // Match found, caching should be disabled
        }
    }

    return false; // No match found, caching is allowed
}

// Only used for search engines
function jprestaIsMobileDevice() {
    if (file_exists('%PS_ROOT_DIR%/vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php')) {
        require_once '%PS_ROOT_DIR%/vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php';
        $mobileDetector = new Mobile_Detect();
        return $mobileDetector->isMobile();
    }
    else {
        return isset($_SERVER["HTTP_USER_AGENT"]) && preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }
}

// Only check static cache if the cache context is set and that this is a GET request
if ($_SERVER["REQUEST_METHOD"] === 'GET' && (isset($_COOKIE['jpresta_cache_context']) || jprestaIsSearchEngine())) {

    $DEBUG_MODE = %DEBUG_MODE%;
    $ALWAYS_DISPLAY_INFOS = %ALWAYS_DISPLAY_INFOS%;
    $MODULE_PATH = %MODULE_PATH%;
    $CACHE_DIR = %CACHE_DIR%;
    $PCU_HEADER = %PCU_HEADER%;
    $IGNORED_PARAMS = %IGNORED_PARAMS%;
    $EXPIRES_MIN = %EXPIRES_MIN%;

    // Ajax requests are not cached. If it is defined with URL parameter 'ajax' ok but if it is defined with
    // 'Accept' header then we must check it
    $isAjax = false;
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        $isAjax = preg_match('#\bapplication/json\b#', $_SERVER['HTTP_ACCEPT']);
    }

    // Do not use the cache for Cache-warmer
    $isCacheWarmer = isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] === 'JPresta-Cache-Warmer';

    // Check if cache is enabled in debug mode
    $isCacheDisabled = $DEBUG_MODE && strpos($_SERVER["REQUEST_URI"], 'dbgpagecache=1') === false;

    // Check if the user want to delete the current cache (in debug mode)
    $wantCacheRefresh = ($DEBUG_MODE || $ALWAYS_DISPLAY_INFOS) && strpos($_SERVER["REQUEST_URI"], 'delpagecache') !== false;

    // Only check static cache if the URL looks like a PHP request (not static files)
    $ext = pathinfo(strtok($_SERVER["REQUEST_URI"], '?'), PATHINFO_EXTENSION);
    $looksLikePHPRequest = !$ext || $ext === 'htm' || $ext === 'html';

    // Check if the shop is a copy of an other shop
    $isWrongPath = !file_exists($MODULE_PATH);
    if ($isWrongPath) {
        error_log('Prestashop Static cache is disabled because this shop is a copy. Please, disable then enable Prestashop static in Page Cache Ultimate module to fix it.');
        unlink(__FILE__);
    }

    // Find the context key to use
    $contextKey = null;
    if (isset($_COOKIE['jpresta_cache_context'])) {
        $contextKey = $_COOKIE['jpresta_cache_context'];
    }

    if (!$isWrongPath && $contextKey && !$isCacheWarmer && !$isAjax && !$isCacheDisabled && !$wantCacheRefresh && $looksLikePHPRequest && !jprestaIsExcludedByReferer()) {

        function filterAndSortParams($query_string, $ignored_params)
        {
            $new_query_string = '';
            if ($query_string) {
                $keyvalues = explode('&', $query_string);
                sort($keyvalues);
                foreach ($keyvalues as $keyvalue) {
                    if ($keyvalue !== '') {
                        $key = '';
                        $value = '';
                        $current_key_value = explode('=', $keyvalue);
                        if (count($current_key_value) > 0) {
                            $key = strtolower($current_key_value[0]);
                        }
                        if (count($current_key_value) > 1) {
                            $value = $current_key_value[1];
                        }
                        if (!in_array($key, $ignored_params)) {
                            $new_query_string .= '&' . $key . '=' . $value;
                        }
                    }
                }
                if ($new_query_string !== '') {
                    $new_query_string = substr($new_query_string, 1);
                }
            }
            return $new_query_string;
        }

        require_once $MODULE_PATH . 'vendor/http_build_url.php';

        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $isSecure = true;
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            $isSecure = true;
        }
        $url = ($isSecure ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $ignored_params = explode(',', $IGNORED_PARAMS);
        $ignored_params[] = 'delpagecache';
        $ignored_params[] = 'dbgpagecache';
        $ignored_params[] = 'cfgpagecache';
        $query_string = parse_url($url, PHP_URL_QUERY);
        $new_query_string = filterAndSortParams($query_string, $ignored_params);
        if ($new_query_string) {
            $normalized_url = http_build_url($url, array("query" => $new_query_string));
        } else {
            $normalized_url = http_build_url($url, array(), HTTP_URL_STRIP_QUERY);
        }

        $key = md5($normalized_url);
        $subdir = $CACHE_DIR . $contextKey;
        for ($i = 0; $i < min(3, strlen($key)); $i++) {
            $subdir .= DIRECTORY_SEPARATOR . $key[$i];
        }
        $filename = $subdir . DIRECTORY_SEPARATOR . $key . '.gz';
        if (file_exists($filename)) {
            $isContentServed = false;
            try {
                header($PCU_HEADER . ': status=on, reason=static, age=' . (time() - filemtime($filename)));
                header('Vary: Content-Encoding');
                header('Content-type: text/html; charset=utf-8');

                $acceptDeflate = false;
                if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
                    $acceptDeflate = preg_match('#\bdeflate\b#', $_SERVER['HTTP_ACCEPT_ENCODING']);
                }

                if (!$DEBUG_MODE && !$ALWAYS_DISPLAY_INFOS && $acceptDeflate) {
                    header('Content-Encoding: deflate');
                    header('Content-Length: ' . filesize($filename));
                    header('Server-Timing: jpresta_cache;desc=3');
                    header('Timing-Allow-Origin: *');
                    if ($EXPIRES_MIN > 0) {
                        $offset = 60 * $EXPIRES_MIN;
                        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT');
                        header('Cache-Control: max-age='.$offset.', private');
                        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
                        header_remove('Pragma');
                    }
                    else {
                        // Browser cache is disabled, force the browser to not use it (specially for back/forward cache)
                        header('Expires: Wed, 19 Oct 1977 18:00:00 GMT');
                        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
                    }
                    echo file_get_contents($filename);
                    $isContentServed = true;
                } else {
                    // In debug mode we cannot send zipped content since we add non zipped content at the end...
                    $uncompressedContent = @gzuncompress(file_get_contents($filename));
                    if ($uncompressedContent === false) {
                        error_log('*** Cannot serve cache in static way gzuncompress returned false on file ' . $filename);
                    }
                    else {
                        header('Server-Timing: jpresta_cache;desc=3');
                        header('Timing-Allow-Origin: *');
                        if ($EXPIRES_MIN > 0) {
                            $offset = 60 * $EXPIRES_MIN;
                            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT');
                            header('Cache-Control: max-age='.$offset.', private');
                            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
                            header_remove('Pragma');
                        }
                        else {
                            // Browser cache is disabled, force the browser to not use it (specially for back/forward cache)
                            header('Expires: Wed, 19 Oct 1977 18:00:00 GMT');
                            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
                        }
                        echo $uncompressedContent;
                        $isContentServed = true;
                    }
                }

                if ($DEBUG_MODE || $ALWAYS_DISPLAY_INFOS) {
                    // In debug mode we cannot send zipped content since we add non zipped content at the end...
                    $infos = file_get_contents($MODULE_PATH . 'views/templates/hook/pagecache-infos-static.htm');
                    $urlDebug = str_replace(['?delpagecache=1','&delpagecache=1'], '', $url);
                    $urlOff = str_replace('dbgpagecache=1', 'dbgpagecache=0', $urlDebug);
                    $urlOffDisabled = '';
                    if (!$DEBUG_MODE && $ALWAYS_DISPLAY_INFOS) {
                        // Infos box cannot be closed
                        $urlOffDisabled = 'disabled="true" style="cursor:not-allowed; pointer-events: none; opacity: 0.4;"';
                    }
                    if (strpos($urlDebug, '?') !== false) {
                        $urlDel = $urlDebug . '&delpagecache=1';
                    }
                    else {
                        $urlDel = $urlDebug . '?delpagecache=1';
                    }
                    $urlReload = $urlDebug;
                    $urlClose = $normalized_url;
                    $urlCloseDisabled = '';
                    if ($ALWAYS_DISPLAY_INFOS) {
                        // Infos box cannot be closed
                        $urlCloseDisabled = 'disabled="true" style="cursor:not-allowed; pointer-events: none; opacity: 0.4;"';
                    }

                    echo str_replace(['URL_OFF_DISABLED', 'URL_CLOSE_DISABLED', 'URL_OFF', 'URL_DEL', 'URL_RELOAD', 'URL_CLOSE'],
                    [$urlOffDisabled, $urlCloseDisabled, $urlOff, $urlDel, $urlReload, $urlClose], $infos);
                }
            }
            catch (Exception $e) {
                error_log('*** Cannot serve cache in static way : ' . $e->getMessage());
            }

            if ($isContentServed) {
                exit;
            }
            // else display it the normal way
        }
    }
}
