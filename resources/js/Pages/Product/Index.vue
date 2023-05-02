<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('product.index')" :create-route="route('product.create')" />
            <div class="sale container">
                <PrintDetails />
                <!-- table -->
                <div class="card border-0">
                    <div class="mx-3 d-flex justify-content-between align-items-center">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_products }}</h4><br>
                            <!-- <small class="print-none">About 563,000,000 results (0.98 seconds)</small> -->
                        </div>
                        <!-- add -->

                        <div class="print-none">
                            <refresh-icon :Url="route('product.index')" />
                            <search-icon Url="#product-search-collapse" />
                            <print-icon />
                        </div>
                    </div>

                    <!-- content body -->
                    <div class="card-body p-0">
                        <div class="print-none" :class="[search ? '' : 'collapse']" id="product-search-collapse">
                            <form @submit.prevent="getSearchProduct" >
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <input type="hidden" v-model="search">
                                        <label for="category">{{lang.category}}</label>
                                        <select name="category_id" v-model="category_id" class="form-control" id="category">
                                            <option :value='null'>{{ lang.all_category }}</option>
                                            <option v-for="(category, index) in categories" :value="category.id" :key="index">
                                                {{ category.name }}
                                            </option>
                                        </select>
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
                            <table class="table custom-table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col" width="100">{{ lang.sl }}</th>
                                    <th scope="col">{{ lang.name }}</th>
                                    <th scope="col">{{ lang.category }}</th>
                                    <th scope="col">{{ lang.brand }}</th>
                                    <th scope="col">{{ lang.price_type }}</th>
                                    <th scope="col">{{ lang.unit }}</th>
                                    <th v-if="permissions.includes('product-purchase_price')" scope="col">{{ lang.purchase_price }}</th>
                                    <th scope="col">{{ lang.sale_price }}</th>
<!--                                    <th scope="col">{{ lang.type }}</th>-->
                                    <th scope="col">{{ lang.barcode }}</th>
                                     <th scope="col" width="100">{{ lang.action }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item,index) in products.data" :key="index">
                                    <th scope="row">{{ products.from + index }}</th>
                                    <td>{{ item.name }}</td>
                                    <td>{{ item.category_name }}</td>
                                    <td>{{ item.brand_name }}</td>
                                    <td>{{ item.price_type }}</td>
                                    <td>{{ item.unit_name }}</td>
                                    <td v-if="permissions.includes('product-purchase_price')">{{ item.purchase_price }}</td>
                                    <td>{{ item.sale_price }}</td>
                                    <td>{{ item.barcode ? item.barcode: 'N/A' }}</td>
                                    <td>
                                        <edit-icon :Url="route('product.edit', item.id)" />
                                        <delete-icon @click.prevent="deleteProduct(item.id)" />
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="products.links" />

                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Button from "@/Components/Button";
import Pagination from '@/Components/Pagination'
import alert from '@/alert'
import deleteAlert from "@/deleteAlert";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import SearchIcon from "@/Pages/ReusableComponent/Icon/SearchIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";

export default {
    components: {
        Button,
        BreezeAuthenticatedLayout,
        Pagination,
        PrintDetails,
        TopBar,
        RefreshIcon,
        SearchIcon,
        PrintIcon,
        EditIcon,
        DeleteIcon,
    },
    props: {
        categories:Object,
        products:Object,
        brands:Object,
        message:Text
    },
    data(){
        return {
            permissions: this.$page.props.permissionNames,
            lang: this.$page.props.lang,
            productModel: {},
            processing: false,
            search: false,
            category_id: null,
            brand_id: null,
            name: null,
            type: null,
        }
    },
    methods:{
        getSearchProduct() {
            this.processing = true
            this.search = true
            this.$inertia.visit('/product', {
                method: 'get',
                data: {
                    search: this.search,
                    category_id: this.category_id,
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

        deleteProduct(item_id){
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("product/" + item_id, {
                        onSuccess: () => {
                            toast.fire({
                                icon: 'success',
                                title: 'Successfully Deleted!'
                            })
                        },
                    });
                }
            })
        }
    },

    mounted() {
    }

}
</script>
