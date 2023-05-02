<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('salary.index')" :create-route="route('salary.create')" />

            <div class="container mt-2">
                <!-- Print btn -->
                <!-- End of the Print btn -->
                <div class="container-fluid mt-2">
                    <print-details />

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{ lang.salary }}</h5>

                                <div>
                                    <print-icon />
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <p class="col-4 mb-2"><span style="font-size: larger">নাম:</span> {{ salary.user?.name }} </p>
                                <p class="col-4 mb-2"><span style="font-size: larger">সেলারি নাম্বার:</span> {{ salary.voucher_no }} </p>
                                <p class="col-4 mb-2"><span style="font-size: larger">ইমেইল:</span> {{ salary.user?.email }} </p>
                                <p class="col-4"><span style="font-size: larger">সেলারির মাস:</span> {{ formatter.monthFormat(salary.salary_month) }} </p>
                                <p class="col-4"><span style="font-size: larger">সেলারির দেয়ার তারিখ:</span> {{ formatter.dateFormat(salary.given_date) }} </p>
                            </div>
                            <hr>
                            <div class="row mt-2">
                                <div class="col-12 table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                        <tr>
                                            <th>ক্র.নং</th>
                                            <th>পণ্যের নাম</th>
                                            <th class="text-end">পরিমান</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(details, index) in salary.details" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ details.purpose.charAt(0).toUpperCase() + details.purpose.replace(/_/g, " ").slice(1) }}</td>
                                                <td class="text-end">{{ Number.parseFloat(Math.abs(details.amount)).toFixed(2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="1" rowspan="9" class="align-middle"></td>
                                            </tr>
                                            <tr>
                                                <td>মোট</td>
                                                <td class="text-end">
                                                    {{ Number.parseFloat(salary.total_salary_paid).toFixed(2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <P>{{ lang.note }}:</P> <span v-html="salary.note"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import * as _formatter from '@/Helpers/formatter'
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
export default {
    name: "show",
    data(){
        return{
            lang: this.$page.props.lang,
        }
    },
    props: {
        salary: Object
    },
    components: {
        BreezeAuthenticatedLayout,
        TopBar,
        PrintDetails,
        PrintIcon,
    },

    computed: {
        formatter() {
            return _formatter
        }
    },
}
</script>

<style scoped>

</style>
