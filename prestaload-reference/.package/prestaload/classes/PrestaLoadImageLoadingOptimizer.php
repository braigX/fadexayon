<?php
/**
 * Applies conservative image loading hints to public HTML.
 *
 * The goal is to keep the first visible images eager while lazy-loading the
 * remaining images so below-the-fold media does not compete with LCP.
 *
 * Inline background images are handled separately and conservatively:
 * - obvious hero/banner/slider sections are kept eager
 * - later inline backgrounds are swapped in via IntersectionObserver
 */

class PrestaLoadImageLoadingOptimizer
{
    private const IMG_TAG_PATTERN = '/<img\b[^>]*>/i';
    private const INLINE_BACKGROUND_TAG_PATTERN = '/<(div|section|figure|li|a|span)\b[^>]*style=(["\'])(.*?)\2[^>]*>/is';

    /**
     * @var PrestaLoadCacheSettings
     */
    private $settings;

    private $lazyBackgroundCount = 0;

    public function __construct(PrestaLoadCacheSettings $settings)
    {
        $this->settings = $settings;
    }

    public function optimize($html)
    {
        if (!$this->settings->isImageLoadingOptimizationEnabled() || !is_string($html) || trim($html) === '') {
            return $html;
        }

        $imageIndex = 0;
        $html = preg_replace_callback(self::IMG_TAG_PATTERN, function ($matches) use (&$imageIndex) {
            $imageIndex++;

            return $this->optimizeImgTag($matches[0], $imageIndex);
        }, $html);

        if ($this->settings->isBackgroundImageLazyLoadingEnabled()) {
            $this->lazyBackgroundCount = 0;
            $html = preg_replace_callback(self::INLINE_BACKGROUND_TAG_PATTERN, function ($matches) {
                return $this->optimizeInlineBackgroundTag($matches[0]);
            }, $html);
            $html = $this->injectBackgroundLazyLoader($html);
        }

        return $html;
    }

    /**
     * Keep the earliest or explicitly critical images eager. Other raster
     * images become lazy by default.
     */
    private function optimizeImgTag($tag, $imageIndex)
    {
        $attributes = $this->extractTagAttributes($tag);
        $src = isset($attributes['src']) ? html_entity_decode($attributes['src'], ENT_QUOTES, 'UTF-8') : '';

        if (!$this->isOptimizableImage($src, $attributes)) {
            return $tag;
        }

        if ($this->shouldKeepEager($attributes, $imageIndex)) {
            $tag = $this->replaceOrAppendAttribute($tag, 'loading', 'eager');
            $tag = $this->replaceOrAppendAttribute($tag, 'fetchpriority', 'high');

            return $this->replaceOrAppendAttribute($tag, 'decoding', 'sync');
        }

        $tag = $this->replaceOrAppendAttribute($tag, 'loading', 'lazy');
        $tag = $this->replaceOrAppendAttribute($tag, 'decoding', 'async');

        return $this->replaceOrAppendAttribute($tag, 'fetchpriority', 'low');
    }

    private function optimizeInlineBackgroundTag($tag)
    {
        $attributes = $this->extractTagAttributes($tag);
        $style = isset($attributes['style']) ? html_entity_decode($attributes['style'], ENT_QUOTES, 'UTF-8') : '';

        if ($style === '' || stripos($style, 'background-image') === false) {
            return $tag;
        }

        if (!preg_match('/background-image\s*:\s*url\((["\']?)(.*?)\1\)/i', $style, $match)) {
            return $tag;
        }

        $backgroundUrl = trim((string) $match[2]);
        if ($backgroundUrl === '' || strpos($backgroundUrl, 'data:') === 0) {
            return $tag;
        }

        if ($this->shouldKeepBackgroundEager($attributes)) {
            return $tag;
        }

        ++$this->lazyBackgroundCount;

        $tag = $this->replaceOrAppendAttribute($tag, 'data-prestaload-bg', $backgroundUrl);
        $tag = $this->replaceOrAppendAttribute($tag, 'data-prestaload-bg-lazy', '1');

        $className = isset($attributes['class']) ? trim((string) $attributes['class']) : '';
        $className = trim($className . ' prestaload-bg-lazy');
        $tag = $this->replaceOrAppendAttribute($tag, 'class', $className);

        $updatedStyle = preg_replace('/background-image\s*:\s*url\((["\']?)(.*?)\1\)\s*;?/i', 'background-image:none;', $style, 1);
        $updatedStyle = trim((string) $updatedStyle);
        if ($updatedStyle === '') {
            $updatedStyle = 'background-image:none;';
        }

        return $this->replaceOrAppendAttribute($tag, 'style', $updatedStyle);
    }

