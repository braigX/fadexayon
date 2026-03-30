<?php

class PrestaloadCacheprepareModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $ajax = true;
    public $display_column_left = false;
    public $display_column_right = false;

    /**
     * @var string|null
     */
    private $rawBody;

    public function initContent()
    {
    }

    public function postProcess()
    {
        header('Content-Type: application/json; charset=utf-8');
        $body = $this->getRawBody();
        $payload = $body !== '' ? json_decode($body, true) : [];

        $this->module->logMessage('front.cacheprepare.request', [
            'body_bytes' => strlen($body),
            'shop_id' => is_array($payload) && isset($payload['shop_id']) ? (int) $payload['shop_id'] : 0,
            'shop_url_id' => is_array($payload) && isset($payload['shop_url_id']) ? (string) $payload['shop_url_id'] : '',
            'device_class' => is_array($payload) && isset($payload['device_class']) ? (string) $payload['device_class'] : '',
        ]);

        try {
            if (!is_array($payload)) {
                throw new Exception('Invalid cache prepare payload.');
            }

            $this->module->getSignedRequestService()->assertSignedJsonRequest(
                'POST',
                '/module/prestaload/cacheprepare',
                $body,
                $payload
            );

            $result = $this->module->getCacheContextService()->prepare($payload);

            $this->module->logMessage('front.cacheprepare.response', [
                'status' => 200,
                'cacheable' => isset($result['cacheable']) ? (bool) $result['cacheable'] : null,
                'variant_key' => isset($result['variant_key']) ? (string) $result['variant_key'] : '',
                'cache_exists' => isset($result['cache_exists']) ? (bool) $result['cache_exists'] : null,
                'shop_id' => isset($result['variant']['shop_id']) ? (int) $result['variant']['shop_id'] : 0,
            ]);

            $this->sendJsonResponse(json_encode(array_merge([
                'success' => true,
                'message' => 'Cache prepare payload ready.',
            ], $result)));
        } catch (Exception $exception) {
            $this->module->logMessage('front.cacheprepare.response', [
                'status' => 403,
                'error' => $exception->getMessage(),
            ]);
            http_response_code(403);
            $this->sendJsonResponse(json_encode([
                'success' => false,
                'message' => $exception->getMessage(),
            ]));
        } catch (Throwable $throwable) {
            $this->module->logMessage('front.cacheprepare.response', [
                'status' => 500,
                'error' => $throwable->getMessage(),
            ]);
            http_response_code(500);
            $this->sendJsonResponse(json_encode([
                'success' => false,
                'message' => $throwable->getMessage(),
            ]));
        }
    }

    private function getRawBody()
    {
        if ($this->rawBody === null) {
            $this->rawBody = (string) file_get_contents('php://input');
        }

        return $this->rawBody;
    }

    private function sendJsonResponse($json)
    {
        echo (string) $json;
        exit;
    }
}
