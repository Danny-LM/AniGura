<?php
namespace Core;

abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = "id";

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function save(array $data): int|false {
        if (empty($data[$data])) return false;

        $cols = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO {$this->table} ($cols)
                VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $val = ($value instanceof \UnitEnum) ? $value->value : $value;
            $stmt->bindValue(":{$key}", $val);
        }

        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }
    
    public function find(int $id): array|false {
        if (empty($id)) return false;

        $sql = "SELECT * FROM {$this->table}
                WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function all(): array {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function update(int $id, array $data): bool {
        if (empty($data)) return false;

        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "{$key} = :{$key}, ";
        }
        $fields = rtrim($fields, ", ");

        $sql = "UPDATE {$this->table} SET {$fields} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $val = ($value instanceof \UnitEnum) ? $value->value : $value;
            $stmt->bindValue(":{$key}", $val);
        }
        $stmt->bindValue(":id", $id);

        return $stmt->execute() ? true : false;
    }

    public function delete($id): bool {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table}
            WHERE {$this->primaryKey} = :id
        ");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}
