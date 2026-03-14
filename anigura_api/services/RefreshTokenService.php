<?php
namespace Services;

use Interfaces\Models\{ IRefreshTokenModel, IUserModel};
use Exception;

class RefreshTokenService {
    private $model, $userModel;

    public function __construct(IRefreshTokenModel $model, IUserModel $userModel) {
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

    public function findByToken(string $token): array {
        $data = $this->model->findByToken($token);
        if (!$data) throw new Exception("Invalid refresh token", 401);
        if (strtotime($data["expires_at"]) < time()) {
            $this->model->delete($token);
            throw new Exception("Refresh token expired", 401);
        }

        $user = $this->userModel->find($data["id_user"]);
        if (!$user) throw new Exception("User not found", 401);

        return array_merge($data, ["role" => $user["role"]]);
    }

    public function deleteByToken(string $token): void {
        $deleted = $this->model->deleteByToken($token);
        if (!$deleted) throw new Exception("Invalid refresh token", 401);
    }
}
