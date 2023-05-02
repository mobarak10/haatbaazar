<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container print-none">
                <ul class="mt-2 nav nav-tabs">
                    <li class="nav-item">
                        <inertia-link
                            class="nav-link active"
                            :href="route('report.customer-ledger')">
                            {{ lang.customer_ledger }}
                        </inertia-link>
                    </li>

                    <li class="nav-item">
                        <inertia-link
                            class="nav-link"
                            :href="route('report.supplier-ledger')">
                            {{ lang.supplier_ledger }}
                        </inertia-link>
                    </li>
                </ul>
            </div>

            <div class="container mt-2 sale">
                <print-details />
                <!-- table -->
                <div class="mt-2 card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ lang.customer_ledger }}</h5>
                            <div>
                                <div class="d-none d-print-block">
                                    {{ party?.name }}
                                </div>
                                <refresh-icon :Url="route('report.customer-ledger')" />
                                <print-icon />
                            </div>
                        </div>
                    </div>
                    <!-- search form start -->
                    <div class="card-body">
                        <form @submit.prevent="getLedgerDetails" class="print-none" method="post">
                            <div class="form-row col-md-12">
                                <div class="row">
                                    <div class="form-group col-md-2">
                                        <from-date ref="fromDate" />
                                    </div>

                                    <div class="form-group col-md-2">
                                        <to-date ref="toDate" />
                                    </div>

                                    <div class="form-group col-md-3">
                                        <mokam />
                                    </div>

                                    <div class="form-group col-md-3">
                                        <customer ref="customer" />
                                    </div>

                                    <div class="text-right form-group col-md-2" style="margin-top: 22px">
                                        <search-button :processing="processing" />
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div v-if="search">
                            <!-- search form start -->
                            <div class="mx-0 mt-2 form-row col-md-12">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                            <tr>
                                                <th>{{ lang.sl }}</th>
                                                <th>{{ lang.date }}</th>
                                                <th>{{ lang.particular }}</th>
                                                <th class="text-end">{{ lang.debit }}</th>
                                                <th class="text-end">{{ lang.credit }}</th>
                                                <th class="text-end">{{ lang.discount }}</th>
                                                <th class="text-end">{{ lang.balance }}</th>
                                                <th class="text-end print-none">{{ lang.action }}</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            <tr>
                                                <td>1.</td>
                                                <td></td>
                                                <td>{{ lang.opening_balance }}</td>
                                                <td colspan="4" class="text-end">
                                                    {{ Number.parseFloat(opening_balance).toFixed(2) }}
                                                    {{ opening_balance >= 0 ? lang.receivable : lang.payable }}
                                                </td>
                                                <td class="print-none"></td>
                                            </tr>

                                            <tr v-for="(ledger, index) in data.party_ledgers" :key="index">
                                                <td>{{ index + 2 }}</td>
                                                <td>{{ formatter.dateFormat(ledger.date) }}</td>
                                                <td style="max-width: 200px" class="text-wrap">
                                                    <p v-if="ledger.type === 'sale'">{{ lang.product_sale }}</p>
                                                    <p v-if="ledger.type === 'sale_return'" class="text-danger">{{ lang.product_return }}</p>
                                                    <p v-if="ledger.type === 'due_manage'">{{ lang.due_management }}</p>
                                                </td>

                                                <td class="text-end">
                                                    <p v-if="ledger.type === 'sale'">{{ Number.parseFloat(parseFloat(ledger.grand_total) + parseFloat(ledger.discount)).toFixed(2) }}</p>
                                                    <p v-if="ledger.type === 'sale_return'">{{ Number.parseFloat(ledger.return_paid).toFixed(2) }}</p>
                                                    <p v-if="ledger.type === 'due_manage'">{{ (ledger.amount <= 0) ? Number.parseFloat(Math.abs(ledger.amount)).toFixed(2) : Number.parseFloat(0).toFixed(2) }}</p>

                                                </td>

                                                <td class="text-end">
                                                    <p v-if="ledger.type === 'sale'">{{ Number.parseFloat(ledger.paid).toFixed(2) }}</p>
                                                    <p v-if="ledger.type === 'sale_return'">{{ Number.parseFloat(ledger.return_grand_total).toFixed(2) }}</p>
                                                    <p v-if="ledger.type === 'due_manage'">{{ (ledger.amount > 0) ? Number.parseFloat(ledger.amount).toFixed(2) : Number.parseFloat(0).toFixed(2) }}</p>
                                                </td>

                                                <td class="text-end">
                                                    <p v-if="ledger.type === 'sale'">{{ Number.parseFloat(ledger.discount).toFixed(2) }}</p>
                                                    <!--                                                <p v-if="ledger.type === 'sale_return'">{{ Number.parseFloat(ledger.return_grand_total).toFixed(2) }}</p>-->
                                                    <p v-if="ledger.type === 'due_manage'">{{ Number.parseFloat(ledger.adjustment).toFixed(2) }}</p>
                                                </td>

                                                <td class="text-end ">
                                                    {{ getBalance(ledger) }}
                                                </td>

                                                <td class="text-end print-none">
                                                    <a v-if="ledger.type === 'sale'" :href="route('sale.show', ledger.id)" class="btn btn-success btn-sm" title="View Invoice"
                                                       target="_blank">
                                                        <i class="bi bi-eye"></i>
                                                    </a>

                                                    <a v-if="ledger.type === 'sale_return'" :href="route('sale-return.show', ledger.id)" class="btn btn-success btn-sm" title="View Return Invoice"
                                                       target="_blank">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr v-if="!data.party_ledgers">
                                                <td colspan="15" class="text-center">{{ lang.no_ledger_available }}</td>
                                            </tr>

                                            <tr>
                                                <th colspan="3" class="text-end">{{ lang.total }}</th>
                                                <td class="text-end">
                                                    {{ parseFloat(parseFloat(data.total_debit) + parseFloat(data.total_discount)).toFixed(2) }}
                                                </td>
                                                <td class="text-end">
                                                    {{ Number.parseFloat(data.total_credit).toFixed(2) }}
                                                </td>
                                                <td class="text-end">
                                                    {{ parseFloat(parseFloat(data.total_discount) + parseFloat(data.total_adjustment)).toFixed(2) }}
                                                </td>
                                                <td></td>
                                                <td class="print-none"></td>
                                            </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                    <h5 class="text-end">{{ lang.closing_balance }}:
                                        {{ (data.party_balance < 0) ? Number.parseFloat(Math.abs(data.party_balance)).toFixed(2)+ ' ' + lang.payable : Number.parseFloat(Math.abs(data.party_balance)).toFixed(2)+ ' ' + lang.receivable }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- search form end -->
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import VSelect from "vue-select";
import 'vue-select/dist/vue-select.css';
import 'vue-select/dist/vue-select'
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import * as _formatter from '@/Helpers/formatter'
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import Mokam from "@/Pages/ReusableComponent/Customer/Mokam";
import Customer from "@/Pages/ReusableComponent/Customer/Customer";
import FromDate from "@/Pages/ReusableComponent/Date/FromDate";
import ToDate from "@/Pages/ReusableComponent/Date/ToDate";
import SearchButton from "@/Pages/ReusableComponent/Button/SearchButton";
import alert from "@/alert";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";

export default {
    name: "CustomerLedger",
    components: {
        BreezeAuthenticatedLayout,
        VSelect,
        PrintDetails,
        Mokam,
        Customer,
        FromDate,
        ToDate,
        SearchButton,
        PrintIcon,
        RefreshIcon,
    },

    computed: {
        formatter() {
            return _formatter
        },
    },

    props: {
        data: Object
    },

    data() {
        return {
            allParties: this.parties,
            lang:this.$page.props.lang,
            search: false,
            balance: 0,
            opening_balance: 0,
            party: null,
            processing: false,
        }
    },

    methods: {
        getLedgerDetails() {
            if (!this.$refs.customer.party_id) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select customer!'
                })
                return
            }
            this.processing = true
            this.search = true
            this.$inertia.visit('/report/customer-ledger', {
                method: 'get',
                data: {
                    search: true,
                    party_id: this.$refs.customer.party_id,
                    from_date: this.$refs.fromDate.from_date,
                    to_date: this.$refs.toDate.to_date,
                },
                preserveState: true,
                onSuccess: page => {
                    this.getOpeningBalance()
                    this.processing = false
                },
                onFinish: visit => {
                    this.processing = false
                },
            })
        },

        getBalance(ledger) {
            let _balance = parseFloat(localStorage.getItem("balance")) || 0
            if (ledger.type === 'sale') {
                _balance += (ledger.grand_total - ledger.total_paid)
            }
            else if (ledger.type === 'sale_return') {
                _balance += (ledger.return_paid - ledger.return_grand_total);
            }
            else if (ledger.type === 'due_manage'){
                if (ledger.amount >= 0) {
                    _balance -= parseFloat(ledger.amount) + parseFloat(ledger.adjustment);
                }else{
                    _balance += parseFloat(Math.abs(ledger.amount));
                }
            }

            localStorage.setItem('balance', _balance)
            let balanceStatus = _balance >= 0 ? this.lang.receivable : this.lang.payable
            return Number.parseFloat(Math.abs(_balance)).toFixed(2) + ' ' + balanceStatus
        },

        getOpeningBalance() {
            let opening_balance = 0;
            if(this.data.total_debit > this.data.total_credit) {
                opening_balance = (parseFloat(this.data.party_balance) - (parseFloat(this.data.total_debit) - parseFloat(this.data.total_credit)));
            }
            else {
                opening_balance = (parseFloat(this.data.party_balance) + (parseFloat(this.data.total_credit) - parseFloat(this.data.total_debit)));
            }
            this.opening_balance = opening_balance + parseFloat(this.data.total_adjustment)
            localStorage.setItem('balance', this.opening_balance)
        },

    },

    mounted() {
        const queryString = window.location.search;
        const parameters = new URLSearchParams(queryString);
        if(parameters.get('search')){
            this.search = true
            this.$refs.customer.party_id = parameters.get('party_id')
            this.$refs.fromDate.from_date = parameters.get('from_date')
            this.$refs.toDate.to_date = parameters.get('to_date')
            this.getOpeningBalance()
        }
    }
}
</script>

<style scoped>

</style>
