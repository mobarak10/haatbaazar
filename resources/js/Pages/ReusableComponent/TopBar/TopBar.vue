<template>
    <div class="container print-none">
        <ul class="nav nav-tabs mt-2">
            <li class="nav-item">
                <inertia-link class="nav-link" :class="{active: $page.props.currentFullUrl === indexRoute}" :href="indexRoute">{{lang.all_records}}</inertia-link>
            </li>

            <li v-if="modal" class="nav-item">
                <a class="nav-link" href="#" @click="modal">{{ lang.new_entry }}</a>
            </li>

            <li v-else class="nav-item">
                <inertia-link class="nav-link" :class="{active: $page.props.currentFullUrl === createRoute}" :href="createRoute">{{ lang.new_entry }}</inertia-link>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: "TopBar",
    props: {
        indexRoute: String,
        createRoute: String,
        modal: Function,
    },
    data() {
        return {
            lang: this.$page.props.lang
        }
    },

    mounted() {
    }
}
</script>

<style scoped>

</style>
