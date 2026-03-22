<script lang="ts">
    import ProductCard from "./ProductCard.svelte";
    import type { Product } from "../../../lib/types";
    import Icon from "../../ui/Icon.svelte";

    interface Props {
        products: Product[];
    }

    let { products }: Props = $props();
</script>

{#if products.length === 0}
    <div class="empty">
        <Icon name="box" size={48} />
        <p class="empty-title">No products found</p>
        <p class="empty-desc">Try a different filter</p>
    </div>
{:else}
    <div class="grid">
        {#each products as product (product.id)}
            <ProductCard {product} />
        {/each}
    </div>    
{/if}

<style>
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: var(--space-4);
    }

    .empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: var(--space-6) 0;
        gap: var(--space-2);
        text-align: center;
    }

    .empty-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-secondary);
    }

    .empty-desc {
        font-size: 13px;
        color: var(--text-muted);
    }
</style>

