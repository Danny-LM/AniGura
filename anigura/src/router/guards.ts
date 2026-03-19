import { Storage } from "../lib/utils";
import { isTokenExpired } from "../lib/utils";

export type GuardType = "admin"|"auth"|"guest";

export interface GuardResult {
    allow: boolean;
    redirectTo?: string;
}

/**
 * 
 * @param guard 
 * @returns 
 */
export function useGuard(guard: GuardType): GuardResult {
    switch(guard) {
        case "admin": return adminGuard();
        case "auth":  return authGuard();
        case "guest": return guestGuard();
        default:      return { allow: false, redirectTo: "/" };
    }
}

/**
 * 
 * @returns 
 */
export function authGuard(): GuardResult {
    const tokens = Storage.getTokens();

    if (!tokens) return { allow: false, redirectTo: "/" };

    return { allow: true };
}

/**
 * 
 * @returns 
 */
export function adminGuard(): GuardResult {
    const authResult = authGuard();
    if (!authResult.allow) return authResult;

    const user = Storage.getUser();
    if (user?.role !== "admin") return { allow: false, redirectTo: "/" };

    return { allow: true };
}

export function guestGuard(): GuardResult {
    const tokens = Storage.getTokens();
    if (tokens && !isTokenExpired(tokens.access_token)) {
        return { allow: false, redirectTo: "/" };
    }

    return { allow: true };
}

