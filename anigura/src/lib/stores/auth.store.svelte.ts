import { Storage } from "../utils";
import type { User, AuthTokens } from "../types";

class AuthStore {
    currentUser = $state<User|null>(Storage.getUser());
    tokens = $state<AuthTokens|null>(Storage.getTokens());

    get isLoggedIn(): boolean {
        return this.currentUser !== null && this.tokens !== null;
    }

    get isAdmin(): boolean {
        return this.currentUser?.role === "admin";
    }

    login(tokens: AuthTokens, user: User) {
        Storage.setTokens(tokens);
        Storage.setUser(user);

        this.tokens = tokens;
        this.currentUser = user;
    }

    logout() {
        Storage.clearSession();

        this.tokens = null;
        this.currentUser = null;
    }

    updateTokens(newTokens: AuthTokens) {
        Storage.setTokens(newTokens);
        this.tokens = newTokens;    
    }
}

export const authStore = new AuthStore();
