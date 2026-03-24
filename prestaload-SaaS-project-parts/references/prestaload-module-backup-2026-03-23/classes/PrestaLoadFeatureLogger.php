<?php
/**
 * Stores feature logs as grouped JSON requests instead of line-based logs.
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

        $requestId = self::getRequestId();
        $entry = [
            'stage' => isset($payload['stage']) ? (string) $payload['stage'] : 'unknown',
            'step' => isset($payload['step']) ? (string) $payload['step'] : 'unknown',
            'logged_at' => gmdate('c'),
            'payload' => $this->withoutStageAndStep($payload),
        ];

        $this->appendToRequest($requestId, $entry);
    }

    public static function getRequestId()
    {
        if (!isset($_SERVER['PRESTALOAD_REQUEST_ID']) || $_SERVER['PRESTALOAD_REQUEST_ID'] === '') {
            $_SERVER['PRESTALOAD_REQUEST_ID'] = sha1(uniqid('prestaload_', true) . '|' . microtime(true) . '|' . mt_rand());
        }

        return (string) $_SERVER['PRESTALOAD_REQUEST_ID'];
    }

    public static function logStatic($logFile, array $payload)
    {
        $logger = new self($logFile);
        $logger->log($payload);
    }

    private function appendToRequest($requestId, array $entry)
    {
        $snapshot = $this->readSnapshot();
        $requests = isset($snapshot['requests']) && is_array($snapshot['requests']) ? $snapshot['requests'] : [];
        $index = $this->findRequestIndex($requests, $requestId);

        if ($index === -1) {
            $requests[] = [
                'request_id' => $requestId,
                'request_uri' => isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '',
                'method' => isset($_SERVER['REQUEST_METHOD']) ? (string) $_SERVER['REQUEST_METHOD'] : '',
                'started_at' => gmdate('c'),
                'events' => [],
            ];
            $index = count($requests) - 1;
        }

        $requests[$index]['request_uri'] = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        $requests[$index]['method'] = isset($_SERVER['REQUEST_METHOD']) ? (string) $_SERVER['REQUEST_METHOD'] : '';
        $requests[$index]['updated_at'] = gmdate('c');
        $requests[$index]['events'][] = $entry;

        if (count($requests) > 100) {
            $requests = array_slice($requests, -100);
        }

        $snapshot['requests'] = array_values($requests);
        $encoded = json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) {
            return;
        }

        @file_put_contents($this->logFile, $encoded . PHP_EOL, LOCK_EX);
    }

    private function readSnapshot()
    {
        if (!is_file($this->logFile)) {
            return [
                'format' => 'grouped-json-v1',
                'requests' => [],
            ];
        }

        $raw = @file_get_contents($this->logFile);
        if (!is_string($raw) || trim($raw) === '') {
            return [
                'format' => 'grouped-json-v1',
                'requests' => [],
            ];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [
                'format' => 'grouped-json-v1',
                'requests' => [],
            ];
        }

        if (!isset($decoded['format'])) {
            $decoded['format'] = 'grouped-json-v1';
        }

        if (!isset($decoded['requests']) || !is_array($decoded['requests'])) {
            $decoded['requests'] = [];
        }

        return $decoded;
    }

    private function findRequestIndex(array $requests, $requestId)
    {
        foreach ($requests as $index => $request) {
            if (isset($request['request_id']) && (string) $request['request_id'] === (string) $requestId) {
                return $index;
            }
        }

        return -1;
    }

    private function withoutStageAndStep(array $payload)
    {
        unset($payload['stage'], $payload['step']);

        return $payload;
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
