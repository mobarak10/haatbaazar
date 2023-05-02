<template>
    <label class="form-label" for="from_date">{{ lang.from_date }}</label>
    <input type="date" id="from_date" v-model="from_date" class="form-control">
</template>

<script>
export default {
    name: "FromDate",
    data() {
        return {
            from_date: null,
            lang: this.$page.props.lang
        }
    },
}
</script>

<style scoped>

</style>
