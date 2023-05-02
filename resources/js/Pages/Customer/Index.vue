<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :indexRoute="route('customer.index')" :createRoute="route('customer.create')" />
            <div class="supplier container">
                <PrintDetails />
                <!-- table -->
                <div class="card border-0">
                    <div class="mx-3 d-flex justify-content-between align-items-center">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_customer }}</h4><br>
                        </div>

                        <div class="print-none">
                            <refresh-icon :Url="route('customer.index')" />
                            <search-icon Url="#customer-search-collapse" />
                            <print-icon />
                        </div>
                    </div>


                    <!-- content body -->
                    <div class="card-body p-0">
                        <!-- search from start -->
                        <div class="print-none" :class="[search ? '' : 'collapse']" id="customer-search-collapse">
                            <form @submit.prevent="getSearchCustomer" >
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label for="name">{{ lang.name }}</label>
                                        <input type="text" id="name" name="name" v-model="name" class="form-control" :placeholder="lang.enter_customer_name">
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <Mokam ref="mokam" />
                                    </div>

                                    <div class="mt-4 text-right col-md-2">
                                        <search-button :processing="processing" />
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- search from end -->

                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" width="5%">{{lang.sl}}</th>
                                        <th scope="col">{{ lang.customer_name }}</th>
                                        <th scope="col">{{ lang.phone }}</th>
                                        <th scope="col" class="text-end">{{ lang.balance }} ({{ lang.bdt }})</th>
                                        <th scope="col" width="8%" class="print-none text-end">{{ lang.action }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(customer,index) in customers.data" :key="index">
                                        <th scope="row">{{ customers.from + index }}</th>
                                        <td>{{ customer.custom_name ?? customer.name }}</td>
                                        <td>{{ customer.phone }}</td>
                                        <td class="text-end">{{ Number.parseFloat(Math.abs(customer.balance)).toFixed(2) }}</td>
<!--                                        <td class="text-end">{{ customer.balance >= 0 ? 'Receivable' : 'payable' }}</td>-->
                                        <td class="text-end print-none">
                                            <edit-icon :Url="route('customer.edit', customer.id)" />
                                            <delete-icon @click.prevent="deleteCustomer(customer.id)" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end">{{ lang.total }}</td>
                                        <td class="text-end">{{ Number.parseFloat(totalBalance).toFixed(2) }}</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="customers.links" />
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Pagination from '@/Components/Pagination'
import PaginationHelper from "@/reusable/PaginationHelper";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import SearchButton from "@/Pages/ReusableComponent/Button/SearchButton";
import Mokam from "@/Pages/ReusableComponent/Customer/Mokam";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import SearchIcon from "@/Pages/ReusableComponent/Icon/SearchIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
    export default {
        name: "Index",
        components: {
            BreezeAuthenticatedLayout,
            Pagination,
            PaginationHelper,
            PrintDetails,
            SearchButton,
            Mokam,
            RefreshIcon,
            SearchIcon,
            PrintIcon,
            EditIcon,
            DeleteIcon,
            TopBar,
        },
        props: {
            customers: Object,
            totalBalance: Number,
        },
        methods: {
            deleteCustomer(item_id){
                let toast = alert()
                deleteAlert().then((result) => {
                    if (result.isConfirmed) {
                        this.$inertia.delete("customer/" + item_id, {
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

            getSearchCustomer() {
                this.processing = true
                this.search = true
                this.$inertia.visit('/customer', {
                    method: 'get',
                    data: {
                        search: this.search,
                        name: this.name,
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
           data() {
            return{
                lang: this.$page.props.lang,
                name: null,
                processing: false,
                search: false,
            }
        },
        mounted() {

        }
    }
</script>

<style scoped>

</style>
