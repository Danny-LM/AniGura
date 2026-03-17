<?php
namespace Controllers;

use Exception;
use Core\BaseController;
use Interfaces\Services\IOrderService;

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

    public function store(int $id_user): void {
        $this->validate(["id_user" => $id_user], ["id_user" => "num"]);

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

    public function userOrders(int $userId): void {
        $this->validate(["id_user" => $userId], ["id_user" => "num"]);
        $p = $this->getPagination();
        $this->paginated($this->service->findByUser($userId, $p["page"], $p["limit"]));
    }

    public function orderDetails(int $userId, int $orderId): void {
        $this->validate(
            ["id_user" => $userId, "id_order" => $orderId],
            ["id_user" => "num", "id_order" => "num"]
        );
        $this->ok($this->service->findWithDetails($userId, $orderId));
    }

    public function cancel(int $userId, int $orderId): void {
        $this->validate(
            ["id_user" => $userId, "id_order" => $orderId],
            ["id_user" => "num", "id_order" => "num"]
        );
        $this->service->cancel($orderId, $userId);
        $this->ok(null, "Order cancelled successfully");
    }
}
