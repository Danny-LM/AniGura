<?php
namespace Services;

use Interfaces\Models\IPublisherModel;
use Exception;

class PublisherService {
    private $model;

    public function __construct(IPublisherModel $model) {
        $this->model = $model;
    }

    public function findAll() {
        return $this->model->all();
    }

    public function find(int $id) {
        $item = $this->model->find($id);
        if (!$item) throw new Exception("Publisher not found", 404);

        return $item;
    }

    public function create(array $data) {
        return $this->model->save($data);
    }

    public function update(int $id, array $data) {
        if (!$this->model->exists($id)) throw new Exception("Publisher not found", 404);

        return $this->model->update($id, $data);
    }

    public function delete(int $id) {
        if (!$this->model->exists($id)) throw new Exception("Publisher not found", 404);

        return $this->model->delete($id);
    }
}
