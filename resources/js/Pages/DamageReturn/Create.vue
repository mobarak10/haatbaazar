<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('damage-return.index')" :create-route="route('damage-return.create')" />
            <div class="container-fluid mt-2">
                <form method="POST" @submit.prevent="damageReturn">
                    <div class="row">
                        <product-component :old-damage-return="oldDamageReturn" :from="from" ref="product" />
                        <div class="col-md-12">
                            <div class="row">
                                <supplier-component :old-damage-return="oldDamageReturn" :from="from" ref="supplier" class="col-md-7"/>
                                <payment-component
                                    :old-damage-return="oldDamageReturn"
                                    :from="from"
                                    ref="payment"
                                    class="col-md-5"
                                />
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
import productComponent from "@/Pages/PurchaseReusableComponent/ProductComponent";
import supplierComponent from "@/Pages/PurchaseReusableComponent/SupplierComponent";
import paymentComponent from "@/Pages/PurchaseReusableComponent/PaymentComponent";
import { Inertia } from '@inertiajs/inertia'
import confirmAlert from "@/confirm";
import alert from "@/alert";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import {mapMutations} from "vuex";

export default {
    name: "Create",
    components: {
        BreezeAuthenticatedLayout,
        VSelect,
        productComponent,
        supplierComponent,
        paymentComponent,
        TopBar,
    },

    props: {
        oldDamageReturn: Object,
    },

    data() {
        return {
            lang: this.$page.props.lang,
            from: 'damage_return',
        }
    },

    methods: {
        ...mapMutations([
            'mutationLoadOldData',
            'mutationLoadSupplierBalance',
            'mutationLoadOldData',
            'mutationLoadSubtotal'
        ]),
        damageReturn(){
            confirmAlert().then((result) => {
                if (result.isConfirmed) {
                    this.confirmDamageReturn()
                }
            })
        },
        confirmDamageReturn(){
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
            if (!this.$refs.supplier.supplierId) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select supplier!'
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
                if (product.quantity > 0) {
                    form.products.push({
                        id: product.id,
                        quantity: product.quantity,
                        quantity_in_unit: product.quantity_in_unit,
                        purchase_price: product.purchase_price,
                        return_price: product.price,
                        line_total: product.total_price
                    });
                }
            });

            if (quantityError) {
                form.products = []
                return
            }
            form.party_id = this.$refs.supplier.supplierId
            form.note = this.$refs.supplier.note
            form.business_group_id = this.$refs.product.business_group_id
            form.date = this.$refs.product.date
            form.paid_to = this.$refs.payment.payment_type
            form.cash_id = this.$refs.payment.cashId
            form.bank_account_id = this.$refs.payment.bankAccountId
            form.discount = this.$refs.payment.discount || 0
            form.paid = this.$refs.payment.paid || 0
            form.grand_total = this.$refs.payment.grandTotal
            if (this.$refs.payment.dueOrChange > 0) {
                form.due = this.$refs.payment.dueOrChange
                form.party_balance = this.$refs.payment.dueOrChange
            }else{
                form.party_balance = 0
                form.due = 0
            }
            form.subtotal = this.$refs.payment.subtotal
            form.previous_balance = this.$refs.payment.supplierBalance

            if (this.oldDamageReturn) {
                Inertia.put(this.route("damage-return.update", this.oldDamageReturn.id), form)
            }else{
                Inertia.post(this.route("damage-return.store"), form)
            }

        },
    },

    mounted() {
        if (this.oldDamageReturn) {
            this.mutationLoadOldData(this.oldDamageReturn)
        }else{
            this.mutationLoadOldData(null)
            this.mutationLoadSupplierBalance(0)
            this.mutationLoadSubtotal(0)
        }
    }
}
</script>

<style scoped>

</style>
