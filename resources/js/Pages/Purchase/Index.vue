<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :indexRoute="route('purchase.index')" :createRoute="route('purchase.create')" />
            <div class="container mt-2">
                <div class="card border-0">
                    <div class="d-flex mx-3 justify-content-between align-items-center">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_purchase }}</h4><br>
                        </div>
                        <!-- add -->

                        <div class="print-none">
                            <create-icon :Url="route('purchase.create')" :title="lang.create_new_purchase" />
                            <search-icon Url="#purchase-search-collapse" />
                            <refresh-icon :Url="route('purchase.index')" />
                            <print-icon />
                        </div>
                    </div>
                    <print-details />

                    <div class="card-body p-0">
                        <div class="print-none" :class="[search ? '' : 'collapse']" id="purchase-search-collapse">
                            <form @submit.prevent="getSearchPurchase" >
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <input type="hidden" v-model="search">
                                        <label for="date">{{ lang.from_date }}</label>
                                        <input type="date" id="date" class="form-control" name="date" v-model="from_date">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="to_date">{{ lang.to_date }}</label>
                                        <input type="date" id="to_date" class="form-control" name="to_date" v-model="to_date">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="voucher_no">{{ lang.voucher_number }}</label>
                                        <input type="text" id="voucher_no" placeholder="xxxxxxxx" class="form-control" name="voucher_no" v-model="voucher_no">
                                    </div>

                                    <div class="col-md-3 mt-4 text-right">
                                        <label>&nbsp;</label>
                                        <search-button :processing="processing" />
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>{{ lang.date }}</th>
                                    <th>{{ lang.supplier }}</th>
                                    <th>{{ lang.voucher_number }}</th>
                                    <th v-if="permissions.includes('product-purchase_price')" class="text-end">{{ lang.total }}</th>
                                    <th v-if="permissions.includes('product-purchase_price')" class="text-end">{{ lang.paid }}</th>
                                    <th class="text-end print-none">{{ lang.action }}</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for="(purchase, index) in purchases.data" :key="index">
                                    <td>{{ purchases.from + index }}</td>
                                    <td>{{ formatter.dateFormat(purchase.date) }}</td>
                                    <td>{{ purchase.party_name }}</td>
                                    <td>{{ purchase.voucher_no }}</td>
                                    <td v-if="permissions.includes('product-purchase_price')" class="text-end">{{ Number.parseFloat(purchase.grand_total).toFixed(2) }}</td>
                                    <td v-if="permissions.includes('product-purchase_price')" class="text-end">{{ Number.parseFloat(purchase.paid).toFixed(2) }}</td>
                                    <td class="text-end print-none">
                                        <show-icon :Url="route('purchase.show', purchase.id)" />
                                        <edit-icon :Url="route('purchase.edit', purchase.id)" />
                                        <delete-icon @click.prevent="deletePurchase(purchase.id)" />
                                    </td>
                                </tr>
                                <tr v-if="purchases.length === 0">
                                    <td colspan="8" class="text-center">No purchase available</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                         <pagination :links="purchases.links" />
                    </div>
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import * as _formatter from '@/Helpers/formatter'
import Pagination from "@/Components/Pagination";
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import searchButton from "@/Pages/ReusableComponent/Button/SearchButton";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import SearchIcon from "@/Pages/ReusableComponent/Icon/SearchIcon";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import ShowIcon from "@/Pages/ReusableComponent/Icon/ShowIcon";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
export default {
    name: "Index",
    components: {
        BreezeAuthenticatedLayout,
        Pagination,
        PrintDetails,
        searchButton,
        ShowIcon,
        EditIcon,
        DeleteIcon,
        TopBar,
        CreateIcon,
        SearchIcon,
        PrintIcon,
        RefreshIcon,
    },
    computed: {
        formatter() {
            return _formatter
        }
    },
    props: {
        purchases: Object
    },
    data() {
        return {
            lang: this.$page.props.lang,
            search: false,
            processing: false,
            from_date: null,
            to_date: null,
            voucher_no: null,
            permissions: this.$page.props.permissionNames
        }
    },
    methods: {
        showPurchaseDetails(){

        },

        deletePurchase(item_id) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("purchase/" + item_id, {
                        onSuccess: () => {
                            toast.fire({
                                icon: 'success',
                                title: 'Successfully Deleted!'
                            })
                        },
                    });
                }
            })
        },

        getSearchPurchase() {
            if (this.from_date && !this.to_date) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please enter to date!'
                })
                return
            }

            if (!this.from_date && this.to_date) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please enter from date!'
                })
                return
            }
            this.processing = true
            this.search = true
            this.$inertia.visit('/purchase', {
                method: 'get',
                data: {
                    search: this.search,
                    from_date: this.from_date,
                    to_date: this.to_date,
                    voucher_no: this.voucher_no,
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
