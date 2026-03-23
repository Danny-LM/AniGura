import { apiClient, ENDPOINTS } from "../api";
import { uiStore } from "../stores/ui.store.svelte";
import { cartStore } from "../stores/cart.store.svelte";
import type {
    CartItem, AddToCartRequest, UpdateCartRequest
} from "../types";
import { getErrorMsg } from "../utils";

export class CartService {
    constructor(private api=apiClient) {}

    /**
     * To load the cart and validate stock at the same time.
     */
    async loadCart(): Promise<void> {
        uiStore.setLoading(true);

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
     * @param productId 
     * @param cartItemId 
     * @param newQty 
     * @param oldQty 
     */
    async updateQuantity(productId:number, cartItemId:number, newQty:number, oldQty:number): Promise<void> {
        cartStore.updateQty(productId, newQty);

        try {
            const payload: UpdateCartRequest = { quantity: newQty };
            await this.api.patch(ENDPOINTS.CART.ITEM(cartItemId), payload);

            if (newQty === 0) await this.loadCart();
        
        } catch (error) {
            cartStore.updateQty(productId, oldQty);
            uiStore.showToast(getErrorMsg(error, "Not enough stock available"), "error");
        }
    }

    /**
     * To remove an item from the cart
     * @param productId 
     * @param cartItemId 
     */
    async removeItem(productId: number, cartItemId: number): Promise<void> {
        cartStore.removeItem(productId);

        try {
            await this.api.delete(ENDPOINTS.CART.ITEM(cartItemId));
            await this.loadCart();

        } catch (error) {
            await this.loadCart();
            uiStore.showToast(getErrorMsg(error,"Failed to remove item"), "error");
        }
    }
}

export const cartService = new CartService();

