<?php
/**
 * Applies admin-defined asset rules to the rendered HTML.
 *
 * Rules are page-scoped by request path and intentionally exact-match the
 * asset URL from the scan. That keeps V1 predictable and avoids broad pattern
 * rewrites that could break unrelated pages.
 */

class PrestaLoadAssetRuleApplier
{
    private const SCRIPT_TAG_PATTERN = '/<script\b[^>]*\bsrc=(["\'])(.*?)\1[^>]*>\s*<\/script>/is';
    private const LINK_TAG_PATTERN = '/<link\b[^>]*>/i';

    /**
     * @var PrestaLoadAssetRuleStore
     */
    private $ruleStore;

    /**
     * @var PrestaLoadCssOptimizer
     */
    private $cssOptimizer;

    /**
     * @var PrestaLoadAssetMinifier
     */
    private $assetMinifier;

    /**
     * @var Context
     */
    private $context;

    public function __construct(Context $context, PrestaLoadAssetRuleStore $ruleStore, PrestaLoadCssOptimizer $cssOptimizer, PrestaLoadAssetMinifier $assetMinifier)
    {
        $this->context = $context;
        $this->ruleStore = $ruleStore;
        $this->cssOptimizer = $cssOptimizer;
        $this->assetMinifier = $assetMinifier;
    }

    public function optimize($html)
    {
        if (!is_string($html) || trim($html) === '') {
            return $html;
        }

        $pageKey = $this->resolveCurrentPageKey();
        if ($pageKey === '') {
            return $html;
        }

        $rules = $this->ruleStore->getRulesForPage($pageKey);
        if (empty($rules)) {
            return $html;
        }

        $html = $this->applyScriptRules($html, $rules);

        return $this->applyLinkRules($html, $rules);
    }

    private function applyScriptRules($html, array $rules)
    {
        return preg_replace_callback(self::SCRIPT_TAG_PATTERN, function ($matches) use ($rules) {
            $tag = $matches[0];
            $src = html_entity_decode($matches[2], ENT_QUOTES, 'UTF-8');
            $rule = $this->findRuleForUrl($rules, $src, 'js');

            if ($rule === null) {
                return $tag;
            }

            if ($this->isFlagEnabled($rule, 'disable')) {
                return '';
            }

            $effectiveSrc = $src;
            if ($this->isFlagEnabled($rule, 'minify')) {
                $minifiedUrl = $this->assetMinifier->getMinifiedAssetUrl($src, 'js');
                if ($minifiedUrl !== '') {
                    $effectiveSrc = $minifiedUrl;
                    $tag = $this->replaceScriptSrc($tag, $minifiedUrl);
                }
            }

            if ($this->isFlagEnabled($rule, 'load_after_window_load')) {
                return $this->buildWindowLoadScriptTag($effectiveSrc);
            }

            if ($this->isFlagEnabled($rule, 'defer')) {
                return $this->buildDeferredScriptTag($tag);
            }

            return $tag;
        }, $html);
    }

    private function applyLinkRules($html, array $rules)
    {
        if (!preg_match_all(self::LINK_TAG_PATTERN, $html, $matches, PREG_OFFSET_CAPTURE)) {
            return $html;
        }

        $result = '';
        $cursor = 0;

        foreach ($matches[0] as $match) {
            $tag = $match[0];
            $offset = (int) $match[1];
            $attributes = $this->extractAttributes($tag);
            $href = isset($attributes['href']) ? html_entity_decode($attributes['href'], ENT_QUOTES, 'UTF-8') : '';
            $rel = isset($attributes['rel']) ? Tools::strtolower((string) $attributes['rel']) : '';
            $rule = $href !== '' ? $this->findRuleForUrl($rules, $href, 'css') : null;

            $replacement = $tag;
            if ($rule !== null && $rel === 'stylesheet') {
                if ($this->isFlagEnabled($rule, 'disable')) {
                    $replacement = '';
                } else {
                    $effectiveTag = $tag;
                    if ($this->isFlagEnabled($rule, 'minify')) {
                    $minifiedUrl = $this->assetMinifier->getMinifiedAssetUrl($href, 'css');
                        if ($minifiedUrl !== '') {
                            $effectiveTag = $this->replaceOrAppendAttribute($tag, 'href', $minifiedUrl);
                        }
                    }

                    if ($this->isFlagEnabled($rule, 'defer')) {
                        $replacement = $this->buildDeferredStylesheetTag($effectiveTag);
                    } else {
                        $replacement = $effectiveTag;
                    }
                }
            }

            $result .= substr($html, $cursor, $offset - $cursor);
            $result .= $replacement;
            $cursor = $offset + strlen($tag);
        }

        $result .= substr($html, $cursor);

        return $result;
    }

    private function findRuleForUrl(array $rules, $url, $type)
    {
        $normalizedUrl = $this->normalizeAssetUrl($url);

        foreach ($rules as $rule) {
            if (
                isset($rule['asset_url'], $rule['asset_type'], $rule['action'])
                && $this->normalizeAssetUrl($rule['asset_url']) === $normalizedUrl
                && $rule['asset_type'] === $type
                && $rule['action'] !== 'keep'
            ) {
                return $rule;
            }
        }

        return null;
    }

