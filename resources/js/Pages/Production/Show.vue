<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container-fluid print-none">
                <top-bar :index-route="route('production.index')" :create-route="route('production.create')" />
            </div>

            <div class="container-fluid mt-2">
                <print-details />

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ lang.production_details }}</h5>

                            <div>
                                <print-icon />
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <p class="col-6">{{ lang.date }}: {{ formatter.dateFormat(production.date) }} </p>
                            <p class="col-6">{{ lang.production_number }}: {{ production.production_no }} </p>
                        </div>
                        <hr>
                        <div class="row mt-2">
                            <div class="col-6">
                                <div class="card border-0">
                                    <div class="card-header p-0 py-1 d-sm-flex d-block  align-items-center border-0">
                                        <!-- page title -->
                                        <div>
                                            <p>{{ lang.raw_product }}</p>
                                        </div>
                                        <p class="ms-auto">{{ lang.showroom }}: {{ production.from_showroom?.name }}</p>
                                    </div>
                                </div>
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ lang.product_name }}</th>
                                            <th class="text-end">{{ lang.quantity }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(details, index) in production.production_details" :key="index">
                                            <td v-if="details.type === 'raw_product'">{{ details.product?.name }}</td>
                                            <td v-if="details.type === 'raw_product'" class="text-end">{{ quantityConvertInUnit(details.quantity, details.product.unit) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-6">
                                <div class="card border-0">
                                    <div class="card-header p-0 py-1 d-sm-flex d-block  align-items-center border-0">
                                        <!-- page title -->
                                        <div>
                                            <p>{{ lang.finish_product }}</p>
                                        </div>
                                        <p class="ms-auto">{{ lang.showroom }}: {{ production.to_showroom?.name }}</p>
                                    </div>
                                </div>
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ lang.product_name }}</th>
                                            <th class="text-end">{{ lang.quantity }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(details, index) in production.production_details" :key="index">
                                            <td v-if="details.type === 'finish_product'">{{ details.product?.name }}</td>
                                            <td v-if="details.type === 'finish_product'" class="text-end">{{ quantityConvertInUnit(details.quantity, details.product.unit) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="text-center">
                            <P>{{ lang.note }}: {{ production.comment }}</P>
                        </div>
                    </div>
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import * as _formatter from '@/Helpers/formatter'
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import upperConverter from "@/reusable/ConvertToUpperUnit";
export default {
    name: "show",
    props: {
        production: Object
    },
    components: {
        BreezeAuthenticatedLayout,
        TopBar,
        PrintDetails,
        PrintIcon,
    },

    computed: {
        formatter() {
            return _formatter
        }
    },

    data(){
        return{
            lang: this.$page.props.lang,
        }
    },
    methods: {
        quantityConvertInUnit(quantity, unit){
            return upperConverter(quantity, unit.label, unit.relation)
        },
    },
}
</script>

<style scoped>

</style>
