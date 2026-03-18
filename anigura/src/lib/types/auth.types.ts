
export type UserRole = "admin" | "user";

export interface User {
    id:          number;
    role:        UserRole;
    full_name:   string;
    email:       string;
    rfc:         string|null;
    created_at?: string;
    updated_at?: string;
}

export interface AuthTokens {
    access_token:  string;
    refresh_token: string;
}

export interface AuthResponse {
    access_token:  string;
    refresh_token: string;
    user:          User;
}

export interface LoginRequest {
    email:    string;
    password: string;
}

export interface RegisterRequest {
    full_name: string;
    email:     string;
    password:  string;
    rfc?:      string;
}

export interface RefreshResponse {
    access_token: string;
}

