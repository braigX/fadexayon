<?php
/**
 * Requests per-page font usage audits from the remote scanner service.
 */

class PrestaLoadFontUsageScannerClient
{
    /**
     * @var PrestaLoadCacheSettings
     */
    private $settings;

    public function __construct(PrestaLoadCacheSettings $settings)
    {
        $this->settings = $settings;
    }

    public function generate($pageType, $url)
    {
        $endpoint = $this->settings->getAssetScannerBaseUrl() . '/font-usage';
        $payload = json_encode([
            'url' => (string) $url,
            'page_type' => (string) $pageType,
            'device' => 'both',
        ]);

        if ($payload === false) {
            throw new Exception('Could not encode the font usage payload.');
        }

        $response = function_exists('curl_init')
            ? $this->sendWithCurl($endpoint, $payload)
            : $this->sendWithStreams($endpoint, $payload);

        $variants = $this->extractVariants($response);
        if (empty($variants)) {
            throw new Exception('The scanner did not return any font usage data.');
        }

        return [
            'variants' => $variants,
            'raw' => $response,
        ];
    }

    private function sendWithCurl($endpoint, $payload)
    {
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 300,
        ]);

        $body = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            throw new Exception('Font usage request failed: ' . $error);
        }

        return $this->decodeResponse($body, $httpCode);
    }

    private function sendWithStreams($endpoint, $payload)
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 300,
                'ignore_errors' => true,
            ],
        ]);

        $body = @file_get_contents($endpoint, false, $context);
        $httpCode = 0;
        if (!empty($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches)) {
            $httpCode = (int) $matches[1];
        }

        if ($body === false) {
            throw new Exception('Font usage request failed.');
        }

        return $this->decodeResponse($body, $httpCode);
    }

    private function decodeResponse($body, $httpCode)
    {
        $decoded = json_decode((string) $body, true);
        if (!is_array($decoded)) {
            throw new Exception('Font usage scanner returned invalid JSON.');
        }

        if ($httpCode >= 400 || (isset($decoded['success']) && empty($decoded['success']))) {
            throw new Exception(isset($decoded['message']) ? (string) $decoded['message'] : 'Font usage request failed.');
        }

        return $decoded;
    }

    private function extractVariants(array $response)
    {
        $variants = [];

        foreach (['mobile', 'desktop'] as $device) {
            $devicePayload = isset($response['data'][$device]) && is_array($response['data'][$device])
                ? $response['data'][$device]
                : [];

            if (!empty($devicePayload)) {
                $variants[$device] = $this->normalizeVariant($device, $devicePayload);
            }
        }

        if (!empty($variants)) {
            return $variants;
        }

        $singlePayload = isset($response['data']) && is_array($response['data']) ? $response['data'] : $response;
        if (empty($singlePayload)) {
            return [];
        }

        $device = isset($singlePayload['device']) && $singlePayload['device'] === 'desktop' ? 'desktop' : 'mobile';

        return [
            $device => $this->normalizeVariant($device, $singlePayload),
        ];
    }

    private function normalizeVariant($device, array $payload)
    {
        $meta = isset($payload['meta']) && is_array($payload['meta']) ? $payload['meta'] : [];
        if (empty($meta)) {
            $metaKeys = ['viewport', 'element_count', 'viewport_element_count', 'scanned_element_count'];
            foreach ($metaKeys as $key) {
                if (array_key_exists($key, $payload)) {
                    $meta[$key] = $payload[$key];
                }
            }
        }

        return [
            'device' => $device,
            'generated_at' => isset($payload['generated_at'])
                ? (string) $payload['generated_at']
                : (isset($payload['scanned_at']) ? (string) $payload['scanned_at'] : date('c')),
            'generator_version' => isset($payload['generator_version']) ? (string) $payload['generator_version'] : '',
            'meta' => $meta,
            'declared_font_families' => $this->normalizeStringList(isset($payload['declared_font_families']) ? $payload['declared_font_families'] : []),
            'used_font_families' => $this->normalizeStringList(isset($payload['used_font_families']) ? $payload['used_font_families'] : []),
            'used_above_the_fold' => $this->normalizeStringList(isset($payload['used_above_the_fold']) ? $payload['used_above_the_fold'] : []),
            'google_fonts_stylesheets' => $this->normalizeStringList(isset($payload['google_fonts_stylesheets']) ? $payload['google_fonts_stylesheets'] : []),
            'duplicate_icon_font_stylesheets' => $this->normalizeDuplicateIconEntries(isset($payload['duplicate_icon_font_stylesheets']) ? $payload['duplicate_icon_font_stylesheets'] : []),
            'unused_declared_families' => $this->normalizeStringList(isset($payload['unused_declared_families']) ? $payload['unused_declared_families'] : []),
            'used_weights' => $this->normalizeScalarMap(isset($payload['used_weights']) ? $payload['used_weights'] : []),
            'used_styles' => $this->normalizeScalarMap(isset($payload['used_styles']) ? $payload['used_styles'] : []),
            'font_face_rule_count' => isset($payload['font_face_rule_count']) ? (int) $payload['font_face_rule_count'] : 0,
        ];
    }

    private function normalizeStringList($value)
    {
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $item) {
            if (!is_scalar($item)) {
                continue;
            }
            $normalized[] = trim((string) $item);
        }

        return array_values(array_filter(array_unique($normalized), function ($item) {
            return $item !== '';
        }));
    }

    private function normalizeScalarMap($value)
    {
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $key => $item) {
            if (is_scalar($item)) {
                $normalized[(string) $key] = $item;
            }
        }

        return $normalized;
    }

    private function normalizeDuplicateIconEntries($value)
    {
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];

        foreach ($value as $item) {
            if (is_scalar($item)) {
                $text = trim((string) $item);
                if ($text !== '') {
                    $normalized[] = [
                        'family' => '',
                        'href' => $text,
                        'count' => 0,
                    ];
                }
                continue;
            }

            if (!is_array($item)) {
                continue;
            }

            $href = isset($item['href']) && is_scalar($item['href']) ? trim((string) $item['href']) : '';
            $family = isset($item['family']) && is_scalar($item['family']) ? trim((string) $item['family']) : '';
            $count = isset($item['count']) ? (int) $item['count'] : 0;

            if ($href === '' && $family === '') {
                continue;
            }

            $normalized[] = [
                'family' => $family,
                'href' => $href,
                'count' => $count,
            ];
        }

        return $normalized;
    }
}
