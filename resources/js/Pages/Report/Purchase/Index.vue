<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container unit">
                <print-details />
                <!-- table -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ lang.purchase_report }}</h5>
                            <div>
                                <refresh-icon :Url="route('report.purchase-report')" />
                                <print-icon />
                            </div>
                        </div>
                    </div>

                    <!-- search form start -->
                    <div class="card-body print-none">
                        <form @submit.prevent="getSearchedPurchaseData" class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label" for="from_date">{{ lang.from_date }}</label>
                                        <input type="date" id="from_date" v-model="from_date" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="to_date">{{ lang.to_date }}</label>
                                        <input type="date" id="to_date" v-model="to_date" class="form-control">
                                    </div>
                                    <div class="col-md-4">
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

                                    <div class="col-md-5">
                                        <label for="supplier" class="form-label">{{ lang.supplier }}</label>
                                        <v-select
                                            id="supplier"
                                            :options="records['suppliers']"
                                            :reduce="supplier => supplier.id"
                                            :placeholder="lang.select_client"
                                            v-model="party_id"
                                            label="name">
                                            <template slot="option" slot-scope="option">
                                                <span class="fa" :class="option.icon"></span>
                                                {{ option.name }}
                                            </template>
                                        </v-select>
                                    </div>

                                    <div class="col-md-5">
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

                        <div v-if="records['purchases']" class="mt-2 row col-md-12">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th>{{ lang.date }}</th>
                                                <th>{{ lang.supplier }}</th>
                                                <th>{{ lang.voucher_number }}</th>
                                                <th class="text-end">{{ lang.total }}</th>
                                                <th class="text-end">{{ lang.paid }}</th>
                                                <th class="text-end">{{ lang.action }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr v-for="(purchase, index) in records['purchases'].data" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ formatter.dateFormat(purchase.date) }}</td>
                                                <td>{{ purchase.party.name }}</td>
                                                <td>{{ purchase.voucher_no }}</td>
                                                <td class="text-end">{{ Number.parseFloat(purchase.grand_total).toFixed(2) }}</td>
                                                <td class="text-end">{{ Number.parseFloat(purchase.paid).toFixed(2) }}</td>
                                                <td class="text-end print-none">
                                                    <inertia-link :href="route('purchase.show', purchase.id)" class="btn table-small-button" :title="lang.show">
                                                        <i class="bi bi-eye"></i>
                                                    </inertia-link >
                                                </td>
                                            </tr>
                                            <tr v-if="records['purchases'].length === 0">
                                                <td colspan="8" class="text-center">No purchase available</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <pagination :links="records['purchases'].links" />
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
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
export default {
    name: "Index",
    props: {
        records: Array,
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
            to_date: null,
            showroom_id: null,
            party_id: null,
            product_id: null,
            processing: false,
        }
    },

    methods: {
        getSearchedPurchaseData() {
            this.processing = true
            this.$inertia.visit('/report/purchase-report', {
                method: 'get',
                data: {
                    search: true,
                    from_date: this.from_date,
                    to_date: this.to_date,
                    showroom_id: this.showroom_id,
                    product_id: this.product_id,
                    party_id: this.party_id,
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
        console.log(this.records['showrooms'])
    }
}
</script>

<style scoped>

</style>
