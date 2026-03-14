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

        if (!is_dir($this->baseDirectory)) {
            return [
                'directory' => $this->baseDirectory,
                'count' => 0,
                'size_bytes' => 0,
            ];
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->baseDirectory, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile()) {
                $count++;
                $size += (int) $fileInfo->getSize();
            }
        }

        return [
            'directory' => $this->baseDirectory,
            'count' => $count,
            'size_bytes' => $size,
        ];
    }

    private function buildPath($key)
    {
        $prefix = substr((string) $key, 0, 2);

        return $this->baseDirectory . '/' . $prefix . '/' . $key . '.json';
    }
}
