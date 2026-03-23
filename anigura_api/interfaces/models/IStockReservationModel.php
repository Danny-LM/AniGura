<?php
namespace Interfaces\Models;

use Core\IBaseModel;

interface IStockReservationModel extends IBaseModel {
    public function findActiveByProduct(int $productId): array;
    public function findByToken(string $token): array|false;
    public function findActiveByUser(int $userId): array;
    public function deleteExpired(): int;
    public function deleteByToken(string $token): bool;
    public function getAvailableStock(int $productId): int;
    public function getAvailableStockBulk(array $productIds): array;
    public function findByIdempotencyKey(string $key): array|false;
}
