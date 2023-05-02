<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('advanced-salary.index')" :modal="showModal" />
            <div class="unit container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card-header p-0 py-1 d-sm-flex d-block  align-items-center border-0">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all }} {{ lang.advanced }}</h4><br>
                            <!-- <small class="print-none">About 563,000,000 results (0.98 seconds)</small> -->
                        </div>
                        <!-- add -->
                        <button
                            type="button"
                            role="button"
                            class="btn top-icon-button print-none ms-auto"
                            :title="lang.create_new"
                            @click="showModal">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>

                    <!-- New advances salary Modal -->
                    <advanced-modal ref="advancedModal" :users="users"
                                    :cashes="cashes"
                                    :bankAccounts="bankAccounts"
                                    :showrooms="showrooms" />

                    <!-- content body -->
                    <div class="card-body p-0">

                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" width="100">{{ lang.sl }}</th>
                                        <th scope="col">{{ lang.name }}</th>
                                        <th scope="col">{{ lang.email }}</th>
                                        <th class="text-end" scope="col">{{ lang.advanced }}</th>
                                        <th class="text-end" scope="col">{{ lang.paid }}</th>
                                        <th class="text-end" scope="col" width="100">{{ lang.action }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(user, index) in users" :key="index">
                                        <td>{{ index+1 }}</td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.email }}</td>
                                        <td class="text-end">{{ Number.parseFloat(Math.abs(user.total_advanced_paid)).toFixed(2) }}</td>
                                        <td class="text-end">{{ Number.parseFloat(user.total_advanced_receive).toFixed(2) }}</td>
                                        <td class="text-end">
                                            <show-icon :Url="route('advanced-salary.show', user.id)" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
<!--                    <pagination :links="advancedSalaries.links" />-->
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
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import AdvancedModal from "@/Pages/AdvancedSalary/AdvancedModal";
export default {
    name: "Index",
    props: {
        users: Object,
        cashes: Object,
        bankAccounts: Object,
        showrooms: Object,
    },
    components:{
        BreezeAuthenticatedLayout,
        Pagination,
        TopBar,
        ShowIcon,
        CreateIcon,
        AdvancedModal,
    },
    data() {
        return {
            lang: this.$page.props.lang
        }
    },
    methods: {
        showModal() {
            this.$refs.advancedModal.showModal()
        },
    },

}
</script>

<style scoped>

</style>
