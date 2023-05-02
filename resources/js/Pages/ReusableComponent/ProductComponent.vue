<template>
    <product-by-barcode-component :cartProducts="cartProducts" @barcodeProduct="barcodeProduct" ref="barcode" :products="allProducts" />
    <div class="row">
        <div class="col-md-4">
            <input type="date" v-model="date" class="form-control">
        </div>
        <div class="col-md-4">
            <select class="form-select" @change="getShowroomProducts" v-model="showroom_id">
                <option selected disabled :value="null">{{lang.select_showroom }}</option>
                <option v-for="(showroom, index) in showrooms" :key="index" :value="showroom.id">{{ showroom.name }}</option>
            </select>
        </div>

        <div class="col-md-4">
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

        <div class="pt-4 pb-2 my-3 table-responsive col-md-12 border-top border-bottom">
            <table class="table table-striped table-bordered table-sm">
                <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th style="min-width: 150px">{{ lang.product_name }}</th>
                    <th class="text-end">{{ lang.stock }}</th>
                    <th style="min-width: 130px">{{ lang.quantity }}</th>
                    <th style="min-width: 100px" v-if="from !== 'damage'">
                        {{ from === 'sale' ? lang.sale_price : lang.return_price }}
                    </th>
                    <th v-if="from !== 'damage'" style="min-width: 100px" class="text-end">
                        {{ lang.total }}
                    </th>
                    <th class="text-end print-none">{{lang.action}}</th>
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

                    <td v-if="from !== 'damage'" class="p-0">
                        <!-- Purchase Price -->
                        <input
                            type="number"
                            step="any"
                            class="form-control form-control-sm"
                            v-model.trim="product.price"
                        />
                    </td>

                    <td v-if="from !== 'damage'" class="text-end">
                        {{
                            Number.parseFloat(
                                product.total_price = (product.price) * product.quantity_for_price)
                                .toFixed(2)
                        }}
                    </td>
                    <td class="text-end print-none">
                        <delete-icon @click.prevent="cartProducts.splice(cartIndex,1)" />
                    </td>
                </tr>

                <tr v-if="cartProducts.length === 0">
                    <td colspan="11" class="text-center">{{lang.no_product_in_cart}}</td>
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
        ...mapState(["showrooms"]),
    },

    props: {
        from: String,
        sale: Object,
        sale_return: Object,
        damage: Object,
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
                    this.disableShowroom = true;
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
            disableShowroom: false,
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
                        price: this.from === 'damage' ? product.purchase_price : product.sale_price,
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
                axios.get(this.$page.props.baseUrl + '/showroom/get-product-for-showroom/' + this.showroom_id)
                    .then(response => {
                        this.products = response.data
                        this.allProducts = response.data
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
            if (this.sale) {
                this.showroom_id = this.sale.showroom_id
                await this.getShowroomProducts()
                this.sale.details.forEach(detail => {
                    let quantity = this.products.find(product => product.id == detail.product.id).quantity
                    let displayQuantity = upperConverter(quantity, detail.product?.unit?.label, detail.product?.unit?.relation);
                    detail.product.quantity = detail.quantity
                    detail.product.price = detail.sale_price
                    detail.product.purchase_price = detail.purchase_price
                    detail.product.quantity = detail.quantity
                    detail.product.product_unit_labels = detail.product?.unit?.label.split('/')
                    detail.product.unit_relation = detail.product?.unit?.relation
                    detail.product.quantity_for_price = detail.quantity_for_price
                    detail.product.display_quantity = displayQuantity
                    detail.product.quantity_in_unit = Object.assign({}, detail.quantity_in_unit)
                    this.cartProducts.push(detail.product)
                })
            }
            else if (this.sale_return){
                this.showroom_id = this.sale_return.showroom_id
                this.date = this.sale_return.date
                await this.getShowroomProducts()
                this.sale_return.details.forEach(detail => {
                    let quantity = this.products.find(product => product.id == detail.product.id).quantity
                    let displayQuantity = upperConverter(quantity, detail.product?.unit?.label, detail.product?.unit?.relation);
                    detail.product.price = detail.return_price
                    detail.product.product_unit_labels = detail.product?.unit?.label.split('/')
                    detail.product.purchase_price = detail.purchase_price
                    detail.product.quantity = detail.quantity
                    detail.product.display_quantity = displayQuantity
                    detail.product.quantity_for_price = detail.quantity_for_price
                    detail.product.quantity_in_unit = Object.assign({}, detail.quantity_in_unit)
                    this.cartProducts.push(detail.product)
                })
            }
            else if (this.damage){
                this.date = this.damage.date
                this.showroom_id = this.damage.showroom_id
                await this.getShowroomProducts()
                this.damage.details.forEach(detail => {
                    let quantity = this.products.find(product => product.id == detail.product.id).quantity
                    let displayQuantity = upperConverter(quantity, detail.product?.unit?.label, detail.product?.unit?.relation);
                    detail.product.price = detail.purchase_price
                    detail.product.quantity = detail.quantity
                    detail.product.product_unit_labels = detail.product?.unit?.label.split('/')
                    detail.product.display_quantity = displayQuantity
                    detail.product.quantity_for_price = detail.quantity_for_price
                    detail.product.quantity_in_unit = Object.assign({}, detail.quantity_in_unit)
                    this.cartProducts.push(detail.product)
                })
            }
            else{
                if(this.showrooms.length > 0) {
                    this.showroom_id = this.showrooms[0].id
                    await this.getShowroomProducts()
                }
            }
        },
    },

    mounted() {
        this.initValues()
    }
}
</script>

<style scoped>

</style>
