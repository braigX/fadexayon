<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\OptimizationFontReportResource;
use App\Models\OptimizationTarget;
use App\Models\PrestashopShop;
use App\Models\PrestashopShopPageTypeProfile;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Services\Optimization\PageTypeAssetRuleService;
use App\Services\Performance\FontUsageDecisionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class WorkspaceShopFontReportController extends ApiController
{
    public function __construct(
        private readonly FontUsageDecisionService $fontUsageDecisionService,
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
            ->whereNotNull('font_usage_json')
            ->orderByDesc('last_font_scanned_at')
            ->get();

        foreach ($profiles as $profile) {
            foreach (['desktop', 'mobile'] as $device) {
                $this->pageTypeAssetRuleService->syncFontRulesForProfile($profile, $device);
            }
        }

        $profiles->load('assetRules');

        $optimizedPageCounts = OptimizationTarget::query()
            ->selectRaw('prestashop_shop_urls.page_type_id as page_type_id, optimization_targets.device_class as device_class, COUNT(DISTINCT optimization_targets.prestashop_shop_url_id) as optimized_pages_count')
            ->join('prestashop_shop_urls', 'prestashop_shop_urls.id', '=', 'optimization_targets.prestashop_shop_url_id')
            ->where('optimization_targets.prestashop_shop_id', $shop->id)
            ->whereIn('optimization_targets.status', ['completed', 'completed_with_errors'])
            ->groupBy('prestashop_shop_urls.page_type_id', 'optimization_targets.device_class')
            ->get()
            ->keyBy(static fn ($row): string => ((string) ($row->page_type_id ?? '')) . '|' . ((string) ($row->device_class ?? 'desktop')));

        $rows = $profiles
            ->flatMap(function (PrestashopShopPageTypeProfile $profile) use ($optimizedPageCounts): array {
                $result = [];

                foreach (['desktop', 'mobile'] as $device) {
                    $optimizedPageCount = max(
                        1,
                        (int) ($optimizedPageCounts->get($profile->page_type_id . '|' . $device)->optimized_pages_count ?? 1)
                    );

                    $row = $this->fontUsageDecisionService->buildDeviceRow(
                        $profile,
                        $device,
                        $optimizedPageCount
                    );

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
        $totalDeclaredFonts = (int) $rows->sum(static fn (array $row): int => (int) ($row['declared_fonts_estimated'] ?? 0));
        $totalUsedFonts = (int) $rows->sum(static fn (array $row): int => (int) ($row['used_fonts_estimated'] ?? 0));
        $totalAboveFoldFonts = (int) $rows->sum(static fn (array $row): int => (int) ($row['above_the_fold_fonts_estimated'] ?? 0));
        $totalDuplicateIconIssues = (int) $rows->sum(static fn (array $row): int => (int) ($row['duplicate_icon_font_count'] ?? 0));

        $avgDeclaredFontsPerPage = $optimizedPagesCount > 0 ? round($totalDeclaredFonts / $optimizedPagesCount, 1) : 0;
        $avgUsedFontsPerPage = $optimizedPagesCount > 0 ? round($totalUsedFonts / $optimizedPagesCount, 1) : 0;
        $avgAboveFoldFontsPerPage = $optimizedPagesCount > 0 ? round($totalAboveFoldFonts / $optimizedPagesCount, 1) : 0;

        return $this->paginated(
            $reports,
            OptimizationFontReportResource::class,
            'Font reports fetched.',
            [
                'summary' => [
                    'optimized_pages_count' => $optimizedPagesCount,
                    'avg_declared_fonts_per_page' => $avgDeclaredFontsPerPage,
                    'avg_used_fonts_per_page' => $avgUsedFontsPerPage,
                    'avg_above_the_fold_fonts_per_page' => $avgAboveFoldFontsPerPage,
                    'duplicate_icon_issues_count' => $totalDuplicateIconIssues,
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
