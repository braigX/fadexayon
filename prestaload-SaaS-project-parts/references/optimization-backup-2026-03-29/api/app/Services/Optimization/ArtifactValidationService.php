<?php

namespace App\Services\Optimization;

use RuntimeException;

class ArtifactValidationService
{
    /**
     * @param  array<string, mixed>  $rendered
     * @return array{
     *   valid: bool,
     *   failed_checks: array<int, string>,
     *   checks: array<string, mixed>,
     *   summary: array<string, mixed>,
     *   error_message: string|null
     * }
     */
    public function validate(
        array $rendered,
        string $optimizedHtml,
        ?string $expectedCriticalCss = null,
        ?string $expectedUsedCssUrl = null,
        ?array $visualValidation = null
    ): array
    {
        $statusCode = $this->normalizeNullableInt($rendered['status_code'] ?? null);
        $rawHtml = (string) ($rendered['html'] ?? '');
        $rawTitle = $this->extractTitle($rawHtml);
        $optimizedTitle = $this->extractTitle($optimizedHtml);
        $rawCanonical = $this->extractCanonical($rawHtml);
        $optimizedCanonical = $this->extractCanonical($optimizedHtml);
        $normalizedRawTitle = $this->normalizeComparableText($rawTitle);
        $normalizedOptimizedTitle = $this->normalizeComparableText($optimizedTitle);
        $normalizedRawCanonical = $this->normalizeComparableText($rawCanonical);
        $normalizedOptimizedCanonical = $this->normalizeComparableText($optimizedCanonical);
        $hasGeneratorMeta = str_contains($optimizedHtml, 'id="optimized_by_prestaload"');
        $injectedCriticalCss = $this->extractCriticalCss($optimizedHtml);
        $injectedUsedCssUrl = $this->extractUsedCssUrl($optimizedHtml);
        $normalizedExpectedCriticalCss = $this->normalizeCriticalCss($expectedCriticalCss);
        $normalizedInjectedCriticalCss = $this->normalizeCriticalCss($injectedCriticalCss);
        $normalizedExpectedUsedCssUrl = $this->normalizeComparableText($expectedUsedCssUrl);
        $normalizedInjectedUsedCssUrl = $this->normalizeComparableText($injectedUsedCssUrl);
        $requiresCriticalCss = $normalizedExpectedCriticalCss !== null;
        $hasCriticalCss = $normalizedInjectedCriticalCss !== null;
        $requiresUsedCss = $normalizedExpectedUsedCssUrl !== null;
        $hasUsedCss = $normalizedInjectedUsedCssUrl !== null;
        $consoleErrorCount = $this->countConsoleErrors($rendered['console_messages'] ?? null);
        $visualDiffRatio = $this->normalizeNullableFloat($visualValidation['diff_ratio'] ?? null);
        $visualDiffThreshold = 0.02;

        $checks = [
            'http_status_ok' => $statusCode !== null && $statusCode >= 200 && $statusCode < 300,
            'optimized_html_present' => trim($optimizedHtml) !== '',
            'html_document_present' => stripos($optimizedHtml, '</html>') !== false,
            'generator_meta_present' => $hasGeneratorMeta,
            'critical_css_present' => ! $requiresCriticalCss || $hasCriticalCss,
            'critical_css_non_empty' => ! $requiresCriticalCss || ($normalizedInjectedCriticalCss !== null && $normalizedInjectedCriticalCss !== ''),
            'critical_css_matches_artifact' => ! $requiresCriticalCss || $normalizedExpectedCriticalCss === $normalizedInjectedCriticalCss,
            'used_css_present' => ! $requiresUsedCss || $hasUsedCss,
            'used_css_matches_artifact' => ! $requiresUsedCss || $normalizedExpectedUsedCssUrl === $normalizedInjectedUsedCssUrl,
            'title_preserved' => $normalizedRawTitle === null || $normalizedRawTitle === $normalizedOptimizedTitle,
            'canonical_preserved' => $normalizedRawCanonical === null || $normalizedRawCanonical === $normalizedOptimizedCanonical,
            'visual_diff_within_threshold' => $visualDiffRatio === null || $visualDiffRatio <= $visualDiffThreshold,
        ];

        $failedChecks = array_keys(array_filter($checks, static fn ($passed): bool => $passed === false));
        $errorMessage = $failedChecks !== []
            ? 'Artifact validation failed: ' . implode(', ', $failedChecks)
            : null;

        return [
            'valid' => $failedChecks === [],
            'failed_checks' => $failedChecks,
            'checks' => $checks,
            'summary' => [
                'status_code' => $statusCode,
                'raw_title' => $rawTitle,
                'optimized_title' => $optimizedTitle,
                'normalized_raw_title' => $normalizedRawTitle,
                'normalized_optimized_title' => $normalizedOptimizedTitle,
                'raw_canonical' => $rawCanonical,
                'optimized_canonical' => $optimizedCanonical,
                'normalized_raw_canonical' => $normalizedRawCanonical,
                'normalized_optimized_canonical' => $normalizedOptimizedCanonical,
                'console_error_count' => $consoleErrorCount,
                'critical_css_required' => $requiresCriticalCss,
                'critical_css_present' => $hasCriticalCss,
                'expected_critical_css_bytes' => $normalizedExpectedCriticalCss !== null ? strlen($normalizedExpectedCriticalCss) : 0,
                'injected_critical_css_bytes' => $normalizedInjectedCriticalCss !== null ? strlen($normalizedInjectedCriticalCss) : 0,
                'expected_critical_css_sha256' => $normalizedExpectedCriticalCss !== null ? hash('sha256', $normalizedExpectedCriticalCss) : null,
                'injected_critical_css_sha256' => $normalizedInjectedCriticalCss !== null ? hash('sha256', $normalizedInjectedCriticalCss) : null,
                'used_css_required' => $requiresUsedCss,
                'used_css_present' => $hasUsedCss,
                'expected_used_css_url' => $normalizedExpectedUsedCssUrl,
                'injected_used_css_url' => $normalizedInjectedUsedCssUrl,
                'visual_diff_ratio' => $visualDiffRatio,
                'visual_diff_threshold' => $visualDiffThreshold,
                'visual_diff_pixels' => $this->normalizeNullableInt($visualValidation['diff_pixels'] ?? null),
                'visual_total_pixels' => $this->normalizeNullableInt($visualValidation['total_pixels'] ?? null),
                'visual_dimensions_match' => isset($visualValidation['dimensions_match']) ? (bool) $visualValidation['dimensions_match'] : null,
                'optimized_html_bytes' => strlen($optimizedHtml),
            ],
            'error_message' => $errorMessage,
        ];
    }

