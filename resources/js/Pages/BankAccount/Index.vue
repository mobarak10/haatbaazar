<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('bank-account.index')" :create-route="route('bank-account.create')" />
            <div class="unit container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card-header p-0 py-1 d-sm-flex d-block  align-items-center border-0">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_bank_account }}</h4><br>
                            <!-- <small class="print-none">About 563,000,000 results (0.98 seconds)</small> -->
                        </div>
                        <!-- add -->

                        <create-icon :Url="route('bank-account.create')" />
                    </div>

                    <!-- content body -->
                    <div class="card-body p-0">

                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col" width="100">{{ lang.sl }}</th>
                                    <th scope="col">{{ lang.bank_name }}</th>
                                    <th scope="col">{{ lang.account_name }}</th>
                                    <th scope="col">{{ lang.account_number }}</th>
                                    <th scope="col">{{ lang.branch }}</th>
                                    <th scope="col">{{ lang.balance }}</th>
                                    <th scope="col" width="100">{{ lang.action }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(account, index) in bank_accounts.data" :key="index">
                                    <th scope="row">{{ index + 1 }}</th>
                                    <td>{{ account.bank?.name }}</td>
                                    <td>{{ account.account_name }}</td>
                                    <td>{{ account.account_number }}</td>
                                    <td>{{ account.branch }}</td>
                                    <td>{{ account.balance }}</td>
                                    <td>
                                        <edit-icon :Url="route('bank-account.edit', account.id)" />
                                        <delete-icon @click.prevent="deleteBankAccount(account.id)" />
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="bank_accounts.links" />
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Pagination from '@/Components/Pagination'
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
export default {
    data(){
        return{
            lang:this.$page.props.lang,
        }
    },
    name: "Index",
    props: {
        bank_accounts: Object,
    },
    components:{
        BreezeAuthenticatedLayout,
        Pagination,
        TopBar,
        CreateIcon,
        EditIcon,
        DeleteIcon,
    },
    methods: {
        deleteBankAccount(id){
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("bank-account/" + id, {
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

    mounted() {
        console.log(this.bank_accounts)
    }
}
</script>

<style scoped>

</style>
