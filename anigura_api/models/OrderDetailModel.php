<?php
namespace Models;

use Core\BaseModel;
use Interfaces\Models\IOrderDetailModel;

class OrderDetailModel extends BaseModel implements IOrderDetailModel {
    protected $table = "order_details";
    protected $primaryKey = "id";

    public function findByOrder(int $orderId): array {
        $sql = "SELECT od.*, p.name, p.sku
                FROM {$this->table} od
                JOIN products p ON od.id_product = p.id
                WHERE od.id_order = :id_order";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id_order", $orderId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
