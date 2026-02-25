<script lang="ts">
    import { onMount } from "svelte";
    import { API } from "./lib/api";
    import type { Product } from "./lib/types";
    import Navbar from "./components/Navbar.svelte";
    import ProductGrid from "./components/ProductGrid.svelte";

    let products = $state<Product[]>([]);
    let error = $state<string | null>(null);
    let activeFilter = $state("all");

    onMount(async () => {
        try {
            products = await API.getProducts();
        } catch (e) {
            error = e instanceof Error ? e.message : "Error";
        }
    });
</script>

<Navbar
    activeFilter={activeFilter}
    cartCount={0}
    onFilterChange={(f) => activeFilter = f}
/>

<main class="main">
    {#if error}
        <p class="error">{error}</p>
    {:else}
        <ProductGrid
            {products}
            {activeFilter}
            onAddToCart={(p) => console.log("cart:", p.id)}
        />
    {/if}
</main>

<style>
    .main {
        padding: var(--space-5);
        max-width: 1400px;
        margin: 0 auto;
    }

    .error {
        color: var(--dager);
    }
</style>
