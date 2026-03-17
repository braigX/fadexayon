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

    public function __construct(PrestaLoadCacheSettings $settings)
    {
        $this->settings = $settings;
    }

    public function generate($pageType, $url)
    {
        $endpoint = $this->settings->getAssetScannerBaseUrl() . '/critical-css';
        $payload = json_encode([
            'url' => (string) $url,
            'page_type' => (string) $pageType,
        ]);

        if ($payload === false) {
            throw new Exception('Could not encode the critical CSS payload.');
        }

        $response = function_exists('curl_init')
            ? $this->sendWithCurl($endpoint, $payload)
            : $this->sendWithStreams($endpoint, $payload);

        $css = $this->extractCss($response);
        if ($css === '') {
            throw new Exception('The scanner did not return any critical CSS.');
        }

        return [
            'css' => $css,
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

        if ($httpCode >= 400 || (isset($decoded['success']) && empty($decoded['success']))) {
            throw new Exception(isset($decoded['message']) ? (string) $decoded['message'] : 'Critical CSS request failed.');
        }

        return $decoded;
    }

    private function extractCss(array $response)
    {
        $candidates = [
            isset($response['css']) ? $response['css'] : '',
            isset($response['critical_css']) ? $response['critical_css'] : '',
            isset($response['data']['css']) ? $response['data']['css'] : '',
            isset($response['data']['critical_css']) ? $response['data']['critical_css'] : '',
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        return '';
    }
}
