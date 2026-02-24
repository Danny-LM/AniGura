const API_URL = "http://localhost:8000";

export async function request(endpoint: string, method = "GET", body?: any) {
    const res = await fetch(`${API_URL}${endpoint}`, {
        method,
        headers: { "Content-Type": "application/json" },
        body: body ? JSON.stringify(body) : undefined
    });
    const result = await res.json();
    if (!res.ok) throw new Error(result.msg || "API Server Error");
    return result.data;
}

export const API = {
    getProducts: () => request("/products"),
    getCart: (userId: number) => request(`/cart/${userId}`),
    addToCart: (userId: number, productId: number) => 
        request(`/cart/${userId}`, "POST", { id_product: productId, quantity: 1 }),
    updateQty: (userId: number, itemId: number, quantity: number) => 
        request(`/cart/${userId}/${itemId}`, "PATCH", { quantity }),
    removeItem: (userId: number, itemId: number) => 
        request(`/cart/${userId}/${itemId}`, "DELETE")
};
