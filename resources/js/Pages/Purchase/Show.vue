<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('purchase.index')" :create-route="route('purchase.create')" />

            <div class="container mt-2">
                <!-- Print btn -->
                <!-- End of the Print btn -->
                <div class="container-fluid mt-2">
                    <print-details />

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{ lang.purchase_details }}</h5>

                                <div>
                                    <print-icon />
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <p class="col-4 mb-2"><span style="font-size: larger">নাম:</span> {{ purchase.party?.name }} </p>
                                <p class="col-4 mb-2"><span style="font-size: larger">ভাউচার নাম্বার:</span> {{ purchase.voucher_no }} </p>
                                <p class="col-4 mb-2"><span style="font-size: larger">মোবাইল:</span> {{ purchase.party?.phone }} </p>
                                <p class="col-4"><span style="font-size: larger">তারিখ:</span> {{ formatter.dateFormat(purchase.formatted_date) }} </p>
                                <p class="col-4"><span style="font-size: larger">শো-রুম:</span> {{ purchase.showroom?.name }} </p>
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
                                                <th
                                                    v-if="permissions.includes('product-purchase_price')"
                                                    class="text-end"
                                                >
                                                    মূল্য(৳)
                                                </th>
                                                <th
                                                    v-if="permissions.includes('product-purchase_price')"
                                                    class="text-end"
                                                >
                                                    মোট(৳)
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(details, index) in purchase.details" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ details.product.name }}</td>
                                                <td class="text-end">{{ quantityConvertInUnit(details.quantity, details.product.unit) }}</td>
                                                <td v-if="permissions.includes('product-purchase_price')"
                                                    class="text-end"
                                                >
                                                    {{ Number.parseFloat(details.purchase_price).toFixed(2) }}
                                                </td>
                                                <td v-if="permissions.includes('product-purchase_price')"
                                                    class="text-end"
                                                >
                                                    {{ Number.parseFloat(details.line_total).toFixed(2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" rowspan="9" class="align-middle"></td>
                                            </tr>

                                            <tr v-if="permissions.includes('product-purchase_price')">
                                                <td>মোট</td>
                                                <td class="text-end">
                                                    {{ Number.parseFloat(purchase.subtotal).toFixed(2) }}
                                                </td>
                                            </tr>
                                            <tr v-if="permissions.includes('product-purchase_price')">
                                                <td>ছাড়</td>
                                                <td class="text-end">{{ Number.parseFloat(purchase.discount).toFixed(2) }}</td>
                                            </tr>
                                            <tr v-if="permissions.includes('product-purchase_price')">
                                                <td>পরিবহন খরচ</td>
                                                <td class="text-end">{{ Number.parseFloat(purchase.purchase_cost.transport_cost).toFixed(2) }}</td>
                                            </tr>
                                            <tr v-if="permissions.includes('product-purchase_price')">
                                                <td>লেবার খরচ</td>
                                                <td class="text-end">{{ Number.parseFloat(purchase.purchase_cost.labour_cost).toFixed(2) }}</td>
                                            </tr>

                                            <tr v-if="permissions.includes('product-purchase_price')">
                                                <td>পূর্বের টাকা</td>
                                                <td class="text-end">
                                                    {{ Number.parseFloat(Math.abs(purchase.previous_balance)).toFixed(2) }} {{ purchase.previous_balance >= 0 ? 'Rec' : 'Pay' }}
                                                </td>
                                            </tr>

                                            <tr v-if="permissions.includes('product-purchase_price')">
                                                <td>সর্বমোট</td>
                                                <td class="text-end">
                                                    {{ Number.parseFloat(purchase.grand_total - purchase.previous_balance).toFixed(2) }}
                                                </td>
                                            </tr>
                                            <tr v-if="permissions.includes('product-purchase_price')">
                                                <td>পেইড</td>
                                                <td class="text-end">
                                                    {{ Number.parseFloat(purchase.paid).toFixed(2) }}
                                                </td>
                                            </tr>
                                            <tr v-if="permissions.includes('product-purchase_price')">
                                                <td>{{ (purchase.grand_total - purchase.previous_balance) - purchase.paid >= 0 ? 'বকেয়া' : 'পার্টি ব্যালেন্স' }}</td>
                                                <td class="text-end">
                                                    {{ Number.parseFloat(Math.abs((purchase.grand_total - purchase.previous_balance) - purchase.paid)).toFixed(2) }}
                                                    {{ (purchase.grand_total - purchase.previous_balance) - purchase.paid >= 0 ? 'Pay' : 'Rec' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="text-center">
                                <P>{{ lang.note }}: {{ purchase.note }}</P>
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
import upperConverter from "@/reusable/ConvertToUpperUnit";
export default {
    data(){
        return{
            lang: this.$page.props.lang,
            permissions: this.$page.props.permissionNames
        }
    },
    name: "show",
    props: {
        purchase: Object
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
    methods: {
        quantityConvertInUnit(quantity, unit){
            return upperConverter(quantity, unit.label, unit.relation)
        },
    },
}
</script>

<style scoped>

</style>
