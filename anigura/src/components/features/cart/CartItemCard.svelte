<script lang="ts">
    import type { CartItem, CartItemStatus } from "../../../lib/types";
    import { cartService } from "../../../lib/services/cart.service"; 
    import { uiStore } from "../../../lib/stores/ui.store.svelte";

    interface Props {
        item:       CartItem;
        selected:   boolean;
        onSelected: (id: number, checked: boolean) => void;
    }

    let { item, selected, onSelected }: Props = $props();

    const status = $derived<CartItemStatus>(
        !item.active || item.available === 0 ? "unavailable"  :
        item.quantity > item.available       ? "insufficient" :
        "ok"
    );

    const isUnavailable  = $derived(status === "unavailable");
    const isInsufficient = $derived(status === "insufficient");
    const hasIssue       = $derived(status !== "ok");
    const canSelect      = $derived(status === "ok");

    const STATUS_MSG = $derived({
        unavailable:  "Product no longer available",
        insufficient: `Only ${item.available} in stock`,
    });

    async function changeQty(amount: number) {
        const oldQty = item.quantity;
        const newQty = oldQty + amount;
        if (newQty <= 0) return uiStore.showToast("Invalid quantity", "error");
        
        await cartService.updateQuantity(
            item.id_product, item.cart_item_id, newQty, oldQty
        );
    }

    async function handleInputChange(e: Event) {
        const newQty = parseInt((e.currentTarget as HTMLInputElement).value);
        if (!isNaN(newQty) && newQty > 0) {
            await cartService.updateQuantity(
                item.id_product, item.cart_item_id, newQty, item.quantity
            );
        }
    }

    async function remove() {
        await cartService.removeItem(item.id_product, item.cart_item_id);
    }
</script>

<div class="cart-item" class:disabled={hasIssue}>
    <label class="checkbox-wrapper">
        <input
            type="checkbox"
            checked={selected}
            disabled={!canSelect}
            onchange={(e) => onSelected(item.cart_item_id, e.currentTarget.checked)}
        />
    </label>

    <div class="item-image">
        {#if item.cover_image}
            <img src="{item.cover_image}" alt="{item.name}" />
        {:else}
            <div class="no-img">?</div>
        {/if}
    </div>

    <div class="item-info">
        <p class="item-name">{item.name}</p>

        {#if hasIssue}
            <p class="item-warning">{STATUS_MSG[status as keyof typeof STATUS_MSG]}</p>
        {/if}

        <div class="item-price">
            <span class="unit-price">${item.unit_price}</span>
            {#if parseFloat(item.discount) > 0}
                <span class="original-price">${parseFloat(item.price).toFixed(2)}</span>
            {/if}
        </div>
    </div>

    <div class="item-controls">
        <div class="qty-control">
            <button
                class="qty-btn"
                onclick={() => changeQty(-1)}
                disabled={isUnavailable}
            >-</button>

            <input
                type="number"
                class="qty-input"
                value={item.quantity}
                min="1"
                max={item.available}
                disabled={isUnavailable}
                onchange={handleInputChange}
            />

            <button
                class="qty-btn"
                onclick={() => changeQty(1)}
                disabled={isUnavailable || isInsufficient || item.quantity >= item.available}
            >+</button>
        </div>

        <p class="item-subtotal">${item.subtotal}</p>

        <button class="remove-btn" onclick={remove} aria-label="Remove">X</button>
    </div>

</div>

<style>
    .cart-item {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        padding: var(--space-4);
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        transition: border-color 0.15s;
    }

    .cart-item:hover { border-color: var(--border-accent); }

    .cart-item.disabled {
        opacity: 0.6;
        border-color: var(--danger);
    }

    .checkbox-wrapper input[type="checkbox"] {
        width: 18px; height: 18px;
        accent-color: var(--accent);
        cursor: pointer;
    }

    .checkbox-wrapper input:disabled { cursor: not-allowed; }

    .item-image {
        width: 64px; height: 80px;
        border-radius: var(--radius-sm);
        overflow: hidden;
        flex-shrink: 0;
        background: var(--bg-elevated);
    }

    .item-image img {
        width: 100%; height: 100%;
        object-fit: cover;
    }

    .no-img {
        width: 100%; height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 20px;
    }

    .item-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: var(--space-1);
        min-width: 0;
    }

    .item-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .item-warning {
        font-size: 12px;
        color: var(--danger);
        font-weight: 600;
    }

    .item-price {
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }

    .unit-price {
        font-size: 14px;
        font-weight: 700;
        color: var(--accent-light);
    }

    .original-price {
        font-size: 12px;
        color: var(--text-muted);
        text-decoration: line-through;
    }

    .item-controls {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: var(--space-2);
        flex-shrink: 0;
    }

    .qty-control {
        display: flex;
        align-items: center;
        background: var(--bg-elevated);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        overflow: hidden;
    }

    .qty-btn {
        width: 32px; height: 32px;
        background: transparent;
        border: none;
        color: var(--text-primary);
        font-size: 18px;
        cursor: pointer;
        transition: background 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qty-btn:hover:not(:disabled) { background: var(--bg-hover); }
    .qty-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    .qty-input {
        width: 48px; height: 32px;
        background: transparent;
        border: none;
        border-left: 1px solid var(--border);
        border-right: 1px solid var(--border);
        color: var(--text-primary);
        font-size: 14px;
        font-weight: 600;
        text-align: center;
        outline: none;
        -moz-appearance: textfield;
        appearance: textfield;
    }

    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
    }

    .item-subtotal {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .remove-btn {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 12px;
        padding: var(--space-1);
        border-radius: var(--radius-sm);
        transition: all 0.15s;
        cursor: pointer;
    }

    .remove-btn:hover {
        color: var(--danger);
        background: rgba(239, 68, 68, 0.1);
    }
</style>

