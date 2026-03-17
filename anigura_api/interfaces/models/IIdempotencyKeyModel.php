<?php
namespace Interfaces\Models;

use Core\IBaseModel;

interface IIdempotencyKeyModel extends IBaseModel {
    public function findByKeyAndEndpoint(string $keyHash, string $endpoint): array|false;
    public function insertProcessing(string $keyHash, int $userId, string $endpoint): bool;
    public function updateStatus(string $keyHash, string $endpoint, string $status, int $statusCode, $response): bool;
    public function deleteExpired(): bool;
}
