<?php

namespace App\Services\Optimization;

use App\Models\PrestashopShop;
use App\Models\PrestashopShopUrl;
use Illuminate\Support\Facades\Http;

class PageTypeSampleUrlCheckerService
{
    public function __construct(
        private readonly OptimizationUrlValidationService $optimizationUrlValidationService
    ) {
    }

    /**
     * @return array{
     *   reachable: bool,
     *   status_code: int|null,
     *   checked_url: string,
     *   reason: string|null
     * }
     */
    public function check(PrestashopShop $shop, PrestashopShopUrl $shopUrl): array
    {
        $store = $shop->prestashopStore;
        if (! $store) {
            return [
                'reachable' => false,
                'status_code' => null,
                'checked_url' => $shopUrl->url,
                'reason' => 'missing_store',
            ];
        }

        $trustedUrlValidation = $this->optimizationUrlValidationService->validateTrustedUrl($store, $shop, $shopUrl);
        if (! ($trustedUrlValidation['valid'] ?? false)) {
            return [
                'reachable' => false,
                'status_code' => null,
                'checked_url' => $shopUrl->url,
                'reason' => (string) ($trustedUrlValidation['reason'] ?? 'invalid_url'),
            ];
        }

        $checkedUrl = $this->appendPrestaLoadBypassParameter($shopUrl->url);

        try {
            $response = Http::timeout(20)
                ->acceptHtml()
                ->withOptions([
                    'verify' => $this->shouldVerifySsl($checkedUrl),
                    'allow_redirects' => [
                        'max' => 5,
                        'strict' => false,
                        'referer' => true,
                        'track_redirects' => true,
                    ],
                ])
                ->get($checkedUrl);

            return [
                'reachable' => $response->successful(),
                'status_code' => $response->status(),
                'checked_url' => $checkedUrl,
                'reason' => $response->successful() ? null : 'non_2xx_response',
            ];
        } catch (\Throwable) {
            return [
                'reachable' => false,
                'status_code' => null,
                'checked_url' => $checkedUrl,
                'reason' => 'request_failed',
            ];
        }
    }

    private function appendPrestaLoadBypassParameter(string $url): string
    {
        $parts = parse_url($url);
        if (! is_array($parts) || empty($parts['host'])) {
            return $url;
        }

        $queryParams = [];
        if (! empty($parts['query'])) {
            parse_str((string) $parts['query'], $queryParams);
        }

        $queryParams['WITHOUTPRESTALOAD'] = 'true';
        $query = http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);

        $scheme = isset($parts['scheme']) ? ((string) $parts['scheme']) . '://' : '';
        $authority = '';
        if (isset($parts['user'])) {
            $authority .= (string) $parts['user'];
            if (isset($parts['pass'])) {
                $authority .= ':' . (string) $parts['pass'];
            }
            $authority .= '@';
        }

        $authority .= (string) $parts['host'];

        if (isset($parts['port'])) {
            $authority .= ':' . (string) $parts['port'];
        }

        $path = isset($parts['path']) ? (string) $parts['path'] : '/';
        $fragment = isset($parts['fragment']) ? '#' . (string) $parts['fragment'] : '';

        return $scheme . $authority . $path . ($query !== '' ? '?' . $query : '') . $fragment;
    }

    private function shouldVerifySsl(string $endpoint): bool
    {
        $host = parse_url($endpoint, PHP_URL_HOST);
        if (! is_string($host) || $host === '') {
            return true;
        }

        if ($host === 'localhost' || $host === '127.0.0.1' || str_ends_with($host, '.local.test') || str_ends_with($host, '.test')) {
            return false;
        }

        return true;
    }
}
