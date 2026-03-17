<?php
namespace Core;

class Logger {
    private static function getPath(): string {
        $dir = __DIR__ . "/../logs";
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        return $dir . "/app-" . date("Y-m-d") . ".log";
    }

    private static function write(string $level, string $message, array $context = []): void {
        $timestamp = date("Y-m-d H:i:s");
        $ctx = !empty($context) ? " - " . json_encode($context) : "";
        $line = "[{$timestamp}] [{$level}] {$message}{$ctx}" . PHP_EOL;

        file_put_contents(self::getPath(), $line, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $message, array $context = []): void {
        self::write("INFO", $message, $context);
    }

    public static function warning(string $message, array $context = []): void {
        self::write("WARNING", $message, $context);
    }

    public static function error(string $message, array $context = []): void {
        self::write("ERROR", $message, $context);
    }
}
