<template>
    <div v-if="hasErrors">
        <div v-for="(error, key) in errors" :key="key" class="alert alert-danger p-2 mb-0 border-0 rounded-0" role="alert" >
            {{ error }}
        </div>
    </div>
</template>

<script>
    export default {
        computed: {
            errors() {
                return this.$page.props.errors
            },

            hasErrors() {
                return Object.keys(this.errors).length > 0;
            },
        }
    }
</script>
