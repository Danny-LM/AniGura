
export interface CartItem {
    cart_item_id: number;
    id_product:   number;
    quantity:     number;
    name:         string;
    price:        string;
    discount:     string;
    unit_price:   string;
    subtotal:     string;
    stock:        number;
    active:       0 | 1;
    cover_image:  string|null;
}

export type CartItemStatus = "ok"|"unavailable"|"insufficient";

export interface CartValidationItem extends CartItem {
    status:    CartItemStatus;
    available: number;
}

export interface AddToCartRequest {
    id_product: number;
    quantity?:  number;
}

export interface UpdateCartRequest {
    quantity: number;
}

