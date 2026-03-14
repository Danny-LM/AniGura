<?php
namespace Models;

use Core\BaseModel;
use Interfaces\Models\IMediaEntryModel;

class MediaEntryModel extends BaseModel implements IMediaEntryModel {
    protected $table = "media_entries";
    protected $primaryKey = "id";
}
