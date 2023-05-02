<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('sale-return.index')" :create-route="route('sale-return.create')" />
            <div class="container-fluid mt-2">
                <form method="POST" @submit.prevent="saleReturn">
                    <div class="row">
                        <product-component :sale_return="old_sale_return" :from="from" ref="product" />
                        <div class="col-md-12">
                            <div class="row">
                                <customer-component :sale_return="old_sale_return" :from="from" ref="customer" class="col-md-7"/>
                                <payment-component :sale_return="old_sale_return" :from="from" ref="payment" class="col-md-5"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import VSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import 'vue-select/dist/vue-select'
import productComponent from "@/Pages/ReusableComponent/ProductComponent";
import customerComponent from "@/Pages/ReusableComponent/CustomerComponent";
import paymentComponent from "@/Pages/ReusableComponent/PaymentComponent";
import { Inertia } from '@inertiajs/inertia'
import confirmAlert from "@/confirm";
import alert from "@/alert";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
export default {
    name: "Create",
    components: {
        BreezeAuthenticatedLayout,
        VSelect,
        productComponent,
        customerComponent,
        paymentComponent,
        TopBar,
    },
    props: {
        old_sale_return: Object,
    },
    data() {
        return {
            lang: this.$page.props.lang,
            from: 'sale_return',
        }
    },

    methods: {
        saleReturn() {
            confirmAlert().then((result) => {
                if (result.isConfirmed) {
                    this.confirmSaleReturn()
                }
            })
        },

        confirmSaleReturn(){
            let form = {
                products: []
            }
            if (this.$refs.product.cartProducts.length === 0) {
                alert().fire({
                    icon: 'warning',
                    title: 'Cart is empty!'
                })
                return
            }
            if (!this.$refs.customer.customerId && !this.$refs.customer.customer.name) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select customer!'
                })
                return
            }

            if (!this.$refs.payment.cashId && !this.$refs.payment.bankAccountId) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select payment method!'
                })
                return
            }

            // for products
            let quantityError = false;
            this.$refs.product.cartProducts.forEach(product => {
                if (product.quantity <= 0) {
                    quantityError = true
                    product.error = "Quantity can\'t be empty"
                }else{
                    quantityError = false
                    product.error = ""
                }
                form.products.push({
                    id: product.id,
                    quantity: product.quantity,
                    quantity_in_unit: product.quantity_in_unit,
                    quantity_for_price : product.quantity_for_price,
                    purchase_price: product.purchase_price,
                    return_price: product.price,
                    line_total: product.total_price
                });
            });

            if (quantityError) {
                form.products = []
                return
            }

            form.party_id = this.$refs.customer.customerId
            form.note = this.$refs.customer.note
            form.showroom_id = this.$refs.product.showroom_id
            form.date = this.$refs.product.date
            form.paid_from = this.$refs.payment.payment_type
            form.cash_id = this.$refs.payment.cashId
            form.bank_account_id = this.$refs.payment.bankAccountId
            form.discount = this.$refs.payment.discount || 0
            form.paid = this.$refs.payment.paid || 0
            form.grand_total = this.$refs.payment.grandTotal
            if (this.$refs.payment.dueOrChange > 0) {
                form.due = this.$refs.payment.dueOrChange
                form.change = 0
            }else{
                form.party_balance = Math.abs(this.$refs.payment.dueOrChange)
                form.due = 0
            }
            form.subtotal = this.$refs.payment.subtotal
            form.previous_balance = this.$refs.payment.customerBalance

            if (this.old_sale_return) {
                Inertia.put(this.route("sale-return.update", this.old_sale_return.id), form)
            }else{
                Inertia.post(this.route("sale-return.store"), form)
            }
        },
    },

    mounted() {
    }

}
</script>

<style scoped>

</style>
