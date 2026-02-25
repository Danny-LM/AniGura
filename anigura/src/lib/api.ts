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
    login: ($data: any) => request("/auth/login", "POST", $data),

    getProduct: (id: number) => request(`/products/${id}`),
    getProducts: () => request("/products"),

    getCart: (userId: number) => request(`/cart/${userId}`),
    addToCart: (userId: number, productId: number, quantity: number = 1) =>
        request(`/cart/${userId}`, "POST", { id_product: productId, quantity }),
    updateQty: (userId: number, itemId: number, quantity: number) =>
        request(`/cart/${userId}/${itemId}`, "PATCH", { quantity }),
    removeItem: (userId: number, itemId: number) =>
        request(`/cart/${userId}/${itemId}`, "DELETE"),
};
