<template>
    <a
        href="#"
        class=" btn table-small-button"
        :title="lang.delete"
    >
        <i class="bi bi-trash"></i>
    </a>
</template>

<script>
export default {
    name: "DeleteIcon",
    data() {
        return {
            lang: this.$page.props.lang
        }
    }
}
</script>

<style scoped>

</style>
