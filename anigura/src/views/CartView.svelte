<script lang="ts">
    import { onMount } from "svelte";
    import { push } from "svelte-spa-router";
    import Navbar from "../components/layout/Navbar.svelte";
    import CartItemCard from "../components/features/cart/CartItemCard.svelte";
    import Spinner from "../components/ui/Spinner.svelte";
    import Icon from "../components/ui/Icon.svelte";
    import { cartStore } from "../lib/stores/cart.store.svelte";
    import { authStore } from "../lib/stores/auth.store.svelte";
    import { cartService } from "../lib/services/cart.service";
    import { uiStore } from "../lib/stores/ui.store.svelte";

    if (!authStore.isLoggedIn) push("/");

    let loading     = $state(true);
    let selectedIds = $state<Set<number>>(new Set());

    function syncSelection() {
        selectedIds = new Set(
            cartStore.items
                .filter(i => i.status === "ok")
                .map(i => i.cart_item_id)
        );
    }

    const selectedItems = $derived(
        cartStore.items.filter(i => selectedIds.has(i.cart_item_id))
    );

    const selectedSubtotal = $derived(
        selectedItems.reduce((sum, i) => sum + parseFloat(i.subtotal), 0)
    );

    const hasIssues = $derived(
        cartStore.items.some(i => i.status !== "ok")
    );

    const availableItems = $derived(
        cartStore.items.filter(i => i.status === "ok")
    );

    const allSelected = $derived(
        availableItems.length > 0 &&
        availableItems.every(i => selectedIds.has(i.cart_item_id))
    );

    function handleSelect(cartItemId: number, checked: boolean) {
        const next = new Set(selectedIds);
        checked ? next.add(cartItemId) : next.delete(cartItemId);
        selectedIds = next;
    }

    function toggleAll() {
        if (allSelected) {
            selectedIds = new Set();
        } else {
            selectedIds = new Set(availableItems.map(i => i.cart_item_id));
        }
    }

    function handleCheckout() {
        if (selectedItems.length === 0) return;
        uiStore.showToast("Checkout coming soon!", "info");
    }

    onMount(async () => {
        loading = true;
        await cartService.loadCart();
        syncSelection();
        loading = false;
    });
</script>

<Navbar onAuthClick={() => {}} minimal={true} />

<main class="view-container">
    <div class="cart-header">
        <h1 class="cart-title">Your Cart</h1>
        {#if cartStore.items.length > 0}
            <span class="cart-count">{cartStore.totalItems} items</span>            
        {/if}
    </div>

    {#if loading}
        <div class="loader">
            <Spinner size={36} />
        </div>

    {:else if cartStore.items.length === 0}
        <div class="empty">
            <Icon name="cart" size={16} />
            <p class="empty-title">Your cart is empty</p>
            <p class="empty-desc">Add some products to get started</p>
            <button class="btn-shop" onclick={() => push("/")}>
                Browse products
            </button>
        </div>

    {:else}
        <div class="cart-layout">
            <div class="cart-items">

                {#if hasIssues}
                    <div class="issues-banner">
                        <Icon name="warning" size={28} /> Some items have stock issues and where deselected
                    </div>
                {/if}

                <div class="select-all">
                    <label for="" class="select-all-label">
                        <input
                            type="checkbox"
                            checked={allSelected}
                            onchange={toggleAll}
                        />
                        <span>Select all available ({availableItems.length})</span>
                    </label>
                </div>

                {#each cartStore.items as item (item.cart_item_id)}
                    <CartItemCard
                        {item}
                        selected={selectedIds.has(item.cart_item_id)}
                        onSelected={handleSelect}
                    />
                {/each}
            </div>

            <div class="cart-summary">
                <h2 class="summary-title">Summary</h2>

                <div class="summary-rows">
                    <div class="summary-row">
                        <span>Selected</span>
                        <span>{selectedItems.length} items</span>
                    </div>
                    <div class="summary-row total">
                        <span>Subtotal</span>
                        <span>${selectedSubtotal.toFixed(2)}</span>
                    </div>
                </div>

                <button
                    class="btn-checkout"
                    disabled={true}
                    onclick={handleCheckout}
                >
                    {selectedItems.length === 0 ? "Select items to checkout" : `Checkout (${selectedItems.length})`}
                </button>

                <button class="btn-continue" onclick={() => push("/")}>
                    Continue shopping
                </button>
            </div>
        </div>
    {/if}
</main>

<style>
    .view-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: var(--space-5);
    }

    .cart-header {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        margin-bottom: var(--space-5);
    }

    .cart-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .cart-count {
        font-size: 13px;
        color: var(--text-muted);
        background: var(--bg-elevated);
        padding: 2px var(--space-2);
        border-radius: 999px;
    }

    .loader {
        display: flex;
        justify-content: center;
        padding: var(--space-6) 0;
    }

    .empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: var(--space-3);
        padding: var(--space-6) 0;
        text-align: center;
    }

    .empty-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-secondary);
    }

    .empty-desc {
        font-size: 14px;
        color: var(--text-muted);
    }

    .btn-shop {
        margin-top: var(--space-2);
        padding: var(--space-2) var(--space-5);
        background: var(--accent);
        color: white;
        border: none;
        border-radius: var(--radius-sm);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
    }

    .btn-shop:hover { background: var(--accent-hover); }

    .cart-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: var(--space-5);
        align-items: start;
    }

    .cart-items {
        display: flex;
        flex-direction: column;
        gap: var(--space-3);
    }

    .issues-banner {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        padding: var(--space-3);
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.2);
        border-radius: var(--radius-sm);
        font-size: 13px;
        color: var(--danger);
        font-weight: 600;
    }

    .select-all {
        padding: var(--space-2) var(--space-1);
    }

    .select-all-label {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        font-size: 13px;
        color: var(--text-secondary);
        cursor: pointer;
    }

    .select-all-label input {
        width: 16px; height: 16px;
        accent-color: var(--accent);
    }

    .cart-summary {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: var(--space-4);
        position: sticky;
        top: 76px;
        display: flex;
        flex-direction: column;
        gap: var(--space-3);
    }

    .summary-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        padding-bottom: var(--space-3);
        border-bottom: 1px solid var(--border);
    }

    .summary-rows {
        display: flex;
        flex-direction: column;
        gap: var(--space-2);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        color: var(--text-secondary);
    }

    .summary-row.total {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        padding-top: var(--space-2);
        border-top: 1px solid var(--border);
    }

    .btn-checkout {
        width: 100%;
        padding: var(--space-3);
        background: var(--accent);
        color: white;
        border: none;
        border-radius: var(--radius-sm);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
    }

    .btn-checkout:hover:not(:disabled) {
        background: var(--accent-hover);
        box-shadow: var(--shadow-accent);
    }

    .btn-checkout:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-continue {
        width: 100%;
        padding: var(--space-2);
        background: transparent;
        color: var(--text-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 13px;
        cursor: pointer;
        transition: all 0.15s;
    }

    .btn-continue:hover {
        background: var(--bg-hover);
        color: var(--text-primary);
    }

    @media (max-width: 768px) {
        .cart-layout {
            grid-template-columns: 1fr;
        }

        .cart-summary {
            position: static;
        }
    }
</style>

