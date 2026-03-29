<?php

namespace App\Services\Optimization;

class CssDeliveryStrategyService
{
    /**
     * @param  list<array<string, mixed>>  $stylesheets
     * @param  array<string, mixed>|null  $scanDeviceReport
     * @return array{
     *   summary: array<string, int>,
     *   stylesheets: list<array<string, mixed>>
     * }
     */
    public function verifyStylesheets(
        ?string $pageUrl,
        ?string $pageType,
        array $stylesheets,
        ?array $scanDeviceReport = null,
        bool $usedCssAvailable = false
    ): array {
        $classified = [];
        $summary = [
            'keep' => 0,
            'preload' => 0,
            'minify' => 0,
            'reduce' => 0,
            'reduce_minify' => 0,
            'remove' => 0,
        ];

        foreach ($stylesheets as $stylesheet) {
            $classification = $this->verifyStylesheet($pageUrl, $pageType, $stylesheet, $scanDeviceReport, $usedCssAvailable);
            $stylesheet['delivery_strategy'] = $classification;
            $classified[] = $stylesheet;

            $strategy = (string) ($classification['strategy'] ?? 'keep');
            if (! array_key_exists($strategy, $summary)) {
                $strategy = 'keep';
            }

            $summary[$strategy]++;
        }

        return [
            'summary' => $summary,
            'stylesheets' => $classified,
        ];
    }

