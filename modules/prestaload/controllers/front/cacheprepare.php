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
            'request_uri' => isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '',
            'method' => isset($_SERVER['REQUEST_METHOD']) ? (string) $_SERVER['REQUEST_METHOD'] : '',
            'query' => isset($_SERVER['QUERY_STRING']) ? (string) $_SERVER['QUERY_STRING'] : '',
            'headers' => $this->module->getSignedRequestService()->getRequestHeadersForLog(),
            'body_bytes' => strlen($body),
            'body_preview' => $body !== '' ? Tools::substr($body, 0, 500) : '',
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
            ]);

            $this->ajaxDie(json_encode(array_merge([
                'success' => true,
                'message' => 'Cache prepare payload ready.',
            ], $result)));
        } catch (Exception $exception) {
            $this->module->logMessage('front.cacheprepare.response', [
                'status' => 403,
                'error' => $exception->getMessage(),
            ]);
            http_response_code(403);
            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $exception->getMessage(),
            ]));
        } catch (Throwable $throwable) {
            $this->module->logMessage('front.cacheprepare.response', [
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
