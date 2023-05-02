<template>
    <div class="col-md-5">
        <div class="row">
            <label for="total_price" class="col-sm-2 col-form-label" >{{lang.total_price}}</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <input
                        type="number"
                        v-model="subtotal"
                        disabled
                        step="any"
                        class="form-control"
                        id="total_price">
                    <div class="input-group-prepend">
                        <div class="input-group-text">৳</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-2 row">
            <label for="discount" class="col-sm-2 col-form-label">{{ from === 'sale' ?  lang.discount  : lang.adjustment }}</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <input
                        type="number"
                        step="any"
                        v-model="discount"
                        class="form-control"
                        id="discount">
                    <div class="input-group-prepend">
                        <div class="input-group-text">৳</div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="from === 'sale'">
            <div class="mt-2 row">
                <label for="labour_cost" class="col-sm-2 col-form-label">{{ lang.labour_cost }}</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input
                            type="number"
                            step="any"
                            v-model="labourCost"
                            class="form-control"
                            id="labour_cost">
                        <div class="input-group-prepend">
                            <div class="input-group-text">৳</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-2 row">
                <label for="transport_cost" class="col-sm-2 col-form-label">{{ lang.transport_cost }}</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input
                            type="number"
                            step="any"
                            v-model="transportCost"
                            class="form-control"
                            id="transport_cost">
                        <div class="input-group-prepend">
                            <div class="input-group-text">৳</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-2 row">
            <label for="previous_balance" class="col-sm-2 col-form-label">{{lang.previous_balance}}</label>
            <div class="col-sm-6">
                <input type="number" v-model="customerBalance" disabled class="form-control" id="previous_balance">
            </div>
            <div class="col-sm-4">
                <input
                    type="text"
                    step="any"
                    disabled
                    :value="customerBalance >= 0 ? lang.receivable : lang.payable"
                    class="form-control">
            </div>
        </div>

        <div class="mt-2 row">
            <label for="grand_total" class="col-sm-2 col-form-label">{{ lang.grand_total }}</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <input
                        disabled
                        step="any"
                        type="number"
                        :value="
                        Number.parseFloat(
                            grandTotal = (parseFloat(subtotal || 0)
                            + parseFloat(party_balance || 0)
                            - parseFloat(discount || 0)
                            + parseFloat(labourCost || 0)
                            + parseFloat(transportCost || 0))
                        ).toFixed(2)"
                        class="form-control"
                        id="grand_total">
                    <div class="input-group-prepend">
                        <div class="input-group-text">৳</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-2 row">
            <label for="payment" class="col-sm-2 col-form-label">{{ lang.paid }}</label>
            <div class="col-sm-6">
                <div class="input-group">
                    <input
                        type="number"
                        step="any"
                        v-model="paid"
                        min="0"
                        class="form-control"
                        id="payment">
                    <div class="input-group-prepend">
                        <div class="input-group-text">৳</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <select v-model="payment_type" class="form-select">
                    <option value="cash">Cash</option>
                    <option value="bank">Bank</option>
                </select>
            </div>
        </div>

        <div v-if="payment_type === 'cash'" class="mt-2 row">
            <label class="col-sm-2 col-form-label" >{{ lang.cash_name }}</label>
            <div class="col-sm-10">
                <select class="form-select" v-model="cashId">
                    <option :value="null">{{ lang.choose_cash }}</option>
                    <option v-for="(cash, index) in cashes" :key="index" :value="cash.id">{{ cash.title }}</option>
                </select>
            </div>
        </div>

        <div v-if="payment_type === 'bank'">
            <div class="mt-2 row">
                <label for="total_price" class="col-sm-2 col-form-label" >{{ lang.bank_name }}</label>
                <div class="col-sm-10">
                    <select class="form-select" v-model="bankAccountId">
                        <option :value="null">{{ lang.choose_bank }}</option>
                        <option v-for="(account, index) in bankAccounts" :key="index" :value="account.id">{{ account.bank.name + ' (' + account.account_name + ')' }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-2 row">
            <label for="due" class="col-sm-2 col-form-label">{{ dueOrChange >= 0 ? lang.due : 'Party Balance' }}</label>
            <div class="col-sm-10">
                <div class="mb-2 input-group">
                    <input
                        type="number"
                        disabled
                        class="form-control"
                        step="any"
                        :value="
                        Number.parseFloat(Math.abs(dueOrChange)).toFixed(2)"
                        id="due">
                    <div class="input-group-prepend">
                        <div class="input-group-text">৳</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-2 text-end">
            <button type="submit" class="ml-auto mr-3 btn text-end btn-primary">{{ from === 'sale' ? lang.sale : lang.sale_return }}</button>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
export default {
    name: "PaymentComponent",
    props: {
        from: String,
        sale: Object,
        sale_return: Object,
    },
    computed: {
        ...mapState(["subtotal", "customerBalance", 'supplierBalance', 'cashes', 'bankAccounts']),
        dueOrChange() {
            return parseFloat(this.grandTotal || 0) - parseFloat(this.paid || 0)
        },
    },
    watch: {
        cashes: {
            deep: true,
            handler: function(value) {
                if (!this.sale) {
                    if (value.length > 0) {
                        this.cashId = value[0].id
                    }
                }
            }
        },

        customerBalance: {
            deep: true,
            handler: function (value) {
                if (this.from === 'sale') {
                    this.party_balance = value;
                }else{
                    this.party_balance = -1 * value;
                }
            }
        }
    },
    data() {
        return {
            lang: this.$page.props.lang,
            party_balance: 0,
            payment_type: 'cash',
            cashId: null,
            bankAccountId: null,
            discount: null,
            labourCost: null,
            transportCost: null,
            paid: null,
            grandTotal: 0,
        }
    },

    methods: {
        ...mapActions(['loadAllCashes', 'loadAllBankAccounts']),
        ...mapMutations(['mutationLoadSubtotal']),

        initialValues() {
            if (this.sale) {
                this.discount = this.sale.discount
                this.labourCost = this.sale.labour_cost
                this.transportCost = this.sale.transport_cost
                this.paid = this.sale.paid
                if (this.sale.payment_type == 'cash') {
                    this.cashId = this.sale.cash_id
                }else{
                    this.payment_type = 'bank'
                    this.bankAccountId = this.sale.bank_account_id
                }
            }
            if (this.sale_return) {
                this.discount = this.sale_return.discount
                this.labourCost = 0.00
                this.transportCost = 0.00
                this.paid = this.sale_return.paid
                if (this.sale_return.cash_id) {
                    this.cashId = this.sale_return.cash_id
                }else{
                    this.payment_type = 'bank'
                    this.bankAccountId = this.sale_return.bank_account_id
                }
            }
        },
    },
    mounted() {
        this.mutationLoadSubtotal(0)
        this.loadAllCashes()
        this.loadAllBankAccounts()
        this.initialValues()
    }
}
</script>

<style scoped>

</style>
