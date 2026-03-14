<?php
namespace Models;

use Core\BaseModel;
use Interfaces\Models\ISetboxDetailModel;

class SetboxDetailModel extends BaseModel implements ISetboxDetailModel {
    protected $table = "setbox_details";
    protected $primaryKey = "id_product";
}
