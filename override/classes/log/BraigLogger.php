<?php
class BraigLogger {
    private static $logger = null;

    private static function initializeLogger() {
        self::$logger = new Monolog\Logger('braig_logger');
        $streamHandler = new Monolog\Handler\StreamHandler(_PS_ROOT_DIR_.'/var/logs/braig_logger.txt', Monolog\Logger::DEBUG);
        self::$logger->pushHandler($streamHandler);

        // Adding a processor to include file and line number
        self::$logger->pushProcessor(function ($record) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            $caller = $trace[2];  // Adjust the index as necessary to find the right level in the trace
            $record['extra']['file'] = $caller['file'] ?? 'unknown file';
            $record['extra']['line'] = $caller['line'] ?? 'unknown line';
            return $record;
        });
    }

    public static function getLogger() {
        if (self::$logger === null) {
            self::initializeLogger();
        }
        return self::$logger;
    }

    public static function log($level, $message, array $context = []) {
        $logger = self::getLogger();
        $logger->$level($message, $context);
    }
}