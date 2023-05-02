<template>
    <invoice-product-details :sale="sale" />
</template>

<script>
import InvoiceProductDetails from "@/Pages/Sale/Invoice/InvoiceProductDetails";
export default {
    name: "ChalanInvoice",
    props: {
        sale: Object,
    },
    components: {
        InvoiceProductDetails,
    },
}
</script>

<style scoped>

</style>
