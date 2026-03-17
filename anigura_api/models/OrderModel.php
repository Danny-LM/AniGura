<?php
namespace Models;

use Core\BaseModel;
use Interfaces\Models\IOrderModel;

class OrderModel extends BaseModel implements IOrderModel {
    protected $table = "orders";
    protected $primaryKey = "id";

    public function findByUser(int $userId, int $page = 1, int $limit = 20): array {
        $offset = ($page - 1) * $limit;

        $totalSql = "SELECT COUNT(*) FROM {$this->table} WHERE id_user = :id_user";
        $totalStmt = $this->db->prepare($totalSql);
        $totalStmt->bindValue(":id_user", $userId, \PDO::PARAM_INT);
        $totalStmt->execute();
        $total = (int) $totalStmt->fetchColumn();

        $sql = "SELECT * FROM {$this->table}
                WHERE id_user = :id_user
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id_user", $userId, \PDO::PARAM_INT);
        $stmt->bindValue(":limit", $limit, \PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            "info" => [
                "total"   => $total,
                "pages"   => (int) ceil($total / $limit),
                "current" => $page,
                "next"    => ($page * $limit < $total) ? $page + 1 : null,
                "prev"    => $page > 1 ? $page - 1 : null,
            ],
            "results" => $stmt->fetchAll()
        ];
    }
}
