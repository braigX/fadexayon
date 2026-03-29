<?php

namespace App\Services\Optimization;

use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Support\Facades\Log;

class HtmlOptimizationService
{
    private const GOOGLE_FONTS_HOST = 'fonts.googleapis.com';

    private const GOOGLE_STATIC_HOST = 'fonts.gstatic.com';

    private const FONT_EXTENSIONS_PATTERN = '/\.(woff2|woff|ttf|otf|eot)(\?|#|$)/i';

    private const LINK_TAG_PATTERN = '/<link\b[^>]*>/i';

    /**
     * @param  array<string, mixed>  $rendered
     * @param  array<string, mixed>  $variant
     * @param  list<array<string, mixed>>  $classifiedStylesheets
     * @param  array<string, mixed>  $stylesheetBundles
     * @param  string|null  $criticalCss
     * @param  string|null  $usedCssUrl
     * @return array{
     *   html: string,
     *   html_bytes: int,
     *   html_sha256: string,
     *   adjustments: array{
     *     deferred_script_count: int,
     *     low_priority_script_count: int,
     *     interaction_delayed_script_count: int,
     *     meta_injected: bool,
     *     critical_css_injected: bool,
     *     used_css_injected: bool,
     *     preloaded_stylesheet_count: int,
     *     reduced_stylesheet_count: int,
     *     removed_stylesheet_count: int,
     *     font_rule_summary: array<string, int>,
     *     asset_rule_summary: array<string, int>,
     *     compressed_html: bool,
     *     minified_inline_css: bool,
     *     minified_inline_js: bool
     *   }
     * }
     */
    public function buildOptimizedHtml(
        array $rendered,
        array $variant = [],
        ?string $criticalCss = null,
        ?string $usedCssUrl = null,
        array $classifiedStylesheets = [],
        array $stylesheetBundles = [],
        array $classifiedScripts = [],
        array $classifiedFontAssets = [],
        bool $enableStylesheetPreload = true,
        bool $enableFontOptimization = true,
        bool $enableJavascriptOptimization = true,
        bool $compressFinalHtml = true,
        bool $minifyInlineCss = true,
        bool $minifyInlineJs = true
    ): array
    {
        $html = ($minifyInlineCss || $minifyInlineJs)
            ? (string) ($rendered['processed_html'] ?? $rendered['html'] ?? $rendered['optimized_html'] ?? '')
            : (string) ($rendered['html'] ?? $rendered['processed_html'] ?? $rendered['optimized_html'] ?? '');
        $metaInjected = false;
        $criticalCssInjected = false;
        $usedCssInjected = false;
        $preloadedStylesheetCount = 0;

        if ($html === '') {
            return [
                'html' => '',
                'html_bytes' => 0,
                'html_sha256' => hash('sha256', ''),
                'adjustments' => [
                    'deferred_script_count' => 0,
                    'low_priority_script_count' => 0,
                    'interaction_delayed_script_count' => 0,
                    'meta_injected' => false,
                    'critical_css_injected' => false,
                    'used_css_injected' => false,
                    'preloaded_stylesheet_count' => 0,
                    'reduced_stylesheet_count' => 0,
                    'removed_stylesheet_count' => 0,
                    'asset_rule_summary' => [
                        'keep' => 0,
                        'preload' => 0,
                        'minify' => 0,
                        'reduce' => 0,
                        'reduce_minify' => 0,
                        'remove' => 0,
                    ],
                    'compressed_html' => $compressFinalHtml,
                    'minified_inline_css' => $minifyInlineCss,
                    'minified_inline_js' => $minifyInlineJs,
                ],
            ];
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $document->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $metaInjected = $this->injectGeneratorMeta($document);
        $criticalCssInjected = $this->injectCriticalCss($document, $criticalCss);
        $usedCssInjected = $this->injectUsedCssLink($document, $usedCssUrl);
        $stylesheetAdjustments = $this->applyStylesheetDeliveryStrategies(
            $document,
            $classifiedStylesheets,
            $stylesheetBundles,
            $enableStylesheetPreload,
            $usedCssInjected
        );
        $fontAdjustments = [
            'font_rule_summary' => [
                'keep' => 0,
                'self_host' => 0,
                'self_host_preload' => 0,
                'set_font_display_swap' => 0,
                'remove_unused' => 0,
                'dedupe_icon_font' => 0,
            ],
        ];
        if ($enableFontOptimization) {
            $fontAdjustments = $this->optimizeFontDelivery(
                $document,
                (string) ($rendered['final_url'] ?? $rendered['url'] ?? ''),
                $classifiedFontAssets
            );
        }
        $preloadedStylesheetCount = (int) ($stylesheetAdjustments['preloaded_stylesheet_count'] ?? 0);
        $adjustments = $this->applyScriptDeliveryStrategies(
            $document,
            (string) ($variant['device_class'] ?? 'desktop'),
            $classifiedScripts,
            $enableJavascriptOptimization
        );

        $optimizedHtml = $document->saveHTML() ?: $html;
        $finalHtml = $compressFinalHtml ? $this->compressHtmlConservatively($optimizedHtml) : $optimizedHtml;

        return [
            'html' => $finalHtml,
            'html_bytes' => strlen($finalHtml),
            'html_sha256' => hash('sha256', $finalHtml),
            'adjustments' => [
                'deferred_script_count' => $adjustments['deferred_script_count'],
                'low_priority_script_count' => $adjustments['low_priority_script_count'],
                'interaction_delayed_script_count' => $adjustments['interaction_delayed_script_count'],
                'meta_injected' => $metaInjected,
                'critical_css_injected' => $criticalCssInjected,
                'used_css_injected' => $usedCssInjected,
                'preloaded_stylesheet_count' => $preloadedStylesheetCount,
                'reduced_stylesheet_count' => (int) ($stylesheetAdjustments['reduced_stylesheet_count'] ?? 0),
                'removed_stylesheet_count' => (int) ($stylesheetAdjustments['removed_stylesheet_count'] ?? 0),
                'font_rule_summary' => is_array($fontAdjustments['font_rule_summary'] ?? null)
                    ? $fontAdjustments['font_rule_summary']
                    : ['keep' => 0, 'self_host' => 0, 'self_host_preload' => 0, 'set_font_display_swap' => 0, 'remove_unused' => 0, 'dedupe_icon_font' => 0],
                'asset_rule_summary' => is_array($stylesheetAdjustments['asset_rule_summary'] ?? null)
                    ? $stylesheetAdjustments['asset_rule_summary']
                    : ['keep' => 0, 'preload' => 0, 'minify' => 0, 'reduce' => 0, 'reduce_minify' => 0, 'remove' => 0],
                'compressed_html' => $compressFinalHtml,
                'minified_inline_css' => $minifyInlineCss,
                'minified_inline_js' => $minifyInlineJs,
            ],
        ];
    }

    private function compressHtmlConservatively(string $html): string
    {
        if (trim($html) === '') {
            return $html;
        }

        $preservedBlocks = [];
        $protectedHtml = preg_replace_callback(
            '/<(pre|textarea|script|style)\b[^>]*>.*?<\/\1>/is',
            static function (array $matches) use (&$preservedBlocks): string {
                $token = '__PRESTALOAD_HTML_BLOCK_' . count($preservedBlocks) . '__';
                $preservedBlocks[$token] = $matches[0];

                return $token;
            },
            $html
        ) ?? $html;

        $protectedHtml = preg_replace('/<!--(?!\s*\[if\b)(?!<!)(?!>).*?-->/s', '', $protectedHtml) ?? $protectedHtml;
        $protectedHtml = preg_replace('/>\s+</', '><', $protectedHtml) ?? $protectedHtml;
        $protectedHtml = preg_replace("/[\r\n]+/", "\n", $protectedHtml) ?? $protectedHtml;
        $protectedHtml = trim($protectedHtml);

        if ($preservedBlocks === []) {
            return $protectedHtml;
        }

        return strtr($protectedHtml, $preservedBlocks);
    }

    /**
     * @param  list<array<string, mixed>>  $classifiedStylesheets
     * @return array{
     *   preloaded_stylesheet_count: int,
     *   reduced_stylesheet_count: int,
     *   removed_stylesheet_count: int,
     *   asset_rule_summary: array<string, int>
     * }
     */
    private function applyStylesheetDeliveryStrategies(
        DOMDocument $document,
        array $classifiedStylesheets,
        array $stylesheetBundles,
        bool $enableStylesheetPreload,
        bool $usedCssInjected
    ): array
    {
        $head = $document->getElementsByTagName('head')->item(0);

        if (! $head instanceof DOMElement) {
            return [
                'preloaded_stylesheet_count' => 0,
                'reduced_stylesheet_count' => 0,
                'removed_stylesheet_count' => 0,
                'asset_rule_summary' => [
                    'keep' => 0,
                    'preload' => 0,
                    'minify' => 0,
                    'reduce' => 0,
                    'reduce_minify' => 0,
                    'remove' => 0,
                ],
            ];
        }

        $preloaded = 0;
        $reduced = 0;
        $removed = 0;
        $matchedTargets = [];
        $inlineTargets = [];
        $summary = [
            'keep' => 0,
            'preload' => 0,
            'minify' => 0,
            'reduce' => 0,
            'reduce_minify' => 0,
            'remove' => 0,
        ];
        $bundleMap = $this->filterBundleMapForOrderSafety(
            $this->normalizeStylesheetBundles($stylesheetBundles),
            $classifiedStylesheets
        );
        $bundleInjected = [];

        foreach ($classifiedStylesheets as $stylesheet) {
            $strategy = (string) (($stylesheet['delivery_strategy']['strategy'] ?? null) ?: 'keep');
            if (! array_key_exists($strategy, $summary)) {
                $strategy = 'keep';
            }

            $summary[$strategy]++;
            $targetUrl = trim((string) ($stylesheet['source_url'] ?? ''));
            if ($targetUrl !== '') {
                $matchedTargets[$this->normalizeAssetReference($targetUrl)] = [
                    'strategy' => $strategy,
                    'reduced_asset_url' => trim((string) ($stylesheet['delivery_strategy']['reduced_asset_url'] ?? '')),
                    'minified_asset_url' => trim((string) ($stylesheet['delivery_strategy']['minified_asset_url'] ?? '')),
                ];
                continue;
            }

            if ((bool) ($stylesheet['is_inline'] ?? false)) {
                $position = (int) ($stylesheet['position'] ?? 0);
                if ($position > 0) {
                    $inlineTargets[$position] = [
                        'strategy' => $strategy,
                    ];
                }
            }
        }

        $orderedStylesheetNodes = $this->collectOriginalStylesheetNodes($document);

        foreach ($orderedStylesheetNodes as $index => $node) {
            if (! $node instanceof DOMElement || strtolower($node->tagName) !== 'style') {
                continue;
            }

            $position = $index + 1;
            $target = $inlineTargets[$position] ?? null;
            if (! is_array($target)) {
                continue;
            }

            $strategy = (string) ($target['strategy'] ?? 'keep');

            if ($strategy === 'remove' && $usedCssInjected) {
                $node->parentNode?->removeChild($node);
                $removed++;
                continue;
            }

            if ($strategy === 'minify') {
                $content = $node->textContent ?? '';
                $node->textContent = $this->minifyInlineCssContent($content);
                $node->setAttribute('data-prestaload-action', 'minify');
                continue;
            }

            $node->setAttribute('data-prestaload-action', 'keep');
        }

        if ($matchedTargets === [] && $inlineTargets === []) {
            return [
                'preloaded_stylesheet_count' => 0,
                'reduced_stylesheet_count' => 0,
                'removed_stylesheet_count' => $removed,
                'asset_rule_summary' => $summary,
            ];
        }

        $links = [];
        foreach ($head->getElementsByTagName('link') as $link) {
            if ($link instanceof DOMElement) {
                $links[] = $link;
            }
        }

        foreach ($links as $link) {
            if (! $link instanceof DOMElement) {
                continue;
            }

            $rel = strtolower(trim($link->getAttribute('rel')));
            if ($rel !== 'stylesheet') {
                continue;
            }

            $href = trim($link->getAttribute('href'));
            $normalizedHref = $this->normalizeAssetReference($href);
            if ($normalizedHref === '' || ! isset($matchedTargets[$normalizedHref])) {
                continue;
            }

            $target = $matchedTargets[$normalizedHref];
            $strategy = (string) ($target['strategy'] ?? 'keep');
            $reducedAssetUrl = trim((string) ($target['reduced_asset_url'] ?? ''));
            $minifiedAssetUrl = trim((string) ($target['minified_asset_url'] ?? ''));
            $bundle = $bundleMap[$strategy] ?? null;
            $bundleUrl = is_array($bundle) ? trim((string) ($bundle['public_url'] ?? '')) : '';
            $deliveryHref = $href;

            if ($bundleUrl !== '') {
                $deliveryHref = $bundleUrl;
            } elseif (in_array($strategy, ['reduce', 'reduce_minify'], true) && $reducedAssetUrl !== '') {
                $deliveryHref = $reducedAssetUrl;
            } elseif ($strategy === 'minify' && $minifiedAssetUrl !== '') {
                $deliveryHref = $minifiedAssetUrl;
            }

            if ($strategy === 'remove' && $usedCssInjected) {
                $link->parentNode?->removeChild($link);
                $removed++;
                continue;
            }

            if ($bundleUrl !== '' && in_array($strategy, ['minify', 'reduce', 'reduce_minify'], true)) {
                if (! ($bundleInjected[$strategy] ?? false)) {
                    $bundleLink = $this->createBundleLink(
                        $document,
                        $deliveryHref,
                        $strategy,
                        $enableStylesheetPreload,
                        $usedCssInjected
                    );

                    if ($bundleLink instanceof DOMNode) {
                        $head->insertBefore($bundleLink, $link);
                    }

                    if ($enableStylesheetPreload && in_array($strategy, ['reduce', 'reduce_minify'], true)) {
                        $preloaded++;
                    }

                    $bundleInjected[$strategy] = true;
                }

                $link->parentNode?->removeChild($link);

                if (in_array($strategy, ['reduce', 'reduce_minify'], true)) {
                    $reduced++;
                }

                continue;
            }

            if ($enableStylesheetPreload && in_array($strategy, ['preload', 'reduce', 'reduce_minify'], true) && ! $this->hasExistingPreloadSibling($head, $normalizedHref)) {
                $preload = $document->createElement('link');
                $preload->setAttribute('rel', 'preload');
                $preload->setAttribute('as', 'style');
                $preload->setAttribute('href', $deliveryHref);
                $preload->setAttribute('data-prestaload', in_array($strategy, ['reduce', 'reduce_minify'], true) ? 'css-reduce-preload' : 'css-preload');

                $head->insertBefore($preload, $link);
                $preloaded++;
            }

            if (in_array($strategy, ['reduce', 'reduce_minify'], true) && $usedCssInjected) {
                if ($reducedAssetUrl !== '') {
                    $link->setAttribute('data-prestaload-original-href', $href);
                    $link->setAttribute('href', $reducedAssetUrl);
                }
                $this->makeStylesheetNonBlocking($link);
                $link->setAttribute('data-prestaload-action', $strategy);
                $reduced++;
                continue;
            }

            if ($strategy === 'preload') {
                $link->setAttribute('data-prestaload-action', 'preload');
                continue;
            }

            if ($strategy === 'minify') {
                if ($minifiedAssetUrl !== '') {
                    $link->setAttribute('data-prestaload-original-href', $href);
                    $link->setAttribute('href', $minifiedAssetUrl);
                }
                $link->setAttribute('data-prestaload-action', 'minify');
                continue;
            }

            $link->setAttribute('data-prestaload-action', 'keep');
        }

        return [
            'preloaded_stylesheet_count' => $preloaded,
            'reduced_stylesheet_count' => $reduced,
            'removed_stylesheet_count' => $removed,
            'asset_rule_summary' => $summary,
        ];
    }

    /**
     * @return list<DOMElement>
     */
    private function collectOriginalStylesheetNodes(DOMDocument $document): array
    {
        $nodes = [];

        foreach ($document->getElementsByTagName('*') as $element) {
            if (! $element instanceof DOMElement) {
                continue;
            }

            $tagName = strtolower($element->tagName);

            if ($tagName === 'link') {
                if (strtolower(trim($element->getAttribute('rel'))) !== 'stylesheet') {
                    continue;
                }

                if ($element->getAttribute('id') === 'prestaload-used-css' || $element->hasAttribute('data-prestaload')) {
                    continue;
                }

                $nodes[] = $element;
                continue;
            }

            if ($tagName === 'style') {
                if ($element->getAttribute('id') === 'prestaload-critical-css' || $element->hasAttribute('data-prestaload')) {
                    continue;
                }

                $nodes[] = $element;
            }
        }

        return $nodes;
    }

    private function minifyInlineCssContent(string $css): string
    {
        $normalized = preg_replace('!/\*.*?\*/!s', '', $css) ?? $css;
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s*([{}:;,>])\s*/', '$1', $normalized) ?? $normalized;
        $normalized = preg_replace('/;}/', '}', $normalized) ?? $normalized;

        return trim($normalized);
    }

    /**
     * @param  array<string, mixed>  $stylesheetBundles
     * @return array<string, array<string, mixed>>
     */
    private function normalizeStylesheetBundles(array $stylesheetBundles): array
    {
        $normalized = [];

        foreach ($stylesheetBundles as $action => $bundle) {
            if (! is_string($action) || ! is_array($bundle)) {
                continue;
            }

            $publicUrl = trim((string) ($bundle['public_url'] ?? ''));
            if ($publicUrl === '') {
                continue;
            }

            $normalized[$action] = $bundle;
        }

        return $normalized;
    }

    /**
     * Bundling by action can reorder CSS when `reduce`, `reduce_minify`, and `minify`
     * stylesheets are interleaved in the original document. In that case we fall back
     * to per-file generated assets to preserve cascade order.
     *
     * @param  array<string, array<string, mixed>>  $bundleMap
     * @param  list<array<string, mixed>>  $classifiedStylesheets
     * @return array<string, array<string, mixed>>
     */
    private function filterBundleMapForOrderSafety(array $bundleMap, array $classifiedStylesheets): array
    {
        if ($bundleMap === [] || ! $this->hasInterleavedBundledStrategies($classifiedStylesheets)) {
            return $bundleMap;
        }

        return [];
    }

    /**
     * @param  list<array<string, mixed>>  $classifiedStylesheets
     */
    private function hasInterleavedBundledStrategies(array $classifiedStylesheets): bool
    {
        $positionsByStrategy = [];

        foreach ($classifiedStylesheets as $index => $stylesheet) {
            if ((bool) ($stylesheet['is_inline'] ?? false)) {
                continue;
            }

            $sourceUrl = trim((string) ($stylesheet['source_url'] ?? ''));
            if ($sourceUrl === '') {
                continue;
            }

            $strategy = (string) (($stylesheet['delivery_strategy']['strategy'] ?? null) ?: 'keep');
            if (! in_array($strategy, ['minify', 'reduce', 'reduce_minify'], true)) {
                continue;
            }

            $positionsByStrategy[$strategy][] = $index;
        }

        if (count($positionsByStrategy) < 2) {
            return false;
        }

        $ranges = [];

        foreach ($positionsByStrategy as $strategy => $positions) {
            $ranges[$strategy] = [
                'start' => min($positions),
                'end' => max($positions),
            ];
        }

        $strategies = array_keys($ranges);
        $totalStrategies = count($strategies);

        for ($i = 0; $i < $totalStrategies; $i++) {
            for ($j = $i + 1; $j < $totalStrategies; $j++) {
                $left = $ranges[$strategies[$i]];
                $right = $ranges[$strategies[$j]];

                if ($left['start'] <= $right['end'] && $right['start'] <= $left['end']) {
                    return true;
                }
            }
        }

        return false;
    }

    private function createBundleLink(
        DOMDocument $document,
        string $href,
        string $strategy,
        bool $enableStylesheetPreload,
        bool $usedCssInjected
    ): ?DOMNode {
        if ($href === '') {
            return null;
        }

        if ($enableStylesheetPreload && in_array($strategy, ['reduce', 'reduce_minify'], true)) {
            $preload = $document->createElement('link');
            $preload->setAttribute('rel', 'preload');
            $preload->setAttribute('as', 'style');
            $preload->setAttribute('href', $href);
            $preload->setAttribute('data-prestaload', 'css-reduce-bundle-preload');

            $link = $document->createElement('link');
            $link->setAttribute('rel', 'stylesheet');
            $link->setAttribute('href', $href);
            $this->makeStylesheetNonBlocking($link);
            $link->setAttribute('data-prestaload', 'css-bundle');
            $link->setAttribute('data-prestaload-action', $strategy);

            $fragment = $document->createDocumentFragment();
            $fragment->appendChild($preload);
            $fragment->appendChild($link);

            return $fragment;
        }

        $link = $document->createElement('link');
        $link->setAttribute('rel', 'stylesheet');
        $link->setAttribute('href', $href);
        $link->setAttribute('data-prestaload', 'css-bundle');
        $link->setAttribute('data-prestaload-action', $strategy);

        if (in_array($strategy, ['reduce', 'reduce_minify'], true) && $usedCssInjected) {
            $this->makeStylesheetNonBlocking($link);
        }

        return $link;
    }

    private function injectCriticalCss(DOMDocument $document, ?string $criticalCss): bool
    {
        return $this->injectInlineStyle($document, 'prestaload-critical-css', 'critical-css', $criticalCss);
    }

    private function injectUsedCssLink(DOMDocument $document, ?string $usedCssUrl): bool
    {
        $href = trim((string) $usedCssUrl);

        if ($href === '') {
            return false;
        }

        foreach ($document->getElementsByTagName('link') as $link) {
            if ($link instanceof DOMElement && $link->getAttribute('id') === 'prestaload-used-css') {
                return false;
            }
        }

        $link = $document->createElement('link');
        $link->setAttribute('id', 'prestaload-used-css');
        $link->setAttribute('rel', 'stylesheet');
        $link->setAttribute('href', $href);
        $link->setAttribute('data-prestaload', 'used-css');

        $head = $document->getElementsByTagName('head')->item(0);
        if ($head instanceof DOMElement) {
            foreach ($head->childNodes as $child) {
                if ($child instanceof DOMElement && strtolower($child->tagName) === 'link' && strtolower($child->getAttribute('rel')) === 'stylesheet') {
                    $head->insertBefore($link, $child);

                    return true;
                }
            }

            $head->appendChild($link);

            return true;
        }

        $document->insertBefore($link, $document->firstChild);

        return true;
    }

    private function injectInlineStyle(DOMDocument $document, string $id, string $marker, ?string $css): bool
    {
        $content = trim((string) $css);

        if ($content === '') {
            return false;
        }

        foreach ($document->getElementsByTagName('style') as $style) {
            if ($style instanceof DOMElement && $style->getAttribute('id') === $id) {
                return false;
            }
        }

        $style = $document->createElement('style');
        $style->setAttribute('id', $id);
        $style->setAttribute('data-prestaload', $marker);
        $style->nodeValue = $content;

        $head = $document->getElementsByTagName('head')->item(0);
        if ($head instanceof DOMElement) {
            foreach ($head->childNodes as $child) {
                if ($child instanceof DOMElement && strtolower($child->tagName) === 'link' && strtolower($child->getAttribute('rel')) === 'stylesheet') {
                    $head->insertBefore($style, $child);

                    return true;
                }
            }

            $head->appendChild($style);

            return true;
        }

        $document->insertBefore($style, $document->firstChild);

        return true;
    }

    private function injectGeneratorMeta(DOMDocument $document): bool
    {
        foreach ($document->getElementsByTagName('meta') as $meta) {
            if ($meta instanceof DOMElement && $meta->getAttribute('id') === 'optimized_by_prestaload') {
                return false;
            }
        }

        $meta = $document->createElement('meta');
        $meta->setAttribute('id', 'optimized_by_prestaload');
        $meta->setAttribute('name', 'generator');
        $meta->setAttribute('content', 'Optimized by PrestaLoad - https://prestaload.com');

        $head = $document->getElementsByTagName('head')->item(0);
        if ($head instanceof DOMElement) {
            $head->appendChild($meta);

            return true;
        }

        $document->insertBefore($meta, $document->firstChild);

        return true;
    }

    private function hasExistingPreloadSibling(DOMElement $head, string $normalizedHref): bool
    {
        foreach ($head->getElementsByTagName('link') as $link) {
            if (! $link instanceof DOMElement) {
                continue;
            }

            if (strtolower(trim($link->getAttribute('rel'))) !== 'preload') {
                continue;
            }

            if (strtolower(trim($link->getAttribute('as'))) !== 'style') {
                continue;
            }

            if ($this->normalizeAssetReference(trim($link->getAttribute('href'))) === $normalizedHref) {
                return true;
            }
        }

        return false;
    }

    private function makeStylesheetNonBlocking(DOMElement $link): void
    {
        $link->setAttribute('media', 'print');
        $link->setAttribute('onload', "this.onload=null;this.media='all'");
        $link->setAttribute('data-prestaload', 'css-reduced-fallback');
    }

    private function normalizeAssetReference(string $value): string
    {
        $normalized = trim($value);

        if ($normalized === '') {
            return '';
        }

        if (str_starts_with($normalized, '//')) {
            $normalized = 'https:' . $normalized;
        }

        if (! preg_match('#^https?://#i', $normalized)) {
            return ltrim($normalized, '/');
        }

        $path = parse_url($normalized, PHP_URL_PATH);
        $query = parse_url($normalized, PHP_URL_QUERY);

        $normalizedPath = is_string($path) ? ltrim($path, '/') : '';
        $normalizedQuery = is_string($query) && $query !== '' ? '?' . $query : '';

        return $normalizedPath . $normalizedQuery;
    }

    private function normalizeScriptReference(string $value): string
    {
        return $this->normalizeAssetReference($value);
    }

    /**
     * @param  list<array<string, mixed>>  $classifiedScripts
     * @return array{deferred_script_count: int, low_priority_script_count: int, interaction_delayed_script_count: int}
     */
    private function applyScriptDeliveryStrategies(
        DOMDocument $document,
        string $deviceClass,
        array $classifiedScripts = [],
        bool $enableJavascriptOptimization = true
    ): array
    {
        $deferredCount = 0;
        $lowPriorityCount = 0;
        $interactionDelayedCount = 0;
        $matchedTargets = [];
        $loaderNeeded = false;

        foreach ($classifiedScripts as $scriptRule) {
            $targetUrl = trim((string) ($scriptRule['url'] ?? ''));
            if ($targetUrl === '') {
                continue;
            }

            $matchedTargets[$this->normalizeScriptReference($targetUrl)] = [
                'action' => (string) ($scriptRule['action'] ?? 'keep'),
                'origin' => (string) ($scriptRule['origin'] ?? ''),
            ];
        }

        foreach ($document->getElementsByTagName('script') as $script) {
            if (! $script instanceof DOMElement) {
                continue;
            }

            $src = trim($script->getAttribute('src'));
            if ($src === '') {
                continue;
            }

            $normalizedSrc = $this->normalizeScriptReference($src);
            $target = is_array($matchedTargets[$normalizedSrc] ?? null) ? $matchedTargets[$normalizedSrc] : [];
            $resolvedAction = (string) (($target['action'] ?? null) ?: 'keep');

            $type = strtolower(trim($script->getAttribute('type')));
            if (in_array($type, ['module', 'application/ld+json'], true)) {
                continue;
            }

            if ($this->isUnsafeScript(strtolower($src))) {
                continue;
            }

            if ($enableJavascriptOptimization && $resolvedAction === 'load_on_interaction') {
                $this->delayScriptUntilInteraction($script, $src);
                $interactionDelayedCount++;
                $loaderNeeded = true;
                continue;
            }

            if ($script->hasAttribute('nomodule')) {
                continue;
            }

            // Safe V1 rule: only change same-origin or relative storefront assets.
            if (! $this->isOptimizableScript($src, $target)) {
                continue;
            }

            if ($enableJavascriptOptimization && in_array($resolvedAction, ['minify', 'reduce', 'reduce_minify'], true)) {
                $script->setAttribute('data-prestaload-action', $resolvedAction);
                $script->setAttribute('data-prestaload', 'js-optimized');

                if ($this->isInsideHead($script) && ! $script->hasAttribute('async') && ! $script->hasAttribute('defer')) {
                    $script->setAttribute('defer', 'defer');
                    $deferredCount++;
                    continue;
                }

                if ($deviceClass === 'mobile' || ! $script->hasAttribute('fetchpriority')) {
                    $script->setAttribute('fetchpriority', 'low');
                    $lowPriorityCount++;
                }

                continue;
            }

            if ($script->hasAttribute('async') || $script->hasAttribute('defer')) {
                continue;
            }

            // Safety-first V1: do not defer head scripts automatically.
            if ($this->isInsideHead($script)) {
                continue;
            }

            if ($deviceClass === 'mobile' || ! $script->hasAttribute('fetchpriority')) {
                $script->setAttribute('data-prestaload-action', 'keep');
                $script->setAttribute('data-prestaload', 'js-optimized');
                $script->setAttribute('fetchpriority', 'low');
                $lowPriorityCount++;
            }
        }

        if ($loaderNeeded) {
            $this->injectInteractionScriptLoader($document);
        }

        return [
            'deferred_script_count' => $deferredCount,
            'low_priority_script_count' => $lowPriorityCount,
            'interaction_delayed_script_count' => $interactionDelayedCount,
        ];
    }

    private function delayScriptUntilInteraction(DOMElement $script, string $src): void
    {
        $script->setAttribute('data-prestaload-src', $src);
        $script->removeAttribute('src');
        $script->setAttribute('data-prestaload-action', 'load_on_interaction');
        $script->setAttribute('data-prestaload', 'js-interaction');
        $script->setAttribute('type', 'application/prestaload-delayed');
        $script->setAttribute('fetchpriority', 'low');
    }

    private function injectInteractionScriptLoader(DOMDocument $document): void
    {
        foreach ($document->getElementsByTagName('script') as $script) {
            if ($script instanceof DOMElement && $script->getAttribute('id') === 'prestaload-interaction-script-loader') {
                return;
            }
        }

        $loader = $document->createElement('script');
        $loader->setAttribute('id', 'prestaload-interaction-script-loader');
        $loader->setAttribute('data-prestaload', 'interaction-script-loader');
        $loader->nodeValue = $this->interactionScriptLoaderCode();

        $body = $document->getElementsByTagName('body')->item(0);
        if ($body instanceof DOMElement) {
            $body->appendChild($loader);

            return;
        }

        $head = $document->getElementsByTagName('head')->item(0);
        if ($head instanceof DOMElement) {
            $head->appendChild($loader);

            return;
        }

        $document->appendChild($loader);
    }

    private function interactionScriptLoaderCode(): string
    {
        return <<<JS
(function(){if(window.__prestaloadInteractionLoader){return;}window.__prestaloadInteractionLoader=true;var fired=false;function copyAttrs(from,to){for(var i=0;i<from.attributes.length;i++){var attr=from.attributes[i];if(!attr||!attr.name){continue;}if(attr.name==='src'||attr.name==='type'||attr.name==='data-prestaload-src'||attr.name==='data-prestaload-action'||attr.name==='data-prestaload'){continue;}to.setAttribute(attr.name,attr.value);}}function load(){if(fired){return;}fired=true;var nodes=document.querySelectorAll('script[data-prestaload-action=\"load_on_interaction\"][data-prestaload-src]');for(var i=0;i<nodes.length;i++){var node=nodes[i];var src=node.getAttribute('data-prestaload-src');if(!src){continue;}var s=document.createElement('script');copyAttrs(node,s);s.src=src;s.setAttribute('data-prestaload-loaded','1');node.parentNode.insertBefore(s,node.nextSibling);node.parentNode.removeChild(node);}for(var j=0;j<events.length;j++){window.removeEventListener(events[j],load,listenerOpts);document.removeEventListener(events[j],load,listenerOpts);}}var listenerOpts={passive:true};var events=['pointerdown','touchstart','keydown','wheel','scroll'];for(var k=0;k<events.length;k++){window.addEventListener(events[k],load,listenerOpts);document.addEventListener(events[k],load,listenerOpts);}})();
JS;
    }

    private function isInsideHead(DOMElement $script): bool
    {
        $node = $script->parentNode;
        while ($node !== null) {
            if ($node instanceof DOMElement && strtolower($node->tagName) === 'head') {
                return true;
            }

            $node = $node->parentNode;
        }

        return false;
    }

    private function isLocalAsset(string $src): bool
    {
        return str_starts_with($src, '/')
            || str_starts_with($src, './')
            || str_starts_with($src, '../')
            || ! preg_match('#^https?://#i', $src);
    }

    /**
     * @param  array<string, mixed>  $target
     */
    private function isOptimizableScript(string $src, array $target = []): bool
    {
        if ($this->isLocalAsset($src)) {
            return true;
        }

        return strtolower(trim((string) ($target['origin'] ?? ''))) === 'same-origin';
    }

    private function isUnsafeScript(string $src): bool
    {
        foreach ([
            'checkout',
            'cart',
            'login',
            'customer',
            'auth',
            'consent',
            'cookie',
            'captcha',
            'recaptcha',
        ] as $needle) {
            if (str_contains($src, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  list<array<string, mixed>>  $classifiedFontAssets
     */
    private function optimizeFontDelivery(DOMDocument $document, string $documentUrl = '', array $classifiedFontAssets = []): array
    {
        $fontAdjustments = [
            'font_rule_summary' => [
                'keep' => 0,
                'self_host' => 0,
                'self_host_preload' => 0,
                'set_font_display_swap' => 0,
                'remove_unused' => 0,
                'dedupe_icon_font' => 0,
            ],
        ];

        if ($classifiedFontAssets !== []) {
            $fontAdjustments = $this->applyFontAssetRules($document, $classifiedFontAssets, $documentUrl);
        }

        $html = $document->saveHTML();
        if (! is_string($html) || trim($html) === '' || stripos($html, '<head') === false) {
            return $fontAdjustments;
        }

        $fontOrigins = $this->collectFontOriginsFromHtml($html, $documentUrl);
        $optimizedHtml = $html;
        if ($classifiedFontAssets === []) {
            $optimizedHtml = $this->normalizeGoogleFontStylesheets($optimizedHtml, $fontOrigins, $documentUrl);
            $optimizedHtml = $this->consolidateGoogleFontStylesheets($optimizedHtml, $fontOrigins);
        }
        $optimizedHtml = $this->injectPreconnectHints($optimizedHtml, $fontOrigins);

        if ($optimizedHtml === $html) {
            return $fontAdjustments;
        }

        while ($document->firstChild) {
            $document->removeChild($document->firstChild);
        }

        libxml_use_internal_errors(true);
        $document->loadHTML($optimizedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        return $fontAdjustments;
    }

    /**
     * @param  list<array<string, mixed>>  $classifiedFontAssets
     */
    private function applyFontAssetRules(DOMDocument $document, array $classifiedFontAssets, string $documentUrl = ''): array
    {
        $head = $document->getElementsByTagName('head')->item(0);
        if (! $head instanceof DOMElement || $classifiedFontAssets === []) {
            return [
                'font_rule_summary' => [
                    'keep' => 0,
                    'self_host' => 0,
                    'self_host_preload' => 0,
                    'set_font_display_swap' => 0,
                    'remove_unused' => 0,
                    'dedupe_icon_font' => 0,
                ],
            ];
        }

        $targets = [];
        $googleFontTargets = [];
        foreach ($classifiedFontAssets as $asset) {
            if (! is_array($asset)) {
                continue;
            }

            $assetUrl = trim((string) ($asset['asset_url'] ?? $asset['href'] ?? ''));
            $normalized = $this->normalizeAssetReference($assetUrl);
            if ($normalized === '') {
                continue;
            }

            $targets[$normalized] = $asset;

            if (($asset['type'] ?? null) === 'google_stylesheet') {
                foreach ((array) ($asset['families'] ?? []) as $family) {
                    $family = trim((string) $family);
                    if ($family === '') {
                        continue;
                    }

                    $googleFontTargets[strtolower($family)][] = $asset;
                }
            }
        }

        if ($targets === []) {
            return [
                'font_rule_summary' => [
                    'keep' => 0,
                    'self_host' => 0,
                    'self_host_preload' => 0,
                    'set_font_display_swap' => 0,
                    'remove_unused' => 0,
                    'dedupe_icon_font' => 0,
                ],
            ];
        }

        $summary = [
            'keep' => 0,
            'self_host' => 0,
            'self_host_preload' => 0,
            'set_font_display_swap' => 0,
            'remove_unused' => 0,
            'dedupe_icon_font' => 0,
        ];
        $logRules = [];

        foreach ($targets as $target) {
            $action = (string) ($target['action'] ?? 'keep');
            if (! array_key_exists($action, $summary)) {
                $action = 'keep';
            }

            $summary[$action]++;
            $logRules[] = [
                'asset_url' => (string) ($target['asset_url'] ?? $target['href'] ?? ''),
                'action' => (string) ($target['action'] ?? 'keep'),
                'recommended_action' => (string) ($target['recommended_action'] ?? ($target['action'] ?? 'keep')),
                'action_source' => (string) ($target['action_source'] ?? 'auto'),
                'asset_ready' => (bool) ($target['asset_ready'] ?? false),
                'asset_public_url' => (string) ($target['asset_public_url'] ?? ''),
                'type' => (string) ($target['type'] ?? ''),
            ];
        }

        Log::info('prestaload.font_rules.applying', [
            'url' => $documentUrl,
            'rule_count' => count($logRules),
            'summary' => $summary,
            'rules' => $logRules,
        ]);

        $this->splitCombinedGoogleFontLinks($document, $googleFontTargets);

        $links = [];
        foreach ($head->getElementsByTagName('link') as $link) {
            if ($link instanceof DOMElement) {
                $links[] = $link;
            }
        }

        $keptIconStylesheet = [];

        foreach ($links as $link) {
            $rel = strtolower(trim($link->getAttribute('rel')));
            if (! in_array($rel, ['stylesheet', 'preload'], true)) {
                continue;
            }

            $href = trim($link->getAttribute('href'));
            $originalHref = trim($link->getAttribute('data-prestaload-original-href'));
            $normalizedHref = $this->normalizeAssetReference($originalHref !== '' ? $originalHref : $href);
            $target = $normalizedHref !== '' && isset($targets[$normalizedHref])
                ? $targets[$normalizedHref]
                : $this->matchGoogleFontTargetForLink($href, $googleFontTargets);

            if (! is_array($target)) {
                continue;
            }
            $action = (string) ($target['action'] ?? 'keep');

            if ($action === 'remove_unused') {
                $link->parentNode?->removeChild($link);
                continue;
            }

            if ($action === 'dedupe_icon_font') {
                if (isset($keptIconStylesheet[$normalizedHref])) {
                    $link->parentNode?->removeChild($link);
                    continue;
                }

                $keptIconStylesheet[$normalizedHref] = true;
                $link->setAttribute('data-prestaload-action', 'dedupe_icon_font');
                continue;
            }

            if (! in_array($action, ['self_host', 'self_host_preload', 'set_font_display_swap'], true)) {
                continue;
            }

            $assetPublicUrl = trim((string) ($target['asset_public_url'] ?? ''));
            if ($assetPublicUrl === '') {
                continue;
            }

            if ($originalHref === '') {
                $link->setAttribute('data-prestaload-original-href', $href);
            }

            $link->setAttribute('href', $assetPublicUrl);
            $link->setAttribute('data-prestaload-action', $action);
            $link->setAttribute(
                'data-prestaload',
                $action === 'set_font_display_swap' ? 'font-display-swap' : 'font-self-hosted'
            );

            if ($action !== 'self_host_preload') {
                continue;
            }

            $fontMeta = is_array($target['font_meta'] ?? null) ? $target['font_meta'] : [];
            $preloadUrls = is_array($fontMeta['preload_urls'] ?? null) ? $fontMeta['preload_urls'] : [];

            foreach ($preloadUrls as $preloadUrl) {
                $preloadUrl = trim((string) $preloadUrl);
                if ($preloadUrl === '' || $this->hasExistingFontPreload($head, $preloadUrl)) {
                    continue;
                }

                $preload = $document->createElement('link');
                $preload->setAttribute('rel', 'preload');
                $preload->setAttribute('as', 'font');
                $preload->setAttribute('type', 'font/woff2');
                $preload->setAttribute('crossorigin', 'anonymous');
                $preload->setAttribute('href', $preloadUrl);
                $preload->setAttribute('data-prestaload', 'font-preload');
                $head->insertBefore($preload, $link);
            }
        }

        return [
            'font_rule_summary' => $summary,
        ];
    }

    /**
     * @param  array<string, list<array<string, mixed>>>  $googleFontTargets
     */
    private function splitCombinedGoogleFontLinks(DOMDocument $document, array $googleFontTargets): void
    {
        $head = $document->getElementsByTagName('head')->item(0);
        if (! $head instanceof DOMElement) {
            return;
        }

        $links = [];
        foreach ($head->getElementsByTagName('link') as $link) {
            if ($link instanceof DOMElement) {
                $links[] = $link;
            }
        }

        foreach ($links as $link) {
            $href = trim($link->getAttribute('href'));
            $families = $this->extractGoogleFontFamiliesFromUrl($href);
            if (count($families) <= 1) {
                continue;
            }

            $fragments = [];
            foreach ($families as $family) {
                $familyTargets = $googleFontTargets[strtolower($family)] ?? [];
                $familyTarget = is_array($familyTargets[0] ?? null) ? $familyTargets[0] : null;
                $familyHref = trim((string) ($familyTarget['asset_url'] ?? ''));
                if ($familyHref === '') {
                    $familyHref = $this->buildSingleFamilyGoogleFontUrl($href, $family);
                }

                if ($familyHref === '') {
                    continue;
                }

                $clone = $link->cloneNode(true);
                if (! $clone instanceof DOMElement) {
                    continue;
                }

                $clone->setAttribute('href', $familyHref);
                $fragments[] = $clone;
            }

            if ($fragments === []) {
                continue;
            }

            foreach ($fragments as $fragment) {
                $head->insertBefore($fragment, $link);
            }

            $link->parentNode?->removeChild($link);
        }
    }

    /**
     * @param  array<string, list<array<string, mixed>>>  $googleFontTargets
     * @return array<string, mixed>|null
     */
    private function matchGoogleFontTargetForLink(string $href, array $googleFontTargets): ?array
    {
        $urlParts = @parse_url($href);
        if (! is_array($urlParts) || strtolower((string) ($urlParts['host'] ?? '')) !== self::GOOGLE_FONTS_HOST) {
            return null;
        }

        $families = $this->extractGoogleFontFamiliesFromUrl($href);
        if ($families === []) {
            return null;
        }

        $matches = [];
        foreach ($families as $family) {
            foreach ($googleFontTargets[strtolower($family)] ?? [] as $target) {
                $matches[] = $target;
            }
        }

        if ($matches === []) {
            return null;
        }

        usort($matches, static function (array $left, array $right): int {
            $rank = static function (array $asset): int {
                return match ((string) ($asset['action'] ?? 'keep')) {
                    'self_host_preload' => 0,
                    'self_host' => 1,
                    'remove_unused' => 2,
                    'dedupe_icon_font' => 3,
                    default => 4,
                };
            };

            return [$rank($left), 0 - count((array) ($left['families'] ?? []))]
                <=> [$rank($right), 0 - count((array) ($right['families'] ?? []))];
        });

        return $matches[0];
    }

    /**
     * @return list<string>
     */
    private function extractGoogleFontFamiliesFromUrl(string $url): array
    {
        $parts = @parse_url($url);
        if (! is_array($parts) || empty($parts['query'])) {
            return [];
        }

        $families = [];

        foreach (explode('&', (string) $parts['query']) as $pair) {
            [$key, $value] = array_pad(explode('=', $pair, 2), 2, null);
            if ($key !== 'family' || ! is_string($value) || $value === '') {
                continue;
            }

            foreach (explode('|', rawurldecode($value)) as $familySegment) {
                $familySegment = preg_replace('/:.+$/', '', (string) $familySegment) ?? (string) $familySegment;
                $familySegment = trim(str_replace('+', ' ', $familySegment), " \t\n\r\0\x0B'\"");
                if ($familySegment !== '') {
                    $families[] = strtolower($familySegment);
                }
            }
        }

        return array_values(array_unique($families));
    }

    private function buildSingleFamilyGoogleFontUrl(string $sourceUrl, string $familyName): string
    {
        $parts = @parse_url($sourceUrl);
        if (! is_array($parts) || strtolower((string) ($parts['host'] ?? '')) !== self::GOOGLE_FONTS_HOST) {
            return '';
        }

        parse_str((string) ($parts['query'] ?? ''), $query);
        $query['family'] = str_replace(' ', '+', trim($familyName));
        $parts['query'] = http_build_query($query);

        $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : 'https://';
        $host = (string) ($parts['host'] ?? self::GOOGLE_FONTS_HOST);
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $path = (string) ($parts['path'] ?? '/css');
        $queryString = isset($parts['query']) && $parts['query'] !== '' ? '?' . $parts['query'] : '';
        $fragment = isset($parts['fragment']) && $parts['fragment'] !== '' ? '#' . $parts['fragment'] : '';

        return $scheme . $host . $port . $path . $queryString . $fragment;
    }

    /**
     * @return array<string, bool>
     */
    private function collectFontOriginsFromHtml(string $html, string $documentUrl = ''): array
    {
        $fontOrigins = [];

        preg_replace_callback(self::LINK_TAG_PATTERN, function (array $matches) use (&$fontOrigins, $documentUrl): string {
            $attributes = $this->extractLinkAttributes($matches[0]);
            $href = isset($attributes['href']) ? html_entity_decode((string) $attributes['href'], ENT_QUOTES, 'UTF-8') : '';
            $rel = strtolower(trim((string) ($attributes['rel'] ?? '')));

            if ($href === '' || ! in_array($rel, ['stylesheet', 'preload'], true)) {
                return $matches[0];
            }

            $urlParts = @parse_url($href);
            if (! is_array($urlParts) || empty($urlParts['host'])) {
                return $matches[0];
            }

            $host = strtolower((string) $urlParts['host']);
            if ($host === self::GOOGLE_FONTS_HOST) {
                $fontOrigins['https://' . self::GOOGLE_FONTS_HOST] = true;
                $fontOrigins['https://' . self::GOOGLE_STATIC_HOST] = true;

                return $matches[0];
            }

            if ($this->isExternalFontAsset($href, $urlParts, $documentUrl)) {
                $scheme = ! empty($urlParts['scheme']) ? (string) $urlParts['scheme'] : 'https';
                $fontOrigins[$scheme . '://' . $urlParts['host']] = true;
            }

            return $matches[0];
        }, $html);

        return $fontOrigins;
    }

    /**
     * @param  array<string, bool>  $fontOrigins
     */
    private function normalizeGoogleFontStylesheets(string $html, array &$fontOrigins, string $documentUrl = ''): string
    {
        return preg_replace_callback(self::LINK_TAG_PATTERN, function (array $matches) use (&$fontOrigins, $documentUrl): string {
            $tag = $matches[0];
            $attributes = $this->extractLinkAttributes($tag);
            $href = isset($attributes['href']) ? html_entity_decode((string) $attributes['href'], ENT_QUOTES, 'UTF-8') : '';
            $rel = strtolower(trim((string) ($attributes['rel'] ?? '')));

            if ($href === '' || ! in_array($rel, ['stylesheet', 'preload'], true)) {
                return $tag;
            }

            $urlParts = @parse_url($href);
            if (! is_array($urlParts) || empty($urlParts['host'])) {
                return $tag;
            }

            $host = strtolower((string) $urlParts['host']);
            if ($host === self::GOOGLE_FONTS_HOST) {
                $updatedHref = $this->ensureDisplaySwap($href);
                $fontOrigins['https://' . self::GOOGLE_FONTS_HOST] = true;
                $fontOrigins['https://' . self::GOOGLE_STATIC_HOST] = true;

                if ($updatedHref !== $href) {
                    return $this->replaceLinkHref($tag, (string) $attributes['href'], $updatedHref);
                }

                return $tag;
            }

            if ($this->isExternalFontAsset($href, $urlParts, $documentUrl)) {
                $scheme = ! empty($urlParts['scheme']) ? (string) $urlParts['scheme'] : 'https';
                $fontOrigins[$scheme . '://' . $urlParts['host']] = true;
            }

            return $tag;
        }, $html) ?? $html;
    }

    /**
     * @param  array<string, bool>  $fontOrigins
     */
    private function consolidateGoogleFontStylesheets(string $html, array &$fontOrigins): string
    {
        if (! preg_match_all(self::LINK_TAG_PATTERN, $html, $matches, PREG_SET_ORDER)) {
            return $html;
        }

        $classicFamilies = [];
        $css2Urls = [];
        $matchedGoogleTags = [];

        foreach ($matches as $match) {
            $tag = $match[0];
            $attributes = $this->extractLinkAttributes($tag);
            $href = isset($attributes['href']) ? html_entity_decode((string) $attributes['href'], ENT_QUOTES, 'UTF-8') : '';
            $rel = strtolower(trim((string) ($attributes['rel'] ?? '')));

            if ($href === '' || $rel !== 'stylesheet') {
                continue;
            }

            $urlParts = @parse_url($href);
            if (! is_array($urlParts) || empty($urlParts['host'])) {
                continue;
            }

            if (strtolower((string) $urlParts['host']) !== self::GOOGLE_FONTS_HOST) {
                continue;
            }

            $normalizedHref = $this->ensureDisplaySwap($href);
            $matchedGoogleTags[$tag] = true;
            $fontOrigins['https://' . self::GOOGLE_FONTS_HOST] = true;
            $fontOrigins['https://' . self::GOOGLE_STATIC_HOST] = true;

            if ($this->isClassicGoogleFontsRequest($urlParts)) {
                foreach ($this->extractClassicFamilies($normalizedHref) as $family) {
                    $classicFamilies[$family] = true;
                }

                continue;
            }

            $css2Urls[$normalizedHref] = true;
        }

        if ($matchedGoogleTags === []) {
            return $html;
        }

        $replacementTags = [];

        if ($classicFamilies !== []) {
            $replacementTags[] = '<link rel="stylesheet" href="'
                . htmlspecialchars($this->buildClassicGoogleFontsUrl(array_keys($classicFamilies)), ENT_QUOTES, 'UTF-8')
                . '">';
        }

        foreach (array_keys($css2Urls) as $css2Url) {
            $replacementTags[] = '<link rel="stylesheet" href="' . htmlspecialchars($css2Url, ENT_QUOTES, 'UTF-8') . '">';
        }

        $replacementBlock = implode("\n", $replacementTags);
        $firstReplacementDone = false;

        return preg_replace_callback(self::LINK_TAG_PATTERN, static function (array $match) use ($matchedGoogleTags, $replacementBlock, &$firstReplacementDone): string {
            $tag = $match[0];
            if (! isset($matchedGoogleTags[$tag])) {
                return $tag;
            }

            if ($firstReplacementDone) {
                return '';
            }

            $firstReplacementDone = true;

            return $replacementBlock;
        }, $html) ?? $html;
    }

    /**
     * @param  array<string, bool>  $fontOrigins
     */
    private function injectPreconnectHints(string $html, array $fontOrigins): string
    {
        if ($fontOrigins === []) {
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

        if ($hints === []) {
            return $html;
        }

        return preg_replace('/<\/head>/i', implode("\n", $hints) . "\n</head>", $html, 1) ?? $html;
    }

    private function hasExistingFontPreload(DOMElement $head, string $href): bool
    {
        $normalizedHref = $this->normalizeAssetReference($href);

        foreach ($head->getElementsByTagName('link') as $link) {
            if (! $link instanceof DOMElement) {
                continue;
            }

            if (strtolower(trim($link->getAttribute('rel'))) !== 'preload') {
                continue;
            }

            if (strtolower(trim($link->getAttribute('as'))) !== 'font') {
                continue;
            }

            if ($this->normalizeAssetReference(trim($link->getAttribute('href'))) === $normalizedHref) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, string>
     */
    private function extractLinkAttributes(string $tag): array
    {
        $attributes = [];

        if (! preg_match_all('/([a-zA-Z_:][a-zA-Z0-9_:\-]*)\s*=\s*(["\'])(.*?)\2/s', $tag, $matches, PREG_SET_ORDER)) {
            return $attributes;
        }

        foreach ($matches as $match) {
            $attributes[strtolower((string) $match[1])] = (string) $match[3];
        }

        return $attributes;
    }

    private function replaceLinkHref(string $tag, string $oldHref, string $newHref): string
    {
        return preg_replace(
            '/(\bhref\s*=\s*)(["\'])' . preg_quote($oldHref, '/') . '(\2)/i',
            '$1$2' . htmlspecialchars($newHref, ENT_QUOTES, 'UTF-8') . '$3',
            $tag,
            1
        ) ?? $tag;
    }

    /**
     * @param  array<string, mixed>  $urlParts
     */
    private function isClassicGoogleFontsRequest(array $urlParts): bool
    {
        return (($urlParts['path'] ?? '') === '/css');
    }

    /**
     * @return list<string>
     */
    private function extractClassicFamilies(string $href): array
    {
        $parts = @parse_url($href);
        if (! is_array($parts) || empty($parts['query'])) {
            return [];
        }

        $families = [];
        foreach (explode('&', (string) $parts['query']) as $pair) {
            $segments = explode('=', $pair, 2);
            if (count($segments) !== 2 || $segments[0] !== 'family') {
                continue;
            }

            foreach (explode('|', rawurldecode($segments[1])) as $family) {
                $family = trim((string) $family);
                if ($family !== '') {
                    $families[$family] = true;
                }
            }
        }

        return array_keys($families);
    }

    /**
     * @param  list<string>  $families
     */
    private function buildClassicGoogleFontsUrl(array $families): string
    {
        sort($families, SORT_NATURAL | SORT_FLAG_CASE);

        return 'https://' . self::GOOGLE_FONTS_HOST . '/css?family='
            . rawurlencode(implode('|', $families))
            . '&display=swap';
    }

    private function ensureDisplaySwap(string $href): string
    {
        $parts = @parse_url($href);
        if (! is_array($parts)) {
            return $href;
        }

        $query = [];
        if (isset($parts['query'])) {
            parse_str((string) $parts['query'], $query);
        }

        if (strtolower((string) ($query['display'] ?? '')) === 'swap') {
            return $href;
        }

        $query['display'] = 'swap';
        $parts['query'] = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        return $this->buildUrl($parts);
    }

    private function hasPreconnectForOrigin(string $html, string $origin): bool
    {
        return (bool) preg_match(
            '/<link\b[^>]*rel=(["\'])preconnect\1[^>]*href=(["\'])' . preg_quote($origin, '/') . '\2/i',
            $html
        );
    }

    private function needsCrossorigin(string $origin): bool
    {
        return str_contains($origin, self::GOOGLE_STATIC_HOST);
    }

    /**
     * @param  array<string, mixed>  $urlParts
     */
    private function isExternalFontAsset(string $href, array $urlParts, string $documentUrl = ''): bool
    {
        $host = strtolower((string) ($urlParts['host'] ?? ''));
        if ($host === '') {
            return false;
        }

        $shopHost = '';
        $documentHost = parse_url($documentUrl, PHP_URL_HOST);
        if (is_string($documentHost)) {
            $shopHost = strtolower($documentHost);
        }

        if ($shopHost !== '' && $host === $shopHost) {
            return false;
        }

        return (bool) preg_match(self::FONT_EXTENSIONS_PATTERN, $href);
    }

    /**
     * @param  array<string, mixed>  $parts
     */
    private function buildUrl(array $parts): string
    {
        $url = '';

        if (! empty($parts['scheme'])) {
            $url .= $parts['scheme'] . '://';
        }

        if (! empty($parts['user'])) {
            $url .= $parts['user'];
            if (! empty($parts['pass'])) {
                $url .= ':' . $parts['pass'];
            }
            $url .= '@';
        }

        if (! empty($parts['host'])) {
            $url .= $parts['host'];
        }

        if (! empty($parts['port'])) {
            $url .= ':' . $parts['port'];
        }

        $url .= isset($parts['path']) ? (string) $parts['path'] : '';

        if (! empty($parts['query'])) {
            $url .= '?' . $parts['query'];
        }

        if (! empty($parts['fragment'])) {
            $url .= '#' . $parts['fragment'];
        }

        return $url;
    }
}
