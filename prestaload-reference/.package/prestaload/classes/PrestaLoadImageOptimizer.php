<?php
/**
 * Rewrites raster image URLs to ImgProxy URLs in cached anonymous HTML.
 */

class PrestaLoadImageOptimizer
{
    private const IMG_TAG_PATTERN = '/<img\b[^>]*>/i';
    private const SOURCE_TAG_PATTERN = '/<source\b[^>]*>/i';
    private const RASTER_EXTENSIONS_PATTERN = '/\.(jpe?g|png|gif|bmp|webp|avif)(\?|#|$)/i';

    /**
     * @var PrestaLoadCacheSettings
     */
    private $settings;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var PrestaLoadImgProxyUrlBuilder
     */
    private $urlBuilder;

    /**
     * @var PrestaLoadImageDimensionOptimizer
     */
    private $dimensionOptimizer;

    /**
     * @var PrestaLoadImageLoadingOptimizer
     */
    private $loadingOptimizer;

    public function __construct(
        Context $context,
        PrestaLoadCacheSettings $settings,
        PrestaLoadImgProxyUrlBuilder $urlBuilder,
        PrestaLoadImageDimensionOptimizer $dimensionOptimizer,
        PrestaLoadImageLoadingOptimizer $loadingOptimizer
    ) {
        $this->context = $context;
        $this->settings = $settings;
        $this->urlBuilder = $urlBuilder;
        $this->dimensionOptimizer = $dimensionOptimizer;
        $this->loadingOptimizer = $loadingOptimizer;
    }

    /**
     * Applies ImgProxy rewrites to safe raster image references.
     */
    public function optimize($html)
    {
        if (!is_string($html) || trim($html) === '') {
            return $html;
        }

        $html = $this->dimensionOptimizer->optimize($html);
        $html = $this->loadingOptimizer->optimize($html);

        if (!$this->settings->isImageOptimizationEnabled()) {
            return $html;
        }

        $html = preg_replace_callback(self::IMG_TAG_PATTERN, function ($matches) {
            return $this->rewriteImgTag($matches[0]);
        }, $html);

        return preg_replace_callback(self::SOURCE_TAG_PATTERN, function ($matches) {
            return $this->rewriteSourceTag($matches[0]);
        }, $html);
    }

    /**
     * Rewrites the main `src` of an image and keeps layout dimensions intact.
     */
    private function rewriteImgTag($tag)
    {
        $attributes = $this->extractTagAttributes($tag);
        $src = isset($attributes['src']) ? html_entity_decode($attributes['src'], ENT_QUOTES, 'UTF-8') : '';

        if (!$this->isOptimizableImageUrl($src)) {
            return $tag;
        }

        $rewrittenUrl = $this->urlBuilder->buildUrl($this->resolveAssetUrl($src), $this->extractDimensions($attributes));

        return $this->replaceOrAppendAttribute($tag, 'src', $rewrittenUrl);
    }

    /**
     * Rewrites `srcset` on picture sources so responsive image selections also
     * go through ImgProxy.
     */
    private function rewriteSourceTag($tag)
    {
        $attributes = $this->extractTagAttributes($tag);
        $srcset = isset($attributes['srcset']) ? html_entity_decode($attributes['srcset'], ENT_QUOTES, 'UTF-8') : '';

        if ($srcset === '') {
            return $tag;
        }

        $rewrittenCandidates = [];
        foreach (explode(',', $srcset) as $candidate) {
            $candidate = trim((string) $candidate);
            if ($candidate === '') {
                continue;
            }

            $parts = preg_split('/\s+/', $candidate, 2);
            $candidateUrl = isset($parts[0]) ? $parts[0] : '';
            $descriptor = isset($parts[1]) ? $parts[1] : '';

            if (!$this->isOptimizableImageUrl($candidateUrl)) {
                $rewrittenCandidates[] = $candidate;
                continue;
            }

            $candidateDimensions = $this->extractDimensionsFromDescriptor($descriptor);
            $rewrittenUrl = $this->urlBuilder->buildUrl($this->resolveAssetUrl($candidateUrl), $candidateDimensions);
            $rewrittenCandidates[] = trim($rewrittenUrl . ' ' . $descriptor);
        }

        if (empty($rewrittenCandidates)) {
            return $tag;
        }

        return $this->replaceOrAppendAttribute($tag, 'srcset', implode(', ', $rewrittenCandidates));
    }

    /**
     * Keeps the optimizer away from SVGs, data URIs, already proxied URLs,
     * and lazy placeholders that are not the real image source.
     */
    private function isOptimizableImageUrl($url)
    {
        if (!is_string($url) || trim($url) === '') {
            return false;
        }

        $normalizedUrl = trim((string) $url);

        if (strpos($normalizedUrl, 'data:') === 0) {
            return false;
        }

        if (strpos($normalizedUrl, rtrim($this->settings->getImgProxyBaseUrl(), '/')) === 0) {
            return false;
        }

        return (bool) preg_match(self::RASTER_EXTENSIONS_PATTERN, $normalizedUrl);
    }

    /**
     * ImgProxy must always receive an absolute source URL it can fetch itself.
     */
    private function resolveAssetUrl($url)
    {
        $url = trim((string) $url);
        if ($url === '' || preg_match('#^https?://#i', $url)) {
            return $url;
        }

        if (strpos($url, '//') === 0) {
            $shopBase = $this->getShopBaseUrl();
            $parts = parse_url($shopBase);
            $scheme = isset($parts['scheme']) ? $parts['scheme'] : 'https';

            return $scheme . ':' . $url;
        }

        $shopBase = rtrim($this->getShopBaseUrl(), '/');
        if (strpos($url, '/') === 0) {
            return $shopBase . $url;
        }

        return $shopBase . '/' . ltrim($url, '/');
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

    /**
     * Extracts quoted attributes from raw HTML tags without assuming order.
     */
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

    /**
     * Reads width and height when the markup already provides them.
     */
    private function extractDimensions(array $attributes)
    {
        $dimensions = [];

        if (!empty($attributes['width']) && ctype_digit((string) $attributes['width'])) {
            $dimensions['width'] = (int) $attributes['width'];
        }

        if (!empty($attributes['height']) && ctype_digit((string) $attributes['height'])) {
            $dimensions['height'] = (int) $attributes['height'];
        }

        return $dimensions;
    }

    /**
     * Uses `640w` style descriptors when available.
     */
    private function extractDimensionsFromDescriptor($descriptor)
    {
        $dimensions = [];

        if (preg_match('/(\d+)w/i', (string) $descriptor, $match)) {
            $dimensions['width'] = (int) $match[1];
        }

        return $dimensions;
    }

    /**
     * Rewrites or appends an attribute while preserving the rest of the tag.
     */
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
