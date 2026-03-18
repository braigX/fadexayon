<?php
/**
 * Stores beta critical CSS payloads per page type.
 */

class PrestaLoadCriticalCssStore
{
    private $directory;
    private $indexFile;
    /**
     * @var PrestaLoadCriticalCssLogger|null
     */
    private $logger;

    public function __construct($modulePath, $logger = null)
    {
        $this->directory = rtrim((string) $modulePath, '/') . '/cache/critical-css';
        $this->indexFile = $this->directory . '/index.json';
        $this->logger = $logger;
    }

    public function saveVariants(array $page, array $variants)
    {
        $this->ensureDirectory();

        $pageType = isset($page['key']) ? (string) $page['key'] : '';
        if ($pageType === '') {
            throw new Exception('Critical CSS page type is missing.');
        }

        $index = $this->loadIndex();
        $index[$pageType] = [
            'page_key' => $pageType,
            'label' => isset($page['label']) ? (string) $page['label'] : $pageType,
            'sample_label' => isset($page['sample_label']) ? (string) $page['sample_label'] : '',
            'url' => isset($page['url']) ? (string) $page['url'] : '',
            'devices' => [],
        ];

        foreach ($variants as $device => $variant) {
            $normalizedDevice = in_array($device, ['mobile', 'tablet', 'desktop'], true) ? (string) $device : 'mobile';
            $cssFile = $this->directory . '/' . preg_replace('/[^a-z0-9_-]+/i', '-', $pageType) . '-' . $normalizedDevice . '.css';
            $css = isset($variant['css']) ? (string) $variant['css'] : '';
            if ($css === '' || @file_put_contents($cssFile, $css) === false) {
                throw new Exception('Could not write the critical CSS file for ' . $normalizedDevice . '.');
            }

            $index[$pageType]['devices'][$normalizedDevice] = [
                'device' => $normalizedDevice,
                'file' => $cssFile,
                'size_bytes' => filesize($cssFile) ?: 0,
                'generated_at' => isset($variant['generated_at']) ? (string) $variant['generated_at'] : date('c'),
                'generator_version' => isset($variant['generator_version']) ? (string) $variant['generator_version'] : '',
                'meta' => isset($variant['meta']) && is_array($variant['meta']) ? $variant['meta'] : [],
            ];

            $this->log([
                'stage' => 'store_variant',
                'page_type' => $pageType,
                'device' => $normalizedDevice,
                'file' => $cssFile,
                'size_bytes' => $index[$pageType]['devices'][$normalizedDevice]['size_bytes'],
                'generated_at' => $index[$pageType]['devices'][$normalizedDevice]['generated_at'],
                'generator_version' => $index[$pageType]['devices'][$normalizedDevice]['generator_version'],
            ]);
        }

        $this->saveIndex($index);

        $this->log([
            'stage' => 'store_complete',
            'page_type' => $pageType,
            'devices' => array_keys($index[$pageType]['devices']),
            'index_file' => $this->indexFile,
        ]);

        return $index[$pageType];
    }

    public function get($pageType, $device)
    {
        $index = $this->loadIndex();
        if (empty($index[$pageType]['devices'][$device]['file']) || !is_file($index[$pageType]['devices'][$device]['file'])) {
            return [];
        }

        $entry = $index[$pageType];
        $entry['device'] = $device;
        $entry['css'] = (string) @file_get_contents($entry['devices'][$device]['file']);
        $entry['size_bytes'] = (int) $entry['devices'][$device]['size_bytes'];
        $entry['generated_at'] = (string) $entry['devices'][$device]['generated_at'];

        return $entry;
    }

    public function getEntries()
    {
        return $this->loadIndex();
    }

    public function remove($pageType)
    {
        $pageType = (string) $pageType;
        if ($pageType === '') {
            throw new Exception('Critical CSS page type is missing.');
        }

        $index = $this->loadIndex();
        if (empty($index[$pageType])) {
            return false;
        }

        $entry = $index[$pageType];
        if (!empty($entry['devices']) && is_array($entry['devices'])) {
            foreach ($entry['devices'] as $deviceEntry) {
                $file = isset($deviceEntry['file']) ? (string) $deviceEntry['file'] : '';
                if ($file !== '' && is_file($file)) {
                    @unlink($file);
                }
            }
        }

        unset($index[$pageType]);
        $this->saveIndex($index);

        return true;
    }

    private function ensureDirectory()
    {
        if (is_dir($this->directory)) {
            return;
        }

        @mkdir($this->directory, 0777, true);
    }

    private function loadIndex()
    {
        if (!is_file($this->indexFile)) {
            return [];
        }

        $decoded = json_decode((string) @file_get_contents($this->indexFile), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function saveIndex(array $index)
    {
        if (@file_put_contents($this->indexFile, json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n") === false) {
            throw new Exception('Could not update the critical CSS index.');
        }
    }

    private function log(array $entry)
    {
        if ($this->logger) {
            $this->logger->log($entry);
        }
    }
}
