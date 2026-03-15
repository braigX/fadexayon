<?php
/**
 * Stores full scanner payloads locally and extracts the parts that matter for
 * asset-level rule decisions.
 */

class PrestaLoadAssetScanStore
{
    private $reportsDirectory;

    public function __construct($modulePath)
    {
        $this->reportsDirectory = rtrim((string) $modulePath, '/') . '/reports';
    }

    /**
     * Persists the full scanner response and a smaller extracted asset report.
     */
    public function saveScan(array $page, array $scanPayload)
    {
        if (!is_dir($this->reportsDirectory)) {
            @mkdir($this->reportsDirectory, 0775, true);
        }

        $timestamp = date('Ymd\\THis');
        $slug = $this->slugify(isset($page['key']) ? $page['key'] : 'page');
        $baseName = $slug . '-' . $timestamp;
        $fullPath = $this->reportsDirectory . '/' . $baseName . '.json';
        $assetPath = $this->reportsDirectory . '/' . $baseName . '-assets.json';

        $fullJson = json_encode($scanPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $assetJson = json_encode($this->buildAssetSummary($page, $scanPayload, basename($fullPath)), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($fullJson === false || $assetJson === false) {
            throw new Exception('Could not encode the scanner report.');
        }

        if (file_put_contents($fullPath, $fullJson, LOCK_EX) === false) {
            throw new Exception('Could not write the full scan report to ' . $fullPath);
        }

        if (file_put_contents($assetPath, $assetJson, LOCK_EX) === false) {
            throw new Exception('Could not write the asset summary report to ' . $assetPath);
        }

        return [
            'full_path' => $fullPath,
            'asset_path' => $assetPath,
        ];
    }

    /**
     * Returns the most recent extracted asset summary for a page key.
     */
    public function getLatestAssetSummary($pageKey)
    {
        $pattern = $this->reportsDirectory . '/' . $this->slugify((string) $pageKey) . '-*-assets.json';
        $files = glob($pattern);
        if (empty($files)) {
            return null;
        }

        rsort($files);
        $decoded = json_decode((string) file_get_contents($files[0]), true);

        return is_array($decoded) ? $decoded : null;
    }

    private function buildAssetSummary(array $page, array $scanPayload, $sourceReport)
    {
        $payload = isset($scanPayload['data']) && is_array($scanPayload['data']) ? $scanPayload['data'] : $scanPayload;
        $mobile = isset($payload['mobile']) && is_array($payload['mobile']) ? $payload['mobile'] : [];
        $lhr = isset($mobile['raw_lhr']) && is_array($mobile['raw_lhr']) ? $mobile['raw_lhr'] : [];
        $audits = isset($lhr['audits']) && is_array($lhr['audits']) ? $lhr['audits'] : [];

        $summary = [
            'page' => $page,
            'source_report' => $sourceReport,
            'url' => isset($payload['url']) ? $payload['url'] : (isset($page['url']) ? $page['url'] : ''),
            'scanned_at' => isset($payload['scanned_at']) ? $payload['scanned_at'] : date('c'),
            'mobile_score' => isset($mobile['categories']['performance']['score']) ? $mobile['categories']['performance']['score'] : null,
            'metrics' => [],
            'assets' => $this->collectAssets($audits),
        ];

        foreach (['first-contentful-paint', 'largest-contentful-paint', 'speed-index', 'interactive', 'total-blocking-time', 'cumulative-layout-shift'] as $metricId) {
            $audit = isset($audits[$metricId]) ? $audits[$metricId] : [];
            $summary['metrics'][$metricId] = [
                'display_value' => isset($audit['displayValue']) ? $audit['displayValue'] : null,
                'numeric_value' => isset($audit['numericValue']) ? $audit['numericValue'] : null,
                'score' => isset($audit['score']) ? $audit['score'] : null,
            ];
        }

        return $summary;
    }

    /**
     * Builds a flat asset list keyed by URL so the admin UI can show one row
     * per asset with merged signals from several Lighthouse audits.
     */
    private function collectAssets(array $audits)
    {
        $assetMap = [];
        $definitions = [
            'render-blocking-insight' => 'render_blocking',
            'unused-javascript' => 'unused_javascript',
            'unused-css-rules' => 'unused_css',
            'cache-insight' => 'cache_lifetime',
            'image-delivery-insight' => 'image_delivery',
        ];

        foreach ($definitions as $auditId => $signalKey) {
            $items = isset($audits[$auditId]['details']['items']) && is_array($audits[$auditId]['details']['items'])
                ? $audits[$auditId]['details']['items']
                : [];

            foreach ($items as $item) {
                $url = isset($item['url']) ? (string) $item['url'] : '';
                if ($url === '') {
                    continue;
                }

                if (!isset($assetMap[$url])) {
                    $assetMap[$url] = [
                        'url' => $url,
                        'type' => $this->guessAssetType($url),
                        'transfer_size' => null,
                        'render_blocking_ms' => null,
                        'unused_bytes' => null,
                        'cache_lifetime_ms' => null,
                        'signals' => [],
                    ];
                }

                $assetMap[$url]['transfer_size'] = isset($item['totalBytes']) ? $item['totalBytes'] : $assetMap[$url]['transfer_size'];
                if (isset($item['wastedMs'])) {
                    $assetMap[$url]['render_blocking_ms'] = $item['wastedMs'];
                }
                if (isset($item['wastedBytes'])) {
                    $assetMap[$url]['unused_bytes'] = max((int) $assetMap[$url]['unused_bytes'], (int) $item['wastedBytes']);
                }
                if (isset($item['cacheLifetimeMs'])) {
                    $assetMap[$url]['cache_lifetime_ms'] = $item['cacheLifetimeMs'];
                }

                $assetMap[$url]['signals'][] = $signalKey;
            }
        }

        $assets = array_values($assetMap);
        usort($assets, function ($left, $right) {
            $leftScore = ((int) $left['unused_bytes']) + (((int) $left['render_blocking_ms']) * 1000);
            $rightScore = ((int) $right['unused_bytes']) + (((int) $right['render_blocking_ms']) * 1000);

            return $rightScore <=> $leftScore;
        });

        return $assets;
    }

    private function guessAssetType($url)
    {
        $normalizedUrl = Tools::strtolower((string) $url);
        if (preg_match('/\.css(\?|$)/', $normalizedUrl)) {
            return 'css';
        }
        if (preg_match('/\.js(\?|$)/', $normalizedUrl) || strpos($normalizedUrl, '/gtag/js') !== false) {
            return 'js';
        }
        if (preg_match('/\.(png|jpe?g|gif|webp|avif|svg|mp4|webm)(\?|$)/', $normalizedUrl)) {
            return 'media';
        }

        return 'other';
    }

    private function slugify($value)
    {
        $value = Tools::strtolower((string) $value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);

        return trim((string) $value, '-');
    }
}
