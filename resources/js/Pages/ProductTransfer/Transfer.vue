<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('product-transfer.index')" :create-route="route('product-transfer.create')" />

            <div class="mt-2 container-fluid">
                <form method="POST" @submit.prevent="processProduction">
                    <div class="mt-3 row">
                        <div class="col-md-3">
                            <label for="date">{{ lang.date }}</label>
                            <input type="date" id="date" v-model="form.date" class="form-control">
                            <div v-if="form.errors.date" class="text-danger">{{ form.errors.date }}</div>
                        </div>
                        <div class="col-md-3">
                            <label for="from_showroom">{{ lang.from_showroom }}</label>
                            <select class="form-control" @change="getProducts" id="from_showroom" v-model="form.from_showroom_id">
                                <option selected disabled :value="null">{{ lang.select_showroom }}</option>
                                <option v-for="(showroom, index) in showrooms" :key="index" :value="showroom.id">{{ showroom.name }}</option>
                            </select>
                            <div v-if="form.errors.from_showroom_id" class="text-danger">{{ form.errors.from_showroom_id }}</div>
                        </div>

                        <div class="col-md-3">
                            <label for="to_showroom">{{ lang.to_showroom }}</label>
                            <select class="form-control" id="to_showroom" v-model="form.to_showroom_id">
                                <option selected disabled :value="null">{{ lang.select_showroom}}</option>
                                <option v-for="(showroom, index) in showrooms" :key="index" :value="showroom.id">{{ showroom.name }}</option>
                            </select>
                            <div v-if="form.errors.to_showroom_id" class="text-danger">{{ form.errors.to_showroom_id }}</div>
                        </div>

                        <div class="col-md-3">
                            <label for="raw_products">{{ lang.product }}</label>
                            <v-select
                                :options="products"
                                id="raw_products"
                                :reduce="product => product.id"
                                :placeholder="lang.select_product"
                                v-model="productId"
                                label="name">
                                <template slot="option" slot-scope="option">
                                    <span class="fa" :class="option.icon"></span>
                                    {{ option.name }}
                                </template>
                            </v-select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="pt-4 pb-2 my-3 col-md-12 border-top">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th style="min-width: 150px">{{lang.product_name}}</th>
                                    <th class="text-end">{{lang.stock}}</th>
                                    <th>{{ lang.quantity }}</th>
                                    <th class="text-end print-none">{{ lang.action }}</th>
                                </tr>
                                </thead>
                                <tbody style="vertical-align: middle !important;">
                                <tr v-for="(product, cartIndex) in cartProducts" :key="cartIndex">
                                    <td class="text-center">1.</td>
                                    <td>{{ product.name }}</td>
                                    <td class="text-end">{{ product.display_quantity }}</td>
                                    <td class="p-0">
                                        <div class="input-group">
                                            <input
                                                type="number"
                                                aria-describedby="quantityError"
                                                v-for="(label,
                                                            labelIndex) in product
                                                            .product_unit_labels"
                                                :placeholder="label"
                                                :key="labelIndex"
                                                :value="product.quantity_in_unit[labelIndex]"
                                                @blur="
                                                            addQuantity(
                                                                    $event,
                                                                    product.id,
                                                                    labelIndex
                                                                )"
                                                @change="
                                                                addQuantity(
                                                                    $event,
                                                                    product.id,
                                                                    labelIndex
                                                                )"
                                                @keyup="
                                                                addQuantity(
                                                                    $event,
                                                                    product.id,
                                                                    labelIndex
                                                                )"
                                                min="0"
                                                class="form-control form-control-sm"
                                            />
                                        </div>
                                        <div v-if="product.error" id="quantityError" class="form-text text-danger">
                                            {{ product.error }}
                                        </div>
                                    </td>

                                    <td class="text-end print-none">
                                        <delete-icon @click.prevent="deleteProduct(cartIndex)" />
                                    </td>
                                </tr>

                                <tr v-if="cartProducts.length === 0">
                                    <td colspan="11" class="text-center">{{ lang.no_product_in_cart }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="pb-2 col-md-12 border-bottom">
                            <label for="comment">{{ lang.comment }}</label>
                            <textarea v-model="form.comment" class="form-control" id="comment" cols="50" rows="5"></textarea>
                        </div>
                    </div>

                    <div class="mt-2 text-end">
                        <button type="submit" class="ml-auto mr-3 btn text-end btn-primary">{{ lang.transfer }}</button>
                    </div>
                </form>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Button from "@/Components/Button";
import VSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import 'vue-select/dist/vue-select'
import lowestConverter from '../../reusable/ConvertToLowestUnit';
import upperConverter from '../../reusable/ConvertToUpperUnit';
import confirmAlert from "@/confirm";
import alert from "@/alert";
import axios from "axios";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
export default {
    name: "Transfer",
    props: {
        oldProductTransfer: Object,
        showrooms: Object,
    },
    components: {
        Button,
        BreezeAuthenticatedLayout,
        VSelect,
        TopBar,
        DeleteIcon,
    },

    watch: {
        productId: {
            handler: 'productAddToCart',
            deep: true
        },

    },

    data() {
        return {
            lang: this.$page.props.lang,
            productId: null,
            products: [],
            cartProducts: [],
            form: this.$inertia.form({
                cartProducts: [],
                from_showroom_id: null,
                to_showroom_id: null,
                comment: null,
                date: new Date().toISOString().slice(0, 10),
            }),
        }
    },

    methods: {
        getProducts() {
            return new Promise((resolve, reject) => {
                axios.get(this.$page.props.baseUrl +'/showroom/get-product-for-showroom/'+ this.form.from_showroom_id)
                    .then(response => {
                        this.products = response.data
                        resolve()
                    })
                    .catch(error => {
                        console.log(error)
                        reject()
                    })
            })
        },

        productAddToCart() {
            if (this.productId) {
                const index = this.cartProducts.findIndex(
                    product => product.id === this.productId
                );
                let product = this.products.find(product => product.id == this.productId)

                if (index === -1) {
                    let displayQuantity = upperConverter(product.quantity, product.unit_label, product.unit_relation);
                    // default quantity
                    this.cartProducts.push(product);

                    const unitRelation = product.unit_relation.split('/')
                    let _quantity = [];
                    for (let i = 0; i < unitRelation.length; i++) {
                        _quantity[i] = null
                    }

                    product.quantity_in_unit = _quantity
                    product.product_unit_labels = product.unit_label.split('/')

                    const newProduct = {
                        ...product,
                        quantity: 0,
                        error: '',
                        display_quantity: displayQuantity || 'No Quantity Available',
                    };

                    this.cartProducts.splice(
                        this.cartProducts.length - 1,
                        1,
                        newProduct
                    );
                }else{
                    alert('Product already exit!!!')
                    return false
                }
            }
        },

        addQuantity(event, productId, order) {
            let product = this.cartProducts.find(product => product.id == productId)
            product.quantity_in_unit[order] = event.target.value ? event.target.value : null
            product.quantity = lowestConverter(product.quantity_in_unit, product.unit_relation)
        },

        deleteProduct(cartIndex) {
            this.cartProducts.splice(cartIndex, 1)
        },

        processProduction() {
            confirmAlert().then((result) => {
                if (result.isConfirmed) {
                    this.confirmProduction()
                }
            })
        },

        confirmProduction(){
            this.form.cartProducts = []
            if(this.form.from_showroom_id == this.form.to_showroom_id) {
                alert().fire({
                    icon: 'warning',
                    title: 'Product can\'t be transfer in same showroom!'
                })
                return
            }
            if(!this.form.to_showroom_id) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select to Showroom!'
                })
                return
            }

            if(!this.form.date) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select date!'
                })
                return
            }

            if (this.cartProducts.length === 0) {
                alert().fire({
                    icon: 'warning',
                    title: 'Product can\'t be empty!'
                })
                return
            }

            // for products
            let quantityError = false;
            this.cartProducts.forEach(product => {
                if(product.quantity <= 0) {
                    quantityError = true
                    product.error = "Quantity can\'t be empty"
                }else{
                    product.error = ''
                }
                this.form.cartProducts.push({
                    id: product.id,
                    quantity: product.quantity,
                    quantity_in_unit: product.quantity_in_unit,
                });
            });

            if(quantityError) {
                this.form.cartProducts = []
                return
            }

            if (this.oldProductTransfer) {
                this.form.put(this.route('product-transfer.update', this.oldProductTransfer.id))
            }else{
                this.form.post(this.route('product-transfer.store'))
            }
        },

        async oldProductTransferValues() {
            this.form.from_showroom_id = this.oldProductTransfer.from_showroom_id
            await this.getProducts()
            this.form.to_showroom_id = this.oldProductTransfer.to_showroom_id
            this.form.comment = this.oldProductTransfer.comment
            this.form.date = this.oldProductTransfer.date
            this.oldProductTransfer.product_transfer_details.forEach(detail => {
                let quantity = this.products.find(product => product.id == detail.product_id).quantity
                let displayQuantity = upperConverter(quantity, detail.product?.unit?.label, detail.product?.unit?.relation);
                detail.product.quantity = detail.quantity
                detail.product.product_unit_labels = detail.product?.unit?.label.split('/')
                detail.product.unit_relation = detail.product?.unit?.relation
                detail.product.quantity_for_price = detail.quantity_for_price
                detail.product.display_quantity = displayQuantity
                detail.product.quantity_in_unit = Object.assign({}, detail.quantity_in_unit)
                this.cartProducts.push(detail.product)
            })
        },
    },

    mounted() {
        if (this.oldProductTransfer) {
            this.oldProductTransferValues()
        }
    }
}
</script>

<style scoped>

</style>
