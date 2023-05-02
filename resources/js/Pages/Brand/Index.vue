<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('brand.index')" :modal="showModal" />
            <div class="container sale">
                <!-- table -->
                <div class="border-0 card">
                    <div class="p-0 py-1 border-0 card-header d-sm-flex d-block align-items-center">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_brands }}</h4><br>
                            <!-- <small class="print-none">About 563,000,000 results (0.98 seconds)</small> -->
                        </div>
                        <!-- add -->

                        <button type="button" role="button" class="btn top-icon-button print-none ms-auto" title="Creat new brand" @click="showModal">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>

                    <!-- New Category Modal -->
                    <div class="modal fade" id="payment-method" ref="modal" tabindex="-1" aria-labelledby="paymentMethodLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="#" @submit.prevent="submitForm()">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="paymentMethodLabel">{{ this.form.id ? lang.update : lang.add_new }} {{ lang.brand }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label for="name" class="form-label required">{{ lang.brand_name }}</label>
                                                <input type="text" class="form-control" v-model="form.name" id="name" :placeholder="lang.enter_brand_name">
                                                <div v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn custom-btn btn-danger"
                                                data-bs-dismiss="modal">{{ lang.close }}</button>
                                        <button type="submit" class="btn custom-btn btn-success">{{ lang.save }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End New Category Modal -->

                    <!-- content body -->
                    <div class="p-0 card-body">
                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col" width="100">{{ lang.sl }}</th>
                                    <th scope="col">{{ lang.brand }}</th>
                                    <th scope="col" width="100">{{ lang.action }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item,index) in brands" :key="index">
                                    <th scope="row">{{ index + 1 }}</th>
                                    <td>{{ item.name }}</td>
                                    <td>
                                        <edit-modal-icon @click.prevent="editBrand(item)" />
                                        <delete-icon @click.prevent="deleteBrand(item)"
                                                     :class="{disabled: item.product_name}"
                                        />
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Button from "@/Components/Button";
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import EditModalIcon from "@/Pages/ReusableComponent/Icon/EditModalIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
import BackIcon from "@/Pages/ReusableComponent/Icon/BackIcon";

export default {
    components: {
        Button,
        BreezeAuthenticatedLayout,
        TopBar,
        EditModalIcon,
        DeleteIcon,
        BackIcon,
    },
    props: {
        brands:Object
    },
    data(){
        return {
            lang: this.$page.props.lang,
            modal: {},
            form: this.$inertia.form({
                name: '',
            }),
        }
    },
    methods:{
        showModal(){
            this.modal.show()
        },

        hideModal(){
            this.modal.hide()
        },

        resetModal(){
            this.form.name = null
            this.form.id = null
        },

        submitForm(){
            if (this.form.id) {
                this.updateBrand();
                return
            }
            this.form.post(this.route('brand.store'), {
                preserveScroll: true,
                onSuccess: () => this.hideModal()
            })
        },

        editBrand(brand){
            this.form = this.$inertia.form({
                ...brand
            });
            this.showModal();
        },

        updateBrand(){
            this.form.patch(this.route("brand.update", this.form.id), {
                preserveScroll: true,
                onSuccess: () => this.hideModal(),
            });
        },

        deleteBrand(item){
            let toast = alert()
            if(item.product_name) {
                toast.fire({
                    icon: 'warning',
                    title: 'Brand has product, so it can\'t be deleted!'
                })
                return
            }
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("brand/" + item.id, {
                        onSuccess: () => {
                            toast.fire({
                                icon: 'success',
                                title: 'Successfully Deleted!'
                            })
                        },
                    });
                }
            })
        }
    },

    mounted() {
        this.modal = new bootstrap.Modal(this.$refs.modal)
        this.$refs.modal.addEventListener('hidden.bs.modal', () => this.resetModal())
    }

}
</script>
