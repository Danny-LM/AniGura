<?php
namespace Core;

interface IBaseService {
    // [BEGIN:index]
    public function findAll(int $page = 1, int $limit = 20);
    // [END:index]

    // [BEGIN: show]
    public function find(int $id);
    // [END:show]

    // [BEGIN:store]
    public function create(array $data);
    // [END:store]

    // [BEGIN:update]
    public function update(int $id, array $data);
    // [END:update]

    // [BEGIN:destroy]
    public function delete(int $id);
    // [END:destroy]
}
