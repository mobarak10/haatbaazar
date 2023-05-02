<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('salary.index')" :create-route="route('salary.create')" />
            <div class="unit container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card-header p-0 py-1 print-none d-flex d-block justify-content-between border-0">
                        <!-- page title -->
                        <div class="mt-3 mb-2">
                            <h4 class="main-title">{{ lang.all }} {{ lang.salary }}</h4><br>
                            <!-- <small class="print-none">About 563,000,000 results (0.98 seconds)</small> -->
                        </div>
                        <!-- add -->
                        <div class="text-end">
                            <refresh-icon :Url="route('salary.index')" />
                            <search-icon Url="#salary-search-collapse" />
                            <create-icon :Url="route('salary.create')" />
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="print-none" :class="[search ? '' : 'collapse']" id="salary-search-collapse">
                            <form @submit.prevent="getSearchSalary" >
                                <div class="mb-4 row">
                                    <div class="col-md-3">
                                        <label for="month">{{lang.month}}</label>
                                        <input type="month" id="month" class="form-control" name="month" v-model="tempMonth">
                                    </div>

                                    <div class="mt-4 text-right col-md-3">
                                        <label>&nbsp;</label>
                                        <search-button :processing="processing" />
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr>
                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" width="100">{{ lang.sl }}</th>
                                        <th scope="col">{{ lang.name }}</th>
                                        <th scope="col">{{ lang.email }}</th>
                                        <th scope="col">{{ lang.month }}</th>
                                        <th scope="col">{{ lang.salary }}</th>
                                        <th class="text-end" scope="col" width="100">{{ lang.action }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(user, index) in users" :key="index">
                                        <td>{{ index+1 }}</td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.email }}</td>
                                        <td>{{ month }}</td>
                                        <td :class="user.salaries.length > 0 ? 'text-success' : 'text-danger'">
                                            {{ user.salaries.length > 0 ? lang.paid : lang.unpaid }}
                                        </td>
                                        <td class="text-end">
                                            <show-icon v-if="user.salaries.length > 0" :Url="route('salary.show', user.salaries[0]?.id)" />
                                            <edit-icon v-if="user.salaries.length > 0" :Url="route('salary.edit', user.salaries[0]?.id)" />
                                            <delete-icon v-if="user.salaries.length > 0" @click.prevent="deleteSalary(user.salaries[0]?.id)" />
                                            <create-icon v-if="user.salaries.length === 0" :Url="route('salary.create', {'id': user.id, 'month': month})" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
<!--                    <pagination :links="salaries.links" />-->
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Pagination from '@/Components/Pagination'
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import ShowIcon from "@/Pages/ReusableComponent/Icon/ShowIcon";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import SearchIcon from "@/Pages/ReusableComponent/Icon/SearchIcon";
import SearchButton from "@/Pages/ReusableComponent/Button/SearchButton";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import alert from "@/alert";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
import deleteAlert from "@/deleteAlert";
export default {
    name: "Index",
    props: {
        users: Object,
        month: String,
    },
    components:{
        BreezeAuthenticatedLayout,
        Pagination,
        TopBar,
        ShowIcon,
        CreateIcon,
        EditIcon,
        SearchIcon,
        SearchButton,
        RefreshIcon,
        DeleteIcon,
    },
    data() {
        return {
            lang: this.$page.props.lang,
            processing: false,
            search: false,
            tempMonth: null,
        }
    },
    methods: {
        getSearchSalary() {
            if (!this.tempMonth) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select month!'
                })
                return
            }

            this.processing = true
            this.search = true
            this.$inertia.visit('/salary', {
                method: 'get',
                data: {
                    salary_month: this.tempMonth+'-01',
                    search: true,
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

        deleteSalary(item_id) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("salary/" + item_id, {
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
        // console.log(window.location.search)
    }
}
</script>

<style scoped>

</style>
