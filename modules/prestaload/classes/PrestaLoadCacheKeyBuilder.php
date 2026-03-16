<?php
/**
 * Builds cache keys from request dimensions that can change anonymous HTML output.
 */

class PrestaLoadCacheKeyBuilder
{
    private $context;
    private $modulePath;

    public function __construct(Context $context, $modulePath)
    {
        $this->context = $context;
        $this->modulePath = rtrim((string) $modulePath, '/');
    }

    /**
     * Returns the exact dimensions the cache key relies on plus the final hash.
     */
    public function buildContext()
    {
        $parts = [
            'scheme' => $this->isSecureRequest() ? 'https' : 'http',
            'host' => isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : '',
            'uri' => isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/',
            'shop' => isset($this->context->shop->id) ? (int) $this->context->shop->id : 0,
            'lang' => isset($this->context->language->id) ? (int) $this->context->language->id : 0,
            'currency' => isset($this->context->currency->id) ? (int) $this->context->currency->id : 0,
            'country' => isset($this->context->country->id) ? (int) $this->context->country->id : 0,
            'device' => method_exists($this->context, 'getDevice') ? (string) $this->context->getDevice() : 'desktop',
            // These versions let the cache key move forward when PrestaLoad
            // settings or asset rules change, even if old cache files cannot
            // be deleted because of shared-host ownership quirks.
            'runtime_version' => $this->getFileVersion($this->modulePath . '/cache/runtime-config.php'),
            'asset_rules_version' => $this->getFileVersion($this->modulePath . '/cache/asset-rules.json'),
        ];

        return [
            'parts' => $parts,
            'key' => sha1(json_encode($parts)),
        ];
    }

    public function buildKey()
    {
        $context = $this->buildContext();

        return $context['key'];
    }

    private function isSecureRequest()
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }

        if (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) {
            return true;
        }

        return false;
    }

    private function getFileVersion($path)
    {
        return is_file($path) ? (int) @filemtime($path) : 0;
    }
}
