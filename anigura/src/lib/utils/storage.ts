import type { AuthTokens, User } from "../types";

export class Storage {
    private static readonly KEYS = {
        ACCESS_TOKEN: "anigura_access",
        REFRESH_TOKEN: "anigura_refresh",
        USER: "anigura_user",
    } as const;

    static getAccessToken(): string|null {
        return localStorage.getItem(this.KEYS.ACCESS_TOKEN);
    }

    static getRefreshToken(): string|null {
        return localStorage.getItem(this.KEYS.REFRESH_TOKEN);
    }

    static getTokens(): AuthTokens|null {
        const access = this.getAccessToken();
        const refresh = this.getRefreshToken();

        if (!access || !refresh) return null;

        return { access_token: access, refresh_token: refresh }
    }

    static setTokens(tokens: AuthTokens): void {
        localStorage.setItem(this.KEYS.ACCESS_TOKEN, tokens.access_token);
        localStorage.setItem(this.KEYS.REFRESH_TOKEN, tokens.refresh_token);
    }

    static getUser(): User|null {
        const raw = localStorage.getItem(this.KEYS.USER);
        if (!raw) return null;

        try {
            return JSON.parse(raw);
        
        } catch {
            return null;
        }
    }

    static setUser(user: User): void {
        localStorage.setItem(this.KEYS.USER, JSON.stringify(user));
    }

    static clearSession(): void {
        localStorage.removeItem(this.KEYS.ACCESS_TOKEN);
        localStorage.removeItem(this.KEYS.REFRESH_TOKEN);
        localStorage.removeItem(this.KEYS.USER);
    }

    static hasSession(): boolean {
        return this.getTokens() !== null && this.getUser() !== null;
    }
}
