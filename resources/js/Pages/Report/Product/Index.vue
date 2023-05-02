<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container unit">
                <print-details :showroom_name="showroom_name" />
                <!-- table -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ lang.product }} {{ lang.report }}</h5>
                            <div>
                                <div class="d-none print-show">
                                    <p>{{ this.type.charAt(0).toUpperCase() + this.type.slice(1) }} {{ lang.report }}</p>
                                    <p>{{ this.from_date }} - {{ this.to_date }}</p>
                                </div>
                                <refresh-icon :Url="route('report.product-report')" />
                                <print-icon />
                            </div>
                        </div>
                    </div>

                    <!-- search form start -->
                    <div class="card-body">
                        <form @submit.prevent="getSearchedProductData" class="row print-none">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="showroom" class="form-label required">{{ lang.showroom }}</label>
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

                                    <div class="col-md-4">
                                        <label class="form-label" for="from_date">{{ lang.from_date }}</label>
                                        <input type="date" id="from_date" v-model="from_date" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label" for="to_date">{{ lang.to_date }}</label>
                                        <input type="date" id="to_date" v-model="to_date" class="form-control">
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

                                    <div class="col-md-4">
                                        <label for="type" class="form-label required">{{ lang.type }}</label>
                                        <select v-model="type" class="form-control" id="type">
                                            <option value="sale">Sale</option>
                                            <option value="purchase">Purchase</option>
                                        </select>
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

                        <div v-if="records['search_products']" class="mt-2 row col-md-12">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-sm">
                                        <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>{{ lang.product }}</th>
                                            <th>{{ lang.barcode }}</th>
                                            <th>{{ lang.size }}</th>
<!--                                            <th>{{ lang.brand }}</th>-->
                                            <th class="text-end">{{ lang.quantity }}</th>
                                            <th class="text-end">{{ lang.price }}</th>
                                            <th v-if="type == 'sale'" class="text-end">{{ lang.discount }}</th>
                                            <th class="text-end">{{ lang.total }} {{ lang.price }}</th>
                                            <th class="text-end" v-if="type == 'sale'">{{ lang.total }} {{ lang.purchase }} {{ lang.price }}</th>
                                            <th class="text-end" v-if="type == 'sale'">{{ lang.profit }}</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr v-for="(product, index) in records['search_products'].data" :key="index">
                                            <td class="text-center">{{ records['search_products'].from + index }}</td>
                                            <td>{{ product?.name }}</td>
                                            <td>{{ product?.barcode }}</td>
                                            <td>{{ product?.size }}</td>
<!--                                            <td>{{ product?.brand_name }}</td>-->
                                            <td class="text-end">{{ quantityConvertInUnit((product?.sale_details ?? product?.purchase_details), product?.unit_id) }}</td>
                                            <td class="text-end">{{ getPrice((product?.sale_details ?? product?.purchase_details), product) }}</td>
                                            <td v-if="type == 'sale'" class="text-end">{{ getDiscount(product?.sale_details) }}</td>
                                            <td class="text-end">{{ getPriceTotal((product?.sale_details ?? product?.purchase_details)) }}</td>
                                            <td v-if="type == 'sale'" class="text-end">{{ getPurchasePriceTotal(product) }}</td>
                                            <td v-if="type == 'sale'" class="text-end">
                                                {{ getTotalProfit(product) }}
                                            </td>
                                        </tr>
                                        <tr v-if="records['search_products'].length === 0">
                                            <td colspan="8" class="text-center">No product available</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <pagination :links="records['search_products'].links" />
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
import Pagination from "@/Components/Pagination";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import alert from "@/alert";
import upperConverter from "@/reusable/ConvertToUpperUnit";

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
        Pagination,
        RefreshIcon,
        PrintIcon,
    },
    data() {
        return{
            datas: [],
            lang:this.$page.props.lang,
            from_date: null,
            type: 'sale',
            to_date: null,
            showroom_id: null,
            product_id: null,
            processing: false,
        }
    },

    methods: {
        getSearchedProductData() {
            if (!this.showroom_id) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select showroom!'
                })
                return
            }
            this.processing = true
            this.$inertia.visit('/report/product-report', {
                method: 'get',
                data: {
                    search: true,
                    from_date: this.from_date,
                    to_date: this.to_date,
                    showroom_id: this.showroom_id,
                    product_id: this.product_id,
                    type: this.type,
                },
                preserveState: true,
                onSuccess: page => {
                    this.processing = false
                    this.showroom_name = this.records['showrooms'].find(showroom => showroom.id == this.showroom_id).name
                },
                onFinish: visit => {
                    this.processing = false
                },
            })
        },

        quantityConvertInUnit(sale_details, unit_id){
            const sumOfQuantity = sale_details.reduce((accumulator, currentValue) => {
                return accumulator + parseFloat(currentValue.quantity);
            }, 0);
            const unit = this.records['units'].find(unit => unit.id == unit_id)

            return upperConverter(sumOfQuantity, unit.label, unit.relation)
        },

        getPrice(details) {
            const sumOfPrice = details.reduce((accumulator, currentValue) => {
                return accumulator + parseFloat(currentValue?.sale_price ?? currentValue?.purchase_price);
            }, 0);

            return Number.parseFloat(sumOfPrice / details.length).toFixed(2)
        },

        getDiscount(details) {
            return  details.reduce((accumulator, currentValue) => {
                return accumulator + parseFloat(currentValue?.discount ?? 0);
            }, 0);
        },

        getPriceTotal(details) {
            let totalPrice = details.reduce((accumulator, currentValue) => {
                return accumulator + parseFloat(currentValue?.line_total);
            }, 0);
            return Number.parseFloat(totalPrice).toFixed(2)
        },

        getPurchasePriceTotal(product) {
            let totalPurchasePrice = product?.sale_details?.reduce((accumulator, currentValue) => {
                return accumulator + parseFloat((currentValue?.quantity / product?.divisor_number) * currentValue?.purchase_price);
            }, 0);
            return Number.parseFloat(totalPurchasePrice).toFixed(2)
        },

        getTotalProfit(product) {
            let totalPrice = product?.sale_details.reduce((accumulator, currentValue) => {
                return accumulator + parseFloat(currentValue?.line_total);
            }, 0);

            let totalPurchasePrice = product?.sale_details?.reduce((accumulator, currentValue) => {
                return accumulator + parseFloat((currentValue?.quantity / product?.divisor_number) * currentValue?.purchase_price);
            }, 0);

            return Number.parseFloat(parseFloat(totalPrice) - parseFloat(totalPurchasePrice)).toFixed(2)
        },
    },

    mounted(){
        const queryString = window.location.search;
        const parameters = new URLSearchParams(queryString);
        if(parameters){
            this.search = true
            this.from_date = parameters.get('from_date')
            this.to_date = parameters.get('to_date')
            this.showroom_id = parameters.get('showroom_id')
            this.product_id = parameters.get('product_id')
            this.type = parameters.get('type') ?? 'sale'
            if(parameters.get('showroom_id')) {
                this.showroom_name = this.records['showrooms'].find(showroom => showroom.id == parameters.get('showroom_id')).name
            }
        }
    }
}
</script>

<style scoped>

</style>
