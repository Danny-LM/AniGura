import { apiClient, ENDPOINTS } from "../api";
import { cacheStore } from "../stores/cache.store";
import type { Paginated, Product } from "../types";

export class ProductService {
    constructor(private api=apiClient) {}

    /**
     * To get paginated list of products using Cache-First stragegy
     * @param page 
     * @param limit 
     * @returns 
     */
    async getAll(page:number=1, limit:number=20): Promise<Paginated<Product>> {
        const cacheKey = `products?page=${page}&limit=${limit}`;

        const cached = cacheStore.get<Paginated<Product>>(cacheKey);
        if (cached) return cached;

        const url = `${ENDPOINTS.PRODUCTS}?page=${page}&limit=${limit}`;
        const response = await this.api.get<Paginated<Product>>(url);

        if (!response.data) throw new Error("Failed to fetch products");

        cacheStore.set(cacheKey, response.data);
        return response.data;
    }

    /**
     * To get a single product
     * @param id 
     * @returns 
     */
    async getById(id:number): Promise<Product> {
        const url = `${ENDPOINTS.PRODUCTS}/${id}`;
        const response = await this.api.get<Product>(url);

        if (!response.data) throw new Error("Product not found");

        return response.data;
    }
}

export const productService = new ProductService();

