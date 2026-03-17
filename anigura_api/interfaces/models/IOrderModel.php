<?php
namespace Interfaces\Models;

use Core\IBaseModel;

interface IOrderModel extends IBaseModel {

    public function findByUser(int $userId, int $page = 1, int $limit = 20): array;
}
