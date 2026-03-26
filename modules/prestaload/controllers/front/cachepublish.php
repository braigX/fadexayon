<?php

class PrestaloadCachepublishModuleFrontController extends ModuleFrontController
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

        $this->module->logMessage('front.cachepublish.request', [
            'body_bytes' => strlen($body),
            'variant_key' => is_array($payload) && isset($payload['variant_key']) ? (string) $payload['variant_key'] : '',
            'shop_id' => is_array($payload) && isset($payload['variant']['shop_id']) ? (int) $payload['variant']['shop_id'] : 0,
            'html_bytes' => is_array($payload) && isset($payload['html_bytes']) ? (int) $payload['html_bytes'] : 0,
        ]);

        try {
            if (!is_array($payload)) {
                throw new Exception('Invalid cache publish payload.');
            }

            $this->module->getSignedRequestService()->assertSignedJsonRequest(
                'POST',
                '/module/prestaload/cachepublish',
                $body,
                $payload
            );

            $result = $this->module->getCacheStoreService()->publish($payload);

            $this->module->logMessage('front.cachepublish.response', [
                'status' => 200,
                'variant_key' => isset($payload['variant_key']) ? (string) $payload['variant_key'] : '',
                'bytes' => isset($result['bytes']) ? (int) $result['bytes'] : 0,
                'shop_id' => isset($payload['variant']['shop_id']) ? (int) $payload['variant']['shop_id'] : 0,
            ]);

            $this->ajaxDie(json_encode([
                'success' => true,
                'message' => 'Cache entry stored.',
                'stored' => (bool) (isset($result['stored']) ? $result['stored'] : false),
                'path' => isset($result['path']) ? $result['path'] : '',
                'meta_path' => isset($result['meta_path']) ? $result['meta_path'] : '',
                'bytes' => isset($result['bytes']) ? $result['bytes'] : 0,
            ]));
        } catch (Exception $exception) {
            $this->module->logMessage('front.cachepublish.response', [
                'status' => 403,
                'error' => $exception->getMessage(),
            ]);
            http_response_code(403);
            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $exception->getMessage(),
            ]));
        } catch (Throwable $throwable) {
            $this->module->logMessage('front.cachepublish.response', [
                'status' => 500,
                'error' => $throwable->getMessage(),
            ]);
            http_response_code(500);
            $this->ajaxDie(json_encode([
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
}
