<?php
namespace Dtos;

use Enums\Role;

readonly class UpdateUserDto {

    public function __construct(
        public ?string $full_name = null,
        public ?string $email = null,
        public ?string $password = null,
        public ?Role $role = null,

        public ?string $rfc = null,
        public ?string $address = null,
        public ?string $zip_code = null,
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            full_name: $data["full_name"] ?? null,
            email: $data["email"] ?? null,
            password: $data["password"] ?? null,
            role: isset($data["role"]) ? Role::from($data["role"]) : null,
            rfc: $data["rfc"] ?? null,
            address: $data["address"] ?? null,
            zip_code: $data["zip_code"] ?? null,
        );
    }

    public function toMap(): array {
        $data = [
            "full_name" => $this->full_name,
            "email" => $this->email,
            "password" => $this->password,
            "role" => $this->role?->value,
            "rfc" => $this->rfc,
            "address" => $this->address,
            "zip_code" => $this->zip_code,
        ];

        return array_filter($data, fn($value) => !is_null($value));
    }
}
