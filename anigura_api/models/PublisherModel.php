<?php
namespace Models;

use Core\BaseModel;
use Interfaces\Models\IPublisherModel;

class PublisherModel extends BaseModel implements IPublisherModel {
    protected $table = "publishers";
    protected $primaryKey = "id";
}
