<?php

namespace App\Services\Optimization;

use App\Models\OptimizationArtifactVersion;
use App\Models\OptimizationCssReport;
use App\Models\OptimizationCssReportStylesheet;
use App\Models\OptimizationRun;
use App\Models\OptimizationTarget;
use App\Models\PrestashopShopPageTypeCssReport;
use App\Models\PrestashopShopPageTypeCssReportStylesheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OptimizationCssReportService
{
    /**
     * @param  array<string, mixed>  $analysis
     */
    public function storeAnalysis(
        OptimizationTarget $target,
        OptimizationRun $run,
        array $analysis,
        ?string $variantKey = null,
        ?string $variantLabel = null,
        ?OptimizationArtifactVersion $artifact = null
    ): OptimizationCssReport {
        return DB::transaction(function () use ($target, $run, $analysis, $variantKey, $variantLabel, $artifact): OptimizationCssReport {
            $summary = is_array($analysis['summary'] ?? null) ? $analysis['summary'] : [];
            $scroll = is_array($analysis['scroll_summary'] ?? null) ? $analysis['scroll_summary'] : [];
            $stylesheets = array_values(array_filter(
                is_array($analysis['stylesheets'] ?? null) ? $analysis['stylesheets'] : [],
                static fn ($row): bool => is_array($row)
            ));

            $report = OptimizationCssReport::query()->create([
                'id' => (string) Str::uuid(),
                'optimization_target_id' => $target->id,
                'optimization_run_id' => $run->id,
                'optimization_artifact_version_id' => $artifact?->id,
                'variant_key' => $variantKey,
                'variant_label' => $variantLabel,
                'device_class' => (string) ($analysis['device_class'] ?? 'desktop'),
                'final_url' => isset($analysis['final_url']) ? (string) $analysis['final_url'] : null,
                'status_code' => isset($analysis['status_code']) ? (int) $analysis['status_code'] : null,
                'stylesheet_count' => (int) ($summary['stylesheet_count'] ?? count($stylesheets)),
                'total_css_bytes' => (int) ($summary['total_css_bytes'] ?? 0),
                'total_used_css_bytes' => (int) ($summary['total_used_css_bytes'] ?? 0),
                'used_ratio' => (float) ($summary['used_ratio'] ?? 0),
                'unused_ratio' => (float) ($summary['unused_ratio'] ?? 0),
                'scroll_height' => isset($scroll['final_scroll_height']) ? (int) $scroll['final_scroll_height'] : null,
                'viewport_height' => isset($scroll['viewport_height']) ? (int) $scroll['viewport_height'] : null,
                'console_message_count' => count(is_array($analysis['console_messages'] ?? null) ? $analysis['console_messages'] : []),
                'duration_ms' => isset($analysis['duration_ms']) ? (int) $analysis['duration_ms'] : null,
            ]);

            foreach ($stylesheets as $position => $stylesheet) {
                OptimizationCssReportStylesheet::query()->create([
                    'id' => (string) Str::uuid(),
                    'optimization_css_report_id' => $report->id,
                    'position' => $position + 1,
                    'style_sheet_key' => isset($stylesheet['style_sheet_id']) ? (string) $stylesheet['style_sheet_id'] : null,
                    'source_url' => isset($stylesheet['url']) ? (string) $stylesheet['url'] : null,
                    'origin' => isset($stylesheet['origin']) ? (string) $stylesheet['origin'] : null,
                    'is_inline' => (bool) ($stylesheet['inline'] ?? false),
                    'is_disabled' => (bool) ($stylesheet['disabled'] ?? false),
                    'bytes' => (int) ($stylesheet['bytes'] ?? 0),
                    'used_bytes' => (int) ($stylesheet['used_bytes'] ?? 0),
                    'used_ratio' => (float) ($stylesheet['used_ratio'] ?? 0),
                    'rule_count' => isset($stylesheet['rule_count']) ? (int) $stylesheet['rule_count'] : null,
                    'minified_bytes' => isset($stylesheet['minified_bytes']) ? (int) $stylesheet['minified_bytes'] : null,
                ]);
            }

            return $report->load('stylesheets');
        });
    }

    public function attachArtifact(OptimizationCssReport $report, OptimizationArtifactVersion $artifact): void
    {
        $report->optimization_artifact_version_id = $artifact->id;
        $report->save();
    }

    public function storeReusedPageTypeReport(
        OptimizationTarget $target,
        OptimizationRun $run,
        PrestashopShopPageTypeCssReport $pageTypeReport,
        ?string $variantKey = null,
        ?string $variantLabel = null,
        ?OptimizationArtifactVersion $artifact = null
    ): OptimizationCssReport {
        return DB::transaction(function () use ($target, $run, $pageTypeReport, $variantKey, $variantLabel, $artifact): OptimizationCssReport {
            $coverage = is_array($pageTypeReport->coverage_json ?? null) ? $pageTypeReport->coverage_json : [];

            $report = OptimizationCssReport::query()->create([
                'id' => (string) Str::uuid(),
                'optimization_target_id' => $target->id,
                'optimization_run_id' => $run->id,
                'optimization_artifact_version_id' => $artifact?->id,
                'variant_key' => $variantKey,
                'variant_label' => $variantLabel,
                'device_class' => (string) $pageTypeReport->device_class,
                'final_url' => $target->normalized_url ?: (isset($coverage['final_url']) ? (string) $coverage['final_url'] : null),
                'status_code' => isset($coverage['status_code']) ? (int) $coverage['status_code'] : null,
                'stylesheet_count' => (int) $pageTypeReport->stylesheet_count,
                'total_css_bytes' => (int) $pageTypeReport->total_css_bytes,
                'total_used_css_bytes' => (int) $pageTypeReport->total_used_css_bytes,
                'used_ratio' => (float) $pageTypeReport->used_ratio,
                'unused_ratio' => (float) $pageTypeReport->unused_ratio,
                'scroll_height' => isset($coverage['scroll_height']) ? (int) $coverage['scroll_height'] : null,
                'viewport_height' => isset($coverage['viewport_height']) ? (int) $coverage['viewport_height'] : null,
                'console_message_count' => isset($coverage['console_message_count']) ? (int) $coverage['console_message_count'] : 0,
                'duration_ms' => isset($coverage['duration_ms']) ? (int) $coverage['duration_ms'] : null,
            ]);

            foreach ($pageTypeReport->stylesheets as $position => $stylesheet) {
                /** @var PrestashopShopPageTypeCssReportStylesheet $stylesheet */
                OptimizationCssReportStylesheet::query()->create([
                    'id' => (string) Str::uuid(),
                    'optimization_css_report_id' => $report->id,
                    'position' => $position + 1,
                    'style_sheet_key' => $stylesheet->style_sheet_key,
                    'source_url' => $stylesheet->source_url,
                    'origin' => $stylesheet->origin,
                    'is_inline' => (bool) $stylesheet->is_inline,
                    'is_disabled' => (bool) $stylesheet->is_disabled,
                    'bytes' => (int) $stylesheet->bytes,
                    'used_bytes' => (int) $stylesheet->used_bytes,
                    'used_ratio' => (float) $stylesheet->used_ratio,
                    'rule_count' => $stylesheet->rule_count !== null ? (int) $stylesheet->rule_count : null,
                    'minified_bytes' => $stylesheet->minified_bytes !== null ? (int) $stylesheet->minified_bytes : null,
                ]);
            }

            return $report->load('stylesheets');
        });
    }
}
