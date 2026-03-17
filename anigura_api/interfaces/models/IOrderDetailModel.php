<?php
namespace Interfaces\Models;

use Core\IBaseModel;

interface IOrderDetailModel extends IBaseModel {
    
    public function findByOrder(int $orderId): array;
}
