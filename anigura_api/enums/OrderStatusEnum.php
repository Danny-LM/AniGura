<?php
namespace Enums;

enum OrderStatusEnum: string {
    case PENDING = "pending";
    case PAID = "paid";
    case CANCELLED = "cancelled";
    case SHIPPED = "shipped";
}
