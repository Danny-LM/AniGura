<?php
namespace Services;

use Exception;
use Models\UserModel;
use Enums\RoleEnum;

class UserService {
    private $userModel;
    private const ALLOWED_FIELDS = ["full_name", "email", "password", "role", "rfc", "address", "zip_code"];

    public function __construct() {
        $this->userModel = new UserModel();    
    }

    public function create(array $data) {
        if (empty($data["full_name"])) throw new \Exception("Name is required");
        if (empty($data["email"])) throw new \Exception("Email is required");
        if (empty($data["password"])) throw new \Exception("Password is required");
        $this->validateAndConvertRole($data);

        $data["password"] = password_hash($data["password"], PASSWORD_BCRYPT);

        $data["rfc"] = $data["rfc"] ?? null;
        $data["address"] = $data["address"] ?? null;
        $data["zip_code"] = $data["zip_code"] ?? null;

        return $this->userModel->save($data);
    }

    public function find(int $id) {
        if (!$id) throw new Exception("ID is required");

        return $this->userModel->find($id);
    }

    public function findAll() {
        return $this->userModel->all();
    }

    public function update(int $id, array $data) {
        if (empty($id)) throw new Exception("ID is required");
        if (isset($data["password"])) throw new Exception("Password cannot be updated in this request");
        $this->validateAndConvertRole($data);

        $filteredData = array_intersect_key($data, array_flip(self::ALLOWED_FIELDS));
        $cleanData = array_filter($filteredData, fn($value) => $value !== null);
        if (empty($cleanData)) return true;

        return $this->userModel->update($id, $cleanData);
    }

    public function delete(int $id) {
        if (!$id) throw new Exception("ID is required");

        return $this->userModel->delete($id);
    }

    public function getByEmail(string $email) {
        if (empty($email)) throw new Exception("Email is required");
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception("Invalid email format");

        $user = $this->userModel->findByEmail($email);
        if (!$user) throw new Exception("Email not found", 404);

        return $user;
    }

    public function verifyPassword(array $data) {
        if (empty($data["email"])) throw new Exception("Email is required");
        if (empty($data["password"])) throw new Exception("Password is required");

        $user = $this->userModel->getAuthData($data["email"]);
        if (!$user) return false;

        return password_verify($data["password"], $user["password"]);
    }

    private function validateAndConvertRole(array &$data): void {
        if (!empty($data["role"])) {
            $enumValue = RoleEnum::tryFrom($data["role"]);
            if (!$enumValue) {
                throw new \Exception("Invalid role. Allowed: admin, user");
            }
            $data["role"] = $enumValue;
        }
    }
}
