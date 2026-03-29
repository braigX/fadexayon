<?php

namespace App\Services\Optimization;

use App\Models\PrestashopShopPageTypeAssetRule;
use App\Models\PrestashopShopPageTypeCssArtifact;
use App\Models\PrestashopShopPageTypeCssReport;
use App\Models\PrestashopShopPageTypeProfile;
use Illuminate\Support\Facades\File;

class PageTypeReducedCssAssetService
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function storeActionBundles(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass
    ): array {
        $report = PrestashopShopPageTypeCssReport::query()
            ->with('stylesheets')
            ->where('profile_id', $profile->id)
            ->where('device_class', $deviceClass)
            ->first();

        if (! $report instanceof PrestashopShopPageTypeCssReport) {
            return [];
        }

        $rules = PrestashopShopPageTypeAssetRule::query()
            ->where('profile_id', $profile->id)
            ->where('device_class', $deviceClass)
            ->where('asset_type', 'css')
            ->get()
            ->keyBy('asset_url');

        $orderedStylesheets = $report->stylesheets
            ->sortBy('position')
            ->values();

        $bundleMap = [];

        foreach ([
            'minify' => [
                'css_type' => 'minify_bundle_css',
                'asset_path_field' => 'minified_css_public_path',
                'asset_url_field' => 'minified_css_public_url',
            ],
            'reduce' => [
                'css_type' => 'reduce_bundle_css',
                'asset_path_field' => 'reduced_css_public_path',
                'asset_url_field' => 'reduced_css_public_url',
            ],
            'reduce_minify' => [
                'css_type' => 'reduce_minify_bundle_css',
                'asset_path_field' => 'reduced_css_public_path',
                'asset_url_field' => 'reduced_css_public_url',
            ],
        ] as $action => $config) {
            $assetPaths = [];
            $assetUrls = [];

            foreach ($orderedStylesheets as $stylesheet) {
                $sourceUrl = trim((string) ($stylesheet->source_url ?? ''));
                if ($sourceUrl === '') {
                    continue;
                }

                /** @var PrestashopShopPageTypeAssetRule|null $rule */
                $rule = $rules->get($sourceUrl);
                if (! $rule instanceof PrestashopShopPageTypeAssetRule || (string) $rule->effective_action !== $action) {
                    continue;
                }

                $publicPath = trim((string) ($rule->{$config['asset_path_field']} ?? ''));
                $publicUrl = trim((string) ($rule->{$config['asset_url_field']} ?? ''));
                if ($publicPath === '' || ! File::exists(public_path($publicPath))) {
                    continue;
                }

                $assetPaths[] = $publicPath;
                $assetUrls[] = $publicUrl;
            }

            $bundleMap[$action] = $this->storeBundleArtifact(
                $profile,
                $deviceClass,
                $action,
                (string) $config['css_type'],
                $assetPaths,
                $assetUrls
            );
        }

        return array_filter($bundleMap, static fn ($bundle): bool => is_array($bundle) && $bundle !== []);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getActionBundles(PrestashopShopPageTypeProfile $profile, string $deviceClass): array
    {
        return PrestashopShopPageTypeCssArtifact::query()
            ->where('profile_id', $profile->id)
            ->where('device_class', $deviceClass)
            ->whereIn('css_type', ['minify_bundle_css', 'reduce_bundle_css', 'reduce_minify_bundle_css'])
            ->get()
            ->mapWithKeys(static function (PrestashopShopPageTypeCssArtifact $artifact): array {
                $action = match ((string) $artifact->css_type) {
                    'minify_bundle_css' => 'minify',
                    'reduce_bundle_css' => 'reduce',
                    'reduce_minify_bundle_css' => 'reduce_minify',
                    default => null,
                };

                if ($action === null) {
                    return [];
                }

                $meta = is_array($artifact->meta_json) ? $artifact->meta_json : [];

                return [
                    $action => [
                        'css_type' => (string) $artifact->css_type,
                        'public_url' => (string) ($meta['public_url'] ?? ''),
                        'asset_count' => (int) ($meta['asset_count'] ?? 0),
                        'bytes' => (int) $artifact->bytes,
                        'sha256' => (string) ($artifact->sha256 ?? ''),
                    ],
                ];
            })
            ->all();
    }

    /**
     * @param  list<array<string, mixed>>  $assets
     */
    public function storeMinifiedCssAssets(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass,
        array $assets
    ): int {
        $stored = 0;

        foreach ($assets as $asset) {
            $sourceUrl = trim((string) ($asset['url'] ?? ''));
            $content = trim((string) ($asset['minified_css'] ?? ''));
            $sha256 = trim((string) ($asset['minified_css_sha256'] ?? ''));

            if ($sourceUrl === '' || $content === '' || $sha256 === '') {
                continue;
            }

            /** @var PrestashopShopPageTypeAssetRule|null $rule */
            $rule = PrestashopShopPageTypeAssetRule::query()
                ->where('profile_id', $profile->id)
                ->where('device_class', $deviceClass)
                ->where('asset_type', 'css')
                ->where('asset_url', $sourceUrl)
                ->first();

            if (! $rule instanceof PrestashopShopPageTypeAssetRule) {
                continue;
            }

            $rewrittenCss = $this->rewriteCssAssetUrls($content, $sourceUrl);
            $publicRelativePath = $this->buildPublicRelativePath($profile, $deviceClass, $sourceUrl, $sha256, 'minified');
            $publicAbsolutePath = public_path($publicRelativePath);
            File::ensureDirectoryExists(dirname($publicAbsolutePath));
            File::put($publicAbsolutePath, $rewrittenCss);

            $previousPath = trim((string) ($rule->minified_css_public_path ?? ''));
            if ($previousPath !== '' && $previousPath !== $publicRelativePath) {
                File::delete(public_path($previousPath));
            }

            $rule->forceFill([
                'minified_css_status' => 'ready',
                'minified_css_public_path' => $publicRelativePath,
                'minified_css_public_url' => $this->buildPublicUrl($publicRelativePath),
                'minified_css_asset_bytes' => strlen($rewrittenCss),
                'minified_css_sha256' => hash('sha256', $rewrittenCss),
                'last_minified_at' => now(),
            ])->save();

            $stored++;
        }

        return $stored;
    }

    /**
     * @param  list<array<string, mixed>>  $assets
     */
    public function storeReducedCssAssets(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass,
        array $assets
    ): int {
        $stored = 0;

        foreach ($assets as $asset) {
            $sourceUrl = trim((string) ($asset['url'] ?? ''));
            $content = trim((string) ($asset['reduced_css'] ?? ''));
            $sha256 = trim((string) ($asset['reduced_css_sha256'] ?? ''));

            if ($sourceUrl === '' || $content === '' || $sha256 === '') {
                continue;
            }

            /** @var PrestashopShopPageTypeAssetRule|null $rule */
            $rule = PrestashopShopPageTypeAssetRule::query()
                ->where('profile_id', $profile->id)
                ->where('device_class', $deviceClass)
                ->where('asset_type', 'css')
                ->where('asset_url', $sourceUrl)
                ->first();

            if (! $rule instanceof PrestashopShopPageTypeAssetRule) {
                continue;
            }

            $rewrittenCss = $this->rewriteCssAssetUrls($content, $sourceUrl);
            $publicRelativePath = $this->buildPublicRelativePath($profile, $deviceClass, $sourceUrl, $sha256, 'reduced');
            $publicAbsolutePath = public_path($publicRelativePath);
            File::ensureDirectoryExists(dirname($publicAbsolutePath));
            File::put($publicAbsolutePath, $rewrittenCss);

            $previousPath = trim((string) ($rule->reduced_css_public_path ?? ''));
            if ($previousPath !== '' && $previousPath !== $publicRelativePath) {
                File::delete(public_path($previousPath));
            }

            $rule->forceFill([
                'reduced_css_status' => 'ready',
                'reduced_css_public_path' => $publicRelativePath,
                'reduced_css_public_url' => $this->buildPublicUrl($publicRelativePath),
                'reduced_css_bytes' => strlen($rewrittenCss),
                'reduced_css_sha256' => hash('sha256', $rewrittenCss),
                'last_reduced_at' => now(),
            ])->save();

            $stored++;
        }

        return $stored;
    }

    private function buildPublicRelativePath(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass,
        string $sourceUrl,
        string $sha256,
        string $variant
    ): string {
        $assetKey = hash('sha256', $sourceUrl);

        return sprintf(
            'prestaload-assets/page-type-profiles/%s/%s/%s-%s.%s.css',
            $profile->id,
            strtolower(trim($deviceClass)) === 'mobile' ? 'mobile' : 'desktop',
            $assetKey,
            substr($sha256, 0, 16),
            $variant === 'minified' ? 'min' : 'reduced'
        );
    }

    private function buildPublicUrl(string $relativePath): string
    {
        $baseUrl = trim((string) config('services.prestaload_assets.base_url', ''));
        if ($baseUrl === '') {
            $baseUrl = trim((string) config('app.url', 'http://localhost'));
        }

        $baseUrl = rtrim($baseUrl, '/');

        return $baseUrl . '/' . ltrim($relativePath, '/');
    }

    /**
     * @param  list<string>  $assetPaths
     * @param  list<string>  $assetUrls
     * @return array<string, mixed>
     */
    private function storeBundleArtifact(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass,
        string $action,
        string $cssType,
        array $assetPaths,
        array $assetUrls
    ): array {
        /** @var PrestashopShopPageTypeCssArtifact|null $existingArtifact */
        $existingArtifact = PrestashopShopPageTypeCssArtifact::query()
            ->where('profile_id', $profile->id)
            ->where('device_class', $deviceClass)
            ->where('css_type', $cssType)
            ->first();

        if ($assetPaths === []) {
            if ($existingArtifact instanceof PrestashopShopPageTypeCssArtifact) {
                $previousPath = trim((string) ($existingArtifact->storage_path ?? ''));
                if ($previousPath !== '') {
                    File::delete(public_path($previousPath));
                }
                $existingArtifact->delete();
            }

            return [];
        }

        $bundleContent = [];
        foreach ($assetPaths as $assetPath) {
            $absolutePath = public_path($assetPath);
            if (! File::exists($absolutePath)) {
                continue;
            }

            $bundleContent[] = trim((string) File::get($absolutePath));
        }

        $combinedCss = trim(implode("\n", array_filter($bundleContent, static fn ($value): bool => $value !== '')));
        if ($combinedCss === '') {
            return [];
        }

        $sha256 = hash('sha256', $combinedCss);
        $publicRelativePath = $this->buildBundleRelativePath($profile, $deviceClass, $action, $sha256);
        $publicAbsolutePath = public_path($publicRelativePath);
        File::ensureDirectoryExists(dirname($publicAbsolutePath));
        File::put($publicAbsolutePath, $combinedCss);

        if ($existingArtifact instanceof PrestashopShopPageTypeCssArtifact) {
            $previousPath = trim((string) ($existingArtifact->storage_path ?? ''));
            if ($previousPath !== '' && $previousPath !== $publicRelativePath) {
                File::delete(public_path($previousPath));
            }
        }

        $artifact = PrestashopShopPageTypeCssArtifact::query()->updateOrCreate(
            [
                'profile_id' => $profile->id,
                'device_class' => $deviceClass,
                'css_type' => $cssType,
            ],
            [
                'id' => $existingArtifact?->id ?: (string) \Illuminate\Support\Str::uuid(),
                'status' => 'published',
                'storage_path' => $publicRelativePath,
                'bytes' => strlen($combinedCss),
                'sha256' => $sha256,
                'meta_json' => [
                    'action' => $action,
                    'public_url' => $this->buildPublicUrl($publicRelativePath),
                    'asset_count' => count($assetPaths),
                    'source_asset_urls' => array_values(array_filter($assetUrls, static fn ($url): bool => is_string($url) && $url !== '')),
                ],
                'published_at' => now(),
            ]
        );

        $meta = is_array($artifact->meta_json) ? $artifact->meta_json : [];

        return [
            'css_type' => $cssType,
            'public_url' => (string) ($meta['public_url'] ?? ''),
            'asset_count' => (int) ($meta['asset_count'] ?? 0),
            'bytes' => (int) $artifact->bytes,
            'sha256' => (string) ($artifact->sha256 ?? ''),
        ];
    }

    private function buildBundleRelativePath(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass,
        string $action,
        string $sha256
    ): string {
        return sprintf(
            'prestaload-assets/page-type-profiles/%s/%s/bundles/%s-%s.css',
            $profile->id,
            strtolower(trim($deviceClass)) === 'mobile' ? 'mobile' : 'desktop',
            str_replace('_', '-', strtolower($action)),
            substr($sha256, 0, 16)
        );
    }

    private function rewriteCssAssetUrls(string $css, string $sourceUrl): string
    {
        return preg_replace_callback(
            '/url\(\s*(["\']?)(.*?)\1\s*\)/i',
            function (array $matches) use ($sourceUrl): string {
                $quote = $matches[1] ?? '';
                $value = trim((string) ($matches[2] ?? ''));

                if ($value === '' || $this->isAlreadyAbsoluteCssReference($value)) {
                    return 'url(' . $quote . $value . $quote . ')';
                }

                $resolved = $this->resolveCssReference($sourceUrl, $value);

                return 'url(' . $quote . $resolved . $quote . ')';
            },
            $css
        ) ?? $css;
    }

    private function isAlreadyAbsoluteCssReference(string $value): bool
    {
        $normalized = strtolower(trim($value));

        return $normalized === ''
            || str_starts_with($normalized, 'data:')
            || str_starts_with($normalized, 'http://')
            || str_starts_with($normalized, 'https://')
            || str_starts_with($normalized, '//')
            || str_starts_with($normalized, '#')
            || str_starts_with($normalized, 'blob:')
            || str_starts_with($normalized, 'about:');
    }

    private function resolveCssReference(string $sourceUrl, string $reference): string
    {
        $reference = trim($reference);

        try {
            $source = parse_url($sourceUrl);

            if (! is_array($source) || empty($source['scheme']) || empty($source['host'])) {
                return $reference;
            }

            $origin = $source['scheme'] . '://' . $source['host'] . (isset($source['port']) ? ':' . $source['port'] : '');

            if (str_starts_with($reference, '/')) {
                return $origin . $reference;
            }

            $sourcePath = (string) ($source['path'] ?? '/');
            $baseDirectory = preg_replace('#/[^/]*$#', '/', $sourcePath) ?: '/';
            $combined = $baseDirectory . $reference;

            $segments = [];
            foreach (explode('/', $combined) as $segment) {
                if ($segment === '' || $segment === '.') {
                    continue;
                }

                if ($segment === '..') {
                    array_pop($segments);
                    continue;
                }

                $segments[] = $segment;
            }

            return $origin . '/' . implode('/', $segments);
        } catch (\Throwable) {
            return $reference;
        }
    }
}
