<script lang="ts">
    import Spinner from "./Spinner.svelte";

    interface Props {
        variant?:  "primary"|"ghost"|"danger";
        type?:     "button"|"submit";
        size?:     "sm"|"md"|"lg";
        disabled?: boolean;
        loading?:  boolean;
        class?:    string;
        children?: import("svelte").Snippet;
        onClick?:  (event: MouseEvent) => void;    
    }

    let {
        variant = "primary",
        type = "button",
        size = "md",
        disabled = false,
        loading = false,
        class: className = "",
        children,
        onClick,
    }: Props = $props();
</script>

<button
    {type}
    {disabled}
    class="btn btn-{variant} btn-{size} {className}"
    onclick={onClick}
>
    {#if loading}
        <span class="spinner-container">
            <Spinner size={size==="sm"?16:20} color="currentColor" />
        </span>
    {/if}

    <span style="
        opacity: {loading?0:1};
        display:flex;
        align-items:center;
        gap: var(--space-2);
    ">
        {@render children?.()}
    </span>
</button>

<style>
    .btn {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        border: none;
        border-radius: var(--radius-sm);
        transition: all 0.2s ease;
    }

    .btn-sm { padding: var(--space-2) var(--space-3); font-size: 13px; }
    .btn-md { padding: var(--space-3) var(--space-4); font-size: 15px; }
    .btn-lg { padding: var(--space-4) var(--space-5); font-size: 16px; }

    .btn-primary {
        background-color: var(--accent);
        color: white;
    }
    .btn-primary:hover:not(:disabled) {
        background-color: var(--accent-hover);
        box-shadow: var(--shadow-accent);
    }

    .btn-ghost {
        background-color: transparent;
        color: var(--text-primary);
        border: 1px solid var(--border);
    }
    .btn-ghost:hover:not(:disabled) {
        background-color: var(--bg-hover);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .spinner-container {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>

