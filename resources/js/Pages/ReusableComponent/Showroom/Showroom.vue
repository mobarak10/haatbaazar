<template>
    <label for="showroom" class="form-label">{{ lang.showroom }}</label>
    <v-select
        id="showroom"
        :options="showrooms"
        :reduce="showroom => showroom.id"
        :placeholder="lang.select_showroom"
        v-model="showroom_id"
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
    name: "Showroom",
    components: {
        VSelect,
    },
    computed: {
        ...mapState(['showrooms'])
    },
    data() {
        return {
            lang: this.$page.props.lang,
            showroom_id: null,
        }
    },
    methods: {
        ...mapActions(['loadAllShowrooms']),
        ...mapMutations(['mutationLoadAllShowrooms']),

        async initialValues() {
            await this.loadAllShowrooms()
        }
    },

    mounted() {
        this.initialValues()
    }
}
</script>

<style scoped>

</style>
