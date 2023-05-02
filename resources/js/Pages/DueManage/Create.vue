<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container print-none">
                <ul class="nav nav-tabs mt-2">
                    <li class="nav-item">
                        <inertia-link v-if="type === 'customer'" class="nav-link" :href="route('customer-due-manage.index')">{{lang.all_records }}</inertia-link>
                        <inertia-link v-else class="nav-link" :href="route('supplier-due-manage.index')">{{ lang.all_records }}</inertia-link>
                    </li>

                    <li class="nav-item">
                        <inertia-link v-if="type === 'customer'" class="nav-link active" :href="route('customer-due-manage.create')">{{lang.new_entry}}</inertia-link>
                        <inertia-link v-else class="nav-link active" :href="route('supplier-due-manage.create')">{{lang.new_entry}}</inertia-link>
                    </li>
                </ul>
            </div>

            <div class="supplier container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card mb-5 border-0">
                        <div class="card-header p-0 border-0 d-flex">
                            <!-- page title -->
                            <div class="mt-3">
                                <h4 class="main-title">{{ lang.new_due_manage }}</h4>
                                <p><small>{{ lang.all }} <span style="color: red">*</span> {{ lang.mark_must_be_required }}</small></p>
                            </div>

                            <!-- header icon -->
                            <inertia-link v-if="type === 'customer'" :href="route('customer-due-manage.index')" class="btn top-icon-button print-none ms-auto" :title="lang.go_back">
                                <i class="bi bi-arrow-left"></i>
                            </inertia-link>

                            <inertia-link v-else :href="route('supplier-due-manage.index')" class="btn top-icon-button print-none ms-auto" :title="lang.go_to_list">
                                <i class="bi bi-arrow-left"></i>
                            </inertia-link>
                        </div>
                    </div>

                    <!-- content body -->
                    <div class="card-body p-0">
                        <form @submit.prevent="saveDueManage()" method="POST">
                            <div class="row mb-2">
                                <div class="col-2">
                                    <label for="showroom" class="form-label mt-1 required">{{lang.showroom}}</label>
                                </div>

                                <div class="col-6">
                                    <v-select
                                        id="showroom"
                                        :options="showrooms"
                                        :reduce="showroom => showroom.id"
                                        :placeholder="lang.select_showroom"
                                        v-model="form.showroom_id"
                                        class="input-height"
                                        label="name">
                                        <template slot="option" slot-scope="option">
                                            <span class="fa" :class="option.icon"></span>
                                            {{ option.name }}
                                        </template>
                                    </v-select>
                                    <div v-if="form.errors.showroom_id" class="text-danger">{{ form.errors.showroom_id }}</div>
                                </div>
                            </div>

                            <div v-if="type === 'customer'" class="row mb-3">
                                <div class="col-2">
                                    <label for="mokam" class="form-label mt-1">{{lang.mokam}}</label>
                                </div>

                                <div class="col-6">
                                    <v-select
                                        id="mokam"
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
                                    <div v-if="form.errors.mokam_id" class="text-danger">{{ form.errors.mokam_id }}</div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-2">
                                    <label
                                        for="party_id"
                                        class="form-label required">
                                        {{ type === 'customer' ? lang.customer : lang.supplier }}
                                    </label>
                                </div>

                                <!-- party select start -->
                                <div class="col-6">
                                    <select v-if="old_due_manage" v-model="form.party_id" class="form-select">
                                        <option v-for="(party, index) in allParties" :value="party.id" :key="index">
                                            {{ party.name }}
                                        </option>
                                    </select>
                                    <v-select
                                        v-else
                                        v-model="form.party_id"
                                        :options="allParties"
                                        id="party_id"
                                        required
                                        label="name"
                                        :clearable="false"
                                        :placeholder="lang.select_party"
                                        :reduce="(party) => party.id">
                                        <template
                                            v-slot:option="option"
                                        >
                                            {{ option.name }}
                                        </template>
                                    </v-select>
                                    <div
                                        v-if="form.errors.party_id"
                                        class="text-danger"
                                    >
                                        {{ form.errors.party_id }}
                                    </div>
                                </div>
                            </div>

                            <div v-if="party_balance != null" class="row mb-3">
                                <div class="col-2">
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <!-- party balance start -->
                                    <p class="d-block bg-dark text-light p-1 px-2">{{ party_balance + ' ' + party_balance_status }}</p>
                                    <!-- party balance end -->
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-2">
                                    <label
                                        for="date"
                                        class="form-label required"
                                    >
                                        {{lang.date}}
                                    </label>
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <input
                                        type="date"
                                        required
                                        class="form-control"
                                        v-model="form.date"
                                        id="date"
                                    />
                                    <div
                                        v-if="form.errors.date"
                                        class="text-danger"
                                    >
                                        {{ form.errors.date }}
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-2">
                                    <label for="payment" class="form-label required">{{ lang.amount }}</label>
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <div class="input-group">
                                        <input
                                            type="number"
                                            step="any"
                                            style="min-width: 80px"
                                            min="0"
                                            v-model="tempAmount"
                                            class="form-control"
                                            id="payment"
                                            :placeholder="lang.enter_amount+' (' +lang.bdt +')'"
                                            required
                                        >
                                        <div class="input-group-append">
                                            <select v-model="form.paymentType" class="form-select px-5 dropdown-toggle">
                                                <option class="dropdown-item" value="paid">দিতেছি</option>
                                                <option class="dropdown-item" value="received">নিতেছি</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-2">
                                    <label for="adjustment" class="form-label">{{ lang.adjustment }}</label>
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <div class="input-group">
                                        <input
                                            type="number"
                                            step="any"
                                            min="0"
                                            v-model="form.adjustment"
                                            class="form-control"
                                            id="adjustment"
                                            :placeholder="lang.enter_amount+' (' +lang.bdt +')'"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-2">
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <div class=" form-check form-check-inline">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            @change="changePaymentDetails"
                                            name="pay_from"
                                            id="cash"
                                            v-model="form.payment_method"
                                            value="cash"
                                        />
                                        <label class="form-check-label" for="cash" >{{lang.cash}}</label>
                                    </div>
                                    <div class=" form-check form-check-inline">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="pay_from"
                                            @change="changePaymentDetails"
                                            id="bank"
                                            v-model="form.payment_method"
                                            value="bank"
                                        />
                                        <label
                                            class="form-check-label"
                                            for="bank"
                                        >{{ lang.bank }}</label
                                        >
                                    </div>
                                </div>
                            </div>

                            <div v-if="form.payment_method === 'cash'" class="row mb-2">
                                <div class="col-2">
                                    <label for="cash_id" class="form-label required">{{ lang.cash }}</label>
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <select v-model="form.cash_id" id="cash_id" required class="form-select">
                                        <option :value="null">{{ lang.choose_cash }}</option>
                                        <option v-for="(cash, index) in cashes" :value="cash.id" :key="index">{{ cash.title }}</option>
                                    </select>
                                    <div
                                        v-if="form.errors.cash_id"
                                        class="text-danger"
                                    >
                                        {{ form.errors.cash_id }}
                                    </div>
                                </div>
                            </div>

                            <div v-if="cash_balance != null" class="row mb-3">
                                <div class="col-2">
                                </div>

                                <div  class="col-6">
                                    <!-- cash balance start -->
                                    <p class="d-block bg-dark text-light p-1 px-2">{{ cash_balance }}</p>
                                    <!-- cash balance end -->
                                </div>
                            </div>

                            <div v-if="form.payment_method === 'bank'" class="row mb-2">
                                <div class="col-2">
                                    <label
                                        for="bank_id"
                                        class="form-label required">
                                        {{ lang.bank }}
                                    </label>
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <select @change="getBankAccount" required v-model="bank_id" id="bank_id" class="form-select">
                                        <option :value="null">{{ lang.choose_one }}</option>
                                        <option v-for="(bank, index) in banks" :value="bank.id" :key="index">
                                            {{ bank.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="form.payment_method === 'bank'" class="row mb-2">
                                <div class="col-2">
                                    <label
                                        for="bank_account_id"
                                        class="form-label required">
                                        {{ lang.bank_account }}
                                    </label>
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <select required v-model="form.bank_account_id" id="bank_account_id" class="form-select">
                                        <option :value="null">{{ lang.choose_one }}</option>
                                        <option v-for="(bank_account, index) in bank_accounts" :value="bank_account.id" :key="index">
                                            {{ bank_account.account_name }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="form.errors.bank_account_id"
                                        class="text-danger"
                                    >
                                        {{ form.errors.bank_account_id }}
                                    </div>
                                </div>
                            </div>

                            <div v-if="bank_balance != null" class="row mb-3">
                                <div class="col-2">
                                </div>

                                <div  class="col-6">
                                    <!-- bank balance start -->
                                    <p class="d-block bg-dark text-light p-1 px-2">{{ bank_balance }}</p>
                                    <!-- bank balance end -->
                                </div>
                            </div>

                            <div v-if="form.payment_method === 'bank'" class="row mb-3">
                                <div class="col-2">
                                    <label
                                        for="check_number"
                                        class="form-label"
                                    >{{ lang.check_number }}</label
                                    >
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="form.check_number"
                                        id="check_number"
                                    />
                                    <div
                                        v-if="form.errors.check_number"
                                        class="text-danger"
                                    >
                                        {{ form.errors.check_number }}
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-2">
                                    <label for="note" class="form-label">{{lang.note}}</label>
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <textarea v-model="form.note" id="note" cols="30" class="form-control" rows="5"></textarea>
                                </div>
                            </div>

                            <!-- Send SMS Start -->
                            <div class="row">
                                <div class="col-2"></div>
                                <div v-if="type === 'customer'" class="col-6">
                                    <div class="form-check form-check-inline">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            v-model="form.sms"
                                            id="sms">
                                        <label class="form-check-label" for="sms">{{lang.send_sms}}</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Send SMS End -->

                            <div class="row mb-3">
                                <div class="col-2">
                                    <label class="form-label mt-1">&nbsp;</label>
                                </div>

                                <div class="col-10">
                                    <button type="reset" class="btn btn-warning me-2">
                                        <i class="bi bi-dash-square"></i>
                                        <span class="p-1">{{ lang.reset }}</span>
                                    </button>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-plus-square"></i>
                                        <span class="p-1">{{ lang.save }}</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import "vue-select/dist/vue-select.css";
import vSelect from "vue-select";
import alert from "@/alert";
export default {
    name: "Create",
    components: {
        BreezeAuthenticatedLayout,
        vSelect,
    },
    watch: {
        mokamId: {
            handler: 'getMokamCustomer',
            deep: true,
        },
        'form.party_id': function (value) {
            let party = this.parties.find(party => party.id == parseInt(value))
            this.party_balance = Math.abs(party.balance)
            this.party_balance_status = party.balance >= 0 ? 'Receivable' : 'Payable'
        },

        'form.cash_id': function (value) {
            let cash = this.cashes.find(cash => cash.id == value)
            if (cash) {
                this.cash_balance = cash.balance
                this.bank_balance = null
            }
        },
        'form.bank_account_id': function (value) {
            let bank_account = this.bank_accounts.find(account => account.id == value)
            if (bank_account) {
                this.bank_balance = bank_account.balance
                this.cash_balance = null
            }
        },
    },
    props: {
        parties: Array,
        mokams: Array,
        cashes: Array,
        banks: Array,
        showrooms: Array,
        old_due_manage: Object,
        type: String,
    },
    data() {
        return {
            allParties: [],
            lang: this.$page.props.lang,
            party_balance: null,
            bank_accounts: [],
            bank_id: null,
            cash_balance: null,
            bank_balance: null,
            party_balance_status: null,
            tempAmount: null,
            mokamId: null,
            form: this.$inertia.form({
                date: new Date().toISOString().substr(0, 10),
                party_id: null,
                showroom_id: null,
                payment_method: 'cash',
                paymentType: 'received',
                amount: null,
                sms: false,
                adjustment: 0,
                type: null,
                bank_account_id: null,
                check_number: null,
                cash_id: null,
                note: null,
            }),
        }
    },

    methods: {
        getMokamCustomer() {
            if(this.mokamId) {
                this.allParties = this.parties.filter(party => party.mokam_id == this.mokamId)
            }else{
                this.allParties = this.parties
            }
        },
        changePaymentDetails() {
            this.form.cash_id = null
            this.cash_balance = null
            this.form.bank_account_id = null
            this.bank_balance = null
        },
        getBankAccount() {
            this.bank_accounts = this.banks.find(bank => bank.id == this.bank_id).bank_accounts
            this.form.cash_id = null
            this.cash_balance = null
            this.form.bank_account_id = null
            this.bank_balance = null
        },
        saveDueManage() {
            if (!this.form.cash_id && !this.form.bank_account_id) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select cash or bank!'
                })
                return
            }

            if (!this.form.party_id) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select party!'
                })
                return
            }

            if (this.form.paymentType === 'received') {
                this.form.amount = this.tempAmount;
            } else {
                this.form.amount = -1 * this.tempAmount;
            }
            if (this.type === 'customer') {
                if(this.old_due_manage) {
                    this.form.put(this.route('customer-due-manage.update', this.old_due_manage.id))
                }else{
                    this.form.post(this.route('customer-due-manage.store'))
                }
            }else{
                if(this.old_due_manage) {
                    this.form.put(this.route('supplier-due-manage.update', this.old_due_manage.id))
                }else{
                    this.form.post(this.route('supplier-due-manage.store'))
                }
            }
        },

        oldDueManageData() {
            this.form.showroom_id = this.old_due_manage.party_id
            this.form.party_id = this.old_due_manage.showroom_id
            this.form.date = this.old_due_manage.formatted_date
            this.form.adjustment = this.old_due_manage.adjustment
            if (this.old_due_manage.amount > 0) {
                this.form.paymentType = 'received'
            }else{
                this.form.paymentType = 'paid'
            }
            if (this.old_due_manage.cash_id) {
                this.form.payment_method = 'cash'
                this.form.cash_id = this.old_due_manage.cash_id
            }else{
                this.form.payment_method = 'bank'
                this.bank_id = this.old_due_manage.bank_account.bank_id
                this.getBankAccount()
                this.form.bank_account_id = this.old_due_manage.bank_account_id
                this.form.check_number = this.old_due_manage.check_number
            }
            this.tempAmount = Math.abs(this.old_due_manage.amount)
        },

    },

    mounted() {
        this.form.type = this.type
        this.allParties = this.parties
        if (this.old_due_manage) {
            this.oldDueManageData()
        }else{
            if (this.type === 'supplier') {
                this.form.paymentType = 'paid'
            }
        }
    }
}
</script>

<style scoped>

</style>

