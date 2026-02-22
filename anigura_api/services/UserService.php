<?php
namespace Services;

use Exception;
use Models\UserModel;

class UserService {
    private $userModel;
    private const ALLOWED_FIELDS = ["full_name", "email", "password", "role", "rfc", "address", "zip_code"];    

    public function __construct() {
        $this->userModel = new UserModel();    
    }

    public function create(array $data) {
        if (!$data["full_name"]) throw new Exception("Name is required");
        if (!$data["email"]) throw new Exception("Email is required");
        if (!$data["password"]) throw new Exception("Password is required");

        $filteredData = array_intersect($data, array_flip(self::ALLOWED_FIELDS));

        $filteredData["password"] = password_hash($filteredData["password"], PASSWORD_BCRYPT);

        $filteredData["rfc"] = $filteredData["rfc"] ? $filteredData["rfc"] : null;
        $filteredData["address"] = $filteredData["address"] ? $filteredData["address"] : null;
        $filteredData["zip_code"] = $filteredData["zip_code"] ? $filteredData["zip_code"] : null;

        return $this->userModel->save($filteredData);
    }

    public function find(int $id) {
        if (!$id) throw new Exception("ID is required");

        return $this->userModel->find($id);
    }

    public function findAll() {
        return $this->userModel->all();
    }

    public function update(array $data) {
        if (empty($data["id"])) throw new Exception("ID is required");
        $id = $data["id"];

        $filteredData = array_intersect_key($data, array_flip(self::ALLOWED_FIELDS));
        $cleanData = array_filter($filteredData, fn($value) => $value !== null);
        if (empty($cleanData)) return true;

        $cleanData = array_filter($data, fn($value) => $value !== null);
        if (empty($cleanData)) return true;

        if (isset($cleanData["password"])) {
            $cleanData["password"] = password_hash($cleanData["password"], PASSWORD_BCRYPT);
        }

        return $this->userModel->update($id, $cleanData);
    }

    public function delete(int $id) {
        if (!$id) throw new Exception("ID is required");

        return $this->userModel->delete($id);
    }

    public function getByEmail(string $email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception("Invalid email format");

        $user = $this->userModel->findByEmail($email);
        if (!$user) throw new Exception("User not found", 404);

        return $user;
    }
}
