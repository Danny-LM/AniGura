<?php
namespace Core;

use Exception;

class AuthMiddleware {
    public static $currentUserId = null;

    public static function handle(): array {
        $headers = getallheaders();
        $authHeader = $headers["Authorization"] ?? $headers["authorization"] ?? null;

        if (!$authHeader) throw new Exception("Authorization header missing", 401);

        if (!str_starts_with($authHeader, "Bearer ")) {
            throw new Exception("Invalid authorization format. Use: Bearer {token}", 401);
        }

        $token = substr($authHeader, 7);

        try {
            $payload = JwtHelper::validate($token);

            if (is_array($payload)) {
                self::$currentUserId = $payload["data"]["id"] ?? $payload["id"] ?? null;
                $role = $payload["role"] ?? null;
            } else {
                self::$currentUserId = $payload->data->id ?? $payload->id ?? null;
                $role = $payload->role ?? null;
            }

            if (!self::$currentUserId) throw new Exception("Token does not contain a valid user ID", 401);

            $payloadArray = is_array($payload) ? $payload : (array) $payload;
            $payloadArray["role"] = $role;

            return $payloadArray;

        } catch (Exception $e) {
            throw new Exception("Unauthorized: " . $e->getMessage(), 401);
        }
    }

    public static function requireRole(string $requiredRole): array {
        $payload = self::handle();

        $role = $payload["role"] ?? null;

        if ($role !== $requiredRole) throw new Exception("Access denied. Required role: $requiredRole", 403);

        return $payload;
    }
}
