<template>
    <breeze-authenticated-layout>
            <div class="dashboard">
                <div class="container">
                    <div class="row">
                        <div class="mt-3 col-12 col-sm-6 col-lg-4 mt-lg-0">
                            <a href="#" class="text-dark text-decoration-none">
                                <div class="content-box">
                                    <div class="content">
                                        <i class="bi bi-currency-dollar"></i>
                                        <div>
                                            <small :title="lang.total_cash">{{ lang.total_cash }} ({{ lang.bdt }})</small>
                                            <h3 class="m-0">
                                                {{ Number.parseFloat(data.cash_balance).toFixed(2) }}
                                            </h3>
                                            <small>{{ lang.sum_of_all_cash_balance }}</small>
                                        </div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                                        <path fill="#17a2b8" fill-opacity="1"
                                              d="M0,224L14.1,218.7C28.2,213,56,203,85,213.3C112.9,224,141,256,169,256C197.6,256,226,224,254,192C282.4,160,311,128,339,138.7C367.1,149,395,203,424,224C451.8,245,480,235,508,208C536.5,181,565,139,593,117.3C621.2,96,649,96,678,117.3C705.9,139,734,181,762,181.3C790.6,181,819,139,847,133.3C875.3,128,904,160,932,181.3C960,203,988,213,1016,218.7C1044.7,224,1073,224,1101,192C1129.4,160,1158,96,1186,101.3C1214.1,107,1242,181,1271,229.3C1298.8,277,1327,299,1355,293.3C1383.5,288,1412,256,1426,240L1440,224L1440,320L1425.9,320C1411.8,320,1384,320,1355,320C1327.1,320,1299,320,1271,320C1242.4,320,1214,320,1186,320C1157.6,320,1129,320,1101,320C1072.9,320,1045,320,1016,320C988.2,320,960,320,932,320C903.5,320,875,320,847,320C818.8,320,791,320,762,320C734.1,320,706,320,678,320C649.4,320,621,320,593,320C564.7,320,536,320,508,320C480,320,452,320,424,320C395.3,320,367,320,339,320C310.6,320,282,320,254,320C225.9,320,198,320,169,320C141.2,320,113,320,85,320C56.5,320,28,320,14,320L0,320Z">
                                        </path>
                                    </svg>
                                </div>
                            </a>
                        </div>
                        <div class="mt-3 col-12 col-sm-6 mt-lg-0 col-lg-4">
                            <a href="#" class="text-dark text-decoration-none">
                                <div class="content-box">
                                    <div class="content">
                                        <i class="bi bi-credit-card"></i>
                                        <div>
                                            <small :title="lang.total_bank_balance">{{ lang.total_bank_balance }} ({{ lang.bdt }})</small>
                                            <h3 class="m-0">
                                                {{ Number.parseFloat(data.bank_balance).toFixed(2) }}
                                            </h3>
                                            <small>{{ lang.sum_of_all_bank_balance}}</small>
                                        </div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                                        <path fill="#6f42c1" fill-opacity="1"
                                              d="M0,224L14.1,218.7C28.2,213,56,203,85,208C112.9,213,141,235,169,202.7C197.6,171,226,85,254,80C282.4,75,311,149,339,192C367.1,235,395,245,424,229.3C451.8,213,480,171,508,176C536.5,181,565,235,593,224C621.2,213,649,139,678,144C705.9,149,734,235,762,229.3C790.6,224,819,128,847,96C875.3,64,904,96,932,144C960,192,988,256,1016,282.7C1044.7,309,1073,299,1101,298.7C1129.4,299,1158,309,1186,309.3C1214.1,309,1242,299,1271,266.7C1298.8,235,1327,181,1355,186.7C1383.5,192,1412,256,1426,288L1440,320L1440,320L1425.9,320C1411.8,320,1384,320,1355,320C1327.1,320,1299,320,1271,320C1242.4,320,1214,320,1186,320C1157.6,320,1129,320,1101,320C1072.9,320,1045,320,1016,320C988.2,320,960,320,932,320C903.5,320,875,320,847,320C818.8,320,791,320,762,320C734.1,320,706,320,678,320C649.4,320,621,320,593,320C564.7,320,536,320,508,320C480,320,452,320,424,320C395.3,320,367,320,339,320C310.6,320,282,320,254,320C225.9,320,198,320,169,320C141.2,320,113,320,85,320C56.5,320,28,320,14,320L0,320Z">
                                        </path>
                                    </svg>
                                </div>
                            </a>
                        </div>
                        <div class="mt-3 col-12 col-sm-6 mt-lg-0 col-lg-4">
                            <a href="#" class="text-dark text-decoration-none">
                                <div class="content-box">
                                    <div class="content">
                                        <i class="bi bi-chat-right-text-fill"></i>
                                        <div>
                                            <small :title="lang.total_stock
                                            ">{{ lang.total_stock }} ({{ lang.bdt }})</small>
                                            <h3 class="m-0">{{ Number.parseFloat(data.total_product_price).toFixed(2) }}</h3>
                                            <small>{{lang.total_amount_of_all_product}}</small>
                                        </div>
                                    </div>


                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                                        <path fill="#1CAF9A" fill-opacity="1"
                                              d="M0,256L14.1,234.7C28.2,213,56,171,85,170.7C112.9,171,141,213,169,208C197.6,203,226,149,254,160C282.4,171,311,245,339,282.7C367.1,320,395,320,424,272C451.8,224,480,128,508,106.7C536.5,85,565,139,593,160C621.2,181,649,171,678,170.7C705.9,171,734,181,762,181.3C790.6,181,819,171,847,138.7C875.3,107,904,53,932,80C960,107,988,213,1016,240C1044.7,267,1073,213,1101,208C1129.4,203,1158,245,1186,234.7C1214.1,224,1242,160,1271,112C1298.8,64,1327,32,1355,53.3C1383.5,75,1412,149,1426,186.7L1440,224L1440,320L1425.9,320C1411.8,320,1384,320,1355,320C1327.1,320,1299,320,1271,320C1242.4,320,1214,320,1186,320C1157.6,320,1129,320,1101,320C1072.9,320,1045,320,1016,320C988.2,320,960,320,932,320C903.5,320,875,320,847,320C818.8,320,791,320,762,320C734.1,320,706,320,678,320C649.4,320,621,320,593,320C564.7,320,536,320,508,320C480,320,452,320,424,320C395.3,320,367,320,339,320C310.6,320,282,320,254,320C225.9,320,198,320,169,320C141.2,320,113,320,85,320C56.5,320,28,320,14,320L0,320Z">
                                        </path>
                                    </svg>


                                </div>
                            </a>
                        </div>

                        <!-- statistics -->
                        <div class="col-12">
                            <div class="mt-4 statistics-wrapper">
                                <div class="row">
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <div class="mt-3 rounded shadow-sm card">
                                            <div class="card-header ">
                                                <i class="bi bi-cart-plus-fill"></i>
                                                <div>
                                                    <p class="card-category">{{ lang.sales }} ({{ lang.bdt }})</p>
                                                    <h3 class="mb-1 card-title">{{ Number.parseFloat(data.today_sale_amount).toFixed(2) }}</h3>
                                                </div>
                                            </div>
                                            <div class="footer">
                                                <i class="bi bi-info-circle-fill"></i>
                                                <span>{{ lang.sum_of_today_sell }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <div class="mt-3 rounded shadow-sm card">
                                            <div class="card-header ">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                                <div>
                                                    <p class="card-category">{{ lang.sale_return }} ({{ lang.bdt }})</p>
                                                    <h3 class="mb-1 card-title">{{ Number.parseFloat(data.today_sale_return_amount).toFixed(2) }}</h3>
                                                </div>
                                            </div>
                                            <div class="footer">
                                                <i class="bi bi-info-circle-fill"></i>
                                                <span>{{lang.sum_of_today_sell_return}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <div class="mt-3 rounded shadow-sm card">
                                            <div class="card-header ">
                                                <i class="bi bi-currency-euro"></i>
                                                <div>
                                                    <p class="card-category">{{lang.customer_due}}({{lang.bdt}})</p>
                                                    <h3 class="mb-1 card-title">{{ Number.parseFloat(data.customer_due).toFixed(2) }}</h3>
                                                </div>
                                            </div>
                                            <div class="footer">
                                                <i class="bi bi-info-circle-fill"></i>
                                                <span>{{lang.sum_of_total_customer_due}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <div class="mt-3 rounded shadow-sm card">
                                            <div class="card-header ">
                                                <i class="bi bi-graph-down"></i>
                                                <div>
                                                    <p class="card-category">{{lang.expense}} ({{lang.bdt}})</p>
                                                    <h3 class="mb-1 card-title">{{ Number.parseFloat(data.expense).toFixed(2) }}</h3>
                                                </div>
                                            </div>
                                            <div class="footer">
                                                <i class="bi bi-info-circle-fill"></i>
                                                <span>{{lang.sum_of_today_expense}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <div class="mt-3 rounded shadow-sm card">
                                            <div class="card-header ">
                                                <i class="bi bi-bag-fill"></i>
                                                <div>
                                                    <p class="card-category">{{lang.purchase}}({{lang.bdt}})</p>
                                                    <h3 class="mb-1 card-title">{{ Number.parseFloat(data.purchase).toFixed(2) }}</h3>
                                                </div>
                                            </div>
                                            <div class="footer">
                                                <i class="bi bi-info-circle-fill"></i>
                                                <span>{{lang.sum_of_today_purchase}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <div class="mt-3 rounded shadow-sm card">
                                            <div class="card-header ">
                                                <i class="bi bi-clipboard-minus"></i>
                                                <div>
                                                    <p class="card-category">{{lang.supplier_due}}({{lang.bdt}})</p>
                                                    <h3 class="mb-1 card-title">{{ Number.parseFloat(Math.abs(data.supplier_due)).toFixed(2) }}</h3>
                                                </div>
                                            </div>
                                            <div class="footer">
                                                <i class="bi bi-info-circle-fill"></i>
                                                <span>{{lang.sum_of_total_supplier_due}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End statistics -->

                        <!-- Chart -->
                        <div class="col-12">
                            <div class="my-5 chartjs">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <canvas id="purchase-chart"></canvas>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <canvas id="sale-chart"></canvas>
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
    </breeze-authenticated-layout>
</template>

<script>
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated';
    import Chart from 'chart.js/auto';
    export default {
        components: {
            BreezeAuthenticatedLayout,
        },
        props: {
            auth: Object,
            errors: Object,
            data: Object,
        },
        data() {
            return{
                lang: this.$page.props.lang
            }
        },
        methods: {
            purchaseChartDetails() {
                    // this chart for purchase
                    let ctx = document.getElementById('purchase-chart').getContext('2d');
                    new Chart(ctx, {
                    // The type of chart we want to create
                    type: 'bar',

                    // The data for our dataset
                    data: {
                    labels: Object.keys(this.data.purchase_monthly_data),
                    datasets: [{
                        label: 'Purchase',
                        backgroundColor: 'rgb(23, 162, 184)',
                        borderColor: 'rgb(23, 162, 184)',
                        data: Object.values(this.data.purchase_monthly_data)
                    }]
                },

                    // Configuration options go here
                    options: {}
                });
                    // End purchase chart
            },

            saleChartDetails() {
                // this chart for Expense
                let ctx = document.getElementById('sale-chart').getContext('2d');
                new Chart(ctx, {
                    // The type of chart we want to create
                    type: 'bar',

                    // The data for our dataset
                    data: {
                        labels: Object.keys(this.data.sale_monthly_data),
                        datasets: [{
                            label: 'Sale',
                            backgroundColor: 'rgb(23, 162, 184)',
                            borderColor: 'rgb(23, 162, 184)',
                            data: Object.values(this.data.sale_monthly_data)
                        }]
                    },

                    // Configuration options go here
                    options: {}
                });
                // End Expense chart
            },

            dailyChartDetails() {
                // this chart for Daily Chart
                let ctx = document.getElementById('daily-chart').getContext('2d');
                new Chart(ctx, {
                    // The type of chart we want to create
                    type: 'line',

                    // The data for our dataset
                    data: {
                        labels: Object.keys(this.data.daily_sale_data),
                        datasets: [{
                            label: 'Sale',
                            backgroundColor: 'transparent',
                            borderColor: 'rgb(0, 204, 204)',
                            data: Object.values(this.data.daily_sale_data),
                        },
                            {
                                label: 'Purchase',
                                backgroundColor: 'transparent',
                                borderColor: 'rgb(50, 175, 154)',
                                data: Object.values(this.data.daily_purchase_data),
                            }
                        ]
                    },

                    // Configuration options go here
                    options: {}
                });
                // End Daily Chart
            },
        },

        mounted() {
            this.purchaseChartDetails()
            this.saleChartDetails()
            this.dailyChartDetails()
        }
    }
</script>
