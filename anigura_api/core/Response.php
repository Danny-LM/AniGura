<?php
namespace Core;

class Response {

    public static function json(int $code, $data=null, string $msg = ""): void {
        http_response_code($code);
        header("Content-Type: application/json; charset=utf-8");

        echo json_encode([
            "status" => $code < 400 ? "success" : "error",
            "code" => $code,
            "msg" => $msg,
            "data" => $data,
            "timestamp" => date("Y-m-d H:i:s")
        ], true);

        exit;
    }
}