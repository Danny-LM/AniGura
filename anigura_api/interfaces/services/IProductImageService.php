<?php
namespace Interfaces\Services;

use Core\IBaseService;

interface IProductImageService extends IBaseService {
    public function findProductCover(int $productId);
    public function findByProduct(int $productId): array;
}
