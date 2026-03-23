
export interface CartItem {
    cart_item_id: number;
    id_product:   number;
    quantity:     number;
    name:         string;
    price:        string;
    discount:     string;
    unit_price:   string;
    subtotal:     string;
    active:       0 | 1;
    available:    number;
    cover_image:  string|null;
}

export type CartItemStatus = "ok"|"unavailable"|"insufficient";

export interface AddToCartRequest {
    id_product: number;
    quantity?:  number;
}

export interface UpdateCartRequest {
    quantity: number;
}


export function getCartItemStatus(item: CartItem): CartItemStatus {
    if (!item.active || item.available === 0) return "unavailable";
    if (item.quantity > item.available)       return "insufficient";
    return "ok";
}
