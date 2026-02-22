<?php
namespace Core;

abstract class BaseController {

    protected function json(int $code, $data=null, string $msg = ""): void {
        Response::json($code, $data, $msg);
    }

    protected function ok($data=null, string $msg = "Request success"): void {
        $this->json(200, $data, $msg);
    }

    protected function error(string $msg, int $code = 400): void {
        $this->json($code, null, $msg);
    }

    protected function getBody(): array {
        $json = file_get_contents("php://input");
        return json_decode($json, true) ?? [];
    }
}