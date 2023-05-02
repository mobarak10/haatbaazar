<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container print-none">
                <ul class="mt-2 nav nav-tabs">
                    <li class="nav-item">
                        <inertia-link class="nav-link active" :href="route('stock.index')">{{ lang.all_records }}</inertia-link>
                    </li>
                </ul>
            </div>
            <div class="container sale">
                <PrintDetails />
                <!-- table -->
                <div class="card">
                    <div class="mx-3 d-flex justify-content-between align-items-center">
                        <!-- page title -->
                        <div class="mt-3 mb-2">
                            <h4 class="main-title">{{lang.product_stock}}</h4><br>
                        </div>

                        <div class="d-none d-print-block">
                            <h4 class="main-title">{{ showroom_id ? showrooms.find(showroom => showroom.id === showroom_id).name : '' }}</h4>
                        </div>
                        <div class="print-none">
                            <refresh-icon :Url="route('stock.index')" />
                            <search-icon Url="#stock-search-collapse" />
                            <print-icon />
                        </div>
                    </div>

                    <!-- content body -->
                    <div class="card-body">
                        <div class="print-none" :class="[search ? '' : 'collapse']" id="stock-search-collapse">
                            <form @submit.prevent="getSearchProduct" >
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <input type="hidden" v-model="search">
                                        <label for="category">{{lang.category}}</label>
                                        <v-select
                                            :options="categories"
                                            id="category"
                                            :reduce="category => category.id"
                                            :placeholder="lang.all_category"
                                            v-model="category_id"
                                            label="name">
                                            <template slot="option" slot-scope="option">
                                                <span class="fa" :class="option.icon"></span>
                                                {{ option.name }}
                                            </template>
                                        </v-select>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label for="brand">{{ lang.brand }}</label>
                                        <select name="brand_id" v-model="brand_id" class="form-control" id="brand">
                                            <option :value="null">{{ lang.all_brand }}</option>
                                            <option v-for="(brand, index) in brands" :value="brand.id" :key="index">
                                                {{ brand.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label for="name">{{ lang.product_name }}</label>
                                        <input type="text" id="name" name="name" v-model="name" class="form-control" :placeholder="lang.enter_product_name">
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label for="category">{{ lang.showroom }}</label>
                                        <select name="showroom_id" v-model="showroom_id" class="form-control" id="showroom_id">
                                            <option :value="null">{{ lang.all_showroom }}</option>
                                            <option v-for="(showroom, index) in showrooms" :value="showroom.id" :key="index">
                                                {{ showroom.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label for="type">{{ lang.product_type }}</label>
                                        <select name="type" v-model="type" id="type" class="form-control">
                                            <option :value="null">{{ lang.all_type }}</option>
                                            <option value="finish_product">Finish Product</option>
                                            <option value="raw_material">Raw Product</option>
                                            <option value="bag">Bag</option>
                                        </select>
                                    </div>

                                    <div class="mt-4 text-right col-md-2">
                                        <label>&nbsp;</label>
                                        <button type="submit" :disabled="processing" class="btn btn-primary" id="button-addon" title="search">
                                            <i class="fa fa-search"></i> {{ lang.search }}
                                            <span
                                                v-if="processing"
                                                class="spinner-border spinner-border-sm"
                                                role="status"
                                                aria-hidden="true"
                                            ></span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive-md">
                            <table class="table table-print-10p table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col" width="100">{{ lang.sl }}</th>
                                        <th scope="col">{{ lang.name }}</th>
                                        <th scope="col">{{ lang.category }}</th>
                                        <th scope="col">{{ lang.brand }}</th>
                                        <th class="text-end" scope="col">{{ lang.quantity }}</th>
                                        <th class="text-end" v-if="permissions.includes('current_stock-purchase_price')" scope="col">{{ lang.purchase_price }}</th>
                                        <th class="text-end" v-if="permissions.includes('current_stock-purchase_price')" scope="col">{{ lang.average_price }}</th>
                                        <th class="text-end" scope="col">{{ lang.sale_price }}</th>
                                        <th class="text-end" v-if="permissions.includes('current_stock-purchase_price')" scope="col">{{ lang.total }} {{ lang.purchase_price }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in allProducts.data" :key="index">
                                        <th
                                            scope="row"
                                            :class="item.total_quantity <= item.stock_alert ? 'table-danger' : ''">
                                            {{ allProducts.from + index }}
                                        </th>
                                        <td :class="item.total_quantity <= item.stock_alert ? 'table-danger' : ''">
                                            {{ item.name }}
                                        </td>
                                        <td :class="item.total_quantity <= item.stock_alert ? 'table-danger' : ''">
                                            {{ item.category_name }}
                                        </td>
                                        <td :class="item.total_quantity <= item.stock_alert ? 'table-danger' : ''">
                                            {{ item.brand_name }}
                                        </td>
                                        <td
                                            class="text-end"
                                            :class="item.total_quantity <= item.stock_alert ? 'table-danger' : ''">
                                            {{ quantityInUnit(item.total_quantity, item.unit_label, item.unit_relation) }}
                                        </td>
                                        <td
                                            class="text-end"
                                            :class="item.total_quantity <= item.stock_alert ? 'table-danger' : ''"
                                            v-if="permissions.includes('current_stock-purchase_price')">
                                            {{ item.purchase_price }}
                                        </td>
                                        <td
                                            class="text-end"
                                            :class="item.total_quantity <= item.stock_alert ? 'table-danger' : ''"
                                            v-if="permissions.includes('current_stock-purchase_price')">
                                            {{ Number.parseFloat(item.average_purchase_price).toFixed(2) }}
                                        </td>
                                        <td
                                            :class="item.total_quantity <= item.stock_alert ? 'table-danger' : ''"
                                            class="text-end">
                                            {{ item.sale_price }}
                                        </td>
                                        <td
                                            class="text-end"
                                            :class="item.total_quantity <= item.stock_alert ? 'table-danger' : ''"
                                            v-if="permissions.includes('current_stock-purchase_price')">
                                            {{ Number.parseFloat((item.total_quantity / item.divisor_number) * item.average_purchase_price).toFixed(2) }}
                                        </td>
                                    </tr>
                                    <tr v-if="permissions.includes('current_stock-purchase_price')">
                                        <td colspan="8" class="text-end">{{ lang.total }}</td>
                                        <td class="text-end">
                                            {{ Number.parseFloat(total_purchase_price).toFixed(2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="allProducts.links" />
                    <!-- pagination -->
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Button from "@/Components/Button";
import Pagination from '@/Components/Pagination'
import convertToUpperUnit from "@/reusable/ConvertToUpperUnit";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import SearchIcon from "@/Pages/ReusableComponent/Icon/SearchIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import priceQuantity from "@/reusable/PriceQuantity";
import VSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import 'vue-select/dist/vue-select'

export default {
    name: "Index",
    components: {
        Button,
        BreezeAuthenticatedLayout,
        Pagination,
        PrintDetails,
        RefreshIcon,
        SearchIcon,
        PrintIcon,
        VSelect,
    },
    props: {
        products: Object,
        categories: Array,
        brands: Array,
        showrooms: Array,
        data: Object,
        total_purchase_price: Number,
    },
    computed: {
        allProducts() {
            return this.products
        }
    },
    data() {
        return {
            permissions: this.$page.props.permissionNames,
            lang: this.$page.props.lang,
            category_id: null,
            showroom_id: null,
            brand_id: null,
            name: null,
            search: false,
            type: null,
            processing: false,
        }
    },
    methods: {
        quantityInUnit(quantity, unit_label, unit_relation) {
            return convertToUpperUnit(quantity, unit_label, unit_relation)
        },

        getSearchProduct() {
            this.processing = true
            this.search = true
            this.$inertia.visit('/stock', {
                method: 'get',
                data: {
                    search: this.search,
                    category_id: this.category_id,
                    showroom_id: this.showroom_id,
                    brand_id: this.brand_id,
                    name: this.name,
                    type: this.type,
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
