<?php
namespace Core;

use Exception;

class AuthMiddleware {

    public static function handle(): array {
        $headers = getallheaders();
        $authHeader = $headers["Authorization"] ?? $headers["authorization"] ?? null;

        if (!$authHeader) {
            throw new Exception("Authorization header missing", 401);
        }

        if (!str_starts_with($authHeader, "Bearer ")) {
            throw new Exception("Invalid authorization format. Use: Bearer {token}", 401);
        }

        $token = substr($authHeader, 7);

        return JwtHelper::validate($token);
    }

    public static function requireRole(string $requiredRole): array {
        $payload = self::handle();

        if ($payload["role"] !== $requiredRole) {
            throw new Exception("Access denied. Required role: $requiredRole", 403);
        }

        return $payload;
    }
}
