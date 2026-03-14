<?php
namespace Services;

use Interfaces\Models\IUserModel;
use Enums\RoleEnum;
use Exception;

class UserService {
    private $model;

    public function __construct(IUserModel $model) {
        $this->model = $model;
    }

    public function create(array $data) {
        $this->validateAndConvertRole($data);

        $data["password"] = password_hash($data["password"], PASSWORD_BCRYPT);

        $id = $this->model->save($data);
        $user = $this->model->find($id);

        return [
            "id"        => $user["id"],
            "role"      => $user["role"],
            "full_name" => $user["full_name"],
            "email"     => $user["email"]
        ];
    }

    public function find(int $id) {
        $user = $this->model->find($id);
        if (!$user) throw new Exception("User not found", 404);

        unset($user["password"]);

        return $user;
    }

    public function findAll() {
        $users = $this->model->all();

        return array_map(function($user) {
            unset($user["password"]);

            return $user;
        }, $users);
    }

    public function update(int $id, array $data) {
        if (!$this->model->exists($id)) throw new Exception("User not found", 404);
        $this->validateAndConvertRole($data);

        return $this->model->update($id, $data);
    }

    public function delete(int $id) {
        if (!$this->model->exists($id)) throw new Exception("User not found", 404);

        return $this->model->delete($id);
    }

    public function getByEmail(string $email) {
        $user = $this->model->findByEmail($email);
        if (!$user) throw new Exception("User with email $email not found", 404);

        return $user;
    }

    private function validateAndConvertRole(array &$data): void {
        if (!empty($data["role"])) {
            $enumValue = RoleEnum::tryFrom($data["role"]);
            if (!$enumValue) throw new Exception("Invalid role. Allowed: admin, user", 400);

            $data["role"] = $enumValue;
        }
    }
}
