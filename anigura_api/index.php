<?php
require_once "core/Autoloader.php";
Core\Autoloader::register();

use Core\{ Config, Response, Router };
use Models\{
    UserModel,
    FranchiseModel
};
use Services\{
    UserService,
    FranchiseService
};
use Controllers\{
    UserController,
    FranchiseController
};

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

$userModel = new UserModel();
$userService = new UserService($userModel);
$userController = new UserController($userService);
$router->get("/users",  [$userController, "index"]);
$router->get("/users/:id", fn($id) => $userController->show((int)$id));
$router->post("/users", [$userController, "store"]);
$router->post("/users/search", fn() => $userController->search());
$router->post("/auth/login", fn() => $userController->checkCredentials());
$router->patch("/users/:id", fn($id) => $userController->update((int)$id));
$router->delete("/users/:id", fn($id) => $userController->destroy((int)$id));

$franchiseModel = new FranchiseModel();
$franchiseService = new FranchiseService($franchiseModel);
$franchiseController = new FranchiseController($franchiseService);
$router->get("/franchises",  [$franchiseController, "index"]);
$router->get("/franchises/:id", fn($id) => $franchiseController->show((int)$id));
$router->post("/franchises", [$franchiseController, "store"]);
$router->patch("/franchises/:id", fn($id) => $franchiseController->update((int)$id));
$router->delete("/franchises/:id", fn($id) => $franchiseController->destroy((int)$id));


$router->run();
