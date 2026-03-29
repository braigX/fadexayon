<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptimizationRunResource extends JsonResource
{
    /**
     * @var list<string>
     */
    private const STEP_ORDER = [
        'validate_target',
        'cache_prepare',
        'render_page',
        'analyze_css',
        'build_css',
        'build_used_css',
        'scan_performance',
        'build_html',
        'validate_artifact',
        'publish_cache',
    ];

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentStep = null;

        if ($this->relationLoaded('steps')) {
            $currentStep = $this->steps->firstWhere('status', 'running')
                ?? $this->steps->sortByDesc('updated_at')->first();
        }

        return [
            'id' => $this->id,
            'optimization_target_id' => $this->optimization_target_id,
            'target_url' => $this->optimizationTarget?->prestashopShopUrl?->url,
            'target_canonical_url' => $this->optimizationTarget?->prestashopShopUrl?->canonical_url,
            'target_page_type' => $this->optimizationTarget?->page_type,
            'run_number' => $this->run_number,
            'status' => $this->status,
            'trigger_type' => $this->trigger_type,
            'total_variants' => (int) ($this->total_variants ?? 0),
            'completed_variants' => (int) ($this->completed_variants ?? 0),
            'failed_variants' => (int) ($this->failed_variants ?? 0),
            'progress_percent' => (int) ($this->progress_percent ?? 0),
            'current_variant_label' => $this->current_variant_label,
            'current_step_name' => $currentStep?->step_name,
            'current_step_status' => $currentStep?->status,
            'step_details' => $this->relationLoaded('steps') ? $this->buildStepDetails() : [],
            'variants' => is_array($this->variants_json) ? $this->variants_json : [],
            'started_at' => $this->started_at,
            'finished_at' => $this->finished_at,
            'duration_ms' => $this->duration_ms,
            'failure_reason' => $this->failure_reason,
            'css_reports' => $this->relationLoaded('cssReports')
                ? OptimizationCssReportResource::collection($this->cssReports)->resolve()
                : [],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildStepDetails(): array
    {
        $stepsByName = $this->steps->keyBy('step_name');
        $details = [];
        $currentVariantLabel = $this->current_variant_label;
        $cssOptimizationEnabled = (bool) ($this->optimizationTarget?->prestashopStore?->optimizationSetting?->css_optimization_enabled ?? true);
        $criticalCssEnabled = (bool) ($this->optimizationTarget?->prestashopStore?->optimizationSetting?->generate_critical_css ?? true);

        foreach (self::STEP_ORDER as $stepName) {
            $step = $stepsByName->get($stepName);

            if (! $step) {
                $details[] = [
                    'step_name' => $stepName,
                    'status' => 'pending',
                    'display_status' => 'pending',
                    'reason' => null,
                ];

                continue;
            }

            $summary = is_array($step->output_summary_json['last']['summary'] ?? null)
                ? $step->output_summary_json['last']['summary']
                : [];
            $displayStatus = $this->resolveDisplayStatus(
                $stepName,
                $step,
                $summary,
                $currentVariantLabel,
                $cssOptimizationEnabled,
                $criticalCssEnabled
            );

            $details[] = [
                'step_name' => $stepName,
                'status' => $step->status,
                'display_status' => $displayStatus,
                'reason' => $this->resolveStepReason(
                    $stepName,
                    $displayStatus,
                    $summary,
                    $step->error_summary,
                    $cssOptimizationEnabled,
                    $criticalCssEnabled
                ),
            ];
        }

        return $details;
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    private function resolveDisplayStatus(
        string $stepName,
        mixed $step,
        array $summary,
        ?string $currentVariantLabel,
        bool $cssOptimizationEnabled,
        bool $criticalCssEnabled
    ): string
    {
        if (in_array($stepName, ['analyze_css', 'build_css', 'build_used_css'], true) && ! $cssOptimizationEnabled) {
            return 'disabled';
        }

        if ($stepName === 'build_css' && ! $criticalCssEnabled) {
            return 'disabled';
        }

        $input = is_array($step->input_summary_json) ? $step->input_summary_json : [];
        $lastVariantLog = is_array($step->output_summary_json['last'] ?? null) ? $step->output_summary_json['last'] : [];
        $inputVariantLabel = $input['variant_label'] ?? null;
        $outputVariantLabel = $lastVariantLog['variant_label'] ?? null;
        $isRunLevelStep = $stepName === 'scan_performance';
        $isCurrentVariantStep = $isRunLevelStep
            || $currentVariantLabel === null
            || $inputVariantLabel === $currentVariantLabel
            || $outputVariantLabel === $currentVariantLabel;

        if (! $isCurrentVariantStep) {
            return 'pending';
        }

        return match ((string) $step->status) {
            'completed' => 'completed',
            'running' => 'running',
            'failed' => 'failed',
            default => 'pending',
        };
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    private function resolveStepReason(
        string $stepName,
        string $displayStatus,
        array $summary,
        ?string $errorSummary,
        bool $cssOptimizationEnabled,
        bool $criticalCssEnabled
    ): ?string
    {
        if ($displayStatus === 'failed') {
            return $errorSummary;
        }

        if (in_array($stepName, ['analyze_css', 'build_css', 'build_used_css'], true) && $displayStatus === 'disabled' && ! $cssOptimizationEnabled) {
            return 'css_optimization_disabled';
        }

        if ($stepName === 'build_css' && $displayStatus === 'disabled' && ! $criticalCssEnabled) {
            return 'critical_css_disabled';
        }

        if ($stepName === 'scan_performance' && ($summary['reused_existing_report'] ?? false)) {
            return 'performance_report_ready';
        }

        return null;
    }
}
