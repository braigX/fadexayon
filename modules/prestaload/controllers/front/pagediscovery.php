<?php

class PrestaloadPagediscoveryModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $ajax = true;
    public $display_column_left = false;
    public $display_column_right = false;
    private $rawBody;

    public function initContent()
    {
    }

    public function postProcess()
    {
        header('Content-Type: application/json; charset=utf-8');
        $body = $this->getRawBody();
        $this->logModuleEvent('front.pagediscovery.request', [
            'request_uri' => isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '',
            'method' => isset($_SERVER['REQUEST_METHOD']) ? (string) $_SERVER['REQUEST_METHOD'] : '',
            'query' => isset($_SERVER['QUERY_STRING']) ? (string) $_SERVER['QUERY_STRING'] : '',
            'headers' => $this->getRequestHeadersForLog(),
            'body_bytes' => strlen($body),
            'body_preview' => $body !== '' ? Tools::substr($body, 0, 500) : '',
        ]);

        try {
            $payload = $this->assertSignedRequest();
            $shopId = isset($payload['shop_id']) ? (int) $payload['shop_id'] : 0;
            $pageType = isset($payload['page_type']) ? (string) $payload['page_type'] : null;
            $page = isset($payload['page']) ? (int) $payload['page'] : 1;
            $perPage = isset($payload['per_page']) ? (int) $payload['per_page'] : 250;
            $result = $this->module->getDiscoveredPageUrls($shopId > 0 ? $shopId : null, $pageType, $page, $perPage);

            $this->logModuleEvent('front.pagediscovery.response', [
                'status' => 200,
                'shop_id' => $shopId,
                'page_type' => $pageType,
                'page' => (int) $result['page'],
                'per_page' => (int) $result['per_page'],
                'total' => (int) $result['total'],
                'has_more' => !empty($result['has_more']),
                'pages_count' => isset($result['items']) && is_array($result['items']) ? count($result['items']) : 0,
            ]);

            $this->sendJsonResponse(json_encode([
                'success' => true,
                'message' => 'Page discovery payload ready.',
                'shop_id' => $shopId,
                'page_type' => $pageType,
                'page' => $result['page'],
                'per_page' => $result['per_page'],
                'total' => $result['total'],
                'has_more' => $result['has_more'],
                'pages' => $result['items'],
            ]));
        } catch (Exception $exception) {
            $this->logModuleEvent('front.pagediscovery.response', [
                'status' => 403,
                'error' => $exception->getMessage(),
            ]);
            http_response_code(403);

            $this->sendJsonResponse(json_encode([
                'success' => false,
                'message' => $exception->getMessage(),
            ]));
        }
    }

    private function assertSignedRequest()
    {
        $storeId = trim((string) $this->getHeaderValue('X-PrestaBoost-Store'));
        $timestamp = trim((string) $this->getHeaderValue('X-PrestaBoost-Timestamp'));
        $signature = trim((string) $this->getHeaderValue('X-PrestaBoost-Signature'));
        $configuredStoreId = (string) Configuration::get('PRESTALOAD_STORE_ID');
        $sharedSecret = (string) Configuration::get('PRESTALOAD_SHARED_SECRET');
        $body = $this->getRawBody();
        $payload = $body !== '' ? json_decode($body, true) : [];

        if ($storeId === '' || $timestamp === '' || $signature === '') {
            throw new Exception('Missing signed page discovery headers.');
        }

        if ($configuredStoreId === '' || $sharedSecret === '' || $storeId !== $configuredStoreId) {
            throw new Exception('Unknown store connection.');
        }

        if (!ctype_digit($timestamp) || abs(time() - (int) $timestamp) > 300) {
            throw new Exception('Signed request expired.');
        }

        if (!is_array($payload)) {
            throw new Exception('Invalid page discovery payload.');
        }

        $signedPayload = implode("\n", [
            $timestamp,
            'POST',
            '/module/prestaload/pagediscovery',
            hash('sha256', $body),
        ]);
        $expected = hash_hmac('sha256', $signedPayload, $sharedSecret);

        if (!hash_equals($expected, $signature)) {
            throw new Exception('Invalid request signature.');
        }

        return $payload;
    }

    private function getHeaderValue($name)
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        if (is_array($headers)) {
            foreach ($headers as $headerName => $headerValue) {
                if (strcasecmp((string) $headerName, (string) $name) === 0) {
                    return $headerValue;
                }
            }
        }

        $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', (string) $name));

        return $_SERVER[$serverKey] ?? '';
    }

    private function getRequestHeadersForLog()
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        if (!is_array($headers)) {
            return [];
        }

        $result = [];
        foreach ($headers as $name => $value) {
            $key = (string) $name;
            if (strcasecmp($key, 'X-PrestaBoost-Signature') === 0) {
                $result[$key] = Tools::substr((string) $value, 0, 16);
                continue;
            }
            $result[$key] = (string) $value;
        }

        return $result;
    }

    private function getRawBody()
    {
        if ($this->rawBody === null) {
            $this->rawBody = (string) file_get_contents('php://input');
        }

        return $this->rawBody;
    }

    private function logModuleEvent($event, array $context = [])
    {
        if (is_object($this->module) && method_exists($this->module, 'logMessage')) {
            $this->module->logMessage($event, $context);
        }
    }

    private function sendJsonResponse($json)
    {
        echo (string) $json;
        exit;
    }
}
