<template>
    <div>
        <breeze-authenticated-layout>
           <top-bar :index-route="route('purchase-return.index')" :create-route="route('purchase-return.create')" />
            <div class="container mt-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex mx-3 justify-content-between align-items-center">
                            <!-- page title -->
                            <div class="mt-3 mb-2 print-none">
                                <h4 class="main-title">{{ lang.purchase_return }}</h4><br>
                            </div>
                            <!-- add -->

                            <div class="print-none">
                                <create-icon :Url="route('purchase-return.create')" />
                                <print-icon />
                            </div>
                        </div>
                        <print-details />

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>{{ lang.date }}</th>
                                    <th>{{ lang.supplier }}</th>
                                    <th>{{ lang.return_number}}</th>
                                    <th v-if="permissions.includes('product-purchase_price')" class="text-end">{{ lang.total }}</th>
                                    <th v-if="permissions.includes('product-purchase_price')" class="text-end">{{ lang.paid }}</th>
                                    <th class="text-end print-none">{{ lang.action }}</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for="(purchase_return, index) in purchase_returns.data" :key="index">
                                    <td>{{ purchase_returns.from + index }}</td>
                                    <td>{{ formatter.dateFormat(purchase_return.date) }}</td>
                                    <td>{{ purchase_return.party?.name }}</td>
                                    <td>{{ purchase_return.return_no }}</td>
                                    <td v-if="permissions.includes('product-purchase_price')" class="text-end">{{ Number.parseFloat(purchase_return.return_grand_total).toFixed(2) }}</td>
                                    <td v-if="permissions.includes('product-purchase_price')" class="text-end">{{ Number.parseFloat(purchase_return.paid).toFixed(2) }}</td>
                                    <td class="text-end">
                                        <show-icon :Url="route('purchase-return.show', purchase_return.id)" />
                                        <edit-icon :Url="route('purchase-return.edit', purchase_return.id)" />
                                        <delete-icon @click.prevent="deletePurchaseReturn(purchase_return.id)" />
                                    </td>
                                </tr>
                                <tr v-if="purchase_returns.length === 0">
                                    <td colspan="8" class="text-center">No purchase available</td>
                                </tr>
                                </tbody>
                            </table>
                            <pagination :links="purchase_returns.links" />
                        </div>
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
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
import ShowIcon from "@/Pages/ReusableComponent/Icon/ShowIcon";
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
export default {
    name: "Index",
    components: {
        BreezeAuthenticatedLayout,
        Pagination,
        TopBar,
        EditIcon,
        ShowIcon,
        CreateIcon,
        PrintIcon,
        PrintDetails,
        DeleteIcon,
    },
    computed: {
        formatter() {
            return _formatter
        }
    },
    props: {
        purchase_returns: Object
    },
    data() {
        return {
            lang: this.$page.props.lang,
            permissions: this.$page.props.permissionNames,
        }
    },
    methods: {
        deletePurchaseReturn(item_id){
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("purchase-return/" + item_id, {
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

}
</script>

<style scoped>

</style>
