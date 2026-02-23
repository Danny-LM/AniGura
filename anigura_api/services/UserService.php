<?php
namespace Services;

use Exception;
use Models\UserModel;
use Enums\RoleEnum;

class UserService {
    private $userModel;

    public function __construct(UserModel $userModel) {
        $this->userModel = $userModel;
    }

    public function create(array $data) {
        $this->validateAndConvertRole($data);

        $data["password"] = password_hash($data["password"], PASSWORD_BCRYPT);

        return $this->userModel->save($data);
    }

    public function find(int $id) {
        $user = $this->userModel->find($id);
        if (!$user) throw new Exception("User not found", 404);

        return $user;
    }

    public function findAll() {
        return $this->userModel->all();
    }

    public function update(int $id, array $data) {
        if (!$this->userModel->exists($id)) throw new Exception("User not found", 404);
        $this->validateAndConvertRole($data);

        return $this->userModel->update($id, $data);
    }

    public function delete(int $id) {
        if (!$this->userModel->exists($id)) throw new Exception("User not found", 404);

        return $this->userModel->delete($id);
    }

    public function getByEmail(string $email) {
        $user = $this->userModel->findByEmail($email);
        if (!$user) throw new Exception("User with email $email not found", 404);

        return $user;
    }

    public function verifyPassword(array $data) {
        $user = $this->userModel->getAuthData($data["email"]);
        if (!$user || password_verify($data["password"], $user["password"])) {
            throw new Exception("Invalid credentials", 401);
        }

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
