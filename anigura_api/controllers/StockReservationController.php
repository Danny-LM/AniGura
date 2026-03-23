<?php
namespace Controllers;

use Exception;
use Core\BaseController;
use Interfaces\Services\IStockReservationService;

class StockReservationController extends BaseController {
    public function __construct(private IStockReservationService $service) {}

    public function index(): void {
        $this->ok($this->service->findAll());
    }

    public function show($id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $this->ok($this->service->find((int)$id));
    }

    public function store(): void {
        $data = $this->getBody();
        $validated = $this->validate($data, []);
        if (empty($validated)) throw new Exception("No valid data provided", 400);

        $id = $this->service->create($validated);
        $this->json(201, ["id" => $id], "StockReservation created successfully");
    }

    public function update(int $id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $data = $this->getBody();
        $validated = $this->validate($data, []);
        if (empty($validated)) throw new Exception("No valid fields provided for update", 400);

        $this->service->update($id, $validated);
        $this->ok(null, "StockReservation updated successfully");
    }

    public function destroy(int $id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $this->service->delete($id);
        $this->ok(null, "StockReservation deleted successfully");
    }
}