    /**
     * @param  array<string, mixed>  $stylesheet
     * @param  array<string, mixed>|null  $scanDeviceReport
     * @return array{
     *   strategy: string,
     *   recommended_action: string,
     *   same_origin: bool,
     *   confidence: float,
     *   reasons: list<string>,
     *   evidence: array<string, mixed>
     * }
     */
    public function verifyStylesheet(
        ?string $pageUrl,
        ?string $pageType,
        array $stylesheet,
        ?array $scanDeviceReport = null,
        bool $usedCssAvailable = false
    ): array {
        $sourceUrl = trim((string) ($stylesheet['source_url'] ?? ''));
        $isInline = (bool) ($stylesheet['is_inline'] ?? false);
        if ($isInline) {
            return $this->verifyInlineStylesheet($stylesheet, $usedCssAvailable);
        }

        $isDisabled = (bool) ($stylesheet['is_disabled'] ?? false);
        $bytes = (int) ($stylesheet['bytes'] ?? 0);
        $usedBytes = (int) ($stylesheet['used_bytes'] ?? 0);
        $usedRatio = (float) ($stylesheet['used_ratio'] ?? 0);
        $position = (int) ($stylesheet['position'] ?? 0);
        $sameOrigin = $this->isSameOriginStylesheet($pageUrl, $sourceUrl);
        $unsafe = $this->isUnsafeStylesheet($sourceUrl, $pageType);
        $specialLibrary = $this->isSpecialLibrary($sourceUrl);
        $scanSignals = $this->extractCssScanSignals($sourceUrl, $scanDeviceReport);
        $hasScanEvidence = $scanSignals['unused_css_bytes'] !== null
            || $scanSignals['minify_css_bytes'] !== null
            || $scanSignals['render_blocking_ms'] !== null;
        $earlyStylesheet = $position > 0 && $position <= 2;
        $highUsage = $usedRatio >= 0.35 || $usedBytes >= 16384;
        $zeroUsage = $usedBytes <= 512 && $usedRatio <= 0.02;
        $highUnused = $usedRatio <= 0.12
            || ($scanSignals['unused_css_bytes'] !== null && $scanSignals['unused_css_bytes'] >= max(12288, (int) round($bytes * 0.45)));
        $renderBlocking = ($scanSignals['render_blocking_ms'] ?? 0) >= 120;
        $minifyGain = ($scanSignals['minify_css_bytes'] ?? 0) >= 2048;
        $strongUnusedBytes = ($scanSignals['unused_css_bytes'] ?? 0) >= max(12288, (int) round(max($bytes, 1) * 0.22));
        $scanPromotedReduce = $usedCssAvailable
            && $sameOrigin
            && $hasScanEvidence
            && ($strongUnusedBytes || ($renderBlocking && $highUnused) || ($minifyGain && $highUnused))
            && $this->canTrustScanDrivenReduce($sourceUrl, $pageType);
        $reasons = [];
        $confidence = 0.55;
        $strategy = 'keep';
        $reduceStrategy = $minifyGain ? 'reduce_minify' : 'reduce';

        if ($sourceUrl === '') {
            $reasons[] = 'missing_asset_url';
        }

        if ($isInline) {
            $reasons[] = 'inline_stylesheet';
        }

        if ($isDisabled) {
            $reasons[] = 'disabled_stylesheet';
        }

        if ($bytes <= 0) {
            $reasons[] = 'empty_stylesheet';
        }

        if (! $sameOrigin) {
            $reasons[] = 'external_origin';
        }

        if ($unsafe) {
            $reasons[] = 'unsafe_path';
        }

        if ($specialLibrary) {
            $reasons[] = 'special_library';
        }

        if ($earlyStylesheet) {
            $reasons[] = 'early_stylesheet';
        }

        if ($highUsage) {
            $reasons[] = 'high_usage';
        }

        if ($usedCssAvailable) {
            $reasons[] = 'used_css_available';
        }

        if ($hasScanEvidence) {
            $reasons[] = 'scan_verified';
        }

        if ($isInline || $isDisabled || $bytes <= 0 || $sourceUrl === '' || ! $sameOrigin) {
            $strategy = 'keep';
            $confidence = 0.85;
        } elseif ($usedCssAvailable && $hasScanEvidence && $position > 2 && $zeroUsage && ($scanSignals['unused_css_bytes'] ?? 0) >= max(8192, (int) round($bytes * 0.7))) {
            $strategy = 'remove';
            $confidence = 0.92;
            $reasons[] = 'zero_usage_confirmed';
            $reasons[] = 'high_unused_css_bytes';
        } elseif ($scanPromotedReduce) {
            $strategy = $reduceStrategy;
            $confidence = $strongUnusedBytes ? 0.88 : ($minifyGain ? 0.84 : 0.8);
            $reasons[] = 'scan_promoted_reduce';
            if ($highUnused) {
                $reasons[] = 'high_unused_css';
            }
            if ($strongUnusedBytes) {
                $reasons[] = 'high_unused_css_bytes';
            }
            if ($renderBlocking) {
                $reasons[] = 'render_blocking';
            }
            if ($minifyGain) {
                $reasons[] = 'minify_opportunity';
            }
        } elseif ($usedCssAvailable && $hasScanEvidence && $position > 2 && $highUnused) {
            $strategy = $reduceStrategy;
            $confidence = $minifyGain ? 0.82 : 0.76;
            $reasons[] = 'high_unused_css';
            if ($minifyGain) {
                $reasons[] = 'minify_opportunity';
            }
        } elseif ($sameOrigin && $hasScanEvidence && $minifyGain && ! $unsafe) {
            $strategy = 'minify';
            $confidence = $renderBlocking ? 0.76 : 0.7;
            $reasons[] = 'minify_opportunity';
            if ($renderBlocking) {
                $reasons[] = 'render_blocking';
            }
        } elseif ($unsafe || $specialLibrary || $earlyStylesheet || $highUsage) {
            $strategy = 'keep';
            $confidence = 0.85;
        } elseif ($sameOrigin && $position > 2 && $bytes >= 4096 && ($renderBlocking || $usedRatio <= 0.30)) {
            $strategy = 'preload';
            $confidence = $renderBlocking ? 0.74 : 0.64;
            if ($renderBlocking) {
                $reasons[] = 'render_blocking';
            }
            if ($usedRatio <= 0.30) {
                $reasons[] = 'non_critical_position';
            }
        } else {
            $strategy = 'keep';
            $confidence = 0.7;
        }

        return [
            'strategy' => $strategy,
            'recommended_action' => $strategy,
            'same_origin' => $sameOrigin,
            'confidence' => round($confidence, 4),
            'reasons' => array_values(array_unique($reasons)),
            'evidence' => [
                'bytes' => $bytes,
                'used_bytes' => $usedBytes,
                'used_ratio' => round($usedRatio, 4),
                'position' => $position,
                'same_origin' => $sameOrigin,
                'used_css_available' => $usedCssAvailable,
                'scan_verified' => $hasScanEvidence,
                'unused_css_bytes' => $scanSignals['unused_css_bytes'],
                'minify_css_bytes' => $scanSignals['minify_css_bytes'],
                'render_blocking_ms' => $scanSignals['render_blocking_ms'],
                'matched_audits' => $scanSignals['matched_audits'],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $stylesheet
     * @return array{
     *   strategy: string,
     *   recommended_action: string,
     *   same_origin: bool,
     *   confidence: float,
     *   reasons: list<string>,
     *   evidence: array<string, mixed>
     * }
     */
    private function verifyInlineStylesheet(array $stylesheet, bool $usedCssAvailable = false): array
    {
        $bytes = (int) ($stylesheet['bytes'] ?? 0);
        $usedBytes = (int) ($stylesheet['used_bytes'] ?? 0);
        $usedRatio = (float) ($stylesheet['used_ratio'] ?? 0);
        $position = (int) ($stylesheet['position'] ?? 0);
        $minifiedBytes = isset($stylesheet['minified_bytes']) ? (int) $stylesheet['minified_bytes'] : null;
        $isDisabled = (bool) ($stylesheet['is_disabled'] ?? false);

        $reasons = ['inline_stylesheet'];
        $strategy = 'keep';
        $confidence = 0.72;

        if ($isDisabled || $bytes <= 0) {
            $strategy = 'keep';
            $confidence = 0.88;
            $reasons[] = $isDisabled ? 'disabled_stylesheet' : 'empty_stylesheet';
        } else {
            $zeroUsage = $usedBytes <= 256 && $usedRatio <= 0.02;
            $substantialMinifyGain = $minifiedBytes !== null && $bytes > 0 && $minifiedBytes < max(0, $bytes - 128);
            $largeInlineBlock = $bytes >= 2048;

            if ($usedCssAvailable && $position > 2 && $zeroUsage) {
                $strategy = 'remove';
                $confidence = 0.9;
                $reasons[] = 'zero_usage_confirmed';
                $reasons[] = 'used_css_available';
            } elseif ($substantialMinifyGain || ($largeInlineBlock && $usedRatio <= 0.4)) {
                $strategy = 'minify';
                $confidence = $substantialMinifyGain ? 0.84 : 0.74;
                $reasons[] = 'minify_opportunity';
                if ($largeInlineBlock && $usedRatio <= 0.4) {
                    $reasons[] = 'large_inline_block';
                }
            } else {
                $strategy = 'keep';
                $confidence = 0.72;
            }
        }

        return [
            'strategy' => $strategy,
            'recommended_action' => $strategy,
            'same_origin' => true,
            'confidence' => round($confidence, 4),
            'reasons' => array_values(array_unique($reasons)),
            'evidence' => [
                'bytes' => $bytes,
                'used_bytes' => $usedBytes,
                'used_ratio' => round($usedRatio, 4),
                'position' => $position,
                'same_origin' => true,
                'used_css_available' => $usedCssAvailable,
                'minified_bytes' => $minifiedBytes,
            ],
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $stylesheets
     * @return array{
     *   summary: array<string, int>,
     *   stylesheets: list<array<string, mixed>>
     * }
     */
    public function classifyStylesheets(?string $pageUrl, ?string $pageType, array $stylesheets): array
    {
        $classified = [];
        $summary = [
            'keep_blocking' => 0,
            'preload_safe' => 0,
            'skip' => 0,
        ];

        foreach ($stylesheets as $stylesheet) {
            $classification = $this->classifyStylesheet($pageUrl, $pageType, $stylesheet);
            $stylesheet['delivery_strategy'] = $classification;
            $classified[] = $stylesheet;

            $strategy = (string) ($classification['strategy'] ?? 'skip');
            if (array_key_exists($strategy, $summary)) {
                $summary[$strategy]++;
            }
        }

        return [
            'summary' => $summary,
            'stylesheets' => $classified,
        ];
    }

    /**
     * @param  array<string, mixed>  $stylesheet
     * @return array{
     *   strategy: string,
     *   same_origin: bool,
     *   preload_safe: bool,
     *   reasons: list<string>
     * }
     */
    public function classifyStylesheet(?string $pageUrl, ?string $pageType, array $stylesheet): array
    {
        $sourceUrl = trim((string) ($stylesheet['source_url'] ?? ''));
        $isInline = (bool) ($stylesheet['is_inline'] ?? false);
        $isDisabled = (bool) ($stylesheet['is_disabled'] ?? false);
        $bytes = (int) ($stylesheet['bytes'] ?? 0);
        $usedRatio = (float) ($stylesheet['used_ratio'] ?? 0);
        $position = (int) ($stylesheet['position'] ?? 0);
        $sameOrigin = $this->isSameOriginStylesheet($pageUrl, $sourceUrl);

        $reasons = [];

        if ($isInline) {
            $reasons[] = 'inline_stylesheet';
        }

        if ($isDisabled) {
            $reasons[] = 'disabled_stylesheet';
        }

        if ($bytes <= 0) {
            $reasons[] = 'empty_stylesheet';
        }

        if (! $sameOrigin) {
            $reasons[] = 'external_origin';
        }

        if ($this->isUnsafeStylesheet($sourceUrl, $pageType)) {
            $reasons[] = 'unsafe_path';
        }

        if ($position > 0 && $position <= 2) {
            $reasons[] = 'early_stylesheet';
        }

        if ($bytes > 0 && $bytes < 4096) {
            $reasons[] = 'small_stylesheet';
        }

        if ($usedRatio >= 0.35) {
            $reasons[] = 'high_used_ratio';
        }

        if ($isInline || $isDisabled || $bytes <= 0) {
            return [
                'strategy' => 'skip',
                'same_origin' => $sameOrigin,
                'preload_safe' => false,
                'reasons' => $reasons,
            ];
        }

        $preloadSafe = $sameOrigin
            && ! $this->isUnsafeStylesheet($sourceUrl, $pageType)
            && $position > 2
            && $bytes >= 4096
            && $usedRatio <= 0.20;

        if ($preloadSafe) {
            $reasons[] = 'same_origin_non_critical';

            return [
                'strategy' => 'preload_safe',
                'same_origin' => true,
                'preload_safe' => true,
                'reasons' => $reasons,
            ];
        }

        return [
            'strategy' => 'keep_blocking',
            'same_origin' => $sameOrigin,
            'preload_safe' => false,
            'reasons' => $reasons,
        ];
    }

    private function isSameOriginStylesheet(?string $pageUrl, string $sourceUrl): bool
    {
        if ($sourceUrl === '') {
            return false;
        }

        if (str_starts_with($sourceUrl, '/')
            || str_starts_with($sourceUrl, './')
            || str_starts_with($sourceUrl, '../')) {
            return true;
        }

        $pageHost = parse_url((string) $pageUrl, PHP_URL_HOST);
        $sourceHost = parse_url($sourceUrl, PHP_URL_HOST);

        if (! is_string($pageHost) || $pageHost === '' || ! is_string($sourceHost) || $sourceHost === '') {
            return false;
        }

        return strcasecmp($pageHost, $sourceHost) === 0;
    }

    private function isUnsafeStylesheet(string $sourceUrl, ?string $pageType): bool
    {
        $normalized = strtolower($sourceUrl);

        foreach ([
            'checkout',
            'cart',
            'login',
            'customer',
            'account',
            'auth',
            'consent',
            'cookie',
            'captcha',
            'recaptcha',
        ] as $needle) {
            if (str_contains($normalized, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>|null  $scanDeviceReport
     * @return array{unused_css_bytes: int|null, minify_css_bytes: int|null, render_blocking_ms: int|null, matched_audits: list<string>}
     */
    private function extractCssScanSignals(string $sourceUrl, ?array $scanDeviceReport): array
    {
        if ($sourceUrl === '' || ! is_array($scanDeviceReport)) {
            return [
                'unused_css_bytes' => null,
                'minify_css_bytes' => null,
                'render_blocking_ms' => null,
                'matched_audits' => [],
            ];
        }

        $requestedAudits = is_array($scanDeviceReport['requested_audits'] ?? null) ? $scanDeviceReport['requested_audits'] : [];
        $matchedAudits = [];

        $unusedCssBytes = $this->extractAuditMetricForUrl(
            $requestedAudits,
            ['unused-css-rules', 'Reduce unused CSS'],
            $sourceUrl,
            ['wastedBytes', 'overallSavingsBytes']
        );
        if ($unusedCssBytes !== null) {
            $matchedAudits[] = 'unused_css';
        }

        $minifyCssBytes = $this->extractAuditMetricForUrl(
            $requestedAudits,
            ['unminified-css', 'Minify CSS'],
            $sourceUrl,
            ['wastedBytes', 'overallSavingsBytes']
        );
        if ($minifyCssBytes !== null) {
            $matchedAudits[] = 'minify_css';
        }

        $renderBlockingMs = $this->extractAuditMetricForUrl(
            $requestedAudits,
            ['render-blocking-resources', 'Render blocking requests'],
            $sourceUrl,
            ['wastedMs']
        );
        if ($renderBlockingMs !== null) {
            $matchedAudits[] = 'render_blocking';
        }

        return [
            'unused_css_bytes' => $unusedCssBytes,
            'minify_css_bytes' => $minifyCssBytes,
            'render_blocking_ms' => $renderBlockingMs,
            'matched_audits' => $matchedAudits,
        ];
    }

    /**
     * @param  array<string, mixed>  $requestedAudits
     * @param  list<string>  $auditKeys
     * @param  list<string>  $metricKeys
     */
    private function extractAuditMetricForUrl(array $requestedAudits, array $auditKeys, string $sourceUrl, array $metricKeys): ?int
    {
        $normalizedSource = $this->normalizeAssetReference($sourceUrl);
        if ($normalizedSource === '') {
            return null;
        }

        foreach ($auditKeys as $auditKey) {
            $audit = $requestedAudits[$auditKey] ?? null;
            if (! is_array($audit)) {
                continue;
            }

            $items = is_array($audit['details']['items'] ?? null) ? $audit['details']['items'] : [];
            $bestMatch = null;

            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $itemUrl = $item['url'] ?? $item['source'] ?? $item['request'] ?? null;
                if (! is_string($itemUrl) || $this->normalizeAssetReference($itemUrl) !== $normalizedSource) {
                    continue;
                }

                foreach ($metricKeys as $metricKey) {
                    $metricValue = $item[$metricKey] ?? null;
                    if (is_numeric($metricValue)) {
                        $bestMatch = max((int) $metricValue, $bestMatch ?? 0);
                    }
                }
            }

            if ($bestMatch !== null) {
                return $bestMatch;
            }
        }

        return null;
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

    private function isSpecialLibrary(string $sourceUrl): bool
    {
        $normalized = strtolower($sourceUrl);

        foreach ([
            'fontawesome',
            'ce-icons',
            'fonts.googleapis',
            'fonts.gstatic',
            'animate',
            'editor-preview',
            'share-buttons',
            'gsi',
            'material-icons',
        ] as $needle) {
            if (str_contains($normalized, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function canTrustScanDrivenReduce(string $sourceUrl, ?string $pageType): bool
    {
        if ($this->isUnsafeStylesheet($sourceUrl, $pageType)) {
            return false;
        }

        $normalized = strtolower($sourceUrl);

        foreach ([
            'fonts.googleapis',
            'fonts.gstatic',
            'gsi',
            'material-icons',
        ] as $needle) {
            if (str_contains($normalized, $needle)) {
                return false;
            }
        }

        return true;
    }
}
