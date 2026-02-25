import { writable, derived } from "svelte/store";
import type { CartItem } from "../types";

function createCartStore() {
    const { subscribe, set, update } = writable<CartItem[]>([]);

    return {
        subscribe,
        set: (items: CartItem[]) => set(items),
        add: (item: CartItem) => update((items) => {
            const exists = items.find((i) => i.id_product === item.id_product);
            if (exists) {
                return items.map((i) => i.id_product === item.id_product
                    ? { ...i, quantity: i.quantity + item.quantity }
                    : i
                );
            }

            return [...items, item];
        }),

        updateQty: (cartItemId: number, quantity: number) => update((items) => 
            items.map(
                (i) => i.cart_item_id === cartItemId ? { ...i, quantity } : i
            )
        ),

        remove: (cartItemId: number) => update((items) =>
            items.filter((i) => i.cart_item_id !== cartItemId)
        ),

        clear: () => set([]),
    };
}

export const cartStore = createCartStore();
export const cartCount = derived(cartStore, ($cart) =>
    $cart.reduce((sum, item) => sum + item.quantity, 0)
);
export const cartTotal = derived(cartStore, ($items) =>
    $items.reduce((sum, i) => sum + Number(i.subtotal), 0)
);
