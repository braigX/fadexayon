<?php
/**
 * Applies conservative HTML font optimizations that are safe across shops.
 *
 * V1 focuses on two low-risk improvements:
 * - ensure Google Fonts stylesheets request `display=swap`
 * - add preconnect hints for external font origins referenced by the page
 */

class PrestaLoadFontOptimizer
{
    /**
     * Font stylesheet hosts that are safe to normalize generically.
     */
    private const GOOGLE_FONTS_HOST = 'fonts.googleapis.com';
    private const GOOGLE_STATIC_HOST = 'fonts.gstatic.com';

    /**
     * Font file extensions used when scanning existing preload tags.
     */
    private const FONT_EXTENSIONS_PATTERN = '/\.(woff2|woff|ttf|otf|eot)(\?|#|$)/i';

    /**
     * Module settings control whether optimization is active.
     *
     * @var PrestaLoadCacheSettings
     */
    private $settings;

    public function __construct(PrestaLoadCacheSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Returns optimized HTML for anonymous cacheable pages.
     */
    public function optimize($html)
    {
        if (!$this->settings->isFontOptimizationEnabled()) {
            return $html;
        }

        if (!is_string($html) || trim($html) === '') {
            return $html;
        }

        if (stripos($html, '<head') === false) {
            return $html;
        }

        $fontOrigins = [];
        $optimizedHtml = $this->normalizeGoogleFontStylesheets($html, $fontOrigins);

        return $this->injectPreconnectHints($optimizedHtml, $fontOrigins);
    }

    /**
     * Updates external font stylesheet URLs in-place and collects origins that
     * should be preconnected before render-blocking stylesheets are fetched.
     */
    private function normalizeGoogleFontStylesheets($html, array &$fontOrigins)
    {
        $pattern = '/<link\b[^>]*rel=(["\'])(?:stylesheet|preload)\1[^>]*href=(["\'])([^"\']+)\2[^>]*>/i';

        return preg_replace_callback($pattern, function ($matches) use (&$fontOrigins) {
            $tag = $matches[0];
            $href = html_entity_decode($matches[3], ENT_QUOTES, 'UTF-8');
            $urlParts = @parse_url($href);

            if (!is_array($urlParts) || empty($urlParts['host'])) {
                return $tag;
            }

            $host = Tools::strtolower((string) $urlParts['host']);

            if ($host === self::GOOGLE_FONTS_HOST) {
                $updatedHref = $this->ensureDisplaySwap($href);
                $fontOrigins['https://' . self::GOOGLE_FONTS_HOST] = true;
                $fontOrigins['https://' . self::GOOGLE_STATIC_HOST] = true;

                if ($updatedHref !== $href) {
                    return str_replace($matches[3], htmlspecialchars($updatedHref, ENT_QUOTES, 'UTF-8'), $tag);
                }

                return $tag;
            }

            if ($this->isExternalFontAsset($href, $urlParts)) {
                $scheme = !empty($urlParts['scheme']) ? $urlParts['scheme'] : 'https';
                $fontOrigins[$scheme . '://' . $urlParts['host']] = true;
            }

            return $tag;
        }, $html);
    }

    /**
     * Google Fonts supports the `display` query parameter. `swap` removes the
     * invisible-text delay with minimal risk.
     */
    private function ensureDisplaySwap($href)
    {
        $parts = @parse_url($href);
        if (!is_array($parts)) {
            return $href;
        }

        $query = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        if (isset($query['display']) && Tools::strtolower((string) $query['display']) === 'swap') {
            return $href;
        }

        $query['display'] = 'swap';
        $parts['query'] = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        return $this->buildUrl($parts);
    }

    /**
     * Inject preconnects once, just before the closing head tag.
     */
    private function injectPreconnectHints($html, array $fontOrigins)
    {
        if (empty($fontOrigins)) {
            return $html;
        }

        $hints = [];
        foreach (array_keys($fontOrigins) as $origin) {
            if ($this->hasPreconnectForOrigin($html, $origin)) {
                continue;
            }

            $crossorigin = $this->needsCrossorigin($origin) ? ' crossorigin' : '';
            $hints[] = '<link rel="preconnect" href="' . htmlspecialchars($origin, ENT_QUOTES, 'UTF-8') . '"' . $crossorigin . '>';
        }

        if (empty($hints)) {
            return $html;
        }

        $injected = implode("\n", $hints) . "\n</head>";

        return preg_replace('/<\/head>/i', $injected, $html, 1);
    }

    /**
     * Reuses existing hints if the theme already added them.
     */
    private function hasPreconnectForOrigin($html, $origin)
    {
        $quotedOrigin = preg_quote($origin, '/');

        return (bool) preg_match('/<link\b[^>]*rel=(["\'])preconnect\1[^>]*href=(["\'])' . $quotedOrigin . '\2/i', $html);
    }

    /**
     * Only crossorigin-enabled hosts need the extra attribute.
     */
    private function needsCrossorigin($origin)
    {
        return strpos($origin, self::GOOGLE_STATIC_HOST) !== false;
    }

    /**
     * Captures generic external font assets without hardcoding one specific shop.
     */
    private function isExternalFontAsset($href, array $urlParts)
    {
        if (empty($urlParts['host'])) {
            return false;
        }

        $shopHost = Tools::strtolower((string) Tools::getHttpHost(false, false));
        $assetHost = Tools::strtolower((string) $urlParts['host']);

        if ($shopHost !== '' && $assetHost === $shopHost) {
            return false;
        }

        return (bool) preg_match(self::FONT_EXTENSIONS_PATTERN, $href);
    }

    /**
     * Small local URL builder so we can safely rewrite query strings.
     */
    private function buildUrl(array $parts)
    {
        $url = '';

        if (!empty($parts['scheme'])) {
            $url .= $parts['scheme'] . '://';
        }

        if (!empty($parts['user'])) {
            $url .= $parts['user'];
            if (!empty($parts['pass'])) {
                $url .= ':' . $parts['pass'];
            }
            $url .= '@';
        }

        if (!empty($parts['host'])) {
            $url .= $parts['host'];
        }

        if (!empty($parts['port'])) {
            $url .= ':' . $parts['port'];
        }

        $url .= isset($parts['path']) ? $parts['path'] : '';

        if (!empty($parts['query'])) {
            $url .= '?' . $parts['query'];
        }

        if (!empty($parts['fragment'])) {
            $url .= '#' . $parts['fragment'];
        }

        return $url;
    }
}
