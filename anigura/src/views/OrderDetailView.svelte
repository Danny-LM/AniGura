<script lang="ts">
    let { params } = $props<{ params?: { id?: string } }>();
</script>

<main>
    <h1>Order {params?.id} Details</h1>
</main>

