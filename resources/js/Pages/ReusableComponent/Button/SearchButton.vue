<template>
    <button type="submit" :disabled="processing"  class="btn btn-primary" :title="lang.search">
        <i class="fa fa-search"></i>
        {{ lang.search}}
        <span
            v-if="processing"
            class="spinner-border spinner-border-sm"
            role="status"
            aria-hidden="true"
        ></span>
    </button>
</template>

<script>
export default {
    name: "SearchButton",
    props: {
        processing: Boolean,
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
