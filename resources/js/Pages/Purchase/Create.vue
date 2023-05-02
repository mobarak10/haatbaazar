<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('purchase.index')" :create-route="route('purchase.create')" />
            <div class="container-fluid mt-2">
                <product-by-barcode-component @barcodeProduct="barcodeProduct" :cartProducts="cartProducts" ref="barcode" :products="allProducts" />
                <form method="POST" @submit.prevent="purchase">
                    <div class="row ">
                        <div class="col-md-4">
                            <input type="date" v-model="form.date" class="form-control">
                            <div v-if="form.errors.date" class="text-danger">{{ form.errors.date }}</div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" v-model="form.showroom_id">
                                <option selected disabled :value="null">{{ lang.select_showroom }}</option>
                                <option v-for="(showroom, index) in showrooms" :key="index" :value="showroom.id">{{ showroom.name }}</option>
                            </select>
                            <div v-if="form.errors.showroom_id" class="text-danger">{{ form.errors.showroom_id }}</div>
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
                        <div class="col-md-12 my-3 table-responsive border-top border-bottom pt-4 pb-2">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th style="min-width: 150px">{{ lang.product_name }}</th>
                                    <th class="text-end">{{lang.stock}}</th>
                                    <th style="min-width: 130px">{{ lang.quantity }}</th>
                                    <th
                                        style="min-width: 100px"
                                        v-if="permissions.includes('product-purchase_price')"
                                    >
                                        {{ lang.purchase_price }}
                                    </th>
                                    <th
                                        v-if="permissions.includes('product-purchase_price')"
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

                                        <td v-if="permissions.includes('product-purchase_price')" class="p-0">
                                            <!-- Purchase Price -->
                                            <input
                                                type="number"
                                                step="any"
                                                class="form-control form-control-sm"
                                                v-model.trim="product.purchase_price"
                                            />
                                        </td>

                                        <td
                                            :class="permissions.includes('product-purchase_price') ? '' : 'd-none'"
                                            class="text-end"
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
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">{{ lang.supplier }}</label>
                                        <div v-if="oldPurchase" class="col-sm-10">
                                            <select v-model="supplierId" class="form-select" required>
                                                <option v-for="(supplier, index) in suppliers" :value="supplier.id" :key="index">
                                                    {{ supplier.name}}
                                                </option>
                                            </select>
                                        </div>

                                        <div v-else class="col-sm-10">
                                            <v-select
                                                :options="suppliers"
                                                :reduce="supplier => supplier.id"
                                                :placeholder=" lang.select_supplier"
                                                :clearable="false"
                                                v-model="supplierId"
                                                class="input-height"
                                                label="name">
                                                <template slot="option" slot-scope="option">
                                                    <span class="fa" :class="option.icon"></span>
                                                    {{ option.name }}
                                                </template>
                                            </v-select>
                                        </div>
                                        <div v-if="form.errors.supplierId" class="text-danger">{{ form.errors.supplierId }}</div>
                                    </div>

                                    <div class="row mt-3">
                                        <label for="model" class="col-sm-2 col-form-label">{{ lang.mobile }}</label>
                                        <div class="col-sm-10">
                                            <input type="text" v-model="supplierMobile" disabled class="form-control" id="model">
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <label for="address" class="col-sm-2 col-form-label">{{ lang.address }}</label>
                                        <div class="col-sm-10">
                                            <textarea id="address" v-model="supplierAddress" disabled rows="3" class="form-control"></textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <label for="note" class="col-sm-2 col-form-label">{{ lang.note}}</label>
                                        <div class="col-sm-10">
                                            <textarea id="note" v-model="form.note" rows="3" :placeholder="lang.write_something_about_this_purchase" class="form-control"></textarea>
                                        </div>
                                    </div>

