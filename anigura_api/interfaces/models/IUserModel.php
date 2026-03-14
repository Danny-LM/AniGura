<?php
namespace Core\Interfaces\Models;

use Core\IBaseModel;

interface IUserModel extends IBaseModel {
    public function findByEmail(string $email): array|false;
    public function getAuthData(string $email): array|false;
}
