<?php

namespace App\Services\Performance;

use App\Models\PrestashopShopPageTypeProfile;

class JsScanReportService
{
    /**
     * @return array<string, mixed>|null
     */
    public function buildDeviceRow(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass,
        int $optimizedPageCount = 1,
        array $settings = [],
    ): ?array {
        $report = is_array($profile->scan_report_json) ? $profile->scan_report_json : [];
        $deviceReport = is_array($report[$deviceClass] ?? null) ? $report[$deviceClass] : [];

        if ($deviceReport === []) {
            return null;
        }

        $unusedAudit = $this->resolveRequestedAudit($report, $deviceClass, [
            'unused-javascript',
            'Reduce unused JavaScript',
        ]);
        $minifyAudit = $this->resolveRequestedAudit($report, $deviceClass, [
            'unminified-javascript',
            'Minify JavaScript',
        ]);
        $bootupAudit = $this->resolveRequestedAudit($report, $deviceClass, [
            'bootup-time',
            'JavaScript execution time',
            'Reduce JavaScript execution time',
        ]);
        $legacyAudit = $this->resolveRequestedAudit($report, $deviceClass, [
            'legacy-javascript-insight',
            'Legacy JavaScript',
            'Avoid serving legacy JavaScript to modern browsers',
        ]);

        $scriptSummary = $this->resolveScriptSummary($deviceReport);
        $originalJsBytes = max(
            0,
            (int) ($scriptSummary['transferSize'] ?? 0) > 0
                ? (int) ($scriptSummary['transferSize'] ?? 0)
                : (int) ($report['page_metrics']['js_bytes'] ?? 0)
        );

        $scripts = $this->mergeAuditItems(
            is_array($unusedAudit['details']['items'] ?? null) ? $unusedAudit['details']['items'] : [],
            is_array($minifyAudit['details']['items'] ?? null) ? $minifyAudit['details']['items'] : [],
            is_array($bootupAudit['details']['items'] ?? null) ? $bootupAudit['details']['items'] : [],
            is_array($legacyAudit['details']['items'] ?? null) ? $legacyAudit['details']['items'] : [],
            $profile->scanSourceUrl?->url ?: $profile->prestashopShop?->url,
            $settings
        );

        $potentialSavingsBytes = array_sum(array_map(
            static fn (array $script): int => (int) ($script['effective_savings_bytes'] ?? 0),
            $scripts
        ));
        $optimizedJsBytes = max(0, $originalJsBytes - $potentialSavingsBytes);

        $actionSummary = [
            'keep' => 0,
            'load_on_interaction' => 0,
            'minify' => 0,
            'reduce' => 0,
            'reduce_minify' => 0,
            'remove' => 0,
        ];

        foreach ($scripts as $script) {
            $action = (string) ($script['action'] ?? 'keep');
            if (array_key_exists($action, $actionSummary)) {
                $actionSummary[$action]++;
            }
        }

        return [
            'id' => $profile->id . ':' . $deviceClass,
            'profile_id' => $profile->id,
            'device_class' => $deviceClass,
            'page_type' => (string) ($profile->pageType?->code ?? 'unknown'),
            'page_type_name' => $profile->pageType?->name,
            'shop_url' => $profile->scanSourceUrl?->url,
            'variant_label' => $profile->scanSourceUrl?->url,
            'optimized_page_count' => max(1, $optimizedPageCount),
            'provider' => (string) ($report['provider'] ?? $profile->scan_provider ?? ''),
            'performance_score' => isset($deviceReport['score']) ? (int) $deviceReport['score'] : null,
            'original_js_bytes' => $originalJsBytes * max(1, $optimizedPageCount),
            'optimized_js_bytes' => $optimizedJsBytes * max(1, $optimizedPageCount),
            'potential_savings_bytes' => $potentialSavingsBytes * max(1, $optimizedPageCount),
            'improvement_ratio' => $originalJsBytes > 0 ? round($potentialSavingsBytes / $originalJsBytes, 4) : 0,
            'script_request_count' => (int) ($scriptSummary['requestCount'] ?? 0),
            'unused_javascript' => [
                'display_value' => (string) ($unusedAudit['display_value'] ?? ''),
                'overall_savings_bytes' => $this->resolveOverallSavingsBytes($unusedAudit),
            ],
            'minified_javascript' => [
                'display_value' => (string) ($minifyAudit['display_value'] ?? ''),
                'overall_savings_bytes' => $this->resolveOverallSavingsBytes($minifyAudit),
            ],
            'action_summary' => $actionSummary,
            'scripts' => $scripts,
            'last_scanned_at' => optional($profile->last_scanned_at)?->toIso8601String(),
            'created_at' => optional($profile->created_at)?->toIso8601String(),
        ];
    }

