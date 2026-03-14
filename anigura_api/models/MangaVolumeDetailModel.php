<?php
namespace Models;

use Core\BaseModel;
use Interfaces\Models\IMangaVolumeDetailModel;

class MangaVolumeDetailModel extends BaseModel implements IMangaVolumeDetailModel {
    protected $table = "manga_volume_details";
    protected $primaryKey = "id_product";
}
