<script lang="ts">
    import type { CartItem } from "../lib/types";

    interface Props {
        item: CartItem;
        onUpdateQty: (cartItemId: number, quantity: number) => void;
        onRemove: (cartItemId: number) => void;
    }

    let { item, onUpdateQty, onRemove }: Props = $props();
</script>

<li class="cart-item" class:inactive={!item.active || item.stock === 0}>
    <div class="item-image">
        {#if item.cover_image}
            <img src={item.cover_image} alt={item.name} />
        {:else}
            <div class="item-placeholder">?</div>
        {/if}
    </div>

    <div class="item-info">
        <p class="item-name">{item.name}</p>
        {#if !item.active || item.stock === 0}
            <span class="item-warning">Unavailable</span>
        {/if}
        <p class="item-price">${Number(item.unit_price).toFixed(2)}</p>
    </div>

    <div class="item-qty">
        <button
            onclick={() => onUpdateQty(item.cart_item_id, item.quantity - 1)}
            disabled={item.quantity <= 1}
        >−</button>
        <span>{item.quantity}</span>
        <button
            onclick={() => onUpdateQty(item.cart_item_id, item.quantity + 1)}
            disabled={item.quantity >= item.stock}
        >+</button>
    </div>

    <p class="item-subtotal">${Number(item.subtotal).toFixed(2)}</p>

    <button class="btn-remove" onclick={() => onRemove(item.cart_item_id)}>✕</button>
</li>

<style>
    .cart-item {
        background: var(--bg-surface);
        display: grid; align-items: center;
        grid-template-columns: 60px 1fr auto auto auto;
        gap: var(--space-3); padding: var(--space-3);
        border: 1px solid var(--border); border-radius: var(--radius-md);
        transition: border-color 0.15s;
    }

    .cart-item:hover {
        border-color: var(--border-accent);
    }

    .inactive {
        opacity: 0.5;
    }

    .item-image {
        width: 60px; height: 60px;
        border-radius: var(--radius-sm);
        overflow: hidden;
        background: var(--bg-elevated);
    }

    .item-image img {
        width: 100%; height: 100%; object-fit: cover;
    }

    .item-placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        color: var(--text-muted);
        font-size: 20px;
    }

    .item-info {
        display: flex; flex-direction: column;
        gap: 2px;
    }

    .item-name {
        font-size: 13px; font-weight: 600; line-height: 1.3;
        color: var(--text-primary);
    }

    .item-warning {
        font-size: 11px; font-weight: 600;
        color: var(--danger);
    }

    .item-price {
        font-size: 12px;
        color: var(--text-muted);
    }

    .item-qty {
        display: flex; align-items: center; border-radius: var(--radius-sm);
        gap: var(--space-2); padding: var(--space-1) var(--space-2);
        background: var(--bg-elevated);
    }

    .item-qty button {
        background: transparent; color: var(--text-secondary);
        border: none; border-radius: 4px;
        font-size: 16px;
        width: 20px; height: 20px;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.15s;
    }

    .item-qty button:hover:not(:disabled) {
        background: var(--bg-hover); color: var(--text-primary);
    }

    .item-qty button:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .item-qty span {
        font-size: 14px; font-weight: 600; text-align: center;
        color: var(--text-primary);
        min-width: 20px;
    }

    .item-subtotal {
        font-size: 14px; font-weight: 700; text-align: right;
        color: var(--text-primary);
        min-width: 60px;
    }

    .btn-remove {
        background: transparent; color: var(--text-muted);
        border: none; border-radius: var(--radius-sm);
        font-size: 12px;
        padding: var(--space-1);
        transition: all 0.15s;
    }

    .btn-remove:hover {
        background: rgba(239, 68, 68, 0.1); color: var(--danger);
    }
</style>
