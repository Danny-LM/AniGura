<script lang="ts">
    import type { Product } from "../lib/types";

    interface Props {
        product: Product;
        onAddToCart: (product: Product) => void;
    }

    let { product, onAddToCart }: Props = $props();

    const TYPE_LABEL: Record<string, string> = {
        manga_volume: "Manga",
        figure:       "Figure",
        setbox:       "Boxset",
    };

    let price         = $derived(Number(product.price));
    let discount      = $derived(Number(product.discount));
    let finalPrice    = $derived(discount > 0 ? price * (1 - discount / 100) : null);
    let isOutOfStock  = $derived(product.stock === 0 || !product.active);
</script>

<article class="card" class:out-of-stock={isOutOfStock}>

    <!-- Image -->
    <div class="card-image">
        <span class="badge-type">{TYPE_LABEL[product.product_type]}</span>

        {#if discount > 0}
            <span class="badge-discount">-{discount}%</span>
        {/if}

        {#if product.cover_image}
            <img src={product.cover_image} alt={product.name} />
        {:else}
            <div class="placeholder">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4-4 4 4 4-6 4 6M4 4h16v16H4z" />
                </svg>
            </div>
        {/if}
    </div>

    <!-- Body -->
    <div class="card-body">
        <h3 class="card-name">{product.name}</h3>

        <div class="card-price">
            <span class="price-final">
                ${finalPrice ? finalPrice.toFixed(2) : price.toFixed(2)}
            </span>
            {#if finalPrice}
                <span class="price-original">${price.toFixed(2)}</span>
            {/if}
        </div>

        <p class="card-stock">{product.stock} in stock</p>

        <button
            class="btn-cart"
            disabled={isOutOfStock}
            onclick={() => onAddToCart(product)}
        >
            {isOutOfStock ? "Out of stock" : "+ Add to cart"}
        </button>
    </div>

</article>

<style>
    .card {
        display: flex; flex-direction: column;
        background: var(--bg-surface);
        border: 1px solid var(--border); border-radius: var(--radius-md);
        overflow: hidden;
        transition: transform 0.15s, box-shadow 0.15s;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-accent);
        border-color: var(--border-accent);
    }

    .out-of-stock {
        opacity: 0.5;
    }

    /* ── Image ── */
    .card-image {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 3;
        background: var(--bg-elevated);
        overflow: hidden;
    }

    .card-image img {
        width: 100%; height: 100%; object-fit: cover;
    }

    .placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        color: var(--text-muted);
    }

    .badge-type {
        position: absolute; top: var(--space-2); left: var(--space-2); z-index: 1;
        background: var(--accent-light);
        color: white;
        border: 1px solid var(--border-accent); border-radius: var(--radius-sm);
        font-size: 10px; font-weight: 700; letter-spacing: 0.5px;
        padding: 2px var(--space-2);
    }

    .badge-discount {
        position: absolute; top: var(--space-2); right: var(--space-2); z-index: 1;
        background: var(--danger);
        color: white;
        font-size: 10px; font-weight: 700;
        padding: 2px var(--space-2);
        border-radius: var(--radius-sm);
    }

    /* ── Body ── */
    .card-body {
        display: flex; flex-direction: column; flex: 1;
        gap: var(--space-2); padding: var(--space-3);
    }

    .card-name {
        font-size: 13px; font-weight: 600;
        color: var(--text-primary);
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-clamp: inherit;
        overflow: hidden;
        min-height: 2.8em;
    }

    .card-price {
        display: flex; align-items: baseline;
        gap: var(--space-2); margin-top: auto;
    }

    .price-final {
        font-size: 16px; font-weight: 700;
        color: var(--text-primary);
    }

    .price-original {
        font-size: 12px;
        color: var(--text-muted);
        text-decoration: line-through;
    }

    .card-stock {
        font-size: 11px;
        color: var(--text-muted);
    }

    .btn-cart {
        width: 100%;
        padding: var(--space-2) 0;
        background: var(--accent);
        color: white;
        border: none; border-radius: var(--radius-sm);
        font-size: 13px; font-weight: 600;
        transition: background 0.15s;
        margin-top: var(--space-1);
    }

    .btn-cart:hover:not(:disabled) {
        background: var(--accent-hover);
    }

    .btn-cart:disabled {
        background: var(--bg-hover);
        color: var(--text-muted);
        cursor: not-allowed;
    }
</style>
