<template>
    <print-details />
    <invoice-header-details :sale="sale" />
    <hr>
    <div class="row mt-2">
        <div class="col-12">
            <div class="table-responsive">
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
                    <tr v-for="(details, index) in sale.details" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td class="text-wrap" style="max-width: 110px">{{ details.product?.name }}</td>
                        <td class="text-end">{{ quantityConvertInUnit(details.quantity, details.product?.unit) }}</td>
                        <td class="text-end">{{ details.sale_price }}</td>
                        <td class="text-end">{{ Number.parseFloat(details.line_total).toFixed(2) }}</td>
                    </tr>

                    <tr>
                        <td colspan="3" rowspan="9" class="align-middle text-center">
                            {{ sale.total_in_word }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-end">মোট</td>
                        <td class="text-end">
                            {{ Number.parseFloat(sale.subtotal).toFixed(2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-end">ছাড়</td>
                        <td class="text-end">{{ Number.parseFloat(sale.discount).toFixed(2) }}</td>
                    </tr>
                    <tr v-if="sale.transport_cost > 0">
                        <td class="text-end">পরিবহন খরচ</td>
                        <td class="text-end">{{ Number.parseFloat(sale.transport_cost).toFixed(2) }}</td>
                    </tr>
                    <tr v-if="sale.labour_cost > 0">
                        <td class="text-end">লেবার খরচ</td>
                        <td class="text-end">{{ Number.parseFloat(sale.labour_cost).toFixed(2) }}</td>
                    </tr>

                    <tr>
                        <td class="text-end">সাবেক</td>
                        <td class="text-end">
                            {{ Number.parseFloat(sale.previous_balance).toFixed(2) }}
                        </td>
                    </tr>

                    <tr>
                        <td class="text-end">সর্বমোট</td>
                        <td class="text-end">
                            {{ Number.parseFloat(sale.grand_total).toFixed(2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-end">জমা</td>
                        <td class="text-end">
                            {{ Number.parseFloat(sale.paid).toFixed(2) }}
                        </td>
                    </tr>

                    <tr>
                        <td class="text-end">বকেয়া</td>
                        <td class="text-end">
                            {{ Number.parseFloat(sale.due).toFixed(2) }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="text-center">
        <P>Note: {{ sale.note }}</P>
        <p class="d-none d-print-block">Developed By Haat Baazar</p>
    </div>
</template>

<script>
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import InvoiceHeaderDetails from "@/Pages/ReusableComponent/InvoiceHeaderDetails";
import upperConverter from "@/reusable/ConvertToUpperUnit";

export default {
    name: "MainInvoice",
    props: {
        sale: Object,
    },
    data() {
        return {
            lang: this.$page.props.lang,
        }
    },
    components: {
        PrintDetails,
        InvoiceHeaderDetails,
    },

    methods: {
        quantityConvertInUnit(quantity, unit){
            return upperConverter(quantity, unit.label, unit.relation)
        },
    },
}
</script>

<style scoped>
.table th, .table td{
    white-space: initial!important;
}
</style>
