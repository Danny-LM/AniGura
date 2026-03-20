<script lang="ts">
    import { push } from "svelte-spa-router";
</script>

<main class="view-container">
    <div class="not-found">
        <span class="code">404</span>
        <h1 class="title">Page Not Found</h1>
        <p class="desc">The page you are looking for does not exist.</p>
        <button class="btn-home" onclick={() => push("/")}>Go Home</button>
    </div>
</main>

<style>
    .view-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content:
        center;
        padding: var(--space-5);
    }

    .not-found {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: var(--space-3);
    }

    .code {
        font-size: 80px;
        font-weight: 800;
        color: var(--accent);
        line-height: 1;
    }
    
    .title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .desc {
        font-size: 14px;
        color: var(--text-muted);
    }

    .btn-home {
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
    
    .btn-home:hover {
        background: var(--accent-hover);
    }
</style>

