<?php
require_once "core/Autoloader.php";
Core\Autoloader::register();

use Core\{ Config, Response };

try {
    Config::load(__DIR__ . "/.env");

} catch (\Exception $e) {
    Response::json(500, null, "Critical Error: " . $e->getMessage());
}


if (Config::get("APP_DEBUG") === "true") {
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
}

$apiInfo = [
    "app_name"    => Config::get("APP_NAME"),
    "app_version" => "1.0.0-alpha",
    "author"      => "Danny-LM",
    "enviroment"  => Config::get("APP_ENV"),
    "docs"        => []
];

Response::json(200, $apiInfo, "Welcome to Anigura API system.");
