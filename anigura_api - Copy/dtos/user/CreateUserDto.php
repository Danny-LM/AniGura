<?php
namespace Dtos;

use Enums\Role;

readonly class CreateUserDto {

    public function __construct(
        public string $full_name,
        public string $email,
        public string $password,
        public Role $role = Role::CUSTOMER,

        public ?string $rfc = null,
        public ?string $address = null,
        public ?string $zip_code = null,
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            full_name: $data["full_name"] ?? throw new \Exception("fullName is required"),
            email: $data["email"] ?? throw new \Exception("email is required"),
            password: $data["password"] ?? throw new \Exception("pass is required"),
            role: isset($data["role"]) ? Role::from($data["role"]) : Role::CUSTOMER,
            
            rfc: $data["rfc"] ?? null,
            address: $data["address"] ?? null,
            zip_code: $data["zip_code"] ?? null,
        );
    }
}
