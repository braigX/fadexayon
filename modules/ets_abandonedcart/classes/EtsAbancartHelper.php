<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class EtsAbancartHelper
{
    public static function file_put_contents($path, $data, $allowed_directory = null, $allowedExtensions = [], $flags = 0, $context = null)
    {
        $sanitizedPath = self::sanitizePath($path);

        if (!$sanitizedPath || !self::isPathInAllowedDirectory($sanitizedPath, $allowed_directory)) {
            error_log('Unauthorized access attempt or invalid path: ' . $path);
            return false;
        }

        if (!is_writable(dirname($sanitizedPath))) {
            error_log('Directory is not writable: ' . $path);
            return false;
        }

        if (!empty($allowedExtensions) && !self::checkFileExtension($path, $allowedExtensions)) {
            error_log('Unsupported file extension: ' . $path);
            return false;
        }

        $result = file_put_contents($sanitizedPath, $data, $flags, $context);
        if ($result === false) {
            error_log('Failed to write to file: ' . $path);
            return false;
        }

        return $result;
    }

    public static function sanitizeURL($url)
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    public static function validateURL($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public static function isAllowedDomain($url, $allowed_domains = [])
    {
        if (empty($allowed_domains)) {
            $allowed_domains = [Context::getContext()->shop->domain];
        }
        $host = parse_url($url, PHP_URL_HOST);
        return in_array($host, $allowed_domains);
    }

    public static function file_get_contents(
        $url,
        $allowed_directory = null,
        $allowedExtensions = [],
        $use_include_path = false,
        $stream_context = null,
        $curl_timeout = 5,
        $fallback = false
    )
    {
        if (preg_match('/^https?:\/\//', $url)) {
            $url = self::sanitizeURL($url);

            if (!self::validateURL($url)) {
                error_log('Invalid URL: %s' . $url);
                return false;
            }

            if (!self::isAllowedDomain($url)) {
                error_log('Domain not allowed: ' . $url);
                return false;
            }
        } else {
            $url = self::sanitizePath($url);

            if (!$url || !self::isPathInAllowedDirectory($url, $allowed_directory)) {
                error_log('Unauthorized access attempt or invalid path: ' . $url);
                return false;
            }

            if (!empty($allowedExtensions) && !self::checkFileExtension($url, $allowedExtensions)) {
                error_log('Unsupported file extension: ' . $url);
                return false;
            }

            if (!file_exists($url)) {
                return false;
            }
        }
        $content = Tools::file_get_contents($url, $use_include_path, $stream_context, $curl_timeout, $fallback);
        if ($content === false) {
            error_log(sprintf('Failed to read file contents: ', $url), 3);
            return false;
        }
        return $content;
    }

    public static function sanitizePath($path)
    {
        $sanitizedPath = preg_replace('/[^\w\-\/\.:\\\\]/', '', $path);
        return $sanitizedPath === $path ? $sanitizedPath : false;
    }

    public static function isPathInAllowedDirectory($path, $allowed_directory = null)
    {
        if (empty($allowed_directory)) {
            $allowed_directory = _PS_ROOT_DIR_;
        }
        $realPath = realpath(dirname($path));
        $realAllowedDir = realpath($allowed_directory);

        return $realPath !== false && $realAllowedDir !== false && strpos($realPath, $realAllowedDir) === 0;
    }

    public static function checkFileExtension($path, $allowedExtensions)
    {
        $fileExtension = pathinfo($path, PATHINFO_EXTENSION);
        return in_array($fileExtension, $allowedExtensions);
    }

    public static function unlink($filePath, $baseDir = null)
    {
        if (preg_match('/^[a-zA-Z0-9_\-\/\.:\\\]+$/', $filePath) && self::isPathInAllowedDirectory($filePath, $baseDir) && file_exists(realpath($filePath))) {
            return unlink(realpath($filePath));
        }
        return false;
    }

    public static function readfile($filepath, $base_dir = null)
    {
        if (!self::isPathInAllowedDirectory($base_dir, $base_dir)) {
            echo 'Invalid base directory or file path.';
            return;
        }
        $normalized_path = realpath($filepath);
        $normalized_path = rtrim($normalized_path, DIRECTORY_SEPARATOR);

        if (!file_exists($normalized_path) || !is_readable($normalized_path)) {
            echo 'File does not exist or is not readable.';
            return;
        }

        readfile($normalized_path);
    }
    public static function sanitizeFileName($name)
    {
        $sanitized_name = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $name);
        if (empty($sanitized_name)) {
            $sanitized_name = 'default_filename';
        }
        return $sanitized_name;
    }
}