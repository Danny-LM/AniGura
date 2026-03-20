<script lang="ts">
    import { fly, fade } from "svelte/transition";
    import Icon from "./Icon.svelte";
    import type { ToastType } from "../../lib/types";

    interface Props {
        id:      string;
        message: string;
        type?:   ToastType;
        onClose: (id: string) => void;
    }

    let { id, message, type ="info", onClose }: Props = $props();

    const typeStyles: Record<ToastType, { color:string, border:string }> = {
        success: { color: "var(--success)", border: "var(--success)" },
        error:   { color: "var(--danger)",  border: "var(--danger)"  },
        warning: { color: "var(--warning)", border: "var(--warning)" },
        info:    { color: "var(--accent)",  border: "var(--accent)"  },
    };

    let styleInfo = $derived(typeStyles[type]);
</script>

<div
    class="toast"
    style="border-left-color: {styleInfo.border};"
    in:fly={{ x:50, duration:300 }}
    out:fade={{ duration:200 }}
    role="alert"
>
    <div class="toast-content">
        <span class="toast-message">{message}</span>
    </div>

    <button class="close-btn" onclick={() => onClose(id)} aria-label="Dismiss">
        <Icon name="close" size={16} />
    </button>
</div>

<style>
    .toast {
        background-color: var(--bg-elevated);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        border: 1px solid var(--border);
        border-left-width: 4px;
        border-radius: var(--radius-sm);
        padding: var(--space-3) var(--space-4);
        width: 300px;
        box-shadow: var(--shadow-sm);
        pointer-events: auto;
    }

    .toast-content {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        flex: 1;
    }

    .toast-message {
        color: var(--text-primary);
        font-size: 14px;
        line-height: 1.4;
    }

    .close-btn {
        background: transparent;
        color: var(--text-muted);
        border: none;
        margin-left: var(--space-3);
        padding: var(--space-1);
        display: flex;
    }

    .close-btn:hover {
        color: var(--text-primary);
    }
</style>

