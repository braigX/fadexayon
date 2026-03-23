<?php
/**
 * Very small file-backed store for cached page payloads.
 */

class PrestaLoadCacheStore
{
    private $baseDirectory;

    public function __construct($baseDirectory)
    {
        $this->baseDirectory = rtrim((string) $baseDirectory, '/');
    }

    public function get($key)
    {
        $path = $this->buildPath($key);
        if (!is_file($path)) {
            return null;
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        if (!is_array($decoded)) {
            @unlink($path);
            return null;
        }

        if (isset($decoded['expires_at']) && time() >= (int) $decoded['expires_at']) {
            @unlink($path);
            return null;
        }

        return $decoded;
    }

    public function put($key, array $payload, $ttl)
    {
        $path = $this->buildPath($key);
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $payload['stored_at'] = time();
        $payload['expires_at'] = time() + max(60, (int) $ttl);

        $json = json_encode($payload);
        if ($json === false) {
            return false;
        }

        $tempPath = $path . '.tmp';
        if (file_put_contents($tempPath, $json, LOCK_EX) === false) {
            return false;
        }

        return rename($tempPath, $path);
    }

    public function clear()
    {
        if (!is_dir($this->baseDirectory)) {
            return true;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->baseDirectory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                @rmdir($fileInfo->getPathname());
            } else {
                @unlink($fileInfo->getPathname());
            }
        }

        return true;
    }

    public function getStats()
    {
        $count = 0;
        $size = 0;
        $pages = [];

        if (!is_dir($this->baseDirectory)) {
            return [
                'directory' => $this->baseDirectory,
                'count' => 0,
                'size_bytes' => 0,
                'pages' => [],
            ];
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->baseDirectory, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile()) {
                $count++;
                $size += (int) $fileInfo->getSize();
                $payload = json_decode((string) @file_get_contents($fileInfo->getPathname()), true);
                $pages[] = [
                    'cache_key' => basename($fileInfo->getFilename(), '.json'),
                    'path' => $fileInfo->getPathname(),
                    'size_bytes' => (int) $fileInfo->getSize(),
                    'controller' => isset($payload['controller']) ? (string) $payload['controller'] : '',
                    'request_uri' => isset($payload['request_uri']) ? (string) $payload['request_uri'] : '',
                    'cache_parts' => isset($payload['cache_parts']) && is_array($payload['cache_parts']) ? $payload['cache_parts'] : [],
                    'early_alias' => !empty($payload['early_alias']),
                    'status_code' => isset($payload['status_code']) ? (int) $payload['status_code'] : 0,
                    'stored_at' => isset($payload['stored_at']) ? (int) $payload['stored_at'] : 0,
                    'expires_at' => isset($payload['expires_at']) ? (int) $payload['expires_at'] : 0,
                ];
            }
        }

        usort($pages, function ($left, $right) {
            return ($right['stored_at'] ?? 0) <=> ($left['stored_at'] ?? 0);
        });

        $groupedPages = $this->groupPagesByRequest($pages);

        return [
            'directory' => $this->baseDirectory,
            'count' => $count,
            'size_bytes' => $size,
            'pages' => $pages,
            'grouped_pages' => $groupedPages,
        ];
    }

    private function groupPagesByRequest(array $pages)
    {
        $groups = [];

        foreach ($pages as $page) {
            $requestUri = isset($page['request_uri']) && $page['request_uri'] !== ''
                ? (string) $page['request_uri']
                : '/' . (string) ($page['controller'] ?: 'unknown');
            $groupKey = $requestUri;

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'request_uri' => $requestUri,
                    'controller' => isset($page['controller']) ? (string) $page['controller'] : '',
                    'total_size_bytes' => 0,
                    'variants' => [],
                ];
            }

            $groups[$groupKey]['total_size_bytes'] += (int) ($page['size_bytes'] ?? 0);
            $groups[$groupKey]['variants'][] = $page;
        }

        foreach ($groups as &$group) {
            usort($group['variants'], function ($left, $right) {
                return ($right['stored_at'] ?? 0) <=> ($left['stored_at'] ?? 0);
            });
        }
        unset($group);

        return array_values($groups);
    }

    private function buildPath($key)
    {
        $prefix = substr((string) $key, 0, 2);

        return $this->baseDirectory . '/' . $prefix . '/' . $key . '.json';
    }
}
