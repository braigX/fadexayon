<?php

namespace App\Services\Performance;

class FontUsageReportService
{
    public function __construct(
        private readonly LocalScannerService $localScannerService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function scanReport(string $url): array
    {
        $scanUrl = $this->appendPrestaLoadBypassParameter($url);
        $report = $this->localScannerService->scanFontUsageReport($scanUrl);
        $report['url'] = $url;
        $report['scanned_url'] = $scanUrl;

        return $report;
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
}
