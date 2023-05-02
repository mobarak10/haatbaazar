<template>
    <div class="col-md-5">
        <div :class="permissions.includes('product-purchase_price') ? '' : 'd-none'">
            <div class="row">
                <label for="total_price" class="col-sm-2 col-form-label" >{{ lang.total_price }}</label>
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

            <div class="row mt-2">
                <label for="previous_balance" class="col-sm-2 col-form-label">{{ lang.previous_balance }}</label>
                <div class="col-sm-6">
                    <input type="number" v-model="supplierBalance" disabled class="form-control" id="previous_balance">
                </div>
                <div class="col-sm-4">
                    <input
                        type="text"
                        step="any"
                        disabled
                        :value="supplierBalance >= 0 ? 'Receivable' : 'Payable'"
                        class="form-control">
                </div>
            </div>

            <div class="row mt-2">
                <label for="discount" class="col-sm-2 col-form-label">{{ from === 'sale' ? lang.discount : lang.adjustment }}</label>
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

            <div v-if="from === 'purchase'">
                <div class="row mt-2">
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

                <div class="row mt-2">
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

            <div class="row mt-2">
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
                    )"
                            class="form-control"
                            id="grand_total">
                        <div class="input-group-prepend">
                            <div class="input-group-text">৳</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
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
                        <option value="cash">{{ lang.cash }}</option>
                        <option value="bank">{{ lang.bank }}</option>
                    </select>
                </div>
            </div>

            <div v-if="payment_type === 'cash'" class="row mt-2">
                <label class="col-sm-2 col-form-label" >{{lang.cash_name}}</label>
                <div class="col-sm-10">
                    <select class="form-select" v-model="cashId">
                        <option :value="null">{{ lang.choose_cash }}</option>
                        <option v-for="(cash, index) in cashes" :key="index" :value="cash.id">{{ cash.title }}</option>
                    </select>
                </div>
            </div>

            <div v-if="payment_type === 'bank'">
                <div class="row mt-2">
                    <label for="total_price" class="col-sm-2 col-form-label" >{{ lang.bank_name }}</label>
                    <div class="col-sm-10">
                        <select class="form-select" v-model="bankAccountId">
                            <option :value="null">{{ lang.choose_bank }}</option>
                            <option v-for="(account, index) in bankAccounts" :key="index" :value="account.id">{{ account.bank.name + ' (' + account.account_name + ')' }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <label for="due" class="col-sm-2 col-form-label">{{ dueOrChange >= 0 ? lang.due : lang.change }}</label>
                <div class="col-sm-10">
                    <div class="input-group mb-2">
                        <input
                            type="number"
                            disabled
                            class="form-control"
                            step="any"
                            :value="
                        Number.parseFloat(
                            dueOrChange = parseFloat(grandTotal || 0) - parseFloat(paid || 0)
                            )"
                            id="due">
                        <div class="input-group-prepend">
                            <div class="input-group-text">৳</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end mt-2">
            <button type="submit" class="btn text-end btn-primary ml-auto mr-3">{{ from === 'purchase' ? lang.purchase : lang.purchase_return }}</button>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
export default {
    name: "PaymentComponent",
    props: {
        from: String,
        oldPurchaseReturn: Object,
    },
    computed: {
        ...mapState(["subtotal", "supplierBalance", 'cashes', 'bankAccounts'])
    },
    watch: {
        cashes: {
            deep: true,
            handler: function(value) {
                if (value.length > 0) {
                    this.cashId = value[0].id
                }
            }
        },

        supplierBalance: {
            deep: true,
            handler: function (value) {
                if (this.from === 'purchase') {
                    this.party_balance = -1 * value;
                }else{
                    this.party_balance = value;
                }
            }
        }
    },
    data() {
        return {
            lang: this.$page.props.lang,
            permissions: this.$page.props.permissionNames,
            party_balance: 0,
            payment_type: 'cash',
            cashId: null,
            bankAccountId: null,
            discount: null,
            labourCost: null,
            transportCost: null,
            paid: null,
            grandTotal: 0,
            dueOrChange: 0,
        }
    },

    methods: {
        ...mapActions(['loadAllCashes', 'loadAllBankAccounts']),
        initialValues() {
            if (this.oldPurchaseReturn) {
                this.discount = this.oldPurchaseReturn.discount
                this.paid = this.oldPurchaseReturn.paid
                if (this.oldPurchaseReturn.paid_from === 'cash') {
                    this.cashId = this.oldPurchaseReturn.cash_id
                }else{
                    this.payment_type = 'bank'
                    this.bankAccountId = this.oldPurchaseReturn.bank_account_id
                }
            }
            else if (this.oldDamageReturn) {
                this.discount = this.oldDamageReturn.discount
                this.paid = this.oldDamageReturn.paid
                if (this.oldDamageReturn.paid_to === 'cash') {
                    this.cashId = this.oldDamageReturn.cash_id
                }else{
                    this.payment_type = 'bank'
                    this.bankAccountId = this.oldDamageReturn.bank_account_id
                }
            }
        },
    },
    mounted() {
        this.loadAllCashes()
        this.loadAllBankAccounts()
        this.initialValues()
    }
}
</script>

<style scoped>

</style>
