<?php
namespace Models;

use Core\BaseModel;

class UserModel extends BaseModel {
    protected $table = "users";
    protected $primaryKey = "id";

    public function findByEmail(string $email): array|false {

        $sql = "SELECT id, role, full_name, email, rfc, created_at
                FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":email", $email);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getAuthData(string $email): array|false {
        $sql = "SELECT id, email, password
                FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":email", $email);
        $stmt->execute();

        return $stmt->fetch();
    }
}
