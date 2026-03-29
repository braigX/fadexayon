<?php

namespace App\Services\Optimization;

use App\Models\OptimizationArtifactVersion;
use App\Models\OptimizationCssReport;
use App\Models\OptimizationCssReportStylesheet;
use App\Models\OptimizationRun;
use App\Models\OptimizationRunStep;
use App\Models\OptimizationTarget;
use App\Models\PrestashopShopPageTypeCssArtifact;
use App\Models\PrestashopShopPageTypeProfile;
use App\Models\PrestashopShop;
use App\Models\PrestashopShopUrl;
use App\Models\Workspace;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class OptimizationCleanupService
{
    public function __construct(private readonly ModuleCacheService $moduleCacheService)
    {
    }

    /**
     * @return array{module: array<string, mixed>, api: array<string, int>}
     */
    public function purgeUrl(Workspace $workspace, PrestashopShop $shop, PrestashopShopUrl $shopUrl, bool $purgeModule = true): array
    {
        $module = [
            'shop_url_id' => $shopUrl->id,
            'variants_count' => 0,
            'purged_count' => 0,
            'results' => [],
        ];

        if ($purgeModule) {
            $store = $shop->prestashopStore;
            $variantsResponse = $this->moduleCacheService->getCacheVariants($store, [
                'shop_id' => $shop->prestashop_shop_id,
                'url' => $shopUrl->url,
            ]);

            $variants = array_values(array_filter(
                is_array($variantsResponse['variants'] ?? null) ? $variantsResponse['variants'] : [],
                static fn ($variant): bool => is_array($variant) && is_array($variant['variant'] ?? null)
            ));

            $results = [];
            foreach ($variants as $variantRow) {
                $variant = $variantRow['variant'];
                $prepare = $this->moduleCacheService->prepareCache($store, [
                    'shop_id' => $shop->prestashop_shop_id,
                    'shop_url_id' => $shopUrl->id,
                    'url' => $shopUrl->url,
                    'language_iso' => $variant['language_iso'] ?? $shopUrl->language_iso,
                    'currency_iso' => $variant['currency_iso'] ?? null,
                    'device_class' => $variant['device_class'] ?? 'desktop',
                    'login_state' => $variant['login_state'] ?? 'guest',
                    'theme_hash' => $variant['theme_hash'] ?? null,
                ]);

                $purge = $this->moduleCacheService->purgeCache($store, [
                    'variant_key' => $prepare['variant_key'] ?? null,
                ]);

                $results[] = [
                    'label' => $variantRow['label'] ?? '',
                    'variant_key' => $prepare['variant_key'] ?? null,
                    'purged' => (bool) ($purge['purged'] ?? false),
                    'deleted_html' => (bool) ($purge['deleted_html'] ?? false),
                    'deleted_meta' => (bool) ($purge['deleted_meta'] ?? false),
                ];
            }

            $module = [
                'shop_url_id' => $shopUrl->id,
                'variants_count' => count($results),
                'purged_count' => count(array_filter($results, static fn ($result): bool => (bool) $result['purged'])),
                'results' => $results,
            ];
        }

        $relatedShopIds = $this->resolveRelatedShopIds($workspace, $shop);

        $deleted = $this->deleteOptimizationDataForTargets(
            OptimizationTarget::query()
                ->whereIn('prestashop_shop_id', $relatedShopIds)
                ->where('normalized_url', $shopUrl->url)
                ->get(['id'])
        );

        return [
            'module' => $module,
            'api' => $deleted,
        ];
    }

    /**
     * @return array{module: array<string, mixed>, api: array<string, int>, related_shop_ids: array<int, string>}
     */
    public function purgeShop(Workspace $workspace, PrestashopShop $shop): array
    {
        $store = $shop->prestashopStore;
        $this->moduleCacheService->forgetCacheVariants($store, (int) $shop->prestashop_shop_id);
        $modulePurge = $this->moduleCacheService->purgeAllCache($store, [
            'shop_id' => $shop->prestashop_shop_id,
        ]);

        $relatedShopIds = $this->resolveRelatedShopIds($workspace, $shop);
        $deleted = $this->deleteOptimizationDataForTargets(
            OptimizationTarget::query()
                ->whereIn('prestashop_shop_id', $relatedShopIds)
                ->get(['id'])
        );

        $pageTypeDeleted = $this->deletePageTypeDataForShops($relatedShopIds);

        return [
            'related_shop_ids' => $relatedShopIds->values()->all(),
            'module' => is_array($modulePurge['data'] ?? null) ? $modulePurge['data'] : [],
            'api' => array_merge($deleted, $pageTypeDeleted),
        ];
    }

    /**
     * @return Collection<int, string>
     */
    private function resolveRelatedShopIds(Workspace $workspace, PrestashopShop $shop): Collection
    {
        return PrestashopShop::query()
            ->where('prestashop_shop_id', $shop->prestashop_shop_id)
            ->whereHas('prestashopStore', function ($query) use ($workspace): void {
                $query->where('workspace_id', $workspace->id);
            })
            ->pluck('id');
    }

    /**
     * @param  Collection<int, OptimizationTarget>|Collection<int, mixed>  $targets
     * @return array<string, int>
     */
    private function deleteOptimizationDataForTargets(Collection $targets): array
    {
        $targetIds = $targets->pluck('id')->filter()->values();

        if ($targetIds->isEmpty()) {
            return [
                'targets_deleted' => 0,
                'runs_deleted' => 0,
                'steps_deleted' => 0,
                'artifacts_deleted' => 0,
                'css_reports_deleted' => 0,
                'css_report_stylesheets_deleted' => 0,
            ];
        }

        $runIds = OptimizationRun::query()
            ->whereIn('optimization_target_id', $targetIds)
            ->pluck('id')
            ->values();
        $stepIds = OptimizationRunStep::query()
            ->whereIn('optimization_run_id', $runIds)
            ->pluck('id')
            ->values();
        $artifactQuery = OptimizationArtifactVersion::query()
            ->whereIn('optimization_target_id', $targetIds);
        $artifactIds = (clone $artifactQuery)->pluck('id')->values();
        $artifactPrefixes = (clone $artifactQuery)
            ->pluck('storage_prefix')
            ->filter()
            ->unique()
            ->values();
        $cssReportIds = OptimizationCssReport::query()
            ->whereIn('optimization_target_id', $targetIds)
            ->pluck('id')
            ->values();
        $cssReportStylesheetIds = OptimizationCssReportStylesheet::query()
            ->whereIn('optimization_css_report_id', $cssReportIds)
            ->pluck('id')
            ->values();

        foreach ($artifactPrefixes as $storagePrefix) {
            Storage::disk('local')->deleteDirectory((string) $storagePrefix);
        }

        DB::transaction(function () use (
            $cssReportStylesheetIds,
            $cssReportIds,
            $artifactIds,
            $stepIds,
            $runIds,
            $targetIds
        ): void {
            if ($cssReportStylesheetIds->isNotEmpty()) {
                OptimizationCssReportStylesheet::query()
                    ->whereIn('id', $cssReportStylesheetIds)
                    ->delete();
            }

            if ($cssReportIds->isNotEmpty()) {
                OptimizationCssReport::query()
                    ->whereIn('id', $cssReportIds)
                    ->delete();
            }

            if ($artifactIds->isNotEmpty()) {
                OptimizationArtifactVersion::query()
                    ->whereIn('id', $artifactIds)
                    ->delete();
            }

            if ($stepIds->isNotEmpty()) {
                OptimizationRunStep::query()
                    ->whereIn('id', $stepIds)
                    ->delete();
            }

            if ($runIds->isNotEmpty()) {
                OptimizationRun::query()
                    ->whereIn('id', $runIds)
                    ->delete();
            }

            OptimizationTarget::query()
                ->whereIn('id', $targetIds)
                ->delete();
        });

        return [
            'targets_deleted' => $targetIds->count(),
            'runs_deleted' => $runIds->count(),
            'steps_deleted' => $stepIds->count(),
            'artifacts_deleted' => $artifactIds->count(),
            'css_reports_deleted' => $cssReportIds->count(),
            'css_report_stylesheets_deleted' => $cssReportStylesheetIds->count(),
        ];
    }

    /**
     * @param  Collection<int, string>  $shopIds
     * @return array<string, int>
     */
    private function deletePageTypeDataForShops(Collection $shopIds): array
    {
        if ($shopIds->isEmpty()) {
            return [
                'page_type_profiles_deleted' => 0,
                'page_type_artifacts_deleted' => 0,
                'url_scan_reports_cleared' => 0,
            ];
        }

        $urlScanRows = PrestashopShopUrl::query()
            ->whereIn('prestashop_shop_id', $shopIds)
            ->where(function ($query): void {
                $query->whereNotNull('scan_report_json')
                    ->orWhereNotNull('mobile_score')
                    ->orWhereNotNull('desktop_score')
                    ->orWhereNotNull('last_scanned_at');
            })
            ->count();

        PrestashopShopUrl::query()
            ->whereIn('prestashop_shop_id', $shopIds)
            ->update([
                'mobile_score' => null,
                'desktop_score' => null,
                'scan_report_json' => null,
                'last_scanned_at' => null,
            ]);

        $profiles = PrestashopShopPageTypeProfile::query()
            ->whereIn('prestashop_shop_id', $shopIds)
            ->get(['id']);

        if ($profiles->isEmpty()) {
            return [
                'page_type_profiles_deleted' => 0,
                'page_type_artifacts_deleted' => 0,
                'url_scan_reports_cleared' => $urlScanRows,
            ];
        }

        $profileIds = $profiles->pluck('id')->values();
        $profileDirectories = $profileIds
            ->map(static fn (string $profileId): string => 'prestaload/page-type-profiles/' . $profileId)
            ->unique()
            ->values();

        foreach ($profileDirectories as $directory) {
            Storage::disk('local')->deleteDirectory((string) $directory);
        }

        foreach ($profileIds as $profileId) {
            File::deleteDirectory(public_path('prestaload-assets/page-type-profiles/' . $profileId));
        }

        $artifactCount = PrestashopShopPageTypeCssArtifact::query()
            ->whereIn('profile_id', $profileIds)
            ->count();

        PrestashopShopPageTypeProfile::query()
            ->whereIn('id', $profileIds)
            ->delete();

        return [
            'page_type_profiles_deleted' => $profileIds->count(),
            'page_type_artifacts_deleted' => $artifactCount,
            'url_scan_reports_cleared' => $urlScanRows,
        ];
    }
}
