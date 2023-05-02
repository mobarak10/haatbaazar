<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('damage-stock.index')" :create-route="route('damage-stock.create')" />

            <div class="container mt-2">
                <!-- Print btn -->
                <!-- End of the Print btn -->
                <div class="container-fluid mt-2">
                    <print-details />

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{lang.damage_details}}</h5>

                                <div>
                                    <print-icon />
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <p class="col-4 mb-2"><span style="font-size: larger">ডেমেজ নাম্বার:</span> {{ damage.damage_no }} </p>
                                <p class="col-4"><span style="font-size: larger">তারিখ:</span> {{ formatter.dateFormat(damage.date) }} </p>
                                <p class="col-4"><span style="font-size: larger">শো-রুম:</span> {{ damage.showroom?.name }} </p>
                            </div>
                            <hr>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>ক্র.নং</th>
                                                <th>পণ্যের নাম</th>
                                                <th class="text-end">পরিমান</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(details, index) in damage.details" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ details.product.name }}</td>
                                                <td class="text-end">{{ quantityConvertInUnit(details.quantity, details.product.unit) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="text-center">
                                <P>Note: {{ damage.comment }}</P>
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
        damage: Object
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
