<script lang="ts">
    import Navbar from "./Navbar.svelte";
    import CartItemComponent from "./CartItem.svelte";
    import CartSummary from "./CartSummary.svelte";
    import { cartStore, cartTotal } from "../lib/stores/cartStore";
    import { CartService } from "../lib/services/cartService";

    interface Props {
        onBack: () => void;
        onAuthClick: () => void;
        onLogout: () => void;
    }

    let { onBack, onAuthClick, onLogout }: Props = $props();

    async function handleUpdateQty(cartItemId: number, quantity: number) {
        if (quantity < 1) return;
        try {
            await CartService.updateQty(cartItemId, quantity);
        } catch (e) {
            console.error("Failed to update qty:", e);
        }
    }

    async function handleRemove(cartItemId: number) {
        try {
            await CartService.remove(cartItemId);
        } catch (e) {
            console.error("Failed to remove item:", e);
        }
    }
</script>

<Navbar
    minimal={true}
    activeFilter="all"
    cartCount={0}
    onFilterChange={() => {}}
    onCartClick={onBack}
    onAuthClick={onAuthClick}
    onLogout={onLogout}
/>

<main class="cart-main">
    <div class="cart-header">
        <button class="btn-back" onclick={onBack}>← Back</button>
        <h1 class="cart-title">Your Cart</h1>
    </div>

    {#if $cartStore.length === 0}
        <div class="empty">
            <p>Your cart is empty.</p>
            <button class="btn-shop" onclick={onBack}>Start shopping</button>
        </div>
    {:else}
        <div class="cart-layout">
            <ul class="cart-items">
                {#each $cartStore as item (item.cart_item_id)}
                    <CartItemComponent
                        {item}
                        onUpdateQty={handleUpdateQty}
                        onRemove={handleRemove}
                    />
                {/each}
            </ul>

            <CartSummary total={$cartTotal} />
        </div>
    {/if}
</main>

<style>
    .cart-main {
        max-width: 1000px;
        margin: 0 auto;
        padding: var(--space-5);
    }

    .cart-header {
        display: flex; align-items: center;
        gap: var(--space-4); margin-bottom: var(--space-5);
    }

    .btn-back {
        background: transparent; color: var(--accent-light);
        border: none; border-radius: var(--radius-sm);
        font-size: 14px; font-weight: 600;
        padding: var(--space-2) var(--space-3);
        transition: background 0.15s;
    }

    .btn-back:hover {
        background: var(--bg-hover);
    }

    .cart-title {
        color: var(--text-primary);
        font-size: 20px; font-weight: 700;
    }

    .empty {
        color: var(--text-muted);
        display: flex; flex-direction: column; align-items: center;
        gap: var(--space-4); padding: var(--space-6) 0;
    }

    .btn-shop {
        background: var(--accent); color: white;
        border: none; border-radius: var(--radius-sm);
        padding: var(--space-2) var(--space-5);
        font-size: 14px; font-weight: 600;
        transition: background 0.15s;
    }

    .btn-shop:hover {
        background: var(--accent-hover);
    }

    .cart-layout {
        display: grid; align-items: start;
        grid-template-columns: 1fr 280px;
        gap: var(--space-5);
    }

    .cart-items {
        list-style: none;
        display: flex; flex-direction: column;
        gap: var(--space-3);
    }
</style>
