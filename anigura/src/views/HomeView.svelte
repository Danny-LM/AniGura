<script lang="ts">
    import Navbar from "../components/layout/Navbar.svelte";
    import AuthModal from "../components/features/auth/AuthModal.svelte";

    let authOpen     = $state(false);
    let activeFilter = $state("all");
</script>

<Navbar
    onAuthClick={() => authOpen = true}
    {activeFilter}
    onFilterChange={(f) => activeFilter = f}
/>

<main class="view-container">
    <h1 class="view-title">Products</h1>
    <p class="view-placeholder">Coming soon...</p>
</main>

<AuthModal
    open={authOpen}
    onClose={() => authOpen = false}
/>

<style>
    .view-container { max-width: 1400px; margin: 0 auto; padding: var(--space-5); }
    .view-title { font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: var(--space-3); }
    .view-placeholder { color: var(--text-muted); font-size: 14px; }
</style>

