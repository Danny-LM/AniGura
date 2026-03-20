import { apiClient, ENDPOINTS } from "../api";
import { cartStore } from "../stores/cart.store";
import { uiStore } from "../stores/ui.store";
import type {
    CartItem, CartValidationItem,
    AddToCartRequest, UpdateCartRequest
} from "../types";

export class CartService {
    constructor(private api=apiClient) {}

    /**
     * To load the cart and validate stock at the same time.
     */
    async loadCart(): Promise<void> {
        uiStore.setLoading(true);

        try {
            const [cartRes, validationRes] = await Promise.all([
                this.api.get<CartItem[]>(ENDPOINTS.CART.BASE),
                this.api.get<CartValidationItem[]>(ENDPOINTS.CART.VALIDATE)
            ]);

            const cartItems = cartRes.data || [];
            const validationItems = validationRes.data || [];

            const mergedItems: CartValidationItem[] = cartItems.map(item => {
                const validationInfo = validationItems.find(v => v.id_product === item.id_product);

                return {
                    ...item,
                    status: validationInfo?.status || "ok",
                    available: validationInfo?.available || 0,
                };
            });

            cartStore.hydrate(mergedItems);

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

        } catch (error: any) {
            const msg = error.response?.data?.msg || "Failed to add product";
            uiStore.showToast(msg, "error");
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
            await this.api.patch(`${ENDPOINTS.CART.ITEM(cartItemId)}`, payload);

            if (newQty === 0) await this.loadCart();
        
        } catch (error: any) {
            cartStore.updateQty(productId, oldQty);
            const msg = error.response?.data?.msg || "Not enough stock available";
            uiStore.showToast(msg, "error");
        }
    }

    /**
     * To remove an item from the cart
     * @param productId 
     * @param cartItemId 
     */
    async removeItem(productId:number, cartItemId:number): Promise<void> {
        cartStore.removeItem(productId);

        try {
            await this.api.delete(`${ENDPOINTS.CART.ITEM(cartItemId)}`);
            await this.loadCart();

        } catch (error: any) {
            await this.loadCart();
            uiStore.showToast("Failed to remove item", "error");
        }
    }
}

export const cartService = new CartService();

