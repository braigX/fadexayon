<?php

class PrestaLoadSignedRequestService
{
    /**
     * @var Prestaload
     */
    private $module;

    public function __construct(Prestaload $module)
    {
        $this->module = $module;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public function assertSignedJsonRequest($expectedMethod, $expectedPath, $rawBody, array $payload = [])
    {
        $storeId = trim((string) $this->getHeaderValue('X-PrestaBoost-Store'));
        $timestamp = trim((string) $this->getHeaderValue('X-PrestaBoost-Timestamp'));
        $signature = trim((string) $this->getHeaderValue('X-PrestaBoost-Signature'));
        $configuredStoreId = (string) Configuration::get('PRESTALOAD_STORE_ID');
        $sharedSecret = (string) Configuration::get('PRESTALOAD_SHARED_SECRET');

        if ($storeId === '' || $timestamp === '' || $signature === '') {
            throw new Exception('Missing signed request headers.');
        }

        if ($configuredStoreId === '' || $sharedSecret === '' || $storeId !== $configuredStoreId) {
            throw new Exception('Unknown store connection.');
        }

        if (!ctype_digit($timestamp) || abs(time() - (int) $timestamp) > 300) {
            throw new Exception('Signed request expired.');
        }

        $signedPayload = implode("\n", [
            $timestamp,
            strtoupper((string) $expectedMethod),
            (string) $expectedPath,
            hash('sha256', (string) $rawBody),
        ]);
        $expected = hash_hmac('sha256', $signedPayload, $sharedSecret);

        if (!hash_equals($expected, $signature)) {
            throw new Exception('Invalid request signature.');
        }

        return $payload;
    }

    /**
     * @return array<string, string>
     */
    public function getRequestHeadersForLog()
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

    /**
     * @return mixed
     */
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

        return isset($_SERVER[$serverKey]) ? $_SERVER[$serverKey] : '';
    }
}
