<template>
    <!-- payment type select start -->
    <div  class="col-6">
        <div class=" form-check form-check-inline">
            <input
                class="form-check-input"
                type="radio"
                name="pay_from"
                @change="changePaymentMethod"
                id="cash"
                v-model="payment_method"
                value="cash"
            />
            <label class="form-check-label" for="cash" >{{lang.cash}}</label>
        </div>
        <div class=" form-check form-check-inline">
            <input
                class="form-check-input"
                type="radio"
                name="pay_from"
                id="bank"
                @change="changePaymentMethod"
                v-model="payment_method"
                value="bank"
            />
            <label class="form-check-label" for="bank">
                {{ lang.bank }}
            </label>
        </div>
    </div>

    <div v-if="payment_method === 'cash'" class="row mb-2">
        <!-- party select start -->
        <div  class="col-12">
            <label for="cash_id" class="form-label required">{{ lang.cash }}</label>
            <select v-model="cash_id" id="cash_id" required class="form-select">
                <option :value="null">{{ lang.choose_cash }}</option>
                <option v-for="(cash, index) in cashes" :value="cash.id" :key="index">{{ cash.title }}</option>
            </select>
        </div>
    </div>

    <div v-if="cash_balance != null" class="row mb-3">
        <div  class="col-12">
            <!-- cash balance start -->
            <p class="d-block bg-dark text-light p-1 px-2">{{ cash_balance }}</p>
            <!-- cash balance end -->
        </div>
    </div>

    <div v-if="payment_method === 'bank'" class="row mb-2">
        <div  class="col-12">
            <label
                for="bank_account_id"
                class="form-label required">
                {{ lang.bank_account }}
            </label>
            <select required v-model="bank_account_id" id="bank_account_id" class="form-select">
                <option :value="null">{{ lang.choose_one }}</option>
                <option v-for="(bank_account, index) in bankAccounts" :value="bank_account.id" :key="index">
                    {{ bank_account.account_name + ' (' + bank_account.bank.name + ')'  }}
                </option>
            </select>
        </div>
    </div>

    <div v-if="bank_balance != null" class="row mb-3">

        <div  class="col-12">
            <!-- bank balance start -->
            <p class="d-block bg-dark text-light p-1 px-2">{{ bank_balance }}</p>
            <!-- bank balance end -->
        </div>
    </div>
</template>

<script>
export default {
    name: "CashBankComponent",
    props: {
        cashes: Object,
        bankAccounts: Object,
    },
    watch: {
        cash_id: function (value) {
            let cash = this.cashes.find(cash => cash.id == value)
            if (cash) {
                this.cash_balance = cash.balance
                this.bank_balance = null
            }
        },

        bank_account_id: {
            deep: true,
            handler: function (value) {
                let bank_account = this.bankAccounts.find(account => account.id == value)
                if (bank_account) {
                    this.bank_balance = bank_account.balance
                    this.cash_balance = null
                }
            },
        }
    },
    data() {
        return {
            lang: this.$page.props.lang,
            payment_method: 'cash',
            cash_id: null,
            cash_balance: null,
            bank_account_id: null,
            bank_balance: null,
        }
    },

    methods: {
        changePaymentMethod() {
            this.cash_id = null
            this.cash_balance = null
            this.bank_account_id = null
            this.bank_balance = null
        },
    },
}
</script>

<style scoped>

</style>
