import { API } from "../api";
import { get } from "svelte/store";
import { cartStore } from "../stores/cartStore";
import { authStore } from "../stores/authStore";

function getUserId(): number {
    const user = get(authStore);
    if (!user) throw new Error("Not authenticated");
    return user.id;
}

const updateTimers = new Map<number, ReturnType<typeof setTimeout>>();
const updating = new Set<number>();

export const CartService = {
    load: async () => {
        const items = await API.getCart(getUserId());
        cartStore.set(items);
    },

    add: async (productId: number, quantity = 1) => {
        await API.addToCart(getUserId(), productId, quantity);
        await CartService.load();
    },

    updateQty: async (cartItemId: number, quantity: number) => {
        const prev = get(cartStore);
        cartStore.updateQty(cartItemId, quantity);

        if (updateTimers.has(cartItemId)) {
            clearTimeout(updateTimers.get(cartItemId));
        }

        updateTimers.set(cartItemId, setTimeout(async () => {
            if (updating.has(cartItemId)) return;

            updating.add(cartItemId);
            try {
                await API.updateQty(getUserId(), cartItemId, quantity);
                await CartService.load();
            } catch (e) {
                cartStore.set(prev);
                throw e;
            } finally {
                updating.delete(cartItemId);
                updateTimers.delete(cartItemId);
            }
        }, 400));
    },

    remove: async (cartItemId: number) => {
        if (updateTimers.has(cartItemId)) {
            clearTimeout(updateTimers.get(cartItemId));
            updateTimers.delete(cartItemId);
        }

        const prev = get(cartStore);
        cartStore.remove(cartItemId);

        try {
            await API.removeItem(getUserId(), cartItemId);
        } catch (e) {
            cartStore.set(prev);
            throw e;
        }
    },

    clear: () => {
        updateTimers.forEach((t) => clearTimeout(t));
        updateTimers.clear();
        updating.clear();
        cartStore.clear();
    },
};
