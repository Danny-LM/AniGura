<?php
namespace Core;

class Autoloader {
    
    public static function register() {
        spl_autoload_register(function ($class) {
            $path = str_replace("\\", DIRECTORY_SEPARATOR, $class);
            $file = __DIR__ . "/../" . str_replace("\\", "/", $path) . ".php";

            $file = strtolower($file);

            if (file_exists($file)) {
                require_once($file);
            }
        });
    }
}