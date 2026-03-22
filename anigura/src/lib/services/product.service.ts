import { apiClient, ENDPOINTS } from "../api";
import { cacheStore } from "../stores/cache.store.svelte";
import type { Paginated, Product, ProductType } from "../types";

export class ProductService {
    constructor(private api=apiClient) {}

    /**
     * To get paginated list of products using Cache-First stragegy
     * @param page 
     * @param limit 
     * @returns 
     */
    async getAll(page: number = 1, limit: number = 20, type?: ProductType): Promise<Paginated<Product>> {
        const cacheKey = `products:page=${page}:limit=${limit}:?type=${type ?? "all"}`;

        const cached = cacheStore.get<Paginated<Product>>(cacheKey);
        if (cached) return cached;

        const params: Record<string, unknown> = { page, limit };
        if (type) params.product_type = type;

        const response = await this.api.get<Paginated<Product>>(
            ENDPOINTS.PRODUCTS.BASE,
            { params }
        );

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
        const response = await this.api.get<Product>(ENDPOINTS.PRODUCTS.BY_ID(id));
        if (!response.data) throw new Error("Product not found");

        return response.data;
    }
}

export const productService = new ProductService();

