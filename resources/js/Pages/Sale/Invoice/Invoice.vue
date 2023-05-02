<template>
    <div class="card-body print-show">
        <main-invoice :sale="sale"/>
    </div>
</template>

<script>
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import MainInvoice from "@/Pages/ReusableComponent/MainInvoice";
export default {
    name: " Invoice",
    props: {
        sale: Object
    },
    data() {
        return {
            lang: this.$page.props.lang,
        }
    },
    components: {
        PrintDetails,
        MainInvoice,
    },
}
</script>

<style scoped>

</style>
