<?php
/**
 * Early PrestaLoad static HTML cache bootstrap.
 *
 * This file is intentionally tiny and avoids loading Prestashop. It runs from
 * index.php before config/config.inc.php, checks a safe anonymous subset of
 * requests, and serves a cached HTML payload immediately when available.
 *
 * V1 stays conservative:
 * - GET requests only
 * - no AJAX
 * - homepage-like requests only
 * - bypass if common session/cart cookies are present
 */

if (!defined('_PS_ROOT_DIR_')) {
    define('_PS_ROOT_DIR_', __DIR__);
}

$prestaLoadRuntimePath = __DIR__ . '/modules/prestaload/cache/runtime-config.php';
if (!is_file($prestaLoadRuntimePath)) {
    return;
}

$prestaLoadRuntime = require $prestaLoadRuntimePath;
if (!is_array($prestaLoadRuntime) || empty($prestaLoadRuntime['enabled'])) {
    return;
}

require_once __DIR__ . '/modules/prestaload/classes/PrestaLoadCacheStore.php';
require_once __DIR__ . '/modules/prestaload/classes/PrestaLoadEarlyCacheKeyBuilder.php';

if (!function_exists('prestaload_should_serve_early_cache')) {
    function prestaload_should_serve_early_cache(array $runtime)
    {
        if (!isset($_SERVER['REQUEST_METHOD']) || strtoupper((string) $_SERVER['REQUEST_METHOD']) !== 'GET') {
            return false;
        }

        if (isset($_GET['ajax']) && $_GET['ajax']) {
            return false;
        }

        $path = isset($_SERVER['REQUEST_URI']) ? (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '/';
        if ($path === false || $path === '') {
            $path = '/';
        }

        // Keep the first server-level test intentionally narrow so it cannot
        // leak personalized content. We can widen this later once invalidation
        // and cookie detection are stronger.
        if ($path !== '/' && $path !== '/index.php') {
            return false;
        }

        $cookieHeader = isset($_SERVER['HTTP_COOKIE']) ? (string) $_SERVER['HTTP_COOKIE'] : '';
        if ($cookieHeader !== '' && preg_match('/(?:PrestaShop-|PHPSESSID|id_cart|logged|customer|checkout|admin)/i', $cookieHeader)) {
            return false;
        }

        return in_array('index', isset($runtime['allowed_controllers']) && is_array($runtime['allowed_controllers']) ? $runtime['allowed_controllers'] : [], true);
    }

    function prestaload_send_cached_headers(array $payload)
    {
        if (!headers_sent() && isset($payload['headers']) && is_array($payload['headers'])) {
            foreach ($payload['headers'] as $header) {
                header($header, true);
            }
        }

        if (!headers_sent()) {
            header('X-PrestaLoad-Boot: early-static');
            header('X-PrestaLoad-Static-Cache: HIT');
            header('Cache-Control: no-cache, must-revalidate');
        }

        if (isset($payload['status_code']) && (int) $payload['status_code'] > 0) {
            http_response_code((int) $payload['status_code']);
        }
    }
}

if (!prestaload_should_serve_early_cache($prestaLoadRuntime)) {
    return;
}

$prestaLoadStore = new PrestaLoadCacheStore(isset($prestaLoadRuntime['cache_directory']) ? $prestaLoadRuntime['cache_directory'] : __DIR__ . '/modules/prestaload/cache/pages');
$prestaLoadKeyContext = PrestaLoadEarlyCacheKeyBuilder::buildContextFromServer();
$prestaLoadPayload = $prestaLoadStore->get($prestaLoadKeyContext['key']);

if (!is_array($prestaLoadPayload) || !isset($prestaLoadPayload['body'])) {
    return;
}

prestaload_send_cached_headers($prestaLoadPayload);
exit($prestaLoadPayload['body']);
