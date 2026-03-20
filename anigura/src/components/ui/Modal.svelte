<script lang="ts">
    import { fade, scale } from "svelte/transition";
    import Icon from "./Icon.svelte";

    interface Props {
        open:     boolean;
        title?:   string;
        onClose:  () => void;
        children: import("svelte").Snippet; 
        footer?:  import("svelte").Snippet; 
    }

    let { 
        open, 
        title, 
        onClose, 
        children, 
        footer 
    }: Props = $props();

    function handleBackdropClick(event: MouseEvent) {
        if (event.target === event.currentTarget) {
            onClose();
        }
    }

    function handleKeydown(event: KeyboardEvent) {
        if (open && event.key === "Escape") {
            onClose();
        }
    }
</script>

<svelte:window onkeydown={handleKeydown} />

{#if open}
    <div 
        class="modal-backdrop"
        onclick={handleBackdropClick}
        transition:fade={{ duration: 200 }}
        role="presentation"
    >
        <div 
            class="modal-box" 
            transition:scale={{ duration: 200, start: 0.95 }}
            role="dialog"
            aria-modal="true"
            tabindex="-1"
        >
            
            <div class="modal-header">
                <h3>{title || ""}</h3>
                <button class="close-btn" onclick={onClose} aria-label="Close modal">
                    <Icon name="close" size={24} />
                </button>
            </div>

            <div class="modal-body">
                {@render children()}
            </div>

            {#if footer}
                <div class="modal-footer">
                    {@render footer()}
                </div>
            {/if}

        </div>
    </div>
{/if}

<style>
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.6);
        width: 100vw; height: 100vh;
        position: fixed;
        padding: var(--space-4);
        top: 0; left: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-box {
        background-color: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 500px; max-height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        outline: none;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--space-4) var(--space-5);
        border-bottom: 1px solid var(--border);
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.25rem;
        color: var(--text-primary);
    }

    .close-btn {
        background: transparent;
        border: none;
        color: var(--text-secondary);
        padding: var(--space-1);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .close-btn:hover {
        background-color: var(--bg-hover);
        color: var(--text-primary);
    }

    .modal-body {
        padding: var(--space-5);
        overflow-y: auto;
        color: var(--text-secondary);
    }

    .modal-footer {
        padding: var(--space-4) var(--space-5);
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: var(--space-3);
        background-color: var(--bg-elevated);
    }
</style>

