<?php

use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;

/**
 * Builds cached minified copies of local CSS and JavaScript assets.
 *
 * The implementation is intentionally limited to local shop assets. External
 * URLs are left untouched because PrestaLoad cannot safely rewrite or persist
 * remote files inside the shop.
 */
class PrestaLoadAssetMinifier
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var string
     */
    private $modulePath;

    public function __construct(Context $context, $modulePath)
    {
        $this->context = $context;
        $this->modulePath = rtrim((string) $modulePath, '/');
    }

    /**
     * Returns a public URL to a minified cached copy, or an empty string when
     * the asset cannot be minified safely.
     */
    public function getMinifiedAssetUrl($assetUrl, $assetType)
    {
        $assetType = Tools::strtolower(trim((string) $assetType));
        if (!in_array($assetType, ['css', 'js'], true)) {
            return '';
        }

        $sourcePath = $this->resolveLocalAssetPath($assetUrl, $assetType);
        if ($sourcePath === '' || !is_file($sourcePath) || !is_readable($sourcePath)) {
            return '';
        }

        $minifiedDirectory = $this->getMinifiedDirectory();
        if (!is_dir($minifiedDirectory) && !@mkdir($minifiedDirectory, 0775, true) && !is_dir($minifiedDirectory)) {
            return '';
        }

        $extension = $assetType === 'css' ? 'css' : 'js';
        $hash = sha1($sourcePath . '|' . @filemtime($sourcePath) . '|' . @filesize($sourcePath));
        $targetFilename = $hash . '.min.' . $extension;
        $targetPath = $minifiedDirectory . '/' . $targetFilename;

        if (!is_file($targetPath)) {
            if (!$this->buildMinifiedCopy($sourcePath, $targetPath, $assetType)) {
                return '';
            }
        }

        return rtrim($this->getShopBaseUrl(), '/') . '/modules/prestaload/cache/minified/' . $targetFilename;
    }

    /**
     * Removes the cached minified copy for a local asset. The original asset
     * file is never touched.
     */
    public function clearMinifiedAsset($assetUrl, $assetType)
    {
        $targetPath = $this->getMinifiedAssetPath($assetUrl, $assetType);
        if ($targetPath === '') {
            return false;
        }

        if (!is_file($targetPath)) {
            return true;
        }

        return @unlink($targetPath);
    }

    private function buildMinifiedCopy($sourcePath, $targetPath, $assetType)
    {
        try {
            if ($assetType === 'css') {
                $minifier = new CSS($sourcePath);
            } else {
                $minifier = new JS($sourcePath);
            }

            $minifier->minify($targetPath);

            return is_file($targetPath) && filesize($targetPath) !== false;
        } catch (Exception $exception) {
            return false;
        } catch (Throwable $throwable) {
            return false;
        }
    }

    private function getMinifiedAssetPath($assetUrl, $assetType)
    {
        $assetType = Tools::strtolower(trim((string) $assetType));
        if (!in_array($assetType, ['css', 'js'], true)) {
            return '';
        }

        $sourcePath = $this->resolveLocalAssetPath($assetUrl, $assetType);
        if ($sourcePath === '' || !is_file($sourcePath) || !is_readable($sourcePath)) {
            return '';
        }

        $extension = $assetType === 'css' ? 'css' : 'js';
        $hash = sha1($sourcePath . '|' . @filemtime($sourcePath) . '|' . @filesize($sourcePath));

        return $this->getMinifiedDirectory() . '/' . $hash . '.min.' . $extension;
    }

    private function resolveLocalAssetPath($assetUrl, $assetType)
    {
        $assetUrl = trim((string) $assetUrl);
        if ($assetUrl === '' || strpos($assetUrl, 'data:') === 0) {
            return '';
        }

        if (strpos($assetUrl, '//') === 0) {
            $assetUrl = $this->getShopScheme() . ':' . $assetUrl;
        }

        $path = '';
        if (preg_match('#^https?://#i', $assetUrl)) {
            $parts = parse_url($assetUrl);
            if ($parts === false) {
                return '';
            }

            $path = isset($parts['path']) ? (string) $parts['path'] : '';
            if (!$this->isShopHost(isset($parts['host']) ? $parts['host'] : '') && !$this->isSupportedLocalAssetPath($path)) {
                return '';
            }
        } else {
            $path = strpos($assetUrl, '/') === 0 ? $assetUrl : '/' . ltrim($assetUrl, '/');
        }

        $path = rawurldecode($path);
        if ($path === '' || strpos($path, '..') !== false || !$this->isSupportedLocalAssetPath($path)) {
            return '';
        }

        $normalizedPath = rtrim(_PS_ROOT_DIR_, '/') . $path;
        if (!preg_match('/\.' . preg_quote($assetType, '/') . '(\?|#|$)/i', $path)) {
            return '';
        }

        return $normalizedPath;
    }

    private function isSupportedLocalAssetPath($path)
    {
        if (!is_string($path) || $path === '') {
            return false;
        }

        return (bool) preg_match('#^/(modules|themes|js|css|img|upload)/#i', $path);
    }

    private function isShopHost($host)
    {
        $shopHost = parse_url($this->getShopBaseUrl(), PHP_URL_HOST);

        return is_string($shopHost) && Tools::strtolower((string) $shopHost) === Tools::strtolower((string) $host);
    }

    private function getMinifiedDirectory()
    {
        return $this->modulePath . '/cache/minified';
    }

    private function getShopBaseUrl()
    {
        $baseUrl = $this->context->shop && method_exists($this->context->shop, 'getBaseURL')
            ? $this->context->shop->getBaseURL(true)
            : '';

        if ($baseUrl === '' && isset($this->context->link)) {
            $baseUrl = (string) $this->context->link->getPageLink('index', true);
        }

        $parts = parse_url((string) $baseUrl);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return rtrim((string) $baseUrl, '/');
        }

        $origin = $parts['scheme'] . '://' . $parts['host'];
        if (!empty($parts['port'])) {
            $origin .= ':' . (int) $parts['port'];
        }

        return $origin;
    }

    private function getShopScheme()
    {
        $scheme = parse_url($this->getShopBaseUrl(), PHP_URL_SCHEME);

        return is_string($scheme) && $scheme !== '' ? $scheme : 'https';
    }
}
