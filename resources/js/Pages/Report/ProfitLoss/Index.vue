<template>
    <div>
        <breeze-authenticated-layout>
            <div class="dashboard">
                <div class="container">
                    <print-details />
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{ lang.profit_loss }} {{ lang.report }}</h5>

                                <div>
                                    <refresh-icon :Url="route('report.profit-loss')" />
                                    <print-icon />
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="getSearchedProfitLossData" class="row print-none">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label" for="from_date">{{ lang.from_date }}</label>
                                            <input type="date" id="from_date" v-model="from_date" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label" for="to_date">{{ lang.to_date }}</label>
                                            <input type="date" id="to_date" v-model="to_date" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="showroom" class="form-label">{{ lang.showroom }}</label>
                                            <v-select
                                                id="showroom"
                                                :options="data.showrooms"
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

                                        <div class="col-md-2" style="padding-top: 30px">
                                            <button type="submit" :disabled="processing"  class="btn btn-primary" :title="lang.search">
                                                <i class="fa fa-search"></i>
                                                {{ lang.search}}
                                                <span
                                                    v-if="processing"
                                                    class="spinner-border spinner-border-sm"
                                                    role="status"
                                                    aria-hidden="true"
                                                ></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row mt-4">
                                <!-- total sale amount -->
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5>{{ lang.sales }}</h5>
                                            <p>{{ Number.parseFloat(data.total_sales_amount).toFixed(2) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- total sale amount -->

                                <!-- total purchase amount -->
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5>{{ lang.purchase }}</h5>
                                            <p>{{ Number.parseFloat(data.total_purchase_amount).toFixed(2) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- total purchase amount -->

                                <!-- total gross profit amount -->
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5>{{ lang.gross }} {{ lang.profit }}</h5>
                                            <p>{{ Number.parseFloat(data.total_sales_amount - data.total_purchase_amount).toFixed(2) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- total gross profit amount -->

                                <!-- total sale return amount -->
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5>{{ lang.sales_return }}</h5>
                                            <p>{{ Number.parseFloat(data.total_sales_return_sale_amount).toFixed(2) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- total sale return amount -->

                                <!-- total sale return purchase amount -->
                                <div class="col-md-3 mt-2">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5>{{ lang.sales_return }} {{ lang.purchase_price }}</h5>
                                            <p>{{ Number.parseFloat(data.total_sales_return_purchase_amount).toFixed(2) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- total sale return purchase amount -->

                                <!-- total sale return profit amount -->
                                <div class="col-md-3 mt-2">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5>{{ lang.sales_return }} {{ lang.profit }}</h5>
                                            <p>{{ Number.parseFloat(data.total_sales_return_sale_amount - data.total_sales_return_purchase_amount).toFixed(2) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- total sale return profit amount -->

                                <!-- total expense amount -->
                                <div class="col-md-3 mt-2">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5>{{ lang.expense }}</h5>
                                            <p>{{ Number.parseFloat(data.total_expenses_amount).toFixed(2) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- total expense amount -->

                                <!-- total profit/loss amount -->
                                <div class="col-md-3 mt-2">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5>{{ lang.total }} {{ totalProfitLoss() >= 0 ? lang.profit : lang.loss }}</h5>
                                            <p>{{ Math.abs(totalProfitLoss()) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- total profit/loss amount -->
                            </div>

                            <div class="row">
                                <!-- Chart -->
                                <div class="col-12">
                                    <div class="my-5 chartjs">
                                        <div class="row">
                                            <!-- bar chart -->
                                            <div class="col-6">
                                                <canvas id="bar-chart"></canvas>
                                            </div>
                                            <!-- bar chart -->

                                            <!-- doughnut chart -->
                                            <div class="col-6">
                                                <div style="height: 309px;">
                                                    <canvas id="doughnut-chart"></canvas>
                                                </div>
                                            </div>
                                            <!-- doughnut chart -->
                                        </div>
                                    </div>
                                </div>
                                <!-- End Chart -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import Chart from "chart.js/auto";
import VSelect from "vue-select";
import 'vue-select/dist/vue-select';
import 'vue-select/dist/vue-select.css'
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";

export default {
    name: "Index",
    components: {
        BreezeAuthenticatedLayout,
        PrintDetails,
        VSelect,
        RefreshIcon,
        PrintIcon,
    },
    props: {
        data: Object,
    },
    data() {
        return{
            _barChart: null,
            _doughnutChart: null,
            lang: this.$page.props.lang,
            from_date: new Date().toISOString().substr(0, 10),
            to_date: new Date().toISOString().substr(0, 10),
            showroom_id: null,
            processing: false,
        }
    },
    methods: {
        getSearchedProfitLossData() {
            this.processing = true
            this.$inertia.visit('/report/profit-loss', {
                method: 'get',
                data: {
                    search: true,
                    from_date: this.from_date,
                    to_date: this.to_date,
                    showroom_id: this.showroom_id,
                },
                preserveState: true,
                onSuccess: page => {
                    this.barChart()
                    this.doughnutChart()
                    this.processing = false
                },
                onFinish: visit => {
                    this.processing = false
                },
            })
        },

        totalProfitLoss() {
            let grossProfit = parseFloat(this.data.total_sales_amount )
                              - parseFloat(this.data.total_purchase_amount)
             let returnProfit = parseFloat(this.data.total_sales_return_sale_amount)
                                - parseFloat(this.data.total_sales_return_purchase_amount)

            return parseFloat(grossProfit - (returnProfit + this.data.total_expenses_amount)).toFixed(2)
        },

        barChart() {
            if(this._barChart) {
                this._barChart.destroy()
            }

            let ctx = document.getElementById('bar-chart').getContext('2d');
            this._barChart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'bar',

                // The data for our dataset
                data: {
                    labels: [
                        this.lang.sale
                        + ' ' + Number.parseFloat(this.data.total_sales_amount).toFixed(2),
                        this.lang.purchase
                        + ' ' + Number.parseFloat(this.data.total_purchase_amount).toFixed(2),
                        this.lang.sale_return
                        + ' ' + Number.parseFloat(this.data.total_sales_return_sale_amount).toFixed(2),
                        this.lang.sale_return + ' ' + this.lang.purchase_price
                        + ' ' + Number.parseFloat(this.data.total_sales_return_purchase_amount).toFixed(2),
                        this.lang.expense
                        + ' ' + Number.parseFloat(this.data.total_expenses_amount).toFixed(2),
                    ],
                    datasets: [{
                        label: 'Overall Report',
                        backgroundColor: 'rgb(23, 162, 184)',
                        borderColor: 'rgb(23, 162, 184)',
                        data: [
                            Number.parseFloat(this.data.total_sales_amount).toFixed(2),
                            Number.parseFloat(this.data.total_purchase_amount).toFixed(2),
                            Number.parseFloat(this.data.total_sales_return_sale_amount).toFixed(2),
                            Number.parseFloat(this.data.total_sales_return_purchase_amount).toFixed(2),
                            Number.parseFloat(this.data.total_expenses_amount).toFixed(2),
                        ]
                    }]
                },

                // Configuration options go here
                options: {}
            });
            // End purchase chart
        },

        doughnutChart() {
            // this chart for purchase
            if(this._doughnutChart != null) {
                this._doughnutChart.destroy()
            }
            let ctx = document.getElementById('doughnut-chart').getContext('2d');
            this._doughnutChart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'doughnut',

                // The data for our dataset
                data: {
                    labels: [
                        this.lang.sale
                        + ' ' + Number.parseFloat(this.data.total_sales_amount).toFixed(2),
                        this.lang.purchase
                        + ' ' + Number.parseFloat(this.data.total_purchase_amount).toFixed(2),
                        this.lang.sale_return
                        + ' ' + Number.parseFloat(this.data.total_sales_return_sale_amount).toFixed(2),
                        this.lang.sale_return + ' ' + this.lang.purchase_price
                        + ' ' + Number.parseFloat(this.data.total_sales_return_purchase_amount).toFixed(2),
                        this.lang.expense
                        + ' ' + Number.parseFloat(this.data.total_expenses_amount).toFixed(2),
                    ],
                    datasets: [{
                        label: 'Overall Report',
                        data: [
                            Number.parseFloat(this.data.total_sales_amount).toFixed(2),
                            Number.parseFloat(this.data.total_purchase_amount).toFixed(2),
                            Number.parseFloat(this.data.total_sales_return_sale_amount).toFixed(2),
                            Number.parseFloat(this.data.total_sales_return_purchase_amount).toFixed(2),
                            Number.parseFloat(this.data.total_expenses_amount).toFixed(2),
                        ],
                        backgroundColor: [
                            'rgb(0, 204, 204)',
                            'rgb(255, 99, 132)',
                            'rgb(46, 139, 87)',
                            'rgb(100, 100, 100)',
                            'rgb(54, 162, 235)',
                        ],
                        hoverOffset: 4
                    }]
                },

                // Configuration options go here
                options: {
                    maintainAspectRatio: false
                }
            });
            // End purchase chart
        },
    },

    mounted() {
        this.barChart()
        this.doughnutChart()
    }
}
</script>

<style scoped>

</style>
