<?php

class PrestaLoadCriticalCssLogger
{
    /**
     * @var string
     */
    private $path;

    public function __construct($path)
    {
        $this->path = (string) $path;
    }

    public function log(array $entry)
    {
        $data = $this->load();
        $data[] = array_merge([
            'logged_at' => date('c'),
        ], $entry);

        if (count($data) > 200) {
            $data = array_slice($data, -200);
        }

        @file_put_contents($this->path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    }

    private function load()
    {
        if (!is_file($this->path)) {
            return [];
        }

        $decoded = json_decode((string) @file_get_contents($this->path), true);

        return is_array($decoded) ? $decoded : [];
    }
}
