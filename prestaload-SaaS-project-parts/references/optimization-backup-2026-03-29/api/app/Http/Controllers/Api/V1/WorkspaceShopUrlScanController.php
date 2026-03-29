<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\PrestashopShopUrlResource;
use App\Models\PrestashopShop;
use App\Models\PrestashopShopUrl;
use App\Models\Workspace;
use App\Services\Optimization\PageTypeProfileService;
use App\Services\Performance\FontUsageReportService;
use App\Services\Performance\PerformanceReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WorkspaceShopUrlScanController extends WorkspaceShopUrlController
{
    public function store(
        Request $request,
        Workspace $workspace,
        PrestashopShop $shop,
        PrestashopShopUrl $shopUrl,
        PerformanceReportService $performanceReportService,
        FontUsageReportService $fontUsageReportService,
        PageTypeProfileService $pageTypeProfileService
    ): JsonResponse {
        $this->assertWorkspaceMember($request, $workspace);
        $this->assertWorkspaceShop($workspace, $shop);
        $this->assertShopUrl($shop, $shopUrl);

        $report = $performanceReportService->scanReport($shopUrl->url);

        $shopUrl->forceFill([
            'mobile_score' => $report['mobile']['score'] ?? null,
            'desktop_score' => $report['desktop']['score'] ?? null,
            'scan_report_json' => $report,
            'last_scanned_at' => now(),
        ])->save();
        $pageTypeProfileService->syncPerformanceReport($shopUrl, $report);

        try {
            if ($pageTypeProfileService->needsFontUsageReport($shopUrl)) {
                $fontUsageReport = $fontUsageReportService->scanReport($shopUrl->url);
                $pageTypeProfileService->syncFontUsageReport($shopUrl, $fontUsageReport);
            }
        } catch (\Throwable $fontError) {
            Log::warning('prestaload.optimization.font_usage_scan.failed', [
                'shop_url_id' => $shopUrl->id,
                'url' => $shopUrl->url,
                'message' => $fontError->getMessage(),
            ]);
        }

        return $this->success(
            new PrestashopShopUrlResource($shopUrl->fresh(['prestashopShop.prestashopStore', 'latestOptimizationTarget.currentOptimizationRun', 'latestOptimizationTarget.latestOptimizationRun'])),
            'Performance scan completed.'
        );
    }

    private function assertShopUrl(PrestashopShop $shop, PrestashopShopUrl $shopUrl): void
    {
        if ($shopUrl->prestashop_shop_id !== $shop->id) {
            abort(403, 'This URL does not belong to the selected shop.');
        }
    }
}
