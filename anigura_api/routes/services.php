<?php
use Interfaces\Models\{
    IUserModel, IFranchiseModel, IPublisherModel, IAddressModel, IMediaEntryModel,
    IProductModel, ICartItemModel, IProductImageModel, IRefreshTokenModel, IOrderModel,
    IOrderDetailModel, IStockReservationModel,
};
use Interfaces\Services\{
    IUserService, IFranchiseService, IPublisherService, IAddressService, IMediaEntryService,
    IProductService, ICartItemService, IProductImageService, IRefreshTokenService, IAuthService,
    IOrderService, IStockReservationService,
};
use Models\{
    UserModel, FranchiseModel, PublisherModel, AddressModel, MediaEntryModel,
    ProductModel, MangaVolumeDetailModel, FigureDetailModel, SetboxDetailModel,
    CartItemModel, ProductImageModel, RefreshTokenModel, OrderModel, OrderDetailModel,
    StockReservationModel,
};
use Services\{
    UserService, FranchiseService, PublisherService, AddressService, MediaEntryService,
    ProductService, CartItemService, ProductImageService, RefreshTokenService, AuthService,
    OrderService, StockReservationService,
};
use Services\Handlers\{ MangaVolumeDetailHandler, FigureDetailHandler, SetboxDetailHandler };
use Controllers\{
    UserController, FranchiseController, PublisherController, AddressController, MediaEntryController,
    ProductController, CartItemController, ProductImageController, AuthController, OrderController,
    StockReservationController,
};

// --- Models ---
$container->bind(IUserModel::class,          fn() => new UserModel());
$container->bind(IFranchiseModel::class,     fn() => new FranchiseModel());
$container->bind(IPublisherModel::class,     fn() => new PublisherModel());
$container->bind(IAddressModel::class,       fn() => new AddressModel());
$container->bind(IMediaEntryModel::class,    fn() => new MediaEntryModel());
$container->bind(IProductModel::class,       fn() => new ProductModel());
$container->bind(ICartItemModel::class,      fn() => new CartItemModel());
$container->bind(IProductImageModel::class,  fn() => new ProductImageModel());
$container->bind(IRefreshTokenModel::class,  fn() => new RefreshTokenModel());
$container->bind(IOrderModel::class,         fn() => new OrderModel());
$container->bind(IOrderDetailModel::class, fn() => new OrderDetailModel());
$container->bind(IStockReservationModel::class, fn() => new StockReservationModel());

// --- Services ---
$container->bind(IUserService::class,        fn($c) => new UserService($c->get(IUserModel::class)));
$container->bind(IFranchiseService::class,   fn($c) => new FranchiseService($c->get(IFranchiseModel::class)));
$container->bind(IPublisherService::class,   fn($c) => new PublisherService($c->get(IPublisherModel::class)));
$container->bind(IAddressService::class,     fn($c) => new AddressService($c->get(IAddressModel::class), $c->get(IUserModel::class)));
$container->bind(IMediaEntryService::class,  fn($c) => new MediaEntryService($c->get(IMediaEntryModel::class), $c->get(IFranchiseModel::class)));
$container->bind(IStockReservationService::class, fn($c) => new StockReservationService($c->get(IStockReservationModel::class)));
$container->bind(ICartItemService::class, fn($c) => new CartItemService(
    $c->get(ICartItemModel::class),
    $c->get(IProductModel::class),
    $c->get(IUserModel::class),
    $c->get(IStockReservationModel::class)
));
$container->bind(IProductImageService::class,fn($c) => new ProductImageService($c->get(IProductImageModel::class), $c->get(IProductModel::class)));
$container->bind(IRefreshTokenService::class,fn($c) => new RefreshTokenService($c->get(IRefreshTokenModel::class), $c->get(IUserModel::class)));
$container->bind(IAuthService::class,        fn($c) => new AuthService($c->get(IUserModel::class), $c->get(IRefreshTokenService::class)));
$container->bind(IProductService::class, function($c) {
    return new ProductService(
        $c->get(IProductModel::class),
        $c->get(IProductImageModel::class),
        [
            "manga_volume" => new MangaVolumeDetailHandler(new MangaVolumeDetailModel()),
            "figure"       => new FigureDetailHandler(new FigureDetailModel()),
            "setbox"       => new SetboxDetailHandler(new SetboxDetailModel()),
        ]
    );
});
$container->bind(IOrderService::class, fn($c) => new OrderService(
    $c->get(IOrderModel::class),
    $c->get(IOrderDetailModel::class),
    $c->get(ICartItemModel::class),
    $c->get(IProductModel::class),
    $c->get(IUserModel::class),
    $c->get(IAddressModel::class)
));

// --- Controllers ---
$container->bind(UserController::class,         fn($c) => new UserController($c->get(IUserService::class)));
$container->bind(FranchiseController::class,    fn($c) => new FranchiseController($c->get(IFranchiseService::class)));
$container->bind(PublisherController::class,    fn($c) => new PublisherController($c->get(IPublisherService::class)));
$container->bind(AddressController::class,      fn($c) => new AddressController($c->get(IAddressService::class)));
$container->bind(MediaEntryController::class,   fn($c) => new MediaEntryController($c->get(IMediaEntryService::class)));
$container->bind(ProductController::class,      fn($c) => new ProductController($c->get(IProductService::class)));
$container->bind(CartItemController::class,     fn($c) => new CartItemController($c->get(ICartItemService::class)));
$container->bind(ProductImageController::class, fn($c) => new ProductImageController($c->get(IProductImageService::class)));
$container->bind(OrderController::class, fn($c) => new OrderController($c->get(IOrderService::class)));
$container->bind(StockReservationController::class, fn($c) => new StockReservationController($c->get(IStockReservationService::class)));
$container->bind(AuthController::class,  fn($c) => new AuthController(
    $c->get(IAuthService::class),
    $c->get(IUserService::class),
));
