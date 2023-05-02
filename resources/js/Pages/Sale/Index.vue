<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('sale.index')" :create-route="route('sale.create')" />
            <div class="container sale">
                <print-details />
                <!-- table -->

                <div class="border-0 card">
                    <div class="mx-3 d-flex justify-content-between align-items-center">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_sales }}</h4><br>
                        </div>
                        <!-- add -->

                        <div class="print-none">
                            <create-icon :Url="route('sale.create')" />
                            <search-icon Url="#sale-search-collapse" />
                            <refresh-icon :Url="route('sale.index')" />
                            <print-icon />
                        </div>
                    </div>

                    <!-- content body -->
                    <div class="p-0 card-body">
                        <div class="print-none" :class="[search ? '' : 'collapse']" id="sale-search-collapse">
                            <form @submit.prevent="getSearchSale" >
                                <div class="mb-4 row">
                                    <div class="col-md-3">
                                        <input type="hidden" v-model="search">
                                        <label for="date">{{lang.from_date}}</label>
                                        <input type="date" id="date" class="form-control" name="date" v-model="from_date">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="to_date">{{ lang.to_date }}</label>
                                        <input type="date" id="to_date" class="form-control" name="to_date" v-model="to_date">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="invoice_no">{{ lang.invoice_number }}</label>
                                        <input type="text" id="invoice_no" placeholder="xxxxxxxx" class="form-control" name="invoice_no" v-model="invoice_no">
                                    </div>

                                    <div class="mt-4 text-right col-md-3">
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
                                        <th scope="col">{{ lang.sl }}</th>
                                        <th scope="col">{{ lang.date }}</th>
                                        <th scope="col">{{lang.customer}}</th>
                                        <th scope="col">{{ lang.invoice_number }}</th>
                                        <th scope="col" class="text-end">{{lang.total}}</th>
                                        <th scope="col" class="text-end">{{lang.paid}}</th>
                                        <th scope="col" class="text-end print-none">{{lang.action}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(sale, index) in sales.data" :key="index">
                                        <td scope="row">{{ sales.from + index }}</td>
                                        <td>{{ formatter.dateFormat(sale.date) }}</td>
                                        <td>{{ sale.party_name }}</td>
                                        <td>
                                            <inertia-link :href="route('sale.show', sale.id)" class="text-decoration-none" title="View Invoice"
                                                          target="_blank">
                                                {{ sale.invoice_no }}
                                            </inertia-link>
                                        </td>
                                        <td class="text-end">{{ Number.parseFloat(sale.grand_total).toFixed(2) }}</td>
                                        <td class="text-end">{{ Number.parseFloat(sale.total_paid).toFixed(2) }}</td>
                                        <td class="text-end print-none">
                                            <show-icon :Url="route('sale.show', sale.id)" />
                                            <edit-icon :Url="route('sale.edit', sale.id)" />
                                            <delete-icon @click.prevent="deleteSale(sale.id)" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="sales.links" />
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Button from "@/Components/Button";
import * as _formatter from '@/Helpers/formatter'
import Pagination from '@/Components/Pagination'
import alert from "@/alert";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import ShowIcon from "@/Pages/ReusableComponent/Icon/ShowIcon";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
import SearchIcon from "@/Pages/ReusableComponent/Icon/SearchIcon";
import deleteAlert from "@/deleteAlert";
export default {
    name: "Index",
    props: {
        sales: Object
    },
    components: {
        SearchIcon,
        Pagination,
        Button,
        BreezeAuthenticatedLayout,
        TopBar,
        CreateIcon,
        RefreshIcon,
        PrintIcon,
        ShowIcon,
        EditIcon,
        DeleteIcon,
        PrintDetails,
    },
    computed: {
        formatter() {
            return _formatter
        }
    },
    data() {
        return {
            lang: this.$page.props.lang,
            search: false,
            processing: false,
            from_date: null,
            to_date: null,
            invoice_no: null,
        }
    },

    methods: {
        showInvoice(invoice_no) {
            // console.log(invoice_no)
            this.$inertia.get("invoice-generate/" + invoice_no);
            // axios.post(this.$page.props.baseUrl + '/invoice-generate/'+invoice_no)
        },

        deleteSale(item_id) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("sale/" + item_id, {
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

        getSearchSale() {
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
            this.$inertia.visit('/sale', {
                method: 'get',
                data: {
                    search: this.search,
                    from_date: this.from_date,
                    to_date: this.to_date,
                    invoice_no: this.invoice_no,
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
        console.log(this.sales)
    }
}
</script>

<style scoped>

</style>
