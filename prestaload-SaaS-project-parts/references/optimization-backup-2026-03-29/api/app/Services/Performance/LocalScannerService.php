<?php

namespace App\Services\Performance;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocalScannerService
{
    public function __construct(
        private readonly ?string $baseUrl = null,
        private readonly ?int $timeout = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function scanReport(string $url): array
    {
        $baseUrl = rtrim($this->baseUrl ?? (string) config('services.local_scanner.base_url'), '/');
        if ($baseUrl === '') {
            throw new \RuntimeException('Local scanner base URL is not configured.');
        }

        $timeout = max(30, (int) ($this->timeout ?? config('services.local_scanner.timeout', 300)));

        $response = Http::timeout($timeout)
            ->acceptJson()
            ->post($baseUrl . '/scan', [
                'url' => $url,
            ]);

        $body = trim((string) $response->body());
        $bodySnippet = $body !== '' ? mb_substr($body, 0, 1500) : null;

        if (! $response->successful()) {
            Log::error('prestaload.local_scanner.failed', [
                'scanner_base_url' => $baseUrl,
                'url' => $url,
                'status' => $response->status(),
                'response_body' => $bodySnippet,
            ]);

            throw new \RuntimeException(sprintf(
                'Local scanner scan failed with HTTP %d.%s',
                $response->status(),
                $bodySnippet !== null ? (' Response: ' . mb_substr($bodySnippet, 0, 500)) : ''
            ));
        }

        $payload = $response->json();
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];
        $mobile = is_array($data['mobile'] ?? null) ? $data['mobile'] : [];
        $desktop = is_array($data['desktop'] ?? null) ? $data['desktop'] : [];

        $report = [
            'provider' => 'local-scanner',
            'url' => $url,
            'scanned_at' => (string) ($data['scanned_at'] ?? now()->toIso8601String()),
            'page_metrics' => is_array($data['page_metrics'] ?? null) ? $data['page_metrics'] : [],
            'mobile' => [
                'score' => $mobile['categories']['performance']['score'] ?? null,
                'strategy' => 'mobile',
                'fetched_at' => (string) ($data['scanned_at'] ?? now()->toIso8601String()),
                'categories' => is_array($mobile['categories'] ?? null) ? $mobile['categories'] : [],
                'requested_audits' => is_array($mobile['requested_audits'] ?? null) ? $mobile['requested_audits'] : [],
                'metrics' => is_array($mobile['metrics'] ?? null) ? $mobile['metrics'] : [],
                'artifacts' => is_array($mobile['artifacts'] ?? null) ? $mobile['artifacts'] : [],
            ],
            'desktop' => [
                'score' => $desktop['categories']['performance']['score'] ?? null,
                'strategy' => 'desktop',
                'fetched_at' => (string) ($data['scanned_at'] ?? now()->toIso8601String()),
                'categories' => is_array($desktop['categories'] ?? null) ? $desktop['categories'] : [],
                'requested_audits' => is_array($desktop['requested_audits'] ?? null) ? $desktop['requested_audits'] : [],
                'metrics' => is_array($desktop['metrics'] ?? null) ? $desktop['metrics'] : [],
                'artifacts' => is_array($desktop['artifacts'] ?? null) ? $desktop['artifacts'] : [],
            ],
        ];

        Log::info('prestaload.local_scanner.succeeded', [
            'scanner_base_url' => $baseUrl,
            'url' => $url,
            'status' => $response->status(),
            'mobile_score' => $report['mobile']['score'] ?? null,
            'desktop_score' => $report['desktop']['score'] ?? null,
            'response_body' => $bodySnippet,
        ]);

        return $report;
    }

