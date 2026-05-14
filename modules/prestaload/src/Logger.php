<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

class PrestaloadLogger
{
    private static string $path = '';

    private static function path(): string
    {
        if (self::$path === '') {
            self::$path = dirname(__DIR__) . '/pl-logs.txt';
        }

        return self::$path;
    }

    public static function write(string $level, string $message, array $context = []): void
    {
        $line = date('Y-m-d H:i:s') . ' [' . strtoupper($level) . '] ' . $message;

        if ($context) {
            $line .= ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        @file_put_contents(self::path(), $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $message, array $context = []): void
    {
        self::write('info', $message, $context);
    }

    public static function warn(string $message, array $context = []): void
    {
        self::write('warn', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('error', $message, $context);
    }
}
