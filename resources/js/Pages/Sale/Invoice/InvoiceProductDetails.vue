<template>
    <div>
        <print-details />
        <invoice-header-details :sale="sale" />
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
                    <tr v-for="(details, index) in sale.details" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td>{{ details.product.name }}</td>
                        <td class="text-end">{{ quantityConvertInUnit(details.quantity, details.product.unit) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import InvoiceHeaderDetails from "@/Pages/ReusableComponent/InvoiceHeaderDetails";
import convertToUpperUnit from "@/reusable/ConvertToUpperUnit";
import upperConverter from "@/reusable/ConvertToUpperUnit";
export default {
    name: "InvoiceProductDetails",
    props: {
        sale: Object,
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

</style>
