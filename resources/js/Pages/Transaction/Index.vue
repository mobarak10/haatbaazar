<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('transaction.index')" :create-route="route('transaction.create')" />
            <div class="container sale">
                <!-- table -->
                <div class="card">
                    <print-details />

                    <div class="mx-3 d-flex justify-content-between align-items-center">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all }} {{ lang.transaction }}</h4><br>
                        </div>
                        <!-- add -->
                        <div class="print-none">
                            <create-icon :Url="route('transaction.create')" />
                            <search-icon Url="#transaction-search-collapse" />
                            <refresh-icon :Url="route('transaction.index')" />
                            <print-icon />
                        </div>
                    </div>

                    <!-- content body -->
                    <div class="p-0 card-body">
                        <div class="print-none mx-2" :class="[search ? '' : 'collapse']" id="transaction-search-collapse">
                            <form @submit.prevent="getSearchTransaction" >
                                <div class="mb-4 row">
                                    <div class="col-md-3">
                                        <input type="hidden" v-model="search">
                                        <label for="date">{{ lang.from_date }}</label>
                                        <input type="date" id="date" class="form-control" name="date" v-model="from_date">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="to_date">{{ lang.to_date }}</label>
                                        <input type="date" id="to_date" class="form-control" name="to_date" v-model="to_date">
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
                        <div class="table-responsive-md mx-2">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" width="100">{{ lang.sl }}</th>
                                        <th scope="col">{{ lang.date }}</th>
                                        <th scope="col">{{ lang.transfer }} {{ lang.from }}</th>
                                        <th scope="col">{{ lang.transfer }} {{ lang.to }}</th>
                                        <th scope="col" class="text-end">{{ lang.amount }}</th>
                                        <th scope="col" width="100">{{ lang.action }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(transaction, index) in transactions.data" :key="index">
                                        <td>{{ transactions.from + index }}</td>
                                        <td>{{ formatter.dateFormat(transaction.date) }}</td>
                                        <td>{{ transaction.transaction_from == 'cash' ? lang.cash + ' (' + transaction.from_transaction.title + ')'  : lang.bank + ' (' + transaction.from_transaction.account_name + ')' }}</td>
                                        <td>{{ transaction.transaction_to == 'cash' ? lang.cash + ' (' + transaction.to_transaction.title + ')'  : lang.bank + ' (' + transaction.to_transaction.bank.name + '-' + transaction.to_transaction.account_name + ')' }}</td>
                                        <td class="text-end">{{ Number.parseFloat(transaction.amount).toFixed(2) }}</td>
                                        <td class="text-end">
                                            <edit-icon :Url="route('transaction.edit', transaction.id)" />
                                            <delete-icon @click.prevent="deleteTransaction(transaction.id)" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="transactions.links" />
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import Pagination from "@/Components/Pagination";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import SearchIcon from "@/Pages/ReusableComponent/Icon/SearchIcon";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import ShowIcon from "@/Pages/ReusableComponent/Icon/ShowIcon";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import * as _formatter from '@/Helpers/formatter'

export default {
    name: "Index",
    components: {
        BreezeAuthenticatedLayout,
        Pagination,
        TopBar,
        CreateIcon,
        SearchIcon,
        RefreshIcon,
        PrintIcon,
        PrintDetails,
        ShowIcon,
        EditIcon,
        DeleteIcon,
    },
    props: {
        transactions:Object,
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
            production_no: null,
        }
    },

    methods: {
        getSearchTransaction() {
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
            this.$inertia.visit('/production', {
                method: 'get',
                data: {
                    search: this.search,
                    from_date: this.from_date,
                    to_date: this.to_date,
                    production_no: this.production_no,
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

        deleteTransaction(item_id) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("transaction/" + item_id, {
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
}
</script>

<style scoped>

</style>
