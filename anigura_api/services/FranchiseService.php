<?php
namespace Services;

use Interfaces\Services\IFranchiseService;
use Interfaces\Models\IFranchiseModel;
use Exception;

class FranchiseService implements IFranchiseService {
    private $model;

    public function __construct(IFranchiseModel $model) {
        $this->model = $model;
    }

    public function findAll(int $page = 1, int $limit = 20) {
        return $this->model->all($page, $limit);
    }

    public function find(int $id) {
        $item = $this->model->find($id);
        if (!$item) throw new Exception("Franchise not found", 404);

        return $item;
    }

    public function create(array $data) {
        return $this->model->save($data);
    }

    public function update(int $id, array $data) {
        if (!$this->model->exists($id)) throw new Exception("Franchise not found", 404);

        return $this->model->update($id, $data);
    }

    public function delete(int $id) {
        if (!$this->model->exists($id)) throw new Exception("Franchise not found", 404);
        
        return $this->model->delete($id);
    }
}
