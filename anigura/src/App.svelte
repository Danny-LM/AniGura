<script lang="ts">
    import { onMount } from "svelte";
    import { API } from "./lib/api";
    import { authStore, isLoggedIn } from "./lib/stores/authStore";
    import { cartCount } from "./lib/stores/cartStore";
    import { CartService } from "./lib/services/cartService";
    import type { Product } from "./lib/types";
    import AuthModal from "./components/AuthModal.svelte";
    import Home from "./components/Home.svelte";

    type View = "home" | "cart";

    let view         = $state<View>("home");
    let products     = $state<Product[]>([]);
    let error        = $state<string | null>(null);
    let activeFilter = $state("all");
    let authOpen     = $state(false);
    let pendingProduct = $state<Product | null>(null);

    onMount(async () => {
        try {
            products = await API.getProducts();
        } catch (e) {
            error = e instanceof Error ? e.message : "Error";
        }

        if ($isLoggedIn) await CartService.load();
    });

    async function handleAddToCart(product: Product) {
        if (!$isLoggedIn) {
            pendingProduct = product;
            authOpen = true;
            return;
        }
        try {
            await CartService.add(product.id);
        } catch (e) {
            console.error("Failed to add to cart:", e);
        }
    }

    async function handleAuthSuccess() {
        authOpen = false;
        await CartService.load();
        if (pendingProduct) {
            try {
                await CartService.add(pendingProduct.id);
            } catch (e) {
                console.error("Failed to add pending product:", e);
            } finally {
                pendingProduct = null;
            }
        }
    }

    function handleLogout() {
        authStore.logout();
        CartService.clear();
    }
</script>

{#if view === "home"}
    <Home
        {products}
        {error}
        {activeFilter}
        cartCount={$cartCount}
        onFilterChange={(f) => activeFilter = f}
        onAddToCart={handleAddToCart}
        onCartClick={() => view = "cart"}
        onAuthClick={() => authOpen = true}
        onLogout={handleLogout}
    />
{:else if view === "cart"}
    <!-- CartView viene después -->
{/if}

<AuthModal
    open={authOpen}
    onClose={() => { authOpen = false; pendingProduct = null; }}
    onSuccess={handleAuthSuccess}
/>