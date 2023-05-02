<template>
    <inertia-link
        :href="href"
        :exact="exact"
        :class="{active: $page.props.currentFullUrl === href}">
        <slot/>
    </inertia-link>
</template>

<script>
export default {
    name: "Link",
    props: {
        href: String,
        exact: Boolean,
    },
    data() {
        return {
        }
    },
    mounted() {

    }
}
</script>

<style scoped>

</style>
