<?php
/**
 * Builds cache keys from request dimensions that can change anonymous HTML output.
 */

class PrestaLoadCacheKeyBuilder
{
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function buildKey()
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
        ];

        return sha1(json_encode($parts));
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
}
