<?php
require_once "core/Autoloader.php";
Core\Autoloader::register();

use Core\{ Config, Response, Router };
use Models\{
    UserModel,
    FranchiseModel,
    PublisherModel,
    AddressModel,
    MediaEntryModel,
};
use Services\{
    UserService,
    FranchiseService,
    PublisherService,
    AddressService,
    MediaEntryService,
};
use Controllers\{
    UserController,
    FranchiseController,
    PublisherController,
    AddressController,
    MediaEntryController,
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

$publisherModel = new PublisherModel();
$publisherService = new PublisherService($publisherModel);
$publisherController = new PublisherController($publisherService);
$router->get("/publishers",  [$publisherController, "index"]);
$router->get("/publishers/:id", fn($id) => $publisherController->show((int)$id));
$router->post("/publishers", [$publisherController, "store"]);
$router->patch("/publishers/:id", fn($id) => $publisherController->update((int)$id));
$router->delete("/publishers/:id", fn($id) => $publisherController->destroy((int)$id));

$addressModel = new AddressModel();
$addressService = new AddressService($addressModel, $userModel);
$addressController = new AddressController($addressService);
$router->get("/addresses",  [$addressController, "index"]);
$router->get("/addresses/user/:userId", fn($userId) => $addressController->userDefault((int)$userId));
$router->get("/addresses/:id", fn($id) => $addressController->show((int)$id));
$router->post("/addresses", [$addressController, "store"]);
$router->patch("/addresses/:id", fn($id) => $addressController->update((int)$id));
$router->delete("/addresses/:id", fn($id) => $addressController->destroy((int)$id));

$mediaEntryModel = new MediaEntryModel();
$mediaEntryService = new MediaEntryService($mediaEntryModel, $franchiseModel);
$mediaEntryController = new MediaEntryController($mediaEntryService);
$router->get("/media_entries",  [$mediaEntryController, "index"]);
$router->get("/media_entries/:id", fn($id) => $mediaEntryController->show((int)$id));
$router->post("/media_entries", [$mediaEntryController, "store"]);
$router->patch("/media_entries/:id", fn($id) => $mediaEntryController->update((int)$id));
$router->delete("/media_entries/:id", fn($id) => $mediaEntryController->destroy((int)$id));


$router->run();
