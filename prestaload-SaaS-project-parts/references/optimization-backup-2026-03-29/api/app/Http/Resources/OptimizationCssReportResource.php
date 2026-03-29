<?php

namespace App\Http\Resources;

use App\Services\Optimization\CssDeliveryStrategyService;
use App\Services\Optimization\PageTypeAssetRuleService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptimizationCssReportResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $stylesheets = $this->relationLoaded('stylesheets') ? $this->stylesheets : collect();
        $stylesheetPayload = OptimizationCssReportStylesheetResource::collection($stylesheets)->resolve();
        $artifactMeta = is_array($this->optimizationArtifactVersion?->meta_json) ? $this->optimizationArtifactVersion->meta_json : [];
        $validation = is_array($artifactMeta['validation'] ?? null) ? $artifactMeta['validation'] : [];
        $validationSummary = is_array($validation['summary'] ?? null) ? $validation['summary'] : [];
        $criticalCss = is_array($artifactMeta['critical_css'] ?? null) ? $artifactMeta['critical_css'] : [];
        $usedCss = is_array($artifactMeta['used_css'] ?? null) ? $artifactMeta['used_css'] : [];
        $deliveryStrategy = $this->optimizationTarget?->prestashopShopUrl
            ? app(PageTypeAssetRuleService::class)->resolveStylesheetActions(
                $this->optimizationTarget->prestashopShopUrl,
                (string) $this->device_class,
                $stylesheetPayload,
                ((int) ($usedCss['bytes'] ?? 0)) > 0
            )
            : app(CssDeliveryStrategyService::class)->verifyStylesheets(
                $this->final_url ?: $this->optimizationTarget?->prestashopShopUrl?->url,
                $this->optimizationTarget?->prestashopShopUrl?->page_type,
                $stylesheetPayload
            );

        return [
            'id' => $this->id,
            'optimization_run_id' => $this->optimization_run_id,
            'optimization_target_id' => $this->optimization_target_id,
            'optimization_artifact_version_id' => $this->optimization_artifact_version_id,
            'variant_key' => $this->variant_key,
            'variant_label' => $this->variant_label,
            'device_class' => $this->device_class,
            'status_code' => $this->status_code !== null ? (int) $this->status_code : null,
            'final_url' => $this->final_url,
            'shop_url' => $this->optimizationTarget?->prestashopShopUrl?->url,
            'canonical_url' => $this->optimizationTarget?->prestashopShopUrl?->canonical_url,
            'page_type' => $this->optimizationTarget?->prestashopShopUrl?->page_type,
            'optimized_page_count' => max(1, (int) ($this->getAttribute('optimized_page_count') ?? 1)),
            'stylesheet_count' => (int) $this->stylesheet_count,
            'total_css_bytes' => (int) ($this->getAttribute('estimated_total_css_bytes') ?? $this->total_css_bytes),
            'total_used_css_bytes' => (int) ($this->getAttribute('estimated_total_used_css_bytes') ?? $this->total_used_css_bytes),
            'unused_css_bytes' => max(
                0,
                (int) ($this->getAttribute('estimated_total_css_bytes') ?? $this->total_css_bytes)
                    - (int) ($this->getAttribute('estimated_total_used_css_bytes') ?? $this->total_used_css_bytes)
            ),
            'used_ratio' => (float) $this->used_ratio,
            'unused_ratio' => (float) $this->unused_ratio,
            'scroll_height' => $this->scroll_height !== null ? (int) $this->scroll_height : null,
            'viewport_height' => $this->viewport_height !== null ? (int) $this->viewport_height : null,
            'console_message_count' => (int) $this->console_message_count,
            'duration_ms' => $this->duration_ms !== null ? (int) $this->duration_ms : null,
            'delivery_strategy' => $deliveryStrategy['summary'],
            'validation' => [
                'failed_checks' => array_values(array_filter(
                    is_array($validation['failed_checks'] ?? null) ? $validation['failed_checks'] : [],
                    static fn ($check): bool => is_string($check) && $check !== ''
                )),
                'visual_diff_ratio' => isset($validationSummary['visual_diff_ratio']) ? (float) $validationSummary['visual_diff_ratio'] : null,
                'visual_diff_pixels' => isset($validationSummary['visual_diff_pixels']) ? (int) $validationSummary['visual_diff_pixels'] : null,
                'visual_total_pixels' => isset($validationSummary['visual_total_pixels']) ? (int) $validationSummary['visual_total_pixels'] : null,
                'visual_dimensions_match' => array_key_exists('visual_dimensions_match', $validationSummary) ? (bool) $validationSummary['visual_dimensions_match'] : null,
            ],
            'critical_css' => [
                'mode' => isset($criticalCss['mode']) ? (string) $criticalCss['mode'] : null,
                'capped' => array_key_exists('capped', $criticalCss) ? (bool) $criticalCss['capped'] : null,
                'bytes' => isset($criticalCss['bytes']) ? (int) $criticalCss['bytes'] : 0,
                'max_bytes' => isset($criticalCss['max_bytes']) ? (int) $criticalCss['max_bytes'] : null,
                'original_bytes' => isset($criticalCss['original_bytes']) ? (int) $criticalCss['original_bytes'] : null,
                'simplified_bytes' => isset($criticalCss['simplified_bytes']) ? (int) $criticalCss['simplified_bytes'] : null,
                'injected' => array_key_exists('critical_css_injected', $artifactMeta['html_adjustments'] ?? [])
                    ? (bool) $artifactMeta['html_adjustments']['critical_css_injected']
                    : false,
            ],
            'used_css' => [
                'mode' => isset($usedCss['mode']) ? (string) $usedCss['mode'] : null,
                'bytes' => isset($usedCss['bytes']) ? (int) $usedCss['bytes'] : 0,
                'sha256' => isset($usedCss['sha256']) ? (string) $usedCss['sha256'] : null,
                'generated' => isset($usedCss['mode']) && $usedCss['mode'] === 'generated',
                'injected' => array_key_exists('used_css_injected', $artifactMeta['html_adjustments'] ?? [])
                    ? (bool) $artifactMeta['html_adjustments']['used_css_injected']
                    : false,
            ],
            'created_at' => $this->created_at,
            'stylesheets' => $deliveryStrategy['stylesheets'],
        ];
    }
}
