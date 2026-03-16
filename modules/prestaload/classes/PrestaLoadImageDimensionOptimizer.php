<?php
/**
 * Adds missing width and height attributes to local image tags.
 *
 * The optimizer is intentionally conservative:
 * - it only touches <img> tags
 * - it keeps existing width/height values
 * - it only reads files that belong to the current shop host or local paths
 * - it skips external, data, and SVG sources
 */
class PrestaLoadImageDimensionOptimizer
{
    private const IMG_TAG_PATTERN = '/<img\b[^>]*>/i';

    /**
     * @var Context
     */
    private $context;

    /**
     * @var PrestaLoadCacheSettings
     */
    private $settings;

    public function __construct(Context $context, PrestaLoadCacheSettings $settings)
    {
        $this->context = $context;
        $this->settings = $settings;
    }

    /**
     * Adds dimensions only when the feature is enabled and the image file is
     * available on the local filesystem.
     */
    public function optimize($html)
    {
        if (!$this->settings->isImageDimensionsOptimizationEnabled() || !is_string($html) || trim($html) === '') {
            return $html;
        }

        return preg_replace_callback(self::IMG_TAG_PATTERN, function ($matches) {
            return $this->optimizeImgTag($matches[0]);
        }, $html);
    }

    private function optimizeImgTag($tag)
    {
        $attributes = $this->extractTagAttributes($tag);
        $src = isset($attributes['src']) ? html_entity_decode($attributes['src'], ENT_QUOTES, 'UTF-8') : '';

        if (!$this->shouldHandleImage($src, $attributes)) {
            return $tag;
        }

        $imagePath = $this->resolveLocalImagePath($src);
        if ($imagePath === '' || !is_file($imagePath)) {
            return $tag;
        }

        $dimensions = @getimagesize($imagePath);
        if (!is_array($dimensions) || empty($dimensions[0]) || empty($dimensions[1])) {
            return $tag;
        }

        $tag = $this->replaceOrAppendAttribute($tag, 'width', (string) (int) $dimensions[0]);

        return $this->replaceOrAppendAttribute($tag, 'height', (string) (int) $dimensions[1]);
    }

    private function shouldHandleImage($src, array $attributes)
    {
        if (!is_string($src) || trim($src) === '' || strpos(trim($src), 'data:') === 0) {
            return false;
        }

        if (!empty($attributes['width']) && !empty($attributes['height'])) {
            return false;
        }

        return !preg_match('/\.svg(\?|#|$)/i', $src);
    }

    /**
     * Maps a local shop image URL or path to its filesystem path under the
     * Prestashop root directory.
     */
    private function resolveLocalImagePath($src)
    {
        $src = trim((string) $src);
        if ($src === '') {
            return '';
        }

        if (strpos($src, '//') === 0) {
            $shopScheme = $this->getShopScheme();
            $src = $shopScheme . ':' . $src;
        }

        $path = '';
        if (preg_match('#^https?://#i', $src)) {
            $parts = parse_url($src);
            if ($parts === false || empty($parts['host']) || !$this->isShopHost($parts['host'])) {
                return '';
            }

            $path = isset($parts['path']) ? (string) $parts['path'] : '';
        } else {
            $path = strpos($src, '/') === 0 ? $src : '/' . ltrim($src, '/');
        }

        $path = rawurldecode($path);
        if ($path === '' || strpos($path, '..') !== false) {
            return '';
        }

        return rtrim(_PS_ROOT_DIR_, '/') . $path;
    }

    private function isShopHost($host)
    {
        $shopHost = parse_url($this->getShopBaseUrl(), PHP_URL_HOST);

        return is_string($shopHost) && Tools::strtolower((string) $shopHost) === Tools::strtolower((string) $host);
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

    private function extractTagAttributes($tag)
    {
        $attributes = [];

        if (!preg_match_all('/([a-zA-Z_:][a-zA-Z0-9_:\-]*)\s*=\s*(["\'])(.*?)\2/s', $tag, $matches, PREG_SET_ORDER)) {
            return $attributes;
        }

        foreach ($matches as $match) {
            $attributes[Tools::strtolower((string) $match[1])] = $match[3];
        }

        return $attributes;
    }

    private function replaceOrAppendAttribute($tag, $attribute, $value)
    {
        $pattern = '/(\b' . preg_quote($attribute, '/') . '\s*=\s*)(["\']).*?\2/i';
        $replacement = '$1"' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';

        if (preg_match($pattern, $tag)) {
            return preg_replace($pattern, $replacement, $tag, 1);
        }

        return preg_replace('/\s*\/?>$/', ' ' . $attribute . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"$0', $tag, 1);
    }
}
