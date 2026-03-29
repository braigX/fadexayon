<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\OptimizationCssReportResource;
use App\Models\OptimizationTarget;
use App\Models\OptimizationCssReport;
use App\Models\PrestashopShop;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class WorkspaceShopCssReportController extends ApiController
{
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

        $candidateReports = OptimizationCssReport::query()
            ->with(['optimizationTarget.prestashopShopUrl'])
            ->whereHas('optimizationTarget', function ($query) use ($shop): void {
                $query->where('prestashop_shop_id', $shop->id);
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereHas('optimizationTarget.prestashopShopUrl', function ($shopUrlQuery) use ($search): void {
                    $shopUrlQuery
                        ->where('url', 'like', '%' . $search . '%')
                        ->orWhere('canonical_url', 'like', '%' . $search . '%');
                });
            })
            ->when($pageType !== '', function ($query) use ($pageType): void {
                $query->whereHas('optimizationTarget.prestashopShopUrl', function ($shopUrlQuery) use ($pageType): void {
                    $shopUrlQuery->where('page_type', $pageType);
                });
            })
            ->when($deviceClass !== '', function ($query) use ($deviceClass): void {
                $query->where('device_class', $deviceClass);
            })
            ->orderByDesc('created_at')
            ->get();

        $optimizedPageCounts = OptimizationTarget::query()
            ->selectRaw('prestashop_shop_urls.page_type as page_type, optimization_targets.device_class as device_class, COUNT(DISTINCT optimization_targets.prestashop_shop_url_id) as optimized_pages_count')
            ->join('prestashop_shop_urls', 'prestashop_shop_urls.id', '=', 'optimization_targets.prestashop_shop_url_id')
            ->where('optimization_targets.prestashop_shop_id', $shop->id)
            ->whereIn('optimization_targets.status', ['completed', 'completed_with_errors'])
            ->groupBy('prestashop_shop_urls.page_type', 'optimization_targets.device_class')
            ->get()
            ->keyBy(static fn ($row): string => ((string) ($row->page_type ?? 'unknown')) . '|' . ((string) ($row->device_class ?? 'desktop')));

        $groupedReports = $candidateReports
            ->filter(static fn (OptimizationCssReport $report): bool => $report->optimizationTarget?->prestashopShopUrl !== null)
            ->groupBy(static function (OptimizationCssReport $report): string {
                $pageType = (string) ($report->optimizationTarget?->prestashopShopUrl?->page_type ?? 'unknown');
                $device = (string) ($report->device_class ?? 'desktop');

                return $pageType . '|' . $device;
            })
            ->map(function (Collection $reports, string $groupKey) use ($optimizedPageCounts): OptimizationCssReport {
                /** @var OptimizationCssReport $report */
                $report = $reports->first();
                $optimizedPageCount = max(1, (int) ($optimizedPageCounts->get($groupKey)->optimized_pages_count ?? 1));
                $report->setAttribute('optimized_page_count', $optimizedPageCount);
                $report->setAttribute('estimated_total_css_bytes', (int) $report->total_css_bytes * $optimizedPageCount);
                $report->setAttribute('estimated_total_used_css_bytes', (int) $report->total_used_css_bytes * $optimizedPageCount);

                return $report;
            })
            ->values();

        $totalReports = $groupedReports->count();
        $page = max(1, (int) $request->integer('page', 1));
        $pagedReports = $groupedReports->forPage($page, $perPage)->values();
        $pagedIds = $pagedReports->pluck('id')->all();

        $reports = new LengthAwarePaginator(
            OptimizationCssReport::query()
                ->with([
                    'stylesheets',
                    'optimizationTarget.prestashopShopUrl',
                    'optimizationArtifactVersion',
                ])
                ->whereIn('id', $pagedIds)
                ->get()
                ->sortBy(static fn (OptimizationCssReport $report): int => array_search($report->id, $pagedIds, true))
                ->map(function (OptimizationCssReport $report) use ($pagedReports): OptimizationCssReport {
                    /** @var OptimizationCssReport|null $selected */
                    $selected = $pagedReports->firstWhere('id', $report->id);
                    if ($selected instanceof OptimizationCssReport) {
                        $report->setAttribute('optimized_page_count', (int) ($selected->getAttribute('optimized_page_count') ?? 1));
                        $report->setAttribute('estimated_total_css_bytes', (int) ($selected->getAttribute('estimated_total_css_bytes') ?? $report->total_css_bytes));
                        $report->setAttribute('estimated_total_used_css_bytes', (int) ($selected->getAttribute('estimated_total_used_css_bytes') ?? $report->total_used_css_bytes));
                    }

                    return $report;
                })
                ->values(),
            $totalReports,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $optimizedPagesCount = (int) $groupedReports->sum(static fn (OptimizationCssReport $report): int => (int) ($report->getAttribute('optimized_page_count') ?? 0));
        $totalOriginalCssBytes = (int) $groupedReports->sum(static fn (OptimizationCssReport $report): int => (int) ($report->getAttribute('estimated_total_css_bytes') ?? 0));
        $totalOptimizedCssBytes = (int) $groupedReports->sum(static fn (OptimizationCssReport $report): int => (int) ($report->getAttribute('estimated_total_used_css_bytes') ?? 0));
        $avgOriginalCssPerPage = $optimizedPagesCount > 0
            ? (int) round($totalOriginalCssBytes / $optimizedPagesCount)
            : 0;
        $avgOptimizedCssPerPage = $optimizedPagesCount > 0
            ? (int) round($totalOptimizedCssBytes / $optimizedPagesCount)
            : 0;
        $improvementRatio = $avgOriginalCssPerPage > 0
            ? round(max(0, 1 - ($avgOptimizedCssPerPage / $avgOriginalCssPerPage)), 4)
            : 0;

        $summary = [
            'optimized_pages_count' => $optimizedPagesCount,
            'avg_original_css_per_page' => $avgOriginalCssPerPage,
            'avg_optimized_css_per_page' => $avgOptimizedCssPerPage,
            'improvement_ratio' => $improvementRatio,
        ];

        return $this->paginated(
            $reports,
            OptimizationCssReportResource::class,
            'CSS reports fetched.',
            [
                'summary' => $summary,
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
