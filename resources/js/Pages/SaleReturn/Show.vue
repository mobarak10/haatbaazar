<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('sale-return.index')" :create-route="route('sale-return.index')" />
            <div class="container mt-2">
                <!-- Print btn -->
                <!-- End of the Print btn -->
                <div class="container-fluid mt-2">
                    <print-details />
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{ lang.sale_return_details }}</h5>

                                <div>
                                    <print-icon />
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <p class="col-4 mb-2"><span style="font-size: larger">নাম:</span> {{ sale_return.party?.name }} </p>
                                <p class="col-4 mb-2"><span style="font-size: larger">রিটার্ন নাম্বার:</span> {{ sale_return.return_no }} </p>
                                <p class="col-4 mb-2"><span style="font-size: larger">মোবাইল:</span> {{ sale_return.party?.phone }} </p>
                                <p class="col-4"><span style="font-size: larger">তারিখ:</span> {{ formatter.dateFormat(sale_return.date) }} </p>
                                <p class="col-4"><span style="font-size: larger">শো-রুম:</span> {{ sale_return.showroom?.name }} </p>
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
                                            <th class="text-end">মূল্য(৳)</th>
                                            <th class="text-end">মোট(৳)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(details, index) in sale_return.details" :key="index">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ details.product?.name }}</td>
                                            <td class="text-end">{{ quantityConvertInUnit(details.quantity, details.product?.unit) }}</td>
                                            <td class="text-end">{{ Number.parseFloat(details.return_price).toFixed(2) }}</td>
                                            <td class="text-end">{{ Number.parseFloat(details.line_total).toFixed(2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" rowspan="8" class="align-middle"></td>
                                        </tr>
                                        <tr>
                                            <td>মোট</td>
                                            <td class="text-end">
                                                {{ Number.parseFloat(sale_return.subtotal).toFixed(2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ছাড়</td>
                                            <td class="text-end">{{ Number.parseFloat(sale_return.discount).toFixed(2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>সর্বমোট</td>
                                            <td class="text-end">
                                                {{ Number.parseFloat(sale_return.return_grand_total).toFixed(2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>পেইড</td>
                                            <td class="text-end">
                                                {{ Number.parseFloat(sale_return.paid).toFixed(2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>বকেয়া</td>
                                            <td class="text-end">
                                                {{ Number.parseFloat(sale_return.return_due).toFixed(2) }}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
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
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import upperConverter from "@/reusable/ConvertToUpperUnit";
export default {
    data() {
        return {
            lang: this.$page.props.lang,
        }
    },
    name: "show",
    props: {
        sale_return: Object
    },
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

    methods: {
        quantityConvertInUnit(quantity, unit){
            return upperConverter(quantity, unit.label, unit.relation)
        },
    },
}
</script>

<style scoped>

</style>
