<template>
    <a class="btn top-icon-button print-none ms-auto mx-1" data-bs-toggle="collapse" :href="Url" role="button" aria-expanded="false">
        <i class="bi bi-search"></i>
    </a>
</template>

<script>
export default {
    name: "SearchIcon",
    props: {
        Url: String
    },
}
</script>

<style scoped>

</style>
