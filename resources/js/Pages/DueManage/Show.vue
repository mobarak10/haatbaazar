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

            <div class="container mt-2">
                <!-- Print btn -->
                <!-- End of the Print btn -->
                <div class="container-fluid mt-2">
                    <print-details />
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between print-none align-items-center">
                                <h5>{{ lang.due_manage }}</h5>
                                <div>
                                    <print-icon />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card-body col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>{{ lang.party_name }}</th>
                                                <th class="text-end">{{ lang.date }}</th>
                                                <th class="text-end">{{ lang.amount }}</th>
                                                <th class="text-end">{{ lang.status }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ due_manage.party?.name }}</td>
                                                <td class="text-end">{{ formatter.dateFormat(due_manage.date) }}</td>
                                                <td class="text-end">{{ Number.parseFloat(Math.abs(due_manage.amount)).toFixed(2) }}</td>
                                                <td class="text-end">{{ due_manage.amount >= 0 ? lang.receive : lang.paid }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <P>Note: {{ due_manage.note }}</P>
                            <p class="d-none d-print-block">Developed By Haat Baazar</p>
                        </div>
                    </div>
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import * as _formatter from "@/Helpers/formatter";
export default {
    name: "Show",
    components: {
        BreezeAuthenticatedLayout,
        PrintDetails,
        TopBar,
        PrintIcon,
    },
    computed: {
        formatter() {
            return _formatter
        }
    },
    props: {
        type: String,
        due_manage: Object,
    },
    data() {
        return {
            lang: this.$page.props.lang,
        }
    },
}
</script>

<style scoped>

</style>
