<template>
    <label class="form-label" for="to_date">{{ lang.to_date }}</label>
    <input type="date" id="to_date" v-model="to_date" class="form-control">
</template>

<script>
export default {
    name: "ToDate",
    data() {
        return {
            to_date: null,
            lang: this.$page.props.lang
        }
    },
}
</script>

<style scoped>

</style>
