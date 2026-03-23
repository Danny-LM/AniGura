<?php
namespace Interfaces\Services;

use Core\IBaseService;

interface IStockReservationService extends IBaseService {
    public function reserve(int $userId, array $itemIds);
    public function release(string $token);
    public function validateToken(string $token, int $userId);
}
