<?php
namespace Controllers;

use Exception;
use Core\{ BaseController, AuthMiddleware, IdempotencyMiddleware };
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

    public function store(): void {
        $id_user = AuthMiddleware::$currentUserId;
        $idem = IdempotencyMiddleware::handle("POST /orders", "Order already created");

        $data = $this->getBody();
        $validated = $this->validate($data, [
            "id_address" => "!null|num",
            "item_ids"   => ""
        ]);
        if (empty($validated)) throw new Exception("No valid data provided", 400);

        if (isset($validated["item_ids"]) && !is_array($validated["item_ids"])) {
            throw new Exception("Invalid item_ids format. It must be an array", 400);
        }

        IdempotencyMiddleware::begin($idem, $id_user);

        try {
            $id = $this->service->createFromCart($id_user, $validated);
            IdempotencyMiddleware::complete($idem, 201, ["id" => $id]);
            $this->json(201, ["id" => $id], "Order created successfully");

        } catch (Exception $e) {
            IdempotencyMiddleware::fail($idem);
            throw $e;
        }
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
        $idem = IdempotencyMiddleware::handle("PATCH /orders/cancel", "Order already cancelled");

        $this->validate(
            ["id_order" => $orderId],
            ["id_order" => "num"]
        );

        IdempotencyMiddleware::begin($idem, $userId);

        try {
            $this->service->cancel($orderId, $userId);
            IdempotencyMiddleware::complete($idem, 200, null);
            $this->ok(null, "Order cancelled successfully");

        } catch (Exception $e) {
            IdempotencyMiddleware::fail($idem);
            throw $e;
        }
    }
}
