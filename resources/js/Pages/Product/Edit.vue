<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('product.index')" :create-route="route('product.create')" />
            <div class="sale container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card mb-5 border-0">
                        <div class="card-header p-0 border-0 d-flex">
                            <!-- page title -->
                            <div class="mt-3">
                                <h4 class="main-title">{{ lang.create_new_product }}</h4>
                                <p><small>{{ lang.all }} <span style="color: red">*</span> {{ lang.mark_must_be_required }}</small></p>
                            </div>

                            <!-- header icon -->
                            <back-icon :Url="route('product.index')" />
                        </div>
                    </div>

                    <form @submit.prevent="editProduct()" method="POST">
                        <!-- type text -->
                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="name" class="form-label required mt-1">{{ lang.product_name }}</label>
                            </div>

                            <div class="col-6">
                                <input type="text" v-model="form.name" class="form-control" id="name" :placeholder="lang.characters_only">
                                <div v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="category" class="form-label required mt-1">{{ lang.category }}</label>
                            </div>

                            <div class="col-6">
                                <!--                                    <input type="text" v-model="form.phone" class="form-control" id="phone" placeholder="Enter phone number">-->
                                <select v-model="form.category_id" id="category" class="form-select">
                                    <option :value="null" selected >{{ lang.choose_one }}</option>
                                    <option v-for="(category, cat_index) in categories" :value="category.id" :key="cat_index">{{ category.name }}</option>
                                </select>
                                <div v-if="form.errors.category_id" class="text-danger">{{ form.errors.category_id }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="brand" class="form-label mt-1">{{ lang.brand }}</label>
                            </div>

                            <div class="col-6">
                                <!--                                    <input type="email" v-model="form.email" class="form-control" id="email" placeholder="Enter email">-->
                                <select v-model="form.brand_id" id="brand" class="form-select">
                                    <option :value="null" selected disabled>{{ lang.choose_one }}</option>
                                    <option v-for="(brand, index) in brands" :value="brand.id" :key="index">{{ brand.name }}</option>
                                </select>
                                <div v-if="form.errors.brand_id" class="text-danger">{{ form.errors.brand_id }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="unit" class="form-label required mt-1">{{ lang.unit }}</label>
                            </div>

                            <div class="col-6">
                                <!--                                    <input type="text" v-model="form.unit_id" class="form-control" id="contact_person_name" placeholder="Characters only">-->
                                <select v-model="form.unit_id" @change="getUnitLabels" id="unit" class="form-select">
                                    <option :value="null" selected >{{ lang.choose_one }}</option>
                                    <option v-for="(unit, unit_index) in units" :value="unit.id" :key="unit_index">{{ unit.name + ' (' + unit.description + ')' }}</option>
                                </select>
                                <div v-if="form.errors.unit_id" class="text-danger">{{ form.errors.unit_id }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="purchase_price" class="form-label required mt-1">{{ lang.purchase_price }}</label>
                            </div>

                            <div class="col-6">
                                <input type="number" step="any" v-model="form.purchase_price" class="form-control" id="purchase_price" :placeholder="lang.enter_purchase_price">
                                <div v-if="form.errors.purchase_price" class="text-danger">{{ form.errors.purchase_price }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="sale_price" class="form-label required mt-1">{{ lang.sale_price }}</label>
                            </div>

                            <div class="col-6">
                                <input type="number" step="any" v-model="form.sale_price" class="form-control" id="sale_price" placeholder="Enter sale price">
                                <div v-if="form.errors.sale_price" class="text-danger">{{ form.errors.sale_price }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="barcode" class="form-label mt-1">{{ lang.barcode }}</label>
                            </div>

                            <div class="col-6">
                                <input type="text" v-model="form.barcode" class="form-control" id="barcode" :placeholder="lang.enter_barcode">
                                <div v-if="form.errors.barcode" class="text-danger">{{ form.errors.barcode }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="price_type" class="form-label required mt-1">{{ lang.price_type }}</label>
                            </div>

                            <div class="col-6">
                                <select v-model="form.price_type" id="price_type" class="form-select">
                                    <option :value="null" selected >{{ lang.choose_one }}</option>
                                    <option v-for="(label, index) in unitLabels" :value="index" :key="index">{{ label }}</option>
                                </select>
                                <div v-if="form.errors.price_type" class="text-danger">{{ form.errors.price_type }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="type" class="form-label required mt-1">{{ lang.type }}</label>
                            </div>

                            <div class="col-6">
                                <select v-model="form.type" id="type" class="form-select">
                                    <option value='raw_material' selected>Raw Product</option>
                                    <option value="finish_product">Finish Product</option>
                                    <option value="bag">Bag</option>
                                </select>
                                <div v-if="form.errors.type" class="text-danger">{{ form.errors.type }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="stock_alert" class="form-label mt-1">{{ lang.stock_alert }}</label>
                            </div>

                            <div class="col-6">
                                <div class="input-group">
                                    <input
                                        type="number"
                                        id="stock_alert"
                                        aria-describedby="quantityError"
                                        v-for="(label,labelIndex) in unitLabels"
                                        :placeholder="label"
                                        :key="labelIndex"
                                        :value="form.quantity_in_unit[labelIndex]"
                                        @blur="
                                        addStockAlertQuantity(
                                                $event,
                                                labelIndex
                                            )"
                                        @change="
                                        addStockAlertQuantity(
                                            $event,
                                            labelIndex
                                        )"
                                        @keyup="
                                        addStockAlertQuantity(
                                            $event,
                                            labelIndex
                                        )"
                                        min="0"
                                        class="form-control form-control-sm"
                                    />
                                </div>
                                <div v-if="form.errors.stock_alert" class="text-danger">{{ form.errors.stock_alert }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label class="form-label required mt-1">{{ lang.status }}</label>
                            </div>

                            <div class="col-6">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" v-model="form.status" value="1">
                                    <label class="form-check-label">{{ lang.available }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" v-model="form.status" value="0">
                                    <label class="form-check-label">{{  lang.not_available}}</label>
                                </div>
                                <div v-if="form.errors.status" class="text-danger">{{ form.errors.status }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label class="form-label mt-1">&nbsp;</label>
                            </div>

                            <div class="col-10">
                                <button type="reset" class="btn btn-warning me-2">
                                    <i class="bi bi-dash-square"></i>
                                    <span class="p-1">{{ lang.reset }}</span>
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-square"></i>
                                    <span class="p-1">{{ lang.save }}</span>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Button from "@/Components/Button";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import BackIcon from "@/Pages/ReusableComponent/Icon/BackIcon";
import lowestConverter from "@/reusable/ConvertToLowestUnit";
export default {
    name: "Edit.blade.php",
    components: {
        Button,
        BreezeAuthenticatedLayout,
        TopBar,
        BackIcon,
    },
    props: {
        categories:Object,
        brands: Object,
        units:Object,
        message:Text,
        product: Object,
    },
    data(){
        return {
            lang:this.$page.props.lang,
            unitLabels: [],
            unit: {},
            form: this.$inertia.form({
                id: null,
                name: '',
                unit_id: null,
                brand_id: null,
                category_id: null,
                price_type: null,
                sale_price: '',
                stock_alert: null,
                quantity_in_unit: {},
                type: 'raw_material',
                barcode: null,
                status: 1,
                purchase_price: '',
            }),
        }
    },
    methods:{
        getUnitLabels() {
            let unit = this.units.find(unit => unit.id == this.form.unit_id)
            this.unitLabels = unit.unit_labels
            this.unit = unit;
            const unitRelation = this.units.find(unit => unit.id === this.form.unit_id).relation.split('/')
            let _quantity = [];
            for (let i = 0; i < unitRelation.length; i++) {
                _quantity[i] = null
            }

            this.form.quantity_in_unit = _quantity
        },

        addStockAlertQuantity(event, order) {
            this.form.quantity_in_unit[order] = event.target.value ? event.target.value : null
            this.form.stock_alert = lowestConverter(this.form.quantity_in_unit, this.unit)
        },

        initialValues(){
            this.form.id = this.product.id
            this.form.name = this.product.name
            this.form.barcode = this.product.barcode
            this.form.category_id = this.product.category_id
            this.form.brand_id = this.product.brand_id
            this.form.sub_category_id = this.product.sub_category_id
            this.form.unit_id = this.product.unit_id
            this.form.purchase_price = this.product.purchase_price
            this.form.sale_price = this.product.sale_price
            this.form.status = this.product.status
            this.form.bag_size = this.product.bag_size
            this.form.type = this.product.type
            this.getUnitLabels()
            this.form.quantity_in_unit = Object.assign({}, this.product.quantity_in_unit)
            this.form.stock_alert = this.product.stock_alert
            this.form.price_type = this.product.price_type
        },
        editProduct(){
            this.form.patch(this.route('product.update', this.form.id))
        },
    },

    mounted() {
        this.initialValues();
    }
}
</script>

<style scoped>

</style>
