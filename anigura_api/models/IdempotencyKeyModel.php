<?php
namespace Models;

use Core\BaseModel;
use Interfaces\Models\IIdempotencyKeyModel;

class IdempotencyKeyModel extends BaseModel implements IIdempotencyKeyModel {
    protected $table = "idempotency_keys";
    protected $primaryKey = "id";

    public function findByKeyAndEndpoint(string $keyHash, string $endpoint): array|false {
        $sql = "SELECT * FROM {$this->table}
                WHERE key_hash = :key_hash
                AND endpoint = :endpoint
                AND expires_at > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":key_hash", $keyHash);
        $stmt->bindValue(":endpoint", $endpoint);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function insertProcessing(string $keyHash, int $userId, string $endpoint): bool {
        $sql = "INSERT IGNORE INTO {$this->table} 
                (key_hash, id_user, endpoint, status, status_code, response, expires_at)
                VALUES (:key_hash, :id_user, :endpoint, 'processing', 0, '', 
                DATE_ADD(NOW(), INTERVAL 1 DAY))";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":key_hash", $keyHash);
        $stmt->bindValue(":id_user",  $userId, \PDO::PARAM_INT);
        $stmt->bindValue(":endpoint", $endpoint);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function updateStatus(string $keyHash, string $endpoint, string $status, int $statusCode, $response): bool {
        $sql = "UPDATE {$this->table}
                SET status = :status, status_code = :status_code, response = :response
                WHERE key_hash = :key_hash AND endpoint = :endpoint";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":status",      $status);
        $stmt->bindValue(":status_code", $statusCode, \PDO::PARAM_INT);
        $stmt->bindValue(":response",    json_encode($response));
        $stmt->bindValue(":key_hash",    $keyHash);
        $stmt->bindValue(":endpoint",    $endpoint);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function deleteExpired(): bool {
        $sql = "DELETE FROM {$this->table} WHERE expires_at < NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
