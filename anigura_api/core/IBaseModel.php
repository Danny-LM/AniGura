<?php
namespace Core;

interface IBaseModel {
    public function save(array $data): int|false;
    public function find(int $id): array|false;
    public function all(int $page = 1, int $limit = 20): array;
    public function update(int $id, array $data): bool;
    public function delete($id): bool;
    public function exists(int $id): bool;
    public function findInIds(array $ids): array;
    public function where(array $criteria): array;
    public function transaction(callable $callback);
    public function findForUpdate(int $id): array|false;
}
