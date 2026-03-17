<?php
namespace Core;

use Core\Response;
use Exception;

class Router {

    private $routes = [];

    /**
     * To get resources
     */
    public function get($path, $callback) {
        $this->routes["GET"][$path] = $callback;
    }

    /**
     * To create resources
     */
    public function post($path, $callback) {
        $this->routes["POST"][$path] = $callback;
    }

    /**
     * To partial update resources
     */
    public function patch($path, $callback) {
        $this->routes["PATCH"][$path] = $callback;
    }

    /**
     * To delete resources
     */
    public function delete($path, $callback) {
        $this->routes["DELETE"][$path] = $callback;
    }

    /**
     * 
     */
    public function run() {
        $method = $_SERVER["REQUEST_METHOD"];
        $path   = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        try {
            foreach ($this->routes[$method] ?? [] as $routePath => $callback) {
                $pattern = "#^" . preg_replace('/:[a-zA-Z0-9_]+/', '([^/]+)', $routePath) . "$#";
    
                if (preg_match($pattern, $path, $matches)) {
                    array_shift($matches);
    
                    if (is_callable($callback)) {
                        return call_user_func_array($callback, $matches);
                    }
                }
            }

            Response::json(404, null, "Route not found: $method $path");

        } catch (Exception $e) {
            $code = is_numeric($e->getCode()) && $e->getCode() >= 400 ? $e->getCode() : 500;

            if ($code === 500) {
                Logger::error("Unhandled exception", [
                    "message" => $e->getMessage(),
                    "file"    => $e->getFile(),
                    "line"    => $e->getLine(),
                ]);
            }

            $debug = Config::get("APP_ENV") !== "production";
            $errorMessage = $debug ? $e->getMessage() : "Internal Server Error";
            Response::json($code, null, $errorMessage);
        }
    }
}
