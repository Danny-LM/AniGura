<?php
namespace Models;

use Core\BaseModel;
use Interfaces\Models\IUserModel;

class UserModel extends BaseModel implements IUserModel {
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
        $sql = "SELECT id, role, full_name, email, password, rfc
                FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":email", $email);
        $stmt->execute();

        return $stmt->fetch();
    }
}
