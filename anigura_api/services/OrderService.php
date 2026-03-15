<?php
namespace Services;

use Exception;
use Interfaces\Models\{
    IAddressModel, ICartItemModel, IOrderModel, IOrderDetailModel,
    IProductModel, IUserModel
};
use Interfaces\Services\IOrderService;

class OrderService implements IOrderService {
    private $model, $detailModel, $cartModel, $productModel, $userModel, $addressModel;

    public function __construct(
        IOrderModel $model,
        IOrderDetailModel $detailModel,
        ICartItemModel $cartModel,
        IProductModel $productModel,
        IUserModel $userModel,
        IAddressModel $addressModel
    ) {
        $this->model        = $model;
        $this->detailModel  = $detailModel;
        $this->cartModel    = $cartModel;
        $this->productModel = $productModel;
        $this->userModel    = $userModel;
        $this->addressModel = $addressModel;
    }

    public function findAll(int $page = 1, int $limit = 20) {
        return $this->model->all($page, $limit);
    }

    public function find(int $id) {
        $order = $this->model->find($id);
        if (!$order) throw new Exception("Order not found", 404);
        return $order;
    }

    public function findByUser(int $userId, int $page = 1, int $limit = 20): array {
        if (!$this->userModel->exists($userId)) throw new Exception("User not found", 404);
        return $this->model->findByUser($userId, $page, $limit);
    }

    public function findWithDetails(int $userId, int $orderId): array {
        $order = $this->model->find($orderId);
        if (!$order) throw new Exception("Order not found", 404);
        if ($order["id_user"] !== $userId) throw new Exception("Access denied", 403);

        $order["details"] = $this->detailModel->findByOrder($orderId);
        return $order;
    }

    public function createFromCart(int $userId, array $data): int {
        $cart = $this->cartModel->getFullCart($userId);
        if (empty($cart)) throw new Exception("Cart is empty", 400);
        if (!$this->userModel->exists($userId)) throw new Exception("User not found", 404);

        $address = $this->addressModel->find($data["id_address"]);
        if (!$address) throw new Exception("Address not found", 404);
        if ($address["id_user"] !== $userId) throw new Exception("Access denied", 403);

        $snapshot = implode(",", array_filter([
            $address["alias"] ?? null,
            $address["street"],
            $address["city"],
            $address["state"],
            $address["zip_code"]
        ]));

        return $this->model->transaction(function() use ($userId, $snapshot, $cart) {
            $total = 0;

            // Lock Resource and Validate Stock
            foreach ($cart as $item) {
                $product = $this->productModel->findForUpdate($item["id_product"]);

                if (!$product || !$product["active"]) {
                    throw new Exception("Product '{$item['name']}' is no longer available", 400);
                }
                if ($product["stock"] < $item["quantity"]) {
                    throw new Exception("Insufficient stock for '{$item['name']}'. Only {$product['stock']} available", 400);
                }

                $unitPrice = round($product["price"] * (1 - $product["discount"] / 100), 2);
                $total += $unitPrice * $item["quantity"];
            }

            // Create Order
            $orderId = $this->model->save([
                "id_user"       => $userId,
                "shipping_addr" => $snapshot,
                "total_amount"  => round($total, 2),
                "status"        => "pending"
            ]);
            if (!$orderId) throw new Exception("Error creating order", 500);

            // Create Order Details and Discount Stock
            foreach ($cart as $item) {
                $product   = $this->productModel->findForUpdate($item["id_product"]);
                $unitPrice = round($product["price"] * (1 - $product["discount"] / 100), 2);

                $this->detailModel->save([
                    "id_order"   => $orderId,
                    "id_product" => $item["id_product"],
                    "quantity"   => $item["quantity"],
                    "unit_price" => $unitPrice
                ]);

                $this->productModel->update($item["id_product"], [
                    "stock" => $product["stock"] - $item["quantity"]
                ]);
            }

            // Clear Cart
            foreach ($cart as $item) {
                $existing = $this->cartModel->where([
                    "id_user"    => $userId,
                    "id_product" => $item["id_product"]
                ]);
                if (!empty($existing)) {
                    $this->cartModel->delete($existing[0]["id"]);
                }
            }

            return $orderId;
        });
    }

    public function create(array $data) {
        throw new Exception("Deprecated method. Use createFromCart instead", 400);
    }

    public function update(int $id, array $data) {
        $order = $this->model->find($id);
        if (!$order) throw new Exception("Order not found", 404);
        if ($order["status"] === "cancelled") throw new Exception("Cannot update a cancelled order", 400);

        return $this->model->update($id, $data);
    }

    public function delete(int $id) {
        throw new Exception("Orders cannot be deleted", 400);
    }

    public function cancel(int $orderId, int $userId): void {
        $order = $this->model->find($orderId);
        if (!$order) throw new Exception("Order not found", 404);
        if ($order["id_user"] !== $userId) throw new Exception("Access denied", 403);
        if ($order["status"] !== "pending") throw new Exception("Only pending orders can be cancelled", 400);

        $this->model->transaction(function() use ($orderId) {
            $details = $this->detailModel->findByOrder($orderId);

            foreach ($details as $detail) {
                $product = $this->productModel->findForUpdate($detail["id_product"]);
                if ($product) {
                    $this->productModel->update($detail["id_product"], [
                        "stock" => $product["stock"] + $detail["quantity"]
                    ]);
                }
            }

            $this->model->update($orderId, ["status" => "cancelled"]);
        });
    }
}
