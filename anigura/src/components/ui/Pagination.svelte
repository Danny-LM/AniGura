<script lang="ts">
    interface PaginationInfo {
        total:   number;
        pages:   number;
        current: number;
        next:    number|null;
        prev:    number|null;
    }

    interface Props {
        info:         PaginationInfo;
        onPageChange: (page: number) => void;
    }

    let { info, onPageChange }: Props = $props();
</script>

{#if info.pages > 1}
    <div class="pagination">
        <button 
            class="page-btn" 
            disabled={!info.prev} 
            onclick={() => info.prev && onPageChange(info.prev)}
        >
            Prev
        </button>

        <span class="page-info">
            Page {info.current} of {info.pages}
        </span>

        <button 
            class="page-btn" 
            disabled={!info.next} 
            onclick={() => info.next && onPageChange(info.next)}
        >
            Next
        </button>
    </div>
{/if}

<style>
    .pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--space-4);
        margin-top: var(--space-5);
    }

    .page-btn {
        background-color: var(--bg-surface);
        border: 1px solid var(--border);
        color: var(--text-primary);
        padding: var(--space-2) var(--space-4);
        border-radius: var(--radius-sm);
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .page-btn:hover:not(:disabled) {
        background-color: var(--bg-hover);
        border-color: var(--border-accent);
        color: var(--accent-light);
    }

    .page-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .page-info {
        color: var(--text-secondary);
        font-size: 14px;
    }
</style>

