<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

/**
 * Front controller: POST /module/prestaload/cachestore
 *
 * Receives optimized HTML from the Prestaload control plane and writes it to
 * the local file cache. Validates authentication and SHA-256 checksum before writing.
 *
 * Payload (JSON body):
 *   url, variant_hash, rules_version, html (base64), ttl_seconds,
 *   sha256_checksum, cached_at
 */
class PrestaloadCachestoreModuleFrontController extends ModuleFrontController
{
    private const CACHE_DIR = _PS_ROOT_DIR_ . '/var/prestaload-cache';

    public function initContent(): void
    {
        header('Content-Type: application/json');

        $this->module->log('info', 'cachestore: request received', [
            'method' => $_SERVER['REQUEST_METHOD'],
            'auth'   => isset($_SERVER['HTTP_X_PRESTALOAD_KEY']) ? 'present' : 'missing',
        ]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->module->log('warn', 'cachestore: method not allowed', ['method' => $_SERVER['REQUEST_METHOD']]);
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed.']);
            exit;
        }

        if (! $this->verifyServerRequest()) {
            $this->module->log('warn', 'cachestore: unauthorized');
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized.']);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);

        if (! $body) {
            $this->module->log('warn', 'cachestore: invalid JSON body');
            http_response_code(422);
            echo json_encode(['error' => 'Invalid JSON body.']);
            exit;
        }

        $url               = $body['url'] ?? '';
        $variantHash       = $body['variant_hash'] ?? '';
        $variantDimensions = is_array($body['variant_dimensions'] ?? null) ? $body['variant_dimensions'] : [];
        $targetShopId      = isset($variantDimensions['shop']) && $variantDimensions['shop'] !== ''
            ? (int) $variantDimensions['shop']
            : null;
        $rulesVersion      = $body['rules_version'] ?? 'v1';
        $htmlB64           = $body['html'] ?? '';
        $checksum          = $body['sha256_checksum'] ?? '';
        $ttl               = max(60, (int) ($body['ttl_seconds'] ?? 86400));

        $this->module->log('info', 'cachestore: payload parsed', [
            'url'          => $url,
            'variant_hash' => $variantHash,
            'rules_version'=> $rulesVersion,
            'ttl_seconds'  => $ttl,
            'html_b64_len' => strlen($htmlB64),
            'checksum'     => $checksum,
        ]);

        if (! $url || ! $variantHash || ! $htmlB64) {
            $this->module->log('warn', 'cachestore: missing required fields', ['url' => $url, 'variant_hash' => $variantHash]);
            http_response_code(422);
            echo json_encode(['error' => 'Missing required fields.']);
            exit;
        }

        $html = base64_decode($htmlB64, true);
        if ($html === false) {
            $this->module->log('warn', 'cachestore: invalid base64 HTML', ['url' => $url]);
            http_response_code(422);
            echo json_encode(['error' => 'Invalid base64 HTML.']);
            exit;
        }

        if ($checksum && ! hash_equals($checksum, hash('sha256', $html))) {
            $this->module->log('warn', 'cachestore: checksum mismatch', [
                'url'      => $url,
                'expected' => $checksum,
                'actual'   => hash('sha256', $html),
            ]);
            http_response_code(422);
            echo json_encode(['error' => 'Checksum mismatch.']);
            exit;
        }

        $path = $this->cachePath($url, $variantHash);
        $dir  = dirname($path);

        if (! is_dir($dir) && ! mkdir($dir, 0755, true)) {
            $this->module->log('error', 'cachestore: cache directory not writable', ['dir' => $dir]);
            http_response_code(500);
            echo json_encode(['error' => 'Cache directory not writable.']);
            exit;
        }

        $meta = [
            'rules_version' => $rulesVersion,
            'cached_at'     => time(),
            'expires_at'    => time() + $ttl,
            'checksum'      => $checksum,
        ];

        file_put_contents($path . '.html', $html, LOCK_EX);
        file_put_contents($path . '.meta', json_encode($meta), LOCK_EX);

        if ($variantHash && $variantDimensions) {
            $this->module->updateVariantMap($variantHash, $variantDimensions, $targetShopId);
        }

        $this->module->log('info', 'cachestore: stored', [
            'url'          => $url,
            'variant_hash' => $variantHash,
            'html_size'    => strlen($html),
            'expires_at'   => $meta['expires_at'],
            'path'         => $path,
        ]);

        echo json_encode(['stored' => true]);
        exit;
    }

    private function cachePath(string $url, string $variantHash): string
    {
        $urlHash = md5($this->normalizeUrl($url));
        $shard   = substr($urlHash, 0, 2);

        return self::CACHE_DIR . "/{$shard}/{$urlHash}/{$variantHash}";
    }

    private function normalizeUrl(string $url): string
    {
        $parsed = parse_url(strtolower(rtrim($url, '/')));
        $host   = $parsed['host'] ?? '';
        $path   = $parsed['path'] ?? '';

        return $host . ($path !== '' && $path !== '/' ? rtrim($path, '/') : '');
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
