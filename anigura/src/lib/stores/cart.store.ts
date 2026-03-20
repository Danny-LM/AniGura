import type { CartItem } from "../types";

class CartStore {
    items = $state<CartItem[]>([]);

    get totalItems(): number {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    }

    get subtotal(): number {
        return this.items.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0);
    }

    hydrate(apiItems: CartItem[]) {
        this.items = apiItems;
    }

    updateQty(productId: number, newQty: number) {
        const index = this.items.findIndex(item => item.id_product === productId);

        if (index !== -1) {
            if (newQty <= 0) this.removeItem(productId);
            else this.items[index].quantity = newQty;
        }
    }

    removeItem(productId: number) {
        this.items = this.items.filter(item => item.id_product !== productId);    
    }

    clear() {
        this.items = [];
    }
}

export const cartStore = new CartStore();
