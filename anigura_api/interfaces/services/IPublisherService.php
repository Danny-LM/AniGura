<?php
namespace Interfaces\Services;

use Core\IBaseService;

interface IPublisherService extends IBaseService {
    // [BEGIN:index]
    public function findAll();
    // [END:index]

    // [BEGIN:show]
    public function find(int $id);
    // [END:show]

    // [BEGIN:store]
    public function create(array $data);
    // [END:store]

    // [BEGIN:update]
    public function update(int $id, array $data);
    // [END:update]

    // [BEGIN:destroy]
    public function destroy(int $id);
    // [END:destroy]
}
