<template>
    <div class="row">
        <p class="col-4"><span style="font-size: larger">নাম:</span> {{ sale.party?.name }} </p>
        <p class="col-4"><span style="font-size: larger">মোকাম:</span> {{ sale.party?.mokam?.name }} </p>
        <p class="col-4"><span style="font-size: larger">ভাউচার নাম্বার:</span> {{ sale.invoice_no }} </p>
        <p class="col-4"><span style="font-size: larger">মোবাইল:</span> {{ sale.party?.phone }} </p>
        <p class="col-4"><span style="font-size: larger">তারিখ:</span> {{ formatter.dateFormat(sale.date) }} </p>
        <p class="col-4"><span style="font-size: larger">শো-রুম:</span> {{ sale.showroom?.name }} </p>
        <p class="col-6"><span style="font-size: larger">সময়:</span> {{ formatter.timeFormat(sale.created_at) }} </p>
    </div>
</template>

<script>
import * as _formatter from "@/Helpers/formatter";
export default {
    name: "InvoiceHeaderDetails",
    props: {
        sale: Object,
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
