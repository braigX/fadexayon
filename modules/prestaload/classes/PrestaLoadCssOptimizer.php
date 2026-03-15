<?php
/**
 * Applies conservative stylesheet optimizations to cached HTML.
 *
 * V1 intentionally limits itself to stylesheets injected outside <head>.
 * Those stylesheets are usually page-builder or module assets added late in the
 * document, and deferring them is much safer than rewriting theme-critical CSS.
 */

class PrestaLoadCssOptimizer
{
    /**
     * Attribute order is not stable across themes, so parse raw link tags first.
     */
    private const LINK_TAG_PATTERN = '/<link\b[^>]*>/i';

    /**
     * Module settings decide whether this optimizer is active.
     *
     * @var PrestaLoadCacheSettings
     */
    private $settings;

    public function __construct(PrestaLoadCacheSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Rewrites safe late stylesheets to a non-blocking preload pattern.
     */
    public function optimize($html)
    {
        if (!$this->settings->isCssOptimizationEnabled()) {
            return $html;
        }

        if (!is_string($html) || trim($html) === '') {
            return $html;
        }

        $headEndOffset = stripos($html, '</head>');
        if ($headEndOffset === false) {
            return $html;
        }

        if (!preg_match_all(self::LINK_TAG_PATTERN, $html, $matches, PREG_OFFSET_CAPTURE)) {
            return $html;
        }

        $result = '';
        $cursor = 0;

        foreach ($matches[0] as $match) {
            $tag = $match[0];
            $tagOffset = (int) $match[1];
            $attributes = $this->extractLinkAttributes($tag);

            $result .= substr($html, $cursor, $tagOffset - $cursor);
            $result .= $this->shouldDeferStylesheet($attributes, $tagOffset, $headEndOffset)
                ? $this->buildDeferredStylesheetTag($tag, $attributes)
                : $tag;

            $cursor = $tagOffset + strlen($tag);
        }

        $result .= substr($html, $cursor);

        return $result;
    }

    /**
     * Only defer stylesheets that are very likely to be non-critical.
     *
     * Rules:
     * - must be a normal stylesheet
     * - must have an href
     * - must not already be preload/async-managed
     * - must be outside <head>
     * - must not look like font delivery CSS
     * - must not use special media modes
     */
    private function shouldDeferStylesheet(array $attributes, $tagOffset, $headEndOffset)
    {
        $rel = isset($attributes['rel']) ? Tools::strtolower((string) $attributes['rel']) : '';
        $href = isset($attributes['href']) ? html_entity_decode($attributes['href'], ENT_QUOTES, 'UTF-8') : '';
        $media = isset($attributes['media']) ? Tools::strtolower(trim((string) $attributes['media'])) : '';

        if ($rel !== 'stylesheet' || $href === '') {
            return false;
        }

        if (isset($attributes['data-prestaload-deferred'])) {
            return false;
        }

        if (isset($attributes['onload'])) {
            return false;
        }

        if ($media !== '' && $media !== 'all' && $media !== 'screen') {
            return false;
        }

        if ($this->looksLikeFontStylesheet($href)) {
            return false;
        }

        if ((int) $tagOffset < (int) $headEndOffset) {
            return false;
        }

        return true;
    }

    /**
     * Convert the blocking stylesheet tag to a preload+onload pattern and keep a
     * noscript fallback so the page still styles correctly without JavaScript.
     */
    private function buildDeferredStylesheetTag($originalTag, array $attributes)
    {
        $deferredTag = $originalTag;
        $deferredTag = $this->replaceOrAppendAttribute($deferredTag, 'rel', 'preload');
        $deferredTag = $this->replaceOrAppendAttribute($deferredTag, 'as', 'style');
        $deferredTag = $this->replaceOrAppendAttribute($deferredTag, 'onload', "this.onload=null;this.rel='stylesheet'");
        $deferredTag = $this->replaceOrAppendAttribute($deferredTag, 'data-prestaload-deferred', '1');

        $fallbackTag = $originalTag;
        if (isset($attributes['data-prestaload-deferred'])) {
            $fallbackTag = preg_replace('/\sdata-prestaload-deferred=(["\']).*?\1/i', '', $fallbackTag);
        }

        return $deferredTag . '<noscript>' . $fallbackTag . '</noscript>';
    }

    /**
     * Do not let the CSS optimizer touch font delivery. Fonts are handled by
     * the dedicated font optimizer so responsibilities stay separated.
     */
    private function looksLikeFontStylesheet($href)
    {
        $normalizedHref = Tools::strtolower((string) $href);

        return strpos($normalizedHref, 'fonts.googleapis.com') !== false
            || strpos($normalizedHref, 'font-awesome') !== false
            || strpos($normalizedHref, '/fonts/') !== false
            || strpos($normalizedHref, 'fontawesome') !== false
            || strpos($normalizedHref, 'ce-icons') !== false;
    }

    /**
     * Parses quoted attributes from a raw link tag without assuming any order.
     */
    private function extractLinkAttributes($tag)
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
