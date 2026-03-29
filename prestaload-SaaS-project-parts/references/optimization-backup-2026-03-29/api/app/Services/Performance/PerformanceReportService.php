<?php

namespace App\Services\Performance;

class PerformanceReportService
{
    public function __construct(
        private readonly PageSpeedService $pageSpeedService,
        private readonly LocalScannerService $localScannerService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function scanReport(string $url): array
    {
        $scanUrl = $this->appendPrestaLoadBypassParameter($url);

        if ($this->shouldUseLocalScanner($url)) {
            $report = $this->localScannerService->scanReport($scanUrl);
        } else {
            $report = $this->pageSpeedService->scanReport($scanUrl);
        }

        $report['url'] = $url;
        $report['scanned_url'] = $scanUrl;

        return $report;
    }

    private function shouldUseLocalScanner(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($host === '') {
            return false;
        }

        if (in_array($host, ['localhost'], true)) {
            return true;
        }

        foreach (['.local', '.test', '.localhost', '.internal'] as $suffix) {
            if (str_ends_with($host, $suffix)) {
                return true;
            }
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
        }

        return false;
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
