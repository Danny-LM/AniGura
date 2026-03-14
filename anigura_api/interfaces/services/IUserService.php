<?php
namespace Interfaces\Services;

use Core\IBaseService;

interface IUserService extends IBaseService {
    public function getByEmail(string $email);
}
