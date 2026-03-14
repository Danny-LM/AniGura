<?php
use Core\{ Response, Config, AuthMiddleware };
use Controllers\{
    UserController, FranchiseController, PublisherController, AddressController,
    MediaEntryController, ProductController, CartItemController, ProductImageController,
    AuthController
};

// --- Info ---
$router->get("/", function () {
    Response::json(200, [
        "app_name"    => Config::get("APP_NAME"),
        "author"      => "Danny-LM",
        "enviroment"  => Config::get("APP_ENV")
    ], "Welcome to Anigura API :D");
});

// --- Auth ---
$router->post("/auth/register", fn() => $container->get(UserController::class)->store());
$router->post("/auth/login",    fn() => $container->get(AuthController::class)->login());
$router->post("/auth/refresh",  fn() => $container->get(AuthController::class)->refresh());
$router->post("/auth/logout", function() use ($container) {
    AuthMiddleware::handle();
    $container->get(AuthController::class)->logout();
});

// --- Users ---
$router->get("/users", function() use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(UserController::class)->index();
});
$router->get("/users/:id", function($id) use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(UserController::class)->show($id);
});
$router->post("/users/search", function() use ($container) {
    AuthMiddleware::handle();
    $container->get(UserController::class)->searchByEmail();
});
$router->patch("/users/:id", function($id) use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(UserController::class)->update((int)$id);
});
$router->delete("/users/:id", function($id) use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(UserController::class)->destroy((int)$id);
});

// --- Franchises ---
$router->get("/franchises",     fn() => $container->get(FranchiseController::class)->index());
$router->get("/franchises/:id", fn($id) => $container->get(FranchiseController::class)->show($id));
$router->post("/franchises", function() use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(FranchiseController::class)->store();
});
$router->patch("/franchises/:id", function($id) use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(FranchiseController::class)->update((int)$id);
});
$router->delete("/franchises/:id", function($id) use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(FranchiseController::class)->destroy((int)$id);
});

// --- Publishers ---
$router->get("/publishers",     fn() => $container->get(PublisherController::class)->index());
$router->get("/publishers/:id", fn($id) => $container->get(PublisherController::class)->show($id));
$router->post("/publishers", function() use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(PublisherController::class)->store();
});
$router->patch("/publishers/:id", function($id) use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(PublisherController::class)->update((int)$id);
});
$router->delete("/publishers/:id", function($id) use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(PublisherController::class)->destroy((int)$id);
});

// --- Addresses ---
$router->get("/addresses", function() use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(AddressController::class)->index();
});
$router->get("/addresses/user/:userId", function($userId) use ($container) {
    AuthMiddleware::handle();
    $container->get(AddressController::class)->userDefault((int)$userId);
});
$router->get("/addresses/:id", function($id) use ($container) {
    AuthMiddleware::handle();
    $container->get(AddressController::class)->show((int)$id);
});
$router->post("/addresses", function() use ($container) {
    AuthMiddleware::handle();
    $container->get(AddressController::class)->store();
});
$router->patch("/addresses/:id", function($id) use ($container) {
    AuthMiddleware::handle();
    $container->get(AddressController::class)->update((int)$id);
});
$router->delete("/addresses/:id", function($id) use ($container) {
    AuthMiddleware::handle();
    $container->get(AddressController::class)->destroy((int)$id);
});

// --- Media Entries ---
$router->get("/media_entries",     fn() => $container->get(MediaEntryController::class)->index());
$router->get("/media_entries/:id", fn($id) => $container->get(MediaEntryController::class)->show((int)$id));
$router->post("/media_entries", function() use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(MediaEntryController::class)->store();
});
$router->patch("/media_entries/:id", function($id) use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(MediaEntryController::class)->update((int)$id);
});
$router->delete("/media_entries/:id", function($id) use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(MediaEntryController::class)->destroy((int)$id);
});

// --- Products ---
$router->get("/products",     fn() => $container->get(ProductController::class)->index());
$router->get("/products/:id", fn($id) => $container->get(ProductController::class)->show((int)$id));
$router->post("/products", function() use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(ProductController::class)->store();
});
$router->patch("/products/:id", function($id) use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(ProductController::class)->update((int)$id);
});
$router->delete("/products/:id", function($id) use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(ProductController::class)->destroy((int)$id);
});

// --- Cart ---
$router->get("/cart/:id_user/validate", function($id) use ($container) {
    AuthMiddleware::handle();
    $container->get(CartItemController::class)->validateUserCart((int)$id);
});
$router->get("/cart/:id_user", function($id) use ($container) {
    AuthMiddleware::handle();
    $container->get(CartItemController::class)->show((int)$id);
});
$router->post("/cart/:id_user", function($id) use ($container) {
    AuthMiddleware::handle();
    $container->get(CartItemController::class)->store((int)$id);
});
$router->patch("/cart/:id_user/:id_item", function($id_user, $id_item) use ($container) {
    AuthMiddleware::handle();
    $container->get(CartItemController::class)->update((int)$id_user, (int)$id_item);
});
$router->delete("/cart/:id_user/:id_item", function($id_user, $id_item) use ($container) {
    AuthMiddleware::handle();
    $container->get(CartItemController::class)->destroy((int)$id_user, (int)$id_item);
});

// --- Images ---
$router->get("/images",               fn() => $container->get(ProductImageController::class)->index());
$router->get("/images/cover/:id",     fn($id) => $container->get(ProductImageController::class)->productCover((int)$id));
$router->get("/images/product/:id",   fn($id) => $container->get(ProductImageController::class)->productImages((int)$id));
$router->get("/images/:id",           fn($id) => $container->get(ProductImageController::class)->show((int)$id));
$router->post("/images", function() use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(ProductImageController::class)->store();
});
$router->patch("/images/:id", function($id) use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(ProductImageController::class)->update((int)$id);
});
$router->delete("/images/:id", function($id) use ($container) {
    AuthMiddleware::requireRole("admin");
    $container->get(ProductImageController::class)->destroy((int)$id);
});
