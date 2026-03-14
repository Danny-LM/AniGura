<?php
namespace Interfaces\Services;

use Core\IBaseService;

interface IRefreshTokenService extends IBaseService {
    public function findByToken(string $token): array;
    public function deleteByToken(string $token): void;
}
