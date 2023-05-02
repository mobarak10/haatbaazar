<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container print-none">
                <ul class="nav nav-tabs mt-2">
                    <li class="nav-item">
                        <inertia-link v-if="type === 'customer'" class="nav-link active" :href="route('customer-due-manage.index')">{{ lang.all_records }}</inertia-link>
                        <inertia-link v-else class="nav-link active" :href="route('supplier-due-manage.index')">{{ lang.all_records }}</inertia-link>
                    </li>

                    <li class="nav-item">
                        <inertia-link v-if="type === 'customer'" class="nav-link" :href="route('customer-due-manage.create')">{{lang.new_entry}}</inertia-link>
                        <inertia-link v-else class="nav-link" :href="route('supplier-due-manage.create')">{{lang.new_entry}}</inertia-link>
                    </li>
                </ul>
            </div>

            <div class="supplier container">
                <PrintDetails />
                <!-- table -->
                <div class="card border-0">
                    <div class="mx-3 d-flex justify-content-between align-items-center">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{lang.all_due_manage}}</h4><br>
                        </div>
                        <div class="print-none">
                            <refresh-icon v-if="type === 'customer'" :Url="route('customer-due-manage.index')" />
                            <refresh-icon v-else :Url="route('supplier-due-manage.index')" />
                            <search-icon Url="#due-search-collapse" />
                            <print-icon />
                            <create-icon v-if="type === 'customer'" :Url="route('customer-due-manage.create')" />
                            <create-icon v-else :Url="route('supplier-due-manage.create')" />
                        </div>
                    </div>

                    <!-- content body -->
                    <div class="card-body p-0">
                        <!-- search from start-->
                        <div class="print-none" :class="[search ? '' : 'collapse']" id="due-search-collapse">
                            <form @submit.prevent="getSearchDueManage" >
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="date">{{ lang.from_date }}</label>
                                        <input type="date" id="date" class="form-control" name="date" v-model="from_date">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="to_date">{{ lang.to_date }}</label>
                                        <input type="date" id="to_date" class="form-control" name="to_date" v-model="to_date">
                                    </div>

                                    <div class="mb-3 col-md-3">
                                        <label for="date">{{ lang.party }}</label>
                                        <v-select
                                            :options="party"
                                            :reduce="party => party.id"
                                            :placeholder="lang.select_party"
                                            v-model="party_id"
                                            label="name">
                                            <template slot="option" slot-scope="option">
                                                <span class="fa" :class="option.icon"></span>
                                                {{ option.name }}
                                            </template>
                                        </v-select>
                                    </div>

                                    <div class="mt-4 text-right col-md-2">
                                        <label>&nbsp;</label>
                                        <search-button :processing="processing" />
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- search from end-->

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th scope="col" width="5%">{{lang.sl}}</th>
                                    <th scope="col">{{lang.date}}</th>
                                    <th scope="col">{{ lang.customer_name }}</th>
                                    <th scope="col" class="text-end">{{ lang.type }}</th>
                                    <th scope="col" class="text-end">{{ lang.amount }}</th>
                                    <th scope="col" width="8%" class="print-none text-end">{{lang.action}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(due_manage, index) in due_manages.data" :key="index">
                                        <th scope="row">{{ due_manages.from + index }}</th>
                                        <td>{{ formatter.dateFormat(due_manage.date) }}</td>
                                        <td>{{ due_manage.party?.name }}</td>
                                        <td class="text-end">{{ due_manage.amount >= 0 ? lang.receive : lang.paid }}</td>
                                        <td class="text-end">{{ Number.parseFloat(Math.abs(due_manage.amount)).toFixed(2) }}</td>
                                        <td class="text-end print-none">
                                            <inertia-link v-if="type === 'customer'" :href="route('customer-due-manage.show', due_manage.id)" class="btn table-small-button" :title="lang.show">
                                                <i class="bi bi-eye"></i>
                                            </inertia-link >

                                            <inertia-link v-else :href="route('supplier-due-manage.show', due_manage.id)" class="btn table-small-button" :title="lang.show">
                                                <i class="bi bi-eye"></i>
                                            </inertia-link >

                                            <inertia-link v-if="type === 'customer'" :href="route('customer-due-manage.edit', due_manage.id)" class="btn table-small-button" :title="lang.edit_data">
                                                <i class="bi bi-pencil"></i>
                                            </inertia-link>

                                            <inertia-link v-else :href="route('supplier-due-manage.edit', due_manage.id)" class="btn table-small-button" :title="lang.edit_data">
                                                <i class="bi bi-pencil"></i>
                                            </inertia-link>

                                            <a href="#" @click.prevent="deleteDueManage(due_manage.id)" class="btn table-small-button" :title="lang.delete">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                       <td colspan="4" class="text-end">{{ lang.total }}</td>
                                        <td class="text-end">{{ Number.parseFloat(total_amount).toFixed(2) }}</td>
                                        <td class="print-none"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="due_manages.links" />
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Pagination from '@/Components/Pagination'
import * as _formatter from '@/Helpers/formatter'
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import SearchIcon from "@/Pages/ReusableComponent/Icon/SearchIcon";
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import SearchButton from "@/Pages/ReusableComponent/Button/SearchButton";
import VSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import 'vue-select/dist/vue-select';
export default {
    data (){
        return{
            processing: false,
            lang: this.$page.props.lang,
            search: false,
            party_id: null,
            from_date: null,
            to_date: null,
        }
    },
    computed: {
        formatter() {
            return _formatter
        }
    },
    name: "Index",
    components: {
        BreezeAuthenticatedLayout,
        Pagination,
        RefreshIcon,
        PrintIcon,
        SearchIcon,
        CreateIcon,
        PrintDetails,
        SearchButton,
        VSelect,
    },
    props: {
        party: Array,
        due_manages: Object,
        type: String,
        total_amount: Number,
    },

    methods: {
        deleteDueManage(item_id) {
            let toast = alert()
            let deleteUrl = this.type === 'customer' ? 'customer-due-manage' : 'supplier-due-manage'
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(deleteUrl+'/' + item_id, {
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

        getSearchDueManage() {
            this.processing = true
            this.search = true
            let searchUrl = this.type === 'customer' ? 'customer-due-manage' : 'supplier-due-manage'
            this.$inertia.visit(searchUrl, {
                method: 'get',
                data: {
                    search: this.search,
                    from_date: this.from_date,
                    to_date: this.to_date,
                    party_id: this.party_id,
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
}
</script>

<style scoped>

</style>
