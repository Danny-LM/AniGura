import type { Response, Product } from "./types";

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
    getProducts: () => request<Product[]>("/products"),
    getProduct:  (id: number) => request<Product>(`/products/${id}`),

    // Cart
    getCart:    (userId: number) => request(`/cart/${userId}`),
    addToCart:  (userId: number, productId: number, quantity = 1) => 
        request(`/cart/${userId}`, "POST", { id_product: productId, quantity }),
    updateQty:  (userId: number, itemId: number, quantity: number) =>
        request(`/cart/${userId}/${itemId}`, "PATCH", { quantity }),
    removeItem: (userId: number, itemId: number) =>
        request(`/cart/${userId}/${itemId}`, "DELETE"),
};
