<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('product-transfer.index')" :create-route="route('product-transfer.create')" />

            <div class="container mt-2">
                <print-details />
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ lang.product_transfer_details }}</h5>
                            <div>
                                <print-icon />
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <p class="col-6">{{ lang.date }}: {{ formatter.dateFormat(product_transfer.date) }} </p>
                            <p class="col-6">{{ lang.transfer_number }}: {{ product_transfer.transfer_no }} </p>
                        </div>
                        <hr>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="card border-0">
                                    <div class="card-header p-0 py-1 d-sm-flex d-block  align-items-center border-0">
                                        <!-- page title -->
                                        <div>
                                            <p class="ms-auto">{{ lang.from_showroom }}: {{ product_transfer.from_showroom_name }}</p>
                                        </div>
                                        <p class="ms-auto">{{ lang.to_showroom }}: {{ product_transfer.to_showroom_name }}</p>
                                    </div>
                                </div>
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th class="text-center">{{ lang.product_name }}</th>
                                        <th class="text-center">{{ lang.quantity }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(details, index) in product_transfer.product_transfer_details" :key="index">
                                        <td class="text-center">{{ details.product.name }}</td>
                                        <td class="text-end">{{ quantityConvertInUnit(details.quantity, details.product?.unit) }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="text-center">
                            <P>{{ lang.note }}: {{ product_transfer.comment }}</P>
                        </div>
                    </div>
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import * as _formatter from "@/Helpers/formatter";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import PrintDetails from "@/Pages/ReusableComponent/PrintDetails";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
import upperConverter from "@/reusable/ConvertToUpperUnit";

export default {
    name: "Show",
    props: {
        product_transfer: Object
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

    methods: {
        quantityConvertInUnit(quantity, unit){
            return upperConverter(quantity, unit.label, unit.relation)
        },
    },

    data(){
        return{
            lang: this.$page.props.lang,
        }
    }
}
</script>

<style scoped>

</style>
