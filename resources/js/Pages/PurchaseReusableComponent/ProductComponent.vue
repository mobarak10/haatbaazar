<template>
    <product-by-barcode-component :cartProducts="cartProducts" @barcodeProduct="barcodeProduct" ref="barcode" :products="allProducts" />
    <div class="row">
        <div class="col-md-3">
            <input type="date" v-model="date" class="form-control">
        </div>
        <div class="col-md-3">
            <select class="form-select" @change="getShowroomProducts" v-model="showroom_id">
                <option selected disabled :value="null">{{ lang.select_showroom }}</option>
                <option v-for="(showroom, index) in showrooms" :key="index" :value="showroom.id">{{ showroom.name }}</option>
            </select>
        </div>

        <div class="col-md-3">
            <select class="form-select" v-model="type" @change="getProducts">
                <option :value="null">{{ lang.select_type}}</option>
                <option value="raw_material">Raw Product</option>
                <option value="finish_product">Finish Product</option>
                <option value="bag">Bag</option>
            </select>
        </div>

        <div class="col-md-3">
            <v-select
                :options="allProducts"
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

        <div class="col-md-12 my-3 border-top table-responsive border-bottom pt-4 pb-2">
            <table class="table table-striped table-bordered table-sm">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th style="min-width: 150px">{{ lang.product_name }}</th>
                        <th class="text-end">{{ lang.stock }}</th>
                        <th style="min-width: 130px">{{ lang.quantity }}</th>
                        <th
                            v-if="permissions.includes('product-purchase_price')"
                            style="min-width: 100px">
                            {{ from === 'purchase' ? lang.purchase_price : lang.return_price }}
                        </th>
                        <th
                            :class="permissions.includes('product-purchase_price') ? '' : 'd-none'"
                            style="min-width: 100px"
                            class="text-end"
                        >
                            {{ lang.total }}
                        </th>
                        <th class="text-end print-none">{{ lang.action }}</th>
                    </tr>
                </thead>
                <tbody style="vertical-align: middle !important;">
                <tr v-for="(product, cartIndex) in cartProducts" :key="cartIndex">
                    <td class="text-center">{{ cartIndex + 1 }}</td>
                    <td>{{ product.name }}</td>
                    <td class="text-end">{{ product.display_quantity }}</td>
                    <td class="p-0">
                        <div class="input-group">
                            <input
                                type="number"
                                aria-describedby="quantityError"
                                v-for="(label,labelIndex) in product.product_unit_labels"
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

                    <td
                        v-if="permissions.includes('product-purchase_price')"
                        class="p-0"
                    >
                        <!-- Purchase Price -->
                        <input
                            type="number"
                            step="any"
                            class="form-control form-control-sm"
                            v-model.trim="product.purchase_price"
                        />
                    </td>

                    <td
                        class="text-end"
                        :class="permissions.includes('product-purchase_price') ? '' : 'd-none'"
                    >
                        {{
                            Number.parseFloat(
                                product.total_price = (product.purchase_price) * product.quantity_for_price)
                                .toFixed(3)
                        }}
                    </td>
                    <td class="text-end print-none">
                        <delete-icon @click.prevent="cartProducts.splice(cartIndex,1)" />
                    </td>
                </tr>

                <tr v-if="cartProducts.length === 0">
                    <td colspan="11" class="text-center">{{ lang.no_product_in_cart }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import upperConverter from "@/reusable/ConvertToUpperUnit";
import VSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import 'vue-select/dist/vue-select'
import { mapState, mapActions, mapMutations } from "vuex";
import lowestConverter from "@/reusable/ConvertToLowestUnit";
import priceQuantity from "@/reusable/PriceQuantity";
import axios from "axios";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
import ProductByBarcodeComponent from "@/Pages/ReusableComponent/Barcode/ProductByBarcodeComponent";
import alert from "@/alert";

export default {
    name: "ProductComponent",
    components: {
        VSelect,
        DeleteIcon,
        ProductByBarcodeComponent,
    },
    computed: {
        ...mapState(["showrooms"])
    },

    props: {
        from: String,
        oldPurchaseReturn: Object,
    },

    watch: {
        productId: {
            handler: 'addToCart',
            deep: true
        },

        cartProducts: {
            deep: true,
            handler: function(value) {
                if(this.cartProducts.length > 0){
                    this.disableWarehouse = true;
                }
                this.subtotal = value.reduce((total, item) => {
                    return parseFloat(item.total_price) + total;
                }, 0);
                this.mutationLoadSubtotal(this.subtotal)
            }
        },
    },
    data() {
        return {
            lang: this.$page.props.lang,
            permissions: this.$page.props.permissionNames,
            disableWarehouse: false,
            date: new Date().toISOString().substr(0, 10),
            products: [],
            subtotal: 0,
            cartProducts: [],
            allProducts: [],
            type: 'finish_product',
            showroom_id: null,
            productId: null,
        }
    },

    methods: {
        barcodeProduct() {
            this.productId = this.$refs.barcode.productId
        },
        ...mapActions([
            "loadAllShowrooms"
        ]),
        ...mapMutations(['mutationLoadSubtotal']),
        addToCart(){
            if (this.productId) {
                const index = this.cartProducts.findIndex(
                    product => product.id === this.productId
                );

                let product = this.products.find(product => product.id === this.productId)

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
                        quantity_for_price: 0,
                        price: product.purchase_price,
                        total_price: 0,
                        error: '',
                        display_quantity: displayQuantity || 'No Quantity Available',
                    };

                    this.cartProducts.splice(
                        this.cartProducts.length - 1,
                        1,
                        newProduct
                    );
                }else{
                    let toast = alert()
                    toast.fire({
                        icon: 'error',
                        title: 'Product already added in cart!'
                    })
                }
            }
        },

        addQuantity(event, productId, order) {
            let product = this.cartProducts.find(product => product.id === productId)
            product.quantity_in_unit[order] = event.target.value ? event.target.value : null
            product.quantity = lowestConverter(product.quantity_in_unit, product.unit_relation)
            product.quantity_for_price = (product.quantity / product.divisor_number)
        },

        getShowroomProducts() {
           return new Promise((resolve, reject) => {
               axios.get(this.$page.props.baseUrl +'/showroom/get-product-for-showroom/'+ this.showroom_id)
                   .then(response => {
                       this.products = response.data
                       if (this.type) {
                           this.getProducts();
                       }
                       resolve()
                   })
                   .catch(error => {
                       console.log(error)
                       reject()
                   })
            })
        },

        async initValues() {
            await this.loadAllShowrooms();
            if (this.oldPurchaseReturn){
                this.showroom_id = this.oldPurchaseReturn.showroom_id
                this.date = this.oldPurchaseReturn.date
                await this.getShowroomProducts()
                this.oldPurchaseReturn.details.forEach(detail => {
                    let quantity = this.products.find(product => product.id == detail.product.id).quantity
                    let displayQuantity = upperConverter(quantity, detail.product?.unit?.label, detail.product?.unit?.relation);
                    detail.product.price = detail.return_price
                    detail.product.quantity = detail.quantity
                    detail.product.product_unit_labels = detail.product?.unit?.label.split('/')
                    detail.product.unit_relation = detail.product?.unit?.relation
                    detail.product.display_quantity = displayQuantity
                    detail.product.quantity_for_price = (detail.quantity / detail.product.divisor_number)
                    detail.product.quantity_in_unit = Object.assign({}, detail.quantity_in_unit)
                    this.cartProducts.push(detail.product)
                })
            }else if (this.oldDamageReturn) {
                this.business_group_id = this.oldDamageReturn.business_group_id
                this.date = this.oldDamageReturn.date
                await this.getShowroomProducts()
                this.oldDamageReturn.details.forEach(detail => {
                    detail.product.price = detail.purchase_price
                    detail.product.quantity = detail.quantity
                    detail.product.quantity_for_price = priceQuantity(detail.product, detail.product.unit)
                    detail.product.quantity_in_unit = Object.assign({}, detail.quantity_in_unit)
                    this.cartProducts.push(detail.product)
                })
            }
        },

        getProducts() {
            this.allProducts = this.products.filter(product => product.type === this.type)
        },
    },

    mounted() {
        this.initValues()
    }
}
</script>

<style scoped>

</style>
