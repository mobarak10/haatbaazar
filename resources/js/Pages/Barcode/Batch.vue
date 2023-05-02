<template>
    <div>
        <Authenticated>
            <div class="container">
                <div class="row">
                    <div class="col-md-12 py-3 p-print-0">
                        <div class="card">

                            <div class="card-header d-flex justify-content-between align-items-center print-none">
                                <h5 class="m-0">{{ $page.props.lang.batch }} {{ $page.props.lang.barcode }}</h5>

                                <div class="btn-group" role="group" aria-level="Action area">
                                    <refresh-icon :Url="route('barcode.batch')" />
                                    <print-icon />
                                </div>
                            </div>

                            <div class="card-body p-print-0">

                                <!-- generator -->
                                <div class="print-none">
                                    <form @submit.prevent="getBarcodeProduct" method="GET" class="d-flex">
                                        <input type="text" v-model="barcode" class="form-control mx-2" :placeholder="$page.props.lang.enter_barcode" required>
                                        <input type="number" v-model="quantity" class="form-control mx-2" :placeholder="$page.props.lang.quantity" required>
                                        <button type="submit" class="btn btn-primary text-white">{{ $page.props.lang.search }}</button>
                                    </form>
                                </div>

                                <!-- barcode list -->
                                <div class="row mt-3 text-center barcode-wrapper">
                                    <div v-if="records['product']" class="col-md-3">
                                        <BatchBarcode :product="records['product']" v-for="(number, index) in parseInt(quantity)" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Authenticated>
    </div>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import RefreshIcon from "@/Pages/ReusableComponent/Icon/RefreshIcon";
import alert from "@/alert";
import BatchBarcode from "@/Pages/Barcode/BatchBarcode";
export default {
    name: "Batch",
    props: {
        records: Object
    },
    components: {
        Authenticated,
        PrintIcon,
        RefreshIcon,
        BatchBarcode,
    },
    data() {
        return {
            barcode: null,
            quantity: null,
        }
    },
    methods: {
        showBarcode() {
            let elements = document.getElementsByName("barcode");
            for(var i = 0; i < elements.length; i++)        {
                var id = elements[i].name + "i";
                elements[i].id = id;
                var value = elements[i].getAttribute("jsbarcode-value");
                JsBarcode("#" + id, value, {
                    format: "code39",
                    lineColor: "#0aa",
                    width: 4,
                    height: 40,
                    displayValue: false
                });
            }
            if (this.records['product']) {
                let JsBarcode = require('jsbarcode');
                let id = document.getElementById('batchBarcode')
                JsBarcode(id, this.records['product'].barcode, {
                    format: "CODE128",
                    font: 'Sans-serif',
                    width: 1.7,
                    fontSize: 15,
                    height: 70,
                    margin: 2,
                    background: '#f9f9f9',
                });
            }
        },
        getBarcodeProduct() {
            this.$inertia.visit('/barcode/batch', {
                method: 'get',
                data: {
                    barcode: this.barcode,
                    quantity: this.quantity
                },
                preserveState: true,
                onSuccess: page => {
                    if (!this.records['product']){
                        let toast = alert()
                        toast.fire({
                            icon: 'error',
                            title: 'No Product Found!'
                        })
                    }
                    this.showBarcode()
                },
                onFinish: visit => {},
            })
        },
        initialValues() {
            this.barcode = this.records['barcode']
            this.quantity = this.records['quantity']
            if (this.records['product']) {
                this.showBarcode()
            }
        },
    },
    mounted() {
        this.initialValues()
    }
}
</script>

<style scoped>

</style>
