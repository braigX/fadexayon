<?php

namespace App\Jobs\Optimization;

use App\Models\OptimizationArtifactVersion;
use App\Models\OptimizationCssReport;
use App\Models\OptimizationRun;
use App\Models\PrestashopStoreOptimizationSetting;
use App\Services\Optimization\ArtifactValidationService;
use App\Services\Optimization\BrowserRenderService;
use App\Services\Optimization\CssAnalysisService;
use App\Services\Optimization\HtmlOptimizationService;
use App\Services\Optimization\ModuleCacheService;
use App\Services\Optimization\OptimizationCssReportService;
use App\Services\Optimization\OptimizationRunService;
use App\Services\Optimization\OptimizationUrlValidationService;
use App\Services\Optimization\PageTypeAssetRuleService;
use App\Services\Optimization\PageTypeProfileService;
use App\Services\Performance\FontUsageReportService;
use App\Services\Performance\JsScanReportService;
use App\Services\Performance\PerformanceReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class RunOptimizationJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public int $timeout = 1200;

    public int $tries = 20;

    public function __construct(public readonly string $runId)
    {
        $this->onQueue('optimization-render');
    }

    public function handle(
        OptimizationRunService $runService,
        ModuleCacheService $moduleCacheService,
        BrowserRenderService $browserRenderService,
        CssAnalysisService $cssAnalysisService,
        PageTypeAssetRuleService $pageTypeAssetRuleService,
        OptimizationCssReportService $cssReportService,
        HtmlOptimizationService $htmlOptimizationService,
        ArtifactValidationService $artifactValidationService,
        OptimizationUrlValidationService $optimizationUrlValidationService,
        PageTypeProfileService $pageTypeProfileService,
        PerformanceReportService $performanceReportService,
        FontUsageReportService $fontUsageReportService,
        JsScanReportService $jsScanReportService
    ): void {
        $run = OptimizationRun::query()
            ->with(['optimizationTarget.prestashopStore.optimizationSetting', 'optimizationTarget.prestashopShop', 'optimizationTarget.prestashopShopUrl', 'steps'])
            ->findOrFail($this->runId);

        $target = $run->optimizationTarget;
        $store = $target->prestashopStore;
        $shop = $target->prestashopShop;
        $shopUrl = $target->prestashopShopUrl;
        $pageTypeProfile = $pageTypeProfileService->findProfileForUrl($shopUrl);

        $this->logRunInfo('prestaload.optimization.run.started', $run, [
            'store_id' => $store?->id,
            'shop_id' => $shop?->id,
            'shop_url_id' => $shopUrl?->id,
            'url' => $shopUrl?->url,
            'page_type' => $shopUrl?->page_type,
            'profile_id' => $pageTypeProfile?->id,
            'profile_status' => $pageTypeProfile?->status,
        ]);

        if ($pageTypeProfile !== null
            && ! $pageTypeProfileService->isPreparedProfile($pageTypeProfile)
            && in_array((string) $pageTypeProfile->status, ['queued', 'preparing'], true)
            && (string) ($pageTypeProfile->scan_source_prestashop_shop_url_id ?? '') !== (string) $shopUrl->id
            && $this->attempts() < 10) {
            Log::info('prestaload.optimization.waiting_for_page_type_preparation', [
                'run_id' => $run->id,
                'shop_url_id' => $shopUrl->id,
                'profile_id' => $pageTypeProfile->id,
                'profile_status' => $pageTypeProfile->status,
                'source_shop_url_id' => $pageTypeProfile->scan_source_prestashop_shop_url_id,
                'attempt' => $this->attempts(),
            ]);

            $run->status = 'queued';
            $run->save();
            $runService->markTargetStatus($target, 'queued');

            $this->release(15);

            return;
        }

            $storeOptimizationSettings = array_merge(
            PrestashopStoreOptimizationSetting::defaults(),
            $store->optimizationSetting?->only([
                'css_optimization_enabled',
                'generate_critical_css',
                'defer_safe_stylesheets',
                'optimize_web_fonts',
                'optimize_javascript',
                'delay_ads_analytics_scripts',
                'compress_final_html',
                'minify_css',
                'compress_inline_js',
                'skip_lazy_load_js_patterns',
            ]) ?? []
        );
        $cssOptimizationEnabled = (bool) ($storeOptimizationSettings['css_optimization_enabled'] ?? true);
        $generateCriticalCssEnabled = $cssOptimizationEnabled && (bool) ($storeOptimizationSettings['generate_critical_css'] ?? true);
        $deferSafeStylesheetsEnabled = $cssOptimizationEnabled && (bool) ($storeOptimizationSettings['defer_safe_stylesheets'] ?? true);
        $compressFinalHtmlEnabled = (bool) ($storeOptimizationSettings['compress_final_html'] ?? false);
        $minifyInlineCssEnabled = (bool) ($storeOptimizationSettings['minify_css'] ?? false);
        $minifyInlineJsEnabled = (bool) ($storeOptimizationSettings['compress_inline_js'] ?? false);
        $optimizeWebFontsEnabled = (bool) ($storeOptimizationSettings['optimize_web_fonts'] ?? true);
        $optimizeJavascriptEnabled = (bool) ($storeOptimizationSettings['optimize_javascript'] ?? true);

        if ($this->shouldAbort($run)) {
            return;
        }

        $runService->markRunStarted($run, 'preparing_cache');
        $runService->markTargetStatus($target, 'preparing_cache');

        try {
            $cacheVariants = $moduleCacheService->getCacheVariants($store, [
                'shop_id' => $shop->prestashop_shop_id,
                'url' => $shopUrl->url,
            ]);

            $variants = array_values(array_filter(
                is_array($cacheVariants['variants'] ?? null) ? $cacheVariants['variants'] : [],
                static fn ($variant): bool => is_array($variant) && is_array($variant['variant'] ?? null)
            ));
            $runService->setRunVariants($run, $variants);

            if ($variants === []) {
                $message = 'No cache variants were returned by the module.';
                $runService->markRunFailed($run, $message);
                $runService->markTargetStatus($target, 'failed', $message);

                return;
            }

            $scanStepHandled = false;

            foreach ($variants as $index => $variantRow) {
                $run->refresh();
                $run->loadMissing('optimizationTarget');

                if ($this->shouldAbort($run)) {
                    return;
                }

                $variantLabel = (string) ($variantRow['label'] ?? ('Variant ' . ($index + 1)));
                $variant = is_array($variantRow['variant'] ?? null) ? $variantRow['variant'] : [];
                $runService->markCurrentVariant($run, $variantLabel);
                $artifact = null;
                $cssReport = null;
                $criticalCss = null;
                $reducedCssAssets = [];
                $minifiedCssAssets = [];
                $deliveryStylesheets = [];
                $classifiedStylesheets = [];
                $usedCssUrl = null;
                $preparedVariantKey = null;
                $currentStepName = null;
                $validation = null;
                $visualValidation = null;

                try {
                    $runService->markRunStarted($run, 'preparing_cache');
                    $runService->markTargetStatus($target, 'preparing_cache');

                    $currentStepName = 'validate_target';
                    $validateTargetStep = $runService->startStep($run, 'validate_target', [
                        'shop_url_id' => $shopUrl?->id,
                        'url' => $shopUrl?->url,
                        'variant_label' => $variantLabel,
                    ]);

                    $trustedUrlValidation = $optimizationUrlValidationService->validateTrustedUrl($store, $shop, $shopUrl);
                    if (! ($trustedUrlValidation['valid'] ?? false)) {
                        $message = match ($trustedUrlValidation['reason'] ?? null) {
                            'foreign_domain' => 'URL host does not belong to the selected shop.',
                            default => 'URL host is invalid.',
                        };

                        $runService->failStep($validateTargetStep, $message);
                        throw new \RuntimeException($message);
                    }

                    $runService->completeStep($validateTargetStep, [
                        'variant_label' => $variantLabel,
                        'url_host' => $trustedUrlValidation['url_host'] ?? null,
                    ]);

                    $currentStepName = 'cache_prepare';
                    $cachePrepareStep = $runService->startStep($run, 'cache_prepare', [
                        'shop_url_id' => $shopUrl?->id,
                        'url' => $shopUrl?->url,
                        'variant_label' => $variantLabel,
                        'variant' => $variant,
                    ]);

                    $cachePrepare = $moduleCacheService->prepareCache($store, [
                        'shop_id' => $shop->prestashop_shop_id,
                        'shop_url_id' => $shopUrl->id,
                        'url' => $shopUrl->url,
                        'language_iso' => $variant['language_iso'] ?? $shopUrl->language_iso,
                        'currency_iso' => $variant['currency_iso'] ?? null,
                        'device_class' => $variant['device_class'] ?? $target->device_class,
                        'login_state' => $variant['login_state'] ?? 'guest',
                        'theme_hash' => $variant['theme_hash'] ?? null,
                    ]);

                    $runService->completeStep($cachePrepareStep, $cachePrepare);
                    $this->logStepInfo('prestaload.optimization.step.completed', $run, $variantLabel, 'cache_prepare', $cachePrepareStep->duration_ms, [
                        'variant_key' => $cachePrepare['variant_key'] ?? null,
                        'cacheable' => $cachePrepare['cacheable'] ?? null,
                    ]);
                    $preparedVariantKey = isset($cachePrepare['variant_key']) ? (string) $cachePrepare['variant_key'] : null;

                    $target->normalized_url = (string) ($cachePrepare['normalized_url'] ?? $target->normalized_url);
                    $target->save();

                    if (! ($cachePrepare['cacheable'] ?? false)) {
                        throw new \RuntimeException((string) ($cachePrepare['reason'] ?? 'Page is not cacheable.'));
                    }

                    $runService->markRunStarted($run, 'rendering');
                    $runService->markTargetStatus($target, 'rendering');

                    $currentStepName = 'render_page';
                    $renderStep = $runService->startStep($run, 'render_page', [
                        'url' => $shopUrl->url,
                        'device_class' => $variant['device_class'] ?? $target->device_class,
                        'variant_label' => $variantLabel,
                    ]);

                    $rendered = $browserRenderService->render(
                        $shopUrl->url,
                        (string) ($variant['device_class'] ?? $target->device_class),
                        $minifyInlineCssEnabled,
                        $minifyInlineJsEnabled,
                        false
                    );

                    $renderStatusCode = isset($rendered['status_code']) ? (int) $rendered['status_code'] : null;
                    $finalUrlValidation = $optimizationUrlValidationService->validateHostAgainstAllowed(
                        isset($rendered['final_url']) ? (string) $rendered['final_url'] : null,
                        $trustedUrlValidation['allowed_hosts'] ?? []
                    );

                    if (! ($finalUrlValidation['valid'] ?? false)) {
                        $message = 'Rendered page resolved outside the selected shop domain.';
                        $runService->failStep($renderStep, $message);
                        throw new \RuntimeException($message);
                    }

                    if ($renderStatusCode === null || $renderStatusCode < 200 || $renderStatusCode >= 300) {
                        $message = 'Page returned HTTP ' . ($renderStatusCode ?? 'unknown') . ' during render.';
                        $runService->failStep($renderStep, $message);
                        throw new \RuntimeException($message);
                    }

                    $runService->completeStep($renderStep, [
                        'variant_label' => $variantLabel,
                        'final_url' => $rendered['final_url'] ?? null,
                        'final_url_host' => $finalUrlValidation['host'] ?? null,
                        'status_code' => $renderStatusCode,
                        'html_bytes' => $rendered['html_bytes'] ?? null,
                        'optimized_html_bytes' => $rendered['optimized_html_bytes'] ?? null,
                        'worker_duration_ms' => $rendered['duration_ms'] ?? null,
                    ]);
                    $this->logStepInfo('prestaload.optimization.step.completed', $run, $variantLabel, 'render_page', $renderStep->duration_ms, [
                        'status_code' => $renderStatusCode,
                        'final_url' => $rendered['final_url'] ?? null,
                        'worker_duration_ms' => $rendered['duration_ms'] ?? null,
                    ]);

                    $deviceClass = (string) ($variant['device_class'] ?? $target->device_class);
                    $reusableCssState = $cssOptimizationEnabled
                        ? $pageTypeProfileService->getReusableCssState($shopUrl, $deviceClass, $generateCriticalCssEnabled)
                        : null;
                    $needsReducedCssBackfill = $cssOptimizationEnabled
                        && $pageTypeAssetRuleService->needsReducedCssBackfill($shopUrl, $deviceClass);

                    if ($needsReducedCssBackfill) {
                        $reusableCssState = null;
                    }

                    if ($cssOptimizationEnabled) {
                        if (is_array($reusableCssState)) {
                            $report = $reusableCssState['report'];
                            $deliveryStylesheets = is_array($reusableCssState['stylesheets'] ?? null) ? $reusableCssState['stylesheets'] : [];
                            if ($report instanceof \App\Models\PrestashopShopPageTypeCssReport) {
                                $cssReport = $cssReportService->storeReusedPageTypeReport(
                                    $target,
                                    $run,
                                    $report,
                                    $preparedVariantKey,
                                    $variantLabel
                                );
                            }

                            $currentStepName = 'analyze_css';
                            $analyzeCssStep = $runService->startStep($run, 'analyze_css', [
                                'url' => $shopUrl->url,
                                'device_class' => $deviceClass,
                                'variant_label' => $variantLabel,
                                'variant_key' => $preparedVariantKey,
                            ]);
                            $runService->completeStep($analyzeCssStep, [
                                'variant_label' => $variantLabel,
                                'variant_key' => $preparedVariantKey,
                                'device_class' => $deviceClass,
                                'status_code' => $report->coverage_json['status_code'] ?? $renderStatusCode,
                                'stylesheet_count' => $report->stylesheet_count,
                                'total_css_bytes' => $report->total_css_bytes,
                                'total_used_css_bytes' => $report->total_used_css_bytes,
                                'unused_ratio' => $report->unused_ratio,
                                'css_optimization_enabled' => true,
                                'reused_page_type_profile' => true,
                            ]);
                            $this->logStepInfo('prestaload.optimization.step.completed', $run, $variantLabel, 'analyze_css', $analyzeCssStep->duration_ms, [
                                'reused_page_type_profile' => true,
                                'stylesheet_count' => $report->stylesheet_count,
                                'total_css_bytes' => $report->total_css_bytes,
                                'total_used_css_bytes' => $report->total_used_css_bytes,
                            ]);

                            $currentStepName = 'build_css';
                            $buildCssStep = $runService->startStep($run, 'build_css', [
                                'url' => $shopUrl->url,
                                'device_class' => $deviceClass,
                                'variant_label' => $variantLabel,
                                'variant_key' => $preparedVariantKey,
                            ]);

                            if (! $generateCriticalCssEnabled) {
                                $criticalCss = [
                                    'device_class' => $deviceClass,
                                    'status_code' => $renderStatusCode,
                                    'critical_css' => null,
                                    'critical_css_bytes' => 0,
                                    'critical_css_sha256' => null,
                                    'critical_css_mode' => 'disabled',
                                    'critical_css_capped' => false,
                                    'critical_css_max_bytes' => null,
                                    'critical_css_original_bytes' => null,
                                    'critical_css_simplified_bytes' => null,
                                ];
                            } else {
                                $criticalMeta = is_array($reusableCssState['critical_css']['meta'] ?? null) ? $reusableCssState['critical_css']['meta'] : [];
                                $criticalContent = isset($reusableCssState['critical_css']['content']) ? (string) $reusableCssState['critical_css']['content'] : '';
                                $criticalCss = [
                                    'device_class' => $deviceClass,
                                    'status_code' => $renderStatusCode,
                                    'critical_css' => $criticalContent !== '' ? $criticalContent : null,
                                    'critical_css_bytes' => (int) ($reusableCssState['critical_css']['bytes'] ?? 0),
                                    'critical_css_sha256' => $reusableCssState['critical_css']['sha256'] ?? null,
                                    'critical_css_mode' => (string) ($criticalMeta['mode'] ?? 'full'),
                                    'critical_css_capped' => (bool) ($criticalMeta['capped'] ?? false),
                                    'critical_css_max_bytes' => $criticalMeta['max_bytes'] ?? null,
                                    'critical_css_original_bytes' => $criticalMeta['original_bytes'] ?? null,
                                    'critical_css_simplified_bytes' => $criticalMeta['simplified_bytes'] ?? null,
                                ];
                            }

                            $runService->completeStep($buildCssStep, [
                                'variant_label' => $variantLabel,
                                'variant_key' => $preparedVariantKey,
                                'device_class' => $deviceClass,
                                'status_code' => $criticalCss['status_code'] ?? null,
                                'critical_css_bytes' => $criticalCss['critical_css_bytes'] ?? null,
                                'critical_css_sha256' => $criticalCss['critical_css_sha256'] ?? null,
                                'critical_css_mode' => $criticalCss['critical_css_mode'] ?? null,
                                'critical_css_capped' => $criticalCss['critical_css_capped'] ?? null,
                                'critical_css_max_bytes' => $criticalCss['critical_css_max_bytes'] ?? null,
                                'critical_css_original_bytes' => $criticalCss['critical_css_original_bytes'] ?? null,
                                'critical_css_simplified_bytes' => $criticalCss['critical_css_simplified_bytes'] ?? null,
                                'css_optimization_enabled' => true,
                                'reused_page_type_profile' => true,
                            ]);
                            $this->logStepInfo('prestaload.optimization.step.completed', $run, $variantLabel, 'build_css', $buildCssStep->duration_ms, [
                                'reused_page_type_profile' => true,
                                'critical_css_mode' => $criticalCss['critical_css_mode'] ?? null,
                                'critical_css_bytes' => $criticalCss['critical_css_bytes'] ?? null,
                            ]);

                            $currentStepName = 'build_used_css';
                            $buildUsedCssStep = $runService->startStep($run, 'build_used_css', [
                                'url' => $shopUrl->url,
                                'device_class' => $deviceClass,
                                'variant_label' => $variantLabel,
                                'variant_key' => $preparedVariantKey,
                            ]);

                            $usedCss = [
                                'device_class' => $deviceClass,
                                'status_code' => $renderStatusCode,
                                'used_css' => (string) ($reusableCssState['used_css']['content'] ?? ''),
                                'used_css_bytes' => (int) ($reusableCssState['used_css']['bytes'] ?? 0),
                                'used_css_sha256' => $reusableCssState['used_css']['sha256'] ?? null,
                                'used_css_mode' => (string) (($reusableCssState['used_css']['meta']['mode'] ?? null) ?: 'reused'),
                            ];
                            $usedCssUrl = ! empty($cachePrepare['used_css_url'])
                                ? (string) $cachePrepare['used_css_url']
                                : null;

                            if ($usedCssUrl !== null && ! empty($usedCss['used_css_sha256'])) {
                                $usedCssUrl .= '&v=' . rawurlencode((string) $usedCss['used_css_sha256']);
                            }

                            $runService->completeStep($buildUsedCssStep, [
                                'variant_label' => $variantLabel,
                                'variant_key' => $preparedVariantKey,
                                'device_class' => $deviceClass,
                                'status_code' => $usedCss['status_code'] ?? null,
                                'used_css_bytes' => $usedCss['used_css_bytes'] ?? null,
                                'used_css_sha256' => $usedCss['used_css_sha256'] ?? null,
                                'used_css_mode' => $usedCss['used_css_mode'] ?? null,
                                'css_optimization_enabled' => true,
                                'reused_page_type_profile' => true,
                            ]);
                            $this->logStepInfo('prestaload.optimization.step.completed', $run, $variantLabel, 'build_used_css', $buildUsedCssStep->duration_ms, [
                                'reused_page_type_profile' => true,
                                'used_css_mode' => $usedCss['used_css_mode'] ?? null,
                                'used_css_bytes' => $usedCss['used_css_bytes'] ?? null,
                            ]);
                        } else {
                            $currentStepName = 'analyze_css';
                            $analyzeCssStep = $runService->startStep($run, 'analyze_css', [
                                'url' => $shopUrl->url,
                                'device_class' => $variant['device_class'] ?? $target->device_class,
                                'variant_label' => $variantLabel,
                                'variant_key' => $preparedVariantKey,
                            ]);

                            $cssAnalysis = $cssAnalysisService->analyze(
                                $shopUrl->url,
                                $deviceClass,
                                45000,
                                $generateCriticalCssEnabled
                            );
                            $reducedCssAssets = array_values(array_filter(
                                is_array($cssAnalysis['reduced_css_assets'] ?? null) ? $cssAnalysis['reduced_css_assets'] : [],
                                static fn ($asset): bool => is_array($asset)
                            ));
                            $minifiedCssAssets = array_values(array_filter(
                                is_array($cssAnalysis['minified_css_assets'] ?? null) ? $cssAnalysis['minified_css_assets'] : [],
                                static fn ($asset): bool => is_array($asset)
                            ));

                            $cssReport = $cssReportService->storeAnalysis(
                                $target,
                                $run,
                                $cssAnalysis,
                                $preparedVariantKey,
                                $variantLabel
                            );
                            $deliveryStylesheets = $cssReport->stylesheets->map(static fn ($stylesheet): array => [
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
                            ])->all();

                            $runService->completeStep($analyzeCssStep, [
                                'variant_label' => $variantLabel,
                                'variant_key' => $preparedVariantKey,
                                'device_class' => $cssAnalysis['device_class'] ?? $deviceClass,
                                'status_code' => $cssAnalysis['status_code'] ?? null,
                                'stylesheet_count' => $cssReport->stylesheet_count,
                                'total_css_bytes' => $cssReport->total_css_bytes,
                                'total_used_css_bytes' => $cssReport->total_used_css_bytes,
                                'unused_ratio' => $cssReport->unused_ratio,
                                'css_optimization_enabled' => true,
                            ]);
                            $this->logStepInfo('prestaload.optimization.step.completed', $run, $variantLabel, 'analyze_css', $analyzeCssStep->duration_ms, [
                                'stylesheet_count' => $cssReport->stylesheet_count,
                                'total_css_bytes' => $cssReport->total_css_bytes,
                                'total_used_css_bytes' => $cssReport->total_used_css_bytes,
                            ]);

                            $currentStepName = 'build_css';
                            $buildCssStep = $runService->startStep($run, 'build_css', [
                                'url' => $shopUrl->url,
                                'device_class' => $deviceClass,
                                'variant_label' => $variantLabel,
                                'variant_key' => $preparedVariantKey,
                            ]);

                            if (! $generateCriticalCssEnabled) {
                                $criticalCss = [
                                    'device_class' => $cssAnalysis['device_class'] ?? $deviceClass,
                                    'status_code' => $cssAnalysis['status_code'] ?? null,
                                    'critical_css' => null,
                                    'critical_css_bytes' => 0,
                                    'critical_css_sha256' => null,
                                    'critical_css_mode' => 'disabled',
                                    'critical_css_capped' => false,
                                    'critical_css_max_bytes' => null,
                                    'critical_css_original_bytes' => null,
                                    'critical_css_simplified_bytes' => null,
                                ];
                            } else {
                                $criticalCss = [
                                    'device_class' => $cssAnalysis['device_class'] ?? $deviceClass,
                                    'status_code' => $cssAnalysis['status_code'] ?? null,
                                    'critical_css' => (($cssAnalysis['critical_css'] ?? null) !== null && trim((string) $cssAnalysis['critical_css']) !== '')
                                        ? (string) $cssAnalysis['critical_css']
                                        : null,
                                    'critical_css_bytes' => isset($cssAnalysis['critical_css_bytes']) ? (int) $cssAnalysis['critical_css_bytes'] : 0,
                                    'critical_css_sha256' => isset($cssAnalysis['critical_css_sha256'])
                                        ? (string) $cssAnalysis['critical_css_sha256']
                                        : null,
                                    'critical_css_mode' => isset($cssAnalysis['critical_css_mode'])
                                        ? (string) $cssAnalysis['critical_css_mode']
                                        : (($cssAnalysis['critical_css'] ?? null) !== null && trim((string) $cssAnalysis['critical_css']) !== '' ? 'full' : 'skipped_empty'),
                                    'critical_css_capped' => isset($cssAnalysis['critical_css_capped']) ? (bool) $cssAnalysis['critical_css_capped'] : false,
                                    'critical_css_max_bytes' => isset($cssAnalysis['critical_css_max_bytes']) ? (int) $cssAnalysis['critical_css_max_bytes'] : null,
                                    'critical_css_original_bytes' => isset($cssAnalysis['original_critical_css_bytes']) ? (int) $cssAnalysis['original_critical_css_bytes'] : null,
                                    'critical_css_simplified_bytes' => isset($cssAnalysis['simplified_critical_css_bytes']) ? (int) $cssAnalysis['simplified_critical_css_bytes'] : null,
                                ];
                            }

                            $runService->completeStep($buildCssStep, [
                                'variant_label' => $variantLabel,
                                'variant_key' => $preparedVariantKey,
                                'device_class' => $criticalCss['device_class'] ?? $deviceClass,
                                'status_code' => $criticalCss['status_code'] ?? null,
                                'critical_css_bytes' => $criticalCss['critical_css_bytes'] ?? null,
                                'critical_css_sha256' => $criticalCss['critical_css_sha256'] ?? null,
                                'critical_css_mode' => $criticalCss['critical_css_mode'] ?? null,
                                'critical_css_capped' => $criticalCss['critical_css_capped'] ?? null,
                                'critical_css_max_bytes' => $criticalCss['critical_css_max_bytes'] ?? null,
                                'critical_css_original_bytes' => $criticalCss['critical_css_original_bytes'] ?? null,
                                'critical_css_simplified_bytes' => $criticalCss['critical_css_simplified_bytes'] ?? null,
                                'css_optimization_enabled' => true,
                            ]);
                            $this->logStepInfo('prestaload.optimization.step.completed', $run, $variantLabel, 'build_css', $buildCssStep->duration_ms, [
                                'critical_css_mode' => $criticalCss['critical_css_mode'] ?? null,
                                'critical_css_bytes' => $criticalCss['critical_css_bytes'] ?? null,
                            ]);

                            $currentStepName = 'build_used_css';
                            $buildUsedCssStep = $runService->startStep($run, 'build_used_css', [
                                'url' => $shopUrl->url,
                                'device_class' => $deviceClass,
                                'variant_label' => $variantLabel,
                                'variant_key' => $preparedVariantKey,
                            ]);

                            $usedCss = [
                                'device_class' => $cssAnalysis['device_class'] ?? $deviceClass,
                                'status_code' => $cssAnalysis['status_code'] ?? null,
                                'used_css' => (($cssAnalysis['used_css'] ?? null) !== null && trim((string) $cssAnalysis['used_css']) !== '')
                                    ? (string) $cssAnalysis['used_css']
                                    : null,
                                'used_css_bytes' => isset($cssAnalysis['used_css_bytes']) ? (int) $cssAnalysis['used_css_bytes'] : 0,
                                'used_css_sha256' => isset($cssAnalysis['used_css_sha256'])
                                    ? (string) $cssAnalysis['used_css_sha256']
                                    : null,
                                'used_css_mode' => isset($cssAnalysis['used_css_mode'])
                                    ? (string) $cssAnalysis['used_css_mode']
                                    : 'empty',
                            ];
                            $usedCssUrl = ! empty($cachePrepare['used_css_url'])
                                ? (string) $cachePrepare['used_css_url']
                                : null;

                            if ($usedCssUrl !== null && ! empty($usedCss['used_css_sha256'])) {
                                $usedCssUrl .= '&v=' . rawurlencode((string) $usedCss['used_css_sha256']);
                            }

                            $runService->completeStep($buildUsedCssStep, [
                                'variant_label' => $variantLabel,
                                'variant_key' => $preparedVariantKey,
                                'device_class' => $usedCss['device_class'] ?? $deviceClass,
                                'status_code' => $usedCss['status_code'] ?? null,
                                'used_css_bytes' => $usedCss['used_css_bytes'] ?? null,
                                'used_css_sha256' => $usedCss['used_css_sha256'] ?? null,
                                'used_css_mode' => $usedCss['used_css_mode'] ?? null,
                                'css_optimization_enabled' => true,
                            ]);
                            $this->logStepInfo('prestaload.optimization.step.completed', $run, $variantLabel, 'build_used_css', $buildUsedCssStep->duration_ms, [
                                'used_css_mode' => $usedCss['used_css_mode'] ?? null,
                                'used_css_bytes' => $usedCss['used_css_bytes'] ?? null,
                                'reduced_css_assets_count' => count($reducedCssAssets),
                                'minified_css_assets_count' => count($minifiedCssAssets),
                            ]);
                        }
                    } else {
                        $criticalCss = [
                            'device_class' => $variant['device_class'] ?? $target->device_class,
                            'status_code' => $renderStatusCode,
                            'critical_css' => null,
                            'critical_css_bytes' => 0,
                            'critical_css_sha256' => null,
                            'critical_css_mode' => 'disabled',
                            'critical_css_capped' => false,
                            'critical_css_max_bytes' => null,
                            'critical_css_original_bytes' => null,
                            'critical_css_simplified_bytes' => null,
                        ];
                        $usedCss = [
                            'device_class' => $variant['device_class'] ?? $target->device_class,
                            'status_code' => $renderStatusCode,
                            'used_css' => null,
                            'used_css_bytes' => 0,
                            'used_css_sha256' => null,
                            'used_css_mode' => 'disabled',
                        ];
                        $usedCssUrl = null;
                    }

                    if (! $scanStepHandled) {
                        $currentStepName = 'scan_performance';
                        $scanStep = $runService->startStep($run, 'scan_performance', [
                            'shop_url_id' => $shopUrl->id,
                            'url' => $shopUrl->url,
                            'page_type_id' => $shopUrl->page_type_id,
                            'page_type' => $shopUrl->page_type,
                        ]);

                        if ($pageTypeProfileService->needsPerformanceReport($shopUrl)) {
                            $runService->markRunStarted($run, 'scanning');
                            $runService->markTargetStatus($target, 'scanning');

                            try {
                                $performanceReport = $performanceReportService->scanReport($shopUrl->url);

                                $shopUrl->forceFill([
                                    'mobile_score' => $performanceReport['mobile']['score'] ?? null,
                                    'desktop_score' => $performanceReport['desktop']['score'] ?? null,
                                    'scan_report_json' => $performanceReport,
                                    'last_scanned_at' => now(),
                                ])->save();

                                $pageTypeProfileService->syncPerformanceReport($shopUrl, $performanceReport);

                                $runService->completeStep($scanStep, [
                                    'provider' => $performanceReport['provider'] ?? null,
                                    'scanned_url' => $performanceReport['scanned_url'] ?? null,
                                    'mobile_score' => $performanceReport['mobile']['score'] ?? null,
                                    'desktop_score' => $performanceReport['desktop']['score'] ?? null,
                                    'page_type_id' => $shopUrl->page_type_id,
                                    'page_type' => $shopUrl->page_type,
                                ]);
                                $this->logStepInfo('prestaload.optimization.step.completed', $run, $variantLabel, 'scan_performance', $scanStep->duration_ms, [
                                    'provider' => $performanceReport['provider'] ?? null,
                                    'mobile_score' => $performanceReport['mobile']['score'] ?? null,
                                    'desktop_score' => $performanceReport['desktop']['score'] ?? null,
                                ]);
                            } catch (Throwable $scanThrowable) {
                                $scanFailedMessage = $scanThrowable->getMessage();
                                $runService->failStep($scanStep, $scanFailedMessage);
                                $pageTypeProfileService->markProfileFailedForUrl($shopUrl);

                                Log::error('prestaload.optimization.scan.failed', [
                                    'run_id' => $run->id,
                                    'shop_url_id' => $shopUrl->id,
                                    'url' => $shopUrl->url,
                                    'message' => $scanFailedMessage,
                                ]);
                            }
                        } else {
                            $runService->completeStep($scanStep, [
                                'reused_existing_report' => true,
                                'page_type_id' => $shopUrl->page_type_id,
                                'page_type' => $shopUrl->page_type,
                            ]);
                            $this->logStepInfo('prestaload.optimization.step.completed', $run, $variantLabel, 'scan_performance', $scanStep->duration_ms, [
                                'reused_existing_report' => true,
                            ]);
                        }

                        $scanStepHandled = true;
                    }

                    $runService->markRunStarted($run, 'rendering');
                    $runService->markTargetStatus($target, 'rendering');

                    $currentStepName = 'build_html';
                    $buildHtmlStep = $runService->startStep($run, 'build_html', [
                        'variant_label' => $variantLabel,
                        'device_class' => $variant['device_class'] ?? $target->device_class,
                        'input_html_bytes' => $rendered['html_bytes'] ?? null,
                        'input_optimized_html_bytes' => $rendered['optimized_html_bytes'] ?? null,
                    ]);

                    $resolvedStylesheetActions = ($cssOptimizationEnabled && $deliveryStylesheets !== [])
                        ? $pageTypeAssetRuleService->resolveStylesheetActions(
                            $shopUrl,
                            $deviceClass,
                            $deliveryStylesheets,
                            $usedCssUrl !== null && (($usedCss['used_css_bytes'] ?? 0) > 0)
                        )
                        : ['stylesheets' => [], 'bundles' => []];
                    $classifiedStylesheets = is_array($resolvedStylesheetActions['stylesheets'] ?? null)
                        ? $resolvedStylesheetActions['stylesheets']
                        : [];
                    $stylesheetBundles = is_array($resolvedStylesheetActions['bundles'] ?? null)
                        ? $resolvedStylesheetActions['bundles']
                        : [];
                    $pageTypeProfile = $pageTypeProfileService->findProfileForUrl($shopUrl);
                    $classifiedScripts = [];
                    $classifiedFontAssets = [];
                    if ($optimizeWebFontsEnabled && $pageTypeProfile instanceof \App\Models\PrestashopShopPageTypeProfile) {
                        try {
                            if ($pageTypeProfileService->needsFontUsageReport($shopUrl)) {
                                $fontUsageReport = $fontUsageReportService->scanReport($shopUrl->url);
                                $pageTypeProfile = $pageTypeProfileService->syncFontUsageReport($shopUrl, $fontUsageReport)
                                    ?? $pageTypeProfileService->findProfileForUrl($shopUrl)
                                    ?? $pageTypeProfile;
                            }
                        } catch (\Throwable $fontError) {
                            Log::warning('prestaload.optimization.font_usage_inline.failed', [
                                'run_id' => $run->id,
                                'shop_url_id' => $shopUrl->id,
                                'url' => $shopUrl->url,
                                'device_class' => $deviceClass,
                                'message' => $fontError->getMessage(),
                            ]);
                        }
                    }
                    if ($optimizeJavascriptEnabled && $pageTypeProfile instanceof \App\Models\PrestashopShopPageTypeProfile) {
                        $classifiedScripts = $pageTypeAssetRuleService->resolveScriptActions(
                            $shopUrl,
                            $deviceClass,
                            $storeOptimizationSettings
                        );
                    }
                    if ($optimizeWebFontsEnabled && $pageTypeProfile instanceof \App\Models\PrestashopShopPageTypeProfile) {
                        $classifiedFontAssets = $pageTypeAssetRuleService->resolveFontActions(
                            $shopUrl,
                            $deviceClass
                        );
                    }

                    $builtHtml = $htmlOptimizationService->buildOptimizedHtml(
                        $rendered,
                        is_array($cachePrepare['variant'] ?? null) ? $cachePrepare['variant'] : $variant,
                        isset($criticalCss['critical_css']) ? (string) $criticalCss['critical_css'] : null,
                        $usedCssUrl,
                        $deferSafeStylesheetsEnabled ? $classifiedStylesheets : [],
                        $deferSafeStylesheetsEnabled ? $stylesheetBundles : [],
                        $optimizeJavascriptEnabled ? $classifiedScripts : [],
                        $optimizeWebFontsEnabled ? $classifiedFontAssets : [],
                        $deferSafeStylesheetsEnabled,
                        $optimizeWebFontsEnabled,
                        $optimizeJavascriptEnabled,
                        $compressFinalHtmlEnabled,
                        $minifyInlineCssEnabled,
                        $minifyInlineJsEnabled
                    );

                    $runService->completeStep($buildHtmlStep, [
                        'variant_label' => $variantLabel,
                        'html_bytes' => $builtHtml['html_bytes'],
                        'html_sha256' => $builtHtml['html_sha256'],
                        'adjustments' => $builtHtml['adjustments'],
                        'asset_rule_summary' => $builtHtml['adjustments']['asset_rule_summary'] ?? null,
                    ]);
                    $this->logStepInfo('prestaload.optimization.step.completed', $run, $variantLabel, 'build_html', $buildHtmlStep->duration_ms, [
                        'html_bytes' => $builtHtml['html_bytes'],
                        'asset_rule_summary' => $builtHtml['adjustments']['asset_rule_summary'] ?? null,
                    ]);

                    $currentStepName = 'validate_artifact';
                    $validationStep = $runService->startStep($run, 'validate_artifact', [
                        'variant_label' => $variantLabel,
                        'status_code' => $rendered['status_code'] ?? null,
                        'html_bytes' => $builtHtml['html_bytes'],
                        'html_sha256' => $builtHtml['html_sha256'],
                    ]);

                    $versionNumber = ((int) OptimizationArtifactVersion::query()
                        ->where('optimization_target_id', $target->id)
                        ->max('version_number')) + 1;
                    $storagePrefix = 'prestaload/optimization-runs/' . $run->id . '/version-' . $versionNumber;
                    $rawHtmlPath = $storagePrefix . '/raw.html';
                    $optimizedHtmlPath = $storagePrefix . '/optimized.html';
                    $criticalCssPath = $storagePrefix . '/critical.css';
                    $usedCssPath = $storagePrefix . '/used.css';
                    $rawHtml = (string) ($rendered['html'] ?? '');
                    $optimizedHtml = $builtHtml['html'];
                    $criticalCssContent = (string) ($criticalCss['critical_css'] ?? '');
                    $usedCssContent = (string) ($usedCss['used_css'] ?? '');

                    Storage::disk('local')->put($rawHtmlPath, $rawHtml);
                    Storage::disk('local')->put($optimizedHtmlPath, $optimizedHtml);
                    if ($criticalCssContent !== '') {
                        Storage::disk('local')->put($criticalCssPath, $criticalCssContent);
                    }
                    if ($usedCssContent !== '') {
                        Storage::disk('local')->put($usedCssPath, $usedCssContent);
                    }

                    $artifact = OptimizationArtifactVersion::query()->create([
                        'id' => (string) Str::uuid(),
                        'optimization_target_id' => $target->id,
                        'optimization_run_id' => $run->id,
                        'device_class' => $deviceClass,
                        'version_number' => $versionNumber,
                        'status' => 'draft',
                        'storage_prefix' => $storagePrefix,
                        'raw_html_path' => $rawHtmlPath,
                        'optimized_html_path' => $optimizedHtmlPath,
                        'critical_css_path' => $criticalCssContent !== '' ? $criticalCssPath : null,
                        'used_css_path' => $usedCssContent !== '' ? $usedCssPath : null,
                        'raw_html_bytes' => strlen($rawHtml),
                        'optimized_html_bytes' => $builtHtml['html_bytes'],
                        'critical_css_bytes' => $criticalCssContent !== '' ? strlen($criticalCssContent) : null,
                        'used_css_bytes' => $usedCssContent !== '' ? strlen($usedCssContent) : null,
                        'raw_html_sha256' => hash('sha256', $rawHtml),
                        'optimized_html_sha256' => $builtHtml['html_sha256'],
                        'critical_css_sha256' => $criticalCssContent !== '' ? ($criticalCss['critical_css_sha256'] ?? hash('sha256', $criticalCssContent)) : null,
                        'used_css_sha256' => $usedCssContent !== '' ? ($usedCss['used_css_sha256'] ?? hash('sha256', $usedCssContent)) : null,
                        'meta_json' => [
                            'variant_label' => $variantLabel,
                            'variant' => $cachePrepare['variant'] ?? $variant,
                            'final_url' => $rendered['final_url'] ?? null,
                            'status_code' => $rendered['status_code'] ?? null,
                            'html_bytes' => $rendered['html_bytes'] ?? null,
                            'optimized_html_bytes' => $rendered['optimized_html_bytes'] ?? null,
                            'artifact_raw_html_bytes' => strlen($rawHtml),
                            'artifact_optimized_html_bytes' => $builtHtml['html_bytes'],
                            'artifact_critical_css_bytes' => $criticalCssContent !== '' ? strlen($criticalCssContent) : 0,
                            'artifact_used_css_bytes' => $usedCssContent !== '' ? strlen($usedCssContent) : 0,
                            'console_messages' => $rendered['console_messages'] ?? null,
                            'headers' => $rendered['headers'] ?? null,
                            'critical_css' => [
                                'bytes' => $criticalCss['critical_css_bytes'] ?? 0,
                                'status_code' => $criticalCss['status_code'] ?? null,
                                'sha256' => $criticalCss['critical_css_sha256'] ?? null,
                                'mode' => $criticalCss['critical_css_mode'] ?? null,
                                'capped' => $criticalCss['critical_css_capped'] ?? false,
                                'max_bytes' => $criticalCss['critical_css_max_bytes'] ?? null,
                                'original_bytes' => $criticalCss['critical_css_original_bytes'] ?? null,
                                'simplified_bytes' => $criticalCss['critical_css_simplified_bytes'] ?? null,
                            ],
                            'used_css' => [
                                'bytes' => $usedCss['used_css_bytes'] ?? 0,
                                'status_code' => $usedCss['status_code'] ?? null,
                                'sha256' => $usedCss['used_css_sha256'] ?? null,
                                'mode' => $usedCss['used_css_mode'] ?? null,
                                'url' => $usedCssUrl,
                            ],
                            'asset_rule_summary' => $builtHtml['adjustments']['asset_rule_summary'] ?? [],
                            'html_adjustments' => $builtHtml['adjustments'],
                            'visual_validation' => null,
                        ],
                    ]);

                    if ($cssReport instanceof OptimizationCssReport) {
                        $cssReportService->attachArtifact($cssReport, $artifact);
                    }

                    $validation = $artifactValidationService->validate(
                        $rendered,
                        $builtHtml['html'],
                        $criticalCss['critical_css'] ?? null,
                        $usedCssUrl,
                        null
                    );
                    $artifact->meta_json = array_merge($artifact->meta_json ?? [], [
                        'validation' => $validation,
                    ]);
                    $artifact->save();
                    if (! ($validation['valid'] ?? false)) {
                        $runService->failStep($validationStep, (string) ($validation['error_message'] ?? 'Artifact validation failed.'));
                        throw new \RuntimeException((string) ($validation['error_message'] ?? 'Artifact validation failed.'));
                    }

                    $runService->completeStep($validationStep, [
                        'variant_label' => $variantLabel,
                        'status_code' => $validation['summary']['status_code'] ?? null,
                        'optimized_html_bytes' => $validation['summary']['optimized_html_bytes'] ?? null,
                        'visual_diff_ratio' => $validation['summary']['visual_diff_ratio'] ?? null,
                        'visual_diff_pixels' => $validation['summary']['visual_diff_pixels'] ?? null,
                    ]);
                    $this->logStepInfo('prestaload.optimization.step.completed', $run, $variantLabel, 'validate_artifact', $validationStep->duration_ms, [
                        'status_code' => $validation['summary']['status_code'] ?? null,
                        'optimized_html_bytes' => $validation['summary']['optimized_html_bytes'] ?? null,
                    ]);

                    $runService->markRunStarted($run, 'publishing');
                    $runService->markTargetStatus($target, 'publishing');

                    $currentStepName = 'publish_cache';
                    $publishStep = $runService->startStep($run, 'publish_cache', [
                        'artifact_id' => $artifact->id,
                        'variant_label' => $variantLabel,
                        'variant_key' => $preparedVariantKey,
                    ]);

                    $publish = $moduleCacheService->publishCache($store, [
                        'artifact_version_id' => $artifact->id,
                        'variant_key' => $preparedVariantKey,
                        'variant' => $cachePrepare['variant'] ?? [],
                        'normalized_url' => $cachePrepare['normalized_url'] ?? $target->normalized_url,
                        'html_type' => 'optimized',
                        'html' => $optimizedHtml,
                        'html_bytes' => $builtHtml['html_bytes'],
                        'checksum_sha256' => $builtHtml['html_sha256'],
                        'used_css' => $usedCssContent,
                        'used_css_sha256' => $usedCss['used_css_sha256'] ?? null,
                    ]);

                    $runService->completeStep($publishStep, $publish);
                    $this->logStepInfo('prestaload.optimization.step.completed', $run, $variantLabel, 'publish_cache', $publishStep->duration_ms, [
                        'variant_key' => $preparedVariantKey,
                    ]);

                    $artifact->status = 'published';
                    $artifact->meta_json = array_merge($artifact->meta_json ?? [], [
                        'publish_response' => $publish,
                        'variant_key' => $preparedVariantKey,
                    ]);
                    $artifact->save();

                    if ($cssReport instanceof OptimizationCssReport) {
                        $pageTypeProfileService->syncLatestOptimization(
                            $shopUrl,
                            $cssReport->fresh('stylesheets'),
                            $artifact,
                            $reducedCssAssets,
                            $minifiedCssAssets
                        );
                    }

                    $runService->markVariantCompleted($run);

                } catch (Throwable $variantThrowable) {
                    if ($preparedVariantKey !== null && $preparedVariantKey !== '') {
                        try {
                            $moduleCacheService->purgeCache($store, [
                                'variant_key' => $preparedVariantKey,
                            ]);
                        } catch (Throwable) {
                        }
                    }

                    foreach ($run->steps()->where('status', 'running')->get() as $pendingStep) {
                        $runService->failStep($pendingStep, $variantThrowable->getMessage());
                    }

                    if ($artifact instanceof OptimizationArtifactVersion) {
                        if (!empty($artifact->storage_prefix)) {
                            Storage::disk('local')->deleteDirectory((string) $artifact->storage_prefix);
                        }

                        $artifact->status = 'failed';
                        $artifact->meta_json = array_merge($artifact->meta_json ?? [], [
                            'rollback_reason' => $variantThrowable->getMessage(),
                            'rolled_back_at' => now()->toIso8601String(),
                        ]);
                        $artifact->save();
                    }

                    $runService->markVariantFailed($run);

                    Log::error('prestaload.optimization.variant.failed', [
                        'run_id' => $run->id,
                        'variant_index' => $index + 1,
                        'variant_label' => $variantLabel,
                        'step_name' => $currentStepName,
                        'url' => $shopUrl->url,
                        'device_class' => $variant['device_class'] ?? $target->device_class,
                        'variant_key' => $preparedVariantKey,
                        'failed_checks' => is_array($validation['failed_checks'] ?? null) ? $validation['failed_checks'] : [],
                        'validation_summary' => is_array($validation['summary'] ?? null) ? $validation['summary'] : null,
                        'message' => $variantThrowable->getMessage(),
                    ]);
                }
            }

            $run = $run->fresh();
            $finalStatus = (int) $run->failed_variants > 0
                ? ((int) $run->completed_variants > 0 ? 'completed_with_errors' : 'failed')
                : 'completed';

            if ($finalStatus === 'failed') {
                $message = $run->failure_reason ?: 'All optimization variants failed.';
                $runService->markRunFailed($run, $message);
                $runService->markTargetStatus($target, 'failed', $message);
                $pageTypeProfileService->markProfileFailedForUrl($shopUrl);
            } else {
                $scanStep = $run->steps()->where('step_name', 'scan_performance')->first();
                if ($scanStep && $scanStep->status === 'created') {
                    $scanStep = $runService->startStep($run, 'scan_performance', [
                        'shop_url_id' => $shopUrl->id,
                        'url' => $shopUrl->url,
                        'page_type_id' => $shopUrl->page_type_id,
                        'page_type' => $shopUrl->page_type,
                    ]);
                    $runService->completeStep($scanStep, [
                        'reused_existing_report' => true,
                        'page_type_id' => $shopUrl->page_type_id,
                        'page_type' => $shopUrl->page_type,
                    ]);
                }

                if ($scanStep && $scanStep->status === 'failed' && $finalStatus === 'completed') {
                    $finalStatus = 'completed_with_errors';
                    $run->failure_reason = $scanStep->error_summary;
                    $run->save();
                }

                $runService->markRunCompleted($run, $finalStatus);
                $runService->markTargetStatus($target, $finalStatus === 'completed' ? 'completed' : 'completed_with_errors');
                $this->logRunInfo('prestaload.optimization.run.completed', $run->fresh(), [
                    'final_status' => $finalStatus,
                    'completed_variants' => $run->completed_variants,
                    'failed_variants' => $run->failed_variants,
                ]);
            }

        } catch (Throwable $e) {
            Log::error('prestaload.optimization.failed', [
                'run_id' => $run->id,
                'target_id' => $target->id,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            foreach ($run->steps()->whereIn('status', ['created', 'queued', 'running'])->get() as $pendingStep) {
                $runService->failStep($pendingStep, $e->getMessage());
            }

            foreach ($run->artifactVersions()->get() as $artifactVersion) {
                if (!empty($artifactVersion->storage_prefix)) {
                    Storage::disk('local')->deleteDirectory((string) $artifactVersion->storage_prefix);
                }

                $artifactVersion->status = 'failed';
                $artifactVersion->meta_json = array_merge($artifactVersion->meta_json ?? [], [
                    'rollback_reason' => $e->getMessage(),
                    'rolled_back_at' => now()->toIso8601String(),
                ]);
                $artifactVersion->save();
            }

            $runService->markRunFailed($run, $e->getMessage());
            $runService->markTargetStatus($target, 'failed', $e->getMessage());
            $pageTypeProfileService->markProfileFailedForUrl($shopUrl);

            throw $e;
        }
    }

    private function shouldAbort(OptimizationRun $run): bool
    {
        $target = $run->optimizationTarget;

        return $run->status === 'failed'
            && $target !== null
            && $target->current_optimization_run_id !== $run->id;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function logRunInfo(string $event, OptimizationRun $run, array $context = []): void
    {
        Log::info($event, array_merge([
            'run_id' => $run->id,
            'trigger_type' => $run->trigger_type,
            'target_id' => $run->optimization_target_id,
            'shop_url_id' => $run->optimizationTarget?->prestashopShopUrl?->id,
            'url' => $run->optimizationTarget?->prestashopShopUrl?->url,
            'page_type' => $run->optimizationTarget?->prestashopShopUrl?->page_type,
            'duration_ms' => $run->duration_ms,
        ], $context));
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function logStepInfo(
        string $event,
        OptimizationRun $run,
        string $variantLabel,
        string $stepName,
        ?int $durationMs,
        array $context = []
    ): void {
        Log::info($event, array_merge([
            'run_id' => $run->id,
            'trigger_type' => $run->trigger_type,
            'shop_url_id' => $run->optimizationTarget?->prestashopShopUrl?->id,
            'url' => $run->optimizationTarget?->prestashopShopUrl?->url,
            'page_type' => $run->optimizationTarget?->prestashopShopUrl?->page_type,
            'variant_label' => $variantLabel,
            'step_name' => $stepName,
            'duration_ms' => $durationMs,
        ], $context));
    }

}
