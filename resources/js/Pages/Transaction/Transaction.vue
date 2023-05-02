<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('transaction.index')" :create-route="route('transaction.create')" />
            <div class="container">
                <div class="card">
                    <div class="card-header mx-3 p-0 border-0 d-flex">
                        <!-- page title -->
                        <div class="mt-3">
                            <h4 class="main-title">{{ lang.transaction }}</h4>
                            <p><small>{{ lang.all }} <span style="color: red">*</span> {{ lang.mark_must_be_required }}</small></p>
                        </div>
                        <!-- header icon -->
                        <back-icon :Url="route('transaction.index')" />
                    </div>
                    <hr>

                    <div class="card-body mx-3 my-2 p-0 pt-3">
                        <form @submit.prevent="saveTransaction()" method="POST">
                            <div class="row">
                                <div class="form-group col-md-12 pr-3">
                                    <label for="showroom" class="required">{{ lang.showroom }}</label>
                                    <v-select
                                        v-model="form.showroom_id"
                                        :options="showrooms"
                                        id="showroom"
                                        required
                                        label="name"
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

                            <div class="row">
                                <div class="form-group col-md-6 pr-3">
                                    <label for="date" class="required">{{ lang.date }}</label>
                                    <input type="date" v-model="form.date" class="form-control" id="date" required>
                                </div>

                                <div class="form-group col-md-6 pl-3">
                                    <label for="amount" class="required">{{ lang.amount }} ({{ lang.bdt }})</label>
                                    <input type="number" v-model="form.amount" class="form-control" placeholder="0.00" id="amount" required>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <!-- Transfer From -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">{{ lang.transfer }} {{ lang.from }} </div>
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <div class="col-sm-auto">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" v-model="form.transaction_from" id="from-cash" value="cash">
                                                        <label class="form-check-label" for="from-cash">{{ lang.cash }} </label>
                                                    </div>

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" v-model="form.transaction_from" type="radio" id="from-bank" value="bank">
                                                        <label class="form-check-label" for="from-bank">{{ lang.bank }} </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- cash -->
                                            <div class="row" v-if="form.transaction_from === 'cash'">
                                                <div class="form-group col-md-12">
                                                    <label class="required">{{ lang.cash }}</label>
                                                    <select v-model="from_cash_id" @change="getFromBalance" class="form-select" required>
                                                        <option :value="null" selected disabled>{{ lang.choose_one }}</option>
                                                        <option v-for="(cash, index) in cashes" :value="cash.id" :key="index">
                                                            {{ cash.title }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- bank -->
                                            <div class="row" v-if="form.transaction_from === 'bank'">
                                                <div class="form-group col-md-6">
                                                    <label class="required">{{ lang.bank }}</label>
                                                    <select v-model="from_bank_id" @change="getFromBankAccount" class="form-select" required>
                                                        <option :value="null" selected disabled>{{ lang.choose_one }}</option>
                                                        <option v-for="(bank, index) in banks" :value="bank.id" :key="index">
                                                            {{ bank.name }}
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label class="required">{{ lang.bank_account }}</label>
                                                    <select class="form-select" @change="getFromBalance" v-model="from_bank_account_id" required>
                                                        <option :value="null" selected disabled>{{ lang.choose_one }}</option>
                                                        <option v-for="(account, index) in fromBankAccounts" :value="account.id" :key="index">
                                                            {{ account.account_name }} ({{ account.account_number }})
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div v-if="fromBalance" class="col-12 mt-2">
                                                <!-- cash balance start -->
                                                <p class="d-block bg-dark text-light p-1 px-2">{{ fromBalance }}</p>
                                                <!-- cash balance end -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Transfer From -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">{{ lang.transfer }} {{ lang.to }}</div>
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <div class="col-sm-auto">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" v-model="form.transaction_to" id="to-cash" value="cash">
                                                        <label class="form-check-label" for="to-cash">{{ lang.cash }} </label>
                                                    </div>

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" v-model="form.transaction_to" type="radio" id="to-bank" value="bank">
                                                        <label class="form-check-label" for="to-bank">{{ lang.bank }} </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- cash -->
                                            <div class="row" v-if="form.transaction_to === 'cash'">
                                                <div class="form-group col-md-12">
                                                    <label class="required">{{ lang.cash }}</label>
                                                    <select v-model="to_cash_id" @change="getToBalance" class="form-select" required>
                                                        <option :value="null" selected disabled>{{ lang.choose_one }}</option>
                                                        <option v-for="(cash, index) in cashes" :value="cash.id" :key="index">
                                                            {{ cash.title }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- bank -->
                                            <div class="row" v-if="form.transaction_to === 'bank'">
                                                <div class="form-group col-md-6">
                                                    <label class="required">{{ lang.bank }}</label>
                                                    <select v-model="to_bank_id" @change="getToBankAccount" class="form-select" required>
                                                        <option :value="null" selected disabled>{{ lang.choose_one }}</option>
                                                        <option v-for="(bank, index) in banks" :value="bank.id" :key="index">
                                                            {{ bank.name }}
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label class="required">{{ lang.bank_account }}</label>
                                                    <select class="form-select" @change="getToBalance" v-model="to_bank_account_id" required>
                                                        <option :value="null" selected disabled>{{ lang.choose_one }}</option>
                                                        <option v-for="(account, index) in toBankAccounts" :value="account.id" :key="index">
                                                            {{ account.account_name }} ({{ account.account_number }})
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div v-if="toBalance" class="col-12 mt-2">
                                                <!-- cash balance start -->
                                                <p class="d-block bg-dark text-light p-1 px-2">{{ toBalance }}</p>
                                                <!-- cash balance end -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12 pr-3">
                                    <label>{{ lang.note }}</label>
                                    <ckeditor :editor="editor" v-model="form.note" :config="editorConfig"></ckeditor>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12 text-end">
                                    <save-button :processing="processing" />
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
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import BackIcon from "@/Pages/ReusableComponent/Icon/BackIcon";
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import SaveButton from "@/Pages/ReusableComponent/Button/SaveButton";
import confirmAlert from "@/confirm";
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import vSelect from "vue-select";
import 'vue-select/dist/vue-select.css'
export default {
    name: "Transaction",
    components: {
        BreezeAuthenticatedLayout,
        TopBar,
        BackIcon,
        SaveButton,
        vSelect,
    },
    watch: {
        'form.transaction_from': function () {
            this.from_bank_account_id = null;
            this.from_cash_id = null;
            this.fromBalance = null;
        },

        'form.transaction_to': function () {
            this.to_bank_account_id = null;
            this.to_cash_id = null;
            this.toBalance = null;
        }
    },
    props: {
        oldTransaction: Object,
        cashes: Array,
        banks: Array,
        showrooms: Array,
    },
    data() {
        return {
            editor: ClassicEditor,
            editorConfig: {
                removePlugins: ['imageUpload', 'mediaEmbed'],
            },
            lang: this.$page.props.lang,
            fromBankAccounts: null,
            fromBalance: null,
            from_bank_id: null,
            from_bank_account_id: null,
            from_cash_id: null,
            toBankAccounts: null,
            toBalance: null,
            to_bank_id: null,
            to_bank_account_id: null,
            to_cash_id: null,
            processing: false,
            form: this.$inertia.form({
                date: new Date().toISOString().substr(0,10),
                amount: null,
                showroom_id: null,
                transaction_from: 'cash',
                transaction_to: 'bank',
                note: '<p></p>',
                transaction_from_id: null,
                transaction_to_id: null,
            }),
        }
    },

    methods: {
        saveTransaction() {
            if (this.from_cash_id) {
                this.form.transaction_from_id = this.from_cash_id;
            } else {
                this.form.transaction_from_id = this.from_bank_account_id;
            }

            if (this.to_cash_id) {
                this.form.transaction_to_id = this.to_cash_id;
            } else {
                this.form.transaction_to_id = this.to_bank_account_id;
            }

            if (!this.from_cash_id && !this.from_bank_account_id) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select from transfer method!'
                })
                return
            }

            if (!this.form.amount) {
                alert().fire({
                    icon: 'warning',
                    title: 'Enter amount!'
                })
                return
            }

            if (!this.to_cash_id && !this.to_bank_account_id) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select to transfer method!'
                })
                return
            }

            confirmAlert().then((result) => {
                if (result.isConfirmed) {
                    if (this.oldTransaction) {
                        this.form.put(this.route('transaction.update', this.oldTransaction.id))
                    }else{
                        this.form.post(this.route('transaction.store'))
                    }
                }
            })
        },

        getFromBalance() {
            if (this.from_cash_id) {
                this.fromBalance = this.cashes.find(cash => cash.id == this.from_cash_id).balance
            }else{
                this.fromBalance = this.fromBankAccounts.find(account => account.id == this.from_bank_account_id).balance
            }
        },

        getToBalance() {
            if (this.to_cash_id) {
                this.toBalance = this.cashes.find(cash => cash.id == this.o_cash_id).balance
            }else{
                this.toBalance = this.toBankAccounts.find(account => account.id == this.to_bank_account_id).balance
            }
        },

        getFromBankAccount() {
            this.fromBankAccounts = this.banks.find(bank => bank.id == this.from_bank_id).bank_accounts
        },

        getToBankAccount() {
            this.toBankAccounts = this.banks.find(bank => bank.id == this.to_bank_id).bank_accounts
        },

        initValues() {
            if (this.oldTransaction) {
                this.form.note = this.oldTransaction.note
                this.form.amount = this.oldTransaction.amount
                this.form.date = this.oldTransaction.date
                this.form.transaction_from = this.oldTransaction.transaction_from
                this.form.transaction_to= this.oldTransaction.transaction_to
                if (this.oldTransaction.transaction_from == 'cash') {
                    this.from_cash_id = this.oldTransaction.transaction_from_id
                }else{
                    let where = 'from';
                    this.getInitialBankDetails(this.oldTransaction, where)
                }
                if(this.oldTransaction.transaction_to == 'cash') {
                    this.to_cash_id = this.oldTransaction.transaction_to_id
                }else{
                    let where = 'to';
                    this.getInitialBankDetails(this.oldTransaction, where)
                }
                this.getFromBalance()
                this.getToBalance()
            }
        },

        getInitialBankDetails(oldTransaction, where) {
            if (where == 'to') {
                this.to_bank_id = oldTransaction.to_transaction.bank.id
                this.to_bank_account_id = this.oldTransaction.transaction_to_id
            }else{
                this.from_bank_id = oldTransaction.from_transaction.bank.id
                this.from_bank_account_id = this.oldTransaction.transaction_from_id
            }
            this.getToBankAccount()
        },
    },

    mounted() {
        this.initValues()
    }
}
</script>

<style>
    .ck-editor__editable {
        min-height: 200px;
    }
</style>
