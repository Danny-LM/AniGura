<?php
namespace Core;

use Exception;

abstract class BaseController {

    protected function json(int $code, $data=null, string $msg = ""): void {
        Response::json($code, $data, $msg);
    }

    protected function ok($data=null, string $msg = "Request success"): void {
        $this->json(200, $data, $msg);
    }

    protected function getBody(): array {
        $json = file_get_contents("php://input");
        if (empty($json)) return [];

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON format: " . json_last_error_msg());
        }

        return $data ?? [];
    }

    protected function validate(array $data, array $rules): array {
        return Validator::validate($data, $rules);
    }
}
