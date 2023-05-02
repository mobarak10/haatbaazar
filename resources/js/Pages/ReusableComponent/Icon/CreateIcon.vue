<template>
    <inertia-link class="btn top-icon-button print-none ms-auto" :title="lang.create_new" :href="Url">
        <i class="bi bi-plus-circle"></i>
    </inertia-link>
</template>

<script>
export default {
    name: "CreateIcon",
    props: {
        Url: String,
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
