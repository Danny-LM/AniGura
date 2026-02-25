import type { Response, Product, User, CartItem } from "./types";

const API_URL = "http://localhost:8000";

async function request<T>(endpoint: string, method = "GET", body?: unknown): Promise<T> {
    const res = await fetch(`${API_URL}${endpoint}`, {
        method,
        headers: { "Content-Type": "application/json" },
        body: body ? JSON.stringify(body) : undefined
    });

    const result: Response<T> = await res.json();
    if (!res.ok) throw new Error(result.msg || "API Server Error");
    if (result.data === null) throw new Error(result.msg || "No Data returned");

    return result.data;
}

export const API = {
    // Auth
    register:   ($full_name: string, $email: string, $password: string) =>
        request<User>("/auth/register", "POST", { full_name: $full_name, email: $email, password: $password }),
    login:      ($email: string, $password: string) =>
        request<User>("/auth/login", "POST", { email: $email, password: $password }),


    // Products
    getProducts: () => request<Product[]>("/products"),
    getProduct:  (id: number) => request<Product>(`/products/${id}`),

    // Cart
    getCart:    (userId: number) => request<CartItem[]>(`/cart/${userId}`),
    addToCart:  (userId: number, productId: number, quantity = 1) => 
        request<CartItem>(`/cart/${userId}`, "POST", { id_product: productId, quantity }),
    updateQty:  (userId: number, itemId: number, quantity: number) =>
        request<CartItem>(`/cart/${userId}/${itemId}`, "PATCH", { quantity }),
    removeItem: (userId: number, itemId: number) =>
        request<void>(`/cart/${userId}/${itemId}`, "DELETE"),
};
