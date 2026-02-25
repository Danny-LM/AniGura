import { API } from "../api";
import { get } from "svelte/store";
import { cartStore } from "../stores/cartStore";
import { authStore } from "../stores/authStore";

function getUserId(): number {
    const user = get(authStore);
    if (!user) throw new Error("Not authenticated");
    return user.id;
}

export const CartService = {
    load: async() => {
        const items = await API.getCart(getUserId());
        cartStore.set(items);
    },

    add: async(productId: number, quantity = 1) => {
        const item = await API.addToCart(getUserId(), productId, quantity);
        cartStore.add(item);
    },

    updateQty: async (cartItemId: number, quantity: number) => {
        await API.updateQty(getUserId(), cartItemId, quantity);
        cartStore.updateQty(cartItemId, quantity);
    },

    remove: async (cartItemId: number) => {
        await API.removeItem(getUserId(), cartItemId);
        cartStore.remove(cartItemId);
    },

    clear: () => cartStore.clear(),
};
