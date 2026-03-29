<?php

namespace App\Services\Optimization;

use App\Models\OptimizationArtifactVersion;
use App\Models\OptimizationRun;
use App\Models\PrestashopShop;
use App\Models\PrestashopShopUrl;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class UrlVerificationService
{
    public function __construct(
        private readonly BrowserRenderService $browserRenderService,
        private readonly ArtifactValidationService $artifactValidationService
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function verifyLatestPublished(PrestashopShop $shop, PrestashopShopUrl $shopUrl): array
    {
        $run = OptimizationRun::query()
            ->with(['artifactVersions', 'optimizationTarget'])
            ->whereHas('optimizationTarget', function ($query) use ($shopUrl, $shop): void {
                $query
                    ->where('prestashop_shop_url_id', $shopUrl->id)
                    ->where('prestashop_shop_id', $shop->id);
            })
            ->whereIn('status', ['completed', 'completed_with_errors'])
            ->orderByDesc('created_at')
            ->first();

        if (! $run instanceof OptimizationRun) {
            throw new RuntimeException('No completed optimization run is available for this URL.');
        }

        $artifacts = $run->artifactVersions
            ->where('status', 'published')
            ->values();

        if ($artifacts->isEmpty()) {
            throw new RuntimeException('No published optimization artifacts are available for this URL.');
        }

        $results = [];

        /** @var OptimizationArtifactVersion $artifact */
        foreach ($artifacts as $artifact) {
            $optimizedHtmlPath = (string) ($artifact->optimized_html_path ?? '');
            $rawHtmlPath = (string) ($artifact->raw_html_path ?? '');

            if ($optimizedHtmlPath === '' || $rawHtmlPath === '') {
                continue;
            }

            if (! Storage::disk('local')->exists($optimizedHtmlPath) || ! Storage::disk('local')->exists($rawHtmlPath)) {
                continue;
            }

            $optimizedHtml = (string) Storage::disk('local')->get($optimizedHtmlPath);
            $rawHtml = (string) Storage::disk('local')->get($rawHtmlPath);
            $criticalCss = $this->readOptionalFile((string) ($artifact->critical_css_path ?? ''));
            $meta = is_array($artifact->meta_json ?? null) ? $artifact->meta_json : [];
            $usedCssUrl = is_array($meta['used_css'] ?? null) ? ($meta['used_css']['url'] ?? null) : null;
            $variant = is_array($meta['variant'] ?? null) ? $meta['variant'] : [];
            $deviceClass = (string) (($variant['device_class'] ?? null) ?: 'desktop');

            $visualValidation = $this->browserRenderService->validateOptimizedHtml(
                $shopUrl->url,
                $optimizedHtml,
                $deviceClass
            );

            $validation = $this->artifactValidationService->validate(
                [
                    'status_code' => $meta['status_code'] ?? null,
                    'html' => $rawHtml,
                    'console_messages' => $meta['console_messages'] ?? null,
                ],
                $optimizedHtml,
                $criticalCss,
                is_string($usedCssUrl) ? $usedCssUrl : null,
                $visualValidation
            );

            $results[] = [
                'artifact_id' => $artifact->id,
                'version_number' => $artifact->version_number,
                'device_class' => $deviceClass,
                'variant_label' => $meta['variant_label'] ?? null,
                'valid' => (bool) ($validation['valid'] ?? false),
                'failed_checks' => $validation['failed_checks'] ?? [],
                'summary' => $validation['summary'] ?? [],
            ];
        }

        if ($results === []) {
            throw new RuntimeException('Published optimization artifacts could not be loaded for verification.');
        }

        $overallValid = collect($results)->every(static fn (array $result): bool => (bool) ($result['valid'] ?? false));

        return [
            'run_id' => $run->id,
            'shop_url_id' => $shopUrl->id,
            'url' => $shopUrl->url,
            'verified_at' => now()->toIso8601String(),
            'overall_valid' => $overallValid,
            'results' => $results,
        ];
    }

    private function readOptionalFile(string $path): ?string
    {
        if ($path === '' || ! Storage::disk('local')->exists($path)) {
            return null;
        }

        $content = (string) Storage::disk('local')->get($path);

        return trim($content) !== '' ? $content : null;
    }
}
