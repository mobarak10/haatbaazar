<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container unit">
                <print-details />
                <!-- table -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ lang.sale_report }}</h5>
                            <div>
                                <refresh-icon :Url="route('report.sale-report')" />
                                <print-icon />
                            </div>
                        </div>
                    </div>

                    <!-- search form start -->
                    <div class="card-body print-none">
                        <form @submit.prevent="getSearchedSaleData" class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <from-date ref="fromDate" />
                                    </div>
                                    <div class="col-md-4">
                                        <to-date ref="toDate" />
                                    </div>
                                    <div class="col-md-4">
                                        <showroom ref="showroom" />
                                    </div>
                                    <div class="col-md-4">
                                        <mokam ref="mokam" />
                                    </div>
                                    <div class="col-md-4">
                                        <Customer ref="customer" />
                                    </div>
                                    <div class="col-md-2" style="padding-top: 30px">
                                        <search-button :processing="processing" />
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div v-if="records['sales']" class="mt-2 row col-md-12">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th>{{ lang.date }}</th>
                                                <th>{{ lang.voucher_number }}</th>
                                                <th>{{ lang.customer }}</th>
                                                <th class="text-end">{{ lang.total }}</th>
                                                <th class="text-end">{{ lang.purchase_price }}</th>
                                                <th class="text-end">{{ lang.profit }}/{{ lang.loss }}</th>
                                                <th class="text-end print-none">{{ lang.action }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr v-for="(sale, index) in records['sales'].data" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ formatter.dateFormat(sale.date) }}</td>
                                                <td>{{ sale.invoice_no }}</td>
                                                <td>{{ sale.party?.name }} ({{ sale.party?.mokam?.name }})</td>
                                                <td class="text-end">{{ Number.parseFloat(sale.sale_amount).toFixed(2) }}</td>
                                                <td class="text-end">{{ Number.parseFloat(sale.total_purchase_price).toFixed(2) }}</td>
                                                <td
                                                    v-if="sale.sale_amount >= sale.total_purchase_price"
                                                    class="text-end"
                                                >
                                                    {{ Number.parseFloat(sale.sale_amount - sale.total_purchase_price).toFixed(2) }} ({{ lang.profit }})
                                                </td>
                                                <td
                                                    v-else
                                                    class="text-end"
                                                >
                                                    {{ Number.parseFloat(sale.total_purchase_price - sale.sale_amount).toFixed(2) }} ({{ lang.loss }})
                                                </td>

                                                <td class="text-end print-none">
                                                    <show-icon :Url="route('sale.show', sale.id)" />
                                                </td>
                                            </tr>
                                            <tr v-if="records['sales'].length === 0">
                                                <td colspan="8" class="text-center">No sale available</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end">
                                                    {{ lang.total }}
                                                </td>
                                                <td class="text-end">
                                                    {{ Number.parseFloat(total_sale_amount).toFixed(2) }}
                                                </td>
                                                <td></td>
                                                <td class="text-end">
                                                    {{ Number.parseFloat(total_loss_profit).toFixed(2) }}
                                                </td>
                                                <td class="print-none"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <pagination :links="records['sales'].links" />
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
import * as _formatter from '@/Helpers/formatter'
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import VSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import 'vue-select/dist/vue-select';
import Pagination from "@/Components/Pagination";
import Mokam from "@/Pages/ReusableComponent/Customer/Mokam";
import Customer from "@/Pages/ReusableComponent/Customer/Customer";
import SearchButton from "@/Pages/ReusableComponent/Button/SearchButton";
import FromDate from "@/Pages/ReusableComponent/Date/FromDate";
import ToDate from "@/Pages/ReusableComponent/Date/ToDate";
import Showroom from "@/Pages/ReusableComponent/Showroom/Showroom";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import ShowIcon from "@/Pages/ReusableComponent/Icon/ShowIcon";
export default {
    name: "Index",
    props: {
        records: Array,
        total_loss_profit: Number,
        total_sale_amount: Number,
    },
    computed: {
        formatter() {
            return _formatter
        }
    },
    components: {
        BreezeAuthenticatedLayout,
        PrintDetails,
        VSelect,
        Pagination,
        SearchButton,
        FromDate,
        ToDate,
        Showroom,
        Mokam,
        Customer,
        RefreshIcon,
        PrintIcon,
        ShowIcon,
    },
    data() {
        return{
            allCustomers: this.records['customers'],
            lang:this.$page.props.lang,
            processing: false,
        }
    },

    methods: {
        getSearchedSaleData() {
            this.processing = true
            this.$inertia.visit('/report/sale-report', {
                method: 'get',
                data: {
                    search: true,
                    from_date: this.$refs.fromDate.from_date,
                    to_date: this.$refs.toDate.to_date,
                    showroom_id: this.$refs.showroom.showroom_id,
                    party_id: this.$refs.customer.party_id,
                    mokam_id: this.$refs.mokam.mokamId,
                },
                preserveState: true,
                onSuccess: page => {
                    this.processing = false
                },
                onFinish: visit => {
                    this.processing = false
                },
            })
        },
    },

    mounted() {
    }
}
</script>

<style scoped>

</style>
