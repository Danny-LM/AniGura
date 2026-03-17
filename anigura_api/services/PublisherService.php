<?php
namespace Services;

use Interfaces\Services\IPublisherService;
use Interfaces\Models\IPublisherModel;
use Exception;
use PDOException;

class PublisherService implements IPublisherService {
    private $model;

    public function __construct(IPublisherModel $model) {
        $this->model = $model;
    }

    public function findAll(int $page = 1, int $limit = 20) {
        return $this->model->all($page, $limit);
    }

    public function find(int $id) {
        $item = $this->model->find($id);
        if (!$item) throw new Exception("Publisher not found", 404);

        return $item;
    }

    public function create(array $data) {
        try {
            $id = $this->model->save($data);
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") throw new Exception("Publisher already exists", 409);
            throw $e;
        }

        return $this->model->find($id);
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
