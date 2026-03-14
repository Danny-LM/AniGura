<?php
namespace Services;

use Core\Validator;
use Exception;
use InvalidArgumentException;
use Models\{ ProductModel, ProductImageModel };
use Services\Handlers\ProductDetailHandlerInterface;

class ProductService {
    private $model, $imageModel;
    private $handlers;

    public function __construct(ProductModel $model, ProductImageModel $imageModel, array $handlers) {
        foreach ($handlers as $type => $handler) {
            if (!$handler instanceof ProductDetailHandlerInterface) {
                throw new InvalidArgumentException("Handler for type $type must implement ProductDetailHandlerInterface");
            }
        }
        
        $this->model = $model;
        $this->imageModel = $imageModel;
        $this->handlers = $handlers;
    }

    public function findAll() {
        $products = $this->model->all();

        if (empty($products)) return [];

        $allCovers = $this->imageModel->where(["is_cover" => 1]);
        $coversMap = [];

        foreach ($allCovers as $img) {
            $coversMap[$img["id_product"]] = $img["image_url"];
        }

        $groupedByType = [];

        foreach ($products as $p) {
            $groupedByType[$p["product_type"]][] = $p["id"];
        }

        $allDetails = [];
        foreach ($groupedByType as $type => $ids) {
            if (isset($this->handlers[$type])) {
                $details = $this->handlers[$type]->getModel()->findInIds($ids);

                foreach ($details as $d) {
                    $allDetails[$type][$d["id_product"]] = $d;
                }
            }
        }

        return array_map(function($p) use ($allDetails, $coversMap) {
            $p["details"] = $allDetails[$p["product_type"]][$p["id"]] ?? null;
            $p["cover_image"] = $coversMap[$p["id"]] ?? null;

            return $p;
        }, $products);
    }

    public function find(int $id) {
        $product = $this->model->find($id);
        if (!$product) throw new Exception("Product not found", 404);

        $covers = $this->imageModel->where(["id_product" => $id, "is_cover" => 1]);
        $product["cover_image"] = !empty($covers) ? $covers[0]["image_url"] : null;

        $type = $product["product_type"];
        if (isset($this->handlers[$type])) {
            $product["details"] = $this->handlers[$type]->getModel()->find($id);
        }

        return $product;
    }

    public function create(array $data) {
        $type = $data["product_type"] ?? null;
        if (!isset($this->handlers[$type])) throw new Exception("Invalid product type", 400);

        if (($data["price"] ?? 0) < 0) throw new Exception("Price cannot be negative", 400);
        if (($data["stock"] ?? 0) < 0) throw new Exception("Stock cannot be negative", 400);

        $details = $data["details"] ?? [];
        unset($data["details"]);

        $rules = $this->handlers[$type]->getRules(false);
        $validatedDetails = Validator::validate($details, $rules);

        return $this->model->transaction(function() use ($data, $validatedDetails, $type) {
            $productId = $this->model->save($data);
            if (!$productId) throw new Exception("Error creating product", 500);

            $validatedDetails["id_product"] = $productId;
            $this->handlers[$type]->getModel()->save($validatedDetails);

            return $productId;
        });
    }

    public function update(int $id, array $data) {
        $current = $this->model->find($id);
        if (!$current) throw new Exception("Product not found", 404);

        if (isset($data["price"]) && $data["price"] < 0) throw new Exception("Price cannot be negative", 400);
        if (isset($data["stock"]) && $data["stock"] < 0) throw new Exception("Stock cannot be negative", 400);

        $type = $current["product_type"];
        $details = $data["details"] ?? null;
        unset($data["details"]);

        $validatedDetails = null;

        if ($details !== null) {
            $rules = $this->handlers[$type]->getRules(true);
            $validatedDetails = Validator::validate($details, $rules);
        }

        return $this->model->transaction(function() use ($id, $data, $validatedDetails, $type) {
            if (!empty($data)) $this->model->update($id, $data);

            if (!empty($validatedDetails) && isset($this->handlers[$type])) {
                $this->handlers[$type]->getModel()->update($id, $validatedDetails);
            }

            return true;
        });
    }

    public function delete(int $id) {
        if (!$this->model->exists($id)) throw new Exception("Product not found", 404);

        return $this->model->delete($id);
    }
}
