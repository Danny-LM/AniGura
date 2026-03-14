<?php
namespace Core\Interfaces\Models;

use Core\IBaseModel;

interface IProductImageModel extends IBaseModel {
    public function findProductCover(int $productId);
    public function findByProduct(int $productId): array;
}
