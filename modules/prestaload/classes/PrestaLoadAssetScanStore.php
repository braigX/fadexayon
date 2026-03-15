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
        $pageUrl = isset($payload['url']) ? $payload['url'] : (isset($page['url']) ? $page['url'] : '');

        $summary = [
            'page' => $page,
            'source_report' => $sourceReport,
            'url' => $pageUrl,
            'scanned_at' => isset($payload['scanned_at']) ? $payload['scanned_at'] : date('c'),
            'mobile_score' => isset($mobile['categories']['performance']['score']) ? $mobile['categories']['performance']['score'] : null,
            'metrics' => [],
            'assets' => $this->collectAssets($pageUrl, $audits),
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
    private function collectAssets($pageUrl, array $audits)
    {
        $assetMap = $this->collectAssetsFromPageHtml($pageUrl);
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
                        'used_bytes' => null,
                        'usage_percent' => null,
                        'render_blocking_ms' => null,
                        'unused_bytes' => null,
                        'cache_lifetime_ms' => null,
                        'signals' => [],
                        'discovered_from' => 'lighthouse',
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

        foreach ($assetMap as &$asset) {
            $asset['signals'] = array_values(array_unique($asset['signals']));
            if ($asset['transfer_size'] !== null && $asset['unused_bytes'] !== null) {
                $asset['used_bytes'] = max(0, (int) $asset['transfer_size'] - (int) $asset['unused_bytes']);
                if ((int) $asset['transfer_size'] > 0) {
                    $asset['usage_percent'] = round(($asset['used_bytes'] / (int) $asset['transfer_size']) * 100, 1);
                }
            }
        }
        unset($asset);

        $assets = array_values($assetMap);
        usort($assets, function ($left, $right) {
            $leftScore = ((int) $left['unused_bytes']) + (((int) $left['render_blocking_ms']) * 1000) + (!empty($left['signals']) ? 1000000 : 0);
            $rightScore = ((int) $right['unused_bytes']) + (((int) $right['render_blocking_ms']) * 1000) + (!empty($right['signals']) ? 1000000 : 0);

            return $rightScore <=> $leftScore;
        });

        return $assets;
    }

    /**
     * Lighthouse only reports a subset of assets in byte-efficiency audits.
     * We fetch the page HTML directly so the UI can also list assets that were
     * used but not flagged by those audits.
     */
    private function collectAssetsFromPageHtml($pageUrl)
    {
        $html = $this->fetchPageHtml($pageUrl);
        if ($html === '') {
            return [];
        }

        $assetMap = [];

        if (preg_match_all('/<script\b[^>]*\bsrc=(["\'])(.*?)\1[^>]*>/is', $html, $scriptMatches)) {
            foreach ($scriptMatches[2] as $src) {
                $this->appendDiscoveredAsset($assetMap, $this->resolveUrl($pageUrl, html_entity_decode((string) $src, ENT_QUOTES, 'UTF-8')), 'js');
            }
        }

        if (preg_match_all('/<link\b[^>]*\brel=(["\'])(.*?)\1[^>]*\bhref=(["\'])(.*?)\3[^>]*>/is', $html, $linkMatches, PREG_SET_ORDER)) {
            foreach ($linkMatches as $match) {
                $rel = Tools::strtolower(trim((string) $match[2]));
                if (strpos($rel, 'stylesheet') === false) {
                    continue;
                }

                $this->appendDiscoveredAsset($assetMap, $this->resolveUrl($pageUrl, html_entity_decode((string) $match[4], ENT_QUOTES, 'UTF-8')), 'css');
            }
        }

        if (preg_match_all('/<link\b[^>]*\bhref=(["\'])(.*?)\1[^>]*\brel=(["\'])(.*?)\3[^>]*>/is', $html, $reverseLinkMatches, PREG_SET_ORDER)) {
            foreach ($reverseLinkMatches as $match) {
                $rel = Tools::strtolower(trim((string) $match[4]));
                if (strpos($rel, 'stylesheet') === false) {
                    continue;
                }

                $this->appendDiscoveredAsset($assetMap, $this->resolveUrl($pageUrl, html_entity_decode((string) $match[2], ENT_QUOTES, 'UTF-8')), 'css');
            }
        }

        return $assetMap;
    }

    private function appendDiscoveredAsset(array &$assetMap, $url, $type = null)
    {
        if ($url === '' || strpos($url, 'data:') === 0) {
            return;
        }

        if (!isset($assetMap[$url])) {
            $assetMap[$url] = [
                'url' => $url,
                'type' => $type !== null ? $type : $this->guessAssetType($url),
                'transfer_size' => null,
                'used_bytes' => null,
                'usage_percent' => null,
                'render_blocking_ms' => null,
                'unused_bytes' => null,
                'cache_lifetime_ms' => null,
                'signals' => [],
                'discovered_from' => 'page_html',
            ];
        }
    }

    private function fetchPageHtml($pageUrl)
    {
        $pageUrl = trim((string) $pageUrl);
        if ($pageUrl === '') {
            return '';
        }

        if (function_exists('curl_init')) {
            $ch = curl_init($pageUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT => 'PrestaLoad Asset Scanner',
            ]);
            $body = curl_exec($ch);
            curl_close($ch);

            return is_string($body) ? $body : '';
        }

        $context = stream_context_create([
            'http' => [
                'timeout' => 60,
                'header' => "User-Agent: PrestaLoad Asset Scanner\r\n",
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $body = @file_get_contents($pageUrl, false, $context);

        return is_string($body) ? $body : '';
    }

    private function resolveUrl($baseUrl, $assetUrl)
    {
        $assetUrl = trim((string) $assetUrl);
        if ($assetUrl === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $assetUrl)) {
            return $assetUrl;
        }

        if (strpos($assetUrl, '//') === 0) {
            $parts = parse_url((string) $baseUrl);
            $scheme = isset($parts['scheme']) ? $parts['scheme'] : 'https';

            return $scheme . ':' . $assetUrl;
        }

        $parts = parse_url((string) $baseUrl);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return $assetUrl;
        }

        $origin = $parts['scheme'] . '://' . $parts['host'];
        if (!empty($parts['port'])) {
            $origin .= ':' . (int) $parts['port'];
        }

        if (strpos($assetUrl, '/') === 0) {
            return $origin . $assetUrl;
        }

        $basePath = isset($parts['path']) ? $parts['path'] : '/';
        $baseDir = rtrim(str_replace('\\', '/', dirname($basePath)), '/');

        return $origin . ($baseDir !== '' ? $baseDir : '') . '/' . ltrim($assetUrl, '/');
    }

    private function guessAssetType($url)
    {
        $normalizedUrl = Tools::strtolower((string) $url);
        if (preg_match('/\.css(\?|$)/', $normalizedUrl)) {
            return 'css';
        }
        if (
            preg_match('/\.js(\?|$)/', $normalizedUrl)
            || strpos($normalizedUrl, '/gtag/js') !== false
            || strpos($normalizedUrl, '/gsi/client') !== false
            || strpos($normalizedUrl, '/_\/gsi\/_/js/') !== false
        ) {
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
