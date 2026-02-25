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

use Core\{ Config, Response, Router };
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
    
    Response::json(200, $apiInfo, "Welcome to Anigura API system.");
});


$router->get("/users",  [$userController, "index"]);
$router->get("/users/:id", fn($id) => $userController->show((int)$id));
$router->post("/auth/register", [$userController, "store"]);
$router->post("/auth/login", fn() => $userController->checkCredentials());
$router->post("/users/search", fn() => $userController->search());
$router->patch("/users/:id", fn($id) => $userController->update((int)$id));
$router->delete("/users/:id", fn($id) => $userController->destroy((int)$id));

$router->get("/franchises",  [$franchiseController, "index"]);
$router->get("/franchises/:id", fn($id) => $franchiseController->show((int)$id));
$router->post("/franchises", [$franchiseController, "store"]);
$router->patch("/franchises/:id", fn($id) => $franchiseController->update((int)$id));
$router->delete("/franchises/:id", fn($id) => $franchiseController->destroy((int)$id));

$router->get("/publishers",  [$publisherController, "index"]);
$router->get("/publishers/:id", fn($id) => $publisherController->show((int)$id));
$router->post("/publishers", [$publisherController, "store"]);
$router->patch("/publishers/:id", fn($id) => $publisherController->update((int)$id));
$router->delete("/publishers/:id", fn($id) => $publisherController->destroy((int)$id));

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
