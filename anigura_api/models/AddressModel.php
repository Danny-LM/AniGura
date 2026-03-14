<?php
namespace Models;

use Core\BaseModel;

class AddressModel extends BaseModel {
    protected $table = "addresses";
    protected $primaryKey = "id";

    public function resetDefaultAddress(int $userId): bool {
        $sql = "UPDATE {$this->table}
                SET is_default = 0
                WHERE id_user = :id_user";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id_user", $userId, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function defaultAddresses(int $userId): array {
        $sql = "SELECT * FROM {$this->table}
                WHERE id_user = :id_user
                AND is_default = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id_user", $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
