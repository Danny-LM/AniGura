<script lang="ts">
    import { onMount } from "svelte";
    import Router from "svelte-spa-router";
    import { routes } from "./router";
    import { authStore } from "./lib/stores/auth.store";
    import { uiStore } from "./lib/stores/ui.store";
    import Toast from "./components/ui/Toast.svelte";
    import Spinner from "./components/ui/Spinner.svelte";

    onMount(() => {
        window.addEventListener("auth:sessionExpired", () => {
            authStore.logout();
            uiStore.showToast("Your session has expired. Please login again.", "warning", 5000);
        });
    });
</script>

<div class="toast-container">
    {#each uiStore.toasts as toast (toast.id) }
        <Toast
            id={toast.id}
            message={toast.msg}
            type={toast.type}
            onClose={(id) => uiStore.removeToast(id)}
        />        
    {/each}
</div>

{#if uiStore.globalLoading}
    <div class="global-loader">
        <Spinner size={40} />
    </div>
{/if}

<Router {routes} />

<style>
    .toast-container {
        position: fixed;
        bottom: var(--space-5);
        right: var(--space-5);
        display: flex;
        flex-direction: column;
        gap: var(--space-3);
        z-index: 9999;
    }

    .global-loader {
        position: fixed;
        top: 0; left: 0;
        width: 100vw; height: 100vh;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        backdrop-filter: blur(2px);
    }
</style>

