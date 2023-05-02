<template>
    <div class="container print-none">
        <div v-if="$page.props.flash.success && show">
            <div class="alert alert-success alert-dismissible  p-0 py-1 d-sm-flex d-block  align-items-center border-0" role="alert">
                <div class="m-2">
                    {{ $page.props.flash.success }}
                </div>
                <button type="button" class="btn-close ms-auto" @click="show=false" data-dismiss="alert"></button>
            </div>
        </div>
        <div v-if="$page.props.flash.error && show">
            <div class="alert alert-danger alert-dismissible  p-0 py-1 d-sm-flex d-block  align-items-center border-0" role="alert">
                <div class="m-2">
                    {{ $page.props.flash.error }}
                </div>
                <button type="button" class="btn-close ms-auto" @click="show=false" data-dismiss="alert"></button>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "FlashMessages",
    components: {

    },
    data() {
        return {
            show: true,
        }
    },
    watch: {
        '$page.props.flash': {
            handler() {
                this.show = true
            },
            deep: true,
        },
    },

    mounted() {

    }
}
</script>

<style scoped>

</style>
