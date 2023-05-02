<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('sale-return.index')" :create-route="route('sale-return.create')" />
            <div class="sale container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card-header p-0 py-1 d-sm-flex d-block  align-items-center border-0">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_sale_return}}</h4><br>
                        </div>
                        <!-- add -->
                        <create-icon :Url="route('sale-return.create')" />
                    </div>

                    <!-- content body -->
                    <div class="card-body p-0">
                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col">{{lang.sl}}</th>
                                    <th scope="col">{{lang.date}}</th>
                                    <th scope="col">{{lang.customer}}</th>
                                    <th scope="col">{{lang.return_number}}</th>
                                    <th scope="col" class="text-end">{{lang.total}}</th>
                                    <th scope="col" class="text-end">{{lang.paid}}</th>
                                    <th scope="col" class="text-end">{{lang.action}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(sale_return, index) in sale_returns.data" :key="index">
                                    <td scope="row">{{ sale_returns.from + index }}</td>
                                    <td>{{ formatter.dateFormat(sale_return.date) }}</td>
                                    <td>{{ sale_return.party_name }}</td>
                                    <td>
                                        <inertia-link :href="route('sale-return.show', sale_return.id)" class="text-decoration-none" title="View Invoice"
                                                      target="_blank">
                                            {{ sale_return.return_no }}
                                        </inertia-link>
                                    </td>
                                    <td class="text-end">{{ Number.parseFloat(sale_return.return_grand_total).toFixed(2) }}</td>
                                    <td class="text-end">{{ Number.parseFloat(sale_return.paid).toFixed(2) }}</td>
                                    <td class="text-end">
                                        <show-icon :Url="route('sale-return.show', sale_return.id)" />
                                        <edit-icon :Url="route('sale-return.edit', sale_return.id)" />
                                        <delete-icon @click.prevent="deleteSaleReturn(sale_return.id)" />
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="sale_returns.links" />
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
import deleteAlert from "@/deleteAlert";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import ShowIcon from "@/Pages/ReusableComponent/Icon/ShowIcon";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
export default {
    data() {
        return {
            lang: this.$page.props.lang,
        }
    },
    name: "Index",
    props: {
        sale_returns: Object,
    },
    components: {
        Pagination,
        Button,
        BreezeAuthenticatedLayout,
        TopBar,
        CreateIcon,
        ShowIcon,
        EditIcon,
        DeleteIcon,
    },
    computed: {
        formatter() {
            return _formatter
        }
    },
    methods: {
        deleteSaleReturn(item_id) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("sale-return/" + item_id, {
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
        console.log(this.sale_returns)
    }
}
</script>

<style scoped>

</style>
