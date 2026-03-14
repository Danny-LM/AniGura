<?php
namespace Interfaces\Models;

use Core\IBaseModel;

interface IRefreshTokenModel extends IBaseModel {
    public function findByToken(string $token): array|false;
    public function deleteByToken(string $token): bool;
    public function deleteByUser(int $userId): bool;
    public function deleteExpired(): bool;
}
