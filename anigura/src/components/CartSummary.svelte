<script lang="ts">
    interface Props {
        total: number;
        onCheckout?: () => void;
    }

    let { total, onCheckout }: Props = $props();
</script>

<div class="cart-summary">
    <h2 class="summary-title">Summary</h2>

    <div class="summary-row">
        <span>Total</span>
        <span class="summary-total">${total.toFixed(2)}</span>
    </div>

    <button
        class="btn-checkout"
        disabled={!onCheckout}
        onclick={onCheckout}
    >
        {onCheckout ? "Checkout" : "Checkout (coming soon)"}
    </button>
</div>

<style>
    .cart-summary {
        background: var(--bg-surface);
        border: 1px solid var(--border); border-radius: var(--radius-md);
        padding: var(--space-4); gap: var(--space-3);
        display: flex; flex-direction: column;
        position: sticky; top: 70px;
    }

    .summary-title {
        font-size: 16px; font-weight: 700;
        color: var(--text-primary);
    }

    .summary-row {
        display: flex; justify-content: space-between; align-items: center;
        font-size: 14px;
        color: var(--text-secondary);
        padding-top: var(--space-2);
        border-top: 1px solid var(--border);
    }

    .summary-total {
        font-size: 18px; font-weight: 700;
        color: var(--text-primary);
    }

    .btn-checkout {
        width: 100%;
        padding: var(--space-3);
        background: var(--accent);
        color: white;
        border: none; border-radius: var(--radius-sm);
        font-size: 14px; font-weight: 600;
        transition: background 0.15s;
    }

    .btn-checkout:hover:not(:disabled) {
        background: var(--accent-hover);
    }

    .btn-checkout:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
