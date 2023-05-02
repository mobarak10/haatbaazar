<template>
    <div class="col-6 mb-2">
        <input
            type="text"
            @keydown.enter.prevent="getBarcodeProduct(barcode)"
            v-model="barcode"
            autofocus
            :placeholder="lang.enter_barcode"
            class="form-control">
    </div>
</template>

<script>

import alert from "@/alert";

export default {
    name: "ProductByBarcodeComponent",
    props: {
        products: Object,
        cartProducts: Object,
    },
    data() {
        return {
            barcode: null,
            productId: null,
            lang: this.$page.props.lang
        }
    },

    methods: {
        getBarcodeProduct(barcode) {
            if (barcode) {
                let product = this.products.find(product => product.barcode === barcode)
                const index = this.cartProducts.findIndex(
                    product => product.barcode === barcode
                );

                if (index === -1) {
                    if (product){
                        this.productId = product.id
                        this.$emit('barcodeProduct')
                    }else{
                        let toast = alert()
                        toast.fire({
                            icon: 'error',
                            title: 'Product not found!'
                        })
                    }
                }else {
                    let toast = alert()
                    toast.fire({
                        icon: 'error',
                        title: 'Product already added in cart!'
                    })
                }
            }
            this.barcode = null
        },
    },
}
</script>

<style scoped>

</style>
