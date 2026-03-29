<?php

namespace App\Services\Performance;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class PageSpeedService
{
    private const ENDPOINT = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
    private const AUDIT_IDS = [
        'unused-javascript',
        'unused-css-rules',
        'unminified-css',
        'unminified-javascript',
        'font-display',
        'font-display-insight',
        'network-requests',
        'render-blocking-resources',
        'network-dependency-tree-insight',
        'resource-summary',
        'total-byte-weight',
    ];

    public function __construct(
        private readonly ?string $apiKey = null,
    ) {
    }

    /**
     * @return array{
     *     mobile: array{score: int|null, strategy: string, fetched_at: string|null},
     *     desktop: array{score: int|null, strategy: string, fetched_at: string|null}
     * }
     */
    public function scanScores(string $url): array
    {
        $report = $this->scanReport($url);

        return [
            'mobile' => [
                'score' => $report['mobile']['score'] ?? null,
                'strategy' => 'mobile',
                'fetched_at' => $report['mobile']['fetched_at'] ?? null,
            ],
            'desktop' => [
                'score' => $report['desktop']['score'] ?? null,
                'strategy' => 'desktop',
                'fetched_at' => $report['desktop']['fetched_at'] ?? null,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function scanReport(string $url): array
    {
        $apiKey = $this->apiKey ?? (string) config('services.pagespeed.key');

        if ($apiKey === '') {
            throw new \RuntimeException('PageSpeed API key is not configured.');
        }

        return [
            'provider' => 'pagespeed',
            'url' => $url,
            'scanned_at' => now()->toIso8601String(),
            'page_metrics' => [],
            'mobile' => $this->scanStrategyReport($url, 'mobile', $apiKey),
            'desktop' => $this->scanStrategyReport($url, 'desktop', $apiKey),
        ];
    }

    /**
     * @return array{score: int|null, strategy: string, fetched_at: string|null}
     */
    private function scanStrategyReport(string $url, string $strategy, string $apiKey): array
    {
        $response = Http::timeout(120)
            ->acceptJson()
            ->get(self::ENDPOINT, [
                'url' => $url,
                'strategy' => $strategy,
                'category' => 'performance',
                'key' => $apiKey,
            ]);

        $this->throwIfFailed($response, $strategy);

        $payload = $response->json();
        $score = $payload['lighthouseResult']['categories']['performance']['score'] ?? null;
        $scorePercent = is_numeric($score) ? (int) round(((float) $score) * 100) : null;

        $lighthouseResult = is_array($payload['lighthouseResult'] ?? null) ? $payload['lighthouseResult'] : [];
        $audits = is_array($lighthouseResult['audits'] ?? null) ? $lighthouseResult['audits'] : [];

        return [
            'score' => $scorePercent,
            'strategy' => $strategy,
            'fetched_at' => $payload['analysisUTCTimestamp'] ?? null,
            'categories' => [
                'performance' => [
                    'title' => 'Performance',
                    'score' => $scorePercent,
                ],
            ],
            'requested_audits' => $this->extractRequestedAudits($audits),
            'metrics' => $this->extractMetrics($audits),
            'artifacts' => $this->extractArtifacts($audits),
        ];
    }

    /**
     * @param  array<string, mixed>  $audits
     * @return array<string, mixed>
     */
    private function extractRequestedAudits(array $audits): array
    {
        $results = [];

        foreach (self::AUDIT_IDS as $auditId) {
            if (! isset($audits[$auditId]) || ! is_array($audits[$auditId])) {
                continue;
            }

            $audit = $audits[$auditId];
            $results[$auditId] = [
                'id' => $auditId,
                'title' => $audit['title'] ?? $auditId,
                'score' => $audit['score'] ?? null,
                'score_display_mode' => $audit['scoreDisplayMode'] ?? null,
                'display_value' => $audit['displayValue'] ?? null,
                'numeric_value' => $audit['numericValue'] ?? null,
                'numeric_unit' => $audit['numericUnit'] ?? null,
                'details' => $audit['details'] ?? null,
            ];
        }

        return $results;
    }

    /**
     * @param  array<string, mixed>  $audits
     * @return array<string, mixed>
     */
    private function extractMetrics(array $audits): array
    {
        $metricIds = [
            'first-contentful-paint',
            'largest-contentful-paint',
            'speed-index',
            'interactive',
            'total-blocking-time',
            'cumulative-layout-shift',
            'max-potential-fid',
        ];

        $metrics = [];

        foreach ($metricIds as $metricId) {
            if (! isset($audits[$metricId]) || ! is_array($audits[$metricId])) {
                continue;
            }

            $audit = $audits[$metricId];
            $metrics[$metricId] = [
                'title' => $audit['title'] ?? $metricId,
                'display_value' => $audit['displayValue'] ?? null,
                'numeric_value' => $audit['numericValue'] ?? null,
                'numeric_unit' => $audit['numericUnit'] ?? null,
                'score' => $audit['score'] ?? null,
            ];
        }

        return $metrics;
    }

    /**
     * @param  array<string, mixed>  $audits
     * @return array<string, mixed>
     */
    private function extractArtifacts(array $audits): array
    {
        $artifactIds = [
            'resource-summary',
            'total-byte-weight',
            'render-blocking-resources',
            'network-dependency-tree-insight',
            'network-requests',
        ];

        $artifacts = [];

        foreach ($artifactIds as $artifactId) {
            if (! isset($audits[$artifactId]) || ! is_array($audits[$artifactId])) {
                continue;
            }

            $audit = $audits[$artifactId];
            $artifacts[$artifactId] = [
                'title' => $audit['title'] ?? $artifactId,
                'display_value' => $audit['displayValue'] ?? null,
                'numeric_value' => $audit['numericValue'] ?? null,
                'details' => $audit['details'] ?? null,
            ];
        }

        return $artifacts;
    }

    private function throwIfFailed(Response $response, string $strategy): void
    {
        if ($response->successful()) {
            return;
        }

        $message = sprintf(
            'PageSpeed %s scan failed with HTTP %d.',
            $strategy,
            $response->status()
        );

        throw new \RuntimeException($message);
    }
}
