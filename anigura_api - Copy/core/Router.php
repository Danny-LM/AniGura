<?php
namespace Core;

use Core\Response;

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
     * To update resources
     */
    public function put($path, $callback) {
        $this->routes["PUT"][$path] = $callback;
    }

    /**
     * To delete resources
     */
    public function delete($path, $callback) {
        $this->routes["DELETE"][$path] = $callback;
    }

    public function run() {
        $method = $_SERVER["REQUEST_METHOD"];
        $path   = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        if (isset($this->routes[$method][$path])) {
            $callback = $this->routes[$method][$path];
            
            if (is_callable($callback)) {
                return $callback();
            }
        }

        Response::json(404, null, "Route not found: $method $path");
    }
}