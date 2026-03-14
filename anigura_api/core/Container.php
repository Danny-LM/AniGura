<?php
namespace Core;

use Exception;

class Container {
    private array $blindings = [], $instances = [];

    public function bind(string $id, callable $factory): void {
        $this->blindings[$id] = $factory;
    }

    public function get(string $id) {
        if (!isset($this->instances[$id])) {
            if (!isset($this->blindings[$id])) {
                throw new Exception("Target [$id] is not bound in the container", 500);
            }

            $this->instances[$id] = ($this->blindings[$id]($this));
        }

        return $this->instances[$id];
    }
}
