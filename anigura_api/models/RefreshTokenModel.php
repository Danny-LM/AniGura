<?php
namespace Models;

use Core\BaseModel;
use Core\Interfaces\Models\IRefreshTokenModel;

class RefreshTokenModel extends BaseModel implements IRefreshTokenModel {
    protected $table = "refresh_tokens";
    protected $primaryKey = "id";

    public function findByToken(string $token): array|false {
        $sql = "SELECT * FROM {$this->table}
                WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":token", $token);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function deleteByToken(string $token): bool {
        $sql  = "DELETE FROM {$this->table}
                 WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":token", $token);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function deleteByUser(int $userId): bool {
        $sql  = "DELETE FROM {$this->table}
                 WHERE id_user = :id_user";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id_user", $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function deleteExpired(): bool {
        $sql  = "DELETE FROM {$this->table}
                 WHERE expires_at < NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