    private function injectBackgroundLazyLoader($html)
    {
        if ($this->lazyBackgroundCount === 0 || strpos($html, 'data-prestaload-bg-lazy="1"') === false) {
            return $html;
        }

        $script = <<<HTML
<script data-prestaload-bg-loader="1">
(function(){var nodes=document.querySelectorAll('[data-prestaload-bg-lazy="1"]');if(!nodes.length){return;}var apply=function(node){var url=node.getAttribute('data-prestaload-bg');if(!url){return;}node.style.backgroundImage='url(\"'+url.replace(/"/g,'\\\\\"')+'\")';node.removeAttribute('data-prestaload-bg-lazy');};if(!('IntersectionObserver' in window)){Array.prototype.forEach.call(nodes,apply);return;}var observer=new IntersectionObserver(function(entries){entries.forEach(function(entry){if(!entry.isIntersecting){return;}apply(entry.target);observer.unobserve(entry.target);});},{rootMargin:'200px 0px'});Array.prototype.forEach.call(nodes,function(node){observer.observe(node);});}());
</script>
HTML;

        if (stripos($html, '</body>') !== false) {
            return preg_replace('/<\/body>/i', $script . '</body>', $html, 1);
        }

        return $html . $script;
    }

    private function shouldKeepEager(array $attributes, $imageIndex)
    {
        if (!empty($attributes['loading']) && Tools::strtolower((string) $attributes['loading']) === 'eager') {
            return true;
        }

        if (!empty($attributes['fetchpriority']) && Tools::strtolower((string) $attributes['fetchpriority']) === 'high') {
            return true;
        }

        $className = isset($attributes['class']) ? Tools::strtolower((string) $attributes['class']) : '';
        $id = isset($attributes['id']) ? Tools::strtolower((string) $attributes['id']) : '';

        if (
            strpos($className, 'logo') !== false
            || strpos($className, 'hero') !== false
            || strpos($className, 'banner') !== false
            || strpos($className, 'slider') !== false
            || strpos($id, 'logo') !== false
            || strpos($id, 'hero') !== false
            || strpos($id, 'banner') !== false
        ) {
            return true;
        }

        return $imageIndex <= 2;
    }

    private function shouldKeepBackgroundEager(array $attributes)
    {
        $className = isset($attributes['class']) ? Tools::strtolower((string) $attributes['class']) : '';
        $id = isset($attributes['id']) ? Tools::strtolower((string) $attributes['id']) : '';

        if (
            strpos($className, 'hero') !== false
            || strpos($className, 'banner') !== false
            || strpos($className, 'slider') !== false
            || strpos($className, 'carousel') !== false
            || strpos($className, 'elementor-top-section') !== false
            || strpos($id, 'hero') !== false
            || strpos($id, 'banner') !== false
            || strpos($id, 'slider') !== false
        ) {
            return true;
        }

        return $this->lazyBackgroundCount === 0;
    }

    private function isOptimizableImage($src, array $attributes)
    {
        if (!is_string($src) || trim($src) === '' || strpos(trim($src), 'data:') === 0) {
            return false;
        }

        $className = isset($attributes['class']) ? Tools::strtolower((string) $attributes['class']) : '';
        if (strpos($className, 'emoji') !== false || strpos($className, 'avatar') !== false) {
            return false;
        }

        return true;
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
