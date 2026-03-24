<?php

class PrestaloadConnectionfinalizeModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $ajax = true;
    public $display_column_left = false;
    public $display_column_right = false;

    public function initContent()
    {
        parent::initContent();

        header('Content-Type: application/json; charset=utf-8');

        $this->module->logMessage('connection.finalize_controller.received', [
            'request_uri' => isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '',
            'method' => isset($_SERVER['REQUEST_METHOD']) ? (string) $_SERVER['REQUEST_METHOD'] : '',
        ]);

        try {
            $payload = $this->assertSignedRequest();
            $this->module->finalizeConnection((string) $payload['store_id']);

            $this->module->logMessage('connection.finalize_controller.success', [
                'store_id' => (string) $payload['store_id'],
            ]);

            $this->ajaxRender(json_encode([
                'success' => true,
                'message' => 'Connection finalized.',
            ]));
        } catch (Exception $exception) {
            $this->module->logMessage('connection.finalize_controller.failed', [
                'message' => $exception->getMessage(),
            ]);
            http_response_code(403);

            $this->ajaxRender(json_encode([
                'success' => false,
                'message' => $exception->getMessage(),
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
        $body = (string) file_get_contents('php://input');
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
}
