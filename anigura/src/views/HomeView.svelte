<script lang="ts">
    import { onMount } from "svelte";
    import Navbar from "../components/layout/Navbar.svelte";
    import AuthModal from "../components/features/auth/AuthModal.svelte";
    import ProductGrid from "../components/features/products/ProductGrid.svelte";
    import Pagination from "../components/ui/Pagination.svelte";
    import Spinner from "../components/ui/Spinner.svelte";
    import { productService } from "../lib/services/product.service";
    import { uiStore } from "../lib/stores/ui.store.svelte";
    import type { Product, PaginationInfo, ProductType } from "../lib/types";
    import { getErrorMsg } from "../lib/utils";

    let authOpen     = $state(false);
    let activeFilter = $state("all");

    let allProducts  = $state<Product[]>([]);
    let pagination   = $state<PaginationInfo|null>(null);
    let loading      = $state(false);
    let currentPage  = $state(1);

    const LIMIT = 20;

    async function loadProducts(page: number, type?: string) {
        loading = true;
        try {
            const result = await productService.getAll(page, LIMIT, type === "all" ? undefined : type as ProductType );
            allProducts = result.results;
            pagination  = result.info;
            currentPage = page;

        } catch (err) {
            uiStore.showToast(getErrorMsg(err, "Failed to load products"), "error");
        } finally {
            loading = false;
        }
    }

    function handlePageChange(page: number) {
        loadProducts(page, activeFilter);
        window.scrollTo({ top: 0, behavior: "smooth" });
    }

    function handleFilterChange(filter: string) {
        activeFilter = filter;
        loadProducts(1, filter);
    }

    onMount(() => loadProducts(1));
</script>

<Navbar
    onAuthClick={() => authOpen = true}
    {activeFilter}
    onFilterChange={handleFilterChange}
/>

<main class="view-container">
    {#if loading}
        <div class="loader">
            <Spinner size={36}/>
        </div>
    {:else}
        <ProductGrid products={allProducts} />

        {#if pagination}
            <Pagination info={pagination} onPageChange={handlePageChange} />            
        {/if}
    {/if}
</main>

<AuthModal
    open={authOpen}
    onClose={() => authOpen = false}
/>

<style>
    .view-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: var(--space-5);
    }

    .loader {
        display: flex;
        justify-content: center;
        padding: var(--space-6) 0;
    }
</style>

