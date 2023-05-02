<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :indexRoute="route('customer.index')" :createRoute="route('customer.create')" />
            <div class="supplier container">
                <PrintDetails />
                <!-- table -->
                <div class="card border-0">
                    <div class="mx-3 d-flex justify-content-between align-items-center">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all }} {{ lang.sms }}</h4><br>
                        </div>

                        <div class="print-none">
                            <refresh-icon :Url="route('sms.index')" />
                            <search-icon Url="#sms-search-collapse" />
                            <print-icon />
                        </div>
                    </div>


                    <!-- content body -->
                    <div class="card-body p-0">
                        <!-- search from start -->
                        <div class="print-none" :class="[search ? '' : 'collapse']" id="sms-search-collapse">
                            <form @submit.prevent="getSearchSms" >
                                <div class="row">
                                    <div class="col-md-3">
                                        <from-date ref="fromDate" />
                                    </div>
                                    <div class="col-md-3">
                                        <to-date ref="toDate" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="number" class="form-label">{{ lang.mobile }}</label>
                                        <input type="text" v-model="number" id="number" class="form-control">
                                    </div>

                                    <div class="mt-4 text-right col-md-2">
                                        <search-button :processing="processing" />
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- search from end -->

                        <div class="table-responsive mt-4">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" width="5%">{{lang.sl}}</th>
                                        <th scope="col">{{ lang.date }}</th>
                                        <th style="max-width: 40px" scope="col">{{ lang.phone }}</th>
                                        <th style="max-width: 40px" scope="col">{{ lang.sms }}</th>
                                        <th scope="col" class="text-end">{{ lang.status }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(message,index) in messages.data" :key="index">
                                        <th style="max-width: 40px" scope="row">{{ messages.from + index }}</th>
                                        <td>{{ formatter.dateFormat(message.created_at)  }}</td>
                                        <td>{{ message.number }}</td>
                                        <td style="max-width: 40px" class="text-wrap">{{ message.message }}</td>
                                        <td class="text-end text-success">{{ message.status }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="messages.links" />
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Pagination from '@/Components/Pagination'
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import SearchButton from "@/Pages/ReusableComponent/Button/SearchButton";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import SearchIcon from "@/Pages/ReusableComponent/Icon/SearchIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import FromDate from "@/Pages/ReusableComponent/Date/FromDate";
import ToDate from "@/Pages/ReusableComponent/Date/ToDate";
import * as _formatter from '@/Helpers/formatter'
import alert from "@/alert";
export default {
    name: "Index",
    components: {
        BreezeAuthenticatedLayout,
        Pagination,
        PrintDetails,
        SearchButton,
        RefreshIcon,
        SearchIcon,
        PrintIcon,
        TopBar,
        FromDate,
        ToDate,
    },
    computed: {
        formatter() {
            return _formatter
        }
    },
    props: {
        messages: Object,
    },
    methods: {
        getSearchSms() {
            this.processing = true
            this.search = true
            this.$inertia.visit('/sms/sms-report', {
                method: 'get',
                data: {
                    search: this.search,
                    number: this.number,
                    from_date: this.$refs.fromDate.from_date,
                    to_date: this.$refs.toDate.to_date,
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
    data() {
        return{
            lang: this.$page.props.lang,
            number: null,
            processing: false,
            search: false,
        }
    },

}
</script>

<style scoped>

</style>
