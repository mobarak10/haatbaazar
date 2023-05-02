<template>
    <inertia-link :href="Url" :title="lang.go_back"
                  class="pe-0 ms-auto print-none top-icon-button mb-2 mt-3">
        <i class="bi bi-arrow-left"></i>
    </inertia-link>
</template>

<script>
export default {
    name: "BackIcon",
    props: {
        Url: String,
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
