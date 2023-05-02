<template>
    <div>
        <breeze-authenticated-layout>
            <print-details />
            <div class="container unit">
                <!-- table -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ lang.income_report }}</h5>
                            <div>
                                <refresh-icon :Url="route('report.income-report')" />
                                <search-icon Url="#income-search-collapse" />
                                <print-icon />
                            </div>
                        </div>
                    </div>

                    <!-- search form start -->
                    <div class="card-body">
                        <div class="print-none" :class="[search ? '' : 'collapse']" id="income-search-collapse">
                            <form @submit.prevent="getYearWiseData" class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <label class="form-label required" for="date">{{ lang.select_year }}</label>
                                            <select v-model="searchYear" class="form-select" id="date">
                                                <option :value="null">{{ lang.select_year }}</option>
                                                <option v-for="(year, index) in years" :key="index" :value="year">{{ year }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2" style="padding-top: 30px">
                                            <search-button :processing="processing" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="mt-2 row col-md-12">
                            <div class="col-md-12">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ lang.sl }}</th>
                                            <th>{{ lang.income_sector }}</th>
                                            <th class="text-end" v-for="(month, index) in months" :key="index">{{ month }}</th>
                                            <th class="text-end">{{ lang.total }}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-if="incomeDetails" v-for="(incomeSector, index) in incomeDetails" :key="index">
                                            <td class="text-wrap">{{ index + 1 }}</td>
                                            <td>{{ incomeSector.name }}</td>
                                            <td class="text-end" v-for="(month, index) in months" :key="index">
                                                {{ incomeSector.sum_of_each_month[month] ? Number.parseFloat(incomeSector.sum_of_each_month[month]).toFixed(2) : Number.parseFloat(0).toFixed(2) }}
                                            </td>
                                            <td class="text-end">
                                                {{ Number.parseFloat(getLineTotal(incomeSector)).toFixed(2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="14" class="text-end">{{ lang.total }}</td>
                                            <td class="text-end">
                                                {{ Number.parseFloat(getGrandTotal(incomeDetails)).toFixed(2) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- search form end -->
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import alert from "@/alert";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import SearchButton from "@/Pages/ReusableComponent/Button/SearchButton";
import SearchIcon from "@/Pages/ReusableComponent/Icon/SearchIcon";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
export default {
    name: "Index",
    components: {
        SearchIcon,
        RefreshIcon,
        BreezeAuthenticatedLayout,
        PrintIcon,
        SearchButton,
        PrintDetails,
    },
    props: {
        incomeDetails: Object,
        months: Object,
    },
    data() {
        return{
            lang:this.$page.props.lang,
            years: [],
            searchYear: null,
            search: false,
            processing: false,
        }
    },

    methods: {
        getYearWiseData() {
            if (!this.searchYear) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select year!'
                })
                return
            }
            this.processing = true;
            this.search = true;
            this.$inertia.visit('/report/income-report', {
                method: 'get',
                data: {
                    search: true,
                    year: this.searchYear,
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

        getLineTotal(sector){
            let lineTotal = 0;
            return lineTotal = this.getSumValue(sector.sum_of_each_month)
        },

        getGrandTotal(incomeSector){
            let grandTotal = 0;
            incomeSector.forEach(sector => {
                grandTotal += this.getSumValue(sector.sum_of_each_month)
            })
            return grandTotal
        },

        getSumValue(object) {
            return Object.values(object).reduce((a, b) => a + b, 0);
        },
    },

    mounted() {
        for (let i = 2021; i <= new Date().getFullYear(); i++){
            this.years.push(i)
        }

        const queryString = window.location.search;
        const parameters = new URLSearchParams(queryString);
        if(parameters.get('year')){
            this.search = true
            this.searchYear = parameters.get('year')
        }
    }
}
</script>

<style scoped>

</style>
