<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container unit">
                <print-details />
                <!-- table -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ lang.stock }} {{ lang.report }}</h5>
                            <div>
                                <refresh-icon :Url="route('report.stock-report')" />
                                <print-icon />
                            </div>
                        </div>
                    </div>

                    <!-- search form start -->
                    <div class="card-body">
                        <form @submit.prevent="getSearchedStockData" class="row print-none">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label" for="from_date">{{ lang.date }}</label>
                                        <input type="date" id="from_date" v-model="date" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label for="product" class="form-label">{{ lang.product }}</label>
                                        <v-select
                                            id="product"
                                            :options="records['products']"
                                            :reduce="product => product.id"
                                            :placeholder="lang.select_product"
                                            v-model="product_id"
                                            label="name">
                                            <template slot="option" slot-scope="option">
                                                <span class="fa" :class="option.icon"></span>
                                                {{ option.name }}
                                            </template>
                                        </v-select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="showroom" class="form-label">{{ lang.showroom }}</label>
                                        <v-select
                                            id="showroom"
                                            :options="records['showrooms']"
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
                                    <div class="col-md-2 mt-4 text-right">
                                        <search-button :processing="processing" />
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div v-if="records['searchProducts']" class="mt-2 row col-md-12">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th>{{ lang.name }}</th>
                                                <th class="text-end">{{ lang.current_stock }}</th>
                                                <th class="text-end">{{ lang.sale }}</th>
                                                <th class="text-end">{{ lang.sale_return }}</th>
                                                <th class="text-end">{{ lang.purchase }}</th>
                                                <th class="text-end">{{ lang.purchase_return }}</th>
                                                <th class="text-end">{{ lang.damage }}</th>
                                                <th class="text-end">{{ lang.production }}</th>
                                                <th class="text-end">{{ formatter.dateFormat(date) }} {{ lang.stock }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr v-for="(product, index) in records['searchProducts'].data" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ product.name }}</td>
                                                <td class="text-end">
                                                    {{ showroom_id ?
                                                    calculateCurrentQuantity(product.total_product_quantity_showroom_wise, product.unit)
                                                    : calculateCurrentQuantity(product.total_quantity, product.unit) }}
                                                </td>
                                                <td class="text-end">{{ calculateQuantity(product.sale_details, product.unit) }}</td>
                                                <td class="text-end">{{ calculateQuantity(product.sale_return_details, product.unit) }}</td>
                                                <td class="text-end">{{ calculateQuantity(product.purchase_details, product.unit) }}</td>
                                                <td class="text-end">{{ calculateQuantity(product.purchase_return_details, product.unit) }}</td>
                                                <td class="text-end">{{ calculateQuantity(product.damage_details, product.unit) }}</td>
                                                <td class=text-end>{{ calculateQuantity(product.production_details, product.unit) }}</td>
                                                <td class="text-end">{{ calculateSearchDateQuantity(product) }}</td>
                                            </tr>
                                            <tr v-if="records['searchProducts'].length === 0">
                                                <td colspan="8" class="text-center">No product available</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <pagination :links="records['searchProducts'].links" />
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
import * as _formatter from "@/Helpers/formatter";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import VSelect from "vue-select";
import 'vue-select/dist/vue-select.css';
import 'vue-select/dist/vue-select';
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import pagination from "@/Components/Pagination";
import SearchButton from "@/Pages/ReusableComponent/Button/SearchButton";
import convertToUpperUnit from "@/reusable/ConvertToUpperUnit";

export default {
    name: "Index",
    props: {
        records: Object,
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
        RefreshIcon,
        PrintIcon,
        pagination,
        SearchButton,
    },
    data() {
        return{
            lang:this.$page.props.lang,
            date: new Date().toISOString().substr(0, 10),
            showroom_id: null,
            product_id: null,
            processing: false,
        }
    },

    methods: {
        getSearchedStockData() {
            this.processing = true
            this.$inertia.visit('/report/stock-report', {
                method: 'get',
                data: {
                    search: true,
                    date: this.date,
                    showroom_id: this.showroom_id,
                    product_id: this.product_id,
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

        calculateQuantity(details, unit) {
            let totalQuantity = details.reduce((total, item) => {
                return (parseFloat(item.quantity) + parseFloat(total))
            }, 0)
            return convertToUpperUnit(totalQuantity, unit)
        },

        calculateCurrentQuantity(quantity, unit){
            return convertToUpperUnit(quantity, unit)
        },

        calculateSearchDateQuantity(product) {
            let currentQuantity = this.showroom_id
                ? product.total_product_quantity_showroom_wise
                : product.total_quantity
            let sale_quantity = this.calculateTotalQuantity(product.sale_details)
            let sale_return_quantity = this.calculateTotalQuantity(product.sale_return_details)
            let purchase_quantity = this.calculateTotalQuantity(product.purchase_details)
            let purchase_return_quantity = this.calculateTotalQuantity(product.purchase_return_details)
            let damage_quantity = this.calculateTotalQuantity(product.damage_details)
            let production_quantity = this.calculateTotalQuantity(product.production_details)
            let totalQuantity =  (
                (parseFloat(currentQuantity)
                + sale_quantity
                + purchase_return_quantity
                + production_quantity
                + damage_quantity)
                -
                (sale_return_quantity
                + purchase_quantity)
            )
            return convertToUpperUnit(totalQuantity, product.unit)
        },

        calculateTotalQuantity(details) {
            return details.reduce((total, item) => {
                if (item.type === 'finish_product'){
                    return parseFloat(total) - parseFloat(item.quantity)
                }else{
                    return parseFloat(total) + parseFloat(item.quantity)
                }
            }, 0)
        },

        initialValues() {
            if (this.records['search']) {
                this.date = this.records['date']
                this.product_id = this.records['product_id']
                this.showroom_id = this.records['showroom_id']
            }
        }
    },

    mounted() {
        this.initialValues()
    }
}
</script>

<style scoped>

</style>
