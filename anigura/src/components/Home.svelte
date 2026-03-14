<script lang="ts">
    import type { Product } from "../lib/types";
    import Navbar from "./Navbar.svelte";
    import ProductGrid from "./ProductGrid.svelte";

    interface Props {
        products: Product[];
        error: string|null;
        activeFilter: string;
        onFilterChange: (f: string) => void;
        onAddToCart: (p: Product) => void;
        onCartClick: () => void;
        onAuthClick: () => void;
        onLogout: () => void;
        cartCount: number;
    }

    let {
        products, error, activeFilter,
        onFilterChange, onAddToCart,
        onCartClick, onAuthClick,
        onLogout, cartCount
    }: Props = $props();
</script>

<Navbar
    {activeFilter}
    {cartCount}
    {onFilterChange}
    {onCartClick}
    {onAuthClick}
    {onLogout}
/>

<main class="main">
    {#if error}
        <p class="error">{error}</p>
    {:else}
        <ProductGrid {products} {activeFilter} {onAddToCart} />
    {/if}
</main>

<style>
    .main {
        padding: var(--space-5);
        max-width: 1400px;
        margin: 0 auto;
    }

    .error {
        color: var(--danger);
    }
</style>
