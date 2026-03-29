<?php

namespace App\Services\Optimization;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class BrowserRenderService
{
    /**
     * @return array<string, mixed>
     */
    public function render(
        string $url,
        string $deviceClass = 'desktop',
        bool $minifyInlineCss = true,
        bool $minifyInlineJs = true,
        bool $compressHtml = true
    ): array
    {
        $endpoint = rtrim((string) env('BROWSER_WORKER_URL', 'http://127.0.0.1:8095'), '/') . '/render';
        $deviceProfile = $this->resolveDeviceProfile($deviceClass);
        $payload = [
            'url' => $url,
            'wait_until' => 'domcontentloaded',
            'device_class' => $deviceProfile['device_class'],
            'viewport' => $deviceProfile['viewport'],
            'user_agent' => $deviceProfile['user_agent'],
            'is_mobile' => $deviceProfile['is_mobile'],
            'has_touch' => $deviceProfile['has_touch'],
            'minify_inline_css' => $minifyInlineCss,
            'minify_inline_js' => $minifyInlineJs,
            'compress_html' => $compressHtml,
        ];

        $response = Http::timeout((int) env('BROWSER_WORKER_TIMEOUT', 90))
            ->acceptJson()
            ->post($endpoint, $payload);

        $body = (string) $response->body();
        $json = $response->json();

        if (! $response->successful()) {
            throw new RuntimeException('Browser worker request failed with status ' . $response->status() . ': ' . mb_substr(trim($body), 0, 500));
        }

        if (! is_array($json) || ! ($json['success'] ?? false) || ! is_array($json['data'] ?? null)) {
            throw new RuntimeException('Browser worker returned an invalid payload.');
        }

        return $json['data'];
    }

    /**
     * @return array<string, mixed>
     */
    public function validateOptimizedHtml(string $url, string $html, string $deviceClass = 'desktop'): array
    {
        $endpoint = rtrim((string) env('BROWSER_WORKER_URL', 'http://127.0.0.1:8095'), '/') . '/validate-render';
        $deviceProfile = $this->resolveDeviceProfile($deviceClass);
        $workerTimeout = (int) env('BROWSER_WORKER_TIMEOUT', 90);
        $payload = [
            'url' => $url,
            'base_url' => $url,
            'html' => $html,
            'wait_until' => 'domcontentloaded',
            'timeout_ms' => 45000,
            'device_class' => $deviceProfile['device_class'],
            'viewport' => $deviceProfile['viewport'],
            'user_agent' => $deviceProfile['user_agent'],
            'is_mobile' => $deviceProfile['is_mobile'],
            'has_touch' => $deviceProfile['has_touch'],
        ];

        $response = Http::timeout(max(150, $workerTimeout * 2))
            ->acceptJson()
            ->post($endpoint, $payload);

        $body = (string) $response->body();
        $json = $response->json();

        if (! $response->successful()) {
            throw new RuntimeException('Browser validation request failed with status ' . $response->status() . ': ' . mb_substr(trim($body), 0, 500));
        }

        if (! is_array($json) || ! ($json['success'] ?? false) || ! is_array($json['data'] ?? null)) {
            throw new RuntimeException('Browser validation returned an invalid payload.');
        }

        return $json['data'];
    }
    /**
     * @return array{device_class: string, viewport: array{width: int, height: int}, user_agent: string, is_mobile: bool, has_touch: bool}
     */
    private function resolveDeviceProfile(string $deviceClass): array
    {
        $normalized = strtolower(trim($deviceClass));

        if ($normalized === 'mobile') {
            return [
                'device_class' => 'mobile',
                'viewport' => [
                    'width' => 390,
                    'height' => 844,
                ],
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
                'is_mobile' => true,
                'has_touch' => true,
            ];
        }

        return [
            'device_class' => 'desktop',
            'viewport' => [
                'width' => 1440,
                'height' => 1600,
            ],
            'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',
            'is_mobile' => false,
            'has_touch' => false,
        ];
    }
}
