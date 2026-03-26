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
        $meta = [
            'artifact_version_id' => $artifactVersionId,
            'variant_key' => $variantKey,
            'variant' => isset($payload['variant']) && is_array($payload['variant']) ? $payload['variant'] : [],
            'normalized_url' => isset($payload['normalized_url']) ? (string) $payload['normalized_url'] : '',
            'html_type' => isset($payload['html_type']) ? (string) $payload['html_type'] : 'optimized',
            'html_bytes' => isset($payload['html_bytes']) ? (int) $payload['html_bytes'] : strlen($html),
            'checksum_sha256' => isset($payload['checksum_sha256']) ? (string) $payload['checksum_sha256'] : hash('sha256', $html),
            'stored_at' => date('c'),
        ];

        $tmpHtmlPath = $htmlPath . '.tmp';
        $tmpMetaPath = $metaPath . '.tmp';

        if (@file_put_contents($tmpHtmlPath, $html, LOCK_EX) === false) {
            throw new Exception('Failed to write cached HTML.');
        }

        $encodedMeta = json_encode($meta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        if ($encodedMeta === false || @file_put_contents($tmpMetaPath, $encodedMeta, LOCK_EX) === false) {
            throw new Exception('Failed to write cache metadata.');
        }

        if (!@rename($tmpHtmlPath, $htmlPath)) {
            @unlink($tmpHtmlPath);
            @unlink($tmpMetaPath);
            throw new Exception('Failed to move cached HTML into place.');
        }

        if (!@rename($tmpMetaPath, $metaPath)) {
            @unlink($tmpMetaPath);
            throw new Exception('Failed to move cache metadata into place.');
        }

        return [
            'stored' => true,
            'path' => $htmlPath,
            'meta_path' => $metaPath,
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
}
