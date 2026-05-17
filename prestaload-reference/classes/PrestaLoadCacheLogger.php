<?php
/**
 * Appends one JSON line per cache decision so request flow is easy to inspect.
 */

class PrestaLoadCacheLogger
{
    private $logFile;

    public function __construct($logFile)
    {
        $this->logFile = (string) $logFile;
    }

    public function log(array $payload)
    {
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
}
