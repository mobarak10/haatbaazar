<template>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="row">
                <div class="col-sm-2"></div>
                <div v-if="from === 'sale'" class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            type="radio"
                            v-model="customerType"
                            @change="changeCustomerType"
                            value="oldCustomer"
                            id="oldCustomer">
                        <label class="form-check-label" for="oldCustomer">{{lang.old_customer}}</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            type="radio"
                            @change="changeCustomerType"
                            v-model="customerType"
                            id="newCustomer"
                            value="newCustomer">
                        <label class="form-check-label" for="newCustomer">{{lang.new_customer}}</label>
                    </div>
                </div>
            </div>
            <div v-if="customerType === 'oldCustomer'">
                <div class="row">
                    <label class="col-sm-2 col-form-label">{{lang.mokam}}</label>
                    <div class="col-sm-10">
                        <v-select
                            :options="mokams"
                            :reduce="mokam => mokam.id"
                            :placeholder="lang.select_mokam"
                            v-model="mokamId"
                            class="input-height"
                            label="name">
                            <template slot="option" slot-scope="option">
                                <span class="fa" :class="option.icon"></span>
                                {{ option.name }}
                            </template>
                        </v-select>
                    </div>
                </div>

                <div class="row">
                    <label class="col-sm-2 col-form-label">{{lang.customer}}</label>
                    <div v-if="sale" class="col-sm-10">
                        <select v-model="customerId" class="form-select" required>
                            <option v-for="(customer, index) in allCustomers" :value="customer.id" :key="index">
                                {{ customer.name}}
                            </option>
                        </select>
                    </div>
                    <div v-else class="col-sm-10">
                        <v-select
                            :options="allCustomers"
                            :reduce="customer => customer.id"
                            :placeholder="lang.select_client"
                            :clearable="false"
                            required
                            v-model="customerId"
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
                    <label for="model" class="col-sm-2 col-form-label">{{lang.mobile}}</label>
                    <div class="col-sm-10">
                        <input type="text" v-model="customerMobile" disabled class="form-control" id="model">
                    </div>
                </div>
                <div class="row mt-3">
                    <label for="address" class="col-sm-2 col-form-label">{{lang.address}}</label>
                    <div class="col-sm-10">
                        <textarea id="address" v-model="customerAddress" disabled rows="3" class="form-control"></textarea>
                    </div>
                </div>
            </div>

            <div v-else>
                <div class="row">
                    <div class="col-sm-2">
                        <label class="required form-label">{{ lang.customer }}</label>
                    </div>
                    <div class="col-sm-10">
                        <input type="text" v-model="customer.name" required :placeholder="lang.enter_customer_name" class="form-control">
                    </div>
                </div>

                <div class="row mt-3">
                    <label for="model" class="col-sm-2 col-form-label">{{lang.mobile}}</label>
                    <div class="col-sm-10">
                        <input type="text" v-model="customer.phone"  class="form-control">
                    </div>
                </div>
                <div class="row mt-3">
                    <label for="new-address" class="col-sm-2 col-form-label">{{lang.address}}</label>
                    <div class="col-sm-10">
                        <textarea id="new-address" v-model="customer.address" rows="3" class="form-control"></textarea>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <label for="note" class="col-sm-2 col-form-label">{{lang.note}}</label>
                <div class="col-sm-10">
                    <textarea
                        id="note"
                        v-model="note"
                        rows="3"
                        :placeholder="from === 'sale'
                        ?
                        lang.write_something_about_this_sales
                        :
                        lang.write_something_about_this_sale_return"
                        class="form-control">
                    </textarea>
                </div>
            </div>

            <!-- Send SMS Start -->
            <div class="row">
                <div class="col-sm-2"></div>
                <div v-if="from === 'sale'" class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            v-model="sms"
                            id="sms">
                        <label class="form-check-label" for="sms">{{lang.send_sms}}</label>
                    </div>
                </div>
            </div>
            <!-- Send SMS End -->

        </div>
    </div>
</template>

<script>
import VSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import 'vue-select/dist/vue-select'
import {mapState, mapActions, mapMutations} from "vuex";

export default {
    name: "CustomerComponent",
    props: {
        from: String,
        sale: Object,
        sale_return: Object,
    },
    components: {
        VSelect,
    },
    watch: {
        customerId: {
            handler: 'getCustomerDetails',
            deep: true,
        },
        mokamId: {
            handler: 'getMokamCustomer',
            deep: true,
        },
    },
    computed: {
        ...mapState(['customers', 'mokams'])
    },
    data() {
        return {
            allCustomers: [],
            lang: this.$page.props.lang,
            customerType: 'oldCustomer',
            customerId: null,
            sms: false,
            mokamId: null,
            customerName: null,
            customerMobile: null,
            customerAddress: null,
            customer: {
                name: null,
                phone: null,
                address: null,
            },
            note: null,
        }
    },

    methods: {
        ...mapActions([
            'loadAllCustomers',
            'loadAllMokams'
        ]),
        ...mapMutations([
            'mutationLoadCustomerBalance',
            'mutationLoadAllMokams'
        ]),

        changeCustomerType() {
            this.customerId = null;
            this.getCustomerDetails()
        },

        getMokamCustomer() {
            if (this.mokamId) {
                this.allCustomers = this.customers.filter(customer => customer.mokam_id == this.mokamId)
            }else{
                this.allCustomers = this.customers
            }
        },

        getCustomerDetails() {
            if (this.customerId) {
                let customer = this.customers.find(customer => customer.id === this.customerId)
                let balance = 0
                if (this.sale) {
                    if (this.sale.party_id == this.customerId){
                        balance = this.sale.previous_balance
                    }else{
                        balance = customer.balance
                        this.mutationLoadCustomerBalance(balance)
                    }
                }else if (this.sale_return) {
                    if (this.sale_return.party_id == this.customerId){
                        balance = this.sale_return.previous_balance
                    }else{
                        balance = customer.balance
                        this.mutationLoadCustomerBalance(balance)
                    }
                }else{
                    balance = customer.balance
                }
                this.mutationLoadCustomerBalance(balance)
                this.customerMobile = customer.phone || ''
                this.customerAddress = customer.address || ''
            }else{
                this.customerBalance = null
                this.mutationLoadCustomerBalance(0)
                this.customerMobile = null
                this.customerAddress = null
            }
        },

        async initialValues() {
            await this.loadAllMokams()
            await this.loadAllCustomers()
            this.allCustomers = this.customers
            if (this.sale) {
                this.customerId = this.sale.party_id
                this.getCustomerDetails()
            }
            if (this.sale_return) {
                this.customerId = this.sale_return.party_id
                this.getCustomerDetails()
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
