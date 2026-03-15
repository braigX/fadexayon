<?php
/**
 * Thin HTTP client around the remote scanner service.
 */

class PrestaLoadAssetScannerClient
{
    /**
     * @var PrestaLoadCacheSettings
     */
    private $settings;

    public function __construct(PrestaLoadCacheSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Requests a remote full scan for the given page URL and returns the
     * decoded scanner payload.
     */
    public function scanPage($url)
    {
        $endpoint = $this->settings->getAssetScannerBaseUrl() . '/scan';
        $payload = json_encode(['url' => (string) $url]);

        if ($payload === false) {
            throw new Exception('Could not encode scanner payload.');
        }

        if (function_exists('curl_init')) {
            return $this->sendWithCurl($endpoint, $payload);
        }

        return $this->sendWithStreams($endpoint, $payload);
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

        $responseBody = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($responseBody === false) {
            throw new Exception('Scanner request failed: ' . $error);
        }

        return $this->decodeResponse($responseBody, $httpCode);
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

        $responseBody = @file_get_contents($endpoint, false, $context);
        $httpCode = 0;
        if (!empty($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches)) {
            $httpCode = (int) $matches[1];
        }

        if ($responseBody === false) {
            throw new Exception('Scanner request failed.');
        }

        return $this->decodeResponse($responseBody, $httpCode);
    }

    private function decodeResponse($responseBody, $httpCode)
    {
        $decoded = json_decode((string) $responseBody, true);
        if (!is_array($decoded)) {
            throw new Exception('Scanner returned invalid JSON.');
        }

        if ($httpCode >= 400 || empty($decoded['success'])) {
            throw new Exception(isset($decoded['message']) ? (string) $decoded['message'] : 'Scanner request failed.');
        }

        return $decoded;
    }
}
