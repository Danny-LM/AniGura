<?php
namespace Enums;

enum Role: string {
    case ADMIN = "admin";
    CASE CUSTOMER = "customer";
    CASE MANAGER = "manager";

    public static function fromString(string $value): self {
        return self::from($value);
    }
}