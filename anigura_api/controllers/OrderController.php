<?php
namespace Controllers;

use Exception;
use Core\BaseController;
use Interfaces\Services\IOrderService;
use Core\AuthMiddleware;

class OrderController extends BaseController {
    public function __construct(private IOrderService $service) {}

    public function index(): void {
        $p = $this->getPagination();
        $this->paginated($this->service->findAll($p["page"], $p["limit"]));
    }

    public function show($id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $this->ok($this->service->find((int)$id));
    }

    public function store(): void {
        $id_user = AuthMiddleware::$currentUserId; 

        $data = $this->getBody();
        $validated = $this->validate($data, [
            "id_address" => "!null|num"
        ]);
        if (empty($validated)) throw new Exception("No valid data provided", 400);

        $id = $this->service->createFromCart($id_user, $validated);
        $this->json(201, ["id" => $id], "Order created successfully");
    }

    public function update(int $id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "status"        => "",
            "shipping_addr" => "max:500"
        ]);
        if (empty($validated)) throw new Exception("No valid fields provided for update", 400);

        $this->service->update($id, $validated);
        $this->ok(null, "Order updated successfully");
    }

    public function destroy(int $id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $this->service->delete($id);
        $this->ok(null, "Order deleted successfully");
    }

    public function userOrders(): void {
        $userId = AuthMiddleware::$currentUserId; 

        $p = $this->getPagination();
        $this->paginated($this->service->findByUser($userId, $p["page"], $p["limit"]));
    }

    public function orderDetails(int $orderId): void {
        $userId = AuthMiddleware::$currentUserId; 

        $this->validate(
            ["id_order" => $orderId],
            ["id_order" => "num"]
        );
        $this->ok($this->service->findWithDetails($userId, $orderId));
    }

    public function cancel(int $orderId): void {
        $userId = AuthMiddleware::$currentUserId; 

        $this->validate(
            ["id_order" => $orderId],
            ["id_order" => "num"]
        );
        $this->service->cancel($orderId, $userId);
        $this->ok(null, "Order cancelled successfully");
    }
}
