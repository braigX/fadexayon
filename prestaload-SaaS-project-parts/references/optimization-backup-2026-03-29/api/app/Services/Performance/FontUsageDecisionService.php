<?php

namespace App\Services\Performance;

use App\Models\PrestashopShopPageTypeAssetRule;
use App\Models\PrestashopShopPageTypeProfile;

class FontUsageDecisionService
{
    /**
     * @return array<string, mixed>|null
     */
    public function buildDeviceRow(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass,
        int $optimizedPageCount = 1
    ): ?array {
        $variant = $this->getDeviceVariant($profile, $deviceClass);

        if ($variant === null) {
            return null;
        }

        $declaredFamilies = $this->normalizeStringList($variant['declared_font_families'] ?? []);
        $usedFamilies = $this->normalizeStringList($variant['used_font_families'] ?? []);
        $aboveFoldFamilies = $this->normalizeStringList($variant['used_above_the_fold'] ?? []);
        $usedWeights = $this->normalizeStringList($variant['used_weights'] ?? []);
        $assets = $this->buildDeviceAssets($profile, $deviceClass);

        $actionSummary = [
            'keep' => 0,
            'self_host' => 0,
            'self_host_preload' => 0,
            'set_font_display_swap' => 0,
            'remove_unused' => 0,
            'dedupe_icon_font' => 0,
        ];

        foreach ($assets as $asset) {
            $action = (string) ($asset['action'] ?? 'keep');
            if (! array_key_exists($action, $actionSummary)) {
                $action = 'keep';
            }

            $actionSummary[$action]++;
        }

        $declaredCount = count($declaredFamilies);
        $usedCount = count($usedFamilies);
        $aboveFoldCount = count($aboveFoldFamilies);

        return [
            'id' => $profile->id . ':' . $deviceClass,
            'profile_id' => $profile->id,
            'shop_url' => $profile->scanSourceUrl?->url,
            'page_type' => $profile->pageType?->code ?? $profile->pageType?->name ?? '',
            'page_type_name' => $profile->pageType?->name ?? $profile->pageType?->code ?? '',
            'device_class' => $deviceClass,
            'optimized_page_count' => $optimizedPageCount,
            'declared_fonts_count' => $declaredCount,
            'used_fonts_count' => $usedCount,
            'above_the_fold_count' => $aboveFoldCount,
            'duplicate_icon_font_count' => count(array_filter($assets, static fn (array $asset): bool => ($asset['type'] ?? null) === 'icon_stylesheet')),
            'google_fonts_count' => count(array_filter($assets, static fn (array $asset): bool => ($asset['type'] ?? null) === 'google_stylesheet')),
            'declared_fonts_estimated' => $declaredCount * $optimizedPageCount,
            'used_fonts_estimated' => $usedCount * $optimizedPageCount,
            'above_the_fold_fonts_estimated' => $aboveFoldCount * $optimizedPageCount,
            'action_summary' => $actionSummary,
            'fonts' => $assets,
            'used_weights' => $usedWeights,
            'last_font_scanned_at' => optional($profile->last_font_scanned_at)?->toIso8601String(),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function buildDeviceAssets(PrestashopShopPageTypeProfile $profile, string $deviceClass): array
    {
        $variant = $this->getDeviceVariant($profile, $deviceClass);

        if ($variant === null) {
            return [];
        }

        $usedFamilies = $this->normalizeStringList($variant['used_font_families'] ?? []);
        $aboveFoldFamilies = $this->normalizeStringList($variant['used_above_the_fold'] ?? []);
        $usedWeights = $this->normalizeStringList($variant['used_weights'] ?? []);
        $googleStylesheets = $this->normalizeUrlList($variant['google_fonts_stylesheets'] ?? []);
        $duplicateIconFontStylesheets = $this->normalizeDuplicateIconStylesheets($variant['duplicate_icon_font_stylesheets'] ?? []);
        $rules = $this->fontRulesForProfile($profile, $deviceClass);
        $documentUrl = $profile->scanSourceUrl?->url ?: (string) (($profile->scan_report_json['url'] ?? '') ?: '');

        $assets = [];

        foreach ($googleStylesheets as $stylesheetUrl) {
            $families = $this->extractGoogleFontFamilies([$stylesheetUrl]);
            $usedForStylesheet = array_values(array_intersect($families, $usedFamilies));
            $aboveFoldForStylesheet = array_values(array_intersect($families, $aboveFoldFamilies));
            $recommendedAction = $usedForStylesheet === []
                ? 'remove_unused'
                : ($aboveFoldForStylesheet !== [] ? 'self_host_preload' : 'self_host');

            $normalizedUrl = $this->normalizeAssetReference($stylesheetUrl);
            $rule = $normalizedUrl !== '' ? ($rules[$normalizedUrl] ?? null) : null;

            $assets[$normalizedUrl] = [
                'type' => 'google_stylesheet',
                'family' => $this->formatGoogleFamiliesLabel($families),
                'href' => $stylesheetUrl,
                'asset_url' => $stylesheetUrl,
                'source' => 'google-fonts',
                'recommended_action' => $recommendedAction,
                'action' => $rule?->effective_action ?: $recommendedAction,
                'action_source' => $rule?->action_source ?: 'auto',
                'rule_id' => $rule?->id,
                'confidence' => $rule ? (float) $rule->confidence : ($usedForStylesheet === [] ? 0.95 : 0.85),
                'used' => $usedForStylesheet !== [],
                'above_the_fold' => $aboveFoldForStylesheet !== [],
                'weights' => $usedWeights,
                'families' => $families,
                'used_families' => $usedForStylesheet,
                'above_fold_families' => $aboveFoldForStylesheet,
                'allowed_actions' => ['keep', 'self_host', 'self_host_preload', 'remove_unused'],
                'asset_ready' => $rule ? ((string) ($rule->font_asset_status ?? '') === 'ready') : false,
                'asset_public_url' => $rule?->font_css_public_url,
                'font_meta' => is_array($rule?->font_meta_json ?? null) ? $rule->font_meta_json : [],
            ];
        }

        foreach ($duplicateIconFontStylesheets as $entry) {
            $href = trim((string) ($entry['href'] ?? ''));
            if ($href === '') {
                continue;
            }

            $normalizedUrl = $this->normalizeAssetReference($href);
            $rule = $normalizedUrl !== '' ? ($rules[$normalizedUrl] ?? null) : null;

            $assets[$normalizedUrl] = [
                'type' => 'icon_stylesheet',
                'family' => trim((string) ($entry['family'] ?? '')) !== '' ? trim((string) $entry['family']) : basename((string) parse_url($href, PHP_URL_PATH)),
                'href' => $href,
                'asset_url' => $href,
                'source' => 'icon-font',
                'recommended_action' => 'dedupe_icon_font',
                'action' => $rule?->effective_action ?: 'dedupe_icon_font',
                'action_source' => $rule?->action_source ?: 'auto',
                'rule_id' => $rule?->id,
                'confidence' => $rule ? (float) $rule->confidence : 0.95,
                'used' => true,
                'above_the_fold' => false,
                'weights' => [],
                'count' => (int) ($entry['count'] ?? 0),
                'allowed_actions' => ['keep', 'dedupe_icon_font'],
                'asset_ready' => false,
                'asset_public_url' => null,
                'font_meta' => [],
            ];
        }

        foreach ($this->buildFontDisplayAssets($profile, $deviceClass, $documentUrl, $duplicateIconFontStylesheets, $rules) as $asset) {
            $normalizedUrl = $this->normalizeAssetReference((string) ($asset['asset_url'] ?? ''));
            if ($normalizedUrl === '') {
                continue;
            }

            $assets[$normalizedUrl] = $asset;
        }

        return array_values($assets);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildFontDisplayAssets(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass,
        string $documentUrl,
        array $duplicateIconFontStylesheets,
        array $rules
    ): array {
        $fontIssueUrls = $this->extractFontDisplayIssueUrls($profile, $deviceClass);
        if ($fontIssueUrls === [] || $documentUrl === '') {
            return [];
        }

        $networkStylesheets = $this->extractNetworkStylesheetUrls($profile, $deviceClass, $documentUrl);
        if ($networkStylesheets === []) {
            return [];
        }

        $duplicateIconUrls = array_values(array_filter(array_map(
            static fn (array $entry): string => trim((string) ($entry['href'] ?? '')),
            $duplicateIconFontStylesheets
        )));

        $matchedFontUrls = [];
        foreach ($fontIssueUrls as $fontUrl) {
            foreach ($this->inferFontDisplayStylesheetCandidates($fontUrl, $documentUrl, $networkStylesheets, $duplicateIconUrls) as $stylesheetUrl) {
                $matchedFontUrls[$stylesheetUrl][] = $fontUrl;
            }
        }

        $assets = [];
        foreach ($matchedFontUrls as $stylesheetUrl => $fontUrls) {
            $normalizedUrl = $this->normalizeAssetReference($stylesheetUrl);
            $rule = $normalizedUrl !== '' ? ($rules[$normalizedUrl] ?? null) : null;
            $familyLabel = $this->formatLocalFontAssetLabel($stylesheetUrl, $fontUrls);
            $confidence = min(0.98, 0.65 + (count($fontUrls) * 0.08));

            $assets[] = [
                'type' => 'local_font_stylesheet',
                'family' => $familyLabel,
                'href' => $stylesheetUrl,
                'asset_url' => $stylesheetUrl,
                'source' => 'font-display',
                'recommended_action' => 'set_font_display_swap',
                'action' => $rule?->effective_action ?: 'set_font_display_swap',
                'action_source' => $rule?->action_source ?: 'auto',
                'rule_id' => $rule?->id,
                'confidence' => $rule ? (float) $rule->confidence : $confidence,
                'used' => true,
                'above_the_fold' => true,
                'weights' => [],
                'families' => [],
                'used_families' => [],
                'above_fold_families' => [],
                'font_issue_urls' => array_values(array_unique($fontUrls)),
                'allowed_actions' => ['keep', 'set_font_display_swap'],
                'asset_ready' => $rule ? ((string) ($rule->font_asset_status ?? '') === 'ready') : false,
                'asset_public_url' => $rule?->font_css_public_url,
                'font_meta' => is_array($rule?->font_meta_json ?? null) ? $rule->font_meta_json : [],
            ];
        }

        return $assets;
    }

    /**
     * @return array<string, PrestashopShopPageTypeAssetRule>
     */
    private function fontRulesForProfile(PrestashopShopPageTypeProfile $profile, string $deviceClass): array
    {
        $rules = $profile->relationLoaded('assetRules')
            ? $profile->assetRules->where('asset_type', 'font')->where('device_class', $deviceClass)
            : PrestashopShopPageTypeAssetRule::query()
                ->where('profile_id', $profile->id)
                ->where('device_class', $deviceClass)
                ->where('asset_type', 'font')
                ->get();

        $mapped = [];
        foreach ($rules as $rule) {
            $normalizedUrl = $this->normalizeAssetReference((string) ($rule->asset_url ?? ''));
            if ($normalizedUrl !== '') {
                $mapped[$normalizedUrl] = $rule;
            }
        }

        return $mapped;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function getDeviceVariant(PrestashopShopPageTypeProfile $profile, string $deviceClass): ?array
    {
        $fontUsage = is_array($profile->font_usage_json ?? null) ? $profile->font_usage_json : [];
        $variant = is_array($fontUsage[$deviceClass] ?? null) ? $fontUsage[$deviceClass] : [];

        return $variant !== [] ? $variant : null;
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

            $text = $this->normalizeFamilyName((string) $item);
            if ($text !== '') {
                $normalized[] = $text;
            }
        }

        return array_values(array_unique($normalized));
    }

    private function normalizeFamilyName(string $value): string
    {
        return trim(mb_strtolower(trim($value, " \t\n\r\0\x0B'\"")));
    }

    /**
     * @param  mixed  $value
     * @return list<string>
     */
    private function normalizeUrlList(mixed $value): array
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
     * @param  list<string>  $urls
     * @return list<string>
     */
    private function extractGoogleFontFamilies(array $urls): array
    {
        $families = [];

        foreach ($urls as $url) {
            $parts = parse_url($url);
            if (! is_array($parts) || empty($parts['query'])) {
                continue;
            }

            foreach (explode('&', (string) $parts['query']) as $pair) {
                $segments = explode('=', $pair, 2);
                if (count($segments) !== 2 || $segments[0] !== 'family') {
                    continue;
                }

                foreach (explode('|', rawurldecode((string) $segments[1])) as $familySegment) {
                    $familySegment = preg_replace('/:.+$/', '', (string) $familySegment) ?? (string) $familySegment;
                    $familySegment = str_replace('+', ' ', $familySegment);
                    $familySegment = $this->normalizeFamilyName($familySegment);

                    if ($familySegment !== '') {
                        $families[$familySegment] = true;
                    }
                }
            }
        }

        return array_keys($families);
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

    /**
     * @param  list<string>  $families
     */
    private function formatGoogleFamiliesLabel(array $families): string
    {
        if ($families === []) {
            return 'Google Fonts';
        }

        return implode(', ', array_map(static function (string $family): string {
            return mb_convert_case($family, \MB_CASE_TITLE, 'UTF-8');
        }, $families));
    }

    /**
     * @return list<string>
     */
    private function extractFontDisplayIssueUrls(PrestashopShopPageTypeProfile $profile, string $deviceClass): array
    {
        $audit = $this->extractRequestedAudit($profile, $deviceClass, [
            'font-display-insight',
            'font-display',
            'Font display insight',
            'Font display',
        ]);

        if (! is_array($audit)) {
            return [];
        }

        $items = is_array($audit['details']['items'] ?? null) ? $audit['details']['items'] : [];
        $urls = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $url = trim((string) ($item['url'] ?? ''));
            if ($url !== '' && preg_match('/\.(woff2|woff|ttf|otf|eot)(\?|#|$)/i', $url)) {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }

    /**
     * @return list<string>
     */
    private function extractNetworkStylesheetUrls(PrestashopShopPageTypeProfile $profile, string $deviceClass, string $documentUrl): array
    {
        $urls = [];
        $audit = $this->extractRequestedAudit($profile, $deviceClass, [
            'network-requests',
            'Network Requests',
        ]);

        $items = is_array($audit['details']['items'] ?? null) ? $audit['details']['items'] : [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $url = trim((string) ($item['url'] ?? ''));
            $resourceType = strtolower(trim((string) ($item['resourceType'] ?? '')));
            $mimeType = strtolower(trim((string) ($item['mimeType'] ?? '')));
            if ($url === '' || ! $this->isSameOriginUrl($url, $documentUrl)) {
                continue;
            }

            if ($resourceType === 'stylesheet' || str_starts_with($mimeType, 'text/css')) {
                $urls[] = $url;
            }
        }

        $pageMetricsRequests = is_array($profile->scan_report_json['page_metrics']['requests'] ?? null)
            ? $profile->scan_report_json['page_metrics']['requests']
            : [];
        foreach ($pageMetricsRequests as $item) {
            if (! is_array($item)) {
                continue;
            }

            $url = trim((string) ($item['url'] ?? ''));
            $resourceType = strtolower(trim((string) ($item['resourceType'] ?? '')));
            if ($url !== '' && $resourceType === 'stylesheet' && $this->isSameOriginUrl($url, $documentUrl)) {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }

    /**
     * @return list<string>
     */
    private function inferFontDisplayStylesheetCandidates(
        string $fontUrl,
        string $documentUrl,
        array $networkStylesheets,
        array $duplicateIconUrls
    ): array {
        if (! $this->isSameOriginUrl($fontUrl, $documentUrl)) {
            return [];
        }

        $fontPath = strtolower((string) parse_url($fontUrl, PHP_URL_PATH));
        if ($fontPath === '') {
            return [];
        }

        $preferred = [];

        if (str_contains($fontPath, '/webfonts/')) {
            $fontBase = basename($fontPath);
            $familyCss = null;
            if (str_starts_with($fontBase, 'fa-brands-')) {
                $familyCss = 'brands.min.css';
            } elseif (str_starts_with($fontBase, 'fa-solid-')) {
                $familyCss = 'solid.min.css';
            } elseif (str_starts_with($fontBase, 'fa-regular-')) {
                $familyCss = 'regular.min.css';
            }

            foreach ($networkStylesheets as $stylesheetUrl) {
                $stylesheetPath = strtolower((string) parse_url($stylesheetUrl, PHP_URL_PATH));
                if ($familyCss !== null && str_ends_with($stylesheetPath, '/' . $familyCss)) {
                    $preferred[] = $stylesheetUrl;
                }
            }
        }

        if (str_contains($fontPath, '/assets/css/')) {
            foreach ($networkStylesheets as $stylesheetUrl) {
                $stylesheetPath = strtolower((string) parse_url($stylesheetUrl, PHP_URL_PATH));
                if (str_contains($stylesheetPath, '/assets/fonts/') || str_ends_with($stylesheetPath, '/fonts.css')) {
                    $preferred[] = $stylesheetUrl;
                }
            }
        }

        if ($preferred !== []) {
            return array_values(array_unique($preferred));
        }

        $scored = [];
        foreach ($networkStylesheets as $stylesheetUrl) {
            $stylesheetPath = strtolower((string) parse_url($stylesheetUrl, PHP_URL_PATH));
            if ($stylesheetPath === '') {
                continue;
            }

            $score = $this->countSharedLeadingSegments($fontPath, $stylesheetPath);

            if (str_contains($stylesheetPath, '/fonts/')) {
                $score += 6;
            }

            if (preg_match('/(?:^|\/)(?:fonts?|webfonts?)\.css$/i', $stylesheetPath)) {
                $score += 5;
            }

            if (in_array($stylesheetUrl, $duplicateIconUrls, true)) {
                $score += 1;
            }

            $scored[$stylesheetUrl] = $score;
        }

        arsort($scored);
        $bestScore = (int) reset($scored);
        if ($bestScore <= 0) {
            return [];
        }

        return array_values(array_keys(array_filter($scored, static fn (int $score): bool => $score === $bestScore)));
    }

    /**
     * @param  list<string>  $fontUrls
     */
    private function formatLocalFontAssetLabel(string $stylesheetUrl, array $fontUrls): string
    {
        $path = basename((string) parse_url($stylesheetUrl, PHP_URL_PATH));
        $fontBase = basename((string) parse_url((string) ($fontUrls[0] ?? ''), PHP_URL_PATH));

        if ($path !== '') {
            return $path . ($fontBase !== '' ? ' -> ' . $fontBase : '');
        }

        if ($fontBase !== '') {
            return $fontBase;
        }

        return 'Local font stylesheet';
    }

    private function countSharedLeadingSegments(string $leftPath, string $rightPath): int
    {
        $left = array_values(array_filter(explode('/', trim($leftPath, '/'))));
        $right = array_values(array_filter(explode('/', trim($rightPath, '/'))));
        $count = 0;

        foreach ($left as $index => $segment) {
            if (($right[$index] ?? null) !== $segment) {
                break;
            }

            $count++;
        }

        return $count;
    }

    private function isSameOriginUrl(string $url, string $documentUrl): bool
    {
        $target = parse_url($url);
        $document = parse_url($documentUrl);

        if (! is_array($target) || ! is_array($document)) {
            return false;
        }

        return strtolower((string) ($target['host'] ?? '')) === strtolower((string) ($document['host'] ?? ''))
            && (($target['port'] ?? null) === ($document['port'] ?? null));
    }

    /**
     * @param  list<string>  $candidateKeys
     * @return array<string, mixed>|null
     */
    private function extractRequestedAudit(
        PrestashopShopPageTypeProfile $profile,
        string $deviceClass,
        array $candidateKeys
    ): ?array {
        $report = is_array($profile->scan_report_json ?? null) ? $profile->scan_report_json : [];
        $device = is_array($report[$deviceClass] ?? null) ? $report[$deviceClass] : [];
        $audits = is_array($device['requested_audits'] ?? null) ? $device['requested_audits'] : [];

        foreach ($candidateKeys as $key) {
            if (is_array($audits[$key] ?? null)) {
                return $audits[$key];
            }
        }

        return null;
    }

    private function normalizeAssetReference(string $value): string
    {
        $normalized = trim($value);

        if ($normalized === '') {
            return '';
        }

        if (str_starts_with($normalized, '//')) {
            $normalized = 'https:' . $normalized;
        }

        if (! preg_match('#^https?://#i', $normalized)) {
            return ltrim($normalized, '/');
        }

        $path = parse_url($normalized, PHP_URL_PATH);
        $query = parse_url($normalized, PHP_URL_QUERY);

        return ltrim((string) ($path ?? ''), '/') . (is_string($query) && $query !== '' ? '?' . $query : '');
    }
}
