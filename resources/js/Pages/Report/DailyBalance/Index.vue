<template>
    <div>
        <breeze-authenticated-layout>
            <div class="dashboard">
                <div class="container">
                    <print-details />
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{ lang.overall_report }}</h5>

                                <div>
                                    <print-icon />
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Chart -->
                                <div class="col-12">
                                    <div class="my-5 chartjs">
                                        <div class="row">
                                            <div class="col-6">
                                                <canvas id="overall-chart"></canvas>
                                            </div>

                                            <div class="col-6">
                                                <div style="height: 309px;">
                                                    <canvas id="overall-doughnut"></canvas>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <canvas id="daily-chart" class="mt-5"></canvas>
                                            </div>
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
import Chart from "chart.js/auto";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
export default {
    name: "Index",
    components: {
        BreezeAuthenticatedLayout,
        PrintDetails,
        PrintIcon,
    },
    props: {
        data: Object,
    },
    data() {
        return{
            lang: this.$page.props.lang
        }
    },
    methods: {
        overallChart() {
            // this chart for purchase
            let ctx = document.getElementById('overall-chart').getContext('2d');
            new Chart(ctx, {
                // The type of chart we want to create
                type: 'bar',

                // The data for our dataset
                data: {
                    labels: [
                        this.lang.customer
                        +' ' + this.lang.balance
                        + ' ' + Number.parseFloat(this.data.customer_balance).toFixed(2),
                        this.lang.supplier
                        +' ' + this.lang.balance
                        + ' ' + Number.parseFloat(this.data.supplier_balance).toFixed(2),
                        this.lang.cash
                        +' ' + this.lang.balance
                        + ' ' + Number.parseFloat(this.data.cash_balance).toFixed(2),
                        this.lang.bank
                        +' ' + this.lang.balance
                        + ' ' + Number.parseFloat(this.data.bank_balance).toFixed(2),
                        this.lang.stock
                        +' ' + this.lang.price
                        + ' ' + Number.parseFloat(this.data.total_stock).toFixed(2),
                        this.lang.damage
                        +' ' + this.lang.price
                        + ' ' + Number.parseFloat(this.data.total_damage).toFixed(2),
                    ],
                    datasets: [{
                        label: 'Overall Report',
                        backgroundColor: 'rgb(23, 162, 184)',
                        borderColor: 'rgb(23, 162, 184)',
                        data: [
                            Number.parseFloat(this.data.customer_balance).toFixed(2),
                            Number.parseFloat(this.data.supplier_balance).toFixed(2),
                            Number.parseFloat(this.data.cash_balance).toFixed(2),
                            Number.parseFloat(this.data.bank_balance).toFixed(2),
                            Number.parseFloat(this.data.total_stock).toFixed(2),
                            Number.parseFloat(this.data.total_damage).toFixed(2),
                        ]
                    }]
                },

                // Configuration options go here
                options: {}
            });
            // End purchase chart
        },

        overallDoughnut() {
            // this chart for purchase
            let ctx = document.getElementById('overall-doughnut').getContext('2d');
            new Chart(ctx, {
                // The type of chart we want to create
                type: 'doughnut',

                // The data for our dataset
                data: {
                    labels: [
                        this.lang.customer
                        +' ' + this.lang.balance
                        + ' ' + Number.parseFloat(this.data.customer_balance).toFixed(2),
                        this.lang.supplier
                        +' ' + this.lang.balance
                        + ' ' + Number.parseFloat(this.data.supplier_balance).toFixed(2),
                        this.lang.cash
                        +' ' + this.lang.balance
                        + ' ' + Number.parseFloat(this.data.cash_balance).toFixed(2),
                        this.lang.bank
                        +' ' + this.lang.balance
                        + ' ' + Number.parseFloat(this.data.bank_balance).toFixed(2),
                        this.lang.stock
                        +' ' + this.lang.price
                        + ' ' + Number.parseFloat(this.data.total_stock).toFixed(2),
                        this.lang.damage
                        +' ' + this.lang.price
                        + ' ' + Number.parseFloat(this.data.total_damage).toFixed(2),
                    ],
                    datasets: [{
                        label: 'Overall Report',
                        data: [
                            Number.parseFloat(this.data.customer_balance).toFixed(2),
                            Number.parseFloat(this.data.supplier_balance).toFixed(2),
                            Number.parseFloat(this.data.cash_balance).toFixed(2),
                            Number.parseFloat(this.data.bank_balance).toFixed(2),
                            Number.parseFloat(this.data.total_stock).toFixed(2),
                            Number.parseFloat(this.data.total_damage).toFixed(2),
                        ],
                        backgroundColor: [
                            'rgb(0, 204, 204)',
                            'rgb(255, 99, 132)',
                            'rgb(23, 162, 184)',
                            'rgb(100, 100, 100)',
                            'rgb(54, 162, 235)',
                            'rgb(255, 205, 86)',
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

        dailyBalanceChart() {
            // this chart for Daily Chart
            let ctx = document.getElementById('daily-chart').getContext('2d');
            new Chart(ctx, {
                // The type of chart we want to create
                type: 'line',

                // The data for our dataset
                data: {
                    labels: Object.keys(this.data.getDailyBalance),
                    datasets: [{
                        label: 'Daily Balance',
                        backgroundColor: 'transparent',
                        borderColor: 'rgb(0, 204, 204)',
                        data: Object.values(this.data.getDailyBalance),
                    }]
                },

                // Configuration options go here
                options: {}
            });
            // End Daily Chart
        },
    },

    mounted() {
        this.overallDoughnut()
        this.overallChart()
        this.dailyBalanceChart()
    }
}
</script>

<style scoped>

</style>
