<?php
namespace Models;

use Core\BaseModel;
use Interfaces\Models\IFranchiseModel;

class FranchiseModel extends BaseModel implements IFranchiseModel {
    protected $table = "franchises";
    protected $primaryKey = "id";
}
