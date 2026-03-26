<?php

class PrestaloadCachepurgeModuleFrontController extends ModuleFrontController
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

        $this->module->logMessage('front.cachepurge.request', [
            'body_bytes' => strlen($body),
            'variant_key' => is_array($payload) && isset($payload['variant_key']) ? (string) $payload['variant_key'] : '',
        ]);

        try {
            if (!is_array($payload)) {
                throw new Exception('Invalid cache purge payload.');
            }

            $this->module->getSignedRequestService()->assertSignedJsonRequest(
                'POST',
                '/module/prestaload/cachepurge',
                $body,
                $payload
            );

            $result = $this->module->getCacheStoreService()->purge($payload);

            $this->module->logMessage('front.cachepurge.response', [
                'status' => 200,
                'variant_key' => isset($payload['variant_key']) ? (string) $payload['variant_key'] : '',
                'purged' => (bool) (isset($result['purged']) ? $result['purged'] : false),
            ]);

            $this->ajaxDie(json_encode([
                'success' => true,
                'message' => 'Cache entry purged.',
                'purged' => (bool) (isset($result['purged']) ? $result['purged'] : false),
                'deleted_html' => (bool) (isset($result['deleted_html']) ? $result['deleted_html'] : false),
                'deleted_meta' => (bool) (isset($result['deleted_meta']) ? $result['deleted_meta'] : false),
                'html_path' => isset($result['html_path']) ? $result['html_path'] : '',
                'meta_path' => isset($result['meta_path']) ? $result['meta_path'] : '',
            ]));
        } catch (Exception $exception) {
            $this->module->logMessage('front.cachepurge.response', [
                'status' => 403,
                'error' => $exception->getMessage(),
            ]);
            http_response_code(403);
            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $exception->getMessage(),
            ]));
        } catch (Throwable $throwable) {
            $this->module->logMessage('front.cachepurge.response', [
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
