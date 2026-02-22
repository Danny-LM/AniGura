<?php
namespace Dtos;

readonly class UserDto {
    
    public function __construct(
        public int $id,
        public string $role,
        public string $full_name,
        public string $email,

        public ?string $rfc = null,
        public ?string $address = null,
        public ?string $zip_code = null,
        public ?string $created_at = null,
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            id: (int)$data["id"],
            role: $data["role"],
            full_name: $data["full_name"],
            email: $data["email"],
            
            rfc: $data["rfc"] ?? null,
            address: $data["address"] ?? null,
            zip_code: $data["zip_code"] ?? null,
            created_at: $data["created_at"] ?? null,
        );
    }
}
