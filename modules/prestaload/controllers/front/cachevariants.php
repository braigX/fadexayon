<?php

class PrestaloadCachevariantsModuleFrontController extends ModuleFrontController
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

        $this->module->logMessage('front.cachevariants.request', [
            'body_bytes' => strlen($body),
            'shop_id' => is_array($payload) && isset($payload['shop_id']) ? (int) $payload['shop_id'] : 0,
        ]);

        try {
            if (!is_array($payload)) {
                throw new Exception('Invalid cache variants payload.');
            }

            $this->module->getSignedRequestService()->assertSignedJsonRequest(
                'POST',
                '/module/prestaload/cachevariants',
                $body,
                $payload
            );

            $result = $this->module->getCacheVariantService()->describe($payload);

            $this->module->logMessage('front.cachevariants.response', [
                'status' => 200,
                'shop_id' => isset($result['shop_id']) ? (int) $result['shop_id'] : 0,
                'variants_count' => isset($result['variants_count']) ? (int) $result['variants_count'] : 0,
                'cache_hit' => !empty($result['cache_hit']),
            ]);

            $this->sendJsonResponse(json_encode(array_merge([
                'success' => true,
                'message' => 'Cache variants payload ready.',
            ], $result)));
        } catch (Exception $exception) {
            $this->module->logMessage('front.cachevariants.response', [
                'status' => 403,
                'error' => $exception->getMessage(),
            ]);
            http_response_code(403);
            $this->sendJsonResponse(json_encode([
                'success' => false,
                'message' => $exception->getMessage(),
            ]));
        } catch (Throwable $throwable) {
            $this->module->logMessage('front.cachevariants.response', [
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
