<?php
namespace Interfaces\Services;

use Core\IBaseService;

interface IAddressService extends IBaseService {
    public function getDefaultByUser(int $userId): array|false;
}
