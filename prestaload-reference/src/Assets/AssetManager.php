<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

class PrestaloadAssetManager
{
    private string $assetsDir;

    public function __construct(?string $assetsDir = null)
    {
        $this->assetsDir = $assetsDir ?: _PS_ROOT_DIR_ . '/modules/prestaload/assets/generated';
    }

    public function store(string $siteUrl, string $relativePath, string $contents, string $mimeType = 'text/css'): array
    {
        $relativePath = ltrim(str_replace(['..\\', '../', '\\'], ['', '', '/'], $relativePath), '/');
        $path = $this->assetsDir . '/' . $relativePath;
        $directory = dirname($path);

        if (! is_dir($directory) && ! @mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Asset directory not writable.');
        }

        file_put_contents($path, $contents, LOCK_EX);

        return [
            'stored' => true,
            'public_url' => rtrim($siteUrl, '/') . '/modules/prestaload/assets/generated/' . str_replace(DIRECTORY_SEPARATOR, '/', $relativePath),
            'storage_path' => $path,
            'bytes' => strlen($contents),
            'mime_type' => $mimeType,
        ];
    }

    public function deleteMany(array $paths): array
    {
        $deleted = 0;

        foreach ($paths as $path) {
            $resolved = $this->safePath($path);

            if (! $resolved || ! is_file($resolved)) {
                continue;
            }

            if (@unlink($resolved)) {
                $deleted++;
            }
        }

        return [
            'deleted_files' => $deleted,
        ];
    }

    private function safePath(string $path): ?string
    {
        $assetsRoot = rtrim(str_replace('\\', '/', $this->assetsDir), '/');
        $candidate = str_replace('\\', '/', trim($path));

        if ($candidate === '') {
            return null;
        }

        if (! str_starts_with($candidate, '/')) {
            $candidate = $assetsRoot . '/' . ltrim(str_replace(['..\\', '../', '\\'], ['', '', '/'], $candidate), '/');
        }

        return str_starts_with($candidate, $assetsRoot . '/') ? $candidate : null;
    }
}
