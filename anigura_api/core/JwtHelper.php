<?php
namespace Core;

use Exception;

class JwtHelper {

    private static function getSecret(): string {
        $secret = Config::get("JWT_SECRET");
        if (!$secret) throw new Exception("JWT_SECRET not configured", 500);
        return $secret;
    }

    private static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public static function generateAccessToken(array $payload): string {
        $secret     = self::getSecret();
        $expiration = (int) Config::get("JWT_ACCESS_EXP", 900);

        $header = self::base64UrlEncode(json_encode([
            "alg" => "HS256",
            "typ" => "JWT"
        ]));

        $payload["iat"] = time();
        $payload["exp"] = time() + $expiration;

        $encodedPayload = self::base64UrlEncode(json_encode(($payload)));

        $signature = self::base64UrlEncode(
            hash_hmac("sha256", "$header.$encodedPayload", $secret, true)
        );

        return "$header.$encodedPayload.$signature";
    }

    public static function validate(string $token): array {
        $secret = self::getSecret();
        $parts = explode(".", $token);

        if (count($parts) !== 3) throw new Exception("Invalid token format", 401);

        [$header, $payload, $signature] = $parts;

        $expectedSignature = self::base64UrlEncode(
            hash_hmac("sha256", "$header.$payload", $secret, true)
        );

        if (!hash_equals($expectedSignature, $signature)) {
            throw new Exception("Invalid token signature", 401);
        }

        $data = json_decode(self::base64UrlDecode($payload), true);

        if (!$data || !isset($data["exp"])) {
            throw new Exception("Invalid token payload", 401);
        }

        if (time() > $data["exp"]) {
            throw new Exception("Token expired", 401);
        }

        return $data;
    }

    public static function generateRefreshToken(): string {
        return bin2hex(random_bytes(64));
    }
}
