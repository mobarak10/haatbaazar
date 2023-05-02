<template>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="row">
                <label class="col-sm-2 col-form-label">{{ lang.supplier }}</label>
                <div class="col-sm-10">
                    <select v-if="!vSelect" v-model="supplierId" class="form-select">
                        <option
                            v-for="(supplier, supplierIndex) in suppliers"
                            :value="supplier.id"
                            :key="supplierIndex"
                        >
                            {{ supplier.name }}
                        </option>
                    </select>

                    <v-select
                        v-else
                        :options="suppliers"
                        :reduce="supplier => supplier.id"
                        placeholder="Select Client"
                        :clearable="false"
                        required
                        v-model="supplierId"
                        class="input-height"
                        label="name">
                        <template slot="option" slot-scope="option">
                            <span class="fa" :class="option.icon"></span>
                            {{ option.name }}
                        </template>
                    </v-select>
                </div>
            </div>

            <div class="row mt-3">
                <label for="model" class="col-sm-2 col-form-label">{{ lang.mobile }}</label>
                <div class="col-sm-10">
                    <input type="text" v-model="supplierMobile" disabled class="form-control" id="model">
                </div>
            </div>
            <div class="row mt-3">
                <label for="address" class="col-sm-2 col-form-label">{{ lang.address }}</label>
                <div class="col-sm-10">
                    <textarea id="address" v-model="supplierAddress" disabled rows="3" class="form-control"></textarea>
                </div>
            </div>

            <div class="row mt-3">
                <label for="note" class="col-sm-2 col-form-label">{{ lang.note }}</label>
                <div class="col-sm-10">
                    <textarea
                        id="note"
                        v-model="note"
                        rows="3"
                        :placeholder="from === 'purchase'
                        ?
                        lang.write_something_about_this_purchase
                        :
                        lang.write_something_about_this_purchase_return"
                        class="form-control">
                    </textarea>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import VSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import 'vue-select/dist/vue-select'
import {mapState, mapActions, mapMutations} from "vuex";

export default {
    name: "SupplierComponent",
    props: {
        from: String,
        oldPurchaseReturn: Object,
    },
    components: {
        VSelect,
    },
    watch: {
        supplierId: {
            handler: 'getSupplierDetails',
            deep: true
        }
    },
    computed: {
        ...mapState(['suppliers'])
    },
    data() {
        return {
            vSelect: true,
            lang: this.$page.props.lang,
            customerType: 'oldCustomer',
            supplierId: null,
            customerName: null,
            supplierMobile: null,
            supplierAddress: null,
            note: null,
        }
    },

    methods: {
        ...mapActions([
            'loadAllSuppliers'
        ]),
        ...mapMutations(['mutationLoadSupplierBalance']),

        changeCustomerType() {
            this.supplierId = null;
            this.getSupplierDetails()
        },

        getSupplierDetails() {
            if (this.supplierId) {
                let supplier = this.suppliers.find(supplier => supplier.id === parseInt(this.supplierId))
                let balance = 0
                if (this.oldPurchaseReturn) {
                    balance = this.oldPurchaseReturn.previous_balance
                }else{
                    balance = supplier.balance
                }
                this.mutationLoadSupplierBalance(balance)
                this.supplierMobile = supplier.phone
                this.supplierAddress = supplier.address
            }else{
                this.supplierBalance = null
                this.mutationLoadSupplierBalance(0)
                this.supplierMobile = null
                this.supplierAddress = null
            }
        },

        async initialValues() {
            await this.loadAllSuppliers()
            if (this.oldPurchaseReturn) {
                this.vSelect = false
                this.supplierId = this.oldPurchaseReturn.party_id
                this.note = this.oldPurchaseReturn.note
                this.getSupplierDetails()
            }
            else if (this.oldDamageReturn) {
                this.vSelect = false
                this.supplierId = this.oldDamageReturn.party_id
                this.note = this.oldDamageReturn.note
                this.getSupplierDetails()
            }
        },
    },

    mounted() {
        this.initialValues()
    }
}
</script>

<style scoped>

</style>
