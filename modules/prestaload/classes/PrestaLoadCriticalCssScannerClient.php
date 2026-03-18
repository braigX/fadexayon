<?php
/**
 * Requests beta critical CSS payloads from the remote scanner service.
 */

class PrestaLoadCriticalCssScannerClient
{
    /**
     * @var PrestaLoadCacheSettings
     */
    private $settings;
    /**
     * @var PrestaLoadCriticalCssLogger|null
     */
    private $logger;

    public function __construct(PrestaLoadCacheSettings $settings, $logger = null)
    {
        $this->settings = $settings;
        $this->logger = $logger;
    }

    public function generate($pageType, $url)
    {
        $endpoint = $this->settings->getAssetScannerBaseUrl() . '/critical-css';
        $payload = json_encode([
            'url' => (string) $url,
            'page_type' => (string) $pageType,
            'device' => 'both',
        ]);

        if ($payload === false) {
            throw new Exception('Could not encode the critical CSS payload.');
        }

        $this->log([
            'stage' => 'scanner_request',
            'page_type' => (string) $pageType,
            'url' => (string) $url,
            'endpoint' => $endpoint,
            'payload' => json_decode($payload, true),
        ]);

        $response = function_exists('curl_init')
            ? $this->sendWithCurl($endpoint, $payload)
            : $this->sendWithStreams($endpoint, $payload);

        $variants = $this->extractVariants($response);
        $this->log([
            'stage' => 'scanner_response',
            'page_type' => (string) $pageType,
            'success' => isset($response['success']) ? (bool) $response['success'] : null,
            'message' => isset($response['message']) ? (string) $response['message'] : '',
            'devices' => array_keys($variants),
            'variant_sizes' => $this->collectVariantSizes($variants),
        ]);

        if (empty($variants)) {
            throw new Exception('The scanner did not return any critical CSS.');
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
            throw new Exception('Critical CSS request failed: ' . $error);
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
            throw new Exception('Critical CSS request failed.');
        }

        return $this->decodeResponse($body, $httpCode);
    }

    private function decodeResponse($body, $httpCode)
    {
        $decoded = json_decode((string) $body, true);
        if (!is_array($decoded)) {
            throw new Exception('Critical CSS scanner returned invalid JSON.');
        }

        $this->log([
            'stage' => 'scanner_http',
            'http_code' => (int) $httpCode,
            'success' => isset($decoded['success']) ? (bool) $decoded['success'] : null,
            'message' => isset($decoded['message']) ? (string) $decoded['message'] : '',
        ]);

        if ($httpCode >= 400 || (isset($decoded['success']) && empty($decoded['success']))) {
            throw new Exception(isset($decoded['message']) ? (string) $decoded['message'] : 'Critical CSS request failed.');
        }

        return $decoded;
    }

    private function extractVariants(array $response)
    {
        $variants = [];

        foreach (['mobile', 'tablet', 'desktop'] as $device) {
            $devicePayload = isset($response['data'][$device]) && is_array($response['data'][$device])
                ? $response['data'][$device]
                : [];

            $css = $this->extractCssFromPayload($devicePayload);
            if ($css !== '') {
                $variants[$device] = [
                    'device' => $device,
                    'css' => $css,
                    'css_size_bytes' => isset($devicePayload['css_size_bytes']) ? (int) $devicePayload['css_size_bytes'] : strlen($css),
                    'generated_at' => isset($devicePayload['generated_at']) ? (string) $devicePayload['generated_at'] : date('c'),
                    'generator_version' => isset($devicePayload['generator_version']) ? (string) $devicePayload['generator_version'] : '',
                    'meta' => isset($devicePayload['meta']) && is_array($devicePayload['meta']) ? $devicePayload['meta'] : [],
                ];
            }
        }

        if (!empty($variants)) {
            return $variants;
        }

        $singlePayload = isset($response['data']) && is_array($response['data']) ? $response['data'] : $response;
        $singleCss = $this->extractCssFromPayload($singlePayload);
        if ($singleCss === '') {
            return [];
        }

        $device = 'mobile';
        if (isset($singlePayload['device']) && in_array($singlePayload['device'], ['mobile', 'tablet', 'desktop'], true)) {
            $device = (string) $singlePayload['device'];
        }

        return [
            $device => [
                'device' => $device,
                'css' => $singleCss,
                'css_size_bytes' => isset($singlePayload['css_size_bytes']) ? (int) $singlePayload['css_size_bytes'] : strlen($singleCss),
                'generated_at' => isset($singlePayload['generated_at']) ? (string) $singlePayload['generated_at'] : date('c'),
                'generator_version' => isset($singlePayload['generator_version']) ? (string) $singlePayload['generator_version'] : '',
                'meta' => isset($singlePayload['meta']) && is_array($singlePayload['meta']) ? $singlePayload['meta'] : [],
            ],
        ];
    }

    private function extractCssFromPayload(array $payload)
    {
        $candidates = [
            isset($payload['css_content']) ? $payload['css_content'] : '',
            isset($payload['css']) ? $payload['css'] : '',
            isset($payload['critical_css']) ? $payload['critical_css'] : '',
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        return '';
    }

    private function collectVariantSizes(array $variants)
    {
        $sizes = [];
        foreach ($variants as $device => $variant) {
            $sizes[$device] = isset($variant['css_size_bytes']) ? (int) $variant['css_size_bytes'] : strlen(isset($variant['css']) ? (string) $variant['css'] : '');
        }

        return $sizes;
    }

    private function log(array $entry)
    {
        if ($this->logger) {
            $this->logger->log($entry);
        }
    }
}
