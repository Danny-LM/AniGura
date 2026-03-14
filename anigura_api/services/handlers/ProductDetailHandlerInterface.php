<?php
namespace Services\Handlers;

interface ProductDetailHandlerInterface {
    public function getModel();
    public function getRules(bool $isUpdate = false): array;
}
