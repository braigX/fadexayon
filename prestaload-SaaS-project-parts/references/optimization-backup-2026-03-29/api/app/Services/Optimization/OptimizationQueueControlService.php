<?php

namespace App\Services\Optimization;

use App\Models\OptimizationRun;
use App\Models\OptimizationTarget;
use App\Models\PrestashopShop;
use App\Models\Workspace;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class OptimizationQueueControlService
{
    private const ACTIVE_STATUSES = ['created', 'queued', 'preparing_cache', 'rendering', 'publishing', 'scanning'];

    private const STALE_MINUTES = 10;

    public function expireStaleRunsForShop(Workspace $workspace, PrestashopShop $shop): void
    {
        $staleBefore = now()->subMinutes(self::STALE_MINUTES);

        $runs = OptimizationRun::query()
            ->with(['optimizationTarget'])
            ->whereHas('optimizationTarget', function ($query) use ($workspace, $shop): void {
                $query
                    ->where('prestashop_shop_id', $shop->id)
                    ->whereHas('prestashopStore', function ($storeQuery) use ($workspace): void {
                        $storeQuery->where('workspace_id', $workspace->id);
                    });
            })
            ->whereIn('status', ['preparing_cache', 'rendering', 'publishing', 'scanning'])
            ->where('updated_at', '<', $staleBefore)
            ->get();

        foreach ($runs as $run) {
            $this->cancelRun($workspace, $shop, $run, 'Optimization timed out after inactivity.');
        }
    }

    public function cancelRun(Workspace $workspace, PrestashopShop $shop, OptimizationRun $run, string $reason = 'Optimization cancelled by user.'): void
    {
        $run->loadMissing(['optimizationTarget']);
        $target = $run->optimizationTarget;

        if (! $target instanceof OptimizationTarget || $target->prestashop_shop_id !== $shop->id || $target->prestashopStore?->workspace_id !== $workspace->id) {
            throw new RuntimeException('This optimization run does not belong to the selected shop.');
        }

        DB::transaction(function () use ($run, $target, $reason): void {
            $this->deleteQueuedJobsForRun($run->id);

            foreach ($run->steps()->whereIn('status', ['created', 'queued', 'running'])->get() as $step) {
                $step->status = 'failed';
                $step->error_summary = mb_substr($reason, 0, 65535);
                $step->started_at ??= now();
                $step->finished_at = now();
                $step->duration_ms = $this->calculateDurationMs($step->started_at, $step->finished_at);
                $step->save();
            }

            foreach ($run->artifactVersions()->get() as $artifact) {
                if (! empty($artifact->storage_prefix)) {
                    Storage::disk('local')->deleteDirectory((string) $artifact->storage_prefix);
                }

                $artifact->status = 'failed';
                $artifact->meta_json = array_merge($artifact->meta_json ?? [], [
                    'rollback_reason' => $reason,
                    'rolled_back_at' => now()->toIso8601String(),
                ]);
                $artifact->save();
            }

            $run->status = 'failed';
            $run->failure_reason = mb_substr($reason, 0, 65535);
            $run->current_variant_label = null;
            $run->finished_at = now();
            $run->duration_ms = $this->calculateDurationMs($run->started_at, $run->finished_at);
            $run->save();

            if ($target->current_optimization_run_id === $run->id) {
                $target->current_optimization_run_id = null;
            }

            $target->status = 'discovered';
            $target->last_error = mb_substr($reason, 0, 65535);
            $target->save();
        });
    }

    public function clearQueuedRunsForShop(Workspace $workspace, PrestashopShop $shop, string $reason = 'Optimization queue cleared by user.'): int
    {
        $runs = OptimizationRun::query()
            ->with(['optimizationTarget'])
            ->whereHas('optimizationTarget', function ($query) use ($workspace, $shop): void {
                $query
                    ->where('prestashop_shop_id', $shop->id)
                    ->whereHas('prestashopStore', function ($storeQuery) use ($workspace): void {
                        $storeQuery->where('workspace_id', $workspace->id);
                    });
            })
            ->whereIn('status', ['created', 'queued'])
            ->get();

        foreach ($runs as $run) {
            $this->cancelRun($workspace, $shop, $run, $reason);
        }

        return $runs->count();
    }

    private function deleteQueuedJobsForRun(string $runId): void
    {
        DB::table('jobs')
            ->where('payload', 'like', '%' . $runId . '%')
            ->delete();
    }

    private function calculateDurationMs(mixed $startedAt, mixed $finishedAt): ?int
    {
        $start = $this->toTimestampMs($startedAt);
        $finish = $this->toTimestampMs($finishedAt);

        if ($start === null || $finish === null) {
            return null;
        }

        return max(0, $finish - $start);
    }

    private function toTimestampMs(mixed $value): ?int
    {
        if (! $value instanceof CarbonInterface) {
            try {
                $value = filled($value) ? Carbon::parse((string) $value) : null;
            } catch (\Throwable) {
                $value = null;
            }
        }

        if (! $value instanceof CarbonInterface) {
            return null;
        }

        return ((int) $value->getTimestamp()) * 1000 + (int) floor(((int) $value->micro) / 1000);
    }
}
