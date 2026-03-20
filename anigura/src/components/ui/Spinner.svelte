<script lang="ts">
    interface Props {
        size?:  number;
        color?: string;
    }

    let { size=24, color="var(--accent)" }: Props = $props();
</script>

<div
    class=      "spinner"
    style=      "width:{size}px; height:{size}px; border-top-color:{color}"
    role=       "status"
    aria-label= "Loading"
></div>

<style>
    .spinner {
        border:        3px solid var(--border-accent);
        border-radius: 50%;
        animation:     spin 1s linear infinite;
    }

    @keyframes spin {
        0%   { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

