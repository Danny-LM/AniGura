<?php
namespace Interfaces\Services;

use Core\IBaseService;

interface IOrderService extends IBaseService {
    
    public function findByUser(int $userId, int $page = 1, int $limit = 20): array;
    public function findWithDetails(int $userId, int $orderId): array;
    public function createFromCart(int $userId, array $data): int;
    public function cancel(int $orderId, int $userId): void;
}
