<template>
    <inertia-link :href="Url" class="btn table-small-button" :title="lang.show">
        <i class="bi bi-pencil"></i>
    </inertia-link >
</template>

<script>
export default {
    name: "EditIcon",
    props: {
        Url: String
    },
    data() {
        return {
            lang: this.$page.props.lang
        }
    },
}
</script>

<style scoped>

</style>
