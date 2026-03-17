<?php
namespace Controllers;

use Interfaces\Services\IProductService;
use Core\BaseController;
use Exception;

class ProductController extends BaseController {
    private $service;

    public function __construct(IProductService $service) {
        $this->service = $service;
    }

    public function index(): void {
        $p = $this->getPagination();
        $this->paginated($this->service->findAll($p["page"], $p["limit"]));
    }

    public function show($id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $this->ok($this->service->find((int)$id));
    }

    public function store(): void {
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "id_franchise" => "!null|num",
            "product_type" => "",
            "name"         => "!null|max:255",
            "description"  => "",
            "price"        => "!null|num",
            "discount"     => "num",
            "stock"        => "!null|num",
            "active"       => "bool",
            "sku"          => "max:60",
            "details"      => "!null"
        ]);
        if (empty($validated)) throw new Exception("No valid data provided", 400);

        $id = $this->service->create($validated);
        $this->json(201, ["id" => $id], "Product created successfully");
    }

    public function update(int $id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "id_franchise" => "num",
            "product_type" => "",
            "name"         => "max:255",
            "description"  => "",
            "price"        => "num",
            "discount"     => "num",
            "stock"        => "num",
            "active"       => "bool",
            "sku"          => "max:60",
            "details"      => ""
        ]);
        if (empty($validated)) throw new Exception("No valid fields provided for update", 400);

        $this->service->update($id, $validated);
        $this->ok(null, "Product updated successfully");
    }

    public function destroy(int $id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $this->service->delete($id);
        $this->ok(null, "Product deleted successfully");
    }
}
