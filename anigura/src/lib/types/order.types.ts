
export type OrderStatus = "pending"|"paid"|"shipped"|"cancelled";

export interface Order {
    id:            number;
    id_user:       number;
    shipping_addr: string;
    total_amount:  string;
    status:        OrderStatus;
    created_at:    string;
    updated_at:    string;
}

export interface OrderDetail {
    id:         number;
    id_order:   number;
    id_product: number;
    quantity:   number;
    unit_price: string;
    name:       string;
    sku:        string|null;
}

export interface OrderWithDetails extends Order {
    details: OrderDetail[];
}

export interface CreateOrderRequest {
    id_address: number;
}

export interface CreateOrderResponse {
    id: number;
}

