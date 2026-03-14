<?php
namespace Services;

use Interfaces\Services\{ IRefreshTokenService, IAuthService };
use Interfaces\Models\IUserModel;
use Core\{ JwtHelper, Config };
use Exception;

class AuthService implements IAuthService {
    private $userModel, $refreshService;

    public function __construct(IUserModel $userModel, IRefreshTokenService $refreshService) {
        $this->userModel = $userModel;
        $this->refreshService = $refreshService;
    }

    public function login(array $data): array {
        $user = $this->userModel->getAuthData($data["email"]);
        if (!$user || !password_verify($data["password"], $user["password"])) {
            throw new Exception("Invalid credentials", 401);
        }

        $accessToken = JwtHelper::generateAccessToken([
            "id" => $user["id"],
            "role" => $user["role"]
        ]);

        $refreshToken = JwtHelper::generateRefreshToken();
        $exp = (int) Config::get("JWT_REFRESH_EXP", 604800);

        $this->refreshService->create([
            "id_user" => $user["id"],
            "token" => $refreshToken,
            "expires_at" => date("Y-m-d H:i:s", time() + $exp)
        ]);

        return [
            "access_token" => $accessToken,
            "refresh_token" => $refreshToken,
            "user" => [
                "id"        => $user["id"],
                "role"      => $user["role"],
                "full_name" => $user["full_name"],
                "email"     => $user["email"],
                "rfc"       => $user["rfc"]
            ]
        ];
    }

    public function refresh(string $refreshToken): array {
        $tokenData = $this->refreshService->findByToken($refreshToken);

        $accessToken = JwtHelper::generateAccessToken([
            "id" =>   $tokenData["id_user"],
            "role" => $tokenData["role"],
        ]);

        return [ "access_token" => $accessToken ];
    }

    public function logout(string $refreshToken): void {
        $this->refreshService->deleteByToken($refreshToken);
    }
}
