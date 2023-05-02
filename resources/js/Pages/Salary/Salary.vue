<template>
    <div>
        <breeze-authenticated-layout>
            <!-- container menu -->
            <top-bar :index-route="route('salary.index')" :create-route="route('salary.create')" />
            <!-- container menu end -->
            <div class="supplier container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card mb-5 border-0">
                        <div class="card-header p-0 border-0 d-flex">
                            <!-- page title -->
                            <div class="mt-3">
                                <h4 class="main-title">{{ lang.new }} {{ lang.salary }}</h4>
                                <p><small>{{ lang.all }} <span style="color: red">*</span> {{ lang.mark_must_be_required }}</small></p>
                            </div>

                            <!-- header icon -->
                            <inertia-link :href="route('salary.index')" class="btn top-icon-button print-none ms-auto" :title="lang.go_back">
                                <i class="bi bi-arrow-left"></i>
                            </inertia-link>
                        </div>
                    </div>

                    <!-- content body -->
                    <div class="card-body p-0">
                        <form @submit.prevent="saveSalary()" method="POST">
                            <div class="row mb-2">
                                <div class="col-2">
                                    <label
                                        for="showroom"
                                        class="form-label required">
                                        {{ lang.showroom }}
                                    </label>
                                </div>

                                <!-- party select start -->
                                <div class="col-6">
                                    <v-select
                                        v-model="form.showroom_id"
                                        :options="showrooms"
                                        id="showroom"
                                        required
                                        label="name"
                                        :clearable="false"
                                        :placeholder="lang.choose_one"
                                        :reduce="(showroom) => showroom.id">
                                        <template
                                            v-slot:option="option"
                                        >
                                            {{ option.name }}
                                        </template>
                                    </v-select>
                                    <div
                                        v-if="form.errors.showroom_id"
                                        class="text-danger"
                                    >
                                        {{ form.errors.showroom_id }}
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-2">
                                    <label
                                        for="user_id"
                                        class="form-label required">
                                        {{ lang.user }}
                                    </label>
                                </div>

                                <!-- party select start -->
                                <div class="col-6">
                                    <select v-if="oldSalary" v-model="form.user_id" class="form-select">
                                        <option v-for="(user, index) in users" :value="user.id" :key="index">
                                            {{ user.name }}
                                        </option>
                                    </select>
                                    <v-select
                                        v-else
                                        v-model="form.user_id"
                                        :options="users"
                                        id="user_id"
                                        required
                                        label="name"
                                        :clearable="false"
                                        :placeholder="lang.choose_one"
                                        :reduce="(user) => user.id">
                                        <template
                                            v-slot:option="option"
                                        >
                                            {{ option.name }}
                                        </template>
                                    </v-select>
                                    <div
                                        v-if="form.errors.user_id"
                                        class="text-danger"
                                    >
                                        {{ form.errors.user_id }}
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-2">
                                    <label
                                        for="salary_month"
                                        class="form-label required"
                                    >
                                        {{lang.salary_month}}
                                    </label>
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <input
                                        type="month"
                                        required
                                        class="form-control"
                                        v-model="tempSalaryMonth"
                                        id="salary_month"
                                    />
                                    <div
                                        v-if="form.errors.salary_month"
                                        class="text-danger"
                                    >
                                        {{ form.errors.salary_month }}
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-2">
                                    <label
                                        for="given_date"
                                        class="form-label required"
                                    >
                                        {{lang.given_date}}
                                    </label>
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <input
                                        type="date"
                                        required
                                        class="form-control"
                                        v-model="form.given_date"
                                        id="given_date"
                                    />
                                    <div
                                        v-if="form.errors.given_date"
                                        class="text-danger"
                                    >
                                        {{ form.errors.given_date }}
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-2">
                                    <label for="payment" class="form-label required">{{ lang.basic_salary }}</label>
                                </div>

                                <!-- amount start -->
                                <div  class="col-6">
                                    <input
                                        type="number"
                                        step="any"
                                        min="0"
                                        v-model="form.basic_salary"
                                        class="form-control"
                                        id="payment"
                                        :placeholder="lang.enter_amount+' (' +lang.bdt +')'"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-2">
                                    <label for="bonus" class="form-label">{{ lang.bonus }}</label>
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <div class="input-group">
                                        <input
                                            type="number"
                                            step="any"
                                            min="0"
                                            v-model="form.bonus"
                                            class="form-control"
                                            id="bonus"
                                            :placeholder="lang.enter_amount+' (' +lang.bdt +')'"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-2">
                                    <label for="advanced" class="form-label">{{ lang.advanced }}</label>
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <div class="input-group">
                                        <input
                                            type="number"
                                            step="any"
                                            min="0"
                                            v-model="tempAdvanced"
                                            class="form-control"
                                            id="advanced"
                                            :placeholder="lang.enter_amount+' (' +lang.bdt +')'"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-2">
                                    <label for="deduction" class="form-label">{{ lang.deduction }}</label>
                                </div>

                                <!-- party select start -->
                                <div  class="col-6">
                                    <div class="input-group">
                                        <input
                                            type="number"
                                            step="any"
                                            min="0"
                                            v-model="tempDeduction"
                                            class="form-control"
                                            id="deduction"
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
                                <div class="col-6">
                                    <ckeditor :editor="editor" id="note" v-model="form.note" :config="editorConfig"></ckeditor>
                                </div>
                            </div>

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
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import {monthFormat} from "@/Helpers/formatter";
export default {
    name: "Salary",
    components: {
        BreezeAuthenticatedLayout,
        vSelect,
        TopBar,
    },
    watch: {
        'form.cash_id': function (value) {
            let cash = this.cashes.find(cash => cash.id == value)
            if (cash) {
                this.cash_balance = cash.balance
                this.bank_balance = null
            }
        },

        'form.user_id' : function (value) {
            if (!this.checkOldDetails) {
                let user = this.users.find(user => user.id == value)
                if (user) {
                    this.form.basic_salary = user.salary
                    this.tempAdvanced = (Math.abs(user.total_advanced_paid) - user.total_advanced_receive)
                }
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
        showrooms: Array,
        users: Array,
        cashes: Array,
        banks: Array,
        oldSalary: Object,
        user_id: Number,
        month: String,
    },
    data() {
        return {
            checkOldDetails: false,
            editor: ClassicEditor,
            editorConfig: {
                removePlugins: ['imageUpload', 'mediaEmbed'],
            },
            allParties: [],
            lang: this.$page.props.lang,
            party_balance: null,
            bank_accounts: [],
            bank_id: null,
            tempAdvanced: null,
            tempDeduction: null,
            tempSalaryMonth: null,
            cash_balance: null,
            bank_balance: null,
            party_balance_status: null,
            tempAmount: null,
            form: this.$inertia.form({
                showroom_id: null,
                given_date: new Date().toISOString().substr(0, 10),
                salary_month: null,
                user_id: null,
                payment_method: 'cash',
                basic_salary: null,
                advanced: null,
                bonus: null,
                deduction: null,
                bank_account_id: null,
                check_number: null,
                cash_id: null,
                note: '<p></p>',
            }),
        }
    },

    methods: {
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
        saveSalary() {
            if (!this.form.cash_id && !this.form.bank_account_id) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select cash or bank!'
                })
                return
            }

            if (!this.form.user_id) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select user!'
                })
                return
            }

            this.form.advanced = (-1 * this.tempAdvanced)
            this.form.deduction = (-1 * this.tempDeduction)
            this.form.salary_month = this.tempSalaryMonth + '-01';
            let user = this.users.find(user => user.id == this.form.user_id)
            if(this.oldSalary) {
                this.form.put(this.route('salary.update', this.oldSalary.id), {
                    onSuccess: () => {
                        alert().fire({
                            icon: 'warning',
                            title: user.name + ' ' + this.lang.salary + ' ' + this.lang.from
                        })
                    },
                    onError: (errors) => console.log('error'),
                })
            }else{
                this.form.post(this.route('salary.store'), {
                    onSuccess: () => {
                        alert().fire({
                            icon: 'warning',
                            title: user.name + ' ' + this.lang.salary + ' ' + this.lang.from
                        })
                    },
                    onError: (errors) => console.log('error'),
                })
            }
        },

        oldSalaryData() {
            this.checkOldDetails = true
            this.form.user_id = this.oldSalary.user_id
            this.form.showroom_id = this.oldSalary.showroom_id
            this.form.basic_salary = this.oldSalary.details.find(detail => detail.purpose === 'basic_salary')?.amount
            this.form.bonus = this.oldSalary.details.find(detail => detail.purpose === 'bonus')?.amount
            this.tempAdvanced = Math.abs(this.oldSalary.details.find(detail => detail.purpose === 'advanced')?.amount)
            this.tempDeduction = Math.abs(this.oldSalary.details.find(detail => detail.purpose === 'deduction')?.amount)
            this.form.note = this.oldSalary.note
            if (this.oldSalary.cash_id) {
                this.form.payment_method = 'cash'
                this.form.cash_id = this.oldSalary.cash_id
            }else{
                this.form.payment_method = 'bank'
                this.bank_id = this.oldSalary.bank_account.bank_id
                this.getBankAccount()
                this.form.bank_account_id = this.oldSalary.bank_account_id
                this.form.check_number = this.oldSalary.check_number
            }
        },

    },

    mounted() {
        this.form.user_id = parseInt(this.user_id)
        this.tempSalaryMonth = this.month
        if (this.oldSalary) {
            this.oldSalaryData()
        }
    }
}
</script>

<style>
.ck-editor__editable {
    min-height: 180px;
}
</style>
