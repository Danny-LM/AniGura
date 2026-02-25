<script lang="ts">
    import type { Product } from "../lib/types";
    import ProductCard from "./ProductCard.svelte";

    interface Props {
        products: Product[];
        activeFilter: string;
        onAddToCart: (product: Product) => void;
    }

    let { products, activeFilter, onAddToCart }: Props = $props();

    let filtered = $derived(
        activeFilter === "all"
            ? products
            : products.filter((p) => p.product_type === activeFilter)
    );
</script>

<div class="grid">
    {#each filtered as product (product.id)}
        <ProductCard {product} {onAddToCart} />
        <!-- <div class="card-debug">
            {product.name}
        </div> -->
    {:else}
        <p class="empty">No products found</p>
    {/each}
</div>

<style>
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: var(--space-3);
    }

    /* .card-debug {
        border: 2px dashed var(--accent); border-radius: var(--radius-md);
        padding: var(--space-4);
        color: var(--accent-light);
        font-size: 13px;
        min-height: 80px;
        display: flex; align-items: center;
    } */

    .empty {
        color: var(--text-muted);
        font-size: 14px;
    }
</style>
