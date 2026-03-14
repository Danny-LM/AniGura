<?php
namespace Services;

use Exception;
use Models\{RefreshTokenModel, UserModel };

class RefreshTokenService {
    private $model, $userModel;

    public function __construct(RefreshTokenModel $model, UserModel $userModel) {
        $this->model = $model;
        $this->userModel = $userModel;
    }

    public function findAll() {
        return $this->model->all();
    }

    public function find(int $id) {
        $item = $this->model->find($id);
        if (!$item) throw new Exception("RefreshToken not found", 404);

        return $item;
    }

    public function create(array $data) {
        if (!$this->userModel->exists($data["id_user"])) throw new Exception("User not found", 404);

        return $this->model->save($data);
    }

    public function update(int $id, array $data) {
        if (!$this->model->exists($id)) throw new Exception("RefreshToken not found", 404);

        if(isset($data["id_user"]) && !$this->userModel->exists($data["id_user"])) {
            throw new Exception("User not found", 404);
        }

        return $this->model->update($id, $data);
    }

    public function delete(int $id) {
        if (!$this->model->exists($id)) throw new Exception("RefreshToken not found", 404);

        return $this->model->delete($id);
    }
}
