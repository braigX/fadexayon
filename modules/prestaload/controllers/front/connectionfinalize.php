<?php

class PrestaloadConnectionfinalizeModuleFrontController extends ModuleFrontController
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
        $this->logFrontRequest('front.connectionfinalize.request');

        try {
            header('Content-Type: application/json; charset=utf-8');

            $payload = $this->assertSignedRequest();
            $this->logModuleEvent('front.connectionfinalize.authorized', [
                'store_id' => isset($payload['store_id']) ? (string) $payload['store_id'] : '',
            ]);
            $this->module->finalizeConnection((string) $payload['store_id']);
            $this->logModuleEvent('front.connectionfinalize.response', [
                'status' => 200,
                'store_id' => (string) $payload['store_id'],
            ]);

            $this->ajaxDie(json_encode([
                'success' => true,
                'message' => 'Connection finalized.',
            ]));
        } catch (Exception $exception) {
            $this->logModuleEvent('front.connectionfinalize.response', [
                'status' => 403,
                'error' => $exception->getMessage(),
            ]);

            http_response_code(403);

            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $exception->getMessage(),
            ]));
        } catch (Throwable $throwable) {
            $this->logModuleEvent('front.connectionfinalize.response', [
                'status' => 500,
                'error' => $throwable->getMessage(),
            ]);

            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $throwable->getMessage(),
            ]));
        }
    }

    private function assertSignedRequest()
    {
        $storeKey = trim((string) $this->getHeaderValue('X-PrestaBoost-Store-Key'));
        $timestamp = trim((string) $this->getHeaderValue('X-PrestaBoost-Timestamp'));
        $signature = trim((string) $this->getHeaderValue('X-PrestaBoost-Signature'));
        $configuredStoreKey = (string) Configuration::get('PRESTALOAD_STORE_KEY');
        $sharedSecret = (string) Configuration::get('PRESTALOAD_SHARED_SECRET');
        $body = $this->getRawBody();
        $payload = json_decode($body, true);

        if ($storeKey === '' || $timestamp === '' || $signature === '') {
            throw new Exception('Missing signed finalize headers.');
        }

        if ($configuredStoreKey === '' || $sharedSecret === '' || $storeKey !== $configuredStoreKey) {
            throw new Exception('Unknown store connection.');
        }

        if (!ctype_digit($timestamp) || abs(time() - (int) $timestamp) > 300) {
            throw new Exception('Signed request expired.');
        }

        if (!is_array($payload) || empty($payload['store_id']) || !is_string($payload['store_id'])) {
            throw new Exception('Invalid finalize payload.');
        }

        $signedPayload = implode("\n", [
            $timestamp,
            'POST',
            '/module/prestaload/connectionfinalize',
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

    private function logFrontRequest($event)
    {
        $body = $this->getRawBody();

        $this->logModuleEvent($event, [
            'request_uri' => isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '',
            'method' => isset($_SERVER['REQUEST_METHOD']) ? (string) $_SERVER['REQUEST_METHOD'] : '',
            'query' => isset($_SERVER['QUERY_STRING']) ? (string) $_SERVER['QUERY_STRING'] : '',
            'headers' => $this->getRequestHeadersForLog(),
            'body_bytes' => strlen($body),
            'body_preview' => $body !== '' ? Tools::substr($body, 0, 500) : '',
        ]);
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
}
