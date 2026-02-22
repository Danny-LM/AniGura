<?php
namespace Core;

class Config {
    
    public static function load(string $path): void {
        if (!file_exists($path)) {
            die(".env file not found. Plz create one based on .env.example");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), "#") === 0) continue;
            if (strpos($line, "=") === false) continue;

            list($name, $value) = explode("=", $line, 2);

            $name = trim($name);
            $value = trim($value);
            $value = trim($value, "\"'");

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf("%s=%s", $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }

    public static function get(string $key, $default = null) {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}
