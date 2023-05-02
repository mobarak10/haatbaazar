<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container print-none">
                <ul class="mt-2 nav nav-tabs">
                    <li class="nav-item">
                        <inertia-link
                            class="nav-link"
                            :href="route('report.customer-ledger')">
                            {{ lang.customer_ledger }}
                        </inertia-link>
                    </li>

                    <li class="nav-item">
                        <inertia-link
                            class="nav-link active"
                            :href="route('report.supplier-ledger')">
                            {{ lang.supplier_ledger }}
                        </inertia-link>
                    </li>
                </ul>
            </div>

            <div class="sale mt-2 container">
                <print-details />
                <!-- table -->
                <div class="card mt-2">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ lang.supplier_ledger }}</h5>
                            <div>
                                <refresh-icon :Url="route('report.supplier-ledger')" />
                                <print-icon />
                            </div>
                        </div>
                    </div>
                    <!-- search form start -->
                    <div class="card-body">
                        <form @submit.prevent="getLedgerDetails" class="print-none" method="post">
                            <div class="form-row col-md-12">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label for="from_date">{{ lang.from_date }}</label>
                                        <input type="date" v-model="from_date" id="from_date" class="form-control">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="to_date">{{ lang.to_date }}</label>
                                        <input type="date" v-model="to_date" id="to_date" class="form-control">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label class="required" for="party_id">{{ lang.supplier }}</label>
                                        <v-select
                                            :options="parties"
                                            id="party_id"
                                            :reduce="party => party.id"
                                            :placeholder="lang.select_supplier"
                                            v-model="party_id"
                                            label="name">
                                            <template slot="option" slot-scope="option">
                                                <span class="fa" :class="option.icon"></span>
                                                {{ option.name }}
                                            </template>
                                        </v-select>
                                    </div>

                                    <div class="form-group col-md-2 text-right" style="margin-top: 22px">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search"></i>
                                            {{ lang.search }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div v-if="search">
                            <!-- search form start -->
                            <div class="form-row col-md-12 mt-2 mx-0">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                        <tr>
                                            <th>{{ lang.sl }}</th>
                                            <th>{{ lang.date }}</th>
                                            <th>{{ lang.particular }}</th>
                                            <th class="text-right">{{ lang.debit }}</th>
                                            <th class="text-right">{{ lang.credit }}</th>
                                            <th class="text-right">{{ lang.discount }}</th>
                                            <th class="text-right">{{ lang.balance }}</th>
                                            <th class="text-right print-none">{{ lang.action }}</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr>
                                            <td>1.</td>
                                            <td></td>
                                            <td>{{ lang.opening_balance }}</td>
                                            <td colspan="4" class="text-end">
                                                {{ Number.parseFloat(Math.abs(opening_balance)).toFixed(2) }}
                                                {{ opening_balance >= 0 ? lang.receivable : lang.payable }}
                                            </td>
                                            <td></td>
                                        </tr>

                                        <tr v-for="(ledger, index) in data.party_ledgers" :key="index">
                                            <td>{{ index + 2 }}</td>
                                            <td>{{ formatter.dateFormat(ledger.date) }}</td>
                                            <td style="max-width: 200px" class="text-wrap">
                                                <p v-if="ledger.type === 'purchase'">{{ lang.purchase_product }}</p>
                                                <p v-if="ledger.type === 'purchase_return'" class="text-danger">{{ lang.product_return }}</p>
                                                <p v-if="ledger.type === 'due_manage'">{{ lang.due_management }}</p>
                                            </td>

                                            <td class="text-end">
                                                <p v-if="ledger.type === 'purchase'">{{ Number.parseFloat(parseFloat(ledger.grand_total) + parseFloat(ledger.discount)).toFixed(2) }}</p>
                                                <p v-if="ledger.type === 'purchase_return'">{{ Number.parseFloat(ledger.return_paid).toFixed(2) }}</p>
                                                <p v-if="ledger.type === 'due_manage'">{{ (ledger.amount <= 0) ? Number.parseFloat(Math.abs(ledger.amount)).toFixed(2) : Number.parseFloat(0).toFixed(2) }}</p>

                                            </td>

                                            <td class="text-end">
                                                <p v-if="ledger.type === 'purchase'">{{ Number.parseFloat(ledger.paid).toFixed(2) }}</p>
                                                <p v-if="ledger.type === 'purchase_return'">{{ Number.parseFloat(ledger.return_grand_total).toFixed(2) }}</p>
                                                <p v-if="ledger.type === 'due_manage'">{{ (ledger.amount > 0) ? Number.parseFloat(ledger.amount).toFixed(2) : Number.parseFloat(0).toFixed(2) }}</p>
                                            </td>

                                            <td class="text-end">
                                                <p v-if="ledger.type === 'purchase'">{{ Number.parseFloat(ledger.discount).toFixed(2) }}</p>
                                                <p v-if="ledger.type === 'due_manage'">{{ Number.parseFloat(ledger.adjustment).toFixed(2) }}</p>
                                            </td>

                                            <td class="text-end ">
                                                {{ getBalance(ledger) }}
                                            </td>

                                            <td class="text-end print-none">
                                                <a v-if="ledger.type === 'purchase'" :href="route('purchase.show', ledger.id)" class="btn btn-success btn-sm" title="View Invoice"
                                                   target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <a v-if="ledger.type === 'purchase_return'" :href="route('purchase-return.show', ledger.id)" class="btn btn-success btn-sm" title="View Return Invoice"
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
                                            <td class="text-end">{{ parseFloat(parseFloat(data.total_adjustment) + parseFloat(data.total_discount)).toFixed(2) }}</td>
                                            <td></td>
                                            <td class="print-none"></td>
                                        </tr>

                                        </tbody>
                                    </table>
                                    <h5 class="text-end">{{ lang.closing_balance }}:
                                        {{
                                            (data.party_balance < 0) ?
                                                Number.parseFloat(Math.abs(data.party_balance)).toFixed(2)
                                                + ' ' +
                                                lang.payable :
                                                Number.parseFloat(Math.abs(data.party_balance)).toFixed(2)
                                                + ' ' +
                                                lang.receivable
                                        }}
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
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import VSelect from "vue-select";
import 'vue-select/dist/vue-select.css'
import 'vue-select/dist/vue-select'
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import * as _formatter from '@/Helpers/formatter'
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";

export default {
    name: "SupplierLedger",
    components: {
        BreezeAuthenticatedLayout,
        VSelect,
        PrintIcon,
        PrintDetails,
        RefreshIcon,
    },
    props: {
        parties: Array,
        data: Object
    },

    computed: {
        formatter() {
            return _formatter
        }
    },

    data() {
        return {
            lang:this.$page.props.lang,
            party_id: null,
            from_date: null,
            to_date: null,
            search: false,
            processing: false,
            opening_balance: 0,
        }
    },

    methods: {
        getLedgerDetails() {
            if (!this.party_id) {
                alert('Please select supplier!')
                return
            }
            this.processing = true
            this.search = true
            this.$inertia.visit('/report/supplier-ledger', {
                method: 'get',
                data: {
                    search: true,
                    party_id: this.party_id,
                    from_date: this.from_date,
                    to_date: this.to_date,
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
            let _balance = parseFloat(localStorage.getItem("supplier_balance")) || 0
            if (ledger.type === 'purchase') {
                _balance -= (parseFloat(ledger.grand_total) - parseFloat(ledger.paid))
            }
            else if (ledger.type === 'purchase_return') {
                _balance -= (parseFloat(ledger.return_paid) - parseFloat(ledger.return_grand_total));
            }
            else if (ledger.type === 'due_manage'){
                if (ledger.amount >= 0) {
                    _balance -= parseFloat(ledger.amount);
                }else{
                    _balance += parseFloat(Math.abs(ledger.amount));
                }
            }

            localStorage.setItem('supplier_balance', _balance)
            let balanceStatus = _balance >= 0 ? this.lang.receivable : this.lang.payable
            return Number.parseFloat(Math.abs(_balance)).toFixed(2) + ' ' + balanceStatus
        },

        getOpeningBalance() {
            let opening_balance = 0;
            if(this.data.total_debit < this.data.total_credit) {
                opening_balance = (parseFloat(this.data.party_balance) + (parseFloat(this.data.total_debit) - parseFloat(this.data.total_credit)));
            }
            else {
                opening_balance = (parseFloat(this.data.party_balance) - (parseFloat(this.data.total_credit) - parseFloat(this.data.total_debit)));
            }
            localStorage.setItem('supplier_balance', opening_balance)
            this.opening_balance = opening_balance
        },

    },

    mounted() {
        const queryString = window.location.search;
        const parameters = new URLSearchParams(queryString);
        if(parameters.get('search')){
            this.search = true
            this.party_id = parameters.get('party_id')
            this.from_date = parameters.get('from_date')
            this.to_date = parameters.get('to_date')
            this.getOpeningBalance()
        }
    }
}
</script>

<style scoped>

</style>
