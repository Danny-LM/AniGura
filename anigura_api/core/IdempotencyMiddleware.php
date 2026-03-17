<?php
namespace Core;

use Models\IdempotencyKeyModel;

class IdempotencyMiddleware {

    public static function handle(string $endpoint, string $msg = "Request already processed"): ?array {
        $headers = getallheaders();
        $key = $headers["X-Idempotency-Key"] ?? $headers["x-idempotency-key"] ?? null;

        if (!$key) return null;

        $keyHash = hash("sha256", $key);
        $model   = new IdempotencyKeyModel();

        $existing = $model->findByKeyAndEndpoint($keyHash, $endpoint);

        if ($existing) {
            match($existing["status"]) {
                "completed" => Response::json(
                    $existing["status_code"],
                    json_decode($existing["response"], true),
                    $msg
                ),
                "processing" => Response::json(409, null, "Request is being processed, please wait"),
                "failed"     => null,
                default      => null
            };
        }

        return ["key_hash" => $keyHash, "endpoint" => $endpoint];
    }

    public static function begin(array $context, int $userId): void {
        if (!$context) return;

        $model = new IdempotencyKeyModel();
        $model->insertProcessing($context["key_hash"], $userId, $context["endpoint"]);
    }

    public static function complete(array $context, int $statusCode, $data): void {
        if (!$context) return;

        $model = new IdempotencyKeyModel();
        $model->updateStatus($context["key_hash"], $context["endpoint"], "completed", $statusCode, $data);
    }

    public static function fail(array $context): void {
        if (!$context) return;

        $model = new IdempotencyKeyModel();
        $model->updateStatus($context["key_hash"], $context["endpoint"], "failed", 0, null);
    }
}
