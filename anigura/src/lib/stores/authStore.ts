import { writable, derived } from "svelte/store";
import type { User } from "../types";

const STORAGE_KEY = "anigura_user";

function createAuthStore() {
    const stored = sessionStorage.getItem(STORAGE_KEY);
    const initial: User|null = stored ? JSON.parse(stored) : null;

    const { subscribe, set } = writable<User|null>(initial);

    return {
        subscribe,
        login: (user: User) => {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(user));
            set(user);
        },
        logout: () => {
            sessionStorage.removeItem(STORAGE_KEY);
            set(null);
        }
    };
}

export const authStore = createAuthStore();
export const isLoggedIn = derived(authStore, ($user) => $user !== null);
