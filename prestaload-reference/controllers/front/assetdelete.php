<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/../../src/Assets/AssetManager.php';

class PrestaloadAssetdeleteModuleFrontController extends ModuleFrontController
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

        $paths = $body['storage_paths'] ?? [];
        $paths = is_array($paths) ? $paths : [];

        try {
            $manager = new PrestaloadAssetManager();
            echo json_encode($manager->deleteMany($paths));
        } catch (Throwable $e) {
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