    private function extractUsedCssUrl(string $html): ?string
    {
        if ($html === '') {
            return null;
        }

        if (! preg_match('/<link\b[^>]*id=["\']prestaload-used-css["\'][^>]*href=["\']([^"\']+)["\']/is', $html, $matches)
            && ! preg_match('/<link\b[^>]*href=["\']([^"\']+)["\'][^>]*id=["\']prestaload-used-css["\']/is', $html, $matches)) {
            return null;
        }

        return trim(html_entity_decode((string) ($matches[1] ?? ''), ENT_QUOTES | ENT_HTML5));
    }

    private function extractCriticalCss(string $html): ?string
    {
        if ($html === '') {
            return null;
        }

        if (! preg_match('/<style\b[^>]*id=["\']prestaload-critical-css["\'][^>]*>(.*?)<\/style>/is', $html, $matches)) {
            return null;
        }

        return html_entity_decode((string) ($matches[1] ?? ''), ENT_QUOTES | ENT_HTML5);
    }

    private function normalizeCriticalCss(?string $css): ?string
    {
        if ($css === null) {
            return null;
        }

        $normalized = trim(preg_replace('/\s+/u', ' ', trim($css)) ?? trim($css));

        return $normalized;
    }

    private function extractTitle(string $html): ?string
    {
        if ($html === '') {
            return null;
        }

        if (! preg_match('/<title\b[^>]*>(.*?)<\/title>/is', $html, $matches)) {
            return null;
        }

        return trim(html_entity_decode(strip_tags($matches[1]), ENT_QUOTES | ENT_HTML5));
    }

    private function extractCanonical(string $html): ?string
    {
        if ($html === '') {
            return null;
        }

        if (! preg_match('/<link\b[^>]*rel=["\']canonical["\'][^>]*href=["\']([^"\']+)["\']/is', $html, $matches)
            && ! preg_match('/<link\b[^>]*href=["\']([^"\']+)["\'][^>]*rel=["\']canonical["\']/is', $html, $matches)) {
            return null;
        }

        return trim($matches[1]);
    }

    private function normalizeComparableText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = preg_replace('/\s+/u', ' ', trim($value));

        return is_string($normalized) ? $normalized : trim($value);
    }

    /**
     * @param  mixed  $value
     */
    private function normalizeNullableFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            throw new RuntimeException('Invalid visual diff ratio.');
        }

        return (float) $value;
    }

    /**
     * @param  mixed  $messages
     */
    private function countConsoleErrors($messages): int
    {
        if (! is_array($messages)) {
            return 0;
        }

        $count = 0;

        foreach ($messages as $message) {
            if (is_array($message) && strtolower((string) ($message['type'] ?? '')) === 'error') {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param  mixed  $value
     */
    private function normalizeNullableInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            throw new RuntimeException('Invalid render status code.');
        }

        return (int) $value;
    }
}
