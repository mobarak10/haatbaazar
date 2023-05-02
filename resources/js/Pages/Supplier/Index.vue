<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :create-route="route('supplier.create')" :index-route="route('supplier.index')" />

            <div class="supplier container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card-header p-0 py-1 d-sm-flex d-block  align-items-center border-0">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{lang.all_supplier}}</h4><br>
                            <!-- <small class="print-none">About 563,000,000 results (0.98 seconds)</small> -->
                        </div>
                        <!-- add -->

                        <create-icon :Url="route('supplier.create')" />
                    </div>

                    <!-- content body -->
                    <div class="card-body p-0">
                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col" width="5%">{{ lang.sl }}</th>
                                    <th scope="col">{{ lang.supplier_name }}</th>
                                    <th scope="col">{{ lang.phone }}</th>
                                    <th scope="col" class="text-end">{{ lang.balance }} ({{ lang.bdt }})</th>
                                    <th scope="col" class="text-end">{{ lang.balance_status }}</th>
                                    <th scope="col" class="print-none text-end">{{ lang.action }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(supplier,index) in suppliers.data" :key="index">
                                    <th scope="row">{{ suppliers.from + index }}</th>
                                    <td>{{ supplier.name }}</td>
                                    <td>{{ supplier.phone }}</td>
                                    <td class="text-end">{{ Number.parseFloat(Math.abs(supplier.balance)).toFixed(2) }}</td>
                                    <td class="text-end">{{ supplier.balance >= 0 ? lang.receivable : lang.payable }}</td>
                                    <td class="text-end">
                                        <edit-icon :Url="route('supplier.edit', supplier.id)" />
                                        <delete-icon @click.prevent="deleteSupplier(supplier.id)" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">{{ lang.total }}</td>
                                    <td class="text-end">{{ Number.parseFloat(Math.abs(supplier_balance)).toFixed(2) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="suppliers.links" />
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
    import Pagination from "@/Components/Pagination";
    import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
    import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
    import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
    import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
    import alert from "@/alert";
    import deleteAlert from "@/deleteAlert";
    export default {
        name: "Index",
        components: {
            BreezeAuthenticatedLayout,
            Pagination,
            EditIcon,
            DeleteIcon,
            TopBar,
            CreateIcon,
        },
        props: {
            suppliers: Object,
            supplier_balance: Number,
        },
        data() {
            return{
                lang: this.$page.props.lang
            }
        },
        methods: {
            deleteSupplier(item_id){
                let toast = alert()
                deleteAlert().then((result) => {
                    if (result.isConfirmed) {
                        this.$inertia.delete("supplier/" + item_id, {
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
        },
        mounted() {

        }
    }
</script>

<style scoped>

</style>
