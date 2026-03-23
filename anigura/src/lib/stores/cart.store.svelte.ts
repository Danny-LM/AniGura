import type { CartItem } from "../types";
import { getCartItemStatus } from "../types";

const STATUS_ORDER = { ok: 0, insufficient: 1, unavailable: 2 } as const;

class CartStore {
    items   = $state<CartItem[]>([]);
    syncing = $state<Set<number>>(new Set());

    get totalItems(): number {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    }

    get subtotal(): number {
        return this.items.reduce(
            (sum, item) => sum + (parseFloat(item.unit_price) * item.quantity), 0
        );
    }

    get sortedItems(): CartItem[] {
        return [...this.items].sort((a, b) => {
            return STATUS_ORDER[getCartItemStatus(a) as keyof typeof STATUS_ORDER] - STATUS_ORDER[getCartItemStatus(b) as keyof typeof STATUS_ORDER];
        });
    }

    isSyncing(cartItemId: number): boolean {
        return this.syncing.has(cartItemId);
    }

    setSyncing(cartItemId: number, pending: boolean) {
        const next = new Set(this.syncing);
        pending ? next.add(cartItemId) : next.delete(cartItemId);
        this.syncing = next;
    }

    hydrate(apiItems: CartItem[]) {
        this.items = apiItems;
    }

    updateQty(productId: number, newQty: number) {
        const index = this.items.findIndex(item => item.id_product === productId);
        if (index === -1) return;

        if (newQty <= 0) this.removeItem(productId);
        else {
            this.items[index].quantity = newQty;
            this.items[index].subtotal = (
                parseFloat(this.items[index].unit_price) * newQty
            ).toFixed(2);
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
