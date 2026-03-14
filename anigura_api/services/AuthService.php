<?php
namespace Services;

use Exception;
use Models\UserModel;
use Core\{ JwtHelper, Config };
use Services\RefreshTokenService;


class AuthService {
    private $userModel, $refreshService;

    public function __construct(UserModel $userModel, RefreshTokenService $refreshService) {
        $this->userModel = $userModel;
        $this->refreshService = $refreshService;
    }

    // public function login(array $data): array {}

    // public function refresh(string $refreshToken): array {}

    // public function logout(string $refreshToken): void {}
}
