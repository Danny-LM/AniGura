<?php

use Models\{
    UserModel, FranchiseModel, PublisherModel, AddressModel, MediaEntryModel,
    ProductModel, MangaVolumeDetailModel, FigureDetailModel, SetboxDetailModel,
    CartItemModel, ProductImageModel, RefreshTokenModel
};
use Services\{
    UserService, FranchiseService, PublisherService, AddressService, MediaEntryService,
    ProductService, CartItemService, ProductImageService, RefreshTokenService, AuthService
};
use Services\Handlers\{ MangaVolumeDetailHandler, FigureDetailHandler, SetboxDetailHandler };
use Controllers\{
    UserController, FranchiseController, PublisherController, AddressController, MediaEntryController,
    ProductController, CartItemController, ProductImageController, AuthController
};

$container->bind(UserModel::class, fn() => new UserModel());
$container->bind(FranchiseModel::class, fn() => new FranchiseModel());
$container->bind(PublisherModel::class, fn() => new PublisherModel());
$container->bind(AddressModel::class, fn() => new AddressModel());
$container->bind(MediaEntryModel::class, fn() => new MediaEntryModel());
$container->bind(ProductModel::class, fn() => new ProductModel());
$container->bind(CartItemModel::class, fn() => new CartItemModel());
$container->bind(ProductImageModel::class, fn() => new ProductImageModel());
$container->bind(RefreshTokenModel::class, fn() => new RefreshTokenModel());

$container->bind(UserService::class, fn($c) => new UserService($c->get(UserModel::class)));
$container->bind(FranchiseService::class, fn($c) => new FranchiseService($c->get(FranchiseModel::class)));
$container->bind(PublisherService::class, fn($c) => new PublisherService($c->get(PublisherModel::class)));
$container->bind(AddressService::class, fn($c) => new AddressService($c->get(AddressModel::class), $c->get(UserModel::class)));
$container->bind(MediaEntryService::class, fn($c) => new MediaEntryService($c->get(MediaEntryModel::class), $c->get(FranchiseModel::class)));
$container->bind(CartItemService::class, fn($c) => new CartItemService($c->get(CartItemModel::class), $c->get(ProductModel::class)));
$container->bind(ProductImageService::class, fn($c) => new ProductImageService($c->get(ProductImageModel::class), $c->get(ProductModel::class)));
$container->bind(RefreshTokenService::class, fn($c) => new RefreshTokenService($c->get(RefreshTokenModel::class), $c->get(UserModel::class)));
$container->bind(AuthService::class, fn($c) => new AuthService($c->get(UserModel::class), $c->get(RefreshTokenService::class)));

$container->bind(ProductService::class, function($c) {
    return new ProductService(
        $c->get(ProductModel::class),
        $c->get(ProductImageModel::class),
        [
            "manga_volume" => new MangaVolumeDetailHandler(new MangaVolumeDetailModel()),
            "figure"       => new FigureDetailHandler(new FigureDetailModel()),
            "setbox"       => new SetboxDetailHandler(new SetboxDetailModel()),
        ]
    );
});

$container->bind(UserController::class, fn($c) => new UserController($c->get(UserService::class)));
$container->bind(FranchiseController::class, fn($c) => new FranchiseController($c->get(FranchiseService::class)));
$container->bind(PublisherController::class, fn($c) => new PublisherController($c->get(PublisherService::class)));
$container->bind(AddressController::class, fn($c) => new AddressController($c->get(AddressService::class)));
$container->bind(MediaEntryController::class, fn($c) => new MediaEntryController($c->get(MediaEntryService::class)));
$container->bind(ProductController::class, fn($c) => new ProductController($c->get(ProductService::class)));
$container->bind(CartItemController::class, fn($c) => new CartItemController($c->get(CartItemService::class)));
$container->bind(ProductImageController::class, fn($c) => new ProductImageController($c->get(ProductImageService::class)));
$container->bind(AuthController::class, fn($c) => new AuthController($c->get(AuthService::class)));
