<template>
    <inertia-link :href="Url" class="btn table-small-button" :title="lang.show">
        <i class="bi bi-eye"></i>
    </inertia-link >
</template>

<script>
export default {
    name: "ShowIcon",
    props: {
        Url: String
    },
    data() {
        return {
            lang: this.$page.props.lang
        }
    }
}
</script>

<style scoped>

</style>
