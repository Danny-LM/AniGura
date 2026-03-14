<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

require_once "core/Autoloader.php";
Core\Autoloader::register();

use Core\{ Config, Response, Container, Router };

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

$container = new Container();
require_once "routes/services.php";

$router = new Router();
require_once "routes/web.php";
$router->run();
