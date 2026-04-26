<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

class PrestaloadCacheManager
{
    private string $cacheDir;

    public function __construct(?string $cacheDir = null)
    {
        $this->cacheDir = $cacheDir ?: _PS_ROOT_DIR_ . '/var/prestaload-cache';
    }

    public function purgeUrls(array $urls): array
    {
        $summary = [
            'deleted_html' => 0,
            'deleted_meta' => 0,
            'deleted_dirs' => 0,
        ];

        $urls = array_values(array_unique(array_filter($urls)));

        foreach ($urls as $url) {
            $result = $this->purgeUrl((string) $url);
            $summary['deleted_html'] += (int) ($result['deleted_html'] ?? 0);
            $summary['deleted_meta'] += (int) ($result['deleted_meta'] ?? 0);
            $summary['deleted_dirs'] += (int) ($result['deleted_dirs'] ?? 0);
        }

        return $summary;
    }

    public function purgeUrl(string $url): array
    {
        $directory = $this->cacheDirectory($url);

        if (! is_dir($directory)) {
            return [
                'deleted_html' => 0,
                'deleted_meta' => 0,
                'deleted_dirs' => 0,
            ];
        }

        return $this->purgeAllFiles($directory);
    }

    public function purgeAll(): array
    {
        $summary = [
            'deleted_html' => 0,
            'deleted_meta' => 0,
            'deleted_dirs' => 0,
        ];

        if (! is_dir($this->cacheDir)) {
            return $summary;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->cacheDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $path) {
            if ($path->isFile()) {
                $extension = strtolower($path->getExtension());

                if ($extension === 'html' && @unlink($path->getPathname())) {
                    $summary['deleted_html']++;
                }

                if ($extension === 'meta' && @unlink($path->getPathname())) {
                    $summary['deleted_meta']++;
                }

                continue;
            }

            if ($path->isDir() && $this->isDirectoryEmpty($path->getPathname()) && @rmdir($path->getPathname())) {
                $summary['deleted_dirs']++;
            }
        }

        if ($this->isDirectoryEmpty($this->cacheDir) && @rmdir($this->cacheDir)) {
            $summary['deleted_dirs']++;
        }

        return $summary;
    }

    private function purgeAllFiles(string $directory): array
    {
        $summary = [
            'deleted_html' => 0,
            'deleted_meta' => 0,
            'deleted_dirs' => 0,
        ];

        foreach ((array) glob($directory . '/*.html') as $path) {
            if (is_file($path) && @unlink($path)) {
                $summary['deleted_html']++;
            }
        }

        foreach ((array) glob($directory . '/*.meta') as $path) {
            if (is_file($path) && @unlink($path)) {
                $summary['deleted_meta']++;
            }
        }

        if ($this->isDirectoryEmpty($directory) && @rmdir($directory)) {
            $summary['deleted_dirs']++;
        }

        return $summary;
    }

    private function cacheDirectory(string $url): string
    {
        $urlHash = md5($this->normalizeUrl($url));
        $shard = substr($urlHash, 0, 2);

        return $this->cacheDir . "/{$shard}/{$urlHash}";
    }

    private function normalizeUrl(string $url): string
    {
        $parsed = parse_url(strtolower(rtrim($url, '/')));
        $host = $parsed['host'] ?? '';
        $path = $parsed['path'] ?? '';

        return $host . ($path !== '' && $path !== '/' ? rtrim($path, '/') : '');
    }

    private function isDirectoryEmpty(string $directory): bool
    {
        $entries = @scandir($directory);

        return is_array($entries) && count(array_diff($entries, ['.', '..'])) === 0;
    }
}
