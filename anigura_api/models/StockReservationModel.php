<?php
namespace Models;

use Core\BaseModel;
use Interfaces\Models\IStockReservationModel;

class StockReservationModel extends BaseModel implements IStockReservationModel {
    protected $table = "stock_reservations";
    protected $primaryKey = "id";

    public function findActiveByProduct(int $productId): array {
        $sql = "SELECT * FROM {$this->table}
                WHERE id_product = :id_product
                AND expires_at > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id_product", $productId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findByToken(string $token): array|false {
        $sql = "SELECT * FROM {$this->table}
                WHERE reservation_token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":token", $token);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function findActiveByUser(int $userId): array {
        $sql = "SELECT sr.*, p.name, p.price, p.discount,
                       ROUND(p.price * (1 - p.discount / 100), 2) AS unit_price,
                       pi.image_url AS cover_image
                FROM {$this->table} sr
                JOIN products p ON sr.id_product = p.id
                LEFT JOIN product_images pi ON p.id = pi.id_product AND pi.is_cover = 1
                WHERE sr.id_user = :id_user
                AND sr.expires_at > NOW()
                ORDER BY sr.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id_user", $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function deleteExpired(): int {
        $sql = "DELETE FROM {$this->table}
                WHERE expires_at < NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function deleteByToken(string $token): bool {
        $sql = "DELETE FROM {$this->table}
                WHERE reservation_token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":token", $token);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getAvailableStock(int $productId): int {
        $sql = "SELECT p.stock - COALESCE(SUM(sr.quantity), 0) AS available
                FROM products p
                LEFT JOIN {$this->table} sr
                       ON sr.id_product = p.id
                       AND sr.expires_at > NOW()
                WHERE p.id = :id_product
                GROUP BY p.id, p.stock";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id_product", $productId, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchColumn();
        return $result !== false ? (int) $result : 0;
    }

    public function getAvailableStockBulk(array $productIds): array {
        if (empty($productIds)) return [];

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));

        $sql = "SELECT p.id, as id_product,
                       p.stock - COALESCE(SUM(sr.quantity), 0) AS available
                FROM products p
                LEFT JOIN {$this->table} sr
                       ON sr.id_product = p.id
                       AND sr.expires_at > NOW()
                WHERE p.id IN ({$placeholders})
                GROUP BY p.id, p.stock";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($productIds);

        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row["id_product"]] = (int) $row["available"];
        }
        return $result;
    }

    public function findByIdempotencyKey(string $key): array|false {
        $sql = "SELECT * FROM {$this->table}
                WHERE idempotency_key = :key
                AND expires_at > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":key", $key);
        $stmt->execute();
        return $stmt->fetch();
    }
}
