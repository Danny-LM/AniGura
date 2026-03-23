import { apiClient, ENDPOINTS } from "../api";
import { uiStore } from "../stores/ui.store.svelte";
import { cartStore } from "../stores/cart.store.svelte";
import type {
    CartItem, AddToCartRequest, UpdateCartRequest
} from "../types";
import { getErrorMsg } from "../utils";

export class CartService {
    constructor(private api=apiClient) {}

    private timers    = new Map<number, ReturnType<typeof setTimeout>>();
    private committed = new Map<number, { productId: number, qty: number }>();

    private pollInterval: ReturnType<typeof setInterval>|null = null;

    /**
     * To load the cart and validate stock at the same time.
     */
    async loadCart(): Promise<void> {
        try {
            const ressponse = await this.api.get<CartItem[]>(ENDPOINTS.CART.BASE);
            cartStore.hydrate(ressponse.data ?? []);

        } catch (error) {
            uiStore.showToast("Failed to load your cart", "error");
        } finally {
            uiStore.setLoading(false);
        }
    }

    /**
     * To silently sync the cart without showing any
     * loading state or error message.
     * @returns 
     */
    async silentSync(): Promise<void> {
        try {
            const response = await this.api.get<CartItem[]>(ENDPOINTS.CART.BASE);
            if (!response.data) return;

            if (cartStore.syncing.size > 0) return;

            cartStore.hydrate(response.data);

        } catch {
            // do nothing
        }
    }

    /**
     * To start polling the cart every X seconds to keep it up-to-date
     * with stock changes.
     * @param intervalMs 
     */
    startPolling(intervalMs = 30_000): void {
        this.stopPolling();
        this.pollInterval = setInterval(() => {
            if (document.visibilityState === "visible") {
                this.silentSync();
            }
        }, intervalMs);
    }

    /**
     * To stop polling the cart
     */
    stopPolling(): void {
        if (this.pollInterval !== null) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }
    }

    /**
     * To add a new product to the cart
     * @param request 
     */
    async addItem(request: AddToCartRequest): Promise<void> {
        try {
            await this.api.post(ENDPOINTS.CART.BASE, request);
            uiStore.showToast("Item added to cart", "success");

            await this.loadCart();

        } catch (error) {
            uiStore.showToast(getErrorMsg(error, "Failed to add product"), "error");
        }
    }

    /**
     * To update the quantity of an item
     * - 1. Update the quantity in the UI immediately and set
     *      syncing state to true.
     * - 2. If there's no pending update for this cart item,
     *      save the current committed quantity for potential rollback.
     * - 3. Clear any existing debounce timer for this cart
     *      item and set a new one.
     * - 4. Set a debounce timer to flush the update to the server.
     * @param productId 
     * @param cartItemId 
     * @param newQty 
     * @param oldQty 
     */
    updateQuantity(productId:number, cartItemId:number, newQty:number, oldQty:number): void {
        cartStore.updateQty(productId, newQty);
        cartStore.setSyncing(cartItemId, true);

        if (!this.timers.has(cartItemId)) {
            this.committed.set(cartItemId, { productId, qty: oldQty });
        }

        clearTimeout(this.timers.get(cartItemId));
        this.timers.set(
            cartItemId,
            setTimeout(() => this._flush(cartItemId, productId, newQty), 500)
        );
    }

    /**
     * To flush the quantity update to the server after the debounce time.
     * @param cartItemId 
     * @param productId 
     * @param qty 
     */
    private async _flush(cartItemId: number, productId: number, qty: number): Promise<void> {
        const committed = this.committed.get(cartItemId);
        
        try {
            await this.api.patch<null>(
                ENDPOINTS.CART.ITEM(cartItemId),
                { quantity: qty } as UpdateCartRequest
            );
            this.committed.delete(cartItemId);

            if (qty === 0) await this.silentSync();

        } catch (error) {
            if (committed) cartStore.updateQty(committed.productId, committed.qty);
            uiStore.showToast(
                getErrorMsg(error, "Not enough stock available"),
                "error"
            );
            this.committed.delete(cartItemId);

        } finally {
            cartStore.setSyncing(cartItemId, false);
            this.timers.delete(cartItemId);
        }
    }

    /**
     * To remove an item from the cart
     * @param productId 
     * @param cartItemId 
     */
    async removeItem(productId: number, cartItemId: number): Promise<void> {
        clearTimeout(this.timers.get(cartItemId));
        this.timers.delete(cartItemId);
        this.committed.delete(cartItemId);
        cartStore.setSyncing(cartItemId, false);

        cartStore.removeItem(productId);

        try {
            await this.api.delete(ENDPOINTS.CART.ITEM(cartItemId));

        } catch (error) {
            await this.loadCart();
            uiStore.showToast(getErrorMsg(error,"Failed to remove item"), "error");
        }
    }
}

export const cartService = new CartService();

