<script lang="ts">
    import Icon from "./Icon.svelte";
    import { authStore, isLoggedIn } from "../lib/stores/authStore";

    interface Props {
        activeFilter:   string,
        cartCount:      number;
        onFilterChange: (filter: string) => void;
        onCartClick:    () => void;
        onAuthClick:    () => void;
        onLogout:       () => void;
        minimal?:       boolean;
    }

    let { 
        activeFilter, cartCount, onFilterChange, onCartClick,
        onAuthClick,onLogout, minimal = false
    }: Props = $props();

    const FILTERS = [
        { label: "ALL",      value: "all"           },
        { label: "Manga",     value: "manga_volume" },
        { label: "Boxet",     value: "setbox"       },
        { label: "Figures",   value: "figure"       },
    ];
</script>

<nav class="navbar">

    <!-- Brand -->
     <a href="/" class="brand">
        <span class="brand-accent">ANI</span>GURA
    </a>

    {#if !minimal}
        <!-- Filters -->
        <ul class="filters">
            {#each FILTERS as f}
                <li>
                    <button 
                        class="filter-btn"
                        class:active={activeFilter === f.value}
                        onclick={() => onFilterChange(f.value)}
                    >
                        {f.label}
                    </button>
                </li>            
            {/each}
        </ul>
    {/if}

    <!-- Actions -->
    <div class="actions">
        {#if !minimal}
            <!-- Cart -->
            <button class="icon-btn" aria-label="Cart" disabled={!$isLoggedIn} onclick={onCartClick}>
                <Icon name="cart" />
                {#if cartCount > 0}
                    <span class="badge">{cartCount}</span>
                {/if}
            </button>
        {/if}

        {#if $isLoggedIn}
            <button class="icon-btn user-btn" onclick={onLogout} aria-label="logout">
                <span class="user-initials">
                    {$authStore?.full_name.charAt(0).toUpperCase()}
                </span>
            </button>

        {:else}
            <button class="icon-btn" aria-label="Account" onclick={onAuthClick}>
                <Icon name="account" />
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
        color: var(--text-primary); white-space: nowrap;
    }

    .brand-accent {
        color: var(--accent-light);
    }

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

    .filter-btn:hover {
        color: var(--text-primary);
        background: var(--bg-hover);
    }

    .filter-btn.active {
        color: var(--accent-light);
        background: var(--accent-soft);
    }

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

    .icon-btn:hover {
        color: var(--text-primary);
        background: var(--bg-hover);
    }

    .badge {
        position: absolute; top: 2px; right: 2px;
        min-width: 16px; height: 16px;
        padding: 0 4px;
        background: var(--accent);
        color: white;
        font-size: 10px; font-weight: 700;
        border-radius: 999px;
        display: flex; align-items: center; justify-content: center;
    }

    .icon-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .user-btn {
        background: var(--accent-soft);
        border: 1px solid var(--border-accent);
    }

    .user-initials {
        font-size: 13px; font-weight: 700;
        color: var(--accent-light);
    }
</style>
