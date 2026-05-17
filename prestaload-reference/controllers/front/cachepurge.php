<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__DIR__, 2) . '/src/Cache/CacheManager.php';

class PrestaloadCachepurgeModuleFrontController extends ModuleFrontController
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

        $purgeEverything = (bool) ($body['purge_everything'] ?? false);
        $scope = isset($body['scope']) ? (string) $body['scope'] : '';
        $urls = isset($body['urls']) && is_array($body['urls']) ? $body['urls'] : [];

        $manager = new PrestaloadCacheManager();

        if ($purgeEverything || $scope === 'site_all') {
            $summary = $manager->purgeAll();

            PrestaloadLogger::write('info', 'cachepurge: completed full purge', [
                'deleted_html' => $summary['deleted_html'] ?? 0,
                'deleted_meta' => $summary['deleted_meta'] ?? 0,
                'deleted_dirs' => $summary['deleted_dirs'] ?? 0,
            ]);

            echo json_encode($summary);
            exit;
        }

        if ($urls === []) {
            http_response_code(422);
            echo json_encode(['error' => 'Missing URLs.']);
            exit;
        }

        $summary = $manager->purgeUrls($urls);

        PrestaloadLogger::write('info', 'cachepurge: completed', [
            'urls' => count($urls),
            'deleted_html' => $summary['deleted_html'] ?? 0,
            'deleted_meta' => $summary['deleted_meta'] ?? 0,
            'deleted_dirs' => $summary['deleted_dirs'] ?? 0,
        ]);

        echo json_encode($summary);
        exit;
    }

    private function verifyServerRequest(): bool
    {
        $receivedHash = $_SERVER['HTTP_X_PRESTALOAD_KEY'] ?? '';
        $settings = json_decode(Configuration::get(Prestaload::CONFIG_KEY, '{}'), true) ?: [];

        if (empty($settings['api_key']) || empty($receivedHash)) {
            return false;
        }

        return hash_equals(hash('sha256', $settings['api_key']), $receivedHash);
    }
}
