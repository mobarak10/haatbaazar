<template>
    <label for="mokam" class="form-label" :class="labelClass">{{ lang.mokam }}</label>
    <v-select
        :class="selectClass"
        id="mokam"
        :options="mokams"
        :reduce="mokam => mokam.id"
        :placeholder="lang.select_mokam"
        v-model="mokamId"
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
import {mapState, mapActions, mapMutations, mapGetters} from "vuex";
export default {
    name: "Mokam",
    props: {
        labelClass: String,
        selectClass: String,
    },
    watch: {
        mokamId: {
            handler: 'getMokamCustomer',
            deep: true,
        }
    },
    components: {
        VSelect,
    },
    computed: {
        ...mapState(['mokams'])
    },
    data() {
        return {
            mokamId: null,
            lang: this.$page.props.lang
        }
    },

    methods: {
        ...mapActions([
            'loadAllMokams',
        ]),
        ...mapMutations([
            'mutationLoadAllMokams',
            "mutationLoadMokamCustomers"
        ]),
        getMokamCustomer() {
            this.mutationLoadMokamCustomers(this.mokamId)
        },
        async initialValues() {
            await this.loadAllMokams()
        }
    },

    mounted() {
        this.initialValues()
    }
}
</script>

<style scoped>

</style>
