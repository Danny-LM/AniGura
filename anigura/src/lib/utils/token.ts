
interface JwtPayload {
    id:   number;
    role: string;
    iat:  number;
    exp:  number;
}

/**
 * To decodify token and get expiration date
 * @param token 
 * @returns 
 */
function decodeToken(token: string): JwtPayload|null {
    try {
        const parts = token.split(".");
        if (parts.length !== 3) return null;

        const payload = parts[1].replace(/-/g, '+').replace(/_/g, '/');

        return JSON.parse(atob(payload)) as JwtPayload;
    
    } catch {
        return null;
    }
}

/**
 * Verify if the token is expired
 * @param token 
 * @param bufferSeconds 
 * @returns 
 */
export function isTokenExpired(token: string, bufferSeconds = 30): boolean {
    const payload = decodeToken(token);
    if (!payload) return true;

    const nowInSeconds = Date.now() / 1000;

    return payload.exp < nowInSeconds + bufferSeconds;
}

/**
 * Get user id and role
 * @param token 
 * @returns 
 */
export function getTokenPayload(token: string): JwtPayload|null {
    return decodeToken(token);
}

/**
 * To get in how many secods the token expires 
 * @param token 
 * @returns -1 if the token is aldeady expired
 */
export function getTokenTtl(token: string): number {
    const payload = decodeToken(token);
    if (!payload) return -1;

    return payload.exp - (Date.now() / 1000);
}