    /**
     * @return array<string, mixed>
     */
    public function scanFontUsageReport(string $url): array
    {
        $baseUrl = rtrim($this->baseUrl ?? (string) config('services.local_scanner.base_url'), '/');
        if ($baseUrl === '') {
            throw new \RuntimeException('Local scanner base URL is not configured.');
        }

        $timeout = max(30, (int) ($this->timeout ?? config('services.local_scanner.timeout', 300)));

        $response = Http::timeout($timeout)
            ->acceptJson()
            ->post($baseUrl . '/font-usage', [
                'url' => $url,
                'device' => 'both',
            ]);

        $body = trim((string) $response->body());
        $bodySnippet = $body !== '' ? mb_substr($body, 0, 1500) : null;

        if (! $response->successful()) {
            Log::error('prestaload.local_scanner.font_usage.failed', [
                'scanner_base_url' => $baseUrl,
                'url' => $url,
                'status' => $response->status(),
                'response_body' => $bodySnippet,
            ]);

            throw new \RuntimeException(sprintf(
                'Local scanner font usage scan failed with HTTP %d.%s',
                $response->status(),
                $bodySnippet !== null ? (' Response: ' . mb_substr($bodySnippet, 0, 500)) : ''
            ));
        }

        $payload = $response->json();
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];

        $report = [
            'provider' => 'local-scanner',
            'url' => $url,
            'scanned_at' => now()->toIso8601String(),
            'mobile' => $this->normalizeFontUsageVariant('mobile', is_array($data['mobile'] ?? null) ? $data['mobile'] : []),
            'desktop' => $this->normalizeFontUsageVariant('desktop', is_array($data['desktop'] ?? null) ? $data['desktop'] : []),
        ];

        if (($report['mobile']['scanned_at'] ?? null) !== null) {
            $report['scanned_at'] = (string) $report['mobile']['scanned_at'];
        } elseif (($report['desktop']['scanned_at'] ?? null) !== null) {
            $report['scanned_at'] = (string) $report['desktop']['scanned_at'];
        }

        Log::info('prestaload.local_scanner.font_usage.succeeded', [
            'scanner_base_url' => $baseUrl,
            'url' => $url,
            'status' => $response->status(),
            'mobile_used_families' => count($report['mobile']['used_font_families'] ?? []),
            'desktop_used_families' => count($report['desktop']['used_font_families'] ?? []),
            'response_body' => $bodySnippet,
        ]);

        return $report;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizeFontUsageVariant(string $device, array $payload): array
    {
        return [
            'device' => $device,
            'scanned_at' => isset($payload['scanned_at']) ? (string) $payload['scanned_at'] : null,
            'declared_font_families' => $this->normalizeStringList($payload['declared_font_families'] ?? []),
            'used_font_families' => $this->normalizeStringList($payload['used_font_families'] ?? []),
            'used_above_the_fold' => $this->normalizeStringList($payload['used_above_the_fold'] ?? []),
            'used_weights' => $this->normalizeStringList($payload['used_weights'] ?? []),
            'used_styles' => $this->normalizeStringList($payload['used_styles'] ?? []),
            'unused_declared_families' => $this->normalizeStringList($payload['unused_declared_families'] ?? []),
            'google_fonts_stylesheets' => $this->normalizeStringList($payload['google_fonts_stylesheets'] ?? []),
            'duplicate_icon_font_stylesheets' => $this->normalizeDuplicateIconStylesheets($payload['duplicate_icon_font_stylesheets'] ?? []),
            'font_face_rule_count' => (int) ($payload['font_face_rule_count'] ?? 0),
            'viewport' => is_array($payload['viewport'] ?? null) ? $payload['viewport'] : [],
            'element_count' => isset($payload['element_count']) ? (int) $payload['element_count'] : null,
            'viewport_element_count' => isset($payload['viewport_element_count']) ? (int) $payload['viewport_element_count'] : null,
            'scanned_element_count' => isset($payload['scanned_element_count']) ? (int) $payload['scanned_element_count'] : null,
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
    private function normalizeDuplicateIconStylesheets(mixed $value): array
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
}
