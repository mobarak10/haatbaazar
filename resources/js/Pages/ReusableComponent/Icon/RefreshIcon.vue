<template>
    <inertia-link class="btn top-icon-button print-none ms-auto" :href="Url">
        <i class="bi bi-arrow-clockwise"></i>
    </inertia-link>
</template>

<script>
export default {
    name: "RefreshIcon",
    props: {
        Url: String
    }
}
</script>

<style scoped>

</style>
