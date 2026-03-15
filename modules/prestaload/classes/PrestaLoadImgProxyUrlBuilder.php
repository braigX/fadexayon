<?php
/**
 * Builds ImgProxy URLs for raster images.
 *
 * Uses the official imgproxy processing URL pattern:
 * /%signature/%processing_options/plain/%source_url@%extension
 * Source: https://docs.imgproxy.net/usage/processing
 */

class PrestaLoadImgProxyUrlBuilder
{
    /**
     * Settings provide the imgproxy base URL and feature toggle.
     *
     * @var PrestaLoadCacheSettings
     */
    private $settings;

    public function __construct(PrestaLoadCacheSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Builds a conservative imgproxy URL for the given source image.
     *
     * Strategy:
     * - keep processing unsigned with `unsafe` for the local development setup
     * - resize only if dimensions are available
     * - convert raster images to WebP
     * - keep source URL plain for readability and easy debugging
     */
    public function buildUrl($sourceUrl, array $dimensions = [])
    {
        $baseUrl = rtrim($this->settings->getImgProxyBaseUrl(), '/');
        if ($baseUrl === '' || !is_string($sourceUrl) || trim($sourceUrl) === '') {
            return $sourceUrl;
        }

        $processingOptions = [];
        $width = !empty($dimensions['width']) ? max(1, (int) $dimensions['width']) : 0;
        $height = !empty($dimensions['height']) ? max(1, (int) $dimensions['height']) : 0;

        if ($width > 0 || $height > 0) {
            $processingOptions[] = 'rs:fit:' . $width . ':' . $height . ':0';
        }

        $processingOptions[] = 'q:' . max(30, min(95, (int) $this->settings->getImgProxyQuality()));

        $path = '/'
            . implode('/', $processingOptions)
            . '/plain/'
            . $this->encodePlainSourceUrl($sourceUrl)
            . '@webp';

        $signature = $this->buildSignature($path);

        return $baseUrl . '/' . $signature . $path;
    }

    /**
     * Encodes the source URL for the plain imgproxy source mode.
     *
     * This mirrors the local helper script so generated URLs match the Docker
     * setup already used in your ImgProxy project.
     */
    private function encodePlainSourceUrl($sourceUrl)
    {
        return str_replace(
            ['%', '?', '@'],
            ['%25', '%3F', '%40'],
            $sourceUrl
        );
    }

    /**
     * Uses signed URLs when key and salt are configured, otherwise falls back
     * to imgproxy's `unsafe` mode for development setups.
     */
    private function buildSignature($path)
    {
        $keyHex = $this->settings->getImgProxyKey();
        $saltHex = $this->settings->getImgProxySalt();

        if ($keyHex === '' || $saltHex === '') {
            return 'unsafe';
        }

        $key = @hex2bin($keyHex);
        $salt = @hex2bin($saltHex);

        if ($key === false || $salt === false || $key === '' || $salt === '') {
            return 'unsafe';
        }

        $hash = hash_hmac('sha256', $salt . $path, $key, true);

        return $this->base64UrlEncode($hash);
    }

    /**
     * ImgProxy signatures use URL-safe base64 without padding.
     */
    private function base64UrlEncode($value)
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
