<?php
namespace Controllers;

use Interfaces\Services\IProductImageService;
use Core\BaseController;
use Exception;

class ProductImageController extends BaseController {
    private $service;

    public function __construct(IProductImageService $service) {
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
            "id_product" => "!null|num",
            "image_url"  => "!null|max:500",
            "is_cover"   => "bool"
        ]);
        if (empty($validated)) throw new Exception("No valid data provided", 400);

        $id = $this->service->create($validated);
        $this->json(201, ["id" => $id], "ProductImage created successfully");
    }

    public function update(int $id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $data = $this->getBody();
        // TODO: Apply validation rules using $this->validate()
        $validated = $this->validate($data, [
            "id_product" => "num",
            "image_url"  => "max:500",
            "is_cover"   => "bool"
        ]);
        if (empty($validated)) throw new Exception("No valid fields provided for update", 400);

        $this->service->update($id, $validated);
        $this->ok(null, "ProductImage updated successfully");
    }

    public function destroy(int $id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $this->service->delete($id);
        $this->ok(null, "ProductImage deleted successfully");
    }

    public function productCover(int $productId): void {
        $this->validate(["id_product" => $productId], ["id_product" => "num"]);
        $this->ok($this->service->findProductCover($productId));
    }

    public function productImages(int $productId): void {
        $this->validate(["id_product" => $productId], ["id_product" => "num"]);
        $this->ok($this->service->findByProduct($productId));
    }
}
