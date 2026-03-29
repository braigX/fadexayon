<?php

namespace App\Jobs\Performance;

use App\Models\OptimizationRun;
use App\Models\PrestashopShopUrl;
use App\Services\Optimization\OptimizationRunService;
use App\Services\Optimization\PageTypeProfileService;
use App\Services\Performance\FontUsageReportService;
use App\Services\Performance\PerformanceReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunPageTypePerformanceScanJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 900;

    public function __construct(
        public readonly string $runId,
        public readonly string $shopUrlId,
        public readonly string $finalStatus,
    ) {
        $this->onQueue('default');
    }

    public function handle(
        PerformanceReportService $performanceReportService,
        FontUsageReportService $fontUsageReportService,
        PageTypeProfileService $pageTypeProfileService,
        OptimizationRunService $runService
    ): void {
        $run = OptimizationRun::query()
            ->with(['optimizationTarget.prestashopShopUrl', 'optimizationTarget'])
            ->find($this->runId);

        if (! $run instanceof OptimizationRun || ! $run->optimizationTarget) {
            return;
        }

        $target = $run->optimizationTarget;
        if ($run->status === 'failed' || $target->current_optimization_run_id !== $run->id) {
            return;
        }

        $shopUrl = PrestashopShopUrl::query()
            ->with('prestashopShop')
            ->find($this->shopUrlId);

        if (! $shopUrl instanceof PrestashopShopUrl) {
            $this->completeRunWithError($runService, $run, $target, 'Performance scan could not start because the URL was not found.');
            return;
        }

        $scanStep = $runService->startStep($run, 'scan_performance', [
            'shop_url_id' => $shopUrl->id,
            'url' => $shopUrl->url,
            'page_type_id' => $shopUrl->page_type_id,
            'page_type' => $shopUrl->page_type,
        ]);

        try {
            if (! $pageTypeProfileService->needsPerformanceReport($shopUrl)) {
                $runService->completeStep($scanStep, [
                    'reused_existing_report' => true,
                    'page_type_id' => $shopUrl->page_type_id,
                    'page_type' => $shopUrl->page_type,
                ]);

                $runService->markRunCompleted($run, $this->finalStatus);
                $runService->markTargetStatus($target, $this->finalStatus === 'completed' ? 'completed' : 'completed_with_errors');

                return;
            }

            $report = $performanceReportService->scanReport($shopUrl->url);

            $shopUrl->forceFill([
                'mobile_score' => $report['mobile']['score'] ?? null,
                'desktop_score' => $report['desktop']['score'] ?? null,
                'scan_report_json' => $report,
                'last_scanned_at' => now(),
            ])->save();

            $pageTypeProfileService->syncPerformanceReport($shopUrl, $report);

            $fontUsageSynced = false;
            try {
                if ($pageTypeProfileService->needsFontUsageReport($shopUrl)) {
                    $fontUsageReport = $fontUsageReportService->scanReport($shopUrl->url);
                    $pageTypeProfileService->syncFontUsageReport($shopUrl, $fontUsageReport);
                    $fontUsageSynced = true;
                }
            } catch (Throwable $fontError) {
                Log::warning('prestaload.optimization.font_usage_scan.failed', [
                    'run_id' => $run->id,
                    'shop_url_id' => $shopUrl->id,
                    'url' => $shopUrl->url,
                    'message' => $fontError->getMessage(),
                ]);
            }

            $runService->completeStep($scanStep, [
                'provider' => $report['provider'] ?? null,
                'scanned_url' => $report['scanned_url'] ?? null,
                'mobile_score' => $report['mobile']['score'] ?? null,
                'desktop_score' => $report['desktop']['score'] ?? null,
                'font_usage_synced' => $fontUsageSynced,
                'page_type_id' => $shopUrl->page_type_id,
                'page_type' => $shopUrl->page_type,
            ]);

            if ($run->fresh()?->status === 'failed' || $target->fresh()?->current_optimization_run_id !== $run->id) {
                return;
            }

            $runService->markRunCompleted($run, $this->finalStatus);
            $runService->markTargetStatus($target, $this->finalStatus === 'completed' ? 'completed' : 'completed_with_errors');
        } catch (Throwable $e) {
            $runService->failStep($scanStep, $e->getMessage());
            $pageTypeProfileService->markProfileFailedForUrl($shopUrl);

            Log::error('prestaload.optimization.scan.failed', [
                'run_id' => $run->id,
                'shop_url_id' => $shopUrl->id,
                'url' => $shopUrl->url,
                'message' => $e->getMessage(),
            ]);

            $this->completeRunWithError($runService, $run, $target, $e->getMessage());
        }
    }

    private function completeRunWithError(
        OptimizationRunService $runService,
        OptimizationRun $run,
        mixed $target,
        string $message
    ): void {
        $runService->markRunCompleted($run, 'completed_with_errors');
        $run->failure_reason = mb_substr($message, 0, 65535);
        $run->save();

        if ($target) {
            $runService->markTargetStatus($target, 'completed_with_errors', $message);
        }
    }
}
