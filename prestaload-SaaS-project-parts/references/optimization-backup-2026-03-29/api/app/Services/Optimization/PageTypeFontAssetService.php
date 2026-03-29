<?php

namespace App\Services\Optimization;

use App\Models\PrestashopShopPageTypeAssetRule;
use App\Models\PrestashopShopPageTypeProfile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PageTypeFontAssetService
{
    public function syncSelfHostedAssets(PrestashopShopPageTypeProfile $profile, string $deviceClass): int
    {
        $stored = 0;

        $rules = PrestashopShopPageTypeAssetRule::query()
            ->where('profile_id', $profile->id)
            ->where('device_class', $deviceClass)
            ->where('asset_type', 'font')
            ->get();

        foreach ($rules as $rule) {
            $action = (string) $rule->effective_action;

            if (! in_array($action, ['self_host', 'self_host_preload', 'set_font_display_swap'], true)) {
                $this->clearSelfHostedAsset($rule);
                continue;
            }

            if (in_array($action, ['self_host', 'self_host_preload'], true) && ! $this->isGoogleFontsStylesheet((string) ($rule->asset_url ?? ''))) {
                $this->clearSelfHostedAsset($rule);
                continue;
            }

            $existingPublicPath = trim((string) ($rule->font_css_public_path ?? ''));
            if (
                (string) ($rule->font_asset_status ?? '') === 'ready'
                && $existingPublicPath !== ''
                && File::exists(public_path($existingPublicPath))
            ) {
                $stored++;
                continue;
            }

            try {
                if ($action === 'set_font_display_swap') {
                    $this->storeFontDisplaySwapStylesheet($profile, $deviceClass, $rule);
                } else {
                    $this->storeSelfHostedStylesheet($profile, $deviceClass, $rule);
                }
                $stored++;
            } catch (\Throwable $exception) {
                $rule->forceFill([
                    'font_asset_status' => 'failed',
                    'notes' => $exception->getMessage(),
                ])->save();
            }
        }

        return $stored;
    }

    public function clearSelfHostedAsset(PrestashopShopPageTypeAssetRule $rule): void
    {
        $relativeDirectory = $this->buildRuleDirectory($rule);
        if ($relativeDirectory !== '') {
            File::deleteDirectory(public_path($relativeDirectory));
        }

        $rule->forceFill([
            'font_asset_status' => null,
            'font_css_public_path' => null,
            'font_css_public_url' => null,
            'font_css_bytes' => null,
            'font_css_sha256' => null,
            'font_meta_json' => null,
            'last_font_built_at' => null,
        ])->save();
    }

    private function storeSelfHostedStylesheet(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass,
        PrestashopShopPageTypeAssetRule $rule
    ): void {
        $sourceUrl = $this->ensureDisplaySwap((string) $rule->asset_url);
        $ruleDirectory = $this->buildRuleDirectory($rule);
        $absoluteDirectory = public_path($ruleDirectory);
        File::deleteDirectory($absoluteDirectory);
        File::ensureDirectoryExists($absoluteDirectory);

        $cssResponse = Http::timeout(30)
            ->withoutVerifying()
            ->withHeaders([
                'User-Agent' => $this->fontUserAgent($deviceClass),
                'Accept' => 'text/css,*/*;q=0.1',
            ])
            ->get($sourceUrl);

        if (! $cssResponse->successful()) {
            throw new \RuntimeException(sprintf(
                'Failed to fetch Google Fonts stylesheet (%d).',
                $cssResponse->status()
            ));
        }

        $css = trim((string) $cssResponse->body());
        if ($css === '') {
            throw new \RuntimeException('Fetched Google Fonts stylesheet is empty.');
        }

        $fontUrls = $this->extractFontUrls($css, $sourceUrl);
        $publicFontUrls = [];

        foreach ($fontUrls as $index => $fontUrl) {
            $fontResponse = Http::timeout(45)
                ->withHeaders([
                    'User-Agent' => $this->fontUserAgent($deviceClass),
                ])
                ->get($fontUrl);

            if (! $fontResponse->successful()) {
                continue;
            }

            $extension = $this->detectFontExtension($fontUrl, (string) $fontResponse->header('Content-Type', ''));
            $fontPath = sprintf(
                '%s/font-%02d-%s.%s',
                $ruleDirectory,
                $index + 1,
                substr(hash('sha256', $fontUrl . $fontResponse->body()), 0, 12),
                $extension
            );

            File::ensureDirectoryExists(dirname(public_path($fontPath)));
            File::put(public_path($fontPath), $fontResponse->body());
            $publicFontUrls[$fontUrl] = $this->buildPublicUrl($fontPath);
        }

        $rewrittenCss = strtr($css, $publicFontUrls);
        $cssSha = hash('sha256', $rewrittenCss);
        $cssPath = sprintf(
            '%s/stylesheet-%s.font.css',
            $ruleDirectory,
            substr($cssSha, 0, 16)
        );
        File::put(public_path($cssPath), $rewrittenCss);

        $preloadUrls = array_values(array_slice(array_filter(
            array_values($publicFontUrls),
            static fn (string $url): bool => str_ends_with(parse_url($url, PHP_URL_PATH) ?? '', '.woff2')
        ), 0, 4));

        $rule->forceFill([
            'font_asset_status' => 'ready',
            'font_css_public_path' => $cssPath,
            'font_css_public_url' => $this->buildPublicUrl($cssPath),
            'font_css_bytes' => strlen($rewrittenCss),
            'font_css_sha256' => $cssSha,
            'font_meta_json' => [
                'source_url' => $sourceUrl,
                'font_urls' => array_values($publicFontUrls),
                'preload_urls' => $preloadUrls,
                'font_count' => count($publicFontUrls),
            ],
            'last_font_built_at' => now(),
        ])->save();
    }

    private function storeFontDisplaySwapStylesheet(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass,
        PrestashopShopPageTypeAssetRule $rule
    ): void {
        $sourceUrl = trim((string) $rule->asset_url);
        if ($sourceUrl === '') {
            throw new \RuntimeException('Source font stylesheet URL is empty.');
        }

        $ruleDirectory = $this->buildRuleDirectory($rule);
        $absoluteDirectory = public_path($ruleDirectory);
        File::deleteDirectory($absoluteDirectory);
        File::ensureDirectoryExists($absoluteDirectory);

        $cssResponse = Http::timeout(30)
            ->withoutVerifying()
            ->withHeaders([
                'User-Agent' => $this->fontUserAgent($deviceClass),
                'Accept' => 'text/css,*/*;q=0.1',
            ])
            ->get($sourceUrl);

        if (! $cssResponse->successful()) {
            throw new \RuntimeException(sprintf(
                'Failed to fetch local font stylesheet (%d).',
                $cssResponse->status()
            ));
        }

        $css = trim((string) $cssResponse->body());
        if ($css === '') {
            throw new \RuntimeException('Fetched local font stylesheet is empty.');
        }

        $rewrittenCss = $this->injectFontDisplaySwap(
            $this->rewriteRelativeUrlsToAbsolute($css, $sourceUrl)
        );
        $cssSha = hash('sha256', $rewrittenCss);
        $cssPath = sprintf(
            '%s/stylesheet-%s.font.css',
            $ruleDirectory,
            substr($cssSha, 0, 16)
        );
        File::put(public_path($cssPath), $rewrittenCss);

        preg_match_all('/@font-face\b/i', $rewrittenCss, $matches);

        $rule->forceFill([
            'font_asset_status' => 'ready',
            'font_css_public_path' => $cssPath,
            'font_css_public_url' => $this->buildPublicUrl($cssPath),
            'font_css_bytes' => strlen($rewrittenCss),
            'font_css_sha256' => $cssSha,
            'font_meta_json' => [
                'source_url' => $sourceUrl,
                'generated_mode' => 'set_font_display_swap',
                'preload_urls' => [],
                'font_count' => count($matches[0] ?? []),
            ],
            'last_font_built_at' => now(),
        ])->save();
    }

    private function buildRuleDirectory(PrestashopShopPageTypeAssetRule $rule): string
    {
        $profileId = (string) $rule->profile_id;
        $device = strtolower(trim((string) $rule->device_class)) === 'mobile' ? 'mobile' : 'desktop';
        $assetKey = hash('sha256', (string) ($rule->asset_url ?? ''));

        if ($profileId === '' || $assetKey === '') {
            return '';
        }

        return sprintf('prestaload-assets/page-type-profiles/%s/%s/fonts/%s', $profileId, $device, $assetKey);
    }

    private function buildPublicUrl(string $relativePath): string
    {
        $baseUrl = trim((string) config('services.prestaload_assets.base_url', ''));
        if ($baseUrl === '') {
            $baseUrl = trim((string) config('app.url', 'http://localhost'));
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($relativePath, '/');
    }

    private function isGoogleFontsStylesheet(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return $host === 'fonts.googleapis.com';
    }

    private function ensureDisplaySwap(string $url): string
    {
        $parts = parse_url($url);
        if (! is_array($parts) || strtolower((string) ($parts['host'] ?? '')) !== 'fonts.googleapis.com') {
            return $url;
        }

        parse_str((string) ($parts['query'] ?? ''), $query);
        $query['display'] = 'swap';
        $parts['query'] = http_build_query($query);

        return $this->unparseUrl($parts);
    }

    /**
     * @return list<string>
     */
    private function extractFontUrls(string $css, string $baseUrl): array
    {
        if (! preg_match_all('/url\((["\']?)(.*?)\1\)/i', $css, $matches, \PREG_SET_ORDER)) {
            return [];
        }

        $urls = [];
        foreach ($matches as $match) {
            $url = trim((string) ($match[2] ?? ''));
            if ($url === '' || str_starts_with($url, 'data:')) {
                continue;
            }

            $urls[] = $this->resolveUrl($baseUrl, $url);
        }

        return array_values(array_unique(array_filter($urls)));
    }

    private function resolveUrl(string $baseUrl, string $url): string
    {
        if (str_starts_with($url, '//')) {
            return 'https:' . $url;
        }

        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        $parts = parse_url($baseUrl);
        if (! is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return $url;
        }

        $path = (string) ($parts['path'] ?? '/');
        $directory = rtrim(str_replace('\\', '/', dirname($path)), '/');

        return $parts['scheme'] . '://' . $parts['host']
            . ($parts['port'] ?? null ? ':' . $parts['port'] : '')
            . ($url[0] === '/' ? $url : $directory . '/' . ltrim($url, '/'));
    }

    private function rewriteRelativeUrlsToAbsolute(string $css, string $sourceUrl): string
    {
        return preg_replace_callback('/url\((["\']?)(.*?)\1\)/i', function (array $matches) use ($sourceUrl): string {
            $quote = (string) ($matches[1] ?? '');
            $url = trim((string) ($matches[2] ?? ''));

            if ($url === '' || str_starts_with($url, 'data:') || preg_match('#^(https?:)?//#i', $url)) {
                return $matches[0];
            }

            $resolved = $this->resolveUrl($sourceUrl, $url);

            return 'url(' . $quote . $resolved . $quote . ')';
        }, $css) ?? $css;
    }

    private function injectFontDisplaySwap(string $css): string
    {
        return preg_replace_callback('/@font-face\s*\{.*?\}/is', static function (array $matches): string {
            $block = (string) ($matches[0] ?? '');
            if ($block === '') {
                return $block;
            }

            if (preg_match('/font-display\s*:/i', $block)) {
                return preg_replace('/font-display\s*:\s*[^;]+;/i', 'font-display: swap;', $block, 1) ?? $block;
            }

            return preg_replace('/\}\s*$/', "  font-display: swap;\n}", $block, 1) ?? $block;
        }, $css) ?? $css;
    }

    private function detectFontExtension(string $url, string $contentType): string
    {
        $path = strtolower((string) parse_url($url, PHP_URL_PATH));
        foreach (['woff2', 'woff', 'ttf', 'otf', 'eot'] as $extension) {
            if (str_ends_with($path, '.' . $extension)) {
                return $extension;
            }
        }

        return match (true) {
            str_contains(strtolower($contentType), 'woff2') => 'woff2',
            str_contains(strtolower($contentType), 'woff') => 'woff',
            str_contains(strtolower($contentType), 'truetype') => 'ttf',
            str_contains(strtolower($contentType), 'opentype') => 'otf',
            default => 'woff2',
        };
    }

    private function fontUserAgent(string $deviceClass): string
    {
        if (strtolower(trim($deviceClass)) === 'mobile') {
            return 'Mozilla/5.0 (Linux; Android 12; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Mobile Safari/537.36';
        }

        return 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36';
    }

    /**
     * @param  array<string, mixed>  $parts
     */
    private function unparseUrl(array $parts): string
    {
        $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
        $host = (string) ($parts['host'] ?? '');
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $path = (string) ($parts['path'] ?? '');
        $query = isset($parts['query']) && $parts['query'] !== '' ? '?' . $parts['query'] : '';
        $fragment = isset($parts['fragment']) && $parts['fragment'] !== '' ? '#' . $parts['fragment'] : '';

        return $scheme . $host . $port . $path . $query . $fragment;
    }
}
