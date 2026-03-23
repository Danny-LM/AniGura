<?php
namespace Interfaces\Services;

use Core\IBaseService;

interface IProductService extends IBaseService {
    public function findAll(int $page = 1, int $limit = 20, ?string $type = null);
}
