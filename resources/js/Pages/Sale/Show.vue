<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('sale.index')" :create-route="route('sale.create')" />

            <div class="container mt-2">
                <!-- Print btn -->
                <!-- End of the Print btn -->
                <div class="container-fluid mt-2">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between print-none align-items-center">
                                <h5>{{lang.sale_details}}</h5>
                                <div>
                                    <button class="btn btn-secondary mx-2" :title="lang.print_chalan" @click="printChalan">
                                        চালান <i class="bi bi-printer"></i>
                                    </button>

                                    <button class="btn btn-secondary" :title="lang.print_gate_pass" @click="printGatePass">
                                        গেইট পাশ <i class="bi bi-printer"></i>
                                    </button>

                                    <print-icon />
                                </div>
                            </div>
                        </div>
                        <div class="row" id="main-invoice">
                            <div class="card-body col-6">
                                <main-invoice :sale="sale" />
                            </div>
                            <div class="card-body print-show col-6">
                                <main-invoice :sale="sale" />
                            </div>
                        </div>

                        <div class="row d-none" id="chalan-invoice">
                            <chalan-invoice class="col-6" :sale="sale" />
                            <chalan-invoice class="col-6" :sale="sale" />
                        </div>

                        <div class="row d-none" id="gate-pass-invoice">
                            <chalan-invoice :sale="sale" />
                        </div>
                    </div>
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import Invoice from "@/Pages/Sale/Invoice/Invoice";
import MainInvoice from "@/Pages/ReusableComponent/MainInvoice";
import ChalanInvoice from "@/Pages/Sale/Invoice/ChalanInvoice";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
export default {
    data() {
        return {
            lang: this.$page.props.lang,
        }
    },
    name: "show",
    props: {
        sale: Object,
        numto: Object,
    },
    components: {
        BreezeAuthenticatedLayout,
        Invoice,
        MainInvoice,
        PrintDetails,
        ChalanInvoice,
        TopBar,
        PrintIcon,
    },

    methods: {
        printMainInvoice() {
            document.querySelector("#chalan-invoice").classList.add("d-none");
            document.querySelector("#gate-pass-invoice").classList.add("d-none");
            window.print();
            this.afterPrint()
        },

        printChalan() {
            document.querySelector("#chalan-invoice").classList.remove("d-none");
            document.querySelector("#main-invoice").classList.add("d-none");
            document.querySelector("#gate-pass-invoice").classList.add("d-none");
            window.print();
            this.afterPrint()
        },

        printGatePass() {
            document.querySelector("#gate-pass-invoice").classList.remove("d-none");
            document.querySelector("#chalan-invoice").classList.add("d-none");
            document.querySelector("#main-invoice").classList.add("d-none");
            window.print();
            this.afterPrint()
        },

        afterPrint() {
            document.querySelector("#chalan-invoice").classList.add("d-none");
            document.querySelector("#gate-pass-invoice").classList.add("d-none");
            document.querySelector("#main-invoice").classList.remove("d-none");
        },
    },

    mounted() {

    }
}
</script>

<style scoped>

</style>
