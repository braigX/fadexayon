<?php

namespace App\Services\Optimization;

use App\Models\OptimizationRun;
use App\Models\OptimizationRunStep;
use App\Models\OptimizationTarget;
use App\Models\PrestashopShop;
use App\Models\PrestashopShopUrl;
use App\Models\Workspace;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class OptimizationRunService
{
    /**
     * @return array{target: OptimizationTarget, run: OptimizationRun}
     */
    public function queueUrlOptimization(
        Workspace $workspace,
        PrestashopShop $shop,
        PrestashopShopUrl $shopUrl,
        string $triggerType = 'manual'
    ): array
    {
        $store = $shop->prestashopStore;
        if (! $store || $store->workspace_id !== $workspace->id) {
            throw new RuntimeException('Shop does not belong to the selected workspace.');
        }

        return $this->queueShopUrlOptimization($shop, $shopUrl, $triggerType);
    }

    /**
     * @return array{target: OptimizationTarget, run: OptimizationRun}
     */
    public function queueShopUrlOptimization(
        PrestashopShop $shop,
        PrestashopShopUrl $shopUrl,
        string $triggerType = 'manual'
    ): array
    {
        if ($shopUrl->prestashop_shop_id !== $shop->id) {
            throw new RuntimeException('URL does not belong to the selected shop.');
        }

        $store = $shop->prestashopStore;
        if (! $store) {
            throw new RuntimeException('Shop store could not be resolved.');
        }

        $result = DB::transaction(function () use ($shop, $shopUrl, $store, $triggerType): array {
            $target = OptimizationTarget::query()->firstOrNew([
                'prestashop_shop_url_id' => $shopUrl->id,
                'device_class' => 'desktop',
            ]);

            $target->fill([
                'prestashop_store_id' => $store->id,
                'prestashop_shop_id' => $shop->id,
                'prestashop_shop_url_id' => $shopUrl->id,
                'page_type' => $shopUrl->page_type,
                'normalized_url' => $shopUrl->canonical_url ?: $shopUrl->url,
                'device_class' => 'desktop',
                'last_error' => null,
            ]);
            $target->save();

            $runNumber = ((int) $target->optimizationRuns()->max('run_number')) + 1;
            $run = OptimizationRun::query()->create([
                'id' => (string) Str::uuid(),
                'optimization_target_id' => $target->id,
                'run_number' => $runNumber,
                'trigger_type' => $triggerType,
                'status' => 'created',
                'total_variants' => 0,
                'completed_variants' => 0,
                'failed_variants' => 0,
                'progress_percent' => 0,
            ]);

            foreach ([
                ['validate_target', 'api'],
                ['cache_prepare', 'module'],
                ['render_page', 'browser-worker'],
                ['analyze_css', 'optimizer-worker'],
                ['build_css', 'optimizer-worker'],
                ['build_used_css', 'optimizer-worker'],
                ['scan_performance', 'scanner'],
                ['build_html', 'api'],
                ['validate_artifact', 'api'],
                ['publish_cache', 'module'],
            ] as [$stepName, $workerType]) {
                OptimizationRunStep::query()->create([
                    'id' => (string) Str::uuid(),
                    'optimization_run_id' => $run->id,
                    'step_name' => $stepName,
                    'worker_type' => $workerType,
                    'status' => 'created',
                    'queue_name' => match ($stepName) {
                        'render_page' => 'optimization-render',
                        'scan_performance' => 'default',
                        default => 'optimization-default',
                    },
                ]);
            }

            return [
                'target' => $target,
                'run' => $run,
            ];
        });

        return $result;
    }

    public function markRunQueued(OptimizationTarget $target, OptimizationRun $run): void
    {
        DB::transaction(function () use ($target, $run): void {
            $target->status = 'queued';
            $target->current_optimization_run_id = $run->id;
            $target->last_error = null;
            $target->save();

            $run->status = 'queued';
            $run->save();

            $run->steps()->where('status', 'created')->update([
                'status' => 'queued',
                'updated_at' => now(),
            ]);
        });
    }

    public function markDispatchFailed(OptimizationTarget $target, OptimizationRun $run, string $message): void
    {
        DB::transaction(function () use ($target, $run, $message): void {
            $target->status = 'failed';
            $target->current_optimization_run_id = $run->id;
            $target->last_error = mb_substr($message, 0, 65535);
            $target->save();

            $run->status = 'failed';
            $run->failure_reason = mb_substr($message, 0, 65535);
            $run->started_at = now();
            $run->finished_at = now();
            $run->duration_ms = 0;
            $run->save();

            $run->steps()->where('status', 'created')->update([
                'status' => 'failed',
                'error_summary' => mb_substr($message, 0, 65535),
                'started_at' => now(),
                'finished_at' => now(),
                'duration_ms' => 0,
                'updated_at' => now(),
            ]);
        });
    }

    public function markTargetStatus(OptimizationTarget $target, string $status, ?string $error = null): void
    {
        $target->status = $status;
        $target->last_error = $error;
        $target->save();
    }

    public function markRunStarted(OptimizationRun $run, string $status): void
    {
        $run->status = $status;
        $run->started_at ??= now();
        $run->save();
    }

    /**
     * @param  array<int, array<string, mixed>>  $variants
     */
    public function setRunVariants(OptimizationRun $run, array $variants): void
    {
        $run->variants_json = $variants;
        $run->total_variants = count($variants);
        $run->completed_variants = 0;
        $run->failed_variants = 0;
        $run->progress_percent = 0;
        $run->current_variant_label = $variants[0]['label'] ?? null;
        $run->save();
    }

    public function markCurrentVariant(OptimizationRun $run, ?string $label): void
    {
        $run->current_variant_label = $label;
        $run->save();
    }

    public function markVariantCompleted(OptimizationRun $run): void
    {
        $run->completed_variants = (int) $run->completed_variants + 1;
        $run->progress_percent = $this->calculateVariantProgress($run);
        $run->save();
    }

    public function markVariantFailed(OptimizationRun $run): void
    {
        $run->failed_variants = (int) $run->failed_variants + 1;
        $run->progress_percent = $this->calculateVariantProgress($run);
        $run->save();
    }

    public function markRunCompleted(OptimizationRun $run, string $status): void
    {
        $run->status = $status;
        $run->progress_percent = 100;
        $run->current_variant_label = null;
        $run->finished_at = now();
        $run->duration_ms = $this->calculateRunDuration($run);
        $run->save();
    }

    public function markRunFailed(OptimizationRun $run, string $message): void
    {
        $run->status = 'failed';
        $run->failure_reason = mb_substr($message, 0, 65535);
        $run->current_variant_label = null;
        $run->finished_at = now();
        $run->duration_ms = $this->calculateRunDuration($run);
        $run->save();
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function startStep(OptimizationRun $run, string $stepName, array $input = []): OptimizationRunStep
    {
        $step = $run->steps()->where('step_name', $stepName)->firstOrFail();
        $step->status = 'running';
        $step->started_at = now();
        $step->input_summary_json = $this->compactStepSummary($input);
        $step->error_summary = null;
        $step->save();

        return $step;
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function queueStep(OptimizationRun $run, string $stepName, array $input = []): OptimizationRunStep
    {
        $step = $run->steps()->where('step_name', $stepName)->firstOrFail();
        $step->status = 'queued';
        $step->input_summary_json = $this->compactStepSummary($input);
        $step->error_summary = null;
        $step->save();

        return $step;
    }

    /**
     * @param  array<string, mixed>  $output
     */
    public function completeStep(OptimizationRunStep $step, array $output = []): void
    {
        $step->status = 'completed';
        $step->finished_at = now();
        $step->duration_ms = $this->calculateStepDuration($step);
        $step->output_summary_json = $this->appendVariantStepLog($step, 'completed', $output, null);
        $step->input_summary_json = null;
        $step->save();
    }

    public function failStep(OptimizationRunStep $step, string $message): void
    {
        $step->status = 'failed';
        $step->finished_at = now();
        $step->duration_ms = $this->calculateStepDuration($step);
        $step->error_summary = mb_substr($message, 0, 65535);
        $step->output_summary_json = $this->appendVariantStepLog($step, 'failed', [], $message);
        $step->input_summary_json = null;
        $step->save();
    }

    private function calculateRunDuration(OptimizationRun $run): ?int
    {
        $startedAt = $this->toTimestampMs($run->started_at);
        $finishedAt = $this->toTimestampMs($run->finished_at);

        if ($startedAt === null || $finishedAt === null) {
            return null;
        }

        return max(0, $finishedAt - $startedAt);
    }

    private function calculateStepDuration(OptimizationRunStep $step): ?int
    {
        $startedAt = $this->toTimestampMs($step->started_at);
        $finishedAt = $this->toTimestampMs($step->finished_at);

        if ($startedAt === null || $finishedAt === null) {
            return null;
        }

        return max(0, $finishedAt - $startedAt);
    }

    private function toTimestampMs(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof CarbonInterface) {
            return ((int) $value->getTimestamp()) * 1000 + (int) floor(((int) $value->micro) / 1000);
        }

        try {
            $parsed = Carbon::parse((string) $value);

            return ((int) $parsed->getTimestamp()) * 1000 + (int) floor(((int) $parsed->micro) / 1000);
        } catch (\Throwable) {
            return null;
        }
    }

    private function calculateVariantProgress(OptimizationRun $run): int
    {
        $total = max(0, (int) $run->total_variants);
        if ($total === 0) {
            return 0;
        }

        $processed = min($total, max(0, (int) $run->completed_variants + (int) $run->failed_variants));

        return (int) floor(($processed / $total) * 100);
    }

    /**
     * @param  array<string, mixed>  $summary
     * @return array<string, mixed>
     */
    private function compactStepSummary(array $summary): array
    {
        $result = [];

        foreach (['variant_label', 'variant_key', 'cacheable', 'cache_exists', 'status_code', 'html_bytes', 'optimized_html_bytes', 'html_sha256', 'artifact_id', 'device_class', 'stylesheet_count', 'total_css_bytes', 'total_used_css_bytes', 'unused_ratio', 'critical_css_bytes', 'critical_css_sha256', 'critical_css_mode', 'critical_css_capped', 'critical_css_max_bytes', 'critical_css_original_bytes', 'critical_css_simplified_bytes', 'used_css_bytes', 'used_css_sha256', 'used_css_mode', 'visual_diff_ratio', 'visual_diff_pixels'] as $key) {
            if (array_key_exists($key, $summary)) {
                $result[$key] = $summary[$key];
            }
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $output
     * @return array<string, mixed>
     */
    private function appendVariantStepLog(OptimizationRunStep $step, string $status, array $output, ?string $error): array
    {
        $existing = is_array($step->output_summary_json) ? $step->output_summary_json : [];
        $variantLogs = isset($existing['variant_logs']) && is_array($existing['variant_logs']) ? $existing['variant_logs'] : [];
        $input = is_array($step->input_summary_json) ? $step->input_summary_json : [];

        $variantLogs[] = [
            'variant_label' => $input['variant_label'] ?? null,
            'status' => $status,
            'started_at' => optional($step->started_at)->toIso8601String(),
            'finished_at' => optional($step->finished_at)->toIso8601String(),
            'duration_ms' => $step->duration_ms,
            'error' => $error !== null ? mb_substr($error, 0, 65535) : null,
            'summary' => $this->compactStepSummary($output),
        ];

        return [
            'variant_logs' => $variantLogs,
            'last' => end($variantLogs) ?: null,
        ];
    }
}
