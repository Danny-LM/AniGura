<?php
namespace Interfaces\Models;

use Core\IBaseModel;

interface IAddressModel extends IBaseModel {
    public function resetDefaultAddress(int $userId): bool;
    public function defaultAddresses(int $userId): array;
}