<!--                                    <div class="row border-top mt-4 pt-4">-->
<!--                                        <div class="form-check">-->
<!--                                            <input class="form-check-input" type="checkbox" value="" id="sms">-->
<!--                                            <label class="form-check-label" for="sms">-->
<!--                                                Send sms-->
<!--                                            </label>-->
<!--                                        </div>-->
<!--                                    </div>-->
                                </div>
                                <div class="col-md-5">
                                    <div :class="permissions.includes('product-purchase_price') ? '' : 'd-none'">
                                        <div class="row">
                                            <label for="total_price" class="col-sm-2 col-form-label" >{{ lang.total_price }}</label>
                                            <div class="col-sm-10">
                                                <input type="number" disabled :value="Number.parseFloat(form.subtotal).toFixed(2)" class="form-control" id="total_price">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <label for="previous_balance" class="col-sm-2 col-form-label">{{ lang.previous_balance }}</label>
                                            <div class="col-sm-6">
                                                <input type="number" disabled class="form-control" :value="Math.abs(supplierBalance)" id="previous_balance">
                                            </div>
                                            <div class="col-sm-4">
                                                <input type="text" disabled :value="(supplierBalance >= 0) ? 'Receivable' : 'Payable'" class="form-control">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <label for="discount" class="col-sm-2 col-form-label">{{ lang.discount }}</label>
                                            <div class="col-sm-10">
                                                <input
                                                    type="number"
                                                    step="any"
                                                    class="form-control"
                                                    v-model="form.discount"
                                                    id="discount">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <label for="labour_cost" class="col-sm-2 col-form-label">{{ lang.labour_cost }}</label>
                                            <div class="col-sm-6">
                                                <input
                                                    type="number"
                                                    step="any"
                                                    class="form-control"
                                                    v-model="form.labour_cost"
                                                    id="labour_cost">
                                                <div v-if="form.errors.labour_cost" class="text-danger">{{ form.errors.labour_cost }}</div>
                                            </div>
                                            <div class="col-sm-4 mt-1">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" v-model="form.labour_cost_adjust_to_supplier" id="labour_cost_adjust_to_supplier">
                                                    <label class="form-check-label" for="labour_cost_adjust_to_supplier">{{ lang.adjust_to_supplier }}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <label for="transport_cost" class="col-sm-2 col-form-label">{{ lang.transport_cost }}</label>
                                            <div class="col-sm-6">
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    step="any"
                                                    v-model="form.transport_cost"
                                                    id="transport_cost">
                                                <div v-if="form.errors.transport_cost" class="text-danger">{{ form.errors.transport_cost }}</div>
                                            </div>
                                            <div class="col-sm-4 mt-1">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" v-model="form.transport_cost_adjust_to_supplier" id="transport_cost_adjust_to_supplier">
                                                    <label class="form-check-label" for="transport_cost_adjust_to_supplier">{{ lang.adjust_to_supplier }}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <label for="grand_total" class="col-sm-2 col-form-label">{{ lang.grand_total }}</label>
                                            <div class="col-sm-10">
                                                <input disabled type="number" :value="Number.parseFloat(grandTotal).toFixed(2)" class="form-control" id="grand_total">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <label for="payment" class="col-sm-2 col-form-label">{{ lang.paid }}</label>
                                            <div class="col-sm-6">
                                                <input
                                                    type="number"
                                                    step="any"
                                                    class="form-control"
                                                    v-model="form.paid"
                                                    id="payment">
                                            </div>
                                            <div class="col-sm-4">
                                                <select v-model="form.payment_type" class="form-select">
                                                    <option value="cash">{{ lang.cash }}</option>
                                                    <option value="bank">{{ lang.bank }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div v-if="form.payment_type === 'cash'" class="row mt-2">
                                            <label class="col-sm-2 col-form-label" >{{ lang.cash_name }}</label>
                                            <div class="col-sm-10">
                                                <select v-model="form.cash_id" class="form-select">
                                                    <option :value="null">{{ lang.choose_cash }}</option>
                                                    <option v-for="(cash, index) in cashes" :key="index" :value="cash.id">{{ cash.title }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div v-if="form.payment_type === 'bank'">
                                            <div class="row mt-2">
                                                <label for="total_price" class="col-sm-2 col-form-label" >{{ lang.bank_name }}</label>
                                                <div class="col-sm-10">
                                                    <select v-model="form.bank_account_id" class="form-select">
                                                        <option :value="null">{{ lang.choose_bank }}</option>
                                                        <option v-for="(account, index) in bank_accounts" :key="index" :value="account.id">{{ account.bank.name + ' (' + account.account_name + ')' }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <label for="due" class="col-sm-2 col-form-label">
                                                {{ party_balance >= 0 ? lang.due : lang.party_balance }}
                                            </label>
                                            <div class="col-sm-5">
                                                <input type="number" :value="Number.parseFloat(Math.abs(party_balance)).toFixed(2)" disabled class="form-control" id="due">
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="text" disabled class="form-control" :value="party_balance > 0 ? 'Payable' : 'Receivable'" placeholder="Receivable">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end mt-2">
                                        <button type="submit" class="btn text-end btn-primary ml-auto mr-3">{{ lang.purchase }}</button>
                                    </div>
                                </div>
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
import 'vue-select/dist/vue-select';
import lowestConverter from '../../reusable/ConvertToLowestUnit';
import upperConverter from '../../reusable/ConvertToUpperUnit';
import priceQuantity from '../../reusable/PriceQuantity';
import confirmAlert from "@/confirm";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
import ProductByBarcodeComponent from "@/Pages/ReusableComponent/Barcode/ProductByBarcodeComponent";
import alert from "@/alert";

export default {
    name: "Create",
    props: {
        oldPurchase: Object,
        showrooms: Object,
        products: Object,
        suppliers: Object,
        cashes: Object,
        bank_accounts: Object,
    },
    watch: {
        productId: {
            handler: 'addToCart',
            deep: true
        },

        supplierId: {
            handler: 'getSupplierDetails',
            deep: true
        },

        cartProducts: {
            deep: true,
            handler: function(value) {
                if(this.cartProducts.length > 0){
                }
                this.form.subtotal = value.reduce((total, item) => {
                    return parseFloat(item.total_price) + total;
                }, 0);
            }
        },
    },
    components: {
        BreezeAuthenticatedLayout,
        VSelect,
        TopBar,
        DeleteIcon,
        ProductByBarcodeComponent,
    },

    computed: {
        party_balance() {
            return (this.grandTotal - this.form.paid)
        },

        grandTotal() {
            let labourCost = 0
            let transportCost = 0
            if (this.form.labour_cost_adjust_to_supplier) {
                labourCost = this.form.labour_cost
            }
            if (this.form.transport_cost_adjust_to_supplier) {
                transportCost = this.form.transport_cost
            }

            if (this.supplierBalance >= 0){
                return ((this.form.subtotal + parseFloat(labourCost || 0) + parseFloat(transportCost || 0)) - parseFloat(this.supplierBalance)) - (this.form.discount)
            }else{
                return ((this.form.subtotal + parseFloat(labourCost || 0) + parseFloat(transportCost || 0)) + parseFloat(Math.abs(this.supplierBalance))) - (this.form.discount)
            }
        },
    },

    data() {
        return {
            lang: this.$page.props.lang,
            permissions: this.$page.props.permissionNames,
            allProducts: [],
            cartProducts: [],
            type: 'finish_product',
            showroom_id: null,
            productId: null,
            supplierId: null,
            cashBalance: null,
            supplierBalance : 0,
            supplierMobile : null,
            supplierAddress : null,
            form: this.$inertia.form({
                products: [],
                cash_id: null,
                bank_account_id: null,
                date: new Date().toISOString().slice(0,10),
                showroom_id: null,
                labour_cost: 0,
                party_id: null,
                previous_balance: 0,
                party_balance: 0,
                subtotal: 0,
                grandTotal: 0,
                labour_cost_adjust_to_supplier: true,
                transport_cost: 0,
                transport_cost_adjust_to_supplier: true,
                discount: 0,
                discount_type: 'flat',
                payment_type: 'cash',
                due_or_change: 0,
                paid: 0,
                total_paid: 0,
                note: null,
            })
        }
    },

    methods: {
        barcodeProduct() {
            this.productId = this.$refs.barcode.productId
        },

        getProducts() {
            this.allProducts = this.products.filter(product => product.type == this.type)
        },

        getSupplierDetails() {
            let supplier = this.suppliers.find(supplier => supplier.id == this.supplierId)
            this.supplierBalance = this.oldPurchase ? this.oldPurchase.previous_balance : supplier.balance
            this.supplierMobile = supplier.phone || ''
            this.supplierAddress = supplier.address || ''
        },

        addToCart(){
            if (this.productId) {
                const index = this.cartProducts.findIndex(
                    product => product.id === this.productId
                );

                let product = this.products.find(product => product.id === this.productId)

                if (index === -1) {
                    let displayQuantity = upperConverter(product.total_quantity, product.unit_label, product.unit_relation);
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
            let product = this.cartProducts.find(product => product.id == productId)
            product.quantity_in_unit[order] = event.target.value ? event.target.value : null
            product.quantity = lowestConverter(product.quantity_in_unit, product.unit_relation)
            product.quantity_for_price = (product.quantity / product.divisor_number)
        },

        purchase() {
            confirmAlert().then((result) => {
                if (result.isConfirmed) {
                    this.confirmPurchase()
                }
            })
        },

        confirmPurchase(){
            if (this.cartProducts.length == 0) {
                alert().fire({
                    icon: 'warning',
                    title: 'Cart is empty!'
                })
                return
            }
            if (!this.supplierId) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select supplier!'
                })
                return
            }

            if (!this.form.cash_id && !this.form.bank_account_id) {
                alert().fire({
                    icon: 'warning',
                    title: 'Please select payment method!'
                })
                return
            }

            this.form.party_id = this.supplierId
            this.form.previous_balance = this.supplierBalance
            this.form.grandTotal = this.grandTotal
            this.form.party_balance = this.party_balance

            this.form.products = [];
            // for products
            let quantityError = false;
            this.cartProducts.forEach(product => {
                if(product.quantity <= 0) {
                    quantityError = true
                    product.error = "Quantity can\'t be empty"
                }else{
                    product.error = ''
                }
                this.form.products.push({
                    id: product.id,
                    quantity: product.quantity,
                    quantity_in_unit: product.quantity_in_unit,
                    price: product.purchase_price,
                    line_total: product.total_price
                });
            });

            if(quantityError) {
                this.form.products = []
                return
            }
            if (this.oldPurchase) {
                this.form.put(this.route("purchase.update", this.oldPurchase.id));
            }else{
                this.form.post(this.route("purchase.store"));
            }
        },

        initialValues() {
            if (this.oldPurchase) {
                this.supplierId = this.oldPurchase.party_id
                this.getSupplierDetails()
                if (this.oldPurchase.cash_id) {
                    this.form.cash_id = this.oldPurchase.cash_id
                }else{
                    this.form.payment_type = 'bank'
                    this.form.bank_account_id = this.oldPurchase.bank_account_id
                }
                this.form.showroom_id = this.oldPurchase.showroom_id
                this.form.paid = this.oldPurchase.paid
                this.form.date = this.oldPurchase.formatted_date
                this.form.labour_cost = this.oldPurchase.purchase_cost.labour_cost
                this.form.transport_cost = this.oldPurchase.purchase_cost.transport_cost
                this.form.discount = this.oldPurchase.discount
                this.oldPurchase.details.forEach(detail => {
                    let quantity = this.products.find(product => product.id == detail.product.id).total_quantity
                    let displayQuantity = upperConverter(quantity, detail.product?.unit?.label, detail.product?.unit?.relation);
                    detail.product.quantity = detail.quantity
                    detail.product.purchase_price = detail.purchase_price
                    detail.product.product_unit_labels = detail.product?.unit?.label.split('/')
                    detail.product.total_price = detail.line_total
                    detail.product.display_quantity = displayQuantity
                    detail.product.quantity_in_unit = Object.assign({}, detail.quantity_in_unit)
                    detail.product.quantity_for_price = (detail.quantity / detail.product.divisor_number)
                    this.cartProducts.push(detail.product)
                })
            }else{
                if (this.cashes.length > 0){
                    this.form.cash_id = this.cashes[0].id
                }

                if(this.showrooms.length > 0){
                    this.form.showroom_id = this.showrooms[0].id
                }
            }
        },

    },

    mounted() {
        this.initialValues()
        this.getProducts()
    },
};
</script>

<style scoped>
</style>
