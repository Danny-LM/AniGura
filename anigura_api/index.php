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
    CartItemModel, ProductImageModel, RefreshTokenModel
};
use Services\Handlers\{ MangaVolumeDetailHandler, FigureDetailHandler, SetboxDetailHandler };
use Services\{
    UserService, FranchiseService, PublisherService, AddressService, MediaEntryService,
    ProductService, CartItemService, ProductImageService, RefreshTokenService, AuthService
};
use Controllers\{
    UserController, FranchiseController, PublisherController, AddressController, MediaEntryController,
    ProductController, CartItemController, ProductImageController, AuthController
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

// Models
$userModel          = new UserModel();
$franchiseModel     = new FranchiseModel();
$publisherModel     = new PublisherModel();
$addressModel       = new AddressModel();
$mediaEntryModel    = new MediaEntryModel();
$productModel       = new ProductModel();
$mangaModel         = new MangaVolumeDetailModel();
$figureModel        = new FigureDetailModel();
$setboxModel        = new SetboxDetailModel();
$cartItemModel      = new CartItemModel();
$imageModel         = new ProductImageModel();
$refreshTokenModel  = new RefreshTokenModel();

// Service Handlers
$mangaModel         = new MangaVolumeDetailModel();
$figureModel        = new FigureDetailModel();
$setboxModel        = new SetboxDetailModel();

// Services
$userService        = new UserService($userModel);
$franchiseService   = new FranchiseService($franchiseModel);
$publisherService   = new PublisherService($publisherModel);
$addressService     = new AddressService($addressModel, $userModel);
$mediaEntryService  = new MediaEntryService($mediaEntryModel, $franchiseModel);
$productService = new ProductService($productModel, $imageModel, [
    "manga_volume" => $mangaHandler,
    "figure"       => $figureHandler,
    "setbox"       => $setboxHandler,
]);
$cartItemService    = new CartItemService($cartItemModel, $productModel);
$imageService       = new ProductImageService($imageModel, $productModel);
$refreshTokenService = new RefreshTokenService($refreshTokenModel, $userModel);
$authService        = new AuthService($userModel, $refreshTokenService);

// Controllers
$userController         = new UserController($userService);
$franchiseController    = new FranchiseController($franchiseService);
$publisherController    = new PublisherController($publisherService);
$addressController      = new AddressController($addressService);
$mediaEntryController   = new MediaEntryController($mediaEntryService);
$productController      = new ProductController($productService);
$cartItemController     = new CartItemController($cartItemService);
$imageController        = new ProductImageController($imageService);
$authController         = new AuthController($authService);

$router = new Router();

// --- Info ---
$router->get("/", function () {
    Response::json(200, [
        "app_name"    => Config::get("APP_NAME"),
        "app_version" => "1.0.0-alpha",
        "author"      => "Danny-LM",
        "enviroment"  => Config::get("APP_ENV"),
        "docs"        => []
    ], "Welcome to Anigura API :D");
});

// --- Auth ---
$router->post("/auth/register", [$userController, "store"]);
$router->post("/auth/login",    [$authController, "login"]);
$router->post("/auth/refresh",  [$authController, "refresh"]);
$router->post("/auth/logout", function() use ($authController) {
    AuthMiddleware::handle();
    $authController->logout();
});

// --- Users ---
$router->get("/users", function() use ($userController) {
    AuthMiddleware::requireRole("admin");
    $userController->index();
});
$router->get("/users/:id", function($id) use ($userController) {
    AuthMiddleware::requireRole("admin");
    $userController->show($id);
});
$router->post("/users/search", function() use ($userController) {
    AuthMiddleware::handle();
    $userController->searchByEmail();
});
$router->patch("/users/:id", function($id) use ($userController) {
    AuthMiddleware::requireRole("admin");
    $userController->update($id);
});
$router->delete("/users/:id", function($id) use ($userController) {
    AuthMiddleware::requireRole("admin");
    $userController->destroy($id);
});

// --- Franchises ---
$router->get("/franchises", [$franchiseController, "index"]);
$router->get("/franchises/:id", fn($id) => $franchiseController->show($id));
$router->post("/franchises", function() use ($franchiseController) {
    AuthMiddleware::requireRole("admin");
    $franchiseController->store();
});
$router->patch("/franchises/:id", function($id) use ($franchiseController) {
    AuthMiddleware::requireRole("admin");
    $franchiseController->update($id);
});
$router->delete("/franchises/:id", function($id) use ($franchiseController) {
    AuthMiddleware::requireRole("admin");
    $franchiseController->destroy($id);
});

// --- Publishers ---
$router->get("/publishers", [$publisherController, "index"]);
$router->get("/publishers/:id", fn($id) => $publisherController->show($id));
$router->post("/publishers", function() use ($publisherController) {
    AuthMiddleware::requireRole("admin");
    $publisherController->store();
});
$router->patch("/publishers/:id", function($id) use ($publisherController) {
    AuthMiddleware::requireRole("admin");
    $publisherController->update($id);
});
$router->delete("/publishers/:id", function($id) use ($publisherController) {
    AuthMiddleware::requireRole("admin");
    $publisherController->destroy($id);
});

// --- Addresses ---
$router->get("/addresses", function() use ($addressController) {
    AuthMiddleware::requireRole("admin");
    $addressController->index();
});
$router->get("/addresses/user/:userId", function($userId) use ($addressController) {
    AuthMiddleware::handle();
    $addressController->userDefault((int)$userId);
});
$router->get("/addresses/:id", function($id) use ($addressController) {
    AuthMiddleware::handle();
    $addressController->show((int)$id);
});
$router->post("/addresses", function() use ($addressController) {
    AuthMiddleware::handle();
    $addressController->store();
});
$router->patch("/addresses/:id", function($id) use ($addressController) {
    AuthMiddleware::handle();
    $addressController->update((int)$id);
});
$router->delete("/addresses/:id", function($id) use ($addressController) {
    AuthMiddleware::handle();
    $addressController->destroy((int)$id);
});

// --- Media Entries ---
$router->get("/media_entries", [$mediaEntryController, "index"]);
$router->get("/media_entries/:id", fn($id) => $mediaEntryController->show((int)$id));
$router->post("/media_entries", function() use ($mediaEntryController) {
    AuthMiddleware::requireRole("admin");
    $mediaEntryController->store();
});
$router->patch("/media_entries/:id", function($id) use ($mediaEntryController) {
    AuthMiddleware::requireRole("admin");
    $mediaEntryController->update((int)$id);
});
$router->delete("/media_entries/:id", function($id) use ($mediaEntryController) {
    AuthMiddleware::requireRole("admin");
    $mediaEntryController->destroy((int)$id);
});

// --- Products ---
$router->get("/products", [$productController, "index"]);
$router->get("/products/:id", fn($id) => $productController->show((int)$id));
$router->post("/products", function() use ($productController) {
    AuthMiddleware::requireRole("admin");
    $productController->store();
});
$router->patch("/products/:id", function($id) use ($productController) {
    AuthMiddleware::requireRole("admin");
    $productController->update((int)$id);
});
$router->delete("/products/:id", function($id) use ($productController) {
    AuthMiddleware::requireRole("admin");
    $productController->destroy((int)$id);
});

// --- Cart ---
$router->get("/cart/:id_user/validate", function($id) use ($cartItemController) {
    AuthMiddleware::handle();
    $cartItemController->validateUserCart((int)$id);
});
$router->get("/cart/:id_user", function($id) use ($cartItemController) {
    AuthMiddleware::handle();
    $cartItemController->show((int)$id);
});
$router->post("/cart/:id_user", function($id) use ($cartItemController) {
    AuthMiddleware::handle();
    $cartItemController->store((int)$id);
});
$router->patch("/cart/:id_user/:id_item", function($id_user, $id_item) use ($cartItemController) {
    AuthMiddleware::handle();
    $cartItemController->update((int)$id_user, (int)$id_item);
});
$router->delete("/cart/:id_user/:id_item", function($id_user, $id_item) use ($cartItemController) {
    AuthMiddleware::handle();
    $cartItemController->destroy((int)$id_user, (int)$id_item);
});

// --- Images ---
$router->get("/images", [$imageController, "index"]);
$router->get("/images/cover/:id", fn($id) => $imageController->productCover((int)$id));
$router->get("/images/product/:id", fn($id) => $imageController->productImages((int)$id));
$router->get("/images/:id", fn($id) => $imageController->show((int)$id));
$router->post("/images", function() use ($imageController) {
    AuthMiddleware::requireRole("admin");
    $imageController->store();
});
$router->patch("/images/:id", function($id) use ($imageController) {
    AuthMiddleware::requireRole("admin");
    $imageController->update((int)$id);
});
$router->delete("/images/:id", function($id) use ($imageController) {
    AuthMiddleware::requireRole("admin");
    $imageController->destroy((int)$id);
});

$router->run();
