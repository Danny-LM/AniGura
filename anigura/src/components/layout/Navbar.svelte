<script lang="ts">
    import { push } from "svelte-spa-router";
    import Icon from "../ui/Icon.svelte";
    import { authStore } from "../../lib/stores/auth.store.svelte";
    import { cartStore } from "../../lib/stores/cart.store.svelte";
    import { authService } from "../../lib/services/auth.service";

    interface Props {
        onAuthClick:     () => void;
        activeFilter?:   string;
        onFilterChange?: (filter: string) => void;
        minimal?:        boolean;
    }

    let {
        onAuthClick,
        activeFilter = "all",
        onFilterChange,
        minimal = false,
    }: Props = $props();

    const FILTERS = [
        { label: "All",     value: "all"          },
        { label: "Manga",   value: "manga_volume" },
        { label: "Setbox",  value: "setbox"       },
        { label: "Figures", value: "figure"       },
    ];

    async function handleLogout() {
        await authService.logout();
        push("/");        
    }
</script>

<nav class="navbar">

    <button class="brand" onclick={() => push("/")}>
        <span class="brand-accent">ANI</span>GURA
    </button>

    {#if !minimal}
        <ul class="filters">
            {#each FILTERS as f}
                <li>
                    <button
                        class="filter-btn"
                        class:active={activeFilter === f.value}
                        onclick={() => onFilterChange?.(f.value)}
                    >{f.label}</button>
                </li>                
            {/each}
        </ul>
    {/if}

    <div class="actions">
        {#if !minimal && authStore.isLoggedIn}
            <button class="icon-btn" aria-label="Cart" onclick={() => push("/cart")}>
                <Icon name="cart"/>
                {#if cartStore.totalItems > 0}
                    <span class="badge">{cartStore.totalItems}</span>
                {/if}
            </button>
        {/if}

        {#if authStore.isLoggedIn}
            <button
                class="icon-btn user-btn"
                onclick={handleLogout}
                aria-label="Logout"
                title="Click to logout"
            >
                <span class="user-initials">
                    {authStore.currentUser?.full_name.charAt(0).toUpperCase()}
                </span>
            </button>
        {:else}
            <button class="icon-btn" aria-label="Login" onclick={onAuthClick}>
                <Icon name="account"/>
            </button>
        {/if}

    </div>
</nav>

<style>
    .navbar {
        position: sticky; top: 0; z-index: 100;
        display: flex; align-items: center;
        gap: var(--space-4); padding: 0 var(--space-5);
        height: 56px;
        background: var(--bg-surface);
        border-bottom: 1px solid var(--border);
    }

    .brand {
        font-size: 18px; font-weight: 800; letter-spacing: 2px;
        color: var(--text-primary);
        background: transparent; border: none;
        white-space: nowrap;
    }

    .brand-accent { color: var(--accent-light); }

    .filters {
        display: flex; align-items: center;
        gap: var(--space-1); flex: 1;
        list-style: none;
    }

    .filter-btn {
        padding: var(--space-2) var(--space-3);
        background: transparent;
        border: none; border-radius: var(--radius-sm);
        color: var(--text-secondary);
        font-size: 13px; font-weight: 600; letter-spacing: 0.5px;
        transition: all 0.15s;
    }

    .filter-btn:hover  { color: var(--text-primary); background: var(--bg-hover); }
    .filter-btn.active { color: var(--accent-light); background: var(--accent-soft); }

    .actions {
        display: flex; align-items: center;
        gap: var(--space-2); margin-left: auto;
    }

    .icon-btn {
        position: relative;
        display: flex; align-items: center; justify-content: center;
        width: 36px; height: 36px;
        background: transparent;
        border: none; border-radius: var(--radius-sm);
        color: var(--text-secondary);
        transition: all 0.15s;
    }

    .icon-btn:hover { color: var(--text-primary); background: var(--bg-hover); }

    .badge {
        position: absolute; top: 2px; right: 2px;
        min-width: 16px; height: 16px; padding: 0 4px;
        background: var(--accent); color: white;
        font-size: 10px; font-weight: 700;
        border-radius: 999px;
        display: flex; align-items: center; justify-content: center;
    }

    .user-btn {
        background: var(--accent-soft);
        border: 1px solid var(--border-accent)
    }

    .user-initials {
        font-size: 13px; font-weight: 700;
        color: var(--accent-light);
    }
</style>

