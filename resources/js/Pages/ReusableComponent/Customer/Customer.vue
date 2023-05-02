<template>
    <label for="customer" class="form-label">{{ lang.customer }}</label>
    <v-select
        id="customer"
        :options="allCustomers"
        :reduce="customer => customer.id"
        :placeholder="lang.select_customer"
        v-model="party_id"
        label="name">
        <template slot="option" slot-scope="option">
            <span class="fa" :class="option.icon"></span>
            {{ option.name }}
        </template>
    </v-select>
</template>

<script>
import VSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import 'vue-select/dist/vue-select';
import {mapState, mapActions, mapMutations} from "vuex";
export default {
    name: "Customer",
    components: {
        VSelect,
    },
    watch: {
        mokamCustomers: {
            handler: 'getMokamCustomer',
            deep: true,
        },
    },
    computed: {
        ...mapState(['mokamCustomers'])
    },
    data() {
        return {
            allCustomers: [],
            party_id: null,
            lang: this.$page.props.lang
        }
    },

    methods: {
        ...mapActions([
            'loadAllCustomers'
        ]),
        ...mapMutations([
            'mutationLoadAllCustomers'
        ]),
        getMokamCustomer() {
            this.allCustomers = this.mokamCustomers
        },
        async initialValues() {
            await this.loadAllCustomers()
        }
    },

    mounted() {
        this.initialValues()
    }
}
</script>

<style scoped>

</style>
