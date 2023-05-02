<template>
    <div v-if="links.length > 3">
        <div class="card-footer print-none">
            <nav aria-label="Page navigation example">
                <ul class="pagination pagination-sm m-0  justify-content-end">
                    <template v-for="(link, index) in links" :key="index">
                        <div v-if="link.url === null || link.active"  class="mr-1 mb-1 px-3 py-2 text-sm leading-4 text-dark-800 border rounded" v-html="link.label" />
                        <inertia-link v-else class="mr-1 mb-1 px-3 py-2 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500" :href="link.url" v-html="link.label" />
                    </template>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script>
export default {
    name: "Pagination",
    props: {
        links: Array,
    },

    mounted() {
    }
}
</script>

<style scoped>

</style>
