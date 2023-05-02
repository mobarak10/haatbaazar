<template>
    <a
        href="#"
        class=" btn table-small-button"
        :title="lang.edit"
    >
        <i class="bi bi-pencil"></i>
    </a>
</template>

<script>
export default {
    name: "EditModalIcon",
    data() {
        return {
            lang: this.$page.props.lang
        }
    },
}
</script>

<style scoped>

</style>
