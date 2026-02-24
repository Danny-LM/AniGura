<?php
namespace Services;

use Core\Validator;
use Exception;
use Models\{ ProductModel, MangaVolumeDetailModel, FigureDetailModel, SetboxDetailModel };

class ProductService {
    private $model;
    private $detailMap;

    public function __construct(
        ProductModel $model,
        MangaVolumeDetailModel $mangaModel,
        FigureDetailModel $figureModel,
        SetboxDetailModel $setboxModel
    ) {

        $this->model = $model;
        $this->detailMap = [
            "manga_volume" => [
                "model" => $mangaModel,
                "rules" => [
                    "id_publisher" => "!null|num",
                    "id_media"     => "num", // def null
                    "volume"       => "num" // def null
                ]
            ],
            "figure" => [
                "model" => $figureModel,
                "rules" => [
                    "brand"        => "!null|max:100",
                    "scale"        => "max:20" // def null
                ]
            ],
            "setbox" => [
                "model" => $setboxModel,
                "rules" => [
                    "id_media"        => "num", // def null
                    "content"         => "!null", // def null
                    "is_limited"      => "bool" // def: false
                ]
            ]
        ];
    }

    public function findAll() {
        $products = $this->model->all();
        if (empty($products)) return [];

        $groupedByType = [];
        foreach ($products as $p) {
            $groupedByType[$p["product_type"]][] = $p["id"];
        }

        $allDetails = [];
        foreach ($groupedByType as $type => $ids) {
            if (isset($this->detailMap[$type])) {
                $details = $this->detailMap[$type]["model"]->findInIds($ids);
                foreach ($details as $d) {
                    $allDetails[$type][$d["id_product"]] = $d;
                }
            }
        }

        return array_map(function($p) use ($allDetails) {
            $p["details"] = $allDetails[$p["product_type"]][$p["id"]] ?? null;
            return $p;
        }, $products);
    }

    public function find(int $id) {
        $product = $this->model->find($id);
        if (!$product) throw new Exception("Product not found", 404);

        $type = $product["product_type"];
        if (isset($this->detailMap[$type])) {
            $product["details"] = $this->detailMap[$type]["model"]->find($id);
        }

        return $product;
    }

    public function create(array $data) {
        $type = $data["product_type"] ?? null;
        if (!isset($this->detailMap[$type])) throw new Exception("Invalid product type", 400);

        if (($data["price"] ?? 0) < 0) throw new Exception("Price cannot be negative", 400);
        if (($data["stock"] ?? 0) < 0) throw new Exception("Stock cannot be negative", 400);

        $details = $data["details"] ?? [];
        unset($data["details"]);

        $detailRules = $this->getValidationRules($type, false);
        $validatedDetails = Validator::validate($details, $detailRules);

        return $this->model->transaction(function() use ($data, $validatedDetails, $type) {
            $productId = $this->model->save($data);
            if (!$productId) throw new Exception("Error creating product", 500);

            $validatedDetails["id_product"] = $productId;
            $this->detailMap[$type]["model"]->save($validatedDetails);

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
            $detailRules = $this->getValidationRules($type, true);
            $validatedDetails = Validator::validate($details, $detailRules);
        }

        return $this->model->transaction(function() use ($id, $data, $validatedDetails, $type) {
            if (!empty($data)) $this->model->update($id, $data);
            
            if (!empty($validatedDetails) && isset($this->detailMap[$type])) {
                $this->detailMap[$type]["model"]->update($id, $validatedDetails);
            }

            return true;
        });
    }

    public function delete(int $id) {
        if (!$this->model->exists($id)) throw new Exception("Product not found", 404);

        return $this->model->delete($id);
    }

    public function getValidationRules(string $type, bool $isUpdate = false): array {
        if (!isset($this->detailMap[$type])) return [];

        $rules = $this->detailMap[$type]["rules"];

        if ($isUpdate) {
            return array_map(function($rule) {
                $parts = explode("|", $rule);
                $filteredParts = array_diff($parts, ["!null"]);
                
                return implode("|", $filteredParts);
            }, $rules);
        }

        return $rules;
    }
}
