<template>
    <a href="#" onclick="window.print();" :title="lang.print" class="btn top-icon-button print-none ms-auto">
        <i class="bi bi-printer"></i>
    </a>
</template>

<script>
export default {
    name: "PrintIcon",
    data() {
        return {
            lang: this.$page.props.lang
        }
    }
}
</script>

<style scoped>

</style>
