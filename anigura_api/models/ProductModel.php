<?php
namespace Models;

use Core\BaseModel;
use Interfaces\Models\IProductModel;

class ProductModel extends BaseModel implements IProductModel {
    protected $table = "products";
    protected $primaryKey = "id";

    public function all(int $page = 1, int $limit = 20, ?string $type = null): array {
        $offset = ($page - 1) * $limit;

        $where = $type ? "WHERE product_type = :type" : "";

        $totalSql = "SELECT COUNT(*) FROM {$this->table} {$where}";
        $totalStmt = $this->db->prepare($totalSql);
        if ($type) $totalStmt->bindValue(":type", $type);
        $totalStmt->execute();
        $total = (int) $totalStmt->fetchColumn();

        $sql = "SELECT * from {$this->table} {$where}
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        if ($type) $stmt->bindValue(":type", $type);
        $stmt->bindValue(":limit", $limit);
        $stmt->bindValue(":offset", $offset);
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
