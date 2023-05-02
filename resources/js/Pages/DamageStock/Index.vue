<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('damage-stock.index')" :create-route="route('damage-stock.create')" />
            <div class="container sale">
                <PrintDetails />
                <!-- table -->
                <div class="border-0 card">
                    <div class="mx-3 d-flex justify-content-between align-items-center">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.damage_stock }}</h4><br>
                        </div>
                        <!-- add -->

                        <div class="print-none">
                            <create-icon :Url="route('damage-stock.create')" />
                            <search-icon Url="#damage-search-collapse" />
                            <refresh-icon :Url="route('damage-stock.index')" />
                            <print-icon />
                        </div>
                    </div>

                    <!-- content body -->
                    <div class="p-0 card-body">
                        <div class="print-none" :class="[search ? '' : 'collapse']" id="damage-search-collapse">
                            <form @submit.prevent="getSearchDamage" >
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
                                    <th scope="col">{{lang.sl}}</th>
                                    <th scope="col">{{ lang.date }}</th>
                                    <th scope="col">{{ lang.damage_number }}</th>
                                    <th scope="col" class="text-end print-none">{{lang.action}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(damage, index) in damage_stocks.data" :key="index">
                                    <td scope="row">{{ damage_stocks.from + index }}</td>
                                    <td>{{ formatter.dateFormat(damage.date) }}</td>
                                    <td>{{ damage.damage_no }}</td>
                                    <td class="text-end print-none">
                                        <show-icon :Url="route('damage-stock.show', damage.id)" />
                                        <edit-icon :Url="route('damage-stock.edit', damage.id)" />
                                        <delete-icon @click.prevent="deleteDamage(damage.id)" />
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="damage_stocks.links" />
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import Pagination from "@/Components/Pagination";
import Button from "@/Components/Button";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import * as _formatter from "@/Helpers/formatter";
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import SearchIcon from "@/Pages/ReusableComponent/Icon/SearchIcon";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
import ShowIcon from "@/Pages/ReusableComponent/Icon/ShowIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";

export default {
    name: "Index",
    props: {
        damage_stocks: Object
    },
    components: {
        Pagination,
        Button,
        BreezeAuthenticatedLayout,
        PrintDetails,
        TopBar,
        CreateIcon,
        SearchIcon,
        RefreshIcon,
        PrintIcon,
        EditIcon,
        ShowIcon,
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
        }
    },

    methods: {
        deleteDamage(item_id) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("damage-stock/" + item_id, {
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

        getSearchDamage() {
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
            this.$inertia.visit('/damage-stock', {
                method: 'get',
                data: {
                    search: this.search,
                    from_date: this.from_date,
                    to_date: this.to_date,
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
