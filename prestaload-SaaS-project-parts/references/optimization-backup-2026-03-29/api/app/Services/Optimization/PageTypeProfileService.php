<?php

namespace App\Services\Optimization;

use App\Models\OptimizationArtifactVersion;
use App\Models\OptimizationCssReport;
use App\Models\PrestashopShop;
use App\Models\PrestashopShopPageTypeAssetRule;
use App\Models\PrestashopShopPageTypeCssArtifact;
use App\Models\PrestashopShopPageTypeCssReport;
use App\Models\PrestashopShopPageTypeCssReportStylesheet;
use App\Models\PrestashopShopPageTypeProfile;
use App\Models\PrestashopShopPageTypeProfileUrl;
use App\Models\PrestashopShopUrl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageTypeProfileService
{
    public function __construct(
        private readonly PageTypeAssetRuleService $pageTypeAssetRuleService,
        private readonly PageTypeReducedCssAssetService $pageTypeReducedCssAssetService
    ) {
    }

    public function ensureProfileForUrl(PrestashopShop $shop, PrestashopShopUrl $shopUrl): ?PrestashopShopPageTypeProfile
    {
        if (empty($shopUrl->page_type_id)) {
            return null;
        }

        return DB::transaction(function () use ($shop, $shopUrl): PrestashopShopPageTypeProfile {
            $profile = PrestashopShopPageTypeProfile::query()->firstOrCreate(
                [
                    'prestashop_shop_id' => $shop->id,
                    'page_type_id' => $shopUrl->page_type_id,
                ],
                [
                    'id' => (string) Str::uuid(),
                    'status' => 'draft',
                ]
            );

            PrestashopShopPageTypeProfileUrl::query()->firstOrCreate(
                [
                    'profile_id' => $profile->id,
                    'prestashop_shop_url_id' => $shopUrl->id,
                ],
                [
                    'id' => (string) Str::uuid(),
                    'sample_weight' => 1,
                    'last_analyzed_at' => now(),
                ]
            );

            return $profile;
        });
    }

    public function findProfileForUrl(PrestashopShopUrl $shopUrl): ?PrestashopShopPageTypeProfile
    {
        return $this->findStoreScopedProfileForUrl($shopUrl);
    }

    public function findExactProfileForUrl(PrestashopShopUrl $shopUrl): ?PrestashopShopPageTypeProfile
    {
        if (empty($shopUrl->page_type_id) || empty($shopUrl->prestashop_shop_id)) {
            return null;
        }

        return PrestashopShopPageTypeProfile::query()
            ->with(['pageType', 'scanSourceUrl'])
            ->where('prestashop_shop_id', $shopUrl->prestashop_shop_id)
            ->where('page_type_id', $shopUrl->page_type_id)
            ->first();
    }

    public function findStoreScopedProfileForUrl(PrestashopShopUrl $shopUrl): ?PrestashopShopPageTypeProfile
    {
        if (empty($shopUrl->page_type_id) || empty($shopUrl->prestashop_shop_id)) {
            return null;
        }

        $storeId = $this->resolveStoreIdForUrl($shopUrl);
        if ($storeId === null || $storeId === '') {
            return $this->findExactProfileForUrl($shopUrl);
        }

        $profiles = PrestashopShopPageTypeProfile::query()
            ->with(['pageType', 'scanSourceUrl', 'prestashopShop'])
            ->where('page_type_id', $shopUrl->page_type_id)
            ->whereHas('prestashopShop', static function ($query) use ($storeId): void {
                $query->where('prestashop_store_id', $storeId);
            })
            ->get();

        if ($profiles->isEmpty()) {
            return null;
        }

        return $profiles
            ->sortBy(fn (PrestashopShopPageTypeProfile $profile): int => $this->profileReuseRank($profile, $shopUrl))
            ->first();
    }

    public function isPreparedForUrl(PrestashopShopUrl $shopUrl): bool
    {
        $profile = $this->findProfileForUrl($shopUrl);

        return $profile instanceof PrestashopShopPageTypeProfile
            && $this->isPreparedProfile($profile);
    }

    public function isPreparedProfile(PrestashopShopPageTypeProfile $profile): bool
    {
        return $this->hasCollectedCssData($profile)
            && $this->hasUsableScanReport($profile);
    }

    public function markProfileQueued(
        PrestashopShopPageTypeProfile $profile,
        ?string $sourceShopUrlId = null
    ): PrestashopShopPageTypeProfile {
        $profile->status = 'queued';
        if ($sourceShopUrlId !== null && $sourceShopUrlId !== '') {
            $profile->scan_source_prestashop_shop_url_id = $sourceShopUrlId;
        }
        $profile->save();

        return $profile;
    }

    public function markProfilePreparing(
        PrestashopShopPageTypeProfile $profile,
        ?string $sourceShopUrlId = null
    ): PrestashopShopPageTypeProfile {
        if ($this->isPreparedProfile($profile)) {
            $profile->status = 'ready';
        } else {
            $profile->status = 'preparing';
        }

        if ($sourceShopUrlId !== null && $sourceShopUrlId !== '') {
            $profile->scan_source_prestashop_shop_url_id = $sourceShopUrlId;
        }

        $profile->save();

        return $profile;
    }

    public function markProfileFailedForUrl(PrestashopShopUrl $shopUrl): ?PrestashopShopPageTypeProfile
    {
        $profile = $this->findExactProfileForUrl($shopUrl);
        if (! $profile instanceof PrestashopShopPageTypeProfile) {
            return null;
        }

        if (! $this->isPreparedProfile($profile)) {
            $profile->status = 'failed';
            $profile->save();
        }

        return $profile;
    }

    public function syncLatestOptimization(
        PrestashopShopUrl $shopUrl,
        OptimizationCssReport $optimizationCssReport,
        ?OptimizationArtifactVersion $artifact = null,
        array $reducedCssAssets = [],
        array $minifiedCssAssets = []
    ): ?PrestashopShopPageTypeProfile {
        $shop = $shopUrl->prestashopShop;
        if (! $shop instanceof PrestashopShop) {
            $shop = $shopUrl->prestashopShop()->first();
        }

        if (! $shop instanceof PrestashopShop) {
            return null;
        }

        $profile = $this->ensureProfileForUrl($shop, $shopUrl);
        if (! $profile instanceof PrestashopShopPageTypeProfile) {
            return null;
        }

        DB::transaction(function () use ($profile, $optimizationCssReport, $artifact, $reducedCssAssets, $minifiedCssAssets): void {
            $profile->last_aggregated_at = now();
            $profile->save();

            $profileUrl = PrestashopShopPageTypeProfileUrl::query()
                ->where('profile_id', $profile->id)
                ->where('prestashop_shop_url_id', $optimizationCssReport->optimizationTarget?->prestashop_shop_url_id)
                ->first();

            if ($profileUrl instanceof PrestashopShopPageTypeProfileUrl) {
                $profileUrl->last_analyzed_at = now();
                $profileUrl->save();
            }

            $pageTypeReport = PrestashopShopPageTypeCssReport::query()->updateOrCreate(
                [
                    'profile_id' => $profile->id,
                    'device_class' => $optimizationCssReport->device_class,
                ],
                [
                    'id' => PrestashopShopPageTypeCssReport::query()
                        ->where('profile_id', $profile->id)
                        ->where('device_class', $optimizationCssReport->device_class)
                        ->value('id') ?: (string) Str::uuid(),
                    'source_optimization_css_report_id' => $optimizationCssReport->id,
                    'sample_count' => 1,
                    'stylesheet_count' => $optimizationCssReport->stylesheet_count,
                    'total_css_bytes' => $optimizationCssReport->total_css_bytes,
                    'total_used_css_bytes' => $optimizationCssReport->total_used_css_bytes,
                    'used_ratio' => $optimizationCssReport->used_ratio,
                    'unused_ratio' => $optimizationCssReport->unused_ratio,
                    'coverage_json' => [
                        'final_url' => $optimizationCssReport->final_url,
                        'status_code' => $optimizationCssReport->status_code,
                        'scroll_height' => $optimizationCssReport->scroll_height,
                        'viewport_height' => $optimizationCssReport->viewport_height,
                        'console_message_count' => $optimizationCssReport->console_message_count,
                        'duration_ms' => $optimizationCssReport->duration_ms,
                    ],
                    'last_compiled_at' => now(),
                ]
            );

            PrestashopShopPageTypeCssReportStylesheet::query()
                ->where('shop_page_type_css_report_id', $pageTypeReport->id)
                ->delete();

            foreach ($optimizationCssReport->stylesheets as $stylesheet) {
                PrestashopShopPageTypeCssReportStylesheet::query()->create([
                    'id' => (string) Str::uuid(),
                    'shop_page_type_css_report_id' => $pageTypeReport->id,
                    'position' => $stylesheet->position,
                    'style_sheet_key' => $stylesheet->style_sheet_key,
                    'source_url' => $stylesheet->source_url,
                    'origin' => $stylesheet->origin,
                    'is_inline' => $stylesheet->is_inline,
                    'is_disabled' => $stylesheet->is_disabled,
                    'bytes' => $stylesheet->bytes,
                    'used_bytes' => $stylesheet->used_bytes,
                    'used_ratio' => $stylesheet->used_ratio,
                    'rule_count' => $stylesheet->rule_count,
                    'minified_bytes' => $stylesheet->minified_bytes,
                ]);
            }

            if (! $artifact instanceof OptimizationArtifactVersion) {
                return;
            }

            $usedCssMeta = is_array($artifact->meta_json['used_css'] ?? null) ? $artifact->meta_json['used_css'] : [];
            $usedCssStoragePath = $this->storeProfileCssCopy(
                $profile,
                (string) $optimizationCssReport->device_class,
                'used_css',
                (string) ($artifact->used_css_path ?? '')
            );

            if ($usedCssStoragePath !== null) {
                PrestashopShopPageTypeCssArtifact::query()->updateOrCreate(
                    [
                        'profile_id' => $profile->id,
                        'device_class' => (string) $optimizationCssReport->device_class,
                        'css_type' => 'used_css',
                    ],
                    [
                        'id' => PrestashopShopPageTypeCssArtifact::query()
                            ->where('profile_id', $profile->id)
                            ->where('device_class', (string) $optimizationCssReport->device_class)
                            ->where('css_type', 'used_css')
                            ->value('id') ?: (string) Str::uuid(),
                        'status' => $artifact->status,
                        'storage_path' => $usedCssStoragePath,
                        'bytes' => (int) ($artifact->used_css_bytes ?? 0),
                        'sha256' => $artifact->used_css_sha256,
                        'meta_json' => [
                            'source_artifact_version_id' => $artifact->id,
                            'source_optimization_run_id' => $artifact->optimization_run_id,
                            'mode' => $usedCssMeta['mode'] ?? null,
                            'url' => $usedCssMeta['url'] ?? null,
                        ],
                        'published_at' => $artifact->status === 'published' ? now() : null,
                    ]
                );
            }

            $criticalCssMeta = is_array($artifact->meta_json['critical_css'] ?? null) ? $artifact->meta_json['critical_css'] : [];
            $criticalCssStoragePath = $this->storeProfileCssCopy(
                $profile,
                (string) $optimizationCssReport->device_class,
                'critical_css',
                (string) ($artifact->critical_css_path ?? '')
            );
            if ($criticalCssStoragePath !== null) {
                PrestashopShopPageTypeCssArtifact::query()->updateOrCreate(
                    [
                        'profile_id' => $profile->id,
                        'device_class' => (string) $optimizationCssReport->device_class,
                        'css_type' => 'critical_css',
                    ],
                    [
                        'id' => PrestashopShopPageTypeCssArtifact::query()
                            ->where('profile_id', $profile->id)
                            ->where('device_class', (string) $optimizationCssReport->device_class)
                            ->where('css_type', 'critical_css')
                            ->value('id') ?: (string) Str::uuid(),
                        'status' => $artifact->status,
                        'storage_path' => $criticalCssStoragePath,
                        'bytes' => (int) ($artifact->critical_css_bytes ?? 0),
                        'sha256' => $artifact->critical_css_sha256,
                        'meta_json' => [
                            'source_artifact_version_id' => $artifact->id,
                            'source_optimization_run_id' => $artifact->optimization_run_id,
                            'mode' => $criticalCssMeta['mode'] ?? null,
                            'capped' => $criticalCssMeta['capped'] ?? null,
                            'max_bytes' => $criticalCssMeta['max_bytes'] ?? null,
                            'original_bytes' => $criticalCssMeta['original_bytes'] ?? null,
                            'simplified_bytes' => $criticalCssMeta['simplified_bytes'] ?? null,
                        ],
                        'published_at' => $artifact->status === 'published' ? now() : null,
                    ]
                );
            }

            $this->pageTypeAssetRuleService->syncCssRulesForProfile($profile, $pageTypeReport->fresh('stylesheets'));
            $this->pageTypeReducedCssAssetService->storeMinifiedCssAssets(
                $profile,
                (string) $optimizationCssReport->device_class,
                array_values(array_filter($minifiedCssAssets, static fn ($asset): bool => is_array($asset)))
            );
            $this->pageTypeReducedCssAssetService->storeReducedCssAssets(
                $profile,
                (string) $optimizationCssReport->device_class,
                array_values(array_filter($reducedCssAssets, static fn ($asset): bool => is_array($asset)))
            );
            $this->pageTypeReducedCssAssetService->storeActionBundles(
                $profile,
                (string) $optimizationCssReport->device_class
            );

            $this->refreshProfileStatus($profile);
        });

        return $profile->fresh(['urls', 'cssReports', 'cssArtifacts']);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getReusableCssState(PrestashopShopUrl $shopUrl, string $deviceClass, bool $requireCriticalCss = false): ?array
    {
        if (empty($shopUrl->page_type_id)) {
            return null;
        }

        $profile = $this->findStoreScopedProfileWithCss($shopUrl, $deviceClass);

        if (! $profile instanceof PrestashopShopPageTypeProfile) {
            return null;
        }

        $report = $profile->cssReports->first();
        if (! $report instanceof PrestashopShopPageTypeCssReport) {
            return null;
        }

        /** @var PrestashopShopPageTypeCssArtifact|null $usedCssArtifact */
        $usedCssArtifact = $profile->cssArtifacts->firstWhere('css_type', 'used_css');
        if (! $usedCssArtifact instanceof PrestashopShopPageTypeCssArtifact || empty($usedCssArtifact->storage_path)) {
            return null;
        }

        $usedCssPath = (string) $usedCssArtifact->storage_path;
        if (! Storage::disk('local')->exists($usedCssPath)) {
            return null;
        }

        $usedCssContent = (string) Storage::disk('local')->get($usedCssPath);
        if (trim($usedCssContent) === '') {
            return null;
        }

        /** @var PrestashopShopPageTypeCssArtifact|null $criticalCssArtifact */
        $criticalCssArtifact = $profile->cssArtifacts->firstWhere('css_type', 'critical_css');
        $criticalCssContent = null;
        if ($criticalCssArtifact instanceof PrestashopShopPageTypeCssArtifact && ! empty($criticalCssArtifact->storage_path)) {
            $criticalPath = (string) $criticalCssArtifact->storage_path;
            if (Storage::disk('local')->exists($criticalPath)) {
                $content = (string) Storage::disk('local')->get($criticalPath);
                if (trim($content) !== '') {
                    $criticalCssContent = $content;
                }
            }
        }

        if ($requireCriticalCss && $criticalCssContent === null) {
            return null;
        }

        return [
            'profile' => $profile,
            'report' => $report,
            'stylesheets' => $report->stylesheets->map(static fn (PrestashopShopPageTypeCssReportStylesheet $stylesheet): array => [
                'position' => $stylesheet->position,
                'source_url' => $stylesheet->source_url,
                'origin' => $stylesheet->origin,
                'is_inline' => $stylesheet->is_inline,
                'is_disabled' => $stylesheet->is_disabled,
                'bytes' => $stylesheet->bytes,
                'used_bytes' => $stylesheet->used_bytes,
                'used_ratio' => (float) $stylesheet->used_ratio,
                'rule_count' => $stylesheet->rule_count,
                'minified_bytes' => $stylesheet->minified_bytes,
            ])->all(),
            'critical_css' => [
                'content' => $criticalCssContent,
                'bytes' => (int) ($criticalCssArtifact?->bytes ?? 0),
                'sha256' => $criticalCssArtifact?->sha256,
                'meta' => is_array($criticalCssArtifact?->meta_json ?? null) ? $criticalCssArtifact->meta_json : [],
            ],
            'used_css' => [
                'content' => $usedCssContent,
                'bytes' => (int) ($usedCssArtifact->bytes ?? strlen($usedCssContent)),
                'sha256' => $usedCssArtifact->sha256,
                'meta' => is_array($usedCssArtifact->meta_json ?? null) ? $usedCssArtifact->meta_json : [],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $report
     */
    public function syncPerformanceReport(PrestashopShopUrl $shopUrl, array $report): ?PrestashopShopPageTypeProfile
    {
        $shop = $shopUrl->prestashopShop;
        if (! $shop instanceof PrestashopShop) {
            $shop = $shopUrl->prestashopShop()->first();
        }

        if (! $shop instanceof PrestashopShop) {
            return null;
        }

        $profile = $this->ensureProfileForUrl($shop, $shopUrl);
        if (! $profile instanceof PrestashopShopPageTypeProfile) {
            return null;
        }

        $profile->scan_provider = isset($report['provider']) ? (string) $report['provider'] : null;
        $profile->scan_source_prestashop_shop_url_id = $shopUrl->id;
        $profile->mobile_score = isset($report['mobile']['score']) && is_numeric($report['mobile']['score'])
            ? (int) $report['mobile']['score']
            : null;
        $profile->desktop_score = isset($report['desktop']['score']) && is_numeric($report['desktop']['score'])
            ? (int) $report['desktop']['score']
            : null;
        $profile->last_scanned_at = now();
        $profile->scan_report_json = $report;
        $profile->save();

        $profile->loadMissing(['cssReports.stylesheets']);
        foreach ($profile->cssReports as $cssReport) {
            $this->pageTypeAssetRuleService->syncCssRulesForProfile($profile, $cssReport);
        }
        foreach (['desktop', 'mobile'] as $deviceClass) {
            $this->pageTypeAssetRuleService->syncJsRulesForProfile(
                $profile,
                $deviceClass,
                array_merge(
                    \App\Models\PrestashopStoreOptimizationSetting::defaults(),
                    $shop->prestashopStore?->optimizationSetting?->only([
                        'delay_ads_analytics_scripts',
                        'skip_lazy_load_js_patterns',
                    ]) ?? []
                )
            );
        }

        $this->refreshProfileStatus($profile);

        return $profile->fresh(['pageType', 'scanSourceUrl']);
    }

    /**
     * @param  array<string, mixed>  $report
     */
    public function syncFontUsageReport(PrestashopShopUrl $shopUrl, array $report): ?PrestashopShopPageTypeProfile
    {
        $shop = $shopUrl->prestashopShop;
        if (! $shop instanceof PrestashopShop) {
            $shop = $shopUrl->prestashopShop()->first();
        }

        if (! $shop instanceof PrestashopShop) {
            return null;
        }

        $profile = $this->ensureProfileForUrl($shop, $shopUrl);
        if (! $profile instanceof PrestashopShopPageTypeProfile) {
            return null;
        }

        $profile->scan_source_prestashop_shop_url_id = $shopUrl->id;
        $profile->last_font_scanned_at = now();
        $profile->font_usage_json = [
            'provider' => $report['provider'] ?? 'local-scanner',
            'url' => $report['url'] ?? $shopUrl->url,
            'scanned_url' => $report['scanned_url'] ?? $shopUrl->url,
            'scanned_at' => $report['scanned_at'] ?? now()->toIso8601String(),
            'mobile' => $this->normalizeFontUsageVariant($report['mobile'] ?? []),
            'desktop' => $this->normalizeFontUsageVariant($report['desktop'] ?? []),
        ];
        $profile->save();

        foreach (['desktop', 'mobile'] as $deviceClass) {
            $this->pageTypeAssetRuleService->syncFontRulesForProfile($profile, $deviceClass);
        }

        return $profile->fresh(['pageType', 'scanSourceUrl']);
    }

    public function needsPerformanceReport(PrestashopShopUrl $shopUrl): bool
    {
        if (empty($shopUrl->page_type_id) || empty($shopUrl->prestashop_shop_id)) {
            return false;
        }

        $profile = $this->findStoreScopedProfileForUrl($shopUrl);

        if (! $profile instanceof PrestashopShopPageTypeProfile) {
            return true;
        }

        return ! is_array($profile->scan_report_json)
            || $profile->scan_report_json === []
            || $profile->last_scanned_at === null;
    }

    public function needsFontUsageReport(PrestashopShopUrl $shopUrl): bool
    {
        if (empty($shopUrl->page_type_id) || empty($shopUrl->prestashop_shop_id)) {
            return false;
        }

        $profile = $this->findStoreScopedProfileForUrl($shopUrl);

        if (! $profile instanceof PrestashopShopPageTypeProfile) {
            return true;
        }

        return ! is_array($profile->font_usage_json)
            || $profile->font_usage_json === []
            || $profile->last_font_scanned_at === null;
    }

    public function refreshProfileStatus(PrestashopShopPageTypeProfile $profile): PrestashopShopPageTypeProfile
    {
        $profile->refresh();

        if ($this->isPreparedProfile($profile)) {
            $profile->status = 'ready';
        } elseif ($this->hasCollectedCssData($profile) || $this->hasUsableScanReport($profile)) {
            $profile->status = 'preparing';
        } elseif (! in_array((string) $profile->status, ['queued', 'failed'], true)) {
            $profile->status = 'draft';
        }

        $profile->save();

        return $profile;
    }

    private function findStoreScopedProfileWithCss(PrestashopShopUrl $shopUrl, string $deviceClass): ?PrestashopShopPageTypeProfile
    {
        $storeId = $this->resolveStoreIdForUrl($shopUrl);
        if ($storeId === null || $storeId === '' || empty($shopUrl->page_type_id)) {
            return null;
        }

        $profiles = PrestashopShopPageTypeProfile::query()
            ->with([
                'cssReports' => fn ($query) => $query->where('device_class', $deviceClass)->with('stylesheets'),
                'cssArtifacts' => fn ($query) => $query->where('device_class', $deviceClass),
                'prestashopShop',
            ])
            ->where('page_type_id', $shopUrl->page_type_id)
            ->whereHas('prestashopShop', static function ($query) use ($storeId): void {
                $query->where('prestashop_store_id', $storeId);
            })
            ->get();

        if ($profiles->isEmpty()) {
            return null;
        }

        return $profiles
            ->sortBy(fn (PrestashopShopPageTypeProfile $profile): int => $this->profileReuseRank($profile, $shopUrl))
            ->first();
    }

    private function profileReuseRank(PrestashopShopPageTypeProfile $profile, PrestashopShopUrl $shopUrl): int
    {
        $exactShop = (string) $profile->prestashop_shop_id === (string) $shopUrl->prestashop_shop_id;
        $prepared = $this->isPreparedProfile($profile);
        $active = in_array((string) $profile->status, ['queued', 'preparing'], true);

        return match (true) {
            $prepared && $exactShop => 0,
            $prepared => 1,
            $active && $exactShop => 2,
            $active => 3,
            $exactShop => 4,
            default => 5,
        };
    }

    private function resolveStoreIdForUrl(PrestashopShopUrl $shopUrl): ?string
    {
        $storeId = $shopUrl->prestashopShop?->prestashop_store_id;
        if (is_string($storeId) && $storeId !== '') {
            return $storeId;
        }

        $resolvedStoreId = PrestashopShop::query()
            ->whereKey($shopUrl->prestashop_shop_id)
            ->value('prestashop_store_id');

        return is_string($resolvedStoreId) && $resolvedStoreId !== ''
            ? $resolvedStoreId
            : null;
    }

    private function hasCollectedCssData(PrestashopShopPageTypeProfile $profile): bool
    {
        if ($profile->last_aggregated_at === null) {
            return false;
        }

        $hasReport = PrestashopShopPageTypeCssReport::query()
            ->where('profile_id', $profile->id)
            ->exists();
        $hasUsedCss = PrestashopShopPageTypeCssArtifact::query()
            ->where('profile_id', $profile->id)
            ->where('css_type', 'used_css')
            ->where('bytes', '>', 0)
            ->exists();
        $hasRules = PrestashopShopPageTypeAssetRule::query()
            ->where('profile_id', $profile->id)
            ->where('asset_type', 'css')
            ->exists();

        return $hasReport && $hasUsedCss && $hasRules;
    }

    private function hasUsableScanReport(PrestashopShopPageTypeProfile $profile): bool
    {
        return is_array($profile->scan_report_json)
            && $profile->scan_report_json !== []
            && $profile->last_scanned_at !== null;
    }

    /**
     * @param  mixed  $variant
     * @return array<string, mixed>
     */
    private function normalizeFontUsageVariant(mixed $variant): array
    {
        if (! is_array($variant)) {
            return [
                'declared_font_families' => [],
                'used_font_families' => [],
                'used_above_the_fold' => [],
                'used_weights' => [],
                'used_styles' => [],
                'unused_declared_families' => [],
                'google_fonts_stylesheets' => [],
                'duplicate_icon_font_stylesheets' => [],
                'font_face_rule_count' => 0,
            ];
        }

        return [
            'device' => isset($variant['device']) ? (string) $variant['device'] : null,
            'scanned_at' => isset($variant['scanned_at']) ? (string) $variant['scanned_at'] : null,
            'declared_font_families' => $this->normalizeStringList($variant['declared_font_families'] ?? []),
            'used_font_families' => $this->normalizeStringList($variant['used_font_families'] ?? []),
            'used_above_the_fold' => $this->normalizeStringList($variant['used_above_the_fold'] ?? []),
            'used_weights' => $this->normalizeStringList($variant['used_weights'] ?? []),
            'used_styles' => $this->normalizeStringList($variant['used_styles'] ?? []),
            'unused_declared_families' => $this->normalizeStringList($variant['unused_declared_families'] ?? []),
            'google_fonts_stylesheets' => $this->normalizeStringList($variant['google_fonts_stylesheets'] ?? []),
            'duplicate_icon_font_stylesheets' => $this->normalizeDuplicateIconFontStylesheets($variant['duplicate_icon_font_stylesheets'] ?? []),
            'font_face_rule_count' => (int) ($variant['font_face_rule_count'] ?? 0),
            'viewport' => is_array($variant['viewport'] ?? null) ? $variant['viewport'] : [],
            'element_count' => isset($variant['element_count']) ? (int) $variant['element_count'] : null,
            'viewport_element_count' => isset($variant['viewport_element_count']) ? (int) $variant['viewport_element_count'] : null,
            'scanned_element_count' => isset($variant['scanned_element_count']) ? (int) $variant['scanned_element_count'] : null,
        ];
    }

    /**
     * @param  mixed  $value
     * @return list<string>
     */
    private function normalizeStringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $item) {
            if (! is_scalar($item)) {
                continue;
            }

            $text = trim((string) $item);
            if ($text !== '') {
                $normalized[] = $text;
            }
        }

        return array_values(array_unique($normalized));
    }

    /**
     * @param  mixed  $value
     * @return list<array{family:string,href:string,count:int}>
     */
    private function normalizeDuplicateIconFontStylesheets(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $item) {
            if (! is_array($item)) {
                continue;
            }

            $href = trim((string) ($item['href'] ?? ''));
            if ($href === '') {
                continue;
            }

            $normalized[] = [
                'family' => trim((string) ($item['family'] ?? '')),
                'href' => $href,
                'count' => (int) ($item['count'] ?? 0),
            ];
        }

        return $normalized;
    }

    private function storeProfileCssCopy(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass,
        string $cssType,
        string $sourcePath
    ): ?string {
        if ($sourcePath === '' || ! Storage::disk('local')->exists($sourcePath)) {
            return null;
        }

        $content = (string) Storage::disk('local')->get($sourcePath);
        if (trim($content) === '') {
            return null;
        }

        $storagePath = sprintf(
            'prestaload/page-type-profiles/%s/%s/%s.css',
            $profile->id,
            strtolower(trim($deviceClass)),
            $cssType
        );

        Storage::disk('local')->put($storagePath, $content);

        return $storagePath;
    }
}
