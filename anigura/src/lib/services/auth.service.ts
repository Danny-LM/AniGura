import { apiClient, ENDPOINTS } from "../api";
import { authStore } from "../stores/auth.store.svelte";
import { cartStore } from "../stores/cart.store.svelte";
import { cacheStore } from "../stores/cache.store.svelte";
import type { LoginRequest, RegisterRequest, AuthResponse } from "../types";

export class AuthService {
    constructor(private api=apiClient) {}

    /**
     * To authenticate user and save data to the store
     * @param credentials 
     */
    async login(credentials: LoginRequest): Promise<void> {
        const response = await this.api.post<AuthResponse>(
            ENDPOINTS.AUTH.LOGIN,
            credentials
        );

        if (response.data) {
            const { access_token, refresh_token, user } = response.data;
            authStore.login({ access_token, refresh_token }, user);
        }
    }


    /**
     * To register a new user and automatically login them
     * @param data 
     */
    async register(data: RegisterRequest): Promise<void> {
        const response = await this.api.post<AuthResponse>(
            ENDPOINTS.AUTH.REGISTER,
            data
        );

        if (response.data) {
            const { access_token, refresh_token, user } = response.data;
            authStore.login({ access_token, refresh_token }, user);
        }
    }

    /**
     * Logs out user from the API and clear local state
     */
    async logout(): Promise<void> {
        try {
            const tokens = authStore.tokens;
            if (tokens?.refresh_token) {
                await this.api.post(ENDPOINTS.AUTH.LOGOUT, {
                    refresh_token: tokens.refresh_token,
                });
            }

        } catch (error) {
            console.error("API logout failed, but local session will be cleared", error);
        
        } finally {
            authStore.logout();
            cartStore.clear();
            cacheStore.clear();
        }
    }
}

export const authService = new AuthService();

