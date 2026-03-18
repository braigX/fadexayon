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

if (!function_exists('prestaload_log_early_boot')) {
    function prestaload_log_early_boot(array $payload)
    {
        $path = isset($_SERVER['REQUEST_URI']) ? (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '/';
        if ($path === false || $path === '') {
            $path = '/';
        }

        if ($path !== '/' && $path !== '/index.php') {
            return;
        }

        $logFile = __DIR__ . '/modules/prestaload/cache/prestaload-features.log';
        $directory = dirname($logFile);
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        $payload['stage'] = 'early_bootstrap';
        $payload['logged_at'] = gmdate('c');
        $payload['request_uri'] = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        $payload['method'] = isset($_SERVER['REQUEST_METHOD']) ? (string) $_SERVER['REQUEST_METHOD'] : '';

        $line = json_encode($payload);
        if ($line !== false) {
            @file_put_contents($logFile, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }
}

$prestaLoadRuntimePath = __DIR__ . '/modules/prestaload/runtime-config.php';
if (!is_file($prestaLoadRuntimePath)) {
    prestaload_log_early_boot(['step' => 'skip', 'reason' => 'runtime-config-missing']);
    return;
}

$prestaLoadRuntime = require $prestaLoadRuntimePath;
if (!is_array($prestaLoadRuntime) || empty($prestaLoadRuntime['enabled'])) {
    prestaload_log_early_boot(['step' => 'skip', 'reason' => 'runtime-disabled']);
    return;
}

require_once __DIR__ . '/modules/prestaload/classes/PrestaLoadCacheStore.php';
require_once __DIR__ . '/modules/prestaload/classes/PrestaLoadEarlyCacheKeyBuilder.php';

if (!function_exists('prestaload_should_serve_early_cache')) {
    function prestaload_get_early_cache_eligibility(array $runtime)
    {
        if (!isset($_SERVER['REQUEST_METHOD']) || strtoupper((string) $_SERVER['REQUEST_METHOD']) !== 'GET') {
            return [
                'eligible' => false,
                'reason' => 'method-not-get',
                'method' => isset($_SERVER['REQUEST_METHOD']) ? (string) $_SERVER['REQUEST_METHOD'] : '',
            ];
        }

        if (isset($_GET['ajax']) && $_GET['ajax']) {
            return [
                'eligible' => false,
                'reason' => 'ajax-request',
            ];
        }

        $path = isset($_SERVER['REQUEST_URI']) ? (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '/';
        if ($path === false || $path === '') {
            $path = '/';
        }

        // Keep the first server-level test intentionally narrow so it cannot
        // leak personalized content. We can widen this later once invalidation
        // and cookie detection are stronger.
        if ($path !== '/' && $path !== '/index.php') {
            return [
                'eligible' => false,
                'reason' => 'path-not-supported',
                'path' => $path,
            ];
        }

        $cookieHeader = isset($_SERVER['HTTP_COOKIE']) ? (string) $_SERVER['HTTP_COOKIE'] : '';
        if ($cookieHeader !== '' && preg_match('/(?:PrestaShop-|id_cart|logged|customer|checkout|admin)/i', $cookieHeader, $matches)) {
            return [
                'eligible' => false,
                'reason' => 'cookie-blocked',
                'matched_cookie_fragment' => isset($matches[0]) ? (string) $matches[0] : '',
            ];
        }

        $allowedControllers = isset($runtime['allowed_controllers']) && is_array($runtime['allowed_controllers']) ? $runtime['allowed_controllers'] : [];
        if (!in_array('index', $allowedControllers, true)) {
            return [
                'eligible' => false,
                'reason' => 'controller-not-allowed',
                'allowed_controllers' => $allowedControllers,
            ];
        }

        return [
            'eligible' => true,
            'reason' => 'ok',
            'path' => $path,
        ];
    }

    function prestaload_should_serve_early_cache(array $runtime)
    {
        $eligibility = prestaload_get_early_cache_eligibility($runtime);

        return !empty($eligibility['eligible']);
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
    $prestaLoadEligibility = prestaload_get_early_cache_eligibility($prestaLoadRuntime);
    unset($prestaLoadEligibility['eligible']);
    $prestaLoadEligibility['step'] = 'skip';
    prestaload_log_early_boot($prestaLoadEligibility);
    return;
}

$prestaLoadStore = new PrestaLoadCacheStore(isset($prestaLoadRuntime['cache_directory']) ? $prestaLoadRuntime['cache_directory'] : __DIR__ . '/modules/prestaload/cache/pages');
$prestaLoadKeyContext = PrestaLoadEarlyCacheKeyBuilder::buildContextFromServer();
$prestaLoadPayload = $prestaLoadStore->get($prestaLoadKeyContext['key']);

if (!is_array($prestaLoadPayload) || !isset($prestaLoadPayload['body'])) {
    prestaload_log_early_boot([
        'step' => 'miss',
        'cache_key' => $prestaLoadKeyContext['key'],
        'cache_parts' => $prestaLoadKeyContext['parts'],
    ]);
    return;
}

prestaload_send_cached_headers($prestaLoadPayload);
prestaload_log_early_boot([
    'step' => 'hit',
    'cache_key' => $prestaLoadKeyContext['key'],
    'cache_parts' => $prestaLoadKeyContext['parts'],
]);
exit($prestaLoadPayload['body']);
