<?php
namespace Core\Interfaces\Models;

use Core\IBaseModel;

interface ICartItemModel extends IBaseModel {
    public function getFullCart(int $userId): array;
}
