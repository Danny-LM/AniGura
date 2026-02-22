<?php
namespace Models;

use Core\BaseModel;

class CategoryModel extends BaseModel {
    protected $table = "categories";
    protected $primaryKey = "id";

    public function findByName(string $name): array|false {
        $sql = "SELECT id, name
                FROM {$this->table} WHERE name = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":name", $name);
        $stmt->execute();

        return $stmt->fetch();
    }
}
