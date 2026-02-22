<?php
require_once "core/Autoloader.php";
Core\Autoloader::register();

use Core\{ Config, Response, Database, Router };
use Controllers\{ UserController, CategoryController, SerieController };

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

$router = new Router();
$userController = new UserController();
$categoryController = new CategoryController();
$serieController = new SerieController();

$router->get("/", function () {
    $apiInfo = [
        "app_name"    => Config::get("APP_NAME"),
        "app_version" => "1.0.0-alpha",
        "author"      => "Danny-LM",
        "enviroment"  => Config::get("APP_ENV"),
        "docs"        => []
    ];
    
    Response::json(200, $apiInfo, "Welcome to Anigura API system.");
});

$router->get("/users",  [$userController, "index"]);
$router->get("/users/:id", fn($id) => $userController->show((int)$id));
$router->post("/users", [$userController, "store"]);
$router->post("/users/search", fn() => $userController->search());
$router->post("/auth/login", fn() => $userController->checkCredentials());
$router->put("/users/:id", fn($id) => $userController->update((int)$id));
$router->delete("/users/:id", fn($id) => $userController->destroy((int)$id));

$router->get("/categories",  [$categoryController, "index"]);
$router->get("/categories/:id", fn($id) => $categoryController->show((int)$id));
$router->post("/categories", [$categoryController, "store"]);
$router->put("/categories/:id", fn($id) => $categoryController->update((int)$id));
$router->delete("/categories/:id", fn($id) => $categoryController->destroy((int)$id));

$router->get("/series",  [$serieController, "index"]);
$router->get("/series/:id", fn($id) => $serieController->show((int)$id));
$router->post("/series", [$serieController, "store"]);
$router->put("/series/:id", fn($id) => $serieController->update((int)$id));
$router->delete("/series/:id", fn($id) => $serieController->destroy((int)$id));


$router->run();