    private function isFlagEnabled(array $rule, $flag)
    {
        if (!empty($rule[$flag])) {
            return true;
        }

        return isset($rule['action']) && $rule['action'] === $flag;
    }

    /**
     * Maps the current request to the same page keys used by the admin scan
     * registry.
     */
    private function resolveCurrentPageKey()
    {
        $controller = Tools::strtolower((string) Tools::getValue('controller', 'index'));

        switch ($controller) {
            case 'index':
                return 'home';
            case 'category':
                return 'category:' . (int) Tools::getValue('id_category');
            case 'product':
                return 'product:' . (int) Tools::getValue('id_product');
            case 'cms':
                return 'cms:' . (int) Tools::getValue('id_cms');
            default:
                return '';
        }
    }

    private function extractAttributes($tag)
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
     * CSS deferral here mirrors the module's existing conservative preload
     * pattern but applies only to admin-selected assets.
     */
    private function buildDeferredStylesheetTag($tag)
    {
        $tag = $this->replaceOrAppendAttribute($tag, 'rel', 'preload');
        $tag = $this->replaceOrAppendAttribute($tag, 'as', 'style');
        $tag = $this->replaceOrAppendAttribute($tag, 'onload', "this.onload=null;this.rel='stylesheet'");
        $tag = $this->replaceOrAppendAttribute($tag, 'data-prestaload-rule-deferred', '1');

        return $tag . '<noscript>' . preg_replace('/\sdata-prestaload-rule-deferred=(["\']).*?\1/i', '', $this->replaceOrAppendAttribute($tag, 'rel', 'stylesheet')) . '</noscript>';
    }

    /**
     * Script deferral must modify only the opening tag, otherwise the browser
     * receives invalid markup such as `</script defer="defer">`.
     */
    private function buildDeferredScriptTag($tag)
    {
        if (!preg_match('/^<script\b[^>]*>/i', $tag, $match)) {
            return $tag;
        }

        $openingTag = $match[0];
        $deferredOpeningTag = $this->replaceOrAppendBooleanAttribute($openingTag, 'defer');
        $deferredOpeningTag = $this->replaceOrAppendAttribute($deferredOpeningTag, 'data-prestaload-rule-deferred', '1');

        return $deferredOpeningTag . substr($tag, strlen($openingTag));
    }

    private function replaceScriptSrc($tag, $value)
    {
        if (!preg_match('/^<script\b[^>]*>/i', $tag, $match)) {
            return $tag;
        }

        $openingTag = $match[0];
        $updatedOpeningTag = $this->replaceOrAppendAttribute($openingTag, 'src', $value);

        return $updatedOpeningTag . substr($tag, strlen($openingTag));
    }

    /**
     * Replaces the original script tag with a small loader that waits until
     * the page `load` event before injecting the script dynamically.
     */
    private function buildWindowLoadScriptTag($src)
    {
        $escapedSrc = json_encode($src);
        if ($escapedSrc === false) {
            return '';
        }

        return '<script data-prestaload-window-load="1">(function(){var loadScript=function(){var s=document.createElement("script");s.src=' . $escapedSrc . ';s.async=true;document.body.appendChild(s);};if(document.readyState==="complete"){loadScript();return;}window.addEventListener("load",loadScript,{once:true});}());</script>';
    }

    /**
     * Scan results may save absolute asset URLs while the rendered HTML uses
     * relative or protocol-relative paths. Normalize both to a stable absolute
     * URL so rule matching remains reliable.
     */
    private function normalizeAssetUrl($url)
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }

        if (strpos($url, '//') === 0) {
            return $this->getShopScheme() . ':' . $url;
        }

        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        $baseUrl = rtrim($this->getShopBaseUrl(), '/');
        if (strpos($url, '/') === 0) {
            return $baseUrl . $url;
        }

        return $baseUrl . '/' . ltrim($url, '/');
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

    private function replaceOrAppendAttribute($tag, $attribute, $value)
    {
        $pattern = '/(\b' . preg_quote($attribute, '/') . '\s*=\s*)(["\']).*?\2/i';
        $replacement = '$1"' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';

        if (preg_match($pattern, $tag)) {
            return preg_replace($pattern, $replacement, $tag, 1);
        }

        return preg_replace('/\s*\/?>$/', ' ' . $attribute . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"$0', $tag, 1);
    }

    private function replaceOrAppendBooleanAttribute($tag, $attribute)
    {
        $pattern = '/\s' . preg_quote($attribute, '/') . '(?:\s*=\s*(["\']).*?\1)?/i';
        if (preg_match($pattern, $tag)) {
            return preg_replace($pattern, ' ' . $attribute, $tag, 1);
        }

        return preg_replace('/\s*>$/', ' ' . $attribute . '>', $tag, 1);
    }
}
