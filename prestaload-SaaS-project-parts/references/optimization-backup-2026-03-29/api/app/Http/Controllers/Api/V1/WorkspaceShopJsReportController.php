<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\OptimizationJsReportResource;
use App\Models\OptimizationTarget;
use App\Models\PrestashopShop;
use App\Models\PrestashopStoreOptimizationSetting;
use App\Models\PrestashopShopPageTypeProfile;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Services\Optimization\PageTypeAssetRuleService;
use App\Services\Performance\JsScanReportService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class WorkspaceShopJsReportController extends ApiController
{
    public function __construct(
        private readonly JsScanReportService $jsScanReportService,
        private readonly PageTypeAssetRuleService $pageTypeAssetRuleService,
    ) {
    }

    public function index(Request $request, Workspace $workspace, PrestashopShop $shop): JsonResponse
    {
        $this->assertWorkspaceMember($request, $workspace);

        if ($shop->prestashopStore?->workspace_id !== $workspace->id) {
            throw new AuthorizationException('This shop does not belong to the selected workspace.');
        }

        $perPage = min(100, max(1, (int) $request->integer('per_page', 25)));
        $search = trim((string) $request->string('search', ''));
        $pageType = trim((string) $request->string('page_type', ''));
        $deviceClass = trim((string) $request->string('device_class', ''));

        $profiles = PrestashopShopPageTypeProfile::query()
            ->with(['pageType', 'scanSourceUrl'])
            ->where('prestashop_shop_id', $shop->id)
            ->whereNotNull('scan_report_json')
            ->orderByDesc('last_scanned_at')
            ->get();
        $storeOptimizationSettings = array_merge(
            PrestashopStoreOptimizationSetting::defaults(),
            $shop->prestashopStore?->optimizationSetting?->only([
                'delay_ads_analytics_scripts',
                'skip_lazy_load_js_patterns',
            ]) ?? []
        );

        $optimizedPageCounts = OptimizationTarget::query()
            ->selectRaw('prestashop_shop_urls.page_type_id as page_type_id, optimization_targets.device_class as device_class, COUNT(DISTINCT optimization_targets.prestashop_shop_url_id) as optimized_pages_count')
            ->join('prestashop_shop_urls', 'prestashop_shop_urls.id', '=', 'optimization_targets.prestashop_shop_url_id')
            ->where('optimization_targets.prestashop_shop_id', $shop->id)
            ->whereIn('optimization_targets.status', ['completed', 'completed_with_errors'])
            ->groupBy('prestashop_shop_urls.page_type_id', 'optimization_targets.device_class')
            ->get()
            ->keyBy(static fn ($row): string => ((string) ($row->page_type_id ?? '')) . '|' . ((string) ($row->device_class ?? 'desktop')));

        $rows = $profiles
            ->flatMap(function (PrestashopShopPageTypeProfile $profile) use ($optimizedPageCounts, $storeOptimizationSettings): array {
                $result = [];

                foreach (['desktop', 'mobile'] as $device) {
                    $optimizedPageCount = max(
                        1,
                        (int) ($optimizedPageCounts->get($profile->page_type_id . '|' . $device)->optimized_pages_count ?? 1)
                    );
                    $row = $this->jsScanReportService->buildDeviceRow(
                        $profile,
                        $device,
                        $optimizedPageCount,
                        $storeOptimizationSettings
                    );

                    $row = $this->pageTypeAssetRuleService->resolveScriptActionsForProfile(
                        $profile,
                        $device,
                        $optimizedPageCount,
                        $storeOptimizationSettings
                    ) ?? $row;

                    if (is_array($row)) {
                        $result[] = $row;
                    }
                }

                return $result;
            })
            ->filter(function (array $row) use ($search, $pageType, $deviceClass): bool {
                if ($pageType !== '' && (string) ($row['page_type'] ?? '') !== $pageType) {
                    return false;
                }

                if ($deviceClass !== '' && (string) ($row['device_class'] ?? '') !== $deviceClass) {
                    return false;
                }

                if ($search === '') {
                    return true;
                }

                $haystacks = [
                    (string) ($row['shop_url'] ?? ''),
                    (string) ($row['page_type'] ?? ''),
                    (string) ($row['page_type_name'] ?? ''),
                ];

                foreach ($haystacks as $haystack) {
                    if ($haystack !== '' && str_contains(mb_strtolower($haystack), mb_strtolower($search))) {
                        return true;
                    }
                }

                return false;
            })
            ->values();

        $totalReports = $rows->count();
        $page = max(1, (int) $request->integer('page', 1));
        $pagedRows = $rows->forPage($page, $perPage)->values();

        $reports = new LengthAwarePaginator(
            $pagedRows,
            $totalReports,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $optimizedPagesCount = (int) $rows->sum(static fn (array $row): int => (int) ($row['optimized_page_count'] ?? 0));
        $totalOriginalJsBytes = (int) $rows->sum(static fn (array $row): int => (int) ($row['original_js_bytes'] ?? 0));
        $totalOptimizedJsBytes = (int) $rows->sum(static fn (array $row): int => (int) ($row['optimized_js_bytes'] ?? 0));
        $avgOriginalJsPerPage = $optimizedPagesCount > 0 ? (int) round($totalOriginalJsBytes / $optimizedPagesCount) : 0;
        $avgOptimizedJsPerPage = $optimizedPagesCount > 0 ? (int) round($totalOptimizedJsBytes / $optimizedPagesCount) : 0;
        $improvementRatio = $avgOriginalJsPerPage > 0
            ? round(max(0, 1 - ($avgOptimizedJsPerPage / $avgOriginalJsPerPage)), 4)
            : 0;

        return $this->paginated(
            $reports,
            OptimizationJsReportResource::class,
            'JS reports fetched.',
            [
                'summary' => [
                    'optimized_pages_count' => $optimizedPagesCount,
                    'avg_original_js_per_page' => $avgOriginalJsPerPage,
                    'avg_optimized_js_per_page' => $avgOptimizedJsPerPage,
                    'improvement_ratio' => $improvementRatio,
                ],
            ]
        );
    }

    private function assertWorkspaceMember(Request $request, Workspace $workspace): void
    {
        $exists = WorkspaceMember::query()
            ->where('workspace_id', $workspace->id)
            ->where('user_id', $request->user()?->id)
            ->where('status', 'active')
            ->exists();

        if (! $exists) {
            throw new AuthorizationException('You do not have access to this workspace.');
        }
    }
}
