<?php
namespace Controllers;

use Interfaces\Services\IFranchiseService;
use Core\BaseController;
use Exception;

class FranchiseController extends BaseController {
    private $service;

    public function __construct(IFranchiseService $service) {
        $this->service = $service;
    }

    public function index(): void {
        $this->ok($this->service->findAll());
    }

    public function show($id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $this->ok($this->service->find((int)$id));
    }

    public function store(): void {
        $data = $this->getBody();
        // TODO: Apply validation rules using $this->validate()
        $validated = $this->validate($data, [
            "name" => "!null|max:255",
            "synopsis" => ""
        ]);
        if (empty($validated)) throw new Exception("No valid data provided", 400);

        $id = $this->service->create($validated);
        $this->json(201, ["id" => $id], "Franchise created successfully");
    }

    public function update(int $id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $data = $this->getBody();
        // TODO: Apply validation rules using $this->validate()
        $validated = $this->validate($data, [
            "name" => "max:255",
            "synopsis" => ""
        ]);
        if (empty($validated)) throw new Exception("No valid fields provided for update", 400);

        $this->service->update($id, $validated);
        $this->ok(null, "Franchise updated successfully");
    }

    public function destroy(int $id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $this->service->delete($id);
        $this->ok(null, "Franchise deleted successfully");
    }
}
