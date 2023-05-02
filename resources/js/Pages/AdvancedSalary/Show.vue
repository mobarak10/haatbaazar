<template>
    <div>
        <breeze-authenticated-layout>
            <print-details />
            <div class="container print-none">
                <ul class="nav nav-tabs mt-2">
                    <li class="nav-item">
                        <inertia-link class="nav-link" :href="route('advanced-salary.index')">
                            {{lang.all_records}}
                        </inertia-link>
                    </li>
                </ul>
            </div>

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
                        <print-icon />
                    </div>
                    <hr>

                    <!-- New advances salary Modal -->
                    <advanced-modal ref="advancedModal" :advancedSalary="advancedSalary" :users="users"
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
                                        <th scope="col">{{ lang.date }}</th>
                                        <th class="text-end" scope="col">{{ lang.amount }}</th>
                                        <th class="text-end" scope="col">{{ lang.status }}</th>
                                        <th class="text-center">{{ lang.note }}</th>
                                        <th class="text-end print-none" scope="col" width="100">{{ lang.action }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(advanced, index) in userAdvancedSalaries.data" :key="index">
                                        <td>{{ index+1 }}</td>
                                        <td>{{ formatter.dateFormat(advanced.date) }}</td>
                                        <td class="text-end">{{ Number.parseFloat(Math.abs(advanced.amount)).toFixed(2) }}</td>
                                        <td class="text-end">{{ advanced.amount > 0 ? lang.advanced+' '+lang.taken : lang.advanced+' '+lang.given }}</td>
                                        <td class="text-center">{{ advanced.note }}</td>
                                        <td class="text-end print-none">
                                            <edit-modal-icon @click.prevent="editAdvanced(advanced)" />
                                            <delete-icon @click.prevent="deleteAdvanced(advanced.id)" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="userAdvancedSalaries.links" />
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Pagination from "@/Components/Pagination";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import EditModalIcon from "@/Pages/ReusableComponent/Icon/EditModalIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
import AdvancedModal from "@/Pages/AdvancedSalary/AdvancedModal";
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import * as _formatter from '@/Helpers/formatter'
export default {
    name: "Show",
    components: {
        BreezeAuthenticatedLayout,
        Pagination,
        AdvancedModal,
        PrintIcon,
        EditModalIcon,
        DeleteIcon,
        PrintDetails,
    },
    props: {
        userAdvancedSalaries: Object,
        users: Object,
        cashes: Object,
        bankAccounts: Object,
        showrooms: Object,
    },
    computed: {
        formatter() {
            return _formatter
        }
    },
    data() {
        return {
            lang: this.$page.props.lang,
            advancedSalary: null,
        }
    },
    methods: {
        editAdvanced(item) {
            this.advancedSalary = item
            this.$refs.advancedModal.showModal()
        },
        deleteAdvanced(advancedId) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(advancedId, {
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
        console.log(this.userAdvancedSalaries)
    }
}
</script>

<style scoped>

</style>
