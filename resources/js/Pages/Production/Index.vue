<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('production.index')" :create-route="route('production.create')" />
            <div class="container sale">
                <!-- table -->
                <div class="border-0 card">
                    <print-details />

                    <div class="mx-3 d-flex justify-content-between align-items-center">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_production }}</h4><br>
                        </div>
                        <!-- add -->
                        <div class="print-none">
                            <create-icon :Url="route('production.index')" />
                            <search-icon Url="#production-search-collapse" />
                            <refresh-icon :Url="route('production.index')" />
                            <print-icon />
                        </div>
                    </div>

                    <!-- content body -->
                    <div class="p-0 card-body">
                        <div class="print-none" :class="[search ? '' : 'collapse']" id="production-search-collapse">
                            <form @submit.prevent="getSearchProduction" >
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

                                    <div class="col-md-3">
                                        <label for="production_no">{{ lang.production_number }}</label>
                                        <input type="text" id="production_no" placeholder="xxxxxxxx" class="form-control" name="production_no" v-model="production_no">
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
                                    <th scope="col" width="100">{{ lang.sl }}</th>
                                    <th scope="col">{{ lang.date }}</th>
                                    <th scope="col">{{ lang.production_no }}</th>
                                    <th scope="col">{{ lang.from_showroom }}</th>
                                    <th scope="col">{{ lang.to_showroom }}</th>
                                    <th scope="col" width="100">{{ lang.action }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item,index) in productions.data" :key="index">
                                    <th scope="row">{{ productions.from + index }}</th>
                                    <td>{{ item.date }}</td>
                                    <td>{{ item.production_no }}</td>
                                    <td>{{ item.from_showroom_name }}</td>
                                    <td>{{ item.to_showroom_name }}</td>
                                    <td>
                                        <show-icon :Url="route('production.show', item.id)" />
                                        <edit-icon :Url="route('production.edit', item.id)" />
                                        <delete-icon @click.prevent="deleteProduction(item.id)" />
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="productions.links" />
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
import SearchIcon from "@/Pages/ReusableComponent/Icon/SearchIcon";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import ShowIcon from "@/Pages/ReusableComponent/Icon/ShowIcon";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";

export default {
    name: 'Index',
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
        productions:Object,
        message:Text
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
        getSearchProduction() {
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

        deleteProduction(item_id) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("production/" + item_id, {
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
