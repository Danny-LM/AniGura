<?php
namespace Controllers;

use Interfaces\Services\ICartItemService;
use Core\BaseController;
use Core\AuthMiddleware;
use Exception;

class CartItemController extends BaseController {
    private $service;

    public function __construct(ICartItemService $service) {
        $this->service = $service;
    }

    public function show(): void {
        $userId = AuthMiddleware::$currentUserId; 

        $cart = $this->service->getCart((int)$userId);
        $this->ok($cart);
    }

    public function store(): void {
        $userId = AuthMiddleware::$currentUserId;
        
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "id_product" => "!null|num",
            "quantity"   => "num"
        ]);
        if (empty($validated)) throw new Exception("No valid data provided", 400);

        $this->service->addItem((int)$userId, $validated);
        $this->json(201, null, "Item added to cart");
    }

    public function update($id_item): void {
        $userId = AuthMiddleware::$currentUserId;

        $this->validate(
            [ "id_item" => $id_item ],
            [ "id_item" => "!null|num" ]
        );

        $data = $this->getBody();
        $validated = $this->validate($data, [
            "quantity" => "!null|num"
        ]);
        if (empty($validated)) throw new Exception("No valid fields provided for update", 400);

        $this->service->updateQty($id_item, $userId, $validated["quantity"]);
        $this->ok(null, "Quantity updated");
    }

    public function destroy($id_item): void {
        $userId = AuthMiddleware::$currentUserId;

        $this->validate(
            [ "id_item" => $id_item ],
            [ "id_item" => "!null|num" ]
        );

        $this->service->removeItem($userId, $id_item);
        $this->ok(null, "Item removed from cart");
    }

    public function validateUserCart(): void {
        $userId = AuthMiddleware::$currentUserId;
        $this->ok($this->service->validateCart($userId));
    }
}