    /**
     * @param  array<string, mixed>  $report
     * @param  list<string>  $candidateKeys
     * @return array<string, mixed>|null
     */
    private function resolveRequestedAudit(array $report, string $deviceClass, array $candidateKeys): ?array
    {
        $audits = is_array($report[$deviceClass]['requested_audits'] ?? null)
            ? $report[$deviceClass]['requested_audits']
            : [];

        foreach ($candidateKeys as $candidateKey) {
            if (isset($audits[$candidateKey]) && is_array($audits[$candidateKey])) {
                return $audits[$candidateKey];
            }
        }

        foreach ($audits as $audit) {
            if (! is_array($audit)) {
                continue;
            }

            $keys = array_filter([
                is_string($audit['id'] ?? null) ? $audit['id'] : null,
                is_string($audit['matched_id'] ?? null) ? $audit['matched_id'] : null,
                is_string($audit['title'] ?? null) ? $audit['title'] : null,
            ]);

            if (array_intersect($candidateKeys, $keys) !== []) {
                return $audit;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $deviceReport
     * @return array<string, mixed>|null
     */
    private function resolveScriptSummary(array $deviceReport): ?array
    {
        $items = $deviceReport['artifacts']['resource-summary']['details']['items'] ?? [];
        if (! is_array($items)) {
            return null;
        }

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $resourceType = (string) ($item['resourceType'] ?? '');
            $label = (string) ($item['label'] ?? '');

            if ($resourceType === 'script' || strcasecmp($label, 'Script') === 0) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param  list<array<string, mixed>>  $unusedItems
     * @param  list<array<string, mixed>>  $minifyItems
     * @param  list<array<string, mixed>>  $bootupItems
     * @param  list<array<string, mixed>>  $legacyItems
     * @return list<array<string, mixed>>
     */
    private function mergeAuditItems(
        array $unusedItems,
        array $minifyItems,
        array $bootupItems,
        array $legacyItems,
        ?string $sampleUrl,
        array $settings = []
    ): array
    {
        $scripts = [];
        $sampleHost = $this->extractHost($sampleUrl);
        $delayThirdPartyScripts = (bool) ($settings['delay_ads_analytics_scripts'] ?? true);
        $skipPatterns = array_values(array_filter(
            is_array($settings['skip_lazy_load_js_patterns'] ?? null) ? $settings['skip_lazy_load_js_patterns'] : [],
            static fn ($pattern): bool => is_string($pattern) && trim($pattern) !== ''
        ));

        foreach ($unusedItems as $item) {
            if (! is_array($item) || ! is_string($item['url'] ?? null)) {
                continue;
            }

            $key = $this->normalizeUrl($item['url']);
            $scripts[$key] ??= $this->baseScriptRow($item['url'], $sampleHost);
            $scripts[$key]['total_bytes'] = max((int) $scripts[$key]['total_bytes'], (int) ($item['totalBytes'] ?? 0));
            $scripts[$key]['unused_savings_bytes'] = max(
                (int) $scripts[$key]['unused_savings_bytes'],
                (int) ($item['wastedBytes'] ?? 0)
            );
        }

        foreach ($minifyItems as $item) {
            if (! is_array($item) || ! is_string($item['url'] ?? null)) {
                continue;
            }

            $key = $this->normalizeUrl($item['url']);
            $scripts[$key] ??= $this->baseScriptRow($item['url'], $sampleHost);
            $scripts[$key]['total_bytes'] = max((int) $scripts[$key]['total_bytes'], (int) ($item['totalBytes'] ?? 0));
            $scripts[$key]['minify_savings_bytes'] = max(
                (int) $scripts[$key]['minify_savings_bytes'],
                (int) ($item['wastedBytes'] ?? 0)
            );
        }

        foreach ($bootupItems as $item) {
            if (! is_array($item) || ! is_string($item['url'] ?? null)) {
                continue;
            }

            $key = $this->normalizeUrl($item['url']);
            $scripts[$key] ??= $this->baseScriptRow($item['url'], $sampleHost);
            $scripts[$key]['bootup_total_ms'] = max(
                (float) ($scripts[$key]['bootup_total_ms'] ?? 0),
                (float) ($item['total'] ?? 0)
            );
            $scripts[$key]['bootup_scripting_ms'] = max(
                (float) ($scripts[$key]['bootup_scripting_ms'] ?? 0),
                (float) ($item['scripting'] ?? 0)
            );
            $scripts[$key]['bootup_parse_ms'] = max(
                (float) ($scripts[$key]['bootup_parse_ms'] ?? 0),
                (float) ($item['scriptParseCompile'] ?? 0)
            );
        }

        foreach ($legacyItems as $item) {
            if (! is_array($item) || ! is_string($item['url'] ?? null)) {
                continue;
            }

            $key = $this->normalizeUrl($item['url']);
            $scripts[$key] ??= $this->baseScriptRow($item['url'], $sampleHost);
            $scripts[$key]['legacy_savings_bytes'] = max(
                (int) ($scripts[$key]['legacy_savings_bytes'] ?? 0),
                (int) ($item['wastedBytes'] ?? 0)
            );
        }

        foreach ($scripts as &$script) {
            $unusedSavings = (int) ($script['unused_savings_bytes'] ?? 0);
            $minifySavings = (int) ($script['minify_savings_bytes'] ?? 0);
            $legacySavings = (int) ($script['legacy_savings_bytes'] ?? 0);
            $bootupTotalMs = (float) ($script['bootup_total_ms'] ?? 0);
            $totalBytes = max(0, (int) ($script['total_bytes'] ?? 0));

            $script['effective_savings_bytes'] = max($unusedSavings, $minifySavings, $legacySavings);
            $script['savings_ratio'] = $totalBytes > 0
                ? round(((int) $script['effective_savings_bytes']) / $totalBytes, 4)
                : 0;
            $script['action'] = $this->resolveAction(
                $script,
                $unusedSavings,
                $minifySavings,
                $legacySavings,
                $bootupTotalMs,
                $delayThirdPartyScripts,
                $skipPatterns
            );
        }
        unset($script);

        usort($scripts, static function (array $left, array $right): int {
            return [(int) $right['effective_savings_bytes'], (int) $right['total_bytes']]
                <=> [(int) $left['effective_savings_bytes'], (int) $left['total_bytes']];
        });

        return array_values($scripts);
    }

    /**
     * @return array<string, mixed>
     */
    private function baseScriptRow(string $url, ?string $sampleHost): array
    {
        $host = $this->extractHost($url);

        return [
            'url' => $url,
            'host' => $host,
            'origin' => $host !== null && $sampleHost !== null && strcasecmp($host, $sampleHost) === 0
                ? 'same-origin'
                : 'third-party',
            'total_bytes' => 0,
            'unused_savings_bytes' => 0,
            'minify_savings_bytes' => 0,
            'legacy_savings_bytes' => 0,
            'bootup_total_ms' => 0.0,
            'bootup_scripting_ms' => 0.0,
            'bootup_parse_ms' => 0.0,
        ];
    }

    /**
     * @param  array<string, mixed>  $script
     * @param  list<string>  $skipPatterns
     */
    private function resolveAction(
        array $script,
        int $unusedSavings,
        int $minifySavings,
        int $legacySavings,
        float $bootupTotalMs,
        bool $delayThirdPartyScripts,
        array $skipPatterns
    ): string {
        $url = (string) ($script['url'] ?? '');

        if ($delayThirdPartyScripts
            && (string) ($script['origin'] ?? '') === 'third-party'
            && ($unusedSavings > 0 || $minifySavings > 0 || $legacySavings > 0 || $bootupTotalMs >= 250)
            && ! $this->matchesSkipPatterns($url, $skipPatterns)) {
            return 'load_on_interaction';
        }

        return match (true) {
            $unusedSavings > 0 && $minifySavings > 0 => 'reduce_minify',
            $unusedSavings > 0 => 'reduce',
            $bootupTotalMs >= 300 && $minifySavings > 0 => 'reduce_minify',
            $bootupTotalMs >= 300 => 'reduce',
            $legacySavings > 0 && $minifySavings > 0 => 'reduce_minify',
            $legacySavings > 0 => 'minify',
            $minifySavings > 0 => 'minify',
            default => 'keep',
        };
    }

    private function resolveOverallSavingsBytes(?array $audit): int
    {
        if (! is_array($audit)) {
            return 0;
        }

        return max(
            0,
            (int) ($audit['details']['overallSavingsBytes'] ?? 0)
        );
    }

    private function normalizeUrl(string $url): string
    {
        return strtolower(trim($url));
    }

    private function extractHost(?string $url): ?string
    {
        if (! is_string($url) || $url === '') {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return is_string($host) && $host !== '' ? strtolower($host) : null;
    }

    /**
     * @param  list<string>  $patterns
     */
    private function matchesSkipPatterns(string $url, array $patterns): bool
    {
        $haystack = strtolower($url);

        foreach ($patterns as $pattern) {
            $needle = strtolower(trim($pattern));
            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
