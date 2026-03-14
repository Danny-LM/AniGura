<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once "core/Autoloader.php";
Core\Autoloader::register();

use Core\{ Config, Response, Router, AuthMiddleware };
use Models\{
    UserModel, FranchiseModel, PublisherModel, AddressModel, MediaEntryModel,
    ProductModel, MangaVolumeDetailModel, FigureDetailModel, SetboxDetailModel,
    CartItemModel, ProductImageModel,
};
use Services\{
    UserService, FranchiseService, PublisherService, AddressService, MediaEntryService,
    ProductService, CartItemService, ProductImageService,
};
use Controllers\{
    UserController, FranchiseController, PublisherController, AddressController, MediaEntryController,
    ProductController, CartItemController, ProductImageController,
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

$userModel = new UserModel();
$franchiseModel = new FranchiseModel();
$publisherModel = new PublisherModel();
$addressModel = new AddressModel();
$mediaEntryModel = new MediaEntryModel();
$productModel = new ProductModel();
$mangaModel = new MangaVolumeDetailModel();
$figureModel = new FigureDetailModel();
$setboxModel = new SetboxDetailModel();
$cartItemModel = new CartItemModel();
$imageModel = new ProductImageModel();

$userService = new UserService($userModel);
$franchiseService = new FranchiseService($franchiseModel);
$publisherService = new PublisherService($publisherModel);
$addressService = new AddressService($addressModel, $userModel);
$mediaEntryService = new MediaEntryService($mediaEntryModel, $franchiseModel);
$productService = new ProductService($productModel, $mangaModel, $figureModel, $setboxModel, $imageModel);
$cartItemService = new CartItemService($cartItemModel, $productModel);
$imageService = new ProductImageService($imageModel, $productModel);

$userController = new UserController($userService);
$franchiseController = new FranchiseController($franchiseService);
$publisherController = new PublisherController($publisherService);
$addressController = new AddressController($addressService);
$mediaEntryController = new MediaEntryController($mediaEntryService);
$productController = new ProductController($productService);
$cartItemController = new CartItemController($cartItemService);
$imageController = new ProductImageController($imageService);

$router = new Router();
$router->get("/", function () {
    $apiInfo = [
        "app_name"    => Config::get("APP_NAME"),
        "app_version" => "1.0.0-alpha",
        "author"      => "Danny-LM",
        "enviroment"  => Config::get("APP_ENV"),
        "docs"        => []
    ];
    
    Response::json(200, $apiInfo, "Welcome to Anigura API :D");
});

$router->post("/auth/register", [$userController, "store"]);
$router->post("/auth/login", fn() => $userController->checkCredentials());
$router->get("/users", function() use ($userController) {
    AuthMiddleware::requireRole("admin");
    $userController->index();
});
$router->get("/users/:id", function() use ($userController, $id) {
    AuthMiddleware::requireRole("admin");
    $userController->show($id);
});
$router->post("/users/search", function() use ($userController) {
    AuthMiddleware::handle();
    $userController->searchByEmail();
});
$router->patch("/users/:id", function() use ($userController, $id) {
    AuthMiddleware::requireRole("admin");
    $userController->update($id);
});
$router->delete("/users/:id", function() use ($userController, $id) {
    AuthMiddleware::requireRole("admin");
    $userController->destroy($id);
});


$router->get("/franchises", function() use ($franchiseController) {
    AuthMiddleware::handle();
    $franchiseController->index();
});
$router->get("/franchises/:id", function() use ($franchiseController, $id) {
    AuthMiddleware::handle();
    $franchiseController->show($id);
});
$router->post("/franchises", function() use ($franchiseController) {
    AuthMiddleware::requireRole("admin");
    $franchiseController->store();
});
$router->patch("/franchises/:id", function() use ($franchiseController, $id) {
    AuthMiddleware::requireRole("admin");
    $franchiseController->update($id);
});
$router->delete("/franchises/:id", function() use ($franchiseController, $id) {
    AuthMiddleware::requireRole("admin");
    $franchiseController->destroy($id);
});


$router->get("/publishers", function() use ($publisherController) {
    AuthMiddleware::handle();
    $publisherController->index();
});
$router->get("/publishers/:id", function() use ($publisherController, $id) {
    AuthMiddleware::handle();
    $publisherController->show($id);
});
$router->post("/publishers", function() use ($publisherController) {
    AuthMiddleware::requireRole("admin");
    $publisherController->store();
});
$router->patch("/publishers/:id", function() use ($publisherController, $id) {
    AuthMiddleware::requireRole("admin");
    $publisherController->update($id);
});
$router->delete("/publishers/:id", function() use ($publisherController, $id) {
    AuthMiddleware::requireRole("admin");
    $publisherController->destroy($id);
});

// $router->get("/publishers",  [$publisherController, "index"]);
// $router->get("/publishers/:id", fn($id) => $publisherController->show((int)$id));
// $router->post("/publishers", [$publisherController, "store"]);
// $router->patch("/publishers/:id", fn($id) => $publisherController->update((int)$id));
// $router->delete("/publishers/:id", fn($id) => $publisherController->destroy((int)$id));

$router->get("/addresses",  [$addressController, "index"]);
$router->get("/addresses/user/:userId", fn($userId) => $addressController->userDefault((int)$userId));
$router->get("/addresses/:id", fn($id) => $addressController->show((int)$id));
$router->post("/addresses", [$addressController, "store"]);
$router->patch("/addresses/:id", fn($id) => $addressController->update((int)$id));
$router->delete("/addresses/:id", fn($id) => $addressController->destroy((int)$id));

$router->get("/media_entries",  [$mediaEntryController, "index"]);
$router->get("/media_entries/:id", fn($id) => $mediaEntryController->show((int)$id));
$router->post("/media_entries", [$mediaEntryController, "store"]);
$router->patch("/media_entries/:id", fn($id) => $mediaEntryController->update((int)$id));
$router->delete("/media_entries/:id", fn($id) => $mediaEntryController->destroy((int)$id));

$router->get("/products",  [$productController, "index"]);
$router->get("/products/:id", fn($id) => $productController->show((int)$id));
$router->post("/products", [$productController, "store"]);
$router->patch("/products/:id", fn($id) => $productController->update((int)$id));
$router->delete("/products/:id", fn($id) => $productController->destroy((int)$id));

$router->get("/cart/:id_user", fn($id) => $cartItemController->show((int)$id));
$router->post("/cart/:id_user", fn($id) => $cartItemController->store((int)$id));
$router->patch("/cart/:id_user/:id_item",
    fn($id_user, $id_item) => $cartItemController->update((int)$id_user, (int)$id_item)
);
$router->delete("/cart/:id_user/:id_item", 
    fn($id_user, $id_item) => $cartItemController->destroy((int)$id_user, (int)$id_item)
);

$router->get("/images",  [$imageController, "index"]);
$router->get("/images/:id", fn($id) => $imageController->show((int)$id));
$router->get("/images/cover/:id", fn($id) => $imageController->productCover((int)$id));
$router->get("/images/product/:id", fn($id) => $imageController->productImages((int)$id));
$router->post("/images", [$imageController, "store"]);
$router->patch("/images/:id", fn($id) => $imageController->update((int)$id));
$router->delete("/images/:id", fn($id) => $imageController->destroy((int)$id));


$router->run();
