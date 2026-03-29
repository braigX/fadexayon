<?php

namespace App\Services\Optimization;

use App\Models\PrestashopShop;
use App\Models\PrestashopShopPageTypeAssetRule;
use App\Models\PrestashopShopPageTypeCssArtifact;
use App\Models\PrestashopShopPageTypeCssReport;
use App\Models\PrestashopShopPageTypeProfile;
use App\Models\PrestashopShopUrl;
use App\Services\Performance\FontUsageDecisionService;
use App\Services\Performance\JsScanReportService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PageTypeAssetRuleService
{
    public function __construct(
        private readonly CssDeliveryStrategyService $cssDeliveryStrategyService,
        private readonly PageTypeReducedCssAssetService $pageTypeReducedCssAssetService,
        private readonly JsScanReportService $jsScanReportService,
        private readonly FontUsageDecisionService $fontUsageDecisionService,
        private readonly PageTypeFontAssetService $pageTypeFontAssetService
    ) {
    }

    /**
     * @return array{
     *   summary: array<string, int>,
     *   bundles: array<string, mixed>,
     *   stylesheets: list<array<string, mixed>>
     * }
     */
    public function resolveStylesheetActions(
        PrestashopShopUrl $shopUrl,
        string $deviceClass,
        array $stylesheets,
        bool $usedCssAvailable = false
    ): array {
        if ($stylesheets === []) {
            return [
                'summary' => [
                    'keep' => 0,
                    'preload' => 0,
                    'minify' => 0,
                    'reduce' => 0,
                    'reduce_minify' => 0,
                    'remove' => 0,
                ],
                'bundles' => [],
                'stylesheets' => [],
            ];
        }

        $profile = $this->findProfileForUrl($shopUrl);
        $pageTypeCode = $profile?->pageType?->code ?? $shopUrl->page_type;
        $scanDeviceReport = $this->extractDeviceScanReport($profile?->scan_report_json, $deviceClass);

        $classified = $this->cssDeliveryStrategyService->verifyStylesheets(
            $shopUrl->url,
            $pageTypeCode,
            $stylesheets,
            $scanDeviceReport,
            $usedCssAvailable
        );

        if (! $profile instanceof PrestashopShopPageTypeProfile) {
            return $classified;
        }

        $this->pageTypeReducedCssAssetService->storeActionBundles($profile, $deviceClass);
        $bundles = $this->pageTypeReducedCssAssetService->getActionBundles($profile, $deviceClass);

        $rules = PrestashopShopPageTypeAssetRule::query()
            ->where('profile_id', $profile->id)
            ->where('device_class', $deviceClass)
            ->where('asset_type', 'css')
            ->get()
            ->keyBy(fn (PrestashopShopPageTypeAssetRule $rule): string => $this->normalizeAssetReference((string) ($rule->asset_url ?? '')));

        if ($rules->isEmpty()) {
            return [
                'summary' => $classified['summary'],
                'bundles' => $bundles,
                'stylesheets' => $classified['stylesheets'],
            ];
        }

        $summary = [
            'keep' => 0,
            'preload' => 0,
            'minify' => 0,
            'reduce' => 0,
            'reduce_minify' => 0,
            'remove' => 0,
        ];
        $resolved = [];

        foreach ($classified['stylesheets'] as $stylesheet) {
            $assetReference = $this->buildCssAssetReference($stylesheet);
            $normalizedUrl = $this->normalizeAssetReference($assetReference);
            $rule = $normalizedUrl !== '' ? $rules->get($normalizedUrl) : null;

            if ($rule instanceof PrestashopShopPageTypeAssetRule) {
                $strategy = (string) $rule->effective_action;
                $reducedAssetUrl = trim((string) ($rule->reduced_css_public_url ?? ''));
                $minifiedAssetUrl = trim((string) ($rule->minified_css_public_url ?? ''));
                if (! $usedCssAvailable && $strategy === 'remove') {
                    $strategy = 'keep';
                } elseif (! $usedCssAvailable && $strategy === 'reduce') {
                    $strategy = 'preload';
                } elseif (! $usedCssAvailable && $strategy === 'reduce_minify') {
                    $strategy = 'minify';
                } elseif ($strategy === 'reduce' && $reducedAssetUrl === '') {
                    $strategy = 'preload';
                } elseif ($strategy === 'reduce_minify' && $reducedAssetUrl === '') {
                    $strategy = 'minify';
                } elseif ($strategy === 'minify' && $minifiedAssetUrl === '') {
                    $strategy = 'keep';
                }

                $reasons = array_values(array_filter(
                    is_array($rule->reasons_json) ? $rule->reasons_json : [],
                    static fn ($reason): bool => is_string($reason) && $reason !== ''
                ));
                if (! $usedCssAvailable && in_array((string) $rule->effective_action, ['reduce', 'reduce_minify', 'remove'], true)) {
                    $reasons[] = 'used_css_unavailable_downgraded';
                } elseif (in_array((string) $rule->effective_action, ['reduce', 'reduce_minify'], true) && $reducedAssetUrl === '') {
                    $reasons[] = 'reduced_asset_unavailable_downgraded';
                } elseif ((string) $rule->effective_action === 'minify' && $minifiedAssetUrl === '') {
                    $reasons[] = 'minified_asset_unavailable_downgraded';
                }

                $stylesheet['delivery_strategy'] = [
                    'strategy' => $strategy,
                    'recommended_action' => (string) $rule->recommended_action,
                    'confidence' => (float) $rule->confidence,
                    'reasons' => array_values(array_unique($reasons)),
                    'evidence' => is_array($rule->evidence_json) ? $rule->evidence_json : [],
                    'action_source' => (string) $rule->action_source,
                    'reduced_asset_url' => $reducedAssetUrl !== '' ? $reducedAssetUrl : null,
                    'minified_asset_url' => $minifiedAssetUrl !== '' ? $minifiedAssetUrl : null,
                    'reduced_asset_sha256' => $rule->reduced_css_sha256,
                    'minified_asset_sha256' => $rule->minified_css_sha256,
                ];
            }

            $strategy = (string) ($stylesheet['delivery_strategy']['strategy'] ?? 'keep');
            if (! array_key_exists($strategy, $summary)) {
                $strategy = 'keep';
            }

            $summary[$strategy]++;
            $resolved[] = $stylesheet;
        }

        return [
            'summary' => $summary,
            'bundles' => $bundles,
            'stylesheets' => $resolved,
        ];
    }

    /**
     * @return array<string, int>
     */
    public function syncCssRulesForProfile(PrestashopShopPageTypeProfile $profile, PrestashopShopPageTypeCssReport $report): array
    {
        $profile->loadMissing(['pageType', 'scanSourceUrl', 'cssArtifacts']);
        $stylesheets = $report->relationLoaded('stylesheets')
            ? $report->stylesheets
            : $report->stylesheets()->get();

        $payload = $stylesheets
            ->map(static fn ($stylesheet): array => [
                'position' => $stylesheet->position,
                'source_url' => $stylesheet->source_url,
                'origin' => $stylesheet->origin,
                'is_inline' => (bool) $stylesheet->is_inline,
                'is_disabled' => (bool) $stylesheet->is_disabled,
                'bytes' => (int) $stylesheet->bytes,
                'used_bytes' => (int) $stylesheet->used_bytes,
                'used_ratio' => (float) $stylesheet->used_ratio,
                'rule_count' => $stylesheet->rule_count !== null ? (int) $stylesheet->rule_count : null,
                'minified_bytes' => $stylesheet->minified_bytes !== null ? (int) $stylesheet->minified_bytes : null,
            ])
            ->all();

        $classified = $this->cssDeliveryStrategyService->verifyStylesheets(
            $profile->scanSourceUrl?->url ?? ($report->coverage_json['final_url'] ?? null),
            $profile->pageType?->code,
            $payload,
            $this->extractDeviceScanReport($profile->scan_report_json, (string) $report->device_class),
            $this->profileHasUsedCssArtifact($profile, (string) $report->device_class)
        );

        $trackedUrls = [];

        foreach ($classified['stylesheets'] as $stylesheet) {
            $assetUrl = $this->buildCssAssetReference($stylesheet);
            if ($assetUrl === '') {
                continue;
            }

            $normalizedAssetUrl = $this->normalizeAssetReference($assetUrl);
            if ($normalizedAssetUrl === '') {
                continue;
            }

            $trackedUrls[] = $assetUrl;
            $strategy = is_array($stylesheet['delivery_strategy'] ?? null) ? $stylesheet['delivery_strategy'] : [];
            $notes = implode(', ', array_values(array_filter(
                is_array($strategy['reasons'] ?? null) ? $strategy['reasons'] : [],
                static fn ($reason): bool => is_string($reason) && $reason !== ''
            )));
            $existingRule = PrestashopShopPageTypeAssetRule::query()
                ->where('profile_id', $profile->id)
                ->where('device_class', (string) $report->device_class)
                ->where('asset_type', 'css')
                ->where('asset_url', $assetUrl)
                ->first();
            $recommendedAction = (string) ($strategy['recommended_action'] ?? $strategy['strategy'] ?? 'keep');
            $effectiveAction = $recommendedAction;
            $actionSource = 'auto';

            if ($existingRule instanceof PrestashopShopPageTypeAssetRule && (string) $existingRule->action_source === 'user') {
                $effectiveAction = trim((string) $existingRule->effective_action) !== ''
                    ? (string) $existingRule->effective_action
                    : $recommendedAction;
                $actionSource = 'user';
            }

            $keepsReducedAsset = in_array($effectiveAction, ['reduce', 'reduce_minify'], true);
            $keepsMinifiedAsset = $effectiveAction === 'minify';

            if ($existingRule instanceof PrestashopShopPageTypeAssetRule && ! $keepsReducedAsset) {
                $publicPath = trim((string) ($existingRule->reduced_css_public_path ?? ''));
                if ($publicPath !== '') {
                    File::delete(public_path($publicPath));
                }
            }

            if ($existingRule instanceof PrestashopShopPageTypeAssetRule && ! $keepsMinifiedAsset) {
                $publicPath = trim((string) ($existingRule->minified_css_public_path ?? ''));
                if ($publicPath !== '') {
                    File::delete(public_path($publicPath));
                }
            }

            PrestashopShopPageTypeAssetRule::query()->updateOrCreate(
                [
                    'profile_id' => $profile->id,
                    'device_class' => (string) $report->device_class,
                    'asset_type' => 'css',
                    'asset_url' => $assetUrl,
                ],
                [
                    'id' => $existingRule?->id ?: (string) Str::uuid(),
                    'asset_pattern' => null,
                    'recommended_action' => $recommendedAction,
                    'effective_action' => $effectiveAction,
                    'action_source' => $actionSource,
                    'confidence' => (float) ($strategy['confidence'] ?? 0),
                    'reasons_json' => array_values(array_filter(
                        is_array($strategy['reasons'] ?? null) ? $strategy['reasons'] : [],
                        static fn ($reason): bool => is_string($reason) && $reason !== ''
                    )),
                    'evidence_json' => is_array($strategy['evidence'] ?? null) ? $strategy['evidence'] : [],
                    'last_verified_at' => now(),
                    'notes' => $notes !== '' ? $notes : null,
                    'reduced_css_status' => $keepsReducedAsset ? $existingRule?->reduced_css_status : null,
                    'reduced_css_public_path' => $keepsReducedAsset ? $existingRule?->reduced_css_public_path : null,
                    'reduced_css_public_url' => $keepsReducedAsset ? $existingRule?->reduced_css_public_url : null,
                    'reduced_css_bytes' => $keepsReducedAsset ? $existingRule?->reduced_css_bytes : null,
                    'reduced_css_sha256' => $keepsReducedAsset ? $existingRule?->reduced_css_sha256 : null,
                    'last_reduced_at' => $keepsReducedAsset ? $existingRule?->last_reduced_at : null,
                    'minified_css_status' => $keepsMinifiedAsset ? $existingRule?->minified_css_status : null,
                    'minified_css_public_path' => $keepsMinifiedAsset ? $existingRule?->minified_css_public_path : null,
                    'minified_css_public_url' => $keepsMinifiedAsset ? $existingRule?->minified_css_public_url : null,
                    'minified_css_asset_bytes' => $keepsMinifiedAsset ? $existingRule?->minified_css_asset_bytes : null,
                    'minified_css_sha256' => $keepsMinifiedAsset ? $existingRule?->minified_css_sha256 : null,
                    'last_minified_at' => $keepsMinifiedAsset ? $existingRule?->last_minified_at : null,
                ]
            );
        }

        $staleRules = PrestashopShopPageTypeAssetRule::query()
            ->where('profile_id', $profile->id)
            ->where('device_class', (string) $report->device_class)
            ->where('asset_type', 'css')
            ->where('action_source', 'auto')
            ->when($trackedUrls !== [], function ($query) use ($trackedUrls): void {
                $query->whereNotIn('asset_url', $trackedUrls);
            })
            ->when($trackedUrls === [], function ($query): void {
                $query->whereNotNull('id');
            })
            ->get();

        foreach ($staleRules as $staleRule) {
            $publicPath = trim((string) ($staleRule->reduced_css_public_path ?? ''));
            if ($publicPath !== '') {
                File::delete(public_path($publicPath));
            }

            $minifiedPath = trim((string) ($staleRule->minified_css_public_path ?? ''));
            if ($minifiedPath !== '') {
                File::delete(public_path($minifiedPath));
            }
        }

        PrestashopShopPageTypeAssetRule::query()
            ->whereIn('id', $staleRules->pluck('id'))
            ->delete();

        return [
            'keep' => (int) ($classified['summary']['keep'] ?? 0),
            'preload' => (int) ($classified['summary']['preload'] ?? 0),
            'minify' => (int) ($classified['summary']['minify'] ?? 0),
            'reduce' => (int) ($classified['summary']['reduce'] ?? 0),
            'reduce_minify' => (int) ($classified['summary']['reduce_minify'] ?? 0),
            'remove' => (int) ($classified['summary']['remove'] ?? 0),
        ];
    }

    public function needsReducedCssBackfill(PrestashopShopUrl $shopUrl, string $deviceClass): bool
    {
        $profile = $this->findProfileForUrl($shopUrl);
        if (! $profile instanceof PrestashopShopPageTypeProfile) {
            return false;
        }

        return PrestashopShopPageTypeAssetRule::query()
            ->where('profile_id', $profile->id)
            ->where('device_class', $deviceClass)
            ->where('asset_type', 'css')
            ->where(function ($query): void {
                $query->whereIn('effective_action', ['reduce', 'reduce_minify'])
                    ->where(function ($assetQuery): void {
                        $assetQuery->whereNull('reduced_css_public_url')
                            ->orWhere('reduced_css_public_url', '')
                            ->orWhereNull('reduced_css_status')
                            ->orWhere('reduced_css_status', '!=', 'ready');
                    });
            })
            ->orWhere(function ($query) use ($profile, $deviceClass): void {
                $query->where('profile_id', $profile->id)
                    ->where('device_class', $deviceClass)
                    ->where('asset_type', 'css')
                    ->where('effective_action', 'minify')
                    ->where(function ($assetQuery): void {
                        $assetQuery->whereNull('minified_css_public_url')
                            ->orWhere('minified_css_public_url', '')
                            ->orWhereNull('minified_css_status')
                            ->orWhere('minified_css_status', '!=', 'ready');
                    });
            })
            ->exists();
    }

    /**
     * @param  array<string, mixed>  $stylesheet
     */
    private function buildCssAssetReference(array $stylesheet): string
    {
        $sourceUrl = trim((string) ($stylesheet['source_url'] ?? ''));
        if ($sourceUrl !== '') {
            return $sourceUrl;
        }

        if ((bool) ($stylesheet['is_inline'] ?? false)) {
            $position = (int) ($stylesheet['position'] ?? 0);
            if ($position > 0) {
                return 'inline://position/' . $position;
            }
        }

        return '';
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>|null
     */
    public function resolveScriptActionsForProfile(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass,
        int $optimizedPageCount = 1,
        array $settings = []
    ): ?array {
        $baseRow = $this->jsScanReportService->buildDeviceRow($profile, $deviceClass, $optimizedPageCount, $settings);
        if (! is_array($baseRow)) {
            return null;
        }

        $rules = PrestashopShopPageTypeAssetRule::query()
            ->where('profile_id', $profile->id)
            ->where('device_class', $deviceClass)
            ->where('asset_type', 'js')
            ->get()
            ->keyBy(fn (PrestashopShopPageTypeAssetRule $rule): string => $this->normalizeAssetReference((string) ($rule->asset_url ?? '')));

        if ($rules->isEmpty()) {
            return $baseRow;
        }

        $summary = [
            'keep' => 0,
            'load_on_interaction' => 0,
            'minify' => 0,
            'reduce' => 0,
            'reduce_minify' => 0,
            'remove' => 0,
        ];
        $scripts = [];

        foreach ((array) ($baseRow['scripts'] ?? []) as $script) {
            if (! is_array($script)) {
                continue;
            }

            $normalizedUrl = $this->normalizeAssetReference((string) ($script['url'] ?? ''));
            $rule = $normalizedUrl !== '' ? $rules->get($normalizedUrl) : null;

            if ($rule instanceof PrestashopShopPageTypeAssetRule) {
                $script['recommended_action'] = (string) $rule->recommended_action;
                $script['action'] = (string) $rule->effective_action;
                $script['action_source'] = (string) $rule->action_source;
                $script['confidence'] = (float) $rule->confidence;
                $script['reasons'] = array_values(array_filter(
                    is_array($rule->reasons_json) ? $rule->reasons_json : [],
                    static fn ($reason): bool => is_string($reason) && $reason !== ''
                ));
                $script['evidence'] = is_array($rule->evidence_json) ? $rule->evidence_json : [];
            }

            $action = (string) ($script['action'] ?? 'keep');
            if (! array_key_exists($action, $summary)) {
                $action = 'keep';
                $script['action'] = 'keep';
            }

            $summary[$action]++;
            $scripts[] = $script;
        }

        $baseRow['action_summary'] = $summary;
        $baseRow['scripts'] = $scripts;

        return $baseRow;
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return list<array<string, mixed>>
     */
    public function resolveScriptActions(
        PrestashopShopUrl $shopUrl,
        string $deviceClass,
        array $settings = []
    ): array {
        $profile = $this->findProfileForUrl($shopUrl);
        if (! $profile instanceof PrestashopShopPageTypeProfile) {
            return [];
        }

        $resolved = $this->resolveScriptActionsForProfile($profile, $deviceClass, 1, $settings);

        return is_array($resolved['scripts'] ?? null) ? $resolved['scripts'] : [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function resolveFontActions(PrestashopShopUrl $shopUrl, string $deviceClass): array
    {
        $profile = $this->findProfileForUrl($shopUrl);
        if (! $profile instanceof PrestashopShopPageTypeProfile) {
            return [];
        }

        return $this->fontUsageDecisionService->buildDeviceAssets($profile, $deviceClass);
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, int>
     */
    public function syncJsRulesForProfile(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass,
        array $settings = []
    ): array {
        $resolved = $this->jsScanReportService->buildDeviceRow($profile, $deviceClass, 1, $settings);
        if (! is_array($resolved)) {
            return [
                'keep' => 0,
                'load_on_interaction' => 0,
                'minify' => 0,
                'reduce' => 0,
                'reduce_minify' => 0,
                'remove' => 0,
            ];
        }

        $trackedUrls = [];

        foreach ((array) ($resolved['scripts'] ?? []) as $script) {
            if (! is_array($script)) {
                continue;
            }

            $assetUrl = trim((string) ($script['url'] ?? ''));
            if ($assetUrl === '') {
                continue;
            }

            $trackedUrls[] = $assetUrl;
            $existingRule = PrestashopShopPageTypeAssetRule::query()
                ->where('profile_id', $profile->id)
                ->where('device_class', $deviceClass)
                ->where('asset_type', 'js')
                ->where('asset_url', $assetUrl)
                ->first();

            $recommendedAction = trim((string) ($script['action'] ?? 'keep'));
            if ($recommendedAction === '') {
                $recommendedAction = 'keep';
            }

            $effectiveAction = $recommendedAction;
            $actionSource = 'auto';

            if ($existingRule instanceof PrestashopShopPageTypeAssetRule && (string) $existingRule->action_source === 'user') {
                $effectiveAction = trim((string) $existingRule->effective_action) !== ''
                    ? (string) $existingRule->effective_action
                    : $recommendedAction;
                $actionSource = 'user';
            }

            $reasons = [];
            if (($script['origin'] ?? null) === 'third-party') {
                $reasons[] = 'third_party_script';
            }
            if (((int) ($script['unused_savings_bytes'] ?? 0)) > 0) {
                $reasons[] = 'unused_javascript_bytes';
            }
            if (((int) ($script['minify_savings_bytes'] ?? 0)) > 0) {
                $reasons[] = 'minify_javascript_bytes';
            }
            if ($recommendedAction === 'load_on_interaction') {
                $reasons[] = 'interaction_delayed';
            }

            PrestashopShopPageTypeAssetRule::query()->updateOrCreate(
                [
                    'profile_id' => $profile->id,
                    'device_class' => $deviceClass,
                    'asset_type' => 'js',
                    'asset_url' => $assetUrl,
                ],
                [
                    'id' => $existingRule?->id ?: (string) Str::uuid(),
                    'asset_pattern' => null,
                    'recommended_action' => $recommendedAction,
                    'effective_action' => $effectiveAction,
                    'action_source' => $actionSource,
                    'confidence' => (float) ($script['savings_ratio'] ?? 0),
                    'reasons_json' => array_values(array_unique($reasons)),
                    'evidence_json' => [
                        'origin' => $script['origin'] ?? null,
                        'host' => $script['host'] ?? null,
                        'total_bytes' => (int) ($script['total_bytes'] ?? 0),
                        'unused_savings_bytes' => (int) ($script['unused_savings_bytes'] ?? 0),
                        'minify_savings_bytes' => (int) ($script['minify_savings_bytes'] ?? 0),
                        'effective_savings_bytes' => (int) ($script['effective_savings_bytes'] ?? 0),
                        'savings_ratio' => (float) ($script['savings_ratio'] ?? 0),
                    ],
                    'last_verified_at' => now(),
                    'notes' => null,
                ]
            );
        }

        PrestashopShopPageTypeAssetRule::query()
            ->where('profile_id', $profile->id)
            ->where('device_class', $deviceClass)
            ->where('asset_type', 'js')
            ->where('action_source', 'auto')
            ->when($trackedUrls !== [], function ($query) use ($trackedUrls): void {
                $query->whereNotIn('asset_url', $trackedUrls);
            })
            ->when($trackedUrls === [], function ($query): void {
                $query->whereNotNull('id');
            })
            ->delete();

        return [
            'keep' => (int) (($resolved['action_summary']['keep'] ?? 0)),
            'load_on_interaction' => (int) (($resolved['action_summary']['load_on_interaction'] ?? 0)),
            'minify' => (int) (($resolved['action_summary']['minify'] ?? 0)),
            'reduce' => (int) (($resolved['action_summary']['reduce'] ?? 0)),
            'reduce_minify' => (int) (($resolved['action_summary']['reduce_minify'] ?? 0)),
            'remove' => (int) (($resolved['action_summary']['remove'] ?? 0)),
        ];
    }

    /**
     * @return array<string, int>
     */
    public function syncFontRulesForProfile(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass
    ): array {
        $resolved = $this->fontUsageDecisionService->buildDeviceAssets($profile, $deviceClass);
        if ($resolved === []) {
            return [
                'keep' => 0,
                'self_host' => 0,
                'self_host_preload' => 0,
                'set_font_display_swap' => 0,
                'remove_unused' => 0,
                'dedupe_icon_font' => 0,
            ];
        }

        $trackedUrls = [];

        foreach ($resolved as $asset) {
            if (! is_array($asset)) {
                continue;
            }

            $assetUrl = trim((string) ($asset['asset_url'] ?? ''));
            if ($assetUrl === '') {
                continue;
            }

            $trackedUrls[] = $assetUrl;
            $existingRule = PrestashopShopPageTypeAssetRule::query()
                ->where('profile_id', $profile->id)
                ->where('device_class', $deviceClass)
                ->where('asset_type', 'font')
                ->where('asset_url', $assetUrl)
                ->first();

            $recommendedAction = trim((string) ($asset['recommended_action'] ?? 'keep'));
            if ($recommendedAction === '') {
                $recommendedAction = 'keep';
            }

            $effectiveAction = $recommendedAction;
            $actionSource = 'auto';

            if ($existingRule instanceof PrestashopShopPageTypeAssetRule && (string) $existingRule->action_source === 'user') {
                $effectiveAction = trim((string) $existingRule->effective_action) !== ''
                    ? (string) $existingRule->effective_action
                    : $recommendedAction;
                $actionSource = 'user';
            }

            $keepsHostedAsset = in_array($effectiveAction, ['self_host', 'self_host_preload', 'set_font_display_swap'], true);

            if ($existingRule instanceof PrestashopShopPageTypeAssetRule && ! $keepsHostedAsset) {
                $this->pageTypeFontAssetService->clearSelfHostedAsset($existingRule);
                $existingRule->refresh();
            }

            PrestashopShopPageTypeAssetRule::query()->updateOrCreate(
                [
                    'profile_id' => $profile->id,
                    'device_class' => $deviceClass,
                    'asset_type' => 'font',
                    'asset_url' => $assetUrl,
                ],
                [
                    'id' => $existingRule?->id ?: (string) Str::uuid(),
                    'asset_pattern' => null,
                    'recommended_action' => $recommendedAction,
                    'effective_action' => $effectiveAction,
                    'action_source' => $actionSource,
                    'confidence' => (float) ($asset['confidence'] ?? 0),
                    'reasons_json' => array_values(array_filter([
                        (string) ($asset['source'] ?? ''),
                        (string) ($recommendedAction === 'remove_unused' ? 'unused_font_asset' : ''),
                        (string) ($recommendedAction === 'dedupe_icon_font' ? 'duplicate_icon_font' : ''),
                        (string) (in_array($recommendedAction, ['self_host', 'self_host_preload'], true) ? 'self_host_candidate' : ''),
                        (string) ($recommendedAction === 'set_font_display_swap' ? 'font_display_swap_candidate' : ''),
                    ], static fn ($reason): bool => $reason !== '')),
                    'evidence_json' => [
                        'type' => $asset['type'] ?? null,
                        'source' => $asset['source'] ?? null,
                        'families' => $asset['families'] ?? [],
                        'used_families' => $asset['used_families'] ?? [],
                        'above_fold_families' => $asset['above_fold_families'] ?? [],
                        'weights' => $asset['weights'] ?? [],
                        'href' => $asset['href'] ?? null,
                        'count' => (int) ($asset['count'] ?? 0),
                        'font_issue_urls' => $asset['font_issue_urls'] ?? [],
                    ],
                    'last_verified_at' => now(),
                    'notes' => null,
                    'font_asset_status' => $keepsHostedAsset ? $existingRule?->font_asset_status : null,
                    'font_css_public_path' => $keepsHostedAsset ? $existingRule?->font_css_public_path : null,
                    'font_css_public_url' => $keepsHostedAsset ? $existingRule?->font_css_public_url : null,
                    'font_css_bytes' => $keepsHostedAsset ? $existingRule?->font_css_bytes : null,
                    'font_css_sha256' => $keepsHostedAsset ? $existingRule?->font_css_sha256 : null,
                    'font_meta_json' => $keepsHostedAsset ? $existingRule?->font_meta_json : null,
                    'last_font_built_at' => $keepsHostedAsset ? $existingRule?->last_font_built_at : null,
                ]
            );
        }

        PrestashopShopPageTypeAssetRule::query()
            ->where('profile_id', $profile->id)
            ->where('device_class', $deviceClass)
            ->where('asset_type', 'font')
            ->where('action_source', 'auto')
            ->when($trackedUrls !== [], function ($query) use ($trackedUrls): void {
                $query->whereNotIn('asset_url', $trackedUrls);
            })
            ->when($trackedUrls === [], function ($query): void {
                $query->whereNotNull('id');
            })
            ->get()
            ->each(function (PrestashopShopPageTypeAssetRule $rule): void {
                $this->pageTypeFontAssetService->clearSelfHostedAsset($rule);
                $rule->delete();
            });

        try {
            $this->pageTypeFontAssetService->syncSelfHostedAssets($profile, $deviceClass);
        } catch (\Throwable $exception) {
            Log::warning('prestaload.font_assets.sync_failed', [
                'profile_id' => $profile->id,
                'device_class' => $deviceClass,
                'message' => $exception->getMessage(),
            ]);
        }

        $freshAssets = $this->fontUsageDecisionService->buildDeviceAssets($profile->fresh('assetRules'), $deviceClass);
        $summary = [
            'keep' => 0,
            'self_host' => 0,
            'self_host_preload' => 0,
            'set_font_display_swap' => 0,
            'remove_unused' => 0,
            'dedupe_icon_font' => 0,
        ];

        foreach ($freshAssets as $asset) {
            $action = (string) ($asset['action'] ?? 'keep');
            if (! array_key_exists($action, $summary)) {
                $action = 'keep';
            }

            $summary[$action]++;
        }

        return $summary;
    }

    private function findProfileForUrl(PrestashopShopUrl $shopUrl): ?PrestashopShopPageTypeProfile
    {
        if (empty($shopUrl->page_type_id) || empty($shopUrl->prestashop_shop_id)) {
            return null;
        }

        $storeId = $shopUrl->prestashopShop?->prestashop_store_id;
        if (! is_string($storeId) || $storeId === '') {
            $storeId = PrestashopShop::query()
                ->whereKey($shopUrl->prestashop_shop_id)
                ->value('prestashop_store_id');
        }

        if (! is_string($storeId) || $storeId === '') {
            return null;
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
            ->sortBy(function (PrestashopShopPageTypeProfile $profile) use ($shopUrl): int {
                $exactShop = (string) $profile->prestashop_shop_id === (string) $shopUrl->prestashop_shop_id;
                $prepared = $this->profileHasUsedCssArtifact($profile, 'desktop')
                    || $this->profileHasUsedCssArtifact($profile, 'mobile')
                    || in_array((string) $profile->status, ['ready', 'preparing', 'queued'], true);

                return match (true) {
                    $prepared && $exactShop => 0,
                    $prepared => 1,
                    $exactShop => 2,
                    default => 3,
                };
            })
            ->first();
    }

    /**
     * @param  array<string, mixed>|null  $scanReport
     * @return array<string, mixed>|null
     */
    private function extractDeviceScanReport(?array $scanReport, string $deviceClass): ?array
    {
        if (! is_array($scanReport) || $scanReport === []) {
            return null;
        }

        $deviceKey = strtolower(trim($deviceClass)) === 'mobile' ? 'mobile' : 'desktop';
        $report = $scanReport[$deviceKey] ?? null;

        return is_array($report) ? $report : null;
    }

    private function profileHasUsedCssArtifact(PrestashopShopPageTypeProfile $profile, string $deviceClass): bool
    {
        $artifacts = $profile->relationLoaded('cssArtifacts')
            ? $profile->cssArtifacts
            : PrestashopShopPageTypeCssArtifact::query()
                ->where('profile_id', $profile->id)
                ->where('device_class', $deviceClass)
                ->where('css_type', 'used_css')
                ->get();

        return $artifacts->contains(static function (PrestashopShopPageTypeCssArtifact $artifact) use ($deviceClass): bool {
            return $artifact->css_type === 'used_css'
                && strtolower((string) $artifact->device_class) === strtolower($deviceClass)
                && ! empty($artifact->storage_path)
                && (int) $artifact->bytes > 0;
        });
    }

    private function normalizeAssetReference(string $value): string
    {
        $normalized = trim($value);

        if ($normalized === '') {
            return '';
        }

        if (str_starts_with($normalized, '//')) {
            $normalized = 'https:' . $normalized;
        }

        if (! preg_match('#^https?://#i', $normalized)) {
            return ltrim($normalized, '/');
        }

        $path = parse_url($normalized, PHP_URL_PATH);
        $query = parse_url($normalized, PHP_URL_QUERY);

        return ltrim((string) ($path ?? ''), '/') . (is_string($query) && $query !== '' ? '?' . $query : '');
    }
}
