<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('product-transfer.index')" :create-route="route('product-transfer.create')" />
            <div class="container sale">
                <print-details />
                <!-- table -->
                <div class="border-0 card">
                    <div class="mx-3 d-flex justify-content-between align-items-center">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_product_transfer }}</h4><br>
                        </div>
                        <!-- add -->

                        <div class="print-none">
                            <create-icon :Url="route('product-transfer.create')" />
                            <search-icon Url="#product-transfer-search-collapse" />
                            <refresh-icon :Url="route('product-transfer.index')" />
                            <print-icon />
                        </div>
                    </div>

                    <!-- content body -->
                    <div class="p-0 card-body">
                        <div class="print-none" :class="[search ? '' : 'collapse']" id="product-transfer-search-collapse">
                            <form @submit.prevent="getSearchTransfer" >
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
                                        <label for="transfer_no">{{ lang.transfer_number }}</label>
                                        <input type="text" id="transfer_no" placeholder="xxxxxxxx" class="form-control" name="transfer_no" v-model="transfer_no">
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

                        <div class="table-responsive">
                            <table class="table custom-table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ lang.sl }}</th>
                                        <th scope="col">{{ lang.date }}</th>
                                        <th scope="col">{{ lang.transfer_number }}</th>
                                        <th scope="col">{{ lang.from_showroom }}</th>
                                        <th scope="col">{{ lang.to_showroom }}</th>
                                        <th scope="col">{{ lang.user }}</th>
                                        <th scope="col" class="text-end print-none">{{lang.action}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(transfer, index) in product_transfers.data" :key="index">
                                        <td scope="row">{{ product_transfers.from + index }}</td>
                                        <td>{{ formatter.dateFormat(transfer.date) }}</td>
                                        <td>
                                            <inertia-link :href="route('product-transfer.show', transfer.id)" class="text-decoration-none" title="View transfer details"
                                                          target="_blank">
                                                {{ transfer.transfer_no }}
                                            </inertia-link>
                                        </td>
                                        <td>{{ transfer.from_showroom_name }}</td>
                                        <td>{{ transfer.to_showroom_name }}</td>
                                        <td>{{ transfer.user_name }}</td>
                                        <td class="text-end print-none">
                                            <show-icon :Url="route('product-transfer.show', transfer.id)" />
                                            <edit-icon :Url="route('product-transfer.edit', transfer.id)" />
                                            <delete-icon @click.prevent="deleteProductTransfer(transfer.id)" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="product_transfers.links" />
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import * as _formatter from '@/Helpers/formatter'
import Pagination from '@/Components/Pagination'
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import SearchIcon from "@/Pages/ReusableComponent/Icon/SearchIcon";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import ShowIcon from "@/Pages/ReusableComponent/Icon/ShowIcon";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
export default {
    name: "Index",
    props: {
        product_transfers: Object
    },
    components: {
        Pagination,
        BreezeAuthenticatedLayout,
        TopBar,
        PrintIcon,
        PrintDetails,
        CreateIcon,
        SearchIcon,
        RefreshIcon,
        ShowIcon,
        EditIcon,
        DeleteIcon,
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
            transfer_no: null,
        }
    },

    methods: {
        deleteProductTransfer(item_id) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("product-transfer/" + item_id, {
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

        getSearchTransfer() {
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
            this.$inertia.visit('/product-transfer', {
                method: 'get',
                data: {
                    search: this.search,
                    from_date: this.from_date,
                    to_date: this.to_date,
                    transfer_no: this.transfer_no,
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
