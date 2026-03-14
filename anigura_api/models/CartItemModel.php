<?php
namespace Models;

use Core\BaseModel;
use Core\Interfaces\Models\ICartItemModel;

class CartItemModel extends BaseModel implements ICartItemModel {
    protected $table = "cart_items";
    protected $primaryKey = "id";

    public function getFullCart(int $userId): array {
        $sql = "SELECT ci.id as cart_item_id, ci.id_product, ci.quantity,
                       p.name, p.price, p.stock, p.active, p.discount,
                       ROUND(p.price * (1 - p.discount / 100), 2) as unit_price,
                       ROUND(p.price * (1 - p.discount / 100) * ci.quantity, 2) as subtotal,
                       pi.image_url as cover_image
                FROM {$this->table} ci
                JOIN products p ON ci.id_product = p.id
                LEFT JOIN product_images pi ON p.id = pi.id_product
                AND pi.is_cover = 1
                WHERE ci.id_user = :id_user";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id_user", $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
