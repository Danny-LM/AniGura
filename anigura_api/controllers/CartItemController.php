<?php
namespace Controllers;

use Interfaces\Services\ICartItemService;
use Core\BaseController;
use Exception;

class CartItemController extends BaseController {
    private $service;

    public function __construct(ICartItemService $service) {
        $this->service = $service;
    }

    public function show($id_user): void {
        $this->validate(["id_user" => $id_user], ["id_user" => "num"]);

        $cart = $this->service->getCart((int)$id_user);
        $this->ok($cart);
    }

    public function store($id_user): void {
        $this->validate(["id_user" => $id_user], ["id_user" => "!null|num"]);
        
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "id_product" => "!null|num",
            "quantity"   => "num"
        ]);
        if (empty($validated)) throw new Exception("No valid data provided", 400);

        $this->service->addItem((int)$id_user, $validated);
        $this->json(201, null, "Item added to cart");
    }

    public function update(int $id_user, $id_item): void {
        $this->validate(
            [ "id_user" => $id_user, "id_item" => $id_item ],
            [ "id_user" => "!null|num", "id_item" => "!null|num" ]
        );

        $data = $this->getBody();
        $validated = $this->validate($data, [
            "quantity" => "!null|num"
        ]);
        if (empty($validated)) throw new Exception("No valid fields provided for update", 400);

        $this->service->updateQty($id_item, $id_user, $validated["quantity"]);
        $this->ok(null, "Quantity updated");
    }

    public function destroy(int $id_user, $id_item): void {
        $this->validate(
            [ "id_user" => $id_user, "id_item" => $id_item ],
            [ "id_user" => "!null|num", "id_item" => "!null|num" ]
        );

        $this->service->removeItem($id_user, $id_item);
        $this->ok(null, "Item removed from cart");
    }

    public function validateUserCart(int $id_user): void {
        $this->validate(["id_user" => $id_user], ["id_user" => "num"]);
        $this->ok($this->service->validateCart($id_user));
    }
}
