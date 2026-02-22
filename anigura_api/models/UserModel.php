<?php
namespace Models;

use Core\BaseModel;

class UserModel extends BaseModel {
    protected $table = "users";
    protected $primaryKey = "id";

    public function findByEmail(string $email): array|false {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":email", $email);
        $stmt->execute();

        return $stmt->fetch();
    }
}
