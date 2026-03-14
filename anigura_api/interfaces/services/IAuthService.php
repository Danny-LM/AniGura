<?php
namespace Interfaces\Services;

interface IAuthService {
    public function login(array $data): array;
    public function refresh(string $refreshToken): array;
    public function logout(string $refreshToken): void;
}
