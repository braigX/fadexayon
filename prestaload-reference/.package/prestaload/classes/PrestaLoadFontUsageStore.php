<?php
/**
 * Stores per-page font usage audits by device.
 */

class PrestaLoadFontUsageStore
{
    private $directory;
    private $indexFile;

    public function __construct($modulePath)
    {
        $this->directory = rtrim((string) $modulePath, '/') . '/cache/font-usage';
        $this->indexFile = $this->directory . '/index.json';
    }

    public function saveVariants(array $page, array $variants)
    {
        $this->ensureDirectory();

        $pageType = isset($page['key']) ? (string) $page['key'] : '';
        if ($pageType === '') {
            throw new Exception('Font usage page type is missing.');
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
            $normalizedDevice = $device === 'desktop' ? 'desktop' : 'mobile';
            $jsonFile = $this->directory . '/' . preg_replace('/[^a-z0-9_-]+/i', '-', $pageType) . '-' . $normalizedDevice . '.json';
            if (@file_put_contents($jsonFile, json_encode($variant, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n") === false) {
                throw new Exception('Could not write the font usage file for ' . $normalizedDevice . '.');
            }

            $index[$pageType]['devices'][$normalizedDevice] = [
                'device' => $normalizedDevice,
                'file' => $jsonFile,
                'generated_at' => isset($variant['generated_at']) ? (string) $variant['generated_at'] : date('c'),
                'generator_version' => isset($variant['generator_version']) ? (string) $variant['generator_version'] : '',
                'declared_count' => count(isset($variant['declared_font_families']) && is_array($variant['declared_font_families']) ? $variant['declared_font_families'] : []),
                'used_count' => count(isset($variant['used_font_families']) && is_array($variant['used_font_families']) ? $variant['used_font_families'] : []),
                'unused_count' => count(isset($variant['unused_declared_families']) && is_array($variant['unused_declared_families']) ? $variant['unused_declared_families'] : []),
                'duplicate_icon_count' => count(isset($variant['duplicate_icon_font_stylesheets']) && is_array($variant['duplicate_icon_font_stylesheets']) ? $variant['duplicate_icon_font_stylesheets'] : []),
                'google_fonts_count' => count(isset($variant['google_fonts_stylesheets']) && is_array($variant['google_fonts_stylesheets']) ? $variant['google_fonts_stylesheets'] : []),
                'declared_font_families' => isset($variant['declared_font_families']) && is_array($variant['declared_font_families']) ? array_values($variant['declared_font_families']) : [],
                'used_font_families' => isset($variant['used_font_families']) && is_array($variant['used_font_families']) ? array_values($variant['used_font_families']) : [],
                'used_above_the_fold' => isset($variant['used_above_the_fold']) && is_array($variant['used_above_the_fold']) ? array_values($variant['used_above_the_fold']) : [],
                'unused_declared_families' => isset($variant['unused_declared_families']) && is_array($variant['unused_declared_families']) ? array_values($variant['unused_declared_families']) : [],
                'duplicate_icon_font_stylesheets' => isset($variant['duplicate_icon_font_stylesheets']) && is_array($variant['duplicate_icon_font_stylesheets']) ? array_values($variant['duplicate_icon_font_stylesheets']) : [],
                'google_fonts_stylesheets' => isset($variant['google_fonts_stylesheets']) && is_array($variant['google_fonts_stylesheets']) ? array_values($variant['google_fonts_stylesheets']) : [],
                'meta' => isset($variant['meta']) && is_array($variant['meta']) ? $variant['meta'] : [],
            ];
        }

        $this->saveIndex($index);

        return $index[$pageType];
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
            throw new Exception('Could not update the font usage index.');
        }
    }
}
