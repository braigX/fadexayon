<?php
/**
 * Stores beta critical CSS payloads per page type.
 */

class PrestaLoadCriticalCssStore
{
    private $directory;
    private $indexFile;

    public function __construct($modulePath)
    {
        $this->directory = rtrim((string) $modulePath, '/') . '/cache/critical-css';
        $this->indexFile = $this->directory . '/index.json';
    }

    public function save(array $page, $css)
    {
        $this->ensureDirectory();

        $pageType = isset($page['key']) ? (string) $page['key'] : '';
        if ($pageType === '') {
            throw new Exception('Critical CSS page type is missing.');
        }

        $cssFile = $this->directory . '/' . preg_replace('/[^a-z0-9_-]+/i', '-', $pageType) . '.css';
        if (@file_put_contents($cssFile, (string) $css) === false) {
            throw new Exception('Could not write the critical CSS file.');
        }

        $index = $this->loadIndex();
        $index[$pageType] = [
            'page_key' => $pageType,
            'label' => isset($page['label']) ? (string) $page['label'] : $pageType,
            'sample_label' => isset($page['sample_label']) ? (string) $page['sample_label'] : '',
            'url' => isset($page['url']) ? (string) $page['url'] : '',
            'file' => $cssFile,
            'size_bytes' => filesize($cssFile) ?: 0,
            'generated_at' => date('c'),
        ];

        $this->saveIndex($index);

        return $index[$pageType];
    }

    public function get($pageType)
    {
        $index = $this->loadIndex();
        if (empty($index[$pageType]['file']) || !is_file($index[$pageType]['file'])) {
            return [];
        }

        $entry = $index[$pageType];
        $entry['css'] = (string) @file_get_contents($entry['file']);

        return $entry;
    }

    public function getEntries()
    {
        return $this->loadIndex();
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
}
