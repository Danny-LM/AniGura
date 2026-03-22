<script lang="ts">
    import Badge from "../../ui/Badge.svelte";
    import type { Product, ProductType } from "../../../lib/types";

    interface Props {
        product: Product;
    }

    let { product }: Props = $props();

    const TYPE_LABEL: Record<ProductType, string> = {
        "manga_volume": "Manga",
        "figure":       "Figure",
        "setbox":       "Setbox",
    };

    const finalPrice = $derived(
        parseFloat(product.price) * (1 - parseFloat(product.discount) / 100)
    );

    const hasDiscount = $derived(parseFloat(product.discount) > 0);
</script>

<article class="card">
    <div class="card-image">
        {#if product.cover_image}
            <img src="{product.cover_image}" alt="{product.name}" loading="lazy" />
        {:else}
            <div class="no-image">No image</div>
        {/if}

        <div class="card-badge">
            <Badge variant="info">{TYPE_LABEL[product.product_type]}</Badge>
        </div>

        {#if hasDiscount}
            <div class="discount-tag">-{product.discount}</div>
        {/if}
    </div>

    <div class="card-body">
        <h3 class="card-title">{product.name}</h3>

        <div class="card-price">
            <span class="final-price">${finalPrice.toFixed(2)}</span>
            {#if hasDiscount}
                <span class="price-original">${parseFloat(product.price).toFixed(2)}</span>
            {/if}
        </div>

        {#if product.stock <= 5 && product.stock > 0}
            <p class="stock-warning">Only {product.stock} left!</p>
        {:else if product.stock === 0}
            <p class="stock-out">Out of stock</p>
        {/if}
    </div>
</article>

<style>
    .card {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: all 0.2s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
    }

    .card:hover {
        border-color: var(--border-accent);
        box-shadow: var(--shadow-accent);
        transform: translateY(-2px);
    }

    .card-image {
        position: relative;
        aspect-ratio: 3/4;
        background: var(--bg-elevated);
        overflow: hidden;
    }

    .card-image img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .card:hover .card-image img { transform: scale(1.04); }

    .no-image {
        width: 100%; height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 13px;
    }

    .card-badge {
        position: absolute;
        top: var(--space-2);
        left: var(--space-2);
    }

    .discount-tag {
        position: absolute;
        top: var(--space-2);
        right: var(--space-2);
        background: var(--danger);
        color: white;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: var(--radius-sm);
    }

    .card-body {
        padding: var(--space-3);
        display: flex;
        flex-direction: column;
        gap: var(--space-1);
        flex: 1;
    }

    .card-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: unset;
        -webkit-box-orient: vertical;
        overflow: hidden
    }

    .card-price {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        margin-top: auto;
        padding-top: var(--space-1);
    }

    .final-price {
        font-size: 15px;
        font-weight: 700;
        color: var(--accent-light);
    }

    .price-original {
        font-size: 12px;
        color: var(--text-muted);
        text-decoration: line-through;
    }

    .stock-warning {
        font-size: 11px;
        color: var(--warning);
        font-weight: 600;
    }

    .stock-out {
        font-size: 11px;
        color: var(--danger);
        font-weight: 600;
    }
</style>

