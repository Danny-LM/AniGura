<?php
namespace Services;

use Interfaces\Models\{ IProductImageModel, IProductModel };
use Exception;

class ProductImageService {
    private $model, $productModel;

    public function __construct(IProductImageModel $model, IProductModel $productModel) {
        $this->model = $model;
        $this->productModel = $productModel;
    }

    public function findAll(int $page = 1, int $limit = 20) {
        return $this->model->all($page, $limit);
    }

    public function find(int $id) {
        $item = $this->model->find($id);
        if (!$item) throw new Exception("ProductImage not found", 404);

        return $item;
    }

    public function create(array $data) {
        if (!$this->productModel->exists($data["id_product"])) throw new Exception("Product not found", 404);

        return $this->model->transaction(function() use ($data) {
            if (!empty($data["is_cover"]) && $data["is_cover"] == true) {
                $this->unsetOtherCovers((int)$data['id_product']);
            }

            return $this->model->save($data);
        });
    }

    public function update(int $id, array $data) {
        $current = $this->model->find($id);
        if (!$current) throw new Exception("ProductImage not found", 404);
        
        if (isset($data["id_product"]) && !$this->productModel->exists($data["id_product"])) {
            throw new Exception("Product not found", 404);
        }

        return $this->model->transaction(function() use ($id, $data, $current) {
            if (isset($data["is_cover"]) && $data["is_cover"] == true) {
                $productId = $data["id_product"] ?? $current["id_product"];
                $this->unsetOtherCovers($productId);
            }

            return $this->model->update($id, $data);
        });
    }

    public function delete(int $id) {
        if (!$this->model->exists($id)) throw new Exception("ProductImage not found", 404);

        return $this->model->delete($id);
    }

    public function findProductCover(int $productId) {
        return $this->model->findProductCover($productId);
    }

    public function findByProduct(int $productId) {
        return $this->model->findByProduct($productId);
    }

    private function unsetOtherCovers(int $productId) {
        $currentCover = $this->model->where(
            [ "id_product" => $productId, "is_cover"   => 1 ]
        );

        foreach ($currentCover as $img) {
            $this->model->update($img["id"], ["is_cover" => 0]);
        }
    }
}
