<?php
namespace Models;

use Core\BaseModel;
use Core\Interfaces\Models\IProductImageModel;

class ProductImageModel extends BaseModel implements IProductImageModel {
    protected $table = "product_images";
    protected $primaryKey = "id";

    public function findProductCover(int $productId) {
        $result = $this->where([ "id_product" => $productId, "is_cover" => 1 ]);
        return $result[0] ?? null;
    }

    public function findByProduct(int $productId): array {
        return $this->where([ "id_product" => $productId ]);
    }
}
