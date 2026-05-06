<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/../../src/Assets/AssetManager.php';

class PrestaloadAssetstoreModuleFrontController extends ModuleFrontController
{
    public function initContent(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed.']);
            exit;
        }

        if (! $this->verifyServerRequest()) {
            $this->module->log('warn', 'assetstore: unauthorized');
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized.']);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);

        if (! is_array($body)) {
            http_response_code(422);
            echo json_encode(['error' => 'Invalid JSON body.']);
            exit;
        }

        $siteUrl = $body['site_url'] ?? '';
        $relativePath = $body['relative_path'] ?? '';
        $contentsB64 = $body['contents'] ?? '';
        $checksum = $body['sha256_checksum'] ?? '';
        $mimeType = $body['mime_type'] ?? 'text/css';

        if (! $siteUrl || ! $relativePath || ! $contentsB64) {
            http_response_code(422);
            echo json_encode(['error' => 'Missing required fields.']);
            exit;
        }

        $contents = base64_decode($contentsB64, true);

        if ($contents === false) {
            http_response_code(422);
            echo json_encode(['error' => 'Invalid base64 contents.']);
            exit;
        }

        if ($checksum && ! hash_equals($checksum, hash('sha256', $contents))) {
            http_response_code(422);
            echo json_encode(['error' => 'Checksum mismatch.']);
            exit;
        }

        try {
            $manager = new PrestaloadAssetManager();
            $result  = $manager->store($siteUrl, $relativePath, $contents, $mimeType);
            $this->module->log('info', 'assetstore: stored', ['path' => $relativePath, 'mime' => $mimeType, 'size' => strlen($contents)]);
            echo json_encode($result);
        } catch (Throwable $e) {
            $this->module->log('error', 'assetstore: failed', ['path' => $relativePath, 'error' => $e->getMessage()]);
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }

        exit;
    }

    private function verifyServerRequest(): bool
    {
        $receivedHash = $_SERVER['HTTP_X_PRESTALOAD_KEY'] ?? '';
        $settings     = json_decode(Configuration::get(Prestaload::CONFIG_KEY, '{}'), true) ?: [];

        if (empty($settings['api_key']) || empty($receivedHash)) {
            return false;
        }

        return hash_equals(hash('sha256', $settings['api_key']), $receivedHash);
    }
}
