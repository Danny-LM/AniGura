<?php
namespace Models;

use Core\BaseModel;

class CartItemModel extends BaseModel {
    protected $table = "cart_items";
    protected $primaryKey = "id";

    public function getFullCart(int $userId) {
        $sql = "SELECT ci.id as cart_item_id, ci.id_product, ci.quantity,
                       p.name, p.price, p.stock, p.active, p.discount
                FROM {$this->table} ci
                JOIN products p ON ci.id_product = p.id
                WHERE ci.id_user = :id_user";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id_user", $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
