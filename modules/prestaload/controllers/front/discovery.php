<?php

class PrestaloadDiscoveryModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $ajax = true;
    public $display_column_left = false;
    public $display_column_right = false;

    public function initContent()
    {
    }

    public function postProcess()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $this->assertSignedRequest();

            $this->ajaxDie(json_encode([
                'success' => true,
                'message' => 'Shop discovery payload ready.',
                'current_shop_id' => (int) $this->module->getCurrentShopId(),
                'shops' => $this->module->getDetectedShops(),
            ]));
        } catch (Exception $exception) {
            http_response_code(403);

            $this->ajaxDie(json_encode([
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

        if ($storeId === '' || $timestamp === '' || $signature === '') {
            throw new Exception('Missing signed discovery headers.');
        }

        if ($configuredStoreId === '' || $sharedSecret === '' || $storeId !== $configuredStoreId) {
            throw new Exception('Unknown store connection.');
        }

        if (!ctype_digit($timestamp) || abs(time() - (int) $timestamp) > 300) {
            throw new Exception('Signed request expired.');
        }

        $payload = implode("\n", [
            $timestamp,
            'GET',
            '/module/prestaload/discovery',
            hash('sha256', ''),
        ]);
        $expected = hash_hmac('sha256', $payload, $sharedSecret);

        if (!hash_equals($expected, $signature)) {
            throw new Exception('Invalid request signature.');
        }
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
