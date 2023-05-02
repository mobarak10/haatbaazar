<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('damage-return.index')" :create-route="route('damage-return.create')" />
            <div class="container mt-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex mx-3 justify-content-between align-items-center">
                            <!-- page title -->
                            <div class="mt-3 mb-2 print-none">
                                <h4 class="main-title">{{ lang.damage }} {{ lang.return }}</h4><br>
                            </div>
                            <!-- add -->

                            <div class="print-none">
                                <create-icon :Url="route('damage-return.create')" />
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
                                    <th>{{ lang.return_number }}</th>
                                    <th v-if="permissions.includes('product-purchase_price')" class="text-end">{{ lang.total }}</th>
                                    <th v-if="permissions.includes('product-purchase_price')" class="text-end">{{ lang.paid }}</th>
                                    <th class="text-end print-none">{{ lang.action }}</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for="(damage_return, index) in damage_returns.data" :key="index">
                                    <td>{{ damage_returns.from + index }}</td>
                                    <td>{{ formatter.dateFormat(damage_return.date) }}</td>
                                    <td>{{ damage_return.party?.name }}</td>
                                    <td>{{ damage_return.damage_return_no }}</td>
                                    <td v-if="permissions.includes('product-purchase_price')" class="text-end">
                                        {{ Number.parseFloat(damage_return.grand_total).toFixed(2) }}
                                    </td>
                                    <td v-if="permissions.includes('product-purchase_price')" class="text-end">
                                        {{ Number.parseFloat(damage_return.paid).toFixed(2) }}
                                    </td>
                                    <td class="text-end">
                                        <show-icon :Url="route('damage-return.show', damage_return.id)" />
                                        <edit-icon :Url="route('damage-return.edit', damage_return.id)" />
                                        <delete-icon @click.prevent="deletePurchaseReturn(damage_return.id)" />
                                    </td>
                                </tr>
                                <tr v-if="damage_returns.length === 0">
                                    <td colspan="8" class="text-center">No purchase available</td>
                                </tr>
                                </tbody>
                            </table>
                            <pagination :links="damage_returns.links" />
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
        damage_returns: Object
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
                    this.$inertia.delete("damage-return/" + item_id, {
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
