<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container mt-3 unit">
                <print-details />
                <!-- table -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ lang.cash_book }}</h5>

                            <div>
                                <refresh-icon :Url="route('report.cash-book')" />
                                <print-icon />
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- search form start -->
                        <form @submit.prevent="getDateWiseData" class="row print-none">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
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
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label required" for="search-date">{{ lang.date }}</label>
                                        <input type="date" v-model="date" class="form-control" id="search-date">
                                    </div>

                                    <div class="col-md-4" style="padding-top: 30px">
                                        <button type="submit" class="btn btn-primary" :title="lang.search">
                                            <i class="fa fa-search"></i>
                                            {{ lang.search}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- search form end -->

                        <!-- start cash book data -->
                        <div v-if="search" class="row mt-2 col-md-12">
                            <div class="col-md-6">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                    <tr>
                                        <th>{{ lang.income_details }}</th>
                                        <th class="text-end">{{ lang.amount }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th>{{ lang.opening_balance }}</th>
                                        <th class="text-end">
                                            {{ Number.parseFloat(cashBookData?.opening_balance?.amount || 0).toFixed(2) }}
                                        </th>
                                    </tr>

                                    <tr>
                                        <th colspan="2" style="font-size: larger">{{ lang.sale }}:</th>
                                    </tr>

                                    <tr v-for="(sale, index) in cashBookData?.sales" :key="index">
                                        <td class="text-wrap">{{ sale.party?.name || '' }} ({{ sale.party?.mokam?.name || '' }})</td>
                                        <td class="text-end">{{ Number.parseFloat(sale.paid).toFixed(2) }}</td>
                                    </tr>

                                    <tr>
                                        <th>{{ lang.total }} {{ lang.sale }}:</th>
                                        <td class="text-end">
                                            {{ Number.parseFloat(cashBookData?.total_sale).toFixed(2) }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th colspan="2" style="font-size: larger">{{ lang.due_receive }}:</th>
                                    </tr>

                                    <tr v-for="(due_receive, index) in cashBookData?.due_receives" :key="index">
                                        <td class="text-wrap">
                                            {{ due_receive.party?.name || '' }}
                                            ({{ due_receive.party?.mokam?.name || '' }})
                                        </td>
                                        <td class="text-end">
                                            {{ Number.parseFloat(due_receive.amount).toFixed(2) }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>{{ lang.total }} {{ lang.due_receive }}:</th>
                                        <td class="text-end">
                                            {{ Number.parseFloat(cashBookData?.total_due_receive).toFixed(2) }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th colspan="2" style="font-size: larger">{{ lang.purchase_return }}:</th>
                                    </tr>

                                    <tr v-for="(purchase_return, index) in cashBookData?.purchase_returns" :key="index">
                                        <td class="text-wrap">{{ purchase_return.party?.name || '' }}</td>
                                        <td class="text-end">
                                            {{ Number.parseFloat(purchase_return.return_paid).toFixed(2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ lang.total }} {{ lang.purchase_return }}:</th>
                                        <td class="text-end">
                                            {{ Number.parseFloat(cashBookData?.total_purchase_return).toFixed(2) }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th style="font-size: larger">{{ lang.grand_total }}:</th>
                                        <td class="text-end">
                                            {{ Number.parseFloat(parseFloat(totalIncome) + parseFloat(cashBookData?.opening_balance?.amount || 0)).toFixed(2) }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                    <tr>
                                        <th>{{ lang.expense_details }}</th>
                                        <th class="text-end">{{ lang.amount }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th colspan="2" style="font-size: larger">{{ lang.purchase }}:</th>
                                    </tr>

                                    <tr v-for="(purchase, index) in cashBookData?.purchases" :key="index">
                                        <td class="text-wrap">{{ purchase.party?.name || '' }}</td>
                                        <td class="text-end">{{ Number.parseFloat(purchase.paid).toFixed(2) }}</td>
                                    </tr>

                                    <tr>
                                        <th>{{ lang.total }} {{ lang.purchase }}:</th>
                                        <td class="text-end">
                                            {{ Number.parseFloat(cashBookData?.total_purchase).toFixed(2) }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th colspan="2" style="font-size: larger">{{ lang.expense }}:</th>
                                    </tr>

                                    <tr v-for="(expense, index) in cashBookData?.expenses" :key="index">
                                        <td class="text-wrap">{{ expense.cost_subcategory?.name }}</td>
                                        <td class="text-end">{{ Number.parseFloat(expense.amount || 0).toFixed(2) }}</td>
                                    </tr>

                                    <tr>
                                        <th>{{ lang.total }} {{ lang.expense }}:</th>
                                        <td class="text-end">{{ Number.parseFloat(cashBookData?.total_expense).toFixed(2) }}</td>
                                    </tr>

                                    <tr>
                                        <th colspan="2" style="font-size: larger">{{ lang.due_paid }}:</th>
                                    </tr>

                                    <tr v-for="(due_paid, index) in cashBookData?.due_payments" :key="index">
                                        <td class="text-wrap">{{ due_paid.party?.name || '' }} ({{ due_paid.party?.mokam?.name || '' }})</td>
                                        <td class="text-end">{{ Number.parseFloat(Math.abs(due_paid.amount)).toFixed(2) }}</td>
                                    </tr>

                                    <tr>
                                        <th>{{ lang.total }} {{ lang.due_paid }}:</th>
                                        <td class="text-end">
                                            {{ Number.parseFloat(Math.abs(cashBookData?.total_due_payment)).toFixed(2) }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th colspan="2" style="font-size: larger">{{ lang.sales_return }}:</th>
                                    </tr>

                                    <tr v-for="(sale_return, index) in cashBookData?.sale_returns" :key="index">
                                        <td class="text-wrap">{{ sale_return.party?.name || '' }} ({{ sale_return.party?.mokam?.name }})</td>
                                        <td class="text-end">{{ Number.parseFloat(sale_return.return_paid).toFixed(2) }}</td>
                                    </tr>

                                    <tr>
                                        <th>{{ lang.total }} {{ lang.sales_return }}:</th>
                                        <td class="text-end">{{ Number.parseFloat(cashBookData?.total_sale_return).toFixed(2) }}</td>
                                    </tr>

                                    <tr>
                                        <th style="font-size: larger">{{ lang.total }} {{ lang.expense }}:</th>
                                        <td class="text-end">
                                            {{ Number.parseFloat(totalExpense).toFixed(2) }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                    <tr>
                                        <th class="text-end">{{ lang.cash_in_hand }}:</th>
                                        <th class="text-end">{{ Number.parseFloat(handCash).toFixed(2) }}</th>
                                    </tr>
                                    </thead>
                                </table>
                                <div class="text-end">
                                    <button type="button" class="btn btn-success" @click="showModal">
                                        {{ lang.cash }} {{ lang.close }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- start cash book data -->
                    </div>


                    <!-- New cash close Modal -->
                    <div class="modal fade" id="payment-method" ref="modal" tabindex="-1" aria-labelledby="paymentMethodLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="#" @submit.prevent="submitForm()">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="paymentMethodLabel">{{ lang.cash }} {{ lang.close }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label for="date" class="form-label required">{{ lang.date }}</label>
                                                <input type="date" class="form-control" required v-model="form.date" id="date">
                                                <div v-if="form.errors.date" class="text-danger">{{ form.errors.date }}</div>
                                            </div>

                                            <div class="col-12">
                                                <label for="amount" class="form-label required">{{ lang.amount }}</label>
                                                <input type="number" step="any" disabled class="form-control" v-model="handCash" id="amount">
                                                <div v-if="form.errors.amount" class="text-danger">{{ form.errors.amount }}</div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn custom-btn btn-danger"
                                                data-bs-dismiss="modal">{{ lang.close }}</button>
                                        <button type="submit" class="btn custom-btn btn-success">{{ lang.save }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End cash close Modal -->

                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import alert from "@/alert";
import vSelect from "vue-select";
import 'vue-select/dist/vue-select.css'

export default {
    name: "Index",
    components: {
        BreezeAuthenticatedLayout,
        PrintIcon,
        PrintDetails,
        RefreshIcon,
        vSelect,
    },
    props: {
        data: Object,
        showrooms: Array,
    },
    computed: {
        totalIncome() {
            return this.cashBookData?.total_sale + this.cashBookData?.total_due_receive + this.cashBookData?.total_purchase_return
        },

        totalExpense() {
            return this.cashBookData?.total_purchase + this.cashBookData?.total_expense + Math.abs(this.cashBookData?.total_due_payment) + this.cashBookData?.total_sale_return
        },

        handCash() {
            return this.search
                ?
                (parseFloat(this.totalIncome) + parseFloat(this.cashBookData?.opening_balance?.amount || 0)) - this.totalExpense
                :
                this.data.cash_balance
        }
    },
    data() {
        return{
            modal: {},
            lang:this.$page.props.lang,
            processing: false,
            search: false,
            date: null,
            showroom_id: null,
            cashBookData: null,
            form: this.$inertia.form({
                showroom_id: null,
                date: new Date().toISOString().substr(0, 10),
                amount: null,
            }),
        }
    },

    methods: {
        showModal(){
            this.modal.show()
        },

        hideModal(){
            this.modal.hide()
        },

        resetModal(){
            this.form.name = null
        },
        getDateWiseData() {
            if (!this.date) {
                alert('Please enter date!')
                return;
            }
            this.search = true
            this.$inertia.visit('/report/cash-book', {
                method: 'get',
                data: {
                    search: true,
                    date: this.date
                },
                preserveState: true,
                onSuccess: page => {
                    this.cashBookData = this.data
                    this.processing = false
                },
            })
        },

        submitForm() {
            this.form.amount = this.handCash
            this.form.post(this.route('report.cash-book'), {
                preserveScroll: true,
                onSuccess: () => {
                    this.hideModal()
                }
            })
        },
    },

    mounted() {
        this.cashBookData = this.data
        this.modal = new bootstrap.Modal(this.$refs.modal)
        this.$refs.modal.addEventListener('hidden.bs.modal', () => this.resetModal())

        const queryString = window.location.search;
        const parameters = new URLSearchParams(queryString);
        if(parameters.get('search')){
            this.search = true
            this.date = parameters.get('date')
            this.showroom_id = parameters.get('showroom_id')
            this.form.showroom_id = parameters.get('showroom_id')
        }
    }
}
</script>

<style scoped>

</style>
