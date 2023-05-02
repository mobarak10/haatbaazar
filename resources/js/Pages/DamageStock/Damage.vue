<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('damage-stock.index')" :create-route="route('damage-stock.create')" />
            <div class="container mt-2">
                <form method="POST" @submit.prevent="saveDamage">
                    <div class="row">
                        <product-component :from="from" :damage="damage" ref="product" />
                        <div class="row col-md-12">
                            <label for="note" class="col-md-2 my-3 col-form-label text-end">{{lang.note}}:</label>
                            <div class="col-md-10">
                                <textarea
                                    id="note"
                                    v-model="comment"
                                    rows="3"
                                    class="form-control">
                                </textarea>
                            </div>
                            <div class="text-end mt-2">
                                <button type="submit" class="ml-auto btn text-end btn-primary">{{ lang.save }}</button>
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
import VSelect from "vue-select";
import productComponent from "@/Pages/ReusableComponent/ProductComponent";
import confirmAlert from "@/confirm";
import alert from "@/alert";
import {Inertia} from "@inertiajs/inertia";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";

export default {
    name: "Damage",
    props: {
        damage: Object
    },
    components: {
        BreezeAuthenticatedLayout,
        VSelect,
        productComponent,
        TopBar,
    },

    data() {
        return {
            lang: this.$page.props.lang,
            from: 'damage',
            comment: null,
        }
    },


    methods: {
        saveDamage() {
            confirmAlert().then((result) => {
                if (result.isConfirmed) {
                    this.confirmSale()
                }
            })
        },
        confirmSale(){
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
                });
            });

            if (quantityError) {
                form.products = []
                return
            }
            form.showroom_id = this.$refs.product.showroom_id
            form.date = this.$refs.product.date
            form.comment = this.comment
            if (this.damage) {
                Inertia.put(this.route("damage-stock.update", this.damage.id), form)
            }else{
                Inertia.post(this.route("damage-stock.store"), form)
            }

        },
    },

    mounted() {
        if (this.damage) {
            this.comment = this.damage.comment
        }
    }
}
</script>

<style scoped>

</style>
