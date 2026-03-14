<?php
namespace Models;

use Core\BaseModel;
use Interfaces\Models\IFigureDetailModel;

class FigureDetailModel extends BaseModel implements IFigureDetailModel {
    protected $table = "figure_details";
    protected $primaryKey = "id_product";
}
