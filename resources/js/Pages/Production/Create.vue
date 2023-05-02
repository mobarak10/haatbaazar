<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('production.index')" :create-route="route('production.create')" />

            <div class="mt-2 container-fluid">
                <form method="POST" @submit.prevent="processProduction">
                    <div class="mb-2 row">
                        <div class="col-md-6">
                            <input type="date" v-model="form.date" class="form-control">
                            <div v-if="form.errors.date" class="text-danger">{{ form.errors.date }}</div>
                        </div>
                    </div>
                    <hr>
                    <div class="mt-3 row">
                        <div class="col-md-3">
                            <label for="from_showroom">{{ lang.from_showroom }}</label>
                            <select class="form-control" @change="getRawProducts" id="from_showroom" v-model="form.from_showroom_id">
                                <option selected disabled :value="null">{{ lang.select_showroom }}</option>
                                <option v-for="(showroom, index) in showrooms" :key="index" :value="showroom.id">{{ showroom.name }}</option>
                            </select>
                            <div v-if="form.errors.from_showroom_id" class="text-danger">{{ form.errors.from_showroom_id }}</div>
                        </div>

                        <div class="col-md-3">
                            <label for="raw_products">{{ lang.raw_product }}</label>
                            <v-select
                                :options="rawProducts"
                                id="raw_products"
                                :reduce="product => product.id"
                                :placeholder="lang.select_product"
                                v-model="rawProductId"
                                label="name">
                                <template slot="option" slot-scope="option">
                                    <span class="fa" :class="option.icon"></span>
                                    {{ option.name }}
                                </template>
                            </v-select>
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
                            <label for="finish_products">{{ lang.finish_product }}</label>
                            <v-select
                                :options="finish_products"
                                id="finish_products"
                                :reduce="product => product.id"
                                :placeholder="lang.select_product"
                                v-model="finishProductId"
                                label="name">
                                <template slot="option" slot-scope="option">
                                    <span class="fa" :class="option.icon"></span>
                                    {{ option.name }}
                                </template>
                            </v-select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="pt-4 pb-2 my-3 col-md-6 border-top">
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
                                <tr v-for="(product, cartIndex) in rawCartProducts" :key="cartIndex">
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
                                                            addRawQuantity(
                                                                    $event,
                                                                    product.id,
                                                                    labelIndex
                                                                )"
                                                @change="
                                                                addRawQuantity(
                                                                    $event,
                                                                    product.id,
                                                                    labelIndex
                                                                )"
                                                @keyup="
                                                                addRawQuantity(
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
                                        <delete-icon @click.prevent="deleteRawProduct(cartIndex)" />
                                    </td>
                                </tr>

                                <tr v-if="rawCartProducts.length === 0">
                                    <td colspan="11" class="text-center">{{ lang.no_product_in_cart }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="pt-4 my-3 col-md-6 border-top">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th style="min-width: 150px">{{lang.product_name}}</th>
                                    <th class="text-end">{{lang.stock}}</th>
                                    <th>{{lang.quantity}}</th>
                                    <th class="text-end print-none">{{lang.action}}</th>
                                </tr>
                                </thead>
                                <tbody style="vertical-align: middle !important;">
                                <tr v-for="(product, cartIndex) in finishCartProducts" :key="cartIndex">
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
                                                            addFinishQuantity(
                                                                    $event,
                                                                    product.id,
                                                                    labelIndex
                                                                )"
                                                @change="
                                                                addFinishQuantity(
                                                                    $event,
                                                                    product.id,
                                                                    labelIndex
                                                                )"
                                                @keyup="
                                                                addFinishQuantity(
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
                                        <delete-icon @click.prevent="deleteFinishProduct(cartIndex)" />
                                    </td>
                                </tr>

                                <tr v-if="finishCartProducts.length === 0">
                                    <td colspan="11" class="text-center">{{lang.no_product_in_cart}}</td>
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
                        <button type="submit" class="ml-auto mr-3 btn text-end btn-primary">{{ lang.production }}</button>
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
    name: "Create",
    props: {
        oldProduction: Object,
        showrooms: Object,
        finish_products: Array,
    },
    components: {
        Button,
        BreezeAuthenticatedLayout,
        VSelect,
        TopBar,
        DeleteIcon,
    },

    watch: {
        rawProductId: {
            handler: 'rawAddToCart',
            deep: true
        },

        finishProductId: {
            handler: 'finishAddToCart',
            deep: true,
        },
    },

    data() {
        return {
             lang: this.$page.props.lang,
            rawProductId: null,
            finishProductId: null,
            rawProducts: [],
            rawCartProducts: [],
            finishCartProducts: [],
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
        getRawProducts() {
            return new Promise((resolve, reject) => {
                axios.get(this.$page.props.baseUrl +'/showroom/get-product-for-showroom/'+ this.form.from_showroom_id)
                    .then(response => {
                        this.rawProducts = response.data
                        resolve()
                    })
                    .catch(error => {
                        console.log(error)
                        reject()
                    })
            })
        },

        rawAddToCart() {
            if (this.rawProductId) {
                const index = this.rawCartProducts.findIndex(
                    product => product.id === this.rawProductId
                );

                let product = this.rawProducts.find(product => product.id === this.rawProductId)

                if (index === -1) {
                    let displayQuantity = upperConverter(product.quantity, product.unit_label, product.unit_relation);
                    // default quantity
                    this.rawCartProducts.push(product);

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

                    this.rawCartProducts.splice(
                        this.rawCartProducts.length - 1,
                        1,
                        newProduct
                    );
                }else{
                    alert('Product already exit!!!')
                    return false
                }
            }
        },

        addRawQuantity(event, productId, order) {
            let product = this.rawCartProducts.find(product => product.id === productId)
            product.quantity_in_unit[order] = event.target.value ? event.target.value : null
            product.quantity = lowestConverter(product.quantity_in_unit, product.unit_relation)
        },

        deleteRawProduct(cartIndex) {
            this.rawCartProducts.splice(cartIndex, 1)
        },

        finishAddToCart() {
            if (this.form.to_showroom_id){
                if (this.finishProductId) {
                    const index = this.finishCartProducts.findIndex(
                        product => product.id === this.finishProductId
                    );

                    let product = this.finish_products.find(product => product.id === this.finishProductId)

                    if (index === -1) {
                        let displayQuantity = upperConverter(product.quantity, product.unit_label, product.unit_relation);
                        // default quantity
                        this.finishCartProducts.push(product);

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

                        this.finishCartProducts.splice(
                            this.finishCartProducts.length - 1,
                            1,
                            newProduct
                        );
                    }else{
                        alert('Product already exit!!!')
                        return false
                    }
                }
            }else{
                alert('Please select showroom group')
                return false
            }
        },

        addFinishQuantity(event, productId, order) {
            let product = this.finishCartProducts.find(product => product.id === productId)
            product.quantity_in_unit[order] = event.target.value ? event.target.value : null
            product.quantity = lowestConverter(product.quantity_in_unit, product.unit_relation)
        },

        deleteFinishProduct(cartIndex) {
            this.finishCartProducts.splice(cartIndex, 1)
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
            if(!this.form.to_showroom_id) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select to showroom group!'
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

            if (this.rawCartProducts.length === 0) {
                alert().fire({
                    icon: 'warning',
                    title: 'Raw Product can\'t be empty!'
                })
                return
            }

            if (this.finishCartProducts.length === 0) {
                alert().fire({
                    icon: 'warning',
                    title: 'Finish Product can\'t be empty!'
                })
                return
            }

            // for products
            let quantityError = false;
            this.rawCartProducts.forEach(product => {
                if(product.quantity <= 0) {
                    quantityError = true
                    product.error = "Quantity can\'t be empty"
                }else{
                    product.error = ''
                }
                this.form.cartProducts.push({
                    id: product.id,
                    quantity: product.quantity,
                    type: 'raw_product',
                    quantity_in_unit: product.quantity_in_unit,
                });
            });

            this.finishCartProducts.forEach(product => {
                if(product.quantity <= 0) {
                    quantityError = true
                    product.error = "Quantity can\'t be empty"
                }else{
                    product.error = ''
                }
                this.form.cartProducts.push({
                    id: product.id,
                    quantity: product.quantity,
                    type: 'finish_product',
                    quantity_in_unit: product.quantity_in_unit,
                });
            });

            if(quantityError) {
                this.form.cartProducts = []
                return
            }

            if (this.oldProduction) {
                this.form.put(this.route('production.update', this.oldProduction.id))
            }else{
                this.form.post(this.route('production.store'))
            }
        },

        async oldProductionValues() {
            this.form.from_showroom_id = this.oldProduction.from_showroom_id
            await this.getRawProducts()
            this.form.to_showroom_id = this.oldProduction.to_showroom_id
            this.form.comment = this.oldProduction.comment
            this.form.date = this.oldProduction.date
            this.oldProduction.production_details.forEach(detail => {
               if (detail.type == 'raw_product') {
                   let quantity = this.rawProducts.find(product => product.id == detail.product.id).quantity
                   let displayQuantity = upperConverter(quantity, detail.product?.unit?.label, detail.product?.unit?.relation);
                   detail.product.quantity = detail.quantity
                   detail.product.product_unit_labels = detail.product?.unit?.label.split('/')
                   detail.product.unit_relation = detail.product?.unit?.relation
                   detail.product.display_quantity = displayQuantity
                   detail.product.quantity_in_unit = Object.assign({}, detail.quantity_in_unit)
                   this.rawCartProducts.push(detail.product)
               }else{
                   let finishQuantity = this.finish_products.find(product => product.id == detail.product.id).quantity
                   let displayFinishQuantity = upperConverter(finishQuantity, detail.product?.unit?.label, detail.product?.unit?.relation);
                   detail.product.quantity = detail.quantity
                   detail.product.product_unit_labels = detail.product?.unit?.label.split('/')
                   detail.product.unit_relation = detail.product?.unit?.relation
                   detail.product.display_quantity = displayFinishQuantity
                   detail.product.quantity_in_unit = Object.assign({}, detail.quantity_in_unit)
                   this.finishCartProducts.push(detail.product)
               }
            })
        },
    },

    mounted() {
        if (this.oldProduction) {
            this.oldProductionValues()
        }
    }
}
</script>

<style scoped>

</style>
