<?php
namespace Services;

use Interfaces\Models\{ ICartItemModel, IProductModel, IUserModel, IStockReservationModel};
use Interfaces\Services\ICartItemService;
use Exception;

class CartItemService implements ICartItemService {
    private $model, $productModel, $userModel, $reservationModel;

    public function __construct(ICartItemModel $model, IProductModel $productModel, IUserModel $userModel, IStockReservationModel $reservationModel) {
        $this->model = $model;
        $this->productModel = $productModel;
        $this->userModel = $userModel;
        $this->reservationModel = $reservationModel;
    }

    public function findAll(int $page = 1, int $limit = 20) {
        return $this->model->all($page, $limit);
    }

    public function find(int $id) {
        $item = $this->model->find($id);
        if (!$item) throw new Exception("CartItem not found", 404);

        return $item;
    }

    public function create(array $data) {
        if (!$this->productModel->exists($data["id_product"])) throw new Exception("Product not found", 404);
        if (!$this->userModel->exists($data["id_user"])) throw new Exception("User not found", 404);

        return $this->model->save($data);
    }

    public function update(int $id, array $data) {
        if (!$this->model->exists($id)) throw new Exception("CartItem not found", 404);

        if (isset($data["id_product"]) && !$this->productModel->exists($data["id_product"])) {
            throw new Exception("Product not found", 404);
        }
        if (isset($data["id_user"]) && !$this->userModel->exists($data["id_user"])) {
            throw new Exception("User not found", 404);
        }

        return $this->model->update($id, $data);
    }

    public function delete(int $id) {
        if (!$this->model->exists($id)) throw new Exception("CartItem not found", 404);

        return $this->model->delete($id);
    }

    public function getCart(int $userId): array {
        return $this->model->getFullCart($userId);
    }

    public function addItem(int $userId, array $data) {
        $productId = $data["id_product"];
        $quantity = $data["quantity"] ?? 1;

        return $this->model->transaction(function() use ($userId, $productId, $quantity) {
            $product = $this->productModel->find($productId);

            if (!$product || !$product["active"]) throw new Exception("Product unavailable", 404);
            $available = $this->reservationModel->getAvailableStock($productId);
            if ($available < $quantity) throw new Exception("Only {$product['stock']} units available", 400);


            $existing = $this->model->where(["id_user" => $userId, "id_product" => $productId]);

            if (!empty($existing)) {
                $item = $existing[0];
                $newQty = $item["quantity"] + $quantity;

                if ($available < $newQty) throw new Exception("Only {$available} units available", 400);

                return $this->model->update($item["id"], ["quantity" => $newQty]);
            }

            return $this->model->save([
                "id_user"    => $userId,
                "id_product" => $productId,
                "quantity"   => $quantity
            ]);
        });
    }

    public function updateQty(int $itemId, int $userId, int $qty) {
        $item = $this->model->find($itemId);

        if (!$item || $item["id_user"] != $userId) throw new Exception("Item not found", 404);
        if ($qty <= 0) return $this->model->delete($itemId);

        $product = $this->productModel->find($item["id_product"]);
        $available = $this->reservationModel->getAvailableStock($item["id_product"]);
        if ($available < $qty) throw new Exception("Only {$product['stock']} units available", 400);

        return $this->model->update($itemId, ["quantity" => $qty]);
    }

    public function removeItem(int $userId, int $itemId) {
        $item = $this->model->find($itemId);
        if (!$item || $item["id_user"] != $userId) throw new Exception("Item not found", 404);

        return $this->model->delete($itemId);
    }
}
