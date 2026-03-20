<script lang="ts">
    export type BadgeVariant = "success"|"warning"|"danger"|"info";

    interface Props {
        variant?: BadgeVariant;
        children: import("svelte").Snippet; 
    }

    let { variant = "info", children }: Props = $props();
</script>

<span class="badge badge-{variant}">
    {@render children()}
</span>

<style>
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-success { background-color: var(--success); color: #fff; }
    .badge-warning { background-color: var(--warning); color: #fff; }
    .badge-danger { background-color: var(--danger); color: #fff; }
    .badge-info { background-color: var(--accent); color: #fff; }
</style>

