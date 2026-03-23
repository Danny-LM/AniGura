<?php
namespace Interfaces\Services;

use Core\IBaseService;

interface ICartItemService extends IBaseService {
    public function getCart(int $userId): array;
    public function addItem(int $userId, array $data);
    public function updateQty(int $itemId, int $userId, int $qty);
    public function removeItem(int $userId, int $itemId);
}
