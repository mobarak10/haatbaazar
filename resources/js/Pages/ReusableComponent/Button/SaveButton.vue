<template>
    <button
        type="submit"
        class="btn custom-btn btn-success"
        :disabled="processing"
    >
        <span
            v-if="processing"
            class=" spinner-border spinner-border-sm"
            role="status"
            aria-hidden="true"
        ></span>
        {{ lang.save}}
    </button>
</template>

<script>
export default {
    name: "SaveButton",
    props: {
        processing: Boolean
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
