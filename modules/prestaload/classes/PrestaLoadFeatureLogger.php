<?php
/**
 * Writes one JSON line per optimization step so request behavior is easy to inspect.
 */

class PrestaLoadFeatureLogger
{
    private $logFile;

    public function __construct($logFile)
    {
        $this->logFile = (string) $logFile;
    }

    public function log(array $payload)
    {
        if (!$this->shouldLogCurrentRequest()) {
            return;
        }

        $directory = dirname($this->logFile);
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        $payload['logged_at'] = gmdate('c');
        $payload['request_uri'] = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        $payload['method'] = isset($_SERVER['REQUEST_METHOD']) ? (string) $_SERVER['REQUEST_METHOD'] : '';

        $line = json_encode($payload);
        if ($line === false) {
            return;
        }

        @file_put_contents($this->logFile, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    private function shouldLogCurrentRequest()
    {
        $controller = '';
        if (class_exists('Tools')) {
            $controller = Tools::strtolower((string) Tools::getValue('controller', 'index'));
        }

        if (in_array($controller, ['index', 'category', 'product'], true)) {
            return true;
        }

        $path = isset($_SERVER['REQUEST_URI']) ? (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '/';
        if ($path === false || $path === '') {
            $path = '/';
        }

        return $path === '/' || $path === '/index.php';
    }
}
