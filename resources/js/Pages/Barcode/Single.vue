<template>
    <div>
        <Authenticated>
            <div class="container">
                <div class="row">
                    <div class="col-md-12 py-3 p-print-0">
                        <div class="card">

                            <div class="card-header d-flex justify-content-between align-items-center print-none">
                                <h5 class="m-0">{{ $page.props.lang.barcode }}</h5>

                                <div class="btn-group" role="group" aria-level="Action area">
                                    <refresh-icon :Url="route('barcode.single')" />
                                    <print-icon />
                                </div>
                            </div>

                            <div class="card-body p-print-0">

                                <!-- generator -->
                                <div class="print-none">
                                    <form @submit.prevent="getBarcodeProduct" method="GET" class="d-flex">
                                        <input type="text" v-model="barcode" class="form-control mx-2" :placeholder="$page.props.lang.enter_barcode" required>
                                        <button type="submit" class="btn btn-primary text-white">{{ $page.props.lang.search }}</button>
                                    </form>
                                </div>

                                <!-- barcode list -->
                                <div class="row mt-3 text-center barcode-wrapper">
                                    <div v-if="product" class="col-md-3">
                                        <h6 style="font-size: small">{{ product.name }}</h6>
                                        <img id="singleBarcode"/>
                                        <h6 class="d-block mr-3">{{ $page.props.lang.price }} {{ product.sale_price}}</h6>
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
export default {
    name: "Single",
    props: {
        product:  Object
    },
    components: {
        Authenticated,
        PrintIcon,
        RefreshIcon,
    },
    data() {
       return {
           barcode: null,
       }
    },
    methods: {
        showBarcode() {
            if (this.product) {
                let JsBarcode = require('jsbarcode');
                let id = document.getElementById('singleBarcode')
                JsBarcode(id, this.product.barcode, {
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
            this.$inertia.visit('/barcode/single', {
                method: 'get',
                data: {
                    barcode: this.barcode
                },
                preserveState: true,
                onSuccess: page => {
                    if (!this.product){
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
        }
    },
    mounted() {
        if (this.product) {
            this.showBarcode()
        }
    }
}
</script>

<style scoped>

</style>
