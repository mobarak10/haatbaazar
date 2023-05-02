<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('income-record.index')" :modal="showModal" />

            <div class="container sale">
                <!-- table -->
                <div class="border-0 card">
                    <div
                        class="p-0 py-1 border-0  card-header d-sm-flex d-block align-items-center"
                    >
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_income_record }}</h4>
                            <br />
                        </div>
                        <!-- add -->

                        <button
                            type="button"
                            role="button"
                            class="btn top-icon-button print-none ms-auto"
                            :title="lang.create_new_income_record"
                            @click="showModal"
                        >
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>

                    <!-- New Category Modal -->
                    <div
                        class="modal fade"
                        id="payment-method"
                        ref="modal"
                        tabindex="-1"
                        aria-labelledby="paymentMethodLabel"
                        aria-hidden="true"
                    >
                        <div class="modal-dialog modal-lg">
                            <form action="#" @submit.prevent="submitForm()">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5
                                            class="modal-title"
                                            id="paymentMethodLabel"
                                        >
                                            {{ lang.add_new_income_record }}
                                        </h5>
                                        <button
                                            type="button"
                                            :disabled="form.processing"
                                            class="btn-close"
                                            data-bs-dismiss="modal"
                                            aria-label="Close"
                                        ></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label
                                                    for="showroom"
                                                    class="form-label">
                                                    {{ lang.showroom }}
                                                </label>
                                                <v-select
                                                    id="showroom"
                                                    v-model="form.showroom_id"
                                                    :options="showrooms"
                                                    label="name"
                                                    :clearable="false"
                                                    :placeholder="lang.select_showroom"
                                                    :reduce="(showroom) =>showroom.id">
                                                </v-select>
                                            </div>

                                            <div class="col-6">
                                                <label
                                                    for="income-sector"
                                                    class="form-label required"
                                                >{{ lang.income_sector }}</label
                                                >
                                                <select
                                                    name="income-sector"
                                                    v-model="form.income_sector_id"
                                                    class="form-select"
                                                    id="income-sector">
                                                    <option :value="null">{{ lang.choose_income_sector }}</option>
                                                    <option v-for="(sector, index) in income_sectors" :key="index" :value="sector.id">
                                                        {{ sector.name }}
                                                    </option>
                                                </select>

                                                <div
                                                    v-if="form.errors.income_sector_id"
                                                    class="text-danger"
                                                >
                                                    {{ form.errors.income_sector_id }}
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <label
                                                    for="date"
                                                    class="form-label required">
                                                   {{lang.date}}
                                                </label>
                                                <input type="date" class="form-control" id="date" v-model="form.date">
                                                <div
                                                    v-if="form.errors.date"
                                                    class="text-danger"
                                                >
                                                    {{ form.errors.date }}
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <label
                                                    for="amount"
                                                    class="form-label required">
                                                    {{ lang.amount }}
                                                </label>
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    :placeholder="lang.enter_income_account"
                                                    id="amount"
                                                    v-model="form.amount">
                                                <div
                                                    v-if="form.errors.amount"
                                                    class="text-danger"
                                                >
                                                    {{ form.errors.amount }}
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" v-model="form.payment_type" id="cash" value="cash">
                                                    <label class="form-check-label" for="cash">{{ lang.cash }}</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" v-model="form.payment_type" id="bank" value="bank">
                                                    <label class="form-check-label" for="bank">{{ lang.bank }}</label>
                                                </div>
                                            </div>

                                            <div v-if="form.payment_type == 'cash'" class="col-12">
                                                <label
                                                    for="cash-payment"
                                                    class="form-label required"
                                                >{{ lang.cash }}</label
                                                >
                                                <select
                                                    name="income-sector"
                                                    v-model="form.cash_id"
                                                    class="form-select"
                                                    id="cash-payment">
                                                    <option :value="null">{{ lang.choose_cash }}</option>
                                                    <option v-for="(cash, index) in cashes" :key="index" :value="cash.id">
                                                        {{ cash.title }}
                                                    </option>
                                                </select>

                                                <div
                                                    v-if="form.errors.cash_id"
                                                    class="text-danger"
                                                >
                                                    {{ form.errors.cash_id }}
                                                </div>
                                            </div>

                                            <!-- party balance start -->
                                            <div v-if="cashBalance != null" class="col-12 mt-2">
                                                <p class="d-block bg-dark text-light p-1 px-2">{{ cashBalance }}</p>
                                            </div>
                                            <!-- party balance end -->

                                            <div class="col-12" v-if="this.form.payment_type == 'bank'">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label
                                                            for="bank-payment"
                                                            class="form-label required">
                                                            {{ lang.bank }}
                                                        </label>
                                                        <select
                                                            name="income-sector"
                                                            v-model="form.bank_id"
                                                            @change="getBankAccount"
                                                            class="form-select"
                                                            id="bank-payment">
                                                            <option :value="null">{{ lang.choose_bank }}</option>
                                                            <option v-for="(bank, index) in banks" :key="index" :value="bank.id">
                                                                {{ bank.name }}
                                                            </option>
                                                        </select>

                                                        <div
                                                            v-if="form.errors.bank_id"
                                                            class="text-danger"
                                                        >
                                                            {{ form.errors.bank_id }}
                                                        </div>
                                                    </div>

                                                    <div class="col-6">
                                                        <label
                                                            for="bank-account-payment"
                                                            class="form-label required">
                                                            {{ lang.bank_account }}
                                                        </label>
                                                        <select
                                                            v-model="form.bank_account_id"
                                                            class="form-select"
                                                            id="bank-account-payment">
                                                            <option :value="null">{{ lang.choose_bank_account }}</option>
                                                            <option v-for="(account, index) in bank_accounts" :key="index" :value="account.id">
                                                                {{ account.account_name }}
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
                                            </div>
                                            <!-- party balance start -->
                                            <div v-if="bankAccountBalance != null" class="col-12 mt-2">
                                                <p class="d-block bg-dark text-light p-1 px-2">{{ bankAccountBalance }}</p>
                                            </div>
                                            <!-- party balance end -->

                                            <div class="col-12">
                                                <label
                                                    for="address"
                                                    class="form-label"
                                                >{{ lang.description }}</label
                                                >
                                                <textarea
                                                    class="form-control"
                                                    v-model="form.description"
                                                    id="address"
                                                    :placeholder="lang.enter_address"
                                                ></textarea>
                                                <div
                                                    v-if="form.errors.description"
                                                    class="text-danger"
                                                >
                                                    {{ form.errors.description }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button
                                            type="button"
                                            :disabled="form.processing"
                                            class="btn custom-btn btn-danger"
                                            data-bs-dismiss="modal"
                                        >
                                            {{ lang.close }}
                                        </button>
                                        <button
                                            type="submit"
                                            class="btn custom-btn btn-success"
                                            :disabled="form.processing"
                                        >
                                            <span
                                                v-if="form.processing"
                                                class=" spinner-border spinner-border-sm"
                                                role="status"
                                                aria-hidden="true"
                                            ></span>
                                            {{ lang.save }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End New Category Modal -->

                    <!-- content body -->
                    <div class="p-0 card-body">
                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" width="100">{{ lang.sl }}</th>
                                        <th scope="col">{{ lang.date }}</th>
                                        <th scope="col">{{ lang.income_sector }}</th>
                                        <th scope="col">{{ lang.amount }}</th>
                                        <th scope="col" width="100">{{ lang.action }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <!-- Starts from here -->
                                <tr
                                    v-for="(record, index) in income_records.data"
                                    :key="index"
                                >
                                    <th scope="row">{{ income_records.from + index }}</th>
                                    <td>{{ formatter.dateFormat(record.date) }}</td>
                                    <td>{{ record.income_sector?.name }}</td>
                                    <td>{{ record.amount}}</td>
                                    <td>
                                        <edit-modal-icon @click.prevent="editIncomeRecord(record)" />
                                        <delete-icon @click.prevent="deleteIncomeRecord(record.id)" />
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <pagination :links="income_records.links" />
                    </div>
                    <!-- pagination -->
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import Button from "@/Components/Button";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import vSelect from "vue-select";
import 'vue-select/dist/vue-select.css'
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import Pagination from "@/Components/Pagination";
import * as _formatter from '@/Helpers/formatter'
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import EditModalIcon from "@/Pages/ReusableComponent/Icon/EditModalIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";

export default {
    name: "Index",
    components: {
        Button,
        BreezeAuthenticatedLayout,
        Pagination,
        vSelect,
        TopBar,
        EditModalIcon,
        DeleteIcon,
    },
    computed: {
        formatter() {
            return _formatter
        }
    },
    props: {
        income_records: Object,
        income_sectors: Object,
        cashes: Array,
        banks: Array,
        showrooms: Array,
    },
    watch: {
        'form.payment_type': function (value) {
            if(value == 'cash') {
                this.form.bank_id = null
                this.form.bank_account_id = null
                this.bankAccountBalance = null
            }else{
                this.form.cash_id = null
                this.cashBalance = null
            }
        },
        'form.cash_id': function (value) {
            if(value) {
                this.cashBalance = this.cashes.find(cash => cash.id == value).balance
            }
        },

        'form.bank_account_id': function (value) {
            if(value) {
                this.bankAccountBalance = this.bank_accounts.find(account => account.id == value).balance
            }
        },
    },
    data() {
        return {
            lang:this.$page.props.lang,
            bank_accounts: [],
            modal: {},
            cashBalance: null,
            bankAccountBalance: null,
            form: this.$inertia.form({
                income_sector_id: null,
                showroom_id: null,
                cash_id: null,
                bank_id: null,
                bank_account_id: null,
                description: null,
                amount: null,
                income_by: null,
                payment_type: 'cash',
                date: new Date().toISOString().substr(0, 10),
            }),
        };
    },
    methods: {
        showModal() {
            this.modal.show();
        },

        hideModal() {
            this.modal.hide();
        },

        resetModal() {
            this.cashBalance = null
            this.bankAccountBalance = null
            this.form = this.$inertia.form({
                income_sector_id: null,
                showroom_id: null,
                cash_id: null,
                bank_id: null,
                bank_account_id: null,
                description: null,
                amount: null,
                income_by: null,
                payment_type: 'cash',
                date: new Date().toISOString().substr(0, 10),
            });
        },

        getBankAccount() {
            this.bank_accounts = this.banks.find(bank => bank.id == this.form.bank_id).bank_accounts
        },

        submitForm() {
            if (this.form.id) {
                this.updateIncomeRecord();
                return;
            }

            this.form.post(this.route("income-record.store"), {
                preserveScroll: true,
                onSuccess: () => this.hideModal(),
            });
        },
        deleteIncomeRecord(record_id) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("income-record/" + record_id, {
                        onSuccess: () => {
                            toast.fire({
                                icon: 'success',
                                title: 'Successfully Deleted!'
                            })
                        },
                    });
                }
            })
        },
        editIncomeRecord(record) {
            if (record.cash_id) {
                record.payment_type = 'cash'
            }else{
                this.form.bank_id = this.banks.find(bank => bank.bank_accounts.find(account => account.id == record.bank_account_id)).id
                this.getBankAccount()
                record.bank_id = this.form.bank_id
                record.payment_type = 'bank'
            }
            this.form = this.$inertia.form({
                ...record,
            });
            this.showModal();
        },
        updateIncomeRecord() {
            this.form.patch(this.route("income-record.update", this.form.id), {
                preserveScroll: true,
                onSuccess: () => {
                    this.hideModal()
                    alert().fire({
                        icon: 'success',
                        title: 'Successfully Updated!'
                    })
                },
            });
        },
    },

    mounted() {
        this.modal = new bootstrap.Modal(this.$refs.modal);
        this.$refs.modal.addEventListener("hidden.bs.modal", () =>
            this.resetModal()
        );
    },
}
</script>

<style scoped>

</style>
