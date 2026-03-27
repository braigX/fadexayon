<?php

class PrestaLoadCacheStoreService
{
    /**
     * @var Prestaload
     */
    private $module;

    public function __construct(Prestaload $module)
    {
        $this->module = $module;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public function publish(array $payload)
    {
        $variantKey = trim((string) (isset($payload['variant_key']) ? $payload['variant_key'] : ''));
        $html = (string) (isset($payload['html']) ? $payload['html'] : '');
        $artifactVersionId = (string) (isset($payload['artifact_version_id']) ? $payload['artifact_version_id'] : '');
        $variant = isset($payload['variant']) && is_array($payload['variant']) ? $payload['variant'] : [];
        $shopId = (int) ($variant['shop_id'] ?? 0);
        $usedCss = (string) (isset($payload['used_css']) ? $payload['used_css'] : '');
        $usedCssSha256 = trim((string) (isset($payload['used_css_sha256']) ? $payload['used_css_sha256'] : ''));

        if ($variantKey === '') {
            throw new Exception('Missing variant key.');
        }

        if ($html === '' || stripos($html, '</html>') === false) {
            throw new Exception('Invalid HTML payload.');
        }

        $dir = $this->getVariantDirectory($variantKey);
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new Exception('Failed to create cache directory.');
        }

        $htmlPath = $this->getHtmlPath($variantKey);
        $metaPath = $this->getMetaPath($variantKey);
        $usedCssPath = $shopId > 0 ? $this->getUsedCssPath($shopId, $variantKey) : '';
        $meta = [
            'artifact_version_id' => $artifactVersionId,
            'variant_key' => $variantKey,
            'variant' => $variant,
            'normalized_url' => isset($payload['normalized_url']) ? (string) $payload['normalized_url'] : '',
            'html_type' => isset($payload['html_type']) ? (string) $payload['html_type'] : 'optimized',
            'html_bytes' => isset($payload['html_bytes']) ? (int) $payload['html_bytes'] : strlen($html),
            'checksum_sha256' => isset($payload['checksum_sha256']) ? (string) $payload['checksum_sha256'] : hash('sha256', $html),
            'used_css_path' => $usedCss !== '' ? $usedCssPath : null,
            'used_css_bytes' => $usedCss !== '' ? strlen($usedCss) : 0,
            'used_css_sha256' => $usedCss !== '' ? ($usedCssSha256 !== '' ? $usedCssSha256 : hash('sha256', $usedCss)) : null,
            'stored_at' => date('c'),
        ];

        $tmpHtmlPath = $htmlPath . '.tmp';
        $tmpMetaPath = $metaPath . '.tmp';
        $tmpUsedCssPath = $usedCssPath !== '' ? $usedCssPath . '.tmp' : '';

        if (@file_put_contents($tmpHtmlPath, $html, LOCK_EX) === false) {
            throw new Exception('Failed to write cached HTML.');
        }

        $encodedMeta = json_encode($meta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        if ($encodedMeta === false || @file_put_contents($tmpMetaPath, $encodedMeta, LOCK_EX) === false) {
            throw new Exception('Failed to write cache metadata.');
        }

        if ($usedCss !== '') {
            $usedCssDir = dirname($usedCssPath);
            if (!is_dir($usedCssDir) && !@mkdir($usedCssDir, 0775, true) && !is_dir($usedCssDir)) {
                @unlink($tmpHtmlPath);
                @unlink($tmpMetaPath);
                throw new Exception('Failed to create used CSS directory.');
            }

            if (@file_put_contents($tmpUsedCssPath, $usedCss, LOCK_EX) === false) {
                @unlink($tmpHtmlPath);
                @unlink($tmpMetaPath);
                throw new Exception('Failed to write used CSS.');
            }
        }

        if (!@rename($tmpHtmlPath, $htmlPath)) {
            @unlink($tmpHtmlPath);
            @unlink($tmpMetaPath);
            if ($tmpUsedCssPath !== '') {
                @unlink($tmpUsedCssPath);
            }
            throw new Exception('Failed to move cached HTML into place.');
        }

        if (!@rename($tmpMetaPath, $metaPath)) {
            @unlink($tmpMetaPath);
            if ($tmpUsedCssPath !== '') {
                @unlink($tmpUsedCssPath);
            }
            throw new Exception('Failed to move cache metadata into place.');
        }

        if ($tmpUsedCssPath !== '' && !@rename($tmpUsedCssPath, $usedCssPath)) {
            @unlink($tmpUsedCssPath);
            throw new Exception('Failed to move used CSS into place.');
        }

        return [
            'stored' => true,
            'path' => $htmlPath,
            'meta_path' => $metaPath,
            'used_css_path' => $usedCss !== '' ? $usedCssPath : null,
            'bytes' => strlen($html),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getCacheMeta($variantKey)
    {
        $metaPath = $this->getMetaPath($variantKey);
        if (!is_file($metaPath)) {
            return null;
        }

        $json = @file_get_contents($metaPath);
        if (!is_string($json) || $json === '') {
            return null;
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : null;
    }

    public function getCachedHtml($variantKey)
    {
        $htmlPath = $this->getHtmlPath($variantKey);
        if (!is_file($htmlPath)) {
            return null;
        }

        $html = @file_get_contents($htmlPath);

        return is_string($html) && $html !== '' ? $html : null;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public function purge(array $payload)
    {
        $variantKey = trim((string) (isset($payload['variant_key']) ? $payload['variant_key'] : ''));
        if ($variantKey === '') {
            throw new Exception('Missing variant key.');
        }

        $htmlPath = $this->getHtmlPath($variantKey);
        $metaPath = $this->getMetaPath($variantKey);
        $deletedHtml = false;
        $deletedMeta = false;
        $deletedUsedCss = false;
        $usedCssPath = '';

        $meta = $this->getCacheMeta($variantKey);
        if (is_array($meta) && !empty($meta['used_css_path'])) {
            $usedCssPath = (string) $meta['used_css_path'];
        }

        if ($usedCssPath === '') {
            $shopId = (int) ($payload['shop_id'] ?? 0);
            $usedCssPath = $this->getUsedCssPath($shopId, $variantKey);
        }

        if (is_file($htmlPath)) {
            $deletedHtml = @unlink($htmlPath);
        }

        if (is_file($metaPath)) {
            $deletedMeta = @unlink($metaPath);
        }

        if ($usedCssPath !== '' && is_file($usedCssPath)) {
            $deletedUsedCss = @unlink($usedCssPath);
            $this->cleanupEmptyAssetDirectories($usedCssPath);
        }

        $this->cleanupEmptyFanoutDirectories($variantKey);

        return [
            'purged' => $deletedHtml || $deletedMeta || $deletedUsedCss,
            'deleted_html' => $deletedHtml,
            'deleted_meta' => $deletedMeta,
            'deleted_used_css' => $deletedUsedCss,
            'html_path' => $htmlPath,
            'meta_path' => $metaPath,
            'used_css_path' => $usedCssPath,
        ];
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public function purgeAllForShop(array $payload)
    {
        $shopId = (int) (isset($payload['shop_id']) ? $payload['shop_id'] : 0);
        if ($shopId <= 0) {
            throw new Exception('Missing shop id.');
        }

        $results = [
            'shop_id' => $shopId,
            'variants_count' => 0,
            'purged_count' => 0,
            'deleted_html_count' => 0,
            'deleted_meta_count' => 0,
            'deleted_variant_cache' => false,
        ];

        $cacheDir = $this->getCacheDirectory();
        if (!is_dir($cacheDir)) {
            return $results;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS)
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || strtolower($file->getExtension()) !== 'json') {
                continue;
            }

            $metaPath = $file->getPathname();
            $json = @file_get_contents($metaPath);
            $meta = is_string($json) ? json_decode($json, true) : null;

            if (!is_array($meta)) {
                continue;
            }

            $variantShopId = (int) ($meta['variant']['shop_id'] ?? 0);
            if ($variantShopId !== $shopId) {
                continue;
            }

            $variantKey = trim((string) ($meta['variant_key'] ?? ''));
            if ($variantKey === '') {
                continue;
            }

            $purgeResult = $this->purge([
                'variant_key' => $variantKey,
            ]);

            $results['variants_count']++;
            if (!empty($purgeResult['purged'])) {
                $results['purged_count']++;
            }
            if (!empty($purgeResult['deleted_html'])) {
                $results['deleted_html_count']++;
            }
            if (!empty($purgeResult['deleted_meta'])) {
                $results['deleted_meta_count']++;
            }
        }

        $variantCachePath = $this->getVariantCachePath($shopId);
        if (is_file($variantCachePath)) {
            $results['deleted_variant_cache'] = @unlink($variantCachePath);
        }

        return $results;
    }

    public function getCacheDirectory()
    {
        return rtrim($this->module->getModuleLocalPath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'html';
    }

    public function getVariantDirectory($variantKey)
    {
        $normalizedKey = strtolower(trim((string) $variantKey));
        $first = Tools::substr($normalizedKey, 0, 2);
        $second = Tools::substr($normalizedKey, 2, 2);

        if ($first === false || $first === '') {
            $first = 'xx';
        }

        if ($second === false || $second === '') {
            $second = 'xx';
        }

        return $this->getCacheDirectory()
            . DIRECTORY_SEPARATOR . $first
            . DIRECTORY_SEPARATOR . $second;
    }

    public function getHtmlPath($variantKey)
    {
        return $this->getVariantDirectory($variantKey) . DIRECTORY_SEPARATOR . $variantKey . '.html';
    }

    public function getMetaPath($variantKey)
    {
        return $this->getVariantDirectory($variantKey) . DIRECTORY_SEPARATOR . $variantKey . '.json';
    }

    public function getUsedCssPath($shopId, $variantKey)
    {
        return $this->getVariantDirectory($variantKey) . DIRECTORY_SEPARATOR . $variantKey . '.used.css';
    }

    public function buildUsedCssUrl($shopId, $variantKey, $sha256 = '')
    {
        $context = Context::getContext();
        $base = '';

        if (isset($context->shop) && Validate::isLoadedObject($context->shop)) {
            $base = rtrim((string) $context->shop->getBaseURL(true), '/');
        }

        if ($base === '') {
            $scheme = Tools::usingSecureMode() ? 'https://' : 'http://';
            $host = isset($_SERVER['HTTP_HOST']) ? trim((string) $_SERVER['HTTP_HOST']) : '';
            $base = $host !== '' ? rtrim($scheme . $host, '/') : '';
        }

        $url = $base . '/module/prestaload/cacheasset?shop_id=' . (int) $shopId
            . '&variant_key=' . rawurlencode((string) $variantKey)
            . '&type=used_css';

        $sha = trim((string) $sha256);

        if ($sha !== '') {
            $url .= '&v=' . rawurlencode($sha);
        }

        return $url;
    }

    public function getVariantCachePath($shopId)
    {
        return rtrim($this->module->getModuleLocalPath(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . 'cache'
            . DIRECTORY_SEPARATOR . 'variants'
            . DIRECTORY_SEPARATOR . 'shop-' . (int) $shopId . '.json';
    }

    private function cleanupEmptyFanoutDirectories($variantKey)
    {
        $variantDir = $this->getVariantDirectory($variantKey);
        if (is_dir($variantDir) && $this->isDirectoryEmpty($variantDir)) {
            @rmdir($variantDir);
        }

        $parentDir = dirname($variantDir);
        $cacheDir = $this->getCacheDirectory();
        if ($parentDir !== $cacheDir && is_dir($parentDir) && $this->isDirectoryEmpty($parentDir)) {
            @rmdir($parentDir);
        }
    }

    private function cleanupEmptyAssetDirectories($path)
    {
        $directory = dirname((string) $path);
        if (is_dir($directory) && $this->isDirectoryEmpty($directory)) {
            @rmdir($directory);
        }

        $parent = dirname($directory);
        $assetBase = $this->getCacheDirectory();
        if ($parent !== $assetBase && is_dir($parent) && $this->isDirectoryEmpty($parent)) {
            @rmdir($parent);
        }
    }

    private function isDirectoryEmpty($path)
    {
        $items = @scandir($path);

        return is_array($items) && count($items) <= 2;
    }
}
