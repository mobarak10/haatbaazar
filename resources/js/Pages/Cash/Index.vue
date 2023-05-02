<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('cash.index')" :create-route="route('cash.create')" />
            <div class="container unit">
                <!-- table -->
                <div class="border-0 card">
                    <div
                        class="p-0 py-1 border-0  card-header d-sm-flex d-block align-items-center"
                    >
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_cash }}</h4>
                            <br />
                            <!-- <small class="print-none">About 563,000,000 results (0.98 seconds)</small> -->
                        </div>
                        <!-- add -->

                        <back-icon :Url="route('cash.create')" />
                    </div>

                    <!-- content body -->
                    <div class="p-0 card-body">
                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" width="100">{{lang.sl}}</th>
                                        <th scope="col">{{ lang.name }}</th>
                                        <th scope="col">{{ lang.balance }}</th>
                                        <th scope="col" width="100">{{ lang.action }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(cash, index) in cashes"
                                        :key="index"
                                    >
                                        <th scope="row">{{ index + 1 }}</th>
                                        <td>{{ cash.title }}</td>
                                        <td>{{ cash.balance }}</td>
                                        <td>
                                            <edit-icon :Url="route('cash.edit', cash.id)" />
                                            <delete-icon @click.prevent="deleteCash(cash.id)" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import BackIcon from "@/Pages/ReusableComponent/Icon/BackIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
export default {
    data(){
        return{
            lang:this.$page.props.lang,
        }
    },
    name: "Index",
    props: {
        cashes: Object,
    },
    components: {
        BreezeAuthenticatedLayout,
        TopBar,
        BackIcon,
        DeleteIcon,
        EditIcon,
    },
    methods: {
        deleteCash(id) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("cash/" + id, {
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

    mounted() {},
};
</script>

<style scoped></style>
