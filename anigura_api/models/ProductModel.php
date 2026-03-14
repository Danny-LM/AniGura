<?php
namespace Models;

use Core\BaseModel;
use Interfaces\Models\IProductModel;

class ProductModel extends BaseModel implements IProductModel {
    protected $table = "products";
    protected $primaryKey = "id";
}
