<?php

namespace App\Services\Optimization;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class CssAnalysisService
{
    /**
     * @return array<string, mixed>
     */
    public function analyze(
        string $url,
        string $deviceClass = 'desktop',
        int $timeoutMs = 45000,
        bool $includeCriticalCss = true
    ): array {
        $endpoint = rtrim((string) config('services.optimizer.base_url', 'http://127.0.0.1:8096'), '/') . '/analyze-css';
        $normalizedDeviceClass = trim(strtolower($deviceClass)) === 'mobile' ? 'mobile' : 'desktop';
        $requestTimeoutSeconds = $this->resolveOptimizerRequestTimeoutSeconds($timeoutMs);

        $response = Http::timeout($requestTimeoutSeconds)
            ->acceptJson()
            ->post($endpoint, [
                'url' => $url,
                'device_class' => $normalizedDeviceClass,
                'timeout_ms' => $timeoutMs,
                'wait_until' => 'load',
                'include_critical_css' => false,
            ]);

        $body = (string) $response->body();
        $json = $response->json();

        if (! $response->successful()) {
            throw new RuntimeException('Optimizer worker request failed with status ' . $response->status() . ': ' . mb_substr(trim($body), 0, 500));
        }

        if (! is_array($json) || ! ($json['success'] ?? false) || ! is_array($json['data'] ?? null)) {
            throw new RuntimeException('Optimizer worker returned an invalid payload.');
        }

        $data = $json['data'];

        if (! $includeCriticalCss) {
            return $data;
        }

        $criticalCss = $this->fetchScannerCriticalCss($url, $normalizedDeviceClass);

        $data['critical_css'] = $criticalCss['critical_css'];
        $data['critical_css_bytes'] = $criticalCss['critical_css_bytes'];
        $data['critical_css_sha256'] = $criticalCss['critical_css_sha256'];
        $data['critical_css_mode'] = $criticalCss['critical_css_mode'];
        $data['critical_css_capped'] = $criticalCss['critical_css_capped'];
        $data['critical_css_max_bytes'] = $criticalCss['critical_css_max_bytes'];
        $data['original_critical_css_bytes'] = $criticalCss['original_critical_css_bytes'];
        $data['simplified_critical_css_bytes'] = $criticalCss['simplified_critical_css_bytes'];

        return $data;
    }

    private function resolveOptimizerRequestTimeoutSeconds(int $timeoutMs): int
    {
        $configuredTimeout = max(120, (int) config('services.optimizer.timeout', 300));
        $requestedTimeout = max(45, (int) ceil($timeoutMs / 1000));

        return max($configuredTimeout, $requestedTimeout + 240);
    }

    /**
     * @return array{
     *   critical_css: string,
     *   critical_css_bytes: int,
     *   critical_css_sha256: ?string,
     *   critical_css_mode: string,
     *   critical_css_capped: bool,
     *   critical_css_max_bytes: ?int,
     *   original_critical_css_bytes: ?int,
     *   simplified_critical_css_bytes: ?int
     * }
     */
    private function fetchScannerCriticalCss(string $url, string $deviceClass): array
    {
        $endpoint = rtrim((string) config('services.local_scanner.base_url', 'http://127.0.0.1:8093'), '/') . '/critical-css';

        $response = Http::timeout((int) config('services.local_scanner.timeout', 300))
            ->acceptJson()
            ->post($endpoint, [
                'url' => $url,
                'device' => $deviceClass === 'desktop' ? 'desktop' : 'mobile',
            ]);

        $body = (string) $response->body();
        $json = $response->json();

        if (! $response->successful()) {
            throw new RuntimeException('Scanner critical CSS request failed with status ' . $response->status() . ': ' . mb_substr(trim($body), 0, 500));
        }

        if (! is_array($json) || ! ($json['success'] ?? false) || ! is_array($json['data'] ?? null)) {
            throw new RuntimeException('Scanner critical CSS returned an invalid payload.');
        }

        $data = $json['data'];
        $meta = is_array($data['meta'] ?? null) ? $data['meta'] : [];

        return [
            'critical_css' => trim((string) ($data['css_content'] ?? '')),
            'critical_css_bytes' => (int) ($data['css_size_bytes'] ?? 0),
            'critical_css_sha256' => ($data['css_hash'] ?? null) !== null ? (string) $data['css_hash'] : null,
            'critical_css_mode' => 'scanner_penthouse',
            'critical_css_capped' => (bool) ($meta['budget_reached'] ?? false),
            'critical_css_max_bytes' => isset($meta['max_css_bytes']) ? (int) $meta['max_css_bytes'] : null,
            'original_critical_css_bytes' => isset($meta['penthouse_raw_css_size_bytes']) ? (int) $meta['penthouse_raw_css_size_bytes'] : null,
            'simplified_critical_css_bytes' => isset($meta['stripped_css_size_bytes']) ? (int) $meta['stripped_css_size_bytes'] : null,
        ];
    }
}
